<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>



<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">

    <!-- ==========================================
     DOLE MIMAROPA LOADING SCREEN
========================================== -->
    <div id="doleLoadingScreen">

        <div id="doleLoadingContent">

            <!-- LOGO -->
            <div id="doleLogoBox">
                <img id="doleLoadingLogo" src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE MIMAROPA Logo">
            </div>

            <!-- TITLE -->
            <div id="doleLoadingTitle">
                DOLE MIMAROPA
            </div>

            <div id="doleLoadingSubtitle">
                E-PORTAL
            </div>

        </div>

    </div>




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
                <span class="stat-number" data-target="<?= $new_application ?>">0</span>
                <span class="stat-label">New Request</span>
                <span class="stat-trend trend-up"><i class="bi bi-calendar-check me-1"></i> <?= $new_today ?> received
                    today</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-gear"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $processing ?>">0</span>
                <span class="stat-label">Processing</span>
                <span class="stat-trend trend-up"><i class="bi bi-arrow-repeat me-1"></i><?= $processing_today ?>
                    updated today</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-box-arrow-up-right"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $for_released ?>">0</span>
                <span class="stat-label">For Released</span>
                <span class="stat-trend trend-up"><i class="bi bi-box-seam me-1"></i><?= $released_today ?> ready today
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-check-circle"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $completed ?>">0</span>
                <span class="stat-label">Completed</span>
                <span class="stat-trend trend-up"><i class="bi bi-calendar-month me-1"></i><?= $completed_month ?>
                    completed this month</span>
            </div>
        </div>



        <div class="stat-card">
            <div class="stat-icon stat-icon-red"><i class="bi bi-x-circle"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $rejected ?>">0</span>
                <span class="stat-label">Rejected</span>
                <span class="stat-trend trend-down"><i class="bi bi-calendar-x me-1"></i><?= $rejected_month ?> rejected
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
                        $allowedStatus = [
                            'New Application',
                            'Processing',
                            'For Released'
                        ];

                        $statusClass = [
                            'New Application' => 'badge-new-application',
                            'Processing' => 'badge-processing',
                            'For Released' => 'badge-for-released',
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

                                        } elseif ($row['status'] == 'For Released' && $row['date_released']) {

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
                    <button class="quick-action-btn" type="button" data-bs-toggle="modal"
                        data-bs-target="#serviceDetailsModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>New Request</span>
                    </button>
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

            <div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <form id="serviceRequestForm" method="post" action="<?= site_url('request/save') ?>"
                        class="modal-content">

                        <?= csrf_field() ?>

                        <div class="modal-header">
                            <h5 class="modal-title">HR Service Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="serviceAvailed" class="form-label">
                                    Service Availed <span class="text-danger">*</span>
                                </label>

                                <select id="serviceAvailed" name="service_availed" class="form-select" required>
                                    <option value="" selected disabled>Select a service</option>
                                    <option value="Service Record">Service Record</option>
                                    <option value="Certificate with Compensation">Certificate with Compensation</option>
                                    <option value="Loan Assistance">Loan Assistance</option>
                                    <option value="Inquiry of Leave Balance">Inquiry of Leave Balance</option>
                                    <option value="Certificate of Employment">Certificate of Employment</option>
                                    <option value="Certification">Certification</option>
                                    <option value="IPCR">IPCR</option>
                                    <option value="201 Docs">201 Docs</option>
                                </select>
                            </div>

                            <!-- Shown for every selected service -->
                            <div id="basicFields" class="row g-3 d-none">
                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="fullname" class="form-control" placeholder="Full name"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="contact_number" class="form-control"
                                        placeholder="Contact number" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email_address" class="form-control"
                                        placeholder="Email address" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Date Requested <span class="text-danger">*</span></label>
                                    <input type="date" name="date_requested" class="form-control"
                                        value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <!-- Certificate of Employment -->
                            <div id="coeFields" class="row g-3 mt-2 d-none">
                                <div class="col-md-6">
                                    <label class="form-label">Employee Category <span
                                            class="text-danger">*</span></label>
                                    <select id="employeeCategory" name="employee_category" class="form-select">
                                        <option value="">Select category</option>
                                        <option value="COS Employee">COS Employee</option>
                                        <option value="Permanent Employee">Permanent Employee</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Position <span class="text-danger">*</span></label>
                                    <input type="text" id="position" name="position" class="form-control">
                                </div>
                            </div>

                            <!-- COS Employee only -->
                            <div id="cosFields" class="row g-3 mt-2 d-none">
                                <div class="col-md-6">
                                    <label class="form-label">Period of Service <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="periodOfService" name="period_of_service"
                                        class="form-control" placeholder="Example: January 2024 to December 2024">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Office / Division Assigned <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="officeDivision" name="office_division" class="form-control">
                                </div>
                            </div>

                            <!-- Certification -->
                            <div id="certificationFields" class="mt-4 d-none">
                                <label class="form-label d-block">
                                    Certification Needed <span class="text-danger">*</span>
                                </label>

                                <div class="form-check">
                                    <input class="form-check-input certification-option" type="checkbox"
                                        name="certification[]" value="No pending case" id="noPendingCase">
                                    <label class="form-check-label" for="noPendingCase">No pending case</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input certification-option" type="checkbox"
                                        name="certification[]" value="Last Salary Received" id="lastSalary">
                                    <label class="form-check-label" for="lastSalary">Last Salary Received</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input certification-option" type="checkbox"
                                        name="certification[]" value="Leave Credits" id="leaveCredits">
                                    <label class="form-check-label" for="leaveCredits">Leave Credits</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input certification-option" type="checkbox"
                                        name="certification[]" value="Leave without pay" id="leaveWithoutPay">
                                    <label class="form-check-label" for="leaveWithoutPay">Leave without pay</label>
                                </div>
                            </div>

                            <!-- IPCR -->
                            <div id="ipcrFields" class="mt-3 d-none">
                                <label class="form-label">Please Indicate Period <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="ipcrPeriod" name="ipcr_period" class="form-control"
                                    placeholder="Example: January to June 2025">
                            </div>

                            <!-- 201 Documents -->
                            <div id="docs201Fields" class="mt-3 d-none">
                                <label class="form-label">Please Specify <span class="text-danger">*</span></label>
                                <textarea id="specify201" name="specify_201" class="form-control" rows="3"
                                    placeholder="Specify the 201 document needed"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</main>
</div>

<script>
    const serviceAvailed = document.getElementById('serviceAvailed');
    const employeeCategory = document.getElementById('employeeCategory');
    const serviceRequestForm = document.getElementById('serviceRequestForm');

    const basicFields = document.getElementById('basicFields');
    const coeFields = document.getElementById('coeFields');
    const cosFields = document.getElementById('cosFields');
    const certificationFields = document.getElementById('certificationFields');
    const ipcrFields = document.getElementById('ipcrFields');
    const docs201Fields = document.getElementById('docs201Fields');

    const position = document.getElementById('position');
    const periodOfService = document.getElementById('periodOfService');
    const officeDivision = document.getElementById('officeDivision');
    const ipcrPeriod = document.getElementById('ipcrPeriod');
    const specify201 = document.getElementById('specify201');

    function toggleSection(element, show) {
        element.classList.toggle('d-none', !show);

        element.querySelectorAll('input, select, textarea').forEach(field => {
            field.disabled = !show;
        });
    }

    function updateServiceFields() {
        const service = serviceAvailed.value;
        const isCOE = service === 'Certificate of Employment';
        const isCOS = isCOE && employeeCategory.value === 'COS Employee';
        const isCertification = service === 'Certification';
        const isIPCR = service === 'IPCR';
        const is201Docs = service === '201 Docs';

        toggleSection(basicFields, service !== '');
        toggleSection(coeFields, isCOE);
        toggleSection(cosFields, isCOS);
        toggleSection(certificationFields, isCertification);
        toggleSection(ipcrFields, isIPCR);
        toggleSection(docs201Fields, is201Docs);

        employeeCategory.required = isCOE;
        position.required = isCOE;
        periodOfService.required = isCOS;
        officeDivision.required = isCOS;
        ipcrPeriod.required = isIPCR;
        specify201.required = is201Docs;

        if (!isCOE) {
            employeeCategory.value = '';
        }
    }

    serviceAvailed.addEventListener('change', updateServiceFields);
    employeeCategory.addEventListener('change', updateServiceFields);

    serviceRequestForm.addEventListener('submit', function (event) {
        if (serviceAvailed.value === 'Certification') {
            const selected = document.querySelectorAll('.certification-option:checked');

            if (selected.length === 0) {
                event.preventDefault();
                alert('Please select at least one certification requirement.');
            }
        }
    });

    updateServiceFields();
</script>

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
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const loadingScreen =
            document.getElementById('doleLoadingScreen');

        if (!loadingScreen) return;

        const sidebarLinks =
            document.querySelectorAll('.nav-link');


        sidebarLinks.forEach(function (link) {

            link.addEventListener('click', function (event) {

                const href = this.getAttribute('href');

                if (
                    !href ||
                    href === '#' ||
                    href === 'javascript:void(0)'
                ) {
                    return;
                }


                /* Allow Ctrl / CMD / Shift / Middle Click */

                if (
                    event.ctrlKey ||
                    event.metaKey ||
                    event.shiftKey ||
                    event.button === 1
                ) {
                    return;
                }


                /* Stop normal navigation temporarily */

                event.preventDefault();


                /* Show loading screen */

                loadingScreen.classList.add('show');

                document.body.style.overflow = 'hidden';


                /*
                 * Navigate immediately.
                 * The loading screen will remain visible
                 * while the next page is loading.
                 */

                window.location.href = href;

            });

        });


        /* Back / Forward */

        window.addEventListener('pageshow', function () {

            loadingScreen.classList.remove('show');

            document.body.style.overflow = '';

        });

    });
</script>