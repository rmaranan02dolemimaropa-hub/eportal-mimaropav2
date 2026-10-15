<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">
<?php if (in_array($annex, ['d', 'e'], true)): ?>
    <!-- D-1 / E-1 are the wide landscape tables -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tia-sheet.css') ?>">
<?php else: ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/tia-report.css') ?>">
<?php endif; ?>

<?php
$annexLabels = [
    'd' => 'Annex D-1 – Summary of Self-Rater Responses',
    'e' => 'Annex E-1 – Summary of Superior-Rater Responses',
    'g' => 'Annex G – Directory of Participants and Immediate Superior',
    'h' => 'Annex H – Training Impact Assessment Form (Public Offerings)',
    'j' => 'Annex J – Training Impact Assessment Report',
];
$partials = [
    'd' => 'dashboard/annexsheets/_annex_d1_sheet',
    'e' => 'dashboard/annexsheets/_annex_e1_sheet',
    'g' => 'dashboard/annexsheets/_annex_g_sheet',
    'h' => 'dashboard/annexsheets/_annex_h_sheet',
    'j' => 'dashboard/annexsheets/_annex_j_sheet',
];
$qMonths = [1 => 'Jan–Mar', 2 => 'Apr–Jun', 3 => 'Jul–Sep', 4 => 'Oct–Dec'];
$periodLabel = $year > 0
    ? ($quarter > 0 ? 'Q' . $quarter . ' ' . $year . ' (' . $qMonths[$quarter] . ')' : 'Year ' . $year)
    : '';
?>

<?php if (session()->getFlashdata('msg')): ?>
    <div class="alert alert-warning no-print">
        <?= esc(session()->getFlashdata('msg')) ?>
    </div>
<?php endif; ?>

<main class="main-content tia-page">

    <div class="page-header no-print">
        <div>
            <h1 class="page-title">TIA Bulk Report</h1>
            <p class="page-subtitle">Print every training of a quarter in one go</p>
        </div>
        <?php if (!empty($items)): ?>
            <div class="d-flex gap-2">
                <?php if (in_array($annex, ['d', 'e'], true)): ?>
                    <a href="<?= site_url('tia-report-bulk/export') ?>?annex=<?= $annex ?>&year=<?= $year ?>&quarter=<?= $quarter ?>"
                        class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                <?php endif; ?>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print / Save as PDF (<?= count($items) ?>)
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Picker -->
    <div class="panel mb-3 no-print tia-picker">
        <form method="get" action="<?= site_url('tia-report-bulk') ?>" class="row g-2 align-items-end p-3">
            <div class="col-md-4">
                <label class="form-label">Report</label>
                <select name="annex" class="form-select">
                    <?php foreach ($annexLabels as $k => $label): ?>
                        <option value="<?= $k ?>" <?= $annex === $k ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Year</label>
                <select name="year" class="form-select">
                    <?php foreach ($years as $yr): ?>
                        <option value="<?= $yr ?>" <?= $year === (int) $yr ? 'selected' : '' ?>><?= $yr ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quarter</label>
                <select name="quarter" class="form-select">
                    <option value="0" <?= $quarter === 0 ? 'selected' : '' ?>>Whole year</option>
                    <?php foreach ($qMonths as $qn => $label): ?>
                        <option value="<?= $qn ?>" <?= $quarter === $qn ? 'selected' : '' ?>>Q<?= $qn ?> (<?= $label ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" type="submit">Load Reports</button>
            </div>
        </form>
    </div>

    <?php if ($year === 0): ?>
        <div class="panel p-5 text-center text-muted no-print">
            Choose a report, year and quarter, then click “Load Reports”.
        </div>

    <?php elseif (empty($items)): ?>
        <div class="panel p-5 text-center text-muted no-print">
            Nothing to print for <?= esc($periodLabel) ?>.
            <?php if (!empty($skipped)): ?>
                <div class="small mt-2"><?= count($skipped) ?> training(s) found but they have no data for this report yet.
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-light border no-print">
            <strong><?= count($items) ?></strong> report<?= count($items) === 1 ? '' : 's' ?> for
            <strong><?= esc($periodLabel) ?></strong> — one page set per training.
            <?php if (!empty($skipped)): ?>
                <div class="small text-muted mt-1">
                    Skipped (no data yet): <?= esc(implode('; ', $skipped)) ?>
                </div>
            <?php endif; ?>
        </div>

        <?php foreach ($items as $item): ?>
            <?php if ($annex === 'h' && $item['report']['participants'] > $maxAttendees): ?>
                <div class="alert alert-warning no-print">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <?= esc($item['training']['training_title']) ?> has <?= (int) $item['report']['participants'] ?>
                    participants. Annex H is meant for <?= (int) $maxAttendees ?> or fewer.
                </div>
            <?php endif; ?>

            <div class="bulk-sheet">
                <?= view($partials[$annex], $item, ['saveData' => false]) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</main>

<?= $this->include('dashboard_layout/loading') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>