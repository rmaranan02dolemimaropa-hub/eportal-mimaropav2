<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>


<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Requests</h1>
            <p class="page-subtitle">
                Welcome back, <?= session('fullname') ?> — manage and monitor all HR service requests from this
                dashboard.
            </p>
        </div>

        <div class="page-date">
            <i class="bi bi-calendar3"></i>
            <span id="currentDate"></span>
        </div>
    </div>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <?= esc(session()->getFlashdata('success')) ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= esc(session()->getFlashdata('error')) ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>





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

                <button class="btn tool-btn" type="button" id="exportButton">
                    <i class="bi bi-download"></i> Export PDF
                </button>

                <div id="exportOverlay" class="export-overlay" aria-live="polite">
                    <div class="export-card">
                        <h3>Preparing PDF</h3>
                        <p>Please wait while your report is being generated.</p>

                        <div class="progress-track">
                            <div class="progress-bar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="status-filter mb-4">

            <?php $status = $_GET['status'] ?? ''; ?>

            <a href="<?= current_url() ?>" class="status-pill <?= $status == '' ? 'active' : '' ?>">
                All
            </a>

            <a href="<?= current_url() ?>?status=New+Application"
                class="status-pill <?= $status == 'New Application' ? 'active' : '' ?>">
                New Application
            </a>

            <a href="<?= current_url() ?>?status=Processing"
                class="status-pill <?= $status == 'Processing' ? 'active' : '' ?>">
                Processing
            </a>

            <a href="<?= current_url() ?>?status=For+Released"
                class="status-pill <?= $status == 'For Released' ? 'active' : '' ?>">
                For Released
            </a>

            <a href="<?= current_url() ?>?status=Completed"
                class="status-pill <?= $status == 'Completed' ? 'active' : '' ?>">
                Completed
            </a>

            <a href="<?= current_url() ?>?status=Rejected"
                class="status-pill <?= $status == 'Rejected' ? 'active' : '' ?>">
                Rejected
            </a>

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
                                        'New Application' => 'badge-new-application',
                                        'Processing' => 'badge-processing',
                                        'Completed' => 'badge-completed',
                                        'For Released' => 'badge-for-released',
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
                                            data-bs-target="#viewRequestModal"
                                            data-reference="<?= esc($row['reference_no'] ?? '', 'attr') ?>"
                                            data-service="<?= esc($row['service_availed'] ?? '', 'attr') ?>"
                                            data-fullname="<?= esc($row['fullname'] ?? '', 'attr') ?>"
                                            data-contact="<?= esc($row['contact_number'] ?? '', 'attr') ?>"
                                            data-email="<?= esc($row['email_address'] ?? '', 'attr') ?>"
                                            data-date-requested="<?= esc($row['date_requested'] ?? '', 'attr') ?>"
                                            data-status="<?= esc($row['status'] ?? '', 'attr') ?>"
                                            data-category="<?= esc($row['employee_category'] ?? '', 'attr') ?>"
                                            data-period="<?= esc($row['period_of_service'] ?? '', 'attr') ?>"
                                            data-position="<?= esc($row['position'] ?? '', 'attr') ?>"
                                            data-office="<?= esc($row['office_division'] ?? '', 'attr') ?>"
                                            data-certification="<?= esc($row['certification'] ?? '', 'attr') ?>"
                                            data-ipcr-period="<?= esc($row['ipcr_period'] ?? '', 'attr') ?>"
                                            data-specify-201="<?= esc($row['specify_201'] ?? '', 'attr') ?>"
                                            data-remarks="<?= esc($row['remarks'] ?? '', 'attr') ?>">
                                            <i class="bi bi-eye"></i>

                                        </button>

                                        <?php if (session('role') == 'IT ADMINISTRATOR'): ?>

                                            <!-- Edit -->
                                            <button class="row-action-btn" title="Edit" data-bs-toggle="modal"
                                                data-bs-target="#edit<?= $row['id'] ?>">

                                                <i class="bi bi-pencil"></i>

                                            </button>


                                            <!-- Delete -->
                                            <button class="row-action-btn row-action-danger" title="Delete" data-bs-toggle="modal"
                                                data-bs-target="#delete<?= $row['id'] ?>">

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



    <div class="modal fade" id="viewRequestModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">HR Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="requestDetails" class="row g-3"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php foreach ($requests as $row): ?>
        <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form method="post" action="<?= site_url('request/update/' . $row['id']) ?>" class="modal-content">

                    <?= csrf_field() ?>

                    <div class="modal-header">
                        <h5 class="modal-title">Update HR Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Reference No.</label>
                                <input type="text" class="form-control" value="<?= esc($row['reference_no']) ?>" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="New Application" <?= $row['status'] === 'New Application' ? 'selected' : '' ?>>
                                        New Application
                                    </option>
                                    <option value="Processing" <?= $row['status'] === 'Processing' ? 'selected' : '' ?>>
                                        Processing
                                    </option>
                                    <option value="For Released" <?= $row['status'] === 'For Released' ? 'selected' : '' ?>>
                                        For Released
                                    </option>
                                    <option value="Completed" <?= $row['status'] === 'Completed' ? 'selected' : '' ?>>
                                        Completed
                                    </option>
                                    <option value="Rejected" <?= $row['status'] === 'Rejected' ? 'selected' : '' ?>>
                                        Rejected
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="fullname" class="form-control"
                                    value="<?= esc($row['fullname']) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control"
                                    value="<?= esc($row['contact_number']) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email_address" class="form-control"
                                    value="<?= esc($row['email_address']) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date Requested</label>
                                <input type="date" name="date_requested" class="form-control"
                                    value="<?= esc($row['date_requested']) ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control"
                                    rows="3"><?= esc($row['remarks'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($requests as $row): ?>
        <div class="modal fade" id="delete<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <form method="post" action="<?= site_url('request/delete/' . $row['id']) ?>" class="modal-content">

                    <?= csrf_field() ?>

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Delete HR Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        Are you sure you want to delete the request for
                        <strong><?= esc($row['fullname']) ?></strong>?

                        <p class="text-danger mt-2 mb-0">
                            This action cannot be undone.
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>





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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById('exportButton');
        const overlay = document.getElementById('exportOverlay');

        button.addEventListener('click', async function () {
            button.disabled = true;
            overlay.classList.add('is-visible');

            try {
                // Forward whatever filters are currently in the URL
                const currentParams = window.location.search; // e.g. "?year=2026&status=Pending"

                const response = await fetch(
                    "<?= site_url('export/hr-request') ?>" + currentParams,
                    { credentials: 'same-origin' }
                );

                if (!response.ok) {
                    throw new Error('Export failed.');
                }

                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');

                link.href = url;
                link.download = 'HR_Request_Report.pdf';
                link.click();

                setTimeout(() => URL.revokeObjectURL(url), 1000);
            } catch (error) {
                alert('Unable to export the PDF. Please try again.');
                console.error(error);
            } finally {
                overlay.classList.remove('is-visible');
                button.disabled = false;
            }
        });
    });
</script>

<script>
    const viewRequestModal = document.getElementById('viewRequestModal');
    const requestDetails = document.getElementById('requestDetails');

    function addDetail(label, value) {
        const column = document.createElement('div');
        column.className = 'col-md-6 mb-3';

        const labelElement = document.createElement('small');
        labelElement.className = 'text-muted d-block';
        labelElement.textContent = label;

        const valueElement = document.createElement('div');
        valueElement.className = 'fw-semibold';
        valueElement.textContent = value || '-';

        column.appendChild(labelElement);
        column.appendChild(valueElement);
        requestDetails.appendChild(column);
    }

    viewRequestModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const data = button.dataset;

        requestDetails.innerHTML = '';

        addDetail('Reference No.', data.reference);
        addDetail('Service Availed', data.service);
        addDetail('Name', data.fullname);
        addDetail('Contact Number', data.contact);
        addDetail('Email Address', data.email);
        addDetail('Date Requested', data.dateRequested);
        addDetail('Status', data.status);

        if (data.service === 'Certificate of Employment') {
            addDetail('Employee Category', data.category);
            addDetail('Position', data.position);

            if (data.category === 'COS Employee') {
                addDetail('Period of Service', data.period);
                addDetail('Office / Division Assigned', data.office);
            }
        }

        if (data.service === 'Certification') {
            addDetail('Certification Needed', data.certification);
        }

        if (data.service === 'IPCR') {
            addDetail('IPCR Period', data.ipcrPeriod);
        }

        if (data.service === '201 Docs') {
            addDetail('Specified Document', data.specify201);
        }

        addDetail('Remarks', data.remarks);
    });
</script>

<script>
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        });
    }, 4000);
</script>


<?= $this->include('dashboard_layout/footer') ?>