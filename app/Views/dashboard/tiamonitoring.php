<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-dashboard.css') ?>">

<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Superior Rating Monitoring</h1>
            <p class="page-subtitle">Track which participants are still waiting for their superior's rating (Annex E)
            </p>
        </div>
    </div>

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
        <div class="panel-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="panel-title mb-0">Participants</h2>

            <form method="get" class="d-flex flex-wrap gap-2">
                <select name="t" class="form-select form-select-sm" style="min-width:260px;"
                    onchange="this.form.submit()">
                    <option value="">All trainings</option>
                    <?php foreach ($trainings as $t): ?>
                        <option value="<?= esc($t['training_code'], 'attr') ?>" <?= $code === $t['training_code'] ? 'selected' : '' ?>>
                            <?= esc($t['training_title']) ?> — <?= (int) $t['pending'] ?> pending
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="rated" <?= $status === 'rated' ? 'selected' : '' ?>>Rated</option>
                    <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All</option>
                </select>
            </form>
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
                        <th class="text-center">TIA Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): ?>
                        <?php foreach ($rows as $i => $r): ?>
                            <tr>
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
                            <td colspan="7" class="text-center text-muted py-5">
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