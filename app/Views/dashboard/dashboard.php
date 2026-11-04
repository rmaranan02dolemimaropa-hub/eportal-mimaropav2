<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<?php
$requests = $requests ?? [];
$allowedStatus = ['New Application', 'Processing', 'For Releasing'];
$statusClass = [
    'New Application' => 'badge-new-application',
    'Processing' => 'badge-processing',
    'For Releasing' => 'badge-for-released',
];

$new_application = (int) ($new_application ?? 0);
$processing = (int) ($processing ?? 0);
$for_releasing = (int) ($for_releasing ?? 0);
$completed = (int) ($completed ?? 0);
$rejected = (int) ($rejected ?? 0);
$total = $new_application + $processing + $for_releasing + $completed + $rejected;
$pct = static fn(int $n, int $d): int => $d > 0 ? (int) min(100, round($n / $d * 100)) : 0;

$breakdown = [
    ['New Request', 'bi-hourglass-split', 'warning', $new_application],
    ['Processing', 'bi-gear', 'primary', $processing],
    ['For Releasing', 'bi-box-arrow-up-right', 'info', $for_releasing],
    ['Completed', 'bi-check-circle', 'success', $completed],
    ['Rejected', 'bi-x-circle', 'danger', $rejected],
];

// Date shown depends on the status
$dateFor = static function (array $row): ?string {
    if ($row['status'] === 'Processing' && !empty($row['date_processing'])) {
        return $row['date_processing'];
    }
    if ($row['status'] === 'For Releasing' && !empty($row['date_released'])) {
        return $row['date_released'];
    }
    return $row['date_requested'] ?? null;
};

$activeRows = array_values(array_filter($requests, static fn($r) => in_array($r['status'] ?? '', $allowedStatus, true)));

// Needs attention: New waiting 3+ days, Processing older than 7 days
$attention = [];
foreach ($activeRows as $r) {
    $d = $dateFor($r);
    $t = $d ? strtotime($d) : false;
    $age = $t ? max(0, (int) floor((time() - $t) / 86400)) : 0;
    if (($r['status'] === 'New Application' && $age >= 3) || ($r['status'] === 'Processing' && $age >= 7)) {
        $r['_age'] = $age;
        $attention[] = $r;
    }
}
usort($attention, static fn($a, $b) => $b['_age'] <=> $a['_age']);
$attentionTotal = count($attention);
$attention = array_slice($attention, 0, 5);
?>

<style>
    /* Extra side-column panels (prefix dd-) — everything else uses your existing CSS */
    .dd-side-panel {
        margin-top: 1rem;
    }

    .dd-break-row {
        margin-bottom: .8rem;
    }

    .dd-break-head {
        display: flex;
        justify-content: space-between;
        font-size: .85rem;
        margin-bottom: .25rem;
    }

    .dd-break-head i {
        margin-right: .35rem;
    }

    .dd-bar {
        height: 8px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }

    .dd-fill {
        height: 100%;
        width: 0;
        border-radius: 99px;
        transition: width .9s ease;
    }

    .dd-rate {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        border-top: 1px dashed #e2e8f0;
        margin-top: 1rem;
        padding-top: .75rem;
        font-size: .8rem;
        color: #64748b;
    }

    .dd-rate strong {
        font-size: 1.4rem;
        color: #065f46;
    }

    .dd-attn {
        list-style: none;
        margin: .75rem 0 0;
        padding: 0;
    }

    .dd-attn li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        padding: .55rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: .85rem;
    }

    .dd-attn li:last-child {
        border-bottom: 0;
    }

    .dd-attn small {
        display: block;
        color: #64748b;
    }

    .dd-age {
        font-size: .72rem;
        font-weight: 600;
        padding: .15rem .55rem;
        border-radius: 99px;
        background: #fef3c7;
        color: #92400e;
        white-space: nowrap;
    }

    .dd-age.hot {
        background: #fee2e2;
        color: #991b1b;
    }

    .dd-ok {
        text-align: center;
        color: #64748b;
        font-size: .85rem;
        padding: 1rem 0 .25rem;
    }
</style>

<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">
                Welcome back, <?= esc(session('fullname')) ?> — manage and monitor all HR service requests from this
                dashboard.
            </p>
        </div>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" id="loginSuccess" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="page-date">
            <i class="bi bi-calendar3"></i>
            <span id="currentDate"></span>
        </div>
    </div>

    <!-- Statistic Cards -->
    <section class="stats-grid" aria-label="Request statistics">

        <div class="stat-card">
            <div class="stat-icon stat-icon-orange"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $new_application ?>">0</span>
                <span class="stat-label">New Request</span>
                <span class="stat-trend trend-up"><i class="bi bi-calendar-check me-1"></i>
                    <?= (int) ($new_today ?? 0) ?> received today</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-gear"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $processing ?>">0</span>
                <span class="stat-label">Processing</span>
                <span class="stat-trend trend-up"><i
                        class="bi bi-arrow-repeat me-1"></i><?= (int) ($processing_today ?? 0) ?> updated today</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-box-arrow-up-right"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $for_releasing ?>">0</span>
                <span class="stat-label">For Releasing</span>
                <span class="stat-trend trend-up"><i
                        class="bi bi-box-seam me-1"></i><?= (int) ($releasing_today ?? 0) ?> ready today</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-check-circle"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $completed ?>">0</span>
                <span class="stat-label">Completed</span>
                <span class="stat-trend trend-up"><i
                        class="bi bi-calendar-month me-1"></i><?= (int) ($completed_month ?? 0) ?> completed this
                    month</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-red"><i class="bi bi-x-circle"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $rejected ?>">0</span>
                <span class="stat-label">Rejected</span>
                <span class="stat-trend trend-down"><i
                        class="bi bi-calendar-x me-1"></i><?= (int) ($rejected_month ?? 0) ?> rejected this month</span>
            </div>
        </div>

    </section>

    <!-- Table + Side Column -->
    <div class="content-grid">

        <!-- Recent Requests Table -->
        <section class="panel table-panel">
            <div class="panel-header">
                <h2 class="panel-title">Recent Service Requests</h2>
                <div class="panel-tools">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" placeholder="Search requests..."
                            aria-label="Search requests" />
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table requests-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Requestor</th>
                            <th>Service</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeRows as $row): ?>
                            <?php $d = $dateFor($row); ?>
                            <tr>
                                <td class="ref-no" data-label="Reference No."><?= esc($row['reference_no']) ?></td>
                                <td data-label="Requestor"><?= esc($row['fullname']) ?></td>
                                <td data-label="Service"><?= esc($row['service_availed']) ?></td>
                                <td data-label="Date Requested"><?= $d ? date('F d, Y', strtotime($d)) : '—' ?></td>
                                <td data-label="Status">
                                    <span
                                        class="badge <?= $statusClass[$row['status']] ?>"><?= esc($row['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="empty-row" id="emptyRow" <?= $activeRows ? 'style="display:none"' : '' ?>>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i><br>
                                No service requests found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if (isset($pager)): ?>
                <nav class="table-pagination">
                    <span class="pagination-summary">
                        Showing <?= count($requests) ?> of <?= $pager->getTotal() ?> requests
                    </span>
                    <ul class="pagination mb-0">
                        <li class="page-item <?= $pager->getCurrentPage() == 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= $pager->getCurrentPage() == 1 ? '#' : $pager->getPageURI($pager->getCurrentPage() - 1) ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $pager->getPageCount(); $i++): ?>
                            <li class="page-item <?= $i == $pager->getCurrentPage() ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $pager->getPageURI($i) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $pager->getCurrentPage() == $pager->getPageCount() ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= $pager->getCurrentPage() == $pager->getPageCount() ? '#' : $pager->getPageURI($pager->getCurrentPage() + 1) ?>">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </section>

        <!-- Side Column -->
        <div class="side-column">

            <!-- Quick Actions (unchanged) -->
            <section class="panel quick-actions-panel">
                <h2 class="panel-title">Quick Actions</h2>
                <div class="quick-actions-grid">
                    <button class="quick-action-btn" type="button"
                        onclick="window.location.href='<?= site_url('reports') ?>'">
                        <i class="bi bi-bar-chart"></i>
                        <span>Generate Report</span>
                    </button>

                    <?php if (session('role') == 'IT ADMINISTRATOR'): ?>
                        <a href="<?= base_url('users') ?>" class="quick-action-btn">
                            <i class="bi bi-people"></i>
                            <span>Manage Users</span>
                        </a>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Request Status -->
            <section class="panel dd-side-panel">
                <h2 class="panel-title">Request Status</h2>
                <?php if ($total === 0): ?>
                    <div class="dd-ok">No requests yet.</div>
                <?php else: ?>
                    <div class="mt-3">
                        <?php foreach ($breakdown as [$label, $icon, $colour, $n]): ?>
                            <div class="dd-break-row">
                                <div class="dd-break-head">
                                    <span><i class="bi <?= $icon ?> text-<?= $colour ?>"></i><?= esc($label) ?></span>
                                    <span><?= $n ?> · <?= $pct($n, $total) ?>%</span>
                                </div>
                                <div class="dd-bar">
                                    <div class="dd-fill bg-<?= $colour ?>" data-width="<?= $pct($n, $total) ?>"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="dd-rate">
                        <span>Completion rate</span>
                        <strong><?= $pct($completed, $total) ?>%</strong>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Needs Attention -->
            <section class="panel dd-side-panel">
                <h2 class="panel-title">
                    <i class="bi bi-exclamation-circle text-warning me-1"></i>Needs Attention
                    <?php if ($attentionTotal > 0): ?>
                        <span class="badge bg-warning text-dark ms-1">
                            <?= $attentionTotal ?>
                        </span>
                    <?php endif; ?>
                </h2>
                <?php if (empty($attention)): ?>
                    <div class="dd-ok">
                        <i class="bi bi-check2-circle text-success fs-3 d-block mb-1"></i>
                        Nothing is overdue.
                    </div>
                <?php else: ?>
                    <ul class="dd-attn">
                        <?php foreach ($attention as $r): ?>
                            <li>
                                <div>
                                    <strong>
                                        <?= esc($r['reference_no']) ?>
                                    </strong>
                                    <small>
                                        <?= esc($r['service_availed']) ?> ·
                                        <?= esc($r['status']) ?>
                                    </small>
                                </div>
                                <span class="dd-age <?= $r['_age'] >= 14 ? 'hot' : '' ?>">
                                    <?= $r['_age'] ?>d
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

        </div>
    </div>

</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        setCurrentDate();
        animateStatCounters();
        animateBars();
        enableTableSearch();
        autoDismissAlerts();
    });

    function setCurrentDate() {
        const el = document.getElementById("currentDate");
        if (!el) return;
        el.textContent = new Date().toLocaleDateString("en-US", {
            weekday: "long", year: "numeric", month: "long", day: "numeric"
        });
    }

    function animateStatCounters() {
        const duration = 900;
        document.querySelectorAll(".stat-number").forEach(function (el) {
            const target = parseInt(el.getAttribute("data-target"), 10) || 0;
            const start = performance.now();
            function tick(now) {
                const p = Math.min((now - start) / duration, 1);
                el.textContent = Math.round((1 - Math.pow(1 - p, 3)) * target).toLocaleString("en-US");
                if (p < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        });
    }

    function animateBars() {
        requestAnimationFrame(function () {
            document.querySelectorAll(".dd-fill[data-width]").forEach(function (el) {
                el.style.width = el.dataset.width + "%";
            });
        });
    }

    function enableTableSearch() {
        const input = document.querySelector(".search-box input");
        const rows = document.querySelectorAll(".requests-table tbody tr:not(.empty-row)");
        const emptyRow = document.getElementById("emptyRow");
        if (!input || !rows.length) return;

        input.addEventListener("input", function () {
            const q = input.value.trim().toLowerCase();
            let visible = 0;
            rows.forEach(function (row) {
                const show = row.textContent.toLowerCase().includes(q);
                row.style.display = show ? "" : "none";
                if (show) visible++;
            });
            if (emptyRow) emptyRow.style.display = visible ? "none" : "";
        });
    }

    function autoDismissAlerts() {
        ["loginAlert", "loginSuccess"].forEach(function (id) {
            const alert = document.getElementById(id);
            if (!alert) return;
            setTimeout(function () {
                alert.style.transition = "opacity .5s";
                alert.style.opacity = "0";
                setTimeout(function () { alert.remove(); }, 500);
            }, 3000);
        });
    }
</script>

<!-- Bootstrap 5 JS Bundle (needed for offcanvas / dropdown components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?= $this->include('dashboard_layout/loading') ?>