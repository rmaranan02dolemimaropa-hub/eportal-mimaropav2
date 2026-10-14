<?php

namespace App\Controllers;

use App\Libraries\TiaExcelExport;
use App\Models\TiaReportModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TiaReportController extends BaseController
{
    /**
     * Adjectival rating scale. VERIFY these cut-offs against AO 229's table.
     * Format: [minimum average, label], highest first.
     */
    private const SCALE = [
        [3.50, 'High Impact'],
        [2.50, 'Moderate Impact'],
        [1.50, 'Low Impact'],
        [1.00, 'No Impact'],
    ];

    /**
     * The sample accomplished Annex E-1 averages FIVE values: its summary box lists the
     * Critical Success Factors score twice (3.16, 2.72, 2.95, 3.6, 3.6 -> 3.206).
     * true = match the sample exactly; false = average the four distinct scores.
     */
    private const E1_COUNT_CSF_TWICE = true;

    /**
     * Annex H lists FIVE adjectival levels (Very High ... No Impact).
     * These cut-offs split the 1-4 rating range into five equal bands.
     * VERIFY against the rating table in AO 229 before official use.
     * Format: [minimum average, label], highest first.
     */
    private const SCALE_H = [
        [3.40, 'Very High Impact'],
        [2.80, 'High Impact'],
        [2.20, 'Moderate Impact'],
        [1.60, 'Low Impact'],
        [1.00, 'No Impact'],
    ];

    /** Annex H is meant for public trainings with this many attendees or fewer. */
    private const H_MAX_ATTENDEES = 5;

    // =====================================================================
    // ANNEX D-1  -  Summary of Self-Rater Responses
    // =====================================================================

    /**
     * GET /tia-report            -> training picker
     * GET /tia-report?t=TRN-...  -> Annex D-1 for that training
     */
    public function index()
    {
        $model = new TiaReportModel();
        $trainings = $model->trainingsWithCounts();
        $code = trim((string) $this->request->getGet('t'));
        $report = null;

        if ($code !== '') {
            $training = $model->findTraining($code);
            if (!$training) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
            }
            $report = $this->buildReport($model, $training);
        }

        return view('dashboard/tiareportannexd', [
            'trainings' => $trainings,
            'code' => $code,
            'report' => $report,
        ]);
    }

    private function buildReport(TiaReportModel $model, array $training): array
    {
        $code = $training['training_code'];
        $rows = $model->responses($code);
        $n = count($rows);

        $att = $this->grid($rows, 'att', 5);
        $perf = $this->grid($rows, 'perf', 5);
        $comp = $this->grid($rows, 'comp', 4);
        $obj = $this->grid($rows, 'obj', 4);
        $app = $this->grid($rows, 'app', 5);

        // Column titles typed by the respondents (competencies / objectives)
        $comp['labels'] = $this->firstTexts($rows, 'comp', 4);
        $obj['labels'] = $this->firstTexts($rows, 'obj', 4);

        // Final average = mean of the 5 overall scores (same as the sample form)
        $overalls = [$att['overall'], $perf['overall'], $comp['overall'], $obj['overall'], $app['overall']];
        $average = $this->mean($overalls);

        return [
            'training' => $training,
            'n' => $n,
            'profile' => [
                'participants' => $model->participantCount($code),
                'respondents' => $n,
                'offices' => $this->withPercent($model->countBy($code, 'office'), $n),
                'positions' => $this->withPercent($model->countBy($code, 'position'), $n),
            ],
            'att' => $att,
            'perf' => $perf,
            'comp' => $comp,
            'obj' => $obj,
            'app' => $app,
            'part2' => $this->part2($rows),
            'challenges' => $this->challenges($rows),
            'ideas' => $this->ideas($rows),
            'summary' => [
                'att' => $att['overall'],
                'perf' => $perf['overall'],
                'comp' => $comp['overall'],
                'obj' => $obj['overall'],
                'app' => $app['overall'],
                'average' => $average,
                'rating' => $this->adjectival($average),
            ],
        ];
    }

    // =====================================================================
    // ANNEX E-1  -  Summary of Superior-Rater Responses
    // =====================================================================

    /** GET /tia-report-superior?t=TRN-... -> Annex E-1 */
    public function superior()
    {
        $model = new TiaReportModel();
        $trainings = $model->trainingsWithSuperiorCounts();
        $code = trim((string) $this->request->getGet('t'));
        $report = null;

        if ($code !== '') {
            $training = $model->findTraining($code);
            if (!$training) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
            }
            $report = $this->buildSuperiorReport($model, $training);
        }

        return view('dashboard/tiareportannexe', [
            'trainings' => $trainings,
            'code' => $code,
            'report' => $report,
        ]);
    }

    private function buildSuperiorReport(TiaReportModel $model, array $training): array
    {
        // grid() reads the row label from 'participant_name'; here it is the superior's name
        $rows = array_map(function ($r) {
            $r['participant_name'] = $r['superior_name'];
            return $r;
        }, $model->superiorResponses($training['training_code']));

        $att = $this->grid($rows, 'att', 5);
        $perf = $this->grid($rows, 'perf', 5);
        $comp = $this->grid($rows, 'comp', 4);
        $app = $this->grid($rows, 'app', 5);   // Critical Success Factors (5 statements)

        $comp['labels'] = $this->firstTexts($rows, 'comp', 4);

        $overalls = [$att['overall'], $perf['overall'], $comp['overall'], $app['overall']];
        if (self::E1_COUNT_CSF_TWICE) {
            $overalls[] = $app['overall'];
        }
        $average = $this->mean($overalls);

        return [
            'training' => $training,
            'n' => count($rows),
            'att' => $att,
            'perf' => $perf,
            'comp' => $comp,
            'app' => $app,
            'summary' => [
                'att' => $att['overall'],
                'perf' => $perf['overall'],
                'comp' => $comp['overall'],
                'app' => $app['overall'],
                'csf_twice' => self::E1_COUNT_CSF_TWICE,
                'average' => $average,
                'rating' => $this->adjectival($average),
            ],
        ];
    }

    // =====================================================================
    // EXCEL EXPORTS  (one sheet each - see app/Libraries/TiaExcelExport.php)
    // =====================================================================

    /** GET /tia-report/export?t=TRN-... -> Annex D-1 as .xlsx */
    public function exportD()
    {
        $model = new TiaReportModel();
        $code = trim((string) $this->request->getGet('t'));
        $training = $code !== '' ? $model->findTraining($code) : null;

        if (!$training) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
        }

        $r = $this->buildReport($model, $training);
        if ($r['n'] === 0) {
            return redirect()->to(site_url('tia-report') . '?t=' . urlencode($code))
                ->with('msg', 'Nothing to export yet - no self-rater responses.');
        }

        $wb = (new TiaExcelExport(self::SCALE))->annexD($r);

        return $this->downloadXlsx($wb, 'Annex-D1_' . $code . '.xlsx');
    }

    /** GET /tia-report-superior/export?t=TRN-... -> Annex E-1 as .xlsx */
    public function exportE()
    {
        $model = new TiaReportModel();
        $code = trim((string) $this->request->getGet('t'));
        $training = $code !== '' ? $model->findTraining($code) : null;

        if (!$training) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
        }

        $r = $this->buildSuperiorReport($model, $training);
        if ($r['n'] === 0) {
            return redirect()->to(site_url('tia-report-superior') . '?t=' . urlencode($code))
                ->with('msg', 'Nothing to export yet - no superior-rater responses.');
        }

        $wb = (new TiaExcelExport(self::SCALE))->annexE($r);

        return $this->downloadXlsx($wb, 'Annex-E1_' . $code . '.xlsx');
    }

    /** Streams a workbook to the browser as a download. */
    private function downloadXlsx(Spreadsheet $wb, string $filename)
    {
        $filename = preg_replace('/[^A-Za-z0-9._-]+/', '_', $filename);

        ob_start();
        (new Xlsx($wb))->save('php://output');
        $data = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0, no-cache, must-revalidate')
            ->setBody($data);
    }

    // =====================================================================
    // ANNEX H  -  Training Impact Assessment Form for Public Offerings
    // =====================================================================

    /**
     * GET /tia-report-annex-h            -> training picker
     * GET /tia-report-annex-h?t=TRN-...  -> Annex H for that training
     */
    public function annexH()
    {
        $model = new TiaReportModel();
        $trainings = $model->trainingsWithCounts();
        $code = trim((string) $this->request->getGet('t'));
        $report = null;

        if ($code !== '') {
            $training = $model->findTraining($code);
            if (!$training) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
            }
            $report = $this->buildAnnexH($model, $training);
        }

        return view('dashboard/tiareportannexh', [
            'trainings' => $trainings,
            'code' => $code,
            'report' => $report,
            'maxAttendees' => self::H_MAX_ATTENDEES,
        ]);
    }

    private function buildAnnexH(TiaReportModel $model, array $training): array
    {
        $code = $training['training_code'];
        $rows = $model->responses($code);
        $n = count($rows);

        // Same self-rater data as Annex D-1
        $att = $this->grid($rows, 'att', 5);
        $perf = $this->grid($rows, 'perf', 5);
        $comp = $this->grid($rows, 'comp', 4);
        $obj = $this->grid($rows, 'obj', 4);
        $app = $this->grid($rows, 'app', 5);

        // Competencies typed in by the respondents (cluster is not collected)
        $competencies = array_values(array_filter(
            $this->firstTexts($rows, 'comp', 4),
            fn($t) => $t !== ''
        ));

        // 3 areas: Performance Impact = work performance + competencies,
        // Critical Success Factors = training objectives + application of knowledge.
        $areas = [
            [
                'label' => 'Attitudinal Outcomes',
                'note' => '',
                'score' => $att['overall'],
            ],
            [
                'label' => 'Performance Impact',
                'note' => '(Changes in work performance and Competencies)',
                'score' => $this->mean([$perf['overall'], $comp['overall']]),
            ],
            [
                'label' => 'Critical Success Factors',
                'note' => '(Achieving Training Objectives and Application of knowledge)',
                'score' => $this->mean([$obj['overall'], $app['overall']]),
            ],
        ];
        foreach ($areas as &$a) {
            $a['rating'] = $this->adjectivalH($a['score']);
        }
        unset($a);

        $overall = $this->mean(array_column($areas, 'score'));

        // Attendees = everyone on the participants list.
        // submitted = true if they already answered the self-rater form (Annex D).
        $norm = fn($s) => strtolower(preg_replace('/\s+/', ' ', trim((string) $s)));

        $doneByEmail = [];
        $doneByName = [];
        foreach ($rows as $r) {
            $doneByName[$norm($r['participant_name'])] = true;
            if (!empty($r['email'])) {
                $doneByEmail[$norm($r['email'])] = true;
            }
        }

        $attendees = [];
        $listedNames = [];
        foreach ($model->participants($code) as $p) {
            $name = trim((string) ($p['participant_name'] ?? $p['name'] ?? ''));
            $email = $norm($p['email'] ?? '');

            $attendees[] = [
                'name' => $name,
                'position' => trim((string) ($p['position'] ?? '')),
                'submitted' => ($email !== '' && isset($doneByEmail[$email]))
                    || isset($doneByName[$norm($name)]),
            ];
            $listedNames[$norm($name)] = true;
        }

        // Respondents who are not on the participants list still count as submitted
        foreach ($rows as $r) {
            if (!isset($listedNames[$norm($r['participant_name'])])) {
                $attendees[] = [
                    'name' => trim((string) $r['participant_name']),
                    'position' => trim((string) ($r['position'] ?? '')),
                    'submitted' => true,
                ];
            }
        }

        // "Challenges encountered and any additional significant inputs"
        $labels = [
            'no_opportunity' => 'No opportunity to use the skills',
            'not_enough_time' => 'Not enough time to apply the skills/knowledge',
            'work_environment' => 'Work environment not appropriate for the use of the skills/knowledge',
            'superior_no_support' => 'Superior does not support this type of program',
        ];
        $ch = $this->challenges($rows);
        $challengeCounts = [];
        foreach ($labels as $k => $label) {
            if (($ch['totals'][$k] ?? 0) > 0) {
                $challengeCounts[] = ['label' => $label, 'total' => (int) $ch['totals'][$k]];
            }
        }
        $others = array_values(array_filter(array_column($ch['people'], 'others'), fn($t) => $t !== ''));

        $ideas = $this->ideas($rows);
        $ideaList = array_values(array_filter(
            array_map(fn($p) => $p['ideas'], $ideas['people']),
            fn($t) => $t !== ''
        ));

        return [
            'training' => $training,
            'n' => $n,
            'participants' => $model->participantCount($code),
            'attendees' => $attendees,
            'competencies' => $competencies,
            'areas' => $areas,
            'overall' => [
                'score' => $overall,
                'rating' => $this->adjectivalH($overall),
            ],
            'challenge_counts' => $challengeCounts,
            'challenge_others' => $others,
            'ideas' => $ideaList,
        ];
    }



    // =====================================================================
    // MONITORING + READ-ONLY SHEETS
    // =====================================================================

    /** GET /tia-monitoring?status=pending|rated|all&t=TRN-... */
    public function monitoring()
    {
        $model = new TiaReportModel();
        $code = trim((string) $this->request->getGet('t'));
        $status = $this->request->getGet('status');
        $status = in_array($status, ['pending', 'rated', 'all'], true) ? $status : 'pending';

        $data = $model->superiorMonitoring($code !== '' ? $code : null);

        $totals = ['total' => 0, 'rated' => 0, 'pending' => 0];
        foreach ($data['trainings'] as $t) {
            $totals['total'] += $t['total'];
            $totals['rated'] += $t['rated'];
            $totals['pending'] += $t['pending'];
        }

        $rows = array_values(array_filter(
            $data['rows'],
            fn($r) =>
                $status === 'all' || ($status === 'pending' ? !$r['rated'] : $r['rated'])
        ));

        // Attach training code + "superior notified" info (count + last sent) to each row
        $info = [];
        foreach ((new \App\Models\TrainingsModel())->findAll() as $tr) {
            $val = [
                'code' => $tr['training_code'],
                'count' => (int) ($tr['superior_notify_count'] ?? 0),
                'at' => $tr['superior_notified_at'] ?? null,
            ];
            $info['code:' . $tr['training_code']] = $val;
            $info['title:' . $tr['training_title'] . '|' . $tr['training_dates']] = $val;
        }
        foreach ($rows as &$r) {
            $v = $info['code:' . ($r['training_code'] ?? '')]
                ?? $info['title:' . ($r['training_title'] ?? '') . '|' . ($r['training_dates'] ?? '')]
                ?? ['code' => '', 'count' => 0, 'at' => null];
            $r['training_code'] = $v['code'];
            $r['notify_count'] = $v['count'];
            $r['notified_at'] = $v['at'];
        }
        unset($r);

        // use the same picker data as the other pages (all trainings, for the dropdown)
        $all = $model->superiorMonitoring(null)['trainings'];

        return view('dashboard/tiamonitoring', [
            'trainings' => $all,
            'rows' => $rows,
            'totals' => $totals,
            'status' => $status,
            'code' => $code,
        ]);
    }

    /** GET /tia-report-monitoring/view/{id} -> read-only Annex E sheet */
    public function viewSuperior(int $id)
    {
        $model = new TiaReportModel();
        $r = $model->superiorResponse($id);
        if (!$r) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Rating sheet not found.');
        }
        $training = $model->findTraining($r['training_code']);

        return view('dashboard/tiasuperiorsheet', [
            'r' => $r,
            'training' => $training ?? [],
        ]);
    }

    /** GET /tia-report-participant-monitoring?status=pending|rated|all&t=TRN-... */
    public function participantMonitoring()
    {
        $model = new TiaReportModel();
        $code = trim((string) $this->request->getGet('t'));
        $status = $this->request->getGet('status');
        $status = in_array($status, ['pending', 'rated', 'all'], true) ? $status : 'pending';

        $data = $model->participantMonitoring($code !== '' ? $code : null);

        $totals = ['total' => 0, 'rated' => 0, 'pending' => 0];
        foreach ($data['trainings'] as $t) {
            $totals['total'] += $t['total'];
            $totals['rated'] += $t['rated'];
            $totals['pending'] += $t['pending'];
        }

        $rows = array_values(array_filter(
            $data['rows'],
            fn($r) =>
                $status === 'all' || ($status === 'pending' ? !$r['rated'] : $r['rated'])
        ));

        return view('dashboard/tiaparticipantmonitoring', [
            'trainings' => $model->participantMonitoring(null)['trainings'],
            'rows' => $rows,
            'totals' => $totals,
            'status' => $status,
            'code' => $code,
        ]);
    }

    /** GET /tia-report-participant-monitoring/view/{id} -> read-only Annex D sheet */
    public function viewParticipant(int $id)
    {
        $model = new TiaReportModel();
        $r = $model->participantResponse($id);
        if (!$r) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Rating sheet not found.');
        }

        return view('dashboard/tiaparticipantsheet', [
            'r' => $r,
            'training' => $model->findTraining($r['training_code']) ?? [],
        ]);
    }

    // =====================================================================
    // ANNEX G  -  Participants list by office
    // =====================================================================

    /**
     * GET /tia-report-annex-g            -> training picker
     * GET /tia-report-annex-g?t=TRN-...  -> Annex G for that training
     */
    public function annexG()
    {
        $model = new TiaReportModel();
        $trainings = $model->trainingsWithCounts();
        $code = trim((string) $this->request->getGet('t'));
        $report = null;

        if ($code !== '') {
            $training = $model->findTraining($code);
            if (!$training) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
            }
            $report = $this->buildAnnexG($model, $training);
        }

        return view('dashboard/tiareportannexg', [
            'trainings' => $trainings,
            'code' => $code,
            'report' => $report,
        ]);
    }

    private function buildAnnexG(TiaReportModel $model, array $training): array
    {
        $code = $training['training_code'];
        $norm = fn($s) => strtolower(preg_replace('/\s+/', ' ', trim((string) $s)));

        // office of each person, taken from their self-rater response when they have one
        $officeByEmail = [];
        $officeByName = [];
        foreach ($model->responses($code) as $r) {
            $office = trim((string) ($r['office'] ?? ''));
            if ($office === '') {
                continue;
            }
            $officeByName[$norm($r['participant_name'])] = $office;
            if (!empty($r['email'])) {
                $officeByEmail[$norm($r['email'])] = $office;
            }
        }

        $fallbackOffice = trim((string) ($training['office'] ?? '')) ?: 'Office not specified';
        $groups = [];   // office => [rows]

        foreach ($model->participants($code) as $p) {
            $name = trim((string) ($p['participant_name'] ?? $p['name'] ?? ''));
            $email = trim((string) ($p['email'] ?? ''));

            $office = trim((string) ($p['office'] ?? ''))
                ?: ($officeByEmail[$norm($email)] ?? null)
                ?: ($officeByName[$norm($name)] ?? null)
                ?: $fallbackOffice;

            $groups[$office][] = [
                'name' => $name,
                'position' => trim((string) ($p['position'] ?? '')),
                'email' => $email,
            ];
        }

        return [
            'training' => $training,
            'groups' => $groups,
            'total' => array_sum(array_map('count', $groups)),
            'superior' => trim((string) ($training['immediate_superior'] ?? '')),
        ];
    }

    // =====================================================================
    // ANNEX J  -  Input form + sheet
    // =====================================================================

    /**
     * GET /tia-report-annex-j            -> training picker
     * GET /tia-report-annex-j?t=TRN-...  -> input form + Annex J sheet
     */
    public function annexJ()
    {
        $model = new TiaReportModel();
        $code = trim((string) $this->request->getGet('t'));
        $training = null;
        $j = null;

        if ($code !== '') {
            $training = $model->findTraining($code);
            if (!$training) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
            }
            $j = $model->annexJ($code);
        }

        return view('dashboard/tiareportannexj', [
            'trainings' => $model->trainingsWithCounts(),
            'code' => $code,
            'training' => $training,
            'j' => $j,
        ]);
    }

    /** POST /tia-report-annex-j/save */
    public function saveAnnexJ()
    {
        $model = new TiaReportModel();
        $code = trim((string) $this->request->getPost('training_code'));

        if ($code === '' || !$model->findTraining($code)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
        }

        $model->saveAnnexJ($code, $this->request->getPost());

        return redirect()
            ->to(site_url('tia-report-annex-j') . '?t=' . urlencode($code))
            ->with('msg', 'Annex J saved.');
    }

    // =====================================================================
    // SHARED DATA HELPERS
    // =====================================================================

    /** Rows of participants x columns, with per-person and per-column averages. */
    private function grid(array $rows, string $prefix, int $count): array
    {
        $people = [];
        $cols = array_fill(1, $count, []);

        foreach ($rows as $r) {
            $vals = [];
            for ($i = 1; $i <= $count; $i++) {
                $v = $r["{$prefix}_{$i}"] ?? null;
                $v = ($v === null || $v === '') ? null : (int) $v;
                $vals[$i] = $v;
                if ($v !== null) {
                    $cols[$i][] = $v;
                }
            }
            $people[] = [
                'name' => $r['participant_name'],
                'values' => $vals,
                'avg' => $this->mean($vals),
            ];
        }

        $colAvg = [];
        for ($i = 1; $i <= $count; $i++) {
            $colAvg[$i] = $this->mean($cols[$i]);
        }

        return [
            'people' => $people,
            'colAvg' => $colAvg,
            'overall' => $this->mean($colAvg),
            'count' => $count,
        ];
    }

    private function firstTexts(array $rows, string $prefix, int $count): array
    {
        $out = array_fill(1, $count, '');
        for ($i = 1; $i <= $count; $i++) {
            foreach ($rows as $r) {
                $t = trim((string) ($r["{$prefix}_{$i}_text"] ?? ''));
                if ($t !== '') {
                    $out[$i] = $t;
                    break;
                }
            }
        }
        return $out;
    }

    private function part2(array $rows): array
    {
        return array_map(fn($r) => [
            'name' => $r['participant_name'],
            'changes' => trim((string) $r['other_behavior_changes']),
            'improvement' => trim((string) $r['measurable_improvements']),
        ], $rows);
    }

    /** Part III: challenges (1 per participant who ticked it) + verbatim "others". */
    private function challenges(array $rows): array
    {
        $keys = ['no_opportunity', 'not_enough_time', 'work_environment', 'superior_no_support', 'none'];

        $people = [];
        $totals = array_fill_keys($keys, 0);

        foreach ($rows as $r) {
            $picked = json_decode((string) $r['barriers'], true);
            $picked = is_array($picked) ? $picked : [];

            $line = ['name' => $r['participant_name'], 'flags' => [], 'others' => ''];
            foreach ($keys as $k) {
                $has = in_array($k, $picked, true);
                $line['flags'][$k] = $has ? 1 : 0;
                $totals[$k] += $has ? 1 : 0;
            }
            if (in_array('others', $picked, true)) {
                $line['others'] = trim((string) $r['barrier_others_text']);
            }
            $people[] = $line;
        }

        return ['people' => $people, 'totals' => $totals, 'total' => array_sum($totals)];
    }

    /** Part IV: proposed project / innovations. */
    private function ideas(array $rows): array
    {
        $people = [];
        $yes = $no = 0;

        foreach ($rows as $r) {
            $isYes = ($r['presented_ideas'] === 'Yes');
            $yes += $isYes ? 1 : 0;
            $no += $isYes ? 0 : 1;

            $people[] = [
                'name' => $r['participant_name'],
                'yes' => $isYes,
                'ideas' => $isYes ? trim((string) $r['ideas_yes']) : '',
                'why_not' => $isYes ? '' : trim((string) $r['ideas_no']),
            ];
        }

        return ['people' => $people, 'yes' => $yes, 'no' => $no];
    }

    private function withPercent(array $list, int $n): array
    {
        return array_map(fn($x) => [
            'label' => $x['label'],
            'total' => (int) $x['total'],
            'percent' => $n > 0 ? round(((int) $x['total'] / $n) * 100) : 0,
        ], $list);
    }

    /** Mean ignoring nulls; null when there is nothing to average. */
    private function mean(array $vals): ?float
    {
        $vals = array_values(array_filter($vals, fn($v) => $v !== null));
        return $vals ? array_sum($vals) / count($vals) : null;
    }

    private function adjectival(?float $avg): string
    {
        if ($avg === null) {
            return '—';
        }
        foreach (self::SCALE as [$min, $label]) {
            if ($avg >= $min) {
                return $label;
            }
        }
        return '—';
    }

    private function adjectivalH(?float $avg): string
    {
        if ($avg === null) {
            return '—';
        }
        foreach (self::SCALE_H as [$min, $label]) {
            if ($avg >= $min) {
                return $label;
            }
        }
        return '—';
    }
}