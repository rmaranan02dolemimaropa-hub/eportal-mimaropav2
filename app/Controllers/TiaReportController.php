<?php

namespace App\Controllers;

use App\Models\TiaReportModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

        return view('dashboard/tiareport', [
            'trainings' => $trainings,
            'code' => $code,
            'report' => $report,
        ]);
    }

    /**
     * GET /tia-report/export?t=TRN-...
     * Downloads the report as .xlsx (1 summary sheet + 1 sheet per table).
     */
    public function export()
    {
        $model = new TiaReportModel();
        $code = trim((string) $this->request->getGet('t'));
        $training = $code !== '' ? $model->findTraining($code) : null;

        if (!$training) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Training not found.');
        }

        $r = $this->buildReport($model, $training);
        if ($r['n'] === 0) {
            return redirect()->to(site_url('tia-report') . '?t=' . rawurlencode($code));
        }

        $wb = new Spreadsheet();
        $summary = $wb->getActiveSheet();
        $summary->setTitle('Summary');

        // ---- one sheet per table (each returns the cell holding its "Overall") ----
        $refAtt = $this->gridSheet($wb, 'Attitudinal', $r['att'], [
            '1. I adopted a more positive attitude towards work.',
            '2. I have shown improved work habits after the training.',
            '3. My motivation to perform the job has increased.',
            '4. My interpersonal skill was enhanced.',
            '5. I have been open to learning new things at work.',
        ]);
        $refPerf = $this->gridSheet($wb, 'Performance', $r['perf'], [
            '1. The quality of my work has improved.',
            '2. I have completed tasks with fewer errors.',
            '3. I have accomplished my task at a faster rate.',
            '4. I was able to manage workload better.',
            '5. I became confident in performing work.',
        ]);

        $compHeads = [];
        foreach ($r['comp']['labels'] as $i => $t) {
            $compHeads[] = $t !== '' ? $t : "Competency No {$i}";
        }
        $objHeads = [];
        foreach ($r['obj']['labels'] as $i => $t) {
            $objHeads[] = $t !== '' ? $t : "Objective {$i}";
        }

        $refComp = $this->gridSheet($wb, 'Productivity Areas', $r['comp'], $compHeads);
        $refObj = $this->gridSheet($wb, 'CSF Objectives', $r['obj'], $objHeads);
        $refApp = $this->gridSheet($wb, 'CSF Application', $r['app'], [
            '1. The training I attended was relevant and directly applicable to the job.',
            '2. I was able to use the new knowledge gained from the training in the job.',
            '3. I was able to use the skills learned from the training in performing my tasks.',
            '4. I was able to introduce new systems, procedures or innovations at work.',
            '5. I have frequently applied the new knowledge/skill at work.',
        ]);

        $this->part2Sheet($wb, $r['part2']);
        $this->part3Sheet($wb, $r['challenges']);
        $this->part4Sheet($wb, $r['ideas']);

        // ---- Summary sheet (built last, it points to the other sheets) ----
        $this->summarySheet($summary, $r, [$refAtt, $refPerf, $refComp, $refObj, $refApp]);
        $wb->setActiveSheetIndex(0);

        // ---- download ----
        $safe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $training['training_title']);

        ob_start();
        (new Xlsx($wb))->save('php://output');
        $data = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="TIA_Report_' . $safe . '.xlsx"')
            ->setHeader('Cache-Control', 'no-store')
            ->setBody($data);
    }

    // ------------------------------------------------------------------ build

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

    // ---------------------------------------------------------------- helpers

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

    // ------------------------------------------------------------ excel helpers

    /** Write one cell. $text = true forces a string, so answers starting with = + - @ never become formulas. */
    private function put($sheet, int $col, int $row, $value, bool $text = false): void
    {
        $cell = Coordinate::stringFromColumnIndex($col) . $row;
        if ($text) {
            $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
        } else {
            $sheet->setCellValue($cell, $value);
        }
    }

    private function boxStyle($sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($range)->getAlignment()->setWrapText(true)->setVertical('top');
    }

    private function headStyle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['wrapText' => true, 'vertical' => 'center', 'horizontal' => 'center'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEEDE']],
        ]);
    }

    /** Participants x statements sheet with AVERAGE formulas. Returns e.g. 'Attitudinal'!B8 (the Overall cell). */
    private function gridSheet(Spreadsheet $wb, string $title, array $g, array $heads): string
    {
        $sheet = $wb->createSheet();
        $sheet->setTitle($title);

        $count = $g['count'];
        $n = count($g['people']);
        $avgCol = $count + 2;
        $first = Coordinate::stringFromColumnIndex(2);
        $last = Coordinate::stringFromColumnIndex($count + 1);
        $avgL = Coordinate::stringFromColumnIndex($avgCol);

        // header
        $this->put($sheet, 1, 1, 'Name of Participants', true);
        foreach (array_values($heads) as $i => $h) {
            $this->put($sheet, $i + 2, 1, $h, true);
        }
        $this->put($sheet, $avgCol, 1, 'AVERAGE (per pax)', true);

        // participants
        foreach ($g['people'] as $i => $p) {
            $row = $i + 2;
            $this->put($sheet, 1, $row, ($i + 1) . '. ' . $p['name'], true);
            foreach ($p['values'] as $k => $v) {
                if ($v !== null) {
                    $this->put($sheet, $k + 1, $row, $v);
                }
            }
            $this->put($sheet, $avgCol, $row, "=IFERROR(AVERAGE({$first}{$row}:{$last}{$row}),\"\")");
        }

        // group average per column
        $gr = $n + 2;
        $this->put($sheet, 1, $gr, 'AVERAGE (as a group):', true);
        for ($c = 2; $c <= $count + 1; $c++) {
            $L = Coordinate::stringFromColumnIndex($c);
            $this->put($sheet, $c, $gr, "=IFERROR(AVERAGE({$L}2:{$L}" . ($n + 1) . "),\"\")");
        }

        // overall
        $or = $n + 3;
        $this->put($sheet, 1, $or, 'Overall', true);
        $this->put($sheet, 2, $or, "=IFERROR(AVERAGE({$first}{$gr}:{$last}{$gr}),\"\")");

        // look
        $this->boxStyle($sheet, "A1:{$avgL}{$gr}");
        $this->headStyle($sheet, "A1:{$avgL}1");
        $sheet->getStyle("{$avgL}2:{$avgL}{$gr}")->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle("A{$gr}:{$avgL}{$gr}")->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle("A{$gr}:{$avgL}{$gr}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEEDE']],
        ]);
        $sheet->getStyle("A{$or}:B{$or}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBD9A7']],
        ]);
        $sheet->getStyle("B{$or}")->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle("B2:{$last}{$n}")->getAlignment()->setHorizontal('center');

        $sheet->getColumnDimension('A')->setWidth(30);
        for ($c = 2; $c <= $count + 2; $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setWidth(20);
        }
        $sheet->freezePane('B2');

        return "'{$title}'!B{$or}";
    }

    private function part2Sheet(Spreadsheet $wb, array $rows): void
    {
        $sheet = $wb->createSheet();
        $sheet->setTitle('Part II');

        $this->put($sheet, 1, 1, 'Name of Participants', true);
        $this->put($sheet, 2, 1, '3. Other changes in behavior as a result of the training (verbatim)', true);
        $this->put($sheet, 3, 1, '4. Areas at work with measurable improvement (verbatim)', true);

        foreach ($rows as $i => $p) {
            $row = $i + 2;
            $this->put($sheet, 1, $row, ($i + 1) . '. ' . $p['name'], true);
            $this->put($sheet, 2, $row, $p['changes'], true);
            $this->put($sheet, 3, $row, $p['improvement'], true);
        }

        $end = count($rows) + 1;
        $this->boxStyle($sheet, "A1:C{$end}");
        $this->headStyle($sheet, 'A1:C1');
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(55);
        $sheet->getColumnDimension('C')->setWidth(55);
        $sheet->freezePane('B2');
    }

    private function part3Sheet(Spreadsheet $wb, array $c): void
    {
        $sheet = $wb->createSheet();
        $sheet->setTitle('Part III');

        $heads = [
            'Name of Participants',
            'I had no opportunity to use the skills',
            "I didn't have enough time to apply the skills/knowledge",
            'My work environment is not appropriate for the use of these skills/knowledge',
            'My superior does not support this type of program',
            'None',
            'Others (please specify) (verbatim)',
        ];
        foreach ($heads as $i => $h) {
            $this->put($sheet, $i + 1, 1, $h, true);
        }

        $n = count($c['people']);
        foreach ($c['people'] as $i => $p) {
            $row = $i + 2;
            $this->put($sheet, 1, $row, ($i + 1) . '. ' . $p['name'], true);
            $col = 2;
            foreach ($p['flags'] as $f) {
                if ($f) {
                    $this->put($sheet, $col, $row, 1);
                }
                $col++;
            }
            $this->put($sheet, 7, $row, $p['others'], true);
        }

        $tr = $n + 2;
        $this->put($sheet, 1, $tr, 'TOTAL:', true);
        for ($col = 2; $col <= 6; $col++) {
            $L = Coordinate::stringFromColumnIndex($col);
            $this->put($sheet, $col, $tr, "=SUM({$L}2:{$L}" . ($n + 1) . ')');
        }
        $this->put($sheet, 1, $tr + 1, 'TOTAL', true);
        $this->put($sheet, 2, $tr + 1, "=SUM(B{$tr}:F{$tr})");

        $this->boxStyle($sheet, "A1:G{$tr}");
        $this->headStyle($sheet, 'A1:G1');
        $sheet->getStyle("A{$tr}:G{$tr}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEEDE']],
        ]);
        $sheet->getStyle("A" . ($tr + 1) . ":B" . ($tr + 1))->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBD9A7']],
        ]);
        $sheet->getStyle("B2:F{$tr}")->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(30);
        foreach (['B', 'C', 'D', 'E', 'F'] as $L) {
            $sheet->getColumnDimension($L)->setWidth(22);
        }
        $sheet->getColumnDimension('G')->setWidth(40);
        $sheet->freezePane('B2');
    }

    private function part4Sheet(Spreadsheet $wb, array $ideas): void
    {
        $sheet = $wb->createSheet();
        $sheet->setTitle('Part IV');

        $heads = ['Name of Participants', 'Yes', 'If yes, what are these ideas? (verbatim)', 'No', 'If no, why? (verbatim)'];
        foreach ($heads as $i => $h) {
            $this->put($sheet, $i + 1, 1, $h, true);
        }

        $n = count($ideas['people']);
        foreach ($ideas['people'] as $i => $p) {
            $row = $i + 2;
            $this->put($sheet, 1, $row, ($i + 1) . '. ' . $p['name'], true);
            if ($p['yes']) {
                $this->put($sheet, 2, $row, 1);
            } else {
                $this->put($sheet, 4, $row, 1);
            }
            $this->put($sheet, 3, $row, $p['ideas'], true);
            $this->put($sheet, 5, $row, $p['why_not'], true);
        }

        $tr = $n + 2;
        $this->put($sheet, 1, $tr, 'TOTAL:', true);
        $this->put($sheet, 2, $tr, '=SUM(B2:B' . ($n + 1) . ')');
        $this->put($sheet, 4, $tr, '=SUM(D2:D' . ($n + 1) . ')');
        $this->put($sheet, 1, $tr + 1, 'TOTAL', true);
        $this->put($sheet, 2, $tr + 1, "=B{$tr}+D{$tr}");

        $this->boxStyle($sheet, "A1:E{$tr}");
        $this->headStyle($sheet, 'A1:E1');
        $sheet->getStyle("A{$tr}:E{$tr}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEEDE']],
        ]);
        $sheet->getStyle("A" . ($tr + 1) . ":B" . ($tr + 1))->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBD9A7']],
        ]);
        $sheet->getStyle("B2:B{$tr}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("D2:D{$tr}")->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->freezePane('B2');
    }

    /** First sheet: Annex D-1 header, respondent profile, and the final average + adjectival rating. */
    private function summarySheet($sheet, array $r, array $refs): void
    {
        $t = $r['training'];

        $this->put($sheet, 1, 1, 'Republic of the Philippines', true);
        $this->put($sheet, 1, 2, 'DEPARTMENT OF LABOR AND EMPLOYMENT', true);
        $this->put($sheet, 1, 3, 'Intramuros, Manila', true);
        $this->put($sheet, 1, 4, 'SUMMARY OF SELF-RATER RESPONSES (ANNEX D-1)', true);
        $this->put($sheet, 1, 5, 'Title of the Training:', true);
        $this->put($sheet, 2, 5, $t['training_title'], true);
        $this->put($sheet, 1, 6, 'Training Date/s:', true);
        $this->put($sheet, 2, 6, (string) $t['training_dates'], true);
        $this->put($sheet, 1, 7, '1 = Strongly Disagree   2 = Disagree   3 = Agree   4 = Strongly Agree', true);
        $sheet->getStyle('A1:A5')->getFont()->setBold(true);

        // ---- respondent's profile ----
        $row = 9;
        $this->put($sheet, 1, $row, "Respondent's Profile", true);
        $this->put($sheet, 2, $row, 'Headcount', true);
        $this->headStyle($sheet, "A{$row}:B{$row}");
        $start = $row;

        $lines = fn(array $list) => implode("\n", array_map(
            fn($o) => "{$o['label']} - {$o['total']} ({$o['percent']}%)",
            $list
        ));

        $profile = [
            ['No. of participants (based on attendance sheet)', (int) $r['profile']['participants']],
            ['Total no. of survey respondents', (int) $r['profile']['respondents']],
            ['No. of respondents per office', $lines($r['profile']['offices'])],
            ['No. of respondents per position', $lines($r['profile']['positions'])],
            ['Gender', 'Not collected'],
            ['Age', 'Not collected'],
        ];
        foreach ($profile as [$label, $val]) {
            $row++;
            $this->put($sheet, 1, $row, $label, true);
            is_int($val) ? $this->put($sheet, 2, $row, $val) : $this->put($sheet, 2, $row, $val, true);
        }
        $this->boxStyle($sheet, "A{$start}:B{$row}");

        // ---- final summary (formulas pointing to the other sheets) ----
        $row += 3;
        $this->put($sheet, 1, $row, 'RESULT', true);
        $this->put($sheet, 2, $row, 'Overall', true);
        $this->headStyle($sheet, "A{$row}:B{$row}");
        $sumStart = $row;

        $labels = [
            'I. Attitudinal Outcomes',
            'II. Performance Impact',
            '    Productivity Areas',
            'III. Critical Success Factors (Objectives)',
            '    Critical Success Factors (Application)',
        ];
        $first = $row + 1;
        foreach ($labels as $i => $label) {
            $row++;
            $this->put($sheet, 1, $row, $label, true);
            $this->put($sheet, 2, $row, '=' . $refs[$i]);
        }
        $lastRef = $row;

        $row++;
        $this->put($sheet, 1, $row, 'Average:', true);
        $this->put($sheet, 2, $row, "=IFERROR(AVERAGE(B{$first}:B{$lastRef}),\"\")");
        $avgRow = $row;

        // adjectival rating from the same scale used by the web report
        $f = '"—"';
        foreach (array_reverse(self::SCALE) as [$min, $label]) {
            $f = "IF(B{$avgRow}>={$min},\"{$label}\",{$f})";
        }
        $row++;
        $this->put($sheet, 1, $row, 'Adjectival Rating:', true);
        $this->put($sheet, 2, $row, "=IF(B{$avgRow}=\"\",\"—\",{$f})");

        $this->boxStyle($sheet, "A{$sumStart}:B{$row}");
        $sheet->getStyle("B{$first}:B{$avgRow}")->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle("A{$avgRow}:B{$row}")->getFont()->setBold(true);

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(34);
        $sheet->getStyle("B{$start}:B{$row}")->getAlignment()->setHorizontal('left');
    }
}