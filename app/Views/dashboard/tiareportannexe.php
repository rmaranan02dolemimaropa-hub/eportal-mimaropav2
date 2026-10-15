<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-sheet.css') ?>">

<?php
$fmt = static fn($v) => $v === null ? '—' : number_format((float) $v, 2);
$grid = static fn(array $g, array $heads) => view('dashboard/_d1_grid', [
    'g' => $g,
    'heads' => $heads,
    'label' => 'Name of Superior-rater',
]);

$compHeads = [];
if ($report) {
    foreach ($report['comp']['labels'] as $i => $t) {
        $compHeads[] = $t !== '' ? $t : "Competency No $i";
    }
}

// Quarter of a training (from its date text, e.g. "March 10-14, 2026")
$monthNames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$quarterOf = static function (array $training) use ($monthNames): string {
    $text = (string) ($training['training_dates'] ?? '');
    $month = 0;
    $year = 0;
    $m = [];
    $y = [];

    if (preg_match('/\b(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*/i', $text, $m)) {
        $month = (int) array_search(strtolower($m[1]), $monthNames, true) + 1;
    }
    if (preg_match('/\b(20\d{2})\b/', $text, $y)) {
        $year = (int) $y[1];
    }
    return ($month > 0 && $year > 0) ? $year . '-Q' . (int) ceil($month / 3) : '';
};

// Years shown in the quarter dropdown: every year found in the data + the current year
$years = [(int) date('Y')];
foreach (($trainings ?? []) as $training) {
    $q = $quarterOf($training);
    if ($q !== '') {
        $years[] = (int) substr($q, 0, 4);
    }
}
$years = array_values(array_unique($years));
rsort($years);
$qMonths = [1 => 'Jan–Mar', 2 => 'Apr–Jun', 3 => 'Jul–Sep', 4 => 'Oct–Dec'];
?>

<main class="main-content">

    <div class="page-header no-print">
        <div>
            <h1 class="page-title">TIA Report</h1>
            <p class="page-subtitle">Annex E-1 – Summary of Superior-Rater Responses</p>
        </div>
        <?php if ($report && $report['n'] > 0): ?>
            <div class="d-flex gap-2 tia-page">
                <a href="<?= site_url('tia-report-superior/export') ?>?t=<?= urlencode($code) ?>"
                    class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </a>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print / Save as PDF
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Training picker -->
    <div class="panel mb-3 no-print tia-picker">
        <form method="get" action="<?= site_url('tia-report-superior') ?>" class="row g-2 align-items-end p-3">
            <div class="col-md-2">
                <label class="form-label">Year</label>
                <select id="filterYear" class="form-select" autocomplete="off">
                    <option value="">All years</option>
                    <?php foreach ($years as $yr): ?>
                        <option value="<?= $yr ?>"><?= $yr ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quarter</label>
                <select id="filterQuarter" class="form-select" autocomplete="off">
                    <option value="">All quarters</option>
                    <?php foreach ($qMonths as $qn => $label): ?>
                        <option value="<?= $qn ?>">Q<?= $qn ?> (<?= $label ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" id="trainingSearch" class="form-control" placeholder="Search trainings..."
                        autocomplete="off">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Training</label>
                <select name="t" class="form-select" onchange="this.form.submit()">
                    <option value="">— Select a training —</option>
                    <?php foreach ($trainings as $t): ?>
                        <?php
                        $isSel = $t['training_code'] === $code ? 'selected' : '';
                        $n = (int) $t['respondents'];
                        ?>
                        <option value="<?= esc($t['training_code'], 'attr') ?>"
                            data-quarter="<?= esc($quarterOf($t), 'attr') ?>" <?= $isSel ?>>
                            <?= esc($t['training_title']) ?> (<?= esc($t['training_dates']) ?>) – <?= $n ?>
                            superior<?= $n === 1 ? '' : 's' ?> rated
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">View Report</button>
            </div>
        </form>
    </div>

    <?php if (!$report): ?>
        <div class="panel p-5 text-center text-muted">Select a training to view its report.</div>

    <?php elseif ($report['n'] === 0): ?>
        <div class="panel p-5 text-center text-muted">
            No superior has submitted the rating sheet (Annex E) for this training yet.
        </div>

    <?php else: ?>
        <div class="panel d1 annex-e sheet">

            <!-- Header with logos -->
            <div class="header-banner">
                <div class="dole-cell"><img src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE"></div>
                <div class="agency-title">
                    <p class="country">Republic of the Philippines</p>
                    <p class="dept">DEPARTMENT OF LABOR AND EMPLOYMENT</p>
                    <p class="region">MIMAROPA REGION</p>
                </div>
                <div class="iso-cell"><img src="<?= base_url('assets/img/iso.png') ?>" alt="ISO 9001:2015 Certification">
                </div>
                <div class="bp-cell"><img src="<?= base_url('assets/img/bagong_pilipinas.png') ?>" alt="Bagong Pilipinas">
                </div>
            </div>

            <div class="text-center mb-3">
                <h5 class="mt-2 mb-1 fw-bold">SUMMARY OF SUPERIOR-RATER RESPONSES</h5>
                <div><em>Title of the Training:</em> <strong><?= esc($report['training']['training_title']) ?></strong>
                </div>
                <div class="text-muted small"><?= esc($report['training']['training_dates']) ?></div>
            </div>

            <div class="small mb-3">
                1 = Strongly Disagree &nbsp; 2 = Disagree &nbsp; 3 = Agree &nbsp; 4 = Strongly Agree
            </div>

            <h6>I. Attitudinal Outcomes</h6>
            <?= $grid($report['att'], [
                '1. The participant has adopted a more positive attitude towards work.',
                '2. The participant has shown improved work habits after the training.',
                '3. The participant\'s motivation to perform the job has increased.',
                '4. The participant\'s interpersonal skill was enhanced.',
                '5. The participant has been open to learning new things at work.',
            ]) ?>

            <h6>II. Performance Impact</h6>
            <?= $grid($report['perf'], [
                '1. The participant\'s quality of work has improved.',
                '2. The participant completed tasks with fewer errors.',
                '3. The participant accomplishes tasks at a faster rate.',
                '4. The participant was able to manage workload better.',
                '5. The participant became confident in performing work.',
            ]) ?>

            <div class="sub">"After attending the training, participant's productivity on the following areas have improved"
            </div>
            <?= $grid($report['comp'], $compHeads) ?>

            <h6>III. Critical Success Factors</h6>
            <?= $grid($report['app'], [
                '1. The training attended by the participant was relevant and directly applicable to the job.',
                '2. The participant used the new knowledge gained from the training in the job.',
                '3. The participant used the skills learned from the training in performing tasks.',
                '4. The participant was able to introduce new systems, procedures or innovations at work.',
                '5. The participant has frequently applied the new knowledge/skill at work.',
            ]) ?>

            <!-- Final summary -->
            <table class="table table-bordered table-sm mt-4" style="max-width:420px;">
                <tbody>
                    <tr>
                        <th>I. Attitudinal Outcomes</th>
                        <td class="num"><?= $fmt($report['summary']['att']) ?></td>
                    </tr>
                    <tr>
                        <th>II. Performance Impact</th>
                        <td class="num"><?= $fmt($report['summary']['perf']) ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="num"><?= $fmt($report['summary']['comp']) ?></td>
                    </tr>
                    <tr>
                        <th>III. Critical Success Factors</th>
                        <td class="num"><?= $fmt($report['summary']['app']) ?></td>
                    </tr>
                    <?php if ($report['summary']['csf_twice']): ?>
                        <tr>
                            <td></td>
                            <td class="num"><?= $fmt($report['summary']['app']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th class="text-end">Average:</th>
                        <td class="num fw-bold"><?= number_format((float) $report['summary']['average'], 3) ?></td>
                    </tr>
                    <tr>
                        <th class="text-end">Adjectival Rating:</th>
                        <td class="fw-bold"><?= esc($report['summary']['rating']) ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
    <?php endif; ?>

</main>

<?= $this->include('dashboard_layout/loading') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/tia-quarter-picker.js') ?>"></script>