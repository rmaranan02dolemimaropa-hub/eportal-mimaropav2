<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientSatisfactionModel extends Model
{
    protected $table = 'client_satisfaction';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [

        'reference_no',
        'office_name',
        'transaction_no',
        'service_availed',
        'client_type',
        'date',
        'sex',
        'age',
        'name',
        'address',
        'cc1',
        'cc2',
        'cc3',
        'sqd0',
        'sqd1',
        'sqd2',
        'sqd3',
        'sqd4',
        'sqd5',
        'sqd6',
        'sqd7',
        'sqd8',
        'suggestions',
        'email',
        'created_at'

    ];

    public static array $questions = [
        'rating_1' => 'Delivered on a timely manner?',
        'rating_2' => 'Responded properly to client queries/needs?',
        'rating_3' => 'Easy access to service/facility?',
        'rating_4' => "Instructions are clearly indicated in the Citizen's Charter?",
        'rating_5' => 'Reasonable fees?',
        'rating_6' => 'Feel safe and secure in doing transaction?',
        'rating_7' => 'Action Officer knowledgeable on DOLE programs/services?',
        'rating_8' => 'How satisfied are you with your experience?',
    ];

    /**
     * Likert text (lowercased, trimmed) -> 5..1 score. Anything not listed
     * here (including "N/A", "", null) is treated as no response.
     */
    private const LIKERT_MAP = [
        'strongly agree' => 5,
        'agree' => 4,
        'neither agree nor disagree' => 3,
        'neutral' => 3,
        'disagree' => 2,
        'strongly disagree' => 1,
    ];

    /**
     * Section headings shown in the report.
     */
    public static array $clientTypes = [
        'citizen' => [
            'label' => 'MIMAROPA Region',
            'totalLabel' => '(Total # of MIMAROPA Clients)',
        ],
        'business' => [
            'label' => 'Business',
            'totalLabel' => '(Total # of Business Clients)',
        ],
        'government' => [
            'label' => 'Government Agency, Official or Employee',
            'totalLabel' => '(Total # of Government Agency Clients)',
        ],
    ];

    /**
     * Classify the free-text client_type column into one of our 3 sections.
     */
    private function classifyClientType(?string $raw): string
    {
        $val = strtolower(trim((string) $raw));

        if (str_contains($val, 'government') || str_contains($val, 'official') || str_contains($val, 'employee')) {
            return 'government';
        }
        if (str_contains($val, 'business')) {
            return 'business';
        }

        return 'citizen'; // covers "General Public" and any unrecognized value
    }

    /**
     * Convert a Likert text answer to a 1-5 score, or null if unanswered/N-A.
     */
    private function scoreFromLikert(?string $raw): ?int
    {
        $val = strtolower(trim((string) $raw));

        return self::LIKERT_MAP[$val] ?? null;
    }

    /**
     * Pull the service name(s) out of a row. service_availed and
     * other_service are both checked; either can hold one value or several
     * separated by comma / semicolon / pipe.
     *
     * @return string[] unique, trimmed service names (never empty array -
     *                   returns ['Unspecified Service'] if both are blank)
     */
    private function extractServices(array $row): array
    {
        $raw = trim((string) ($row['service_availed'] ?? '')) . ',' . trim((string) ($row['other_service'] ?? ''));
        $parts = preg_split('/[,;|]+/', $raw);

        $services = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $services[$part] = true; // dedupe
            }
        }

        return $services === [] ? ['Unspecified Service'] : array_keys($services);
    }

    /**
     * Build the fully aggregated report for a date range, grouped by
     * client type, then by service.
     */
    public function getAggregatedReport(string $startDate, string $endDate): array
    {
        $rows = $this->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->orderBy('date', 'ASC')
            ->findAll();

        $questionKeys = array_keys(self::$questions);
        $sections = [];

        foreach (self::$clientTypes as $typeKey => $meta) {
            $sections[$typeKey] = [
                'label' => $meta['label'],
                'totalLabel' => $meta['totalLabel'],
                'services' => [],
                'totalClients' => 0, // number of RESPONSES (rows) in this section
                'ratingTotals' => array_fill_keys($questionKeys, [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]),
                'complaintCount' => 0,
                'suggestionCount' => 0,
            ];
        }

        foreach ($rows as $row) {
            $typeKey = $this->classifyClientType($row['client_type'] ?? null);

            $sections[$typeKey]['totalClients']++;

            // Section-level rating totals: counted ONCE per response (not per service),
            // so percentages against totalClients stay accurate even when a client
            // selects more than one service.
            foreach ($questionKeys as $qKey) {
                $score = $this->scoreFromLikert($row[$qKey] ?? null);
                if ($score !== null) {
                    $sections[$typeKey]['ratingTotals'][$qKey][$score]++;
                }
            }

            $hasComplaint = trim((string) ($row['complaint'] ?? '')) !== '';
            $hasSuggestion = trim((string) ($row['suggestions'] ?? '')) !== '';
            if ($hasComplaint) {
                $sections[$typeKey]['complaintCount']++;
            }
            if ($hasSuggestion) {
                $sections[$typeKey]['suggestionCount']++;
            }

            // Service-level breakdown: every service the client mentioned gets
            // credited with this response's full set of ratings.
            foreach ($this->extractServices($row) as $service) {
                if (!isset($sections[$typeKey]['services'][$service])) {
                    $sections[$typeKey]['services'][$service] = [
                        'totalClients' => 0,
                        'ratings' => array_fill_keys($questionKeys, [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]),
                        'complaintCount' => 0,
                        'suggestionCount' => 0,
                    ];
                }

                $sections[$typeKey]['services'][$service]['totalClients']++;
                if ($hasComplaint) {
                    $sections[$typeKey]['services'][$service]['complaintCount']++;
                }
                if ($hasSuggestion) {
                    $sections[$typeKey]['services'][$service]['suggestionCount']++;
                }

                foreach ($questionKeys as $qKey) {
                    $score = $this->scoreFromLikert($row[$qKey] ?? null);
                    if ($score !== null) {
                        $sections[$typeKey]['services'][$service]['ratings'][$qKey][$score]++;
                    }
                }
            }
        }

        // No-response per question, per section = responses in that section that left it blank/N-A.
        foreach ($sections as $typeKey => &$section) {
            $section['noResponse'] = [];
            foreach ($questionKeys as $qKey) {
                $answered = array_sum($section['ratingTotals'][$qKey]);
                $section['noResponse'][$qKey] = max(0, $section['totalClients'] - $answered);
            }
            ksort($section['services']);
        }
        unset($section);

        return $sections;
    }

    /**
     * Sum every section together for the closing "Overall Score" block.
     */
    public function getOverallTotals(array $sections): array
    {
        $questionKeys = array_keys(self::$questions);
        $overall = [
            'totalClients' => 0,
            'ratingTotals' => array_fill_keys($questionKeys, [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0]),
            'noResponse' => array_fill_keys($questionKeys, 0),
            'complaintCount' => 0,
            'suggestionCount' => 0,
        ];

        foreach ($sections as $section) {
            $overall['totalClients'] += $section['totalClients'];
            $overall['complaintCount'] += $section['complaintCount'];
            $overall['suggestionCount'] += $section['suggestionCount'];

            foreach ($questionKeys as $qKey) {
                foreach ([5, 4, 3, 2, 1] as $s) {
                    $overall['ratingTotals'][$qKey][$s] += $section['ratingTotals'][$qKey][$s];
                }
                $overall['noResponse'][$qKey] += $section['noResponse'][$qKey];
            }
        }

        return $overall;
    }

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];
}
