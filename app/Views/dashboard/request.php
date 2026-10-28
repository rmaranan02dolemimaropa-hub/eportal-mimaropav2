<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<?php
$requests = $requests ?? [];
$pager = $pager ?? null;
?>




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
                    <input type="text" id="searchRequest" class="form-control" placeholder="Search requests..."
                        value="<?= esc($_GET['search'] ?? '') ?>">
                </div>

                <button class="btn tool-btn" id="exportButton">
                    <i class="bi bi-download"></i> Export PDF
                </button>

                <div id="exportOverlay" class="export-overlay">
                    <div class="export-card">
                        <h3>Preparing PDF Report</h3>
                        <p id="exportText">Initializing...</p>

                        <div class="progress-track">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>

                        <div class="progress-percent">
                            <span id="progressValue">0%</span>
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
                New Request
            </a>

            <a href="<?= current_url() ?>?status=Processing"
                class="status-pill <?= $status == 'Processing' ? 'active' : '' ?>">
                Processing
            </a>

            <a href="<?= current_url() ?>?status=For+Releasing"
                class="status-pill <?= $status == 'For Releasing' ? 'active' : '' ?>">
                For Releasing
            </a>

            <a href="<?= current_url() ?>?status=Completed"
                class="status-pill <?= $status == 'Completed' ? 'active' : '' ?>">
                Completed
            </a>

            <a href="<?= current_url() ?>?status=Rejected"
                class="status-pill <?= $status == 'Rejected' ? 'active' : '' ?>">
                Rejected
            </a>

            <a href="<?= current_url() ?>?status=Canceled"
                class="status-pill <?= $status == 'Canceled' ? 'active' : '' ?>">
                Canceled
            </a>

        </div>



        <div class="table-responsive">
            <table class="table requests-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Reference No.</th>
                        <th>Requestor</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Feedback</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (!empty($requests)): ?>

                        <?php foreach ($requests as $row): ?>

                            <tr>

                                <td class="ref-no" data-label="Reference No.">
                                    <?= esc($row['reference_no']) ?>
                                </td>

                                <td data-label="Requestor">
                                    <?= esc($row['fullname']) ?>
                                </td>

                                <td data-label="Service">
                                    <?= esc($row['service_availed']) ?>
                                </td>

                                <td data-label="Date">
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
                                    } elseif ($row['status'] == 'Canceled' && $row['date_canceled']) {
                                        $displayDate = $row['date_canceled'];
                                    }
                                    ?>
                                    <?= date('F d, Y', strtotime($displayDate)) ?>
                                </td>

                                <td data-label="Status">
                                    <?php
                                    $statusClass = [
                                        'New Application' => 'badge-new-application',
                                        'Processing' => 'badge-processing',
                                        'Completed' => 'badge-completed',
                                        'For Releasing' => 'badge-for-released',
                                        'Rejected' => 'badge-rejected',
                                        'Canceled' => 'badge-canceled',
                                    ];
                                    ?>
                                    <span class="badge <?= $statusClass[$row['status']] ?? 'bg-secondary' ?>">
                                        <?= esc($row['status']) ?>
                                    </span>
                                </td>

                                <td data-label="Feedback">
                                    <?php if (!empty($row['feedback_id'])): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill"></i> Submitted
                                        </span>
                                    <?php elseif ($row['status'] == 'For Releasing'): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split"></i> Waiting for Client
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-dash-circle"></i> Not Yet Available
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end" data-label="Action">
                                    <div class="row-actions">

                                        <!-- View (Everyone) -->
                                        <button class="row-action-btn" title="View" data-bs-toggle="modal"
                                            data-bs-target="#viewRequestModal" data-id="<?= $row['id'] ?>"
                                            data-reference="<?= esc($row['reference_no'], 'attr') ?>"
                                            data-service="<?= esc($row['service_availed'], 'attr') ?>"
                                            data-service-purpose="<?= esc($row['service_purpose'] ?? '', 'attr') ?>"
                                            data-fullname="<?= esc($row['fullname'], 'attr') ?>"
                                            data-contact="<?= esc($row['contact_number'], 'attr') ?>"
                                            data-email="<?= esc($row['email_address'], 'attr') ?>"
                                            data-date-requested="<?= esc($row['date_requested'], 'attr') ?>"
                                            data-status="<?= esc($row['status'], 'attr') ?>"
                                            data-category="<?= esc($row['employee_category'] ?? '', 'attr') ?>"
                                            data-history="<?= htmlspecialchars(json_encode($row['employment_history'] ?? []), ENT_QUOTES, 'UTF-8') ?>"
                                            data-certification="<?= esc($row['certification'] ?? '', 'attr') ?>"
                                            data-ipcr-period="<?= esc($row['ipcr_period'] ?? '', 'attr') ?>"
                                            data-payslip-month="<?= esc($row['payslip_month'] ?? '', 'attr') ?>"
                                            data-payslip-year="<?= esc($row['payslip_year'] ?? '', 'attr') ?>"
                                            data-performance-evaluation-period="<?= esc($row['performance_evaluation_period'] ?? '', 'attr') ?>"
                                            data-documents-list="<?= esc($row['documents_201'] ?? '', 'attr') ?>"
                                            data-remarks="<?= esc($row['remarks'] ?? '', 'attr') ?>"
                                            data-cancellation-reason="<?= esc($row['cancellation_reason'] ?? '', 'attr') ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <?php if (in_array($row['status'], ['Processing', 'For Releasing'])): ?>

                                            <button class="row-action-btn" title="Edit" data-bs-toggle="modal"
                                                data-bs-target="#edit<?= $row['id'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <button class="row-action-btn row-action-warning" title="Cancel" data-bs-toggle="modal"
                                                data-bs-target="#cancel<?= $row['id'] ?>">
                                                <i class="bi bi-x-circle"></i>
                                            </button>

                                        <?php endif; ?>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr class="empty-row">
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <br>
                                No service requests found.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>
        </div>
        <?php if ($pager): ?>
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
        <?php else: ?>
            <div class="table-pagination">
                <span class="pagination-summary">
                    Showing <?= count($requests) ?> requests
                </span>
            </div>
        <?php endif; ?>

    </section>



    <div class="modal fade" id="viewRequestModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content request-modal">

                <div class="modal-header request-modal-header d-flex justify-content-between align-items-start">
                    <div>
                        <div class="request-modal-eyebrow">HR Request Details</div>
                        <h5 class="modal-title request-modal-ref d-flex align-items-center gap-2">
                            <span id="modalReferenceNo">—</span>
                            <span class="ref-status-divider">|</span>
                            <span class="modal-status-pill" id="modalStatusPill">—</span>
                        </h5>
                    </div>
                </div>

                <div class="modal-body request-modal-body">
                    <div id="requestDetails"><!-- populated by JS --></div>
                </div>

                <div class="modal-footer request-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="button" id="startProcessingBtn" class="btn btn-dole-primary d-none">
                        <i class="bi bi-play-circle me-1"></i>
                        Start Processing
                    </button>
                </div>

            </div>
        </div>
    </div>

    <?php foreach ($requests as $row): ?>
        <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form method="post" action="<?= site_url('request/update/' . $row['id']) ?>"
                    class="modal-content request-modal updateRequestForm">

                    <?= csrf_field() ?>

                    <div class="modal-header request-modal-header d-flex justify-content-between align-items-start">
                        <div>
                            <div class="request-modal-eyebrow">Update HR Request</div>
                            <h5 class="modal-title request-modal-ref">
                                <?= esc($row['reference_no']) ?>
                            </h5>
                        </div>
                    </div>

                    <div class="modal-body request-modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Reference No.</label>
                                <input class="form-control" value="<?= esc($row['reference_no']) ?>" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select requestStatus">

                                    <?php if ($row['status'] == 'Processing'): ?>

                                        <option value="Processing" selected>Processing</option>
                                        <option value="For Releasing">For Releasing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Rejected">Rejected</option>

                                    <?php elseif ($row['status'] == 'For Releasing'): ?>

                                        <option value="For Releasing" selected>For Releasing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Rejected">Rejected</option>

                                    <?php endif; ?>

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

                    <div class="modal-footer request-modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-dole-primary">
                            <i class="bi bi-check-circle"></i> Update Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($requests as $row): ?>
        <div class="modal fade" id="cancel<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <form method="post" action="<?= site_url('request/cancel/' . $row['id']) ?>"
                    class="modal-content request-modal cancelRequestForm">

                    <?= csrf_field() ?>

                    <div class="modal-header request-modal-header-danger">
                        <h5 class="modal-title text-white">
                            <i class="bi bi-x-circle me-1"></i> Cancel HR Request
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body request-modal-body">
                        Are you sure you want to cancel the request for
                        <strong><?= esc($row['fullname']) ?></strong>?

                        <div class="col-md-12 mt-3">
                            <label class="form-label">Cancellation Reason (Optional)</label>
                            <textarea name="cancellation_reason" class="form-control" rows="2"></textarea>
                        </div>

                        <p class="text-muted mt-2 mb-0">
                            The request will be marked as canceled.
                        </p>
                    </div>

                    <div class="modal-footer request-modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Back
                        </button>

                        <button type="submit" class="btn btn-dole-danger">
                            <i class="bi bi-x-circle"></i> Cancel Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <div id="processingOverlay" class="processing-overlay d-none">
        <div class="processing-card">
            <div class="spinner-border text-primary mb-3" role="status"></div>

            <h5>Starting Processing...</h5>

            <p id="processingText">
                Updating request and sending email...
            </p>
        </div>
    </div>
    <div id="updateOverlay" class="processing-overlay d-none">
        <div class="processing-card">
            <div class="spinner-border text-primary mb-3" role="status"></div>

            <h5>Updating Request...</h5>

            <p id="updateText">
                Please wait while we update the request status and send the email notification.
            </p>
        </div>
    </div>
    <div id="cancelOverlay" class="processing-overlay d-none">
        <div class="processing-card">
            <div class="spinner-border text-danger mb-3" role="status"></div>
            <h5>Canceling Request...</h5>
            <p>Please wait while we cancel the request and send the email notification.</p>
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

        const searchInput = document.getElementById("searchRequest");

        if (!searchInput) return;


        let timer;


        searchInput.addEventListener("input", function () {


            clearTimeout(timer);


            timer = setTimeout(() => {


                const keyword = searchInput.value.trim();


                const url = new URL(window.location.href);


                if (keyword) {

                    url.searchParams.set(
                        "search",
                        keyword
                    );

                } else {

                    url.searchParams.delete("search");

                }


                window.location.href = url.toString();


            }, 500);


        });

    }

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

    document.addEventListener("DOMContentLoaded", function () {

        const button = document.getElementById("exportButton");
        const overlay = document.getElementById("exportOverlay");
        const bar = document.getElementById("progressBar");
        const percent = document.getElementById("progressValue");
        const text = document.getElementById("exportText");

        button.addEventListener("click", async function () {

            button.disabled = true;
            overlay.classList.add("is-visible");

            let progress = 0;

            bar.style.width = "0%";
            percent.textContent = "0%";
            text.textContent = "Preparing report...";

            const timer = setInterval(() => {

                if (progress < 90) {

                    progress += Math.floor(Math.random() * 8) + 3;

                    if (progress > 90) progress = 90;

                    bar.style.width = progress + "%";
                    percent.textContent = progress + "%";

                    if (progress > 25)
                        text.textContent = "Collecting records...";

                    if (progress > 55)
                        text.textContent = "Generating PDF...";

                    if (progress > 80)
                        text.textContent = "Almost finished...";
                }

            }, 180);

            try {

                // Get current filter parameters
                const currentParams = window.location.search;

                const response = await fetch(
                    "<?= site_url('export/hr-request') ?>" + currentParams, {
                    credentials: "same-origin"
                }
                );

                if (!response.ok) {
                    throw new Error("Export failed.");
                }

                const blob = await response.blob();

                clearInterval(timer);

                progress = 100;
                bar.style.width = "100%";
                percent.textContent = "100%";
                text.textContent = "Download ready.";

                const url = URL.createObjectURL(blob);

                const link = document.createElement("a");
                link.href = url;
                link.download = "HR_Request_Report.pdf";
                link.click();

                setTimeout(() => {
                    URL.revokeObjectURL(url);

                    overlay.classList.remove("is-visible");
                    button.disabled = false;

                    bar.style.width = "0%";
                    percent.textContent = "0%";
                    text.textContent = "Preparing report...";
                }, 700);

            } catch (error) {

                clearInterval(timer);

                overlay.classList.remove("is-visible");
                button.disabled = false;

                bar.style.width = "0%";
                percent.textContent = "0%";

                alert("Unable to export the PDF. Please try again.");
                console.error(error);
            }

        });

    });



    const viewRequestModal = document.getElementById('viewRequestModal');
    const requestDetails = document.getElementById('requestDetails');

    function detailItem(label, value) {
        return `
        <div class="detail-item">
            <dt>${escapeHTML(label)}</dt>
            <dd>${escapeHTML(value) || '—'}</dd>
        </div>
    `;
    }

    function buildEmploymentHistoryHTML(histories) {
        if (!Array.isArray(histories) || histories.length === 0) {
            return detailItem('Employment History', 'No employment records found.');
        }

        const rows = histories.map(function (history, index) {
            return `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHTML(history.period_of_service)}</td>
                <td>${escapeHTML(history.position)}</td>
                <td>${escapeHTML(history.office_division)}</td>
            </tr>
        `;
        }).join('');

        return `
        <div class="detail-item" style="grid-column: 1 / -1;">
    <dt>Employment History</dt>
    <dd>
        ${rows && rows.trim() ? `
        <div class="summary-table-wrapper">
            <table class="summary-table mt-2" aria-label="Employment history">
                <caption class="visually-hidden">
                    List of past positions held, including period of service, position title, and office or division.
                </caption>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Period of Service</th>
                        <th scope="col">Position</th>
                        <th scope="col">Office / Division</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
        ` : `
        <div class="summary-table-empty">
            <i class="bi bi-clock-history me-1"></i>
            No employment history on record.
        </div>
        `}
    </dd>
</div>
    `;
    }

    function escapeHTML(str) {
        if (str === undefined || str === null) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    viewRequestModal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;
        const data = button.dataset;

        /* =========================================================
           HEADER + STATUS PILL
        ========================================================= */
        document.getElementById('modalReferenceNo').textContent = data.reference || '—';

        const pill = document.getElementById('modalStatusPill');
        pill.textContent = data.status || '—';
        pill.setAttribute('data-status', data.status || '');


        /* =========================================================
           SERVICE-SPECIFIC FIELDS
        ========================================================= */
        let serviceFieldsHTML = '';

        serviceFieldsHTML += detailItem('Purpose / Request Details', data.servicePurpose);

        // Certificate of Employment
        if (data.service === 'Certificate of Employment') {
            const category = (data.category || '').trim().toLowerCase();

            serviceFieldsHTML += detailItem('Employee Category', data.category);

            if (category === 'cos' || category === 'cos employee') {
                let histories = [];
                try {
                    histories = JSON.parse(data.history || '[]');
                } catch (error) {
                    console.error('Employment history error:', error);
                }
                serviceFieldsHTML += buildEmploymentHistoryHTML(histories);
            } else {
                serviceFieldsHTML += detailItem('Position', data.position);
            }
        }

        // Certification
        if (data.service === 'Certification') {
            const certificationList = data.certification || '';

            if (certificationList && certificationList.trim() !== '') {
                const certifications = certificationList
                    .split(',')
                    .map(item => item.trim())
                    .filter(item => item.length > 0);

                if (certifications.length > 0) {
                    let certificationHTML = '<div class="documents-list">';

                    certifications.forEach(cert => {
                        certificationHTML += `<div class="document-item">${escapeHTML(cert)}</div>`;
                    });

                    certificationHTML += '</div>';

                    serviceFieldsHTML += `
                <div class="detail-item">
                    <dt>Certification Needed</dt>
                    <dd>${certificationHTML}</dd>
                </div>
            `;
                } else {
                    serviceFieldsHTML += detailItem('Certification Needed', 'No certification specified');
                }
            } else {
                serviceFieldsHTML += detailItem('Certification Needed', 'No certification specified');
            }
        }

        // IPCR
        if (data.service === 'IPCR') {
            const ipcrPeriod = data.ipcrPeriod || '';
            serviceFieldsHTML += detailItem('IPCR Period', ipcrPeriod ? escapeHTML(ipcrPeriod) : 'No period specified');
        }
        // Performance Evaluation
        if (data.service === 'Performance Evaluation') {
            serviceFieldsHTML += detailItem('Performance Evaluation Period', data.performanceEvaluationPeriod);
        }
        // Payslip
        if (data.service === 'Payslip') {
            const month = data.payslipMonth || '';
            const year = data.payslipYear || '';

            if (month && year) {
                const monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                const monthName = monthNames[parseInt(month)] || '';
                const payslipPeriod = monthName + ' ' + year;
                serviceFieldsHTML += detailItem('Payslip Period', payslipPeriod);
            } else {
                serviceFieldsHTML += detailItem('Payslip Period', 'No period specified');
            }
        }




        // 201 Documents - Updated JavaScript Handler
        // Add this to your modal event listener

        if (data.service === '201 Documents') {
            const documentsList = data.documentsList || data.documents201 || '';

            if (documentsList && documentsList.trim() !== '') {
                // Split documents by comma and create list items
                const documents = documentsList
                    .split(',')
                    .map(doc => doc.trim())
                    .filter(doc => doc.length > 0);

                if (documents.length > 0) {
                    let documentsHTML = '<div class="documents-list">';

                    documents.forEach(doc => {
                        documentsHTML += `<div class="document-item">${escapeHTML(doc)}</div>`;
                    });

                    documentsHTML += '</div>';

                    serviceFieldsHTML += `
                <div class="detail-item">
                    <dt>Required Documents</dt>
                    <dd>${documentsHTML}</dd>
                </div>
            `;
                } else {
                    serviceFieldsHTML += detailItem('Required Documents', 'No documents specified');
                }
            } else {
                serviceFieldsHTML += detailItem('Required Documents', 'No documents specified');
            }
        }

        /* =========================================================
   CANCELLATION DETAILS (only if status is Canceled)
========================================================= */
        let cancellationHTML = '';
        if (data.status === 'Canceled') {
            cancellationHTML = `
        <div class="detail-section">
            <div class="detail-section-title"><i class="bi bi-x-circle"></i> Cancellation Details</div>
            <div class="detail-grid">
                ${detailItem('Cancellation Reason', data.cancellationReason)}
            </div>
        </div>
    `;
        }

        /* =========================================================
           REMARKS (hidden when status is Canceled)
        ========================================================= */
        let remarksHTML = '';
        if (data.status !== 'Canceled') {
            remarksHTML = `
        <div class="detail-section">
            <div class="detail-section-title"><i class="bi bi-chat-left-text"></i> Remarks</div>
            <div class="detail-grid">
                ${detailItem('Remarks', data.remarks)}
            </div>
        </div>
    `;
        }

        /* =========================================================
           RENDER
        ========================================================= */
        requestDetails.innerHTML = `
    <div class="detail-section">
        <div class="detail-section-title"><i class="bi bi-person-vcard"></i> Requester Information</div>
        <div class="detail-grid">
            ${detailItem('Name', data.fullname)}
            ${detailItem('Contact Number', data.contact)}
            ${detailItem('Email Address', data.email)}
            ${detailItem('Date Requested', data.dateRequested)}
        </div>
    </div>

    <div class="detail-section">
        <div class="detail-section-title"><i class="bi bi-clipboard2-check"></i> Service Requested</div>
        <div class="entry-card">
            <div class="entry-card-header">
                <span class="entry-card-service">${escapeHTML(data.service) || '—'}</span>
            </div>
            <div class="detail-grid">
                ${serviceFieldsHTML}
            </div>
        </div>
    </div>

    ${remarksHTML}

    ${cancellationHTML}
`;




        /* =========================================================
           START PROCESSING BUTTON
        ========================================================= */
        const startBtn = document.getElementById('startProcessingBtn');
        if (data.status === 'New Application') {
            startBtn.classList.remove('d-none');
            startBtn.dataset.id = data.id;
        } else {
            startBtn.classList.add('d-none');
        }
    });


    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        });
    }, 4000);



    const overlay = document.getElementById("processingOverlay");
    const processingText = document.getElementById("processingText");

    document.getElementById("startProcessingBtn")
        .addEventListener("click", function () {

            const id = this.dataset.id;

            // ✅ REMOVED: if (!confirm("Start processing this request?")) {
            //                return;
            //            }

            overlay.classList.remove("d-none");

            processingText.innerHTML =
                "Updating request...<br>Please wait.";

            fetch("<?= site_url('request/start-processing') ?>", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "<?= csrf_header() ?>": "<?= csrf_hash() ?>"
                },
                body: JSON.stringify({ id: id })
            })
                .then(res => res.json())
                .then(result => {

                    if (result.success) {

                        processingText.innerHTML =
                            "Email sent successfully.<br>Refreshing page...";

                        setTimeout(() => {
                            location.reload();
                        }, 800);

                    } else {

                        overlay.classList.add("d-none");
                        alert(result.message);

                    }

                })
                .catch(err => {

                    overlay.classList.add("d-none");
                    alert("Something went wrong.");

                    console.error(err);

                });

        });

    const updateOverlay = document.getElementById("updateOverlay");
    const updateText = document.getElementById("updateText");

    document.querySelectorAll(".updateRequestForm").forEach(form => {

        form.addEventListener("submit", function () {

            const status = form.querySelector(".requestStatus").value;

            switch (status) {

                case "Processing":
                    updateText.innerHTML =
                        "Updating request...<br><small>Sending processing notification...</small>";
                    break;

                case "For Releasing":
                    updateText.innerHTML =
                        "Updating request...<br><small>Sending release notification...</small>";
                    break;

                case "Completed":
                    updateText.innerHTML =
                        "Completing request...<br><small>Sending completion email and feedback link...</small>";
                    break;

                case "Rejected":
                    updateText.innerHTML =
                        "Rejecting request...<br><small>Sending rejection email...</small>";
                    break;

                case "Canceled":
                    updateText.innerHTML =
                        "Canceling request...<br><small>Sending cancellation email...</small>";
                    break;

                default:
                    updateText.innerHTML =
                        "Updating request...<br><small>Please wait...</small>";
            }

            updateOverlay.classList.remove("d-none");

            // Prevent double submission
            const submitBtn = form.querySelector("button[type='submit']");
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Updating...
        `;
        });

    });

    const cancelOverlay = document.getElementById("cancelOverlay");

    document.querySelectorAll(".cancelRequestForm").forEach(form => {
        form.addEventListener("submit", function () {
            cancelOverlay.classList.remove("d-none");

            // Prevent double submission
            const submitBtn = form.querySelector("button[type='submit']");
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Canceling...
        `;
        });
    });

    // If the user comes back with the browser's Back button, don't leave the overlay stuck
    window.addEventListener("pageshow", function (e) {
        if (e.persisted) cancelOverlay.classList.add("d-none");
    });
</script>

<style>
    .notif-highlight {
        background: #fff7ed !important;
        outline: 2px solid #fdba74;
        outline-offset: -2px;
    }

    .notif-highlight td {
        background: #fff7ed !important;
    }
</style>

<script>
    (function () {
        'use strict';

        var params = new URLSearchParams(window.location.search);
        if (params.get('notif') !== '1') return;

        var ref = (params.get('ref') || '').trim();
        var svc = (params.get('svc') || '').trim();

        function highlight() {
            if (!ref) return;

            var hit = null;
            document.querySelectorAll('.requests-table tbody tr').forEach(function (tr) {
                if (hit) return;
                var refCell = tr.querySelector('.ref-no');
                var svcCell = tr.querySelector('[data-label="Service"]');
                if (!refCell) return;

                var sameRef = refCell.textContent.trim() === ref;
                var sameSvc = !svc || (svcCell && svcCell.textContent.trim() === svc);
                if (sameRef && sameSvc) hit = tr;
            });

            if (hit) {
                hit.classList.add('notif-highlight');
                hit.scrollIntoView({ block: 'center', behavior: 'smooth' });
                setTimeout(function () { hit.classList.remove('notif-highlight'); }, 6000);
            }

            // keep ?status=..., drop the notification params so a refresh doesn't re-highlight
            ['notif', 'ref', 'svc'].forEach(function (k) { params.delete(k); });
            var qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        }

        if (document.readyState === 'complete') { highlight(); }
        else { window.addEventListener('load', highlight); }
    })();
</script>

<?= $this->include('dashboard_layout/loading') ?>