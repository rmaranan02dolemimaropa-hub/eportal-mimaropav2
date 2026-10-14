<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Builds the TIA reports (Annex D-1 and E-1) as ONE worksheet each,
 * laid out top to bottom like the web report. Averages are live Excel formulas.
 *
 * Columns: A = names/labels, B-F = rating columns, G = wide text column.
 */
class TiaExcelExport
{
    private const WIDTHS = ['A' => 34, 'B' => 20, 'C' => 20, 'D' => 20, 'E' => 20, 'F' => 20, 'G' => 38];

    /** @var array<int, array{0: float, 1: string}> adjectival scale, highest first */
    private array $scale;

    public function __construct(array $scale)
    {
        $this->scale = $scale;
    }

    // =====================================================================
    // ANNEX D-1
    // =====================================================================

    public function annexD(array $r): Spreadsheet
    {
        $wb = new Spreadsheet();
        $s = $wb->getActiveSheet();
        $s->setTitle('Annex D-1');
        $this->setup($s);

        $this->letterhead($s, 'SUMMARY OF SELF-RATER RESPONSES (ANNEX D-1)');
        $this->info($s, $r['training']);

        $row = $this->profile($s, 9, $r['profile']);

        [$row, $att] = $this->grid($s, $row, 'PART I. Attitudinal Outcomes', $r['att'], [
            '1. I adopted a more positive attitude towards work.',
            '2. I have shown improved work habits after the training.',
            '3. My motivation to perform the job has increased.',
            '4. My interpersonal skill was enhanced.',
            '5. I have been open to learning new things at work.',
        ]);
        [$row, $perf] = $this->grid($s, $row, 'Performance Impact', $r['perf'], [
            '1. The quality of my work has improved.',
            '2. I have completed tasks with fewer errors.',
            '3. I have accomplished my task at a faster rate.',
            '4. I was able to manage workload better.',
            '5. I became confident in performing work.',
        ]);
        [$row, $comp] = $this->grid(
            $s,
            $row,
            'Productivity - "After attending the training, my productivity on the following areas have improved"',
            $r['comp'],
            $this->heads($r['comp']['labels'], 'Competency No')
        );
        [$row, $obj] = $this->grid(
            $s,
            $row,
            'Critical Success Factors - Achieving Training Objectives',
            $r['obj'],
            $this->heads($r['obj']['labels'], 'Objective')
        );
        [$row, $app] = $this->grid($s, $row, 'Critical Success Factors - Application of new knowledge and skills in the workplace', $r['app'], [
            '1. The training I attended was relevant and directly applicable to the job.',
            '2. I was able to use the new knowledge gained from the training in the job.',
            '3. I was able to use the skills learned from the training in performing my tasks.',
            '4. I was able to introduce new systems, procedures or innovations at work.',
            '5. I have frequently applied the new knowledge/skill at work.',
        ]);

        $row = $this->part2($s, $row, $r['part2']);
        $row = $this->part3($s, $row, $r['challenges']);
        $row = $this->part4($s, $row, $r['ideas']);

        $this->result($s, $row, [
            ['I. Attitudinal Outcomes', $att],
            ['II. Performance Impact', $perf],
            ['    Productivity Areas', $comp],
            ['III. Critical Success Factors (Objectives)', $obj],
            ['    Critical Success Factors (Application)', $app],
        ]);

        $wb->setActiveSheetIndex(0);
        return $wb;
    }

    // =====================================================================
    // ANNEX E-1
    // =====================================================================

    public function annexE(array $r): Spreadsheet
    {
        $wb = new Spreadsheet();
        $s = $wb->getActiveSheet();
        $s->setTitle('Annex E-1');
        $this->setup($s);

        $this->letterhead($s, 'SUMMARY OF SUPERIOR-RATER RESPONSES (ANNEX E-1)');
        $this->info($s, $r['training']);

        // respondent count
        $this->put($s, 1, 9, 'Respondent Profile', true);
        $this->put($s, 2, 9, 'Headcount', true);
        $s->mergeCells('B9:D9');
        $this->put($s, 1, 10, 'Total no. of superior-raters', true);
        $this->put($s, 2, 10, (int) $r['n']);
        $s->mergeCells('B10:D10');
        $this->box($s, 'A9:D10');
        $this->head($s, 'A9:D9');
        $s->getStyle('B10:D10')->getAlignment()->setHorizontal('left');
        $row = 12;

        $who = 'Name of Superior-rater';

        [$row, $att] = $this->grid($s, $row, 'I. Attitudinal Outcomes', $r['att'], [
            '1. The participant has adopted a more positive attitude towards work.',
            '2. The participant has shown improved work habits after the training.',
            "3. The participant's motivation to perform the job has increased.",
            "4. The participant's interpersonal skill was enhanced.",
            '5. The participant has been open to learning new things at work.',
        ], $who);
        [$row, $perf] = $this->grid($s, $row, 'II. Performance Impact', $r['perf'], [
            "1. The participant's quality of work has improved.",
            '2. The participant completed tasks with fewer errors.',
            '3. The participant accomplishes tasks at a faster rate.',
            '4. The participant was able to manage workload better.',
            '5. The participant became confident in performing work.',
        ], $who);
        [$row, $comp] = $this->grid(
            $s,
            $row,
            "Productivity - \"After attending the training, participant's productivity on the following areas have improved\"",
            $r['comp'],
            $this->heads($r['comp']['labels'], 'Competency No'),
            $who
        );
        [$row, $app] = $this->grid($s, $row, 'III. Critical Success Factors', $r['app'], [
            '1. The training attended by the participant was relevant and directly applicable to the job.',
            '2. The participant used the new knowledge gained from the training in the job.',
            '3. The participant used the skills learned from the training in performing tasks.',
            '4. The participant was able to introduce new systems, procedures or innovations at work.',
            '5. The participant has frequently applied the new knowledge/skill at work.',
        ], $who);

        $items = [
            ['I. Attitudinal Outcomes', $att],
            ['II. Performance Impact', $perf],
            ['    Productivity Areas', $comp],
            ['III. Critical Success Factors', $app],
        ];
        if (!empty($r['summary']['csf_twice'])) {
            $items[] = ['    Critical Success Factors (counted twice)', $app];
        }
        $this->result($s, $row, $items);

        $wb->setActiveSheetIndex(0);
        return $wb;
    }

    // =====================================================================
    // SHEET SETUP + LETTERHEAD
    // =====================================================================

    private function setup(Worksheet $s): void
    {
        foreach (self::WIDTHS as $col => $w) {
            $s->getColumnDimension($col)->setWidth($w);
        }
        $s->setShowGridlines(false);

        $ps = $s->getPageSetup();
        $ps->setOrientation('landscape');
        $ps->setPaperSize(9);          // A4
        $ps->setFitToPage(true);
        $ps->setFitToWidth(1);
        $ps->setFitToHeight(0);        // as many pages tall as needed

        $m = $s->getPageMargins();
        $m->setLeft(0.4);
        $m->setRight(0.4);
        $m->setTop(0.5);
        $m->setBottom(0.5);
    }

    /** Rows 1-4: DOLE letterhead with logos (DOLE left, ISO + Bagong Pilipinas right). */
    private function letterhead(Worksheet $s, string $title): void
    {
        $lines = [
            [1, 'Republic of the Philippines', 10, false],
            [2, 'DEPARTMENT OF LABOR AND EMPLOYMENT', 13, true],
            [3, 'MIMAROPA REGION', 11, true],
            [4, $title, 12, true],
        ];
        foreach ($lines as [$row, $text, $size, $bold]) {
            $this->put($s, 1, $row, $text, true);
            $s->mergeCells("A{$row}:G{$row}");
            $s->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => $bold, 'size' => $size],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ]);
            $s->getRowDimension($row)->setRowHeight($row === 4 ? 28 : 24);
        }
        $s->getStyle('A4:G4')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('4472C4');

        // [file, cell, x offset in px, height in px] - adjust if the logos overlap
        $logos = [
            ['dole_blogo.png', 'A1', 6, 84],
            ['iso.png', 'G1', 20, 84],
            ['bagong_pilipinas.png', 'G1', 130, 84],
        ];
        foreach ($logos as [$file, $cell, $offsetX, $height]) {
            $path = $this->logoPath($file);
            if ($path === null) {
                continue;
            }
            $d = new Drawing();
            $d->setPath($path);
            $d->setCoordinates($cell);
            $d->setOffsetX($offsetX);
            $d->setOffsetY(4);
            $d->setHeight($height);
            $d->setWorksheet($s);
        }
    }

    /** Rows 5-7: training title, dates, rating legend. */
    private function info(Worksheet $s, array $t): void
    {
        $this->put($s, 1, 5, 'Title of the Training:', true);
        $this->put($s, 2, 5, $t['training_title'], true);
        $s->mergeCells('B5:G5');

        $this->put($s, 1, 6, 'Training Date/s:', true);
        $this->put($s, 2, 6, (string) $t['training_dates'], true);
        $s->mergeCells('B6:G6');

        $this->put($s, 1, 7, '1 = Strongly Disagree   2 = Disagree   3 = Agree   4 = Strongly Agree', true);
        $s->mergeCells('A7:G7');

        $s->getStyle('A5:A6')->getFont()->setBold(true);
        $s->getStyle('B5')->getFont()->setBold(true);
        $s->getStyle('B5:B6')->getAlignment()->setHorizontal('left');
        $s->getStyle('A7')->getFont()->setItalic(true);
    }

    /**
     * Small copy of a logo for the Excel file. Embedding a huge original (for example a
     * 5000x5000 px PNG) exhausts PHP memory, so a resized copy is cached in writable/cache/logos/.
     * Returns null when the image is missing, unreadable or too big to decode.
     */
    private function logoPath(string $file, int $maxPx = 250): ?string
    {
        $src = FCPATH . 'assets/img/' . $file;
        if (!is_file($src)) {
            return null;
        }

        $dir = WRITEPATH . 'cache/logos/';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $dst = $dir . $maxPx . '_' . pathinfo($file, PATHINFO_FILENAME) . '.png';

        if (is_file($dst) && filemtime($dst) >= filemtime($src)) {
            return $dst;
        }

        $size = @getimagesize($src);
        if (!$size) {
            return null;
        }
        [$w, $h] = $size;

        // already small: use the original as it is
        if (max($w, $h) <= 600 && filesize($src) <= 300 * 1024) {
            return $src;
        }

        // too big to decode safely (GD needs ~4 bytes per pixel): skip instead of crashing
        if ($w * $h > 36000000) {
            log_message('error', "Excel logo skipped: {$file} is {$w}x{$h}px - resize it to about 300px.");
            return null;
        }

        $oldLimit = ini_get('memory_limit');
        ini_set('memory_limit', '1024M');

        $img = @imagecreatefromstring((string) file_get_contents($src));
        if (!$img) {
            ini_set('memory_limit', $oldLimit);
            return null;
        }

        $scale = min(1, $maxPx / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));

        $out = imagecreatetruecolor($nw, $nh);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, 255, 255, 255, 127));
        imagecopyresampled($out, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagepng($out, $dst, 9);

        imagedestroy($img);
        imagedestroy($out);
        ini_set('memory_limit', $oldLimit);

        return is_file($dst) ? $dst : null;
    }

    // =====================================================================
    // BLOCKS  (each takes the row to start at and returns the next free row)
    // =====================================================================

    /** Blue section bar across A:G. Returns the next row. */
    private function bar(Worksheet $s, int $row, string $text): int
    {
        $this->put($s, 1, $row, $text, true);
        $s->mergeCells("A{$row}:G{$row}");
        $s->getStyle("A{$row}:G{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['vertical' => 'center', 'wrapText' => true],
        ]);
        $s->getRowDimension($row)->setRowHeight(22);
        return $row + 1;
    }

    /** D-1 respondent profile table. Returns the next row. */
    private function profile(Worksheet $s, int $row, array $p): int
    {
        $hdr = $row;
        $this->put($s, 1, $hdr, "Respondent's Profile", true);
        $this->put($s, 2, $hdr, 'Headcount', true);
        $s->mergeCells("B{$hdr}:D{$hdr}");

        $lines = fn(array $list) => implode("\n", array_map(
            fn($o) => "{$o['label']} - {$o['total']} ({$o['percent']}%)",
            $list
        ));

        $items = [
            ['No. of participants (based on attendance sheet)', (int) $p['participants']],
            ['Total no. of survey respondents', (int) $p['respondents']],
            ['No. of respondents per office', $lines($p['offices'])],
            ['No. of respondents per position', $lines($p['positions'])],
            ['Gender', 'Not collected'],
            ['Age', 'Not collected'],
        ];

        foreach ($items as $i => [$label, $val]) {
            $r = $hdr + 1 + $i;
            $this->put($s, 1, $r, $label, true);
            is_int($val) ? $this->put($s, 2, $r, $val) : $this->put($s, 2, $r, $val, true);
            $s->mergeCells("B{$r}:D{$r}");

            $n = max($this->lines($label, 34), $this->lines((string) $val, 60));
            $s->getRowDimension($r)->setRowHeight(max(18, $n * 15));
        }

        $end = $hdr + count($items);
        $this->box($s, "A{$hdr}:D{$end}");
        $this->head($s, "A{$hdr}:D{$hdr}");
        $s->getStyle('B' . ($hdr + 1) . ":D{$end}")->getAlignment()->setHorizontal('left');

        return $end + 2;
    }

    /**
     * Participants x statements block with AVERAGE formulas.
     * Returns [next row, "B{n}" = the Overall cell].
     */
    private function grid(Worksheet $s, int $row, string $title, array $g, array $heads, string $nameLabel = 'Name of Participants'): array
    {
        $row = $this->bar($s, $row, $title);

        $count = $g['count'];
        $n = count($g['people']);
        $avgCol = $count + 2;
        $last = Coordinate::stringFromColumnIndex($count + 1);
        $avgL = Coordinate::stringFromColumnIndex($avgCol);

        $hdr = $row;
        $a = $hdr + 1;          // first participant row
        $b = $hdr + $n;         // last participant row
        $gr = $b + 1;           // group average row
        $or = $gr + 1;          // overall row

        // header
        $this->put($s, 1, $hdr, $nameLabel, true);
        $maxLines = 1;
        foreach (array_values($heads) as $i => $h) {
            $this->put($s, $i + 2, $hdr, $h, true);
            $maxLines = max($maxLines, $this->lines((string) $h, 20));
        }
        $this->put($s, $avgCol, $hdr, 'AVERAGE (per pax)', true);
        $s->getRowDimension($hdr)->setRowHeight(max(30, $maxLines * 14 + 4));

        // participants
        foreach ($g['people'] as $i => $p) {
            $r = $a + $i;
            $this->put($s, 1, $r, ($i + 1) . '. ' . $p['name'], true);
            foreach ($p['values'] as $k => $v) {
                if ($v !== null) {
                    $this->put($s, $k + 1, $r, $v);
                }
            }
            $this->put($s, $avgCol, $r, "=IFERROR(AVERAGE(B{$r}:{$last}{$r}),\"\")");
        }

        // group average per column
        $this->put($s, 1, $gr, 'AVERAGE (as a group):', true);
        for ($c = 2; $c <= $count + 1; $c++) {
            $L = Coordinate::stringFromColumnIndex($c);
            $this->put($s, $c, $gr, "=IFERROR(AVERAGE({$L}{$a}:{$L}{$b}),\"\")");
        }

        // overall
        $this->put($s, 1, $or, 'Overall', true);
        $this->put($s, 2, $or, "=IFERROR(AVERAGE(B{$gr}:{$last}{$gr}),\"\")");

        // look
        $this->box($s, "A{$hdr}:{$avgL}{$gr}");
        $this->head($s, "A{$hdr}:{$avgL}{$hdr}");
        $s->getStyle("{$avgL}{$a}:{$avgL}{$gr}")->getNumberFormat()->setFormatCode('0.00');
        $s->getStyle("B{$gr}:{$last}{$gr}")->getNumberFormat()->setFormatCode('0.00');
        $s->getStyle("A{$gr}:{$avgL}{$gr}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEEDE']],
        ]);
        $s->getStyle("A{$or}:B{$or}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBD9A7']],
        ]);
        $s->getStyle("B{$or}")->getNumberFormat()->setFormatCode('0.00');
        $s->getStyle("B{$a}:{$last}{$b}")->getAlignment()->setHorizontal('center');

        return [$or + 2, "B{$or}"];
    }

    /** Part II: verbatim behavior changes + measurable improvements. */
    private function part2(Worksheet $s, int $row, array $rows): int
    {
        $row = $this->bar($s, $row, 'Part II. Changes in Behavior and Performance');
        $hdr = $row;

        $this->put($s, 1, $hdr, 'Name of Participants', true);
        $this->put($s, 2, $hdr, '3. Other changes in behavior as a result of the training (verbatim)', true);
        $s->mergeCells("B{$hdr}:D{$hdr}");
        $this->put($s, 5, $hdr, '4. Areas at work with measurable improvement (verbatim)', true);
        $s->mergeCells("E{$hdr}:G{$hdr}");
        $s->getRowDimension($hdr)->setRowHeight(32);

        foreach ($rows as $i => $p) {
            $r = $hdr + 1 + $i;
            $this->put($s, 1, $r, ($i + 1) . '. ' . $p['name'], true);
            $this->put($s, 2, $r, $p['changes'], true);
            $s->mergeCells("B{$r}:D{$r}");
            $this->put($s, 5, $r, $p['improvement'], true);
            $s->mergeCells("E{$r}:G{$r}");

            $n = max($this->lines((string) $p['changes'], 60), $this->lines((string) $p['improvement'], 78), 1);
            $s->getRowDimension($r)->setRowHeight(max(18, $n * 15));
        }

        $end = $hdr + count($rows);
        $this->box($s, "A{$hdr}:G{$end}");
        $this->head($s, "A{$hdr}:G{$hdr}");

        return $end + 2;
    }

    /** Part III: challenges (1 per participant who ticked it) + verbatim "others". */
    private function part3(Worksheet $s, int $row, array $c): int
    {
        $row = $this->bar($s, $row, 'Part III: Challenges');
        $hdr = $row;

        $heads = [
            'Name of Participants',
            'I had no opportunity to use the skills',
            "I didn't have enough time to apply the skills/knowledge",
            'My work environment is not appropriate for the use of these skills/knowledge',
            'My superior does not support this type of program',
            'None',
            'Others (please specify) (verbatim)',
        ];
        $maxLines = 1;
        foreach ($heads as $i => $h) {
            $this->put($s, $i + 1, $hdr, $h, true);
            $maxLines = max($maxLines, $this->lines($h, $i === 6 ? 38 : ($i === 0 ? 34 : 20)));
        }
        $s->getRowDimension($hdr)->setRowHeight(max(30, $maxLines * 14 + 4));

        $n = count($c['people']);
        foreach ($c['people'] as $i => $p) {
            $r = $hdr + 1 + $i;
            $this->put($s, 1, $r, ($i + 1) . '. ' . $p['name'], true);
            $col = 2;
            foreach ($p['flags'] as $f) {
                if ($f) {
                    $this->put($s, $col, $r, 1);
                }
                $col++;
            }
            $this->put($s, 7, $r, $p['others'], true);
            $s->getRowDimension($r)->setRowHeight(max(18, $this->lines((string) $p['others'], 38) * 15));
        }

        $first = $hdr + 1;
        $last = $hdr + $n;
        $tr = $last + 1;

        $this->put($s, 1, $tr, 'TOTAL:', true);
        for ($col = 2; $col <= 6; $col++) {
            $L = Coordinate::stringFromColumnIndex($col);
            $this->put($s, $col, $tr, "=SUM({$L}{$first}:{$L}{$last})");
        }
        $this->put($s, 1, $tr + 1, 'TOTAL', true);
        $this->put($s, 2, $tr + 1, "=SUM(B{$tr}:F{$tr})");

        $this->box($s, "A{$hdr}:G{$tr}");
        $this->head($s, "A{$hdr}:G{$hdr}");
        $s->getStyle("A{$tr}:G{$tr}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEEDE']],
        ]);
        $s->getStyle('A' . ($tr + 1) . ':B' . ($tr + 1))->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBD9A7']],
        ]);
        $s->getStyle("B{$first}:F{$tr}")->getAlignment()->setHorizontal('center');

        return $tr + 3;
    }

    /** Part IV: proposed project / innovations. */
    private function part4(Worksheet $s, int $row, array $ideas): int
    {
        $row = $this->bar($s, $row, 'Part IV: Proposed Project and/or Innovations');
        $hdr = $row;

        $this->put($s, 1, $hdr, 'Name of Participants', true);
        $this->put($s, 2, $hdr, 'Yes', true);
        $this->put($s, 3, $hdr, 'If yes, what are these ideas? (verbatim)', true);
        $s->mergeCells("C{$hdr}:D{$hdr}");
        $this->put($s, 5, $hdr, 'No', true);
        $this->put($s, 6, $hdr, 'If no, why? (verbatim)', true);
        $s->mergeCells("F{$hdr}:G{$hdr}");
        $s->getRowDimension($hdr)->setRowHeight(32);

        $n = count($ideas['people']);
        foreach ($ideas['people'] as $i => $p) {
            $r = $hdr + 1 + $i;
            $this->put($s, 1, $r, ($i + 1) . '. ' . $p['name'], true);
            if ($p['yes']) {
                $this->put($s, 2, $r, 1);
            } else {
                $this->put($s, 5, $r, 1);
            }
            $this->put($s, 3, $r, $p['ideas'], true);
            $s->mergeCells("C{$r}:D{$r}");
            $this->put($s, 6, $r, $p['why_not'], true);
            $s->mergeCells("F{$r}:G{$r}");

            $lines = max($this->lines((string) $p['ideas'], 40), $this->lines((string) $p['why_not'], 58), 1);
            $s->getRowDimension($r)->setRowHeight(max(18, $lines * 15));
        }

        $first = $hdr + 1;
        $last = $hdr + $n;
        $tr = $last + 1;

        $this->put($s, 1, $tr, 'TOTAL:', true);
        $this->put($s, 2, $tr, "=SUM(B{$first}:B{$last})");
        $this->put($s, 5, $tr, "=SUM(E{$first}:E{$last})");
        $this->put($s, 1, $tr + 1, 'TOTAL', true);
        $this->put($s, 2, $tr + 1, "=B{$tr}+E{$tr}");

        $this->box($s, "A{$hdr}:G{$tr}");
        $this->head($s, "A{$hdr}:G{$hdr}");
        $s->getStyle("A{$tr}:G{$tr}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEEDE']],
        ]);
        $s->getStyle('A' . ($tr + 1) . ':B' . ($tr + 1))->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBD9A7']],
        ]);
        $s->getStyle("B{$first}:B{$tr}")->getAlignment()->setHorizontal('center');
        $s->getStyle("E{$first}:E{$tr}")->getAlignment()->setHorizontal('center');

        return $tr + 3;
    }

    /** Final summary: overall scores (cell references), average and adjectival rating. */
    private function result(Worksheet $s, int $row, array $items): int
    {
        $row = $this->bar($s, $row, 'SUMMARY OF RESULTS');
        $hdr = $row;

        $this->put($s, 1, $hdr, 'Area', true);
        $this->put($s, 2, $hdr, 'Overall', true);

        $first = $hdr + 1;
        $r = $hdr;
        foreach ($items as [$label, $ref]) {
            $r++;
            $this->put($s, 1, $r, $label, true);
            $this->put($s, 2, $r, '=' . $ref);
        }
        $lastItem = $r;

        $r++;
        $avgRow = $r;
        $this->put($s, 1, $r, 'Average:', true);
        $this->put($s, 2, $r, "=IFERROR(AVERAGE(B{$first}:B{$lastItem}),\"\")");

        $r++;
        $this->put($s, 1, $r, 'Adjectival Rating:', true);
        $this->put($s, 2, $r, $this->ratingFormula("B{$avgRow}"));

        $this->box($s, "A{$hdr}:B{$r}");
        $this->head($s, "A{$hdr}:B{$hdr}");
        $s->getStyle("B{$first}:B{$lastItem}")->getNumberFormat()->setFormatCode('0.00');
        $s->getStyle("B{$avgRow}")->getNumberFormat()->setFormatCode('0.000');
        $s->getStyle("A{$avgRow}:B{$r}")->getFont()->setBold(true);
        $s->getStyle("B{$first}:B{$r}")->getAlignment()->setHorizontal('left');

        return $r + 2;
    }

    // =====================================================================
    // SMALL HELPERS
    // =====================================================================

    /** Column titles typed by respondents, with a fallback like "Competency No 2". */
    private function heads(array $labels, string $fallback): array
    {
        $out = [];
        foreach ($labels as $i => $t) {
            $out[] = $t !== '' ? $t : "{$fallback} {$i}";
        }
        return $out;
    }

    /** Excel formula that turns the average in $cell into the adjectival rating. */
    private function ratingFormula(string $cell): string
    {
        $f = '"—"';
        foreach (array_reverse($this->scale) as [$min, $label]) {
            $f = "IF({$cell}>={$min},\"{$label}\",{$f})";
        }
        return "=IF({$cell}=\"\",\"—\",{$f})";
    }

    /** Rough number of text lines a cell needs (Excel does not auto-fit merged cells). */
    private function lines(string $text, float $width): int
    {
        $perLine = max(1, (int) floor($width * 0.95));
        $n = 0;
        foreach (explode("\n", $text) as $line) {
            $n += max(1, (int) ceil(mb_strlen($line) / $perLine));
        }
        return max(1, $n);
    }

    /** Write one cell. $text = true forces a string, so answers starting with = + - @ never become formulas. */
    private function put(Worksheet $s, int $col, int $row, $value, bool $text = false): void
    {
        $cell = Coordinate::stringFromColumnIndex($col) . $row;
        if ($text) {
            $s->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
        } else {
            $s->setCellValue($cell, $value);
        }
    }

    private function box(Worksheet $s, string $range): void
    {
        $s->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $s->getStyle($range)->getAlignment()->setWrapText(true)->setVertical('top');
    }

    private function head(Worksheet $s, string $range): void
    {
        $s->getStyle($range)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['wrapText' => true, 'vertical' => 'center', 'horizontal' => 'center'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FDEEDE']],
        ]);
    }
}