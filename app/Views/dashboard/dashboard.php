<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>



<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">
                Welcome back, <?= session('fullname') ?> — manage and monitor all HR service requests from this
                dashboard.
            </p>
        </div>
        <?php if (session()->getFlashdata('success')): ?>

            <div class="alert alert-success alert-dismissible fade show shadow-sm" id="loginSuccess" role="alert">

                <i class="bi bi-check-circle-fill me-2"></i>

                <?= session()->getFlashdata('success') ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                </button>

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
                <span class="stat-number" data-target="<?= $new_application ?? 0 ?>">0</span>
                <span class="stat-label">New Request</span>
                <span class="stat-trend trend-up"><i class="bi bi-calendar-check me-1"></i> <?= $new_today ?? 0 ?> received
                    today</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-gear"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $processing ?? 0 ?>">0</span>
                <span class="stat-label">Processing</span>
                <span class="stat-trend trend-up"><i class="bi bi-arrow-repeat me-1"></i><?= $processing_today ?? 0 ?>
                    updated today</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-box-arrow-up-right"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $for_releasing ?? 0 ?>">0</span>
                <span class="stat-label">For Releasing</span>
                <span class="stat-trend trend-up"><i class="bi bi-box-seam me-1"></i><?= $releasing_today ?? 0 ?> ready today
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-check-circle"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $completed ?? 0 ?>">0</span>
                <span class="stat-label">Completed</span>
                <span class="stat-trend trend-up"><i class="bi bi-calendar-month me-1"></i><?= $completed_month ?? 0 ?>
                    completed this month</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-red"><i class="bi bi-x-circle"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $rejected ?? 0 ?>">0</span>
                <span class="stat-label">Rejected</span>
                <span class="stat-trend trend-down"><i class="bi bi-calendar-x me-1"></i><?= $rejected_month ?? 0 ?> rejected
                    this month</span>
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

                        <?php
                        $requests = $requests ?? [];
                        $allowedStatus = [
                            'New Application',
                            'Processing',
                            'For Releasing'
                        ];

                        $statusClass = [
                            'New Application' => 'badge-new-application',
                            'Processing' => 'badge-processing',
                            'For Releasing' => 'badge-for-released',
                        ];

                        $hasData = false;
                        ?>

                        <?php if (!empty($requests)): ?>

                            <?php foreach ($requests as $row): ?>

                                <?php if (!in_array($row['status'], $allowedStatus))
                                    continue; ?>

                                <?php $hasData = true; ?>

                                <tr>

                                    <td class="ref-no">
                                        <?= esc($row['reference_no']) ?>
                                    </td>

                                    <td>
                                        <?= esc($row['fullname']) ?>
                                    </td>

                                    <td>
                                        <?= esc($row['service_availed']) ?>
                                    </td>

                                    <td>
                                        <?php

                                        $displayDate = $row['date_requested'];

                                        if ($row['status'] == 'Processing' && $row['date_processing']) {

                                            $displayDate = $row['date_processing'];
                                        } elseif ($row['status'] == 'For Releasing' && $row['date_released']) {

                                            $displayDate = $row['date_released'];
                                        } elseif ($row['status'] == 'Completed' && $row['date_completed']) {

                                            $displayDate = $row['date_completed'];
                                        } elseif ($row['status'] == 'Rejected' && $row['date_rejected']) {

                                            $displayDate = $row['date_rejected'];
                                        }

                                        ?>

                                        <?= date('F d, Y', strtotime($displayDate)) ?>
                                    </td>

                                    <td>
                                        <span class="badge <?= $statusClass[$row['status']] ?>">
                                            <?= esc($row['status']) ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                        <?php if (!$hasData): ?>

                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <br>
                                    No service requests found.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
            <?php if (isset($pager)): ?>
                <nav class="table-pagination">

                    <span class="pagination-summary">
                        Showing <?= count($requests) ?> of <?= $pager->getTotal() ?> requests
                    </span>

                    <ul class="pagination mb-0">

                        <!-- Previous -->
                        <li class="page-item <?= $pager->getCurrentPage() == 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= $pager->getCurrentPage() == 1 ? '#' : $pager->getPageURI($pager->getCurrentPage() - 1) ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php for ($i = 1; $i <= $pager->getPageCount(); $i++): ?>
                            <li class="page-item <?= $i == $pager->getCurrentPage() ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $pager->getPageURI($i) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next -->
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

        <!-- Side Column: Quick Actions -->
        <div class="side-column">

            <!-- Quick Actions -->
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

        </div>
    </div>

</main>

<script>
    /* ==========================================================================
   DOLE MIMAROPA E-Portal — Dashboard Script
   Handles: current date display, animated stat counters, table search filter.
   No external dependencies beyond the Bootstrap bundle already loaded.
   ========================================================================== */

    document.addEventListener("DOMContentLoaded", function() {
        setCurrentDate();
        animateStatCounters();
        enableTableSearch();
    });

    /**
     * Displays today's date in a readable, localized format
     * inside the #currentDate element in the page header.
     */
    function setCurrentDate() {
        const dateEl = document.getElementById("currentDate");
        if (!dateEl) return;

        const today = new Date();
        const options = {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        };
        dateEl.textContent = today.toLocaleDateString("en-US", options);
    }

    /**
     * Animates each .stat-number element from 0 up to its data-target value.
     * Uses requestAnimationFrame for smooth, performant counting.
     */
    function animateStatCounters() {
        const counters = document.querySelectorAll(".stat-number");
        const duration = 900; // milliseconds

        counters.forEach(function(counter) {
            const target = parseInt(counter.getAttribute("data-target"), 10) || 0;
            const startTime = performance.now();

            function tick(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                // Ease-out for a smoother finish
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.round(eased * target);

                counter.textContent = current.toLocaleString("en-US");

                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    counter.textContent = target.toLocaleString("en-US");
                }
            }

            requestAnimationFrame(tick);
        });
    }

    /**
     * Filters visible rows of the Recent Service Requests table
     * as the user types in the search box. Matches against
     * reference number, requestor name, and service name.
     */
    function enableTableSearch() {
        const searchInput = document.querySelector(".search-box input");
        const tableRows = document.querySelectorAll(".requests-table tbody tr");

        if (!searchInput || !tableRows.length) return;

        searchInput.addEventListener("input", function() {
            const query = searchInput.value.trim().toLowerCase();

            tableRows.forEach(function(row) {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(query) ? "" : "none";
            });
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        ['loginAlert', 'loginSuccess'].forEach(function(id) {

            const alert = document.getElementById(id);

            if (alert) {

                setTimeout(function() {

                    alert.style.transition = "opacity .5s";
                    alert.style.opacity = "0";

                    setTimeout(function() {
                        alert.remove();
                    }, 500);

                }, 3000);

            }

        });

    });

    document.addEventListener("DOMContentLoaded", function() {

        const counters = document.querySelectorAll(".stat-number");

        counters.forEach(counter => {

            const target = Number(counter.dataset.target);
            let current = 0;

            const speed = Math.max(1, Math.ceil(target / 40));

            const update = () => {

                current += speed;

                if (current >= target) {
                    counter.textContent = target;
                } else {
                    counter.textContent = current;
                    requestAnimationFrame(update);
                }

            };

            update();

        });

    });
</script>

<!-- Bootstrap 5 JS Bundle (needed for offcanvas / dropdown components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?= $this->include('dashboard_layout/loading') ?>