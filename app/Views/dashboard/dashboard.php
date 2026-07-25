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
                <span class="stat-number" data-target="<?= $pending ?>">0</span>
                <span class="stat-label">Pending</span>
                <span class="stat-trend trend-up"><i class="bi bi-arrow-up-short"></i>4.2% this week</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-gear"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $processing ?>">0</span>
                <span class="stat-label">Processing</span>
                <span class="stat-trend trend-up"><i class="bi bi-arrow-up-short"></i>1.8% this week</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-check-circle"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $completed ?>">0</span>
                <span class="stat-label">Completed</span>
                <span class="stat-trend trend-up"><i class="bi bi-arrow-up-short"></i>9.6% this week</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-box-arrow-up-right"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $released ?>">0</span>
                <span class="stat-label">Released</span>
                <span class="stat-trend trend-up"><i class="bi bi-arrow-up-short"></i>2.1% this week</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-red"><i class="bi bi-x-circle"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $rejected ?>">0</span>
                <span class="stat-label">Rejected</span>
                <span class="stat-trend trend-down"><i class="bi bi-arrow-down-short"></i>0.6% this week</span>
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
                    <button class="btn tool-btn" type="button">
                        <i class="bi bi-funnel"></i><span class="d-none d-md-inline">Filter</span>
                    </button>
                    <button class="btn tool-btn" type="button">
                        <i class="bi bi-download"></i><span class="d-none d-md-inline">Export</span>
                    </button>
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
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!empty($requests)): ?>

                            <?php foreach ($requests as $row): ?>

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
                                        <?= date('F d, Y', strtotime($row['date_requested'])) ?>
                                    </td>

                                    <td>

                                        <?php

                                        $statusClass = [
                                            'Pending' => 'badge-pending',
                                            'Processing' => 'badge-processing',
                                            'Completed' => 'badge-completed',
                                            'Released' => 'badge-released',
                                            'Rejected' => 'badge-rejected',
                                        ];

                                        ?>

                                        <span class="badge <?= $statusClass[$row['status']] ?? 'bg-secondary' ?>">

                                            <?= esc($row['status']) ?>

                                        </span>

                                    </td>

                                    <td class="text-end">

                                        <div class="row-actions">

                                            <!-- View (Everyone) -->
                                            <button class="row-action-btn" title="View" data-bs-toggle="modal"
                                                data-bs-target="#view<?= $row['id'] ?>">

                                                <i class="bi bi-eye"></i>

                                            </button>

                                            <?php if (session('role') == 'IT ADMINISTRATOR'): ?>

                                                <!-- Edit -->
                                                <button class="row-action-btn" title="Edit" data-bs-toggle="modal"
                                                    data-bs-target="#edit<?= $row['id'] ?>">

                                                    <i class="bi bi-pencil"></i>

                                                </button>

                                                <!-- Delete -->
                                                <button class="row-action-btn row-action-danger" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#delete<?= $row['id'] ?>">

                                                    <i class="bi bi-trash"></i>

                                                </button>

                                            <?php elseif (session('role') == 'HR ADMINISTRATOR'): ?>

                                                <!-- HR can update request status -->
                                                <button class="row-action-btn" title="Update Status" data-bs-toggle="modal"
                                                    data-bs-target="#status<?= $row['id'] ?>">

                                                    <i class="bi bi-arrow-repeat"></i>

                                                </button>

                                            <?php endif; ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

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


        </section>

        <!-- Side Column: Quick Actions + Recent Activity -->
        <div class="side-column">

            <!-- Quick Actions -->
            <section class="panel quick-actions-panel">
                <h2 class="panel-title">Quick Actions</h2>
                <div class="quick-actions-grid">
                    <button class="quick-action-btn" type="button">
                        <i class="bi bi-plus-circle"></i>
                        <span>New Request</span>
                    </button>
                    <button class="quick-action-btn" type="button">
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
</div>



<!-- Bootstrap 5 JS Bundle (needed for offcanvas / dropdown components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /* ==========================================================================
   DOLE MIMAROPA E-Portal — Dashboard Script
   Handles: current date display, animated stat counters, table search filter.
   No external dependencies beyond the Bootstrap bundle already loaded.
   ========================================================================== */

    document.addEventListener("DOMContentLoaded", function () {
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

        counters.forEach(function (counter) {
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

        searchInput.addEventListener("input", function () {
            const query = searchInput.value.trim().toLowerCase();

            tableRows.forEach(function (row) {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(query) ? "" : "none";
            });
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        ['loginAlert', 'loginSuccess'].forEach(function (id) {

            const alert = document.getElementById(id);

            if (alert) {

                setTimeout(function () {

                    alert.style.transition = "opacity .5s";
                    alert.style.opacity = "0";

                    setTimeout(function () {
                        alert.remove();
                    }, 500);

                }, 3000);

            }

        });

    });

    document.addEventListener("DOMContentLoaded", function () {

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


<?= $this->include('dashboard_layout/footer') ?>