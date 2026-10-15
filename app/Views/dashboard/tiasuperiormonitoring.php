<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">

<?php
$qs = fn(array $over = []) => http_build_query(array_merge(
    ['status' => $status, 'quarter' => $quarter, 'year' => $year, 's' => $search],
    $over


));
$qMonths = [1 => 'Jan–Mar', 2 => 'Apr–Jun', 3 => 'Jul–Sep', 4 => 'Oct–Dec'];
?>

<main class="main-content tia-page">

    <div class="page-header">
        <div>
            <h1 class="page-title">Rating Monitoring</h1>
            <p class="page-subtitle">See which participants you still need to rate</p>
        </div>
    </div>

    <!-- Summary -->
    <div class="row g-3 mb-3">
        <div class="col-4">
            <div class="panel p-3 text-center">
                <div class="text-muted small">Participants</div>
                <div class="fs-3 fw-semibold"><?= $totals['total'] ?></div>
            </div>
        </div>
        <div class="col-4">
            <div class="panel p-3 text-center">
                <div class="text-muted small">Rated</div>
                <div class="fs-3 fw-semibold text-success"><?= $totals['rated'] ?></div>
            </div>
        </div>
        <div class="col-4">
            <div class="panel p-3 text-center">
                <div class="text-muted small">Pending</div>
                <div class="fs-3 fw-semibold text-danger"><?= $totals['pending'] ?></div>
            </div>
        </div>
    </div>

    <div class="panel table-panel">
        <div class="panel-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <ul class="nav nav-pills">
                <?php foreach (['pending' => 'Pending', 'rated' => 'Rated', 'all' => 'All'] as $k => $label): ?>
                    <li class="nav-item">
                        <a class="nav-link py-1 <?= $status === $k ? 'active' : '' ?>"
                            href="?<?= esc($qs(['status' => $k]), 'attr') ?>"><?= $label ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Filters -->
        <form method="get" class="d-flex flex-wrap gap-2 align-items-end p-3">
            <input type="hidden" name="status" value="<?= esc($status, 'attr') ?>">

            <div>
                <label class="form-label">Year</label>
                <select name="year" class="form-select" style="width:130px;">
                    <option value="0">All years</option>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" <?= $year === (int) $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label">Quarter</label>
                <select name="quarter" class="form-select" style="width:170px;">
                    <option value="0">All quarters</option>
                    <?php foreach ($qMonths as $n => $l): ?>
                        <option value="<?= $n ?>" <?= $quarter === $n ? 'selected' : '' ?>>Q<?= $n ?> (<?= $l ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex-grow-1" style="min-width:200px;">
                <label class="form-label">Search</label>
                <input type="search" name="s" value="<?= esc($search, 'attr') ?>" class="form-control"
                    placeholder="Participant, training, position…">
            </div>

            <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Apply</button>
            <a class="btn btn-outline-secondary" href="<?= site_url('tia-superior/monitoring') ?>">Reset</a>
        </form>

        <div class="table-responsive">
            <table class="table requests-table tia-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Participant</th>
                        <th>Position</th>
                        <th>Training</th>
                        <th>Training Date</th>
                        <th>Qtr</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)): ?>
                        <?php foreach ($rows as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($r['participant_name']) ?></td>
                                <td><?= esc($r['position'] ?: '—') ?></td>
                                <td><span class="ref-no"><?= esc($r['training_title']) ?></span></td>
                                <td><?= esc($r['training_dates']) ?></td>
                                <td><?= $r['quarter'] ? 'Q' . $r['quarter'] . ' ' . $r['year'] : '—' ?></td>
                                <td class="text-center">
                                    <?php if ($r['rated']): ?>
                                        <span class="badge bg-success" title="<?= esc($r['rating_ref'], 'attr') ?>">Rated</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!$r['rated']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary open-rating"
                                            data-url="<?= esc(site_url('tia-superior/rate/' . rawurlencode($r['training_code']) . '/' . $r['participant_id']), 'attr') ?>"
                                            data-name="<?= esc($r['participant_name'], 'attr') ?>">
                                            <i class="bi bi-star me-1"></i> Rate
                                        </button>
                                    <?php else: ?>
                                        <span
                                            class="text-muted small"><?= esc($r['rated_at'] ? date('M d, Y', strtotime($r['rated_at'])) : '') ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">Nothing to show for these filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rating sheet modal -->
    <div class="modal fade tia-modal" id="ratingSheetModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i> Rating Sheet (Annex E)
                        <span class="text-muted fs-6 ms-2" id="ratingSheetFor"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="ratingSheetFrame" title="Rating Sheet" src="about:blank"
                        style="width:100%;height:78vh;border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>
</main>

<?= $this->include('dashboard_layout/loading') ?>

<!-- remove if dashboard_layout/head already loads Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var modalEl = document.getElementById('ratingSheetModal');
        var frame = document.getElementById('ratingSheetFrame');
        var modal = new bootstrap.Modal(modalEl);

        document.querySelectorAll('.open-rating').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('ratingSheetFor').textContent = btn.dataset.name;
                frame.src = btn.dataset.url;
                modal.show();
            });
        });

        // Refresh the list when the sheet is closed so submitted ratings leave "Pending"
        modalEl.addEventListener('hidden.bs.modal', function () {
            frame.src = 'about:blank';
            window.location.reload();
        });
    })();
</script>