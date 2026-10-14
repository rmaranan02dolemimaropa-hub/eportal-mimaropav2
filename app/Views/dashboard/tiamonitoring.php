<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-dashboard.css') ?>">

<?php
// Quarter of each row (from the training date text, e.g. "March 10-14, 2026")
$monthNames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$quarters = [];
foreach (($rows ?? []) as $i => $r) {
    $text = (string) ($r['training_dates'] ?? '');
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

    $quarters[$i] = ($month > 0 && $year > 0) ? $year . '-Q' . (int) ceil($month / 3) : '';
}

// Years shown in the dropdown: every year found in the data + the current year
$years = array_map(fn($q) => (int) substr($q, 0, 4), array_filter($quarters));
$years[] = (int) date('Y');
$years = array_values(array_unique($years));
rsort($years);

$qMonths = [1 => 'Jan–Mar', 2 => 'Apr–Jun', 3 => 'Jul–Sep', 4 => 'Oct–Dec'];
?>

<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Superior Rating Monitoring</h1>
            <p class="page-subtitle">Track which participants are still waiting for their superior's rating (Annex E)
            </p>
        </div>
    </div>

    <!-- Result of Notify Superior -->
    <div id="notifyAlert" class="alert d-none" role="alert"></div>

    <!-- Summary cards -->
    <div class="row g-3 mb-3">
        <?php
        $cards = [
            ['all', 'Total Participants', $totals['total'], 'bi-people', 'text-primary'],
            ['pending', 'Pending', $totals['pending'], 'bi-hourglass-split', 'text-warning'],
            ['rated', 'Rated', $totals['rated'], 'bi-check-circle', 'text-success'],
        ];
        foreach ($cards as [$key, $label, $num, $icon, $color]):
            $href = site_url('tia-monitoring') . '?status=' . $key . ($code !== '' ? '&t=' . urlencode($code) : '');
            ?>
            <div class="col-md-4">
                <a href="<?= esc($href, 'attr') ?>" class="text-decoration-none">
                    <div class="panel p-3 d-flex align-items-center gap-3 <?= $status === $key ? 'border border-2' : '' ?>">
                        <i class="bi <?= $icon ?> fs-1 <?= $color ?>"></i>
                        <div>
                            <div class="fs-3 fw-bold text-body"><?= (int) $num ?></div>
                            <div class="text-muted"><?= esc($label) ?></div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="panel table-panel">
        <div class="panel-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h2 class="panel-title mb-0">Participants</h2>

            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                <!-- Training + status (reloads the page) -->
                <form method="get" class="d-flex flex-wrap gap-2">
                    <select name="t" class="form-select form-select-sm" style="width:260px; flex:0 0 auto;"
                        onchange="this.form.submit()">
                        <option value="">All trainings</option>
                        <?php foreach ($trainings as $t): ?>
                            <option value="<?= esc($t['training_code'], 'attr') ?>" <?= $code === $t['training_code'] ? 'selected' : '' ?>>
                                <?= esc($t['training_title']) ?> — <?= (int) $t['pending'] ?> pending
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="status" class="form-select form-select-sm" style="width:110px; flex:0 0 auto;"
                        onchange="this.form.submit()">
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="rated" <?= $status === 'rated' ? 'selected' : '' ?>>Rated</option>
                        <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All</option>
                    </select>
                </form>

                <!-- Quarter + search (filter the rows on this page instantly) -->
                <select id="quarterFilter" class="form-select form-select-sm" autocomplete="off"
                    style="width:200px; flex:0 0 auto;">
                    <option value="">All quarters</option>
                    <?php foreach ($years as $yr): ?>
                        <optgroup label="<?= $yr ?>">
                            <?php for ($qn = 1; $qn <= 4; $qn++): ?>
                                <option value="<?= $yr ?>-Q<?= $qn ?>">Q<?= $qn ?>         <?= $yr ?> (<?= $qMonths[$qn] ?>)</option>
                            <?php endfor; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>

                <div class="input-group input-group-sm" style="width:260px; flex:0 0 auto;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" id="trainingSearch" class="form-control" placeholder="Search participants..."
                        autocomplete="off">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table requests-table tia-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Participant</th>
                        <th>Position</th>
                        <th>Training</th>
                        <th>Training Date</th>
                        <th>Immediate Superior</th>
                        <th>Superior Notified</th>
                        <th class="text-center">TIA Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): ?>
                        <?php foreach ($rows as $i => $r): ?>
                            <?php
                            $rowCode = (string) ($r['training_code'] ?? '');
                            $notifyCount = (int) ($r['notify_count'] ?? 0);
                            $notifiedAt = !empty($r['notified_at']) ? strtotime((string) $r['notified_at']) : false;
                            ?>
                            <tr data-quarter="<?= esc($quarters[$i] ?? '', 'attr') ?>">
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <?= esc($r['name']) ?>
                                    <?php if ($r['email']): ?>
                                        <div class="small text-muted"><?= esc($r['email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($r['position'] ?: '—') ?></td>
                                <td><?= esc($r['training_title']) ?></td>
                                <td><?= esc($r['training_dates']) ?></td>
                                <td><?= esc($r['superior'] ?: '—') ?></td>
                                <td class="notify-cell" data-code="<?= esc($rowCode, 'attr') ?>">
                                    <div class="notify-info">
                                        <?php if ($notifyCount > 0): ?>
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-envelope-check me-1"></i>Sent <?= $notifyCount ?>
                                                time<?= $notifyCount === 1 ? '' : 's' ?>
                                            </span>
                                            <?php if ($notifiedAt): ?>
                                                <div class="small text-muted">Last: <?= esc(date('M d, Y h:i A', $notifiedAt)) ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">Not yet notified</span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($rowCode !== ''): ?>
                                        <button type="button" class="btn btn-sm tool-btn notify-superior-btn mt-1"
                                            data-code="<?= esc($rowCode, 'attr') ?>"
                                            data-superior="<?= esc((string) ($r['superior'] ?? ''), 'attr') ?>"
                                            data-training="<?= esc((string) ($r['training_title'] ?? ''), 'attr') ?>">
                                            <i class="bi bi-send me-1"></i><span
                                                class="lbl"><?= $notifyCount > 0 ? 'Resend' : 'Notify' ?></span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($r['rated']): ?>
                                        <span class="badge bg-success" <?= $r['rated_at'] ? 'title="Rated ' . esc($r['rated_at'], 'attr') . '"' : '' ?>>
                                            <i class="bi bi-check-circle me-1"></i>Rated
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split me-1"></i>Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($r['rated']): ?>
                                        <button type="button" class="btn btn-sm tool-btn view-sheet-btn" title="View rating sheet"
                                            aria-label="View rating sheet" data-bs-toggle="modal" data-bs-target="#sheetModal"
                                            data-name="<?= esc($r['name'], 'attr') ?>"
                                            data-url="<?= esc(site_url('tia-report-monitoring/view/' . $r['response_id']), 'attr') ?>">
                                            <i class="bi bi-eye me-1"></i>View
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm tool-btn" disabled title="Not yet rated">
                                            <i class="bi bi-eye-slash me-1"></i>View
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <?= $status === 'pending' ? 'Nothing pending 🎉 All superiors have rated.' : 'No participants found.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="sheetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-text me-2"></i> Rating Sheet (Annex E)
                        <span class="text-muted fs-6 ms-2" id="sheetFor"></span>
                    </h5>
                    <a id="sheetOpen" href="#" target="_blank" class="btn btn-sm btn-outline-secondary ms-auto me-2">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Open in new tab
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="sheetFrame" title="Rating Sheet" src="about:blank"
                        style="width:100%;height:78vh;border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll('.view-sheet-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            document.getElementById('sheetFrame').src = b.dataset.url;
            document.getElementById('sheetFor').textContent = b.dataset.name;
            document.getElementById('sheetOpen').href = b.dataset.url;
        });
    });
    document.getElementById('sheetModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('sheetFrame').src = 'about:blank';
    });
</script>

<?= $this->include('dashboard_layout/loading') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.TIA = {
        urls: {
            notifySuperior: '<?= site_url('tia/notify-superior') ?>',
        },
        csrf: {
            name: '<?= csrf_token() ?>',
            header: '<?= csrf_header() ?>',
            hash: '<?= csrf_hash() ?>'
        }
    };
</script>
<script src="<?= base_url('assets/js/tia-monitoring-notify.js') ?>"></script>
<script src="<?= base_url('assets/js/tia-search.js') ?>"></script>