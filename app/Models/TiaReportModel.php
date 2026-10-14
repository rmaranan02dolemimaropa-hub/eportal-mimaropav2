<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Read-only queries for the TIA reports:
 *  - Annex D-1 (self-rater summary)
 *  - Annex E-1 (superior-rater summary)
 *  - Annex H   (public offerings, 1-5 attendees)
 */
class TiaReportModel extends Model
{
    protected $table = 'tia_responses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // ------------------------------------------------------------------
    // Training pickers
    // ------------------------------------------------------------------

    /** Trainings with their number of self-rater respondents (Annex D-1 and H). */
    public function trainingsWithCounts(): array
    {
        return $this->db->query(
            "SELECT t.training_code, t.training_title, t.training_dates,
                    COUNT(r.id) AS respondents
               FROM trainings t
          LEFT JOIN tia_responses r ON r.training_code = t.training_code
           GROUP BY t.id, t.training_code, t.training_title, t.training_dates, t.created_at
           ORDER BY t.created_at DESC"
        )->getResultArray();
    }

    /** Trainings with their number of superior rating sheets (Annex E-1). */
    public function trainingsWithSuperiorCounts(): array
    {
        return $this->db->query(
            "SELECT t.training_code, t.training_title, t.training_dates,
                    COUNT(r.id) AS respondents
               FROM trainings t
          LEFT JOIN tia_superior_responses r ON r.training_code = t.training_code
           GROUP BY t.id, t.training_code, t.training_title, t.training_dates
           ORDER BY t.id DESC"
        )->getResultArray();
    }

    // ------------------------------------------------------------------
    // Single training
    // ------------------------------------------------------------------

    public function findTraining(string $code): ?array
    {
        return $this->db->table('trainings')
            ->where('training_code', $code)
            ->get()
            ->getRowArray() ?: null;
    }

    /** No. of participants (based on the participants list / attendance sheet). */
    public function participantCount(string $code): int
    {
        return (int) $this->db->table('training_participants')
            ->where('training_code', $code)
            ->countAllResults();
    }

    // ------------------------------------------------------------------
    // Responses
    // ------------------------------------------------------------------

    /** All submitted self-rater responses (Annex D) of a training. */
    public function responses(string $code): array
    {
        return $this->db->table('tia_responses')
            ->where('training_code', $code)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** All submitted superior-rater responses (Annex E) of a training. */
    public function superiorResponses(string $code): array
    {
        return $this->db->table('tia_superior_responses')
            ->where('training_code', $code)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** Headcount of self-rater respondents grouped by 'office' or 'position'. */
    public function countBy(string $code, string $column): array
    {
        // Column names can't be bound as parameters, so whitelist them.
        if (!in_array($column, ['office', 'position'], true)) {
            return [];
        }

        return $this->db->query(
            "SELECT {$column} AS label, COUNT(*) AS total
               FROM tia_responses
              WHERE training_code = ?
           GROUP BY {$column}
           ORDER BY total DESC, label ASC",
            [$code]
        )->getResultArray();
    }

    /** Full participants list of a training (attendance sheet). */
    public function participants(string $code): array
    {
        return $this->db->table('training_participants')
            ->where('training_code', $code)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    // ------------------------------------------------------------------
    // Superior-rating monitoring
    // ------------------------------------------------------------------

    private function norm($s): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim((string) $s)));
    }

    /**
     * Every participant of every training (or one training) with a
     * 'rated' flag = has the superior already submitted Annex E for them?
     * Returns [ 'trainings' => [...with counts...], 'rows' => [...] ]
     */
    public function superiorMonitoring(?string $code = null): array
    {
        $tq = $this->db->table('trainings');
        if ($code) {
            $tq->where('training_code', $code);
        }
        $trainings = $tq->orderBy('created_at', 'DESC')->get()->getResultArray();

        $pq = $this->db->table('training_participants');
        $sq = $this->db->table('tia_superior_responses');
        if ($code) {
            $pq->where('training_code', $code);
            $sq->where('training_code', $code);
        }
        $participants = $pq->orderBy('id', 'ASC')->get()->getResultArray();
        $responses = $sq->orderBy('id', 'ASC')->get()->getResultArray();

        // index submitted superior sheets by training + participant (id + date)
        $doneName = [];
        $doneEmail = [];
        foreach ($responses as $r) {
            $tc = $r['training_code'];
            $hit = ['id' => (int) $r['id'], 'at' => $r['created_at'] ?? null];

            $doneName[$tc][$this->norm($r['participant_name'] ?? '')] = $hit;
            if (!empty($r['participant_email'])) {
                $doneEmail[$tc][$this->norm($r['participant_email'])] = $hit;
            }
        }

        $byTraining = [];
        foreach ($trainings as $t) {
            $byTraining[$t['training_code']] = $t + ['total' => 0, 'rated' => 0, 'pending' => 0];
        }

        $rows = [];
        foreach ($participants as $p) {
            $tc = $p['training_code'];
            if (!isset($byTraining[$tc])) {
                continue;
            }
            $name = trim((string) ($p['participant_name'] ?? $p['name'] ?? ''));
            $email = $this->norm($p['email'] ?? '');

            $hit = $doneName[$tc][$this->norm($name)]
                ?? ($email !== '' ? ($doneEmail[$tc][$email] ?? null) : null);
            $rated = $hit !== null;

            $byTraining[$tc]['total']++;
            $byTraining[$tc][$rated ? 'rated' : 'pending']++;

            $rows[] = [
                'training_code' => $tc,
                'training_title' => $byTraining[$tc]['training_title'],
                'training_dates' => $byTraining[$tc]['training_dates'],
                'superior' => $byTraining[$tc]['immediate_superior'] ?? '',
                'name' => $name,
                'position' => trim((string) ($p['position'] ?? '')),
                'email' => trim((string) ($p['email'] ?? '')),
                'rated' => $rated,
                'rated_at' => $hit['at'] ?? null,
                'response_id' => $hit['id'] ?? null,
            ];
        }

        return ['trainings' => array_values($byTraining), 'rows' => $rows];
    }

    public function superiorResponse(int $id): ?array
    {
        return $this->db->table('tia_superior_responses')
            ->where('id', $id)
            ->get()
            ->getRowArray() ?: null;
    }

    // ------------------------------------------------------------------
    // Participant (self-rater, Annex D) monitoring
    // ------------------------------------------------------------------

    /**
     * Every participant of every training (or one training) with a 'rated' flag =
     * has the participant already submitted the Annex D self-rater form?
     */
    public function participantMonitoring(?string $code = null): array
    {
        $tq = $this->db->table('trainings');
        if ($code) {
            $tq->where('training_code', $code);
        }
        $trainings = $tq->orderBy('created_at', 'DESC')->get()->getResultArray();

        $pq = $this->db->table('training_participants');
        $sq = $this->db->table('tia_responses');
        if ($code) {
            $pq->where('training_code', $code);
            $sq->where('training_code', $code);
        }
        $participants = $pq->orderBy('id', 'ASC')->get()->getResultArray();
        $responses = $sq->orderBy('id', 'ASC')->get()->getResultArray();

        $doneName = [];
        $doneEmail = [];
        foreach ($responses as $r) {
            $tc = $r['training_code'];
            $hit = ['id' => (int) $r['id'], 'at' => $r['created_at'] ?? null];

            $doneName[$tc][$this->norm($r['participant_name'] ?? '')] = $hit;
            if (!empty($r['email'])) {
                $doneEmail[$tc][$this->norm($r['email'])] = $hit;
            }
        }

        $byTraining = [];
        foreach ($trainings as $t) {
            $byTraining[$t['training_code']] = $t + ['total' => 0, 'rated' => 0, 'pending' => 0];
        }

        $rows = [];
        foreach ($participants as $p) {
            $tc = $p['training_code'];
            if (!isset($byTraining[$tc])) {
                continue;
            }
            $name = trim((string) ($p['participant_name'] ?? $p['name'] ?? ''));
            $email = $this->norm($p['email'] ?? '');

            $hit = ($email !== '' ? ($doneEmail[$tc][$email] ?? null) : null)
                ?? ($doneName[$tc][$this->norm($name)] ?? null);
            $rated = $hit !== null;

            $byTraining[$tc]['total']++;
            $byTraining[$tc][$rated ? 'rated' : 'pending']++;

            $rows[] = [
                'training_code' => $tc,
                'training_title' => $byTraining[$tc]['training_title'],
                'training_dates' => $byTraining[$tc]['training_dates'],
                'name' => $name,
                'position' => trim((string) ($p['position'] ?? '')),
                'email' => trim((string) ($p['email'] ?? '')),
                'rated' => $rated,
                'rated_at' => $hit['at'] ?? null,
                'response_id' => $hit['id'] ?? null,
            ];
        }

        return ['trainings' => array_values($byTraining), 'rows' => $rows];
    }

    public function participantResponse(int $id): ?array
    {
        return $this->db->table('tia_responses')
            ->where('id', $id)
            ->get()
            ->getRowArray() ?: null;
    }

    // ------------------------------------------------------------------
    // Annex J (manually typed report)
    // ------------------------------------------------------------------

    public const J_FIELDS = [
        'background',
        'methodology',
        'level1',
        'level2',
        'level3_a',
        'level3_b',
        'level3_c',
        'level3_d',
        'analysis',
        'conclusion',
        'recommendations',
    ];

    public function annexJ(string $code): array
    {
        $row = $this->db->table('tia_annex_j')
            ->where('training_code', $code)
            ->get()
            ->getRowArray();

        return $row ?: array_fill_keys(self::J_FIELDS, '');
    }

    public function saveAnnexJ(string $code, array $data): void
    {
        $clean = ['updated_at' => date('Y-m-d H:i:s')];
        foreach (self::J_FIELDS as $f) {
            $clean[$f] = trim((string) ($data[$f] ?? ''));
        }

        $exists = $this->db->table('tia_annex_j')->where('training_code', $code)->countAllResults() > 0;

        if ($exists) {
            $this->db->table('tia_annex_j')->where('training_code', $code)->update($clean);
        } else {
            $this->db->table('tia_annex_j')->insert($clean + ['training_code' => $code]);
        }
    }
}