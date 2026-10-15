<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-report.css') ?>">

<?php
// Input form definition: [field, label, hint]
$inputs = [
    ['background', 'I. Background', ''],
    ['methodology', 'II. Methodology', ''],
    ['level1', 'III. Findings and Observations — Level 1: Evaluation on the aspects of the program', 'Design, facilitation and administration as reflected in the Terminal Report'],
    ['level2', 'Level 2: Evaluation on the knowledge level', 'Knowledge level before and after the course, per the Pre- and Post-test results'],
    ['level3_a', 'Level 3 · A. Level of agreement of the participants in achieving the learning objectives', ''],
    ['level3_b', 'Level 3 · B. Application of learning', 'Percentage of participants\' application of learning'],
    ['level3_c', 'Level 3 · C. Achievement with the application of learning', 'Percentage on organizational and personal application of participants\' learning'],
    ['level3_d', 'Level 3 · D. Focus Group Discussion Result', 'Qualitative result of the FGD: Application of Learning, Benefits Gained, Planned Sustainability'],
    ['analysis', 'IV. Analysis', ''],
    ['conclusion', 'V. Conclusion', ''],
    ['recommendations', 'VI. Recommendations', ''],
];
$v = static fn($k) => esc((string) ($j[$k] ?? ''));

// Quarter of a training (from its date text, e.g. "March 10-14, 2026")
// NOTE: $training is the selected training on this page, so the loop below uses $row instead.
$monthNames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$quarterOf = static function (array $item) use ($monthNames): string {
    $text = (string) ($item['training_dates'] ?? '');
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
foreach (($trainings ?? []) as $row) {
    $q = $quarterOf($row);
    if ($q !== '') {
        $years[] = (int) substr($q, 0, 4);
    }
}
$years = array_values(array_unique($years));
rsort($years);
$qMonths = [1 => 'Jan–Mar', 2 => 'Apr–Jun', 3 => 'Jul–Sep', 4 => 'Oct–Dec'];
?>



<main class="main-content tia-page">

    <div class="page-header no-print">
        <div>
            <h1 class="page-title">TIA Report</h1>
            <p class="page-subtitle">Annex J – Training Impact Assessment Report</p>
        </div>
        <?php if ($training): ?>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print / Save as PDF
            </button>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-success no-print"><?= esc(session()->getFlashdata('msg')) ?></div>
    <?php endif; ?>

    <!-- Training picker -->
    <div class="panel mb-3 no-print tia-picker">
        <form method="get" action="<?= site_url('tia-report-annex-j') ?>" class="row g-2 align-items-end p-3">
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
                        <option value="<?= esc($t['training_code'], 'attr') ?>"
                            data-quarter="<?= esc($quarterOf($t), 'attr') ?>" <?= $t['training_code'] === $code ? 'selected' : '' ?>>
                            <?= esc($t['training_title']) ?> (<?= esc($t['training_dates']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Open</button>
            </div>
        </form>
    </div>

    <?php if (!$training): ?>
        <div class="panel p-5 text-center text-muted no-print">Select a training to write its Annex J report.</div>

    <?php else: ?>

        <!-- Input form -->
        <div class="panel mb-4 no-print">
            <form method="post" action="<?= site_url('tia-report-annex-j/save') ?>" class="p-3">
                <?= csrf_field() ?>
                <input type="hidden" name="training_code" value="<?= esc($code, 'attr') ?>">

                <?php foreach ($inputs as [$field, $label, $hint]): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="j_<?= $field ?>"><?= esc($label) ?></label>
                        <?php if ($hint): ?>
                            <div class="small text-muted fst-italic mb-1"><?= esc($hint) ?></div>
                        <?php endif; ?>
                        <textarea class="form-control" id="j_<?= $field ?>" name="<?= $field ?>"
                            rows="4"><?= $v($field) ?></textarea>
                    </div>
                <?php endforeach; ?>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Annex J
                    </button>
                </div>
            </form>
        </div>

        <!-- Printable sheet -->
        <div class="panel d1 annex-h annex-j-sheet">

            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <img src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE">
                    </td>
                    <td class="agency-title">
                        <p class="country">Republic of the Philippines</p>
                        <p class="dept">DEPARTMENT OF LABOR AND EMPLOYMENT</p>
                        <p class="region">MIMAROPA REGION</p>
                    </td>
                    <td class="cert-cell">
                        <img src="<?= base_url('assets/img/bagong_pilipinas.png') ?>" alt="Bagong Pilipinas">
                        <img src="<?= base_url('assets/img/iso.png') ?>" alt="ISO 9001:2015 Certification">
                    </td>
                </tr>
            </table>
            <div class="ah-annex-tag">ANNEX J</div>

            <div class="aj-title">
                Training Impact Assessment Report on<br>
                <strong><?= esc($training['training_title']) ?></strong> held on <?= esc($training['training_dates']) ?>
            </div>

            <div class="aj-h">I.&nbsp;&nbsp;&nbsp; Background</div>
            <div class="aj-body top"><?= $v('background') ?></div>

            <div class="aj-h">II.&nbsp;&nbsp; Methodology</div>
            <div class="aj-body top"><?= $v('methodology') ?></div>

            <div class="aj-h">III.&nbsp; Findings and Observations</div>

            <div class="aj-lvl">Level 1&nbsp; Evaluation on the aspects of the program.</div>
            <div class="aj-note">• This focuses on design, facilitation and administration as reflected in the Terminal
                Report</div>
            <div class="aj-body"><?= $v('level1') ?></div>

            <div class="aj-lvl">Level 2&nbsp; Evaluation on the knowledge level.</div>
            <div class="aj-note">• Pertains to the knowledge level of the participants before and after the conduct of the
                course as shown in the Pre- and Post-test results.</div>
            <div class="aj-body"><?= $v('level2') ?></div>

            <div class="aj-lvl">Level 3&nbsp; Assessment and Focus Group Discussion Results</div>

            <div class="aj-sub">A.&nbsp; Level of agreement of the participants in achieving the learning objectives</div>
            <div class="aj-body"><?= $v('level3_a') ?></div>

            <div class="aj-sub">B.&nbsp; Application of learning</div>
            <div class="aj-note deep">o Percentage of participants' application of learning</div>
            <div class="aj-body"><?= $v('level3_b') ?></div>

            <div class="aj-sub">C.&nbsp; Achievement with the application of learning</div>
            <div class="aj-note deep">o Percentage on organizational and personal application of
                participants' learning</div>
            <div class="aj-body"><?= $v('level3_c') ?></div>

            <div class="aj-sub">D.&nbsp; Focus Group Discussion Result</div>
            <div class="aj-note deep">• Qualitative discussion on the result of the FGD according to:
                Application of Learning, Benefits Gained, Planned Sustainability</div>
            <div class="aj-body"><?= $v('level3_d') ?></div>

            <div class="aj-h">IV.&nbsp; Analysis</div>
            <div class="aj-body top"><?= $v('analysis') ?></div>

            <div class="aj-h">V.&nbsp;&nbsp; Conclusion</div>
            <div class="aj-body top"><?= $v('conclusion') ?></div>

            <div class="aj-h">VI.&nbsp; Recommendations</div>
            <div class="aj-body top"><?= $v('recommendations') ?></div>

        </div>
    <?php endif; ?>

</main>

<?= $this->include('dashboard_layout/loading') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/tia-quarter-picker.js') ?>"></script>