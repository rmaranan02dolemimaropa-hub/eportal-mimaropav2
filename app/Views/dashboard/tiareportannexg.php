<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-report.css') ?>">

<style>
    /* Annex G is LANDSCAPE, so it has its own sheet and print page (no .d1 class, which is portrait) */
    .annex-g {
        box-sizing: border-box;
        width: 297mm;
        min-height: 210mm;
        margin: 0 auto;
        padding: 15mm;
        background: #fff;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        color: #000;
        font-size: 10pt;
        page: annexg;
    }

    .annex-g .ag-title {
        text-align: center;
        line-height: 1.5;
        margin: 6px 0 14px;
    }

    .annex-g .ag-title b {
        font-size: 11pt;
    }

    .annex-g table.ag-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .annex-g .ag-table th,
    .annex-g .ag-table td {
        border: 1px solid #000;
        padding: 6px 8px;
        vertical-align: middle;
    }

    .annex-g .ag-table th {
        text-align: center;
        font-weight: 400;
        background: #fff;
    }

    .annex-g .ag-table td.ag-office {
        font-weight: 600;
    }

    .annex-g .ag-table tr.ag-row td {
        height: 18mm;
        vertical-align: middle;
        word-break: break-word;
    }

    .annex-g .ag-table td.ag-name {
        text-align: left;
    }

    @page annexg {
        size: A4 landscape;
        margin: 15mm;
    }

    @media print {

        .annex-g,
        .annex-g * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body:has(.annex-g) {
            background: #fff !important;
        }

        body:has(.annex-g) .no-print,
        body:has(.annex-g) .topbar,
        body:has(.annex-g) .sidebar,
        body:has(.annex-g) .navbar,
        body:has(.annex-g) .app-header,
        body:has(.annex-g) .app-footer,
        body:has(.annex-g) .sidebar-offcanvas {
            display: none !important;
        }

        body:has(.annex-g) .app-body {
            display: block !important;
        }

        body:has(.annex-g) .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .annex-g {
            width: 100% !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: 0 !important;
        }

        .annex-g .header-table tr {
            height: auto;
        }

        .annex-g .logo-cell img {
            width: 20mm !important;
            margin-left: 4mm;
        }

        .annex-g .cert-cell img {
            width: 18mm !important;
        }

        .annex-g .ah-annex-tag {
            font-size: 16pt;
        }

        .annex-g thead {
            display: table-header-group;
        }

        .annex-g tr {
            break-inside: avoid;
        }
    }
</style>

<?php
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
            <p class="page-subtitle">Annex G – Directory of Participants and Immediate Superior</p>
        </div>
        <?php if ($report && $report['total'] > 0): ?>
            <div class="tia-page">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print / Save as PDF
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Training picker -->
    <div class="panel mb-3 no-print tia-picker">
        <form method="get" action="<?= site_url('tia-report-annex-g') ?>" class="row g-2 align-items-end p-3">
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
                        <?php $isSel = $t['training_code'] === $code ? 'selected' : ''; ?>
                        <option value="<?= esc($t['training_code'], 'attr') ?>"
                            data-quarter="<?= esc($quarterOf($t), 'attr') ?>" <?= $isSel ?>>
                            <?= esc($t['training_title']) ?> (<?= esc($t['training_dates']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">View Directory</button>
            </div>
        </form>
    </div>

    <?php if (!$report): ?>
        <div class="panel p-5 text-center text-muted no-print">Select a training to view its directory.</div>

    <?php elseif ($report['total'] === 0): ?>
        <div class="panel p-5 text-center text-muted no-print">
            This training has no participants yet. Add them under TIA &rarr; Manage Participants.
        </div>

    <?php else: ?>
        <div class="annex-g annex-h">

            <!-- Header (same as CSF form) -->
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
            <div class="ah-annex-tag">ANNEX G</div>

            <div class="ag-title">
                <b>DIRECTORY</b><br>
                <b>Participants and Immediate Superior</b><br>
                <?= esc($report['training']['training_title']) ?> held on <?= esc($report['training']['training_dates']) ?>
            </div>

            <table class="ag-table">
                <thead>
                    <tr>
                        <th>Participants<br>Name &amp; Position<br>(while in training)</th>
                        <th>Current Position</th>
                        <th>Active email address</th>
                        <th>Employment Status<br>(active, transferred<br>(offices), resigned etc)</th>
                        <th>Name, Position<br>&amp; Email Address<br>of Immediate Superior</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['groups'] as $office => $people): ?>
                        <tr>
                            <td colspan="5" class="ag-office"><?= esc($office) ?></td>
                        </tr>
                        <?php foreach ($people as $i => $p): ?>
                            <tr class="ag-row">
                                <td class="ag-name">
                                    <?= $i + 1 ?>. <?= esc($p['name']) ?>
                                    <?php if ($p['position'] !== ''): ?>
                                        <br><span style="margin-left:14px;"><?= esc($p['position']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td></td>
                                <td><?= esc($p['email']) ?></td>
                                <td></td>
                                <td><?= esc($report['superior']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    <?php endif; ?>

</main>

<?= $this->include('dashboard_layout/loading') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/tia-quarter-picker.js') ?>"></script>