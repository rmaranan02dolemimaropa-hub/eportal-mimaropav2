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
                                <td>
                                    <?php if (!empty($row['feedback_id'])): ?>

                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Submitted
                                        </span>

                                    <?php elseif ($row['status'] == 'For Released'): ?>

                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split"></i>
                                            Waiting for Client
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">
                                            <i class="bi bi-dash-circle"></i>
                                            Not Yet Available
                                        </span>

                                    <?php endif; ?>
                                </td>

                                <td class="text-end">

                                    <div class="row-actions">

                                        <!-- View (Everyone) -->
                                        <button class="row-action-btn" title="View" data-bs-toggle="modal"
                                            data-bs-target="#viewRequestModal" data-id="<?= $row['id'] ?>"
                                            data-reference="<?= esc($row['reference_no'], 'attr') ?>"
                                            data-service="<?= esc($row['service_availed'], 'attr') ?>"
                                            data-fullname="<?= esc($row['fullname'], 'attr') ?>"
                                            data-contact="<?= esc($row['contact_number'], 'attr') ?>"
                                            data-email="<?= esc($row['email_address'], 'attr') ?>"
                                            data-date-requested="<?= esc($row['date_requested'], 'attr') ?>"
                                            data-status="<?= esc($row['status'], 'attr') ?>"
                                            data-category="<?= esc($row['employee_category'] ?? '', 'attr') ?>"
                                            data-history="<?= htmlspecialchars(json_encode($row['employment_history'] ?? []), ENT_QUOTES, 'UTF-8') ?>"
                                            data-certification="<?= esc($row['certification'] ?? '', 'attr') ?>"
                                            data-ipcr-period="<?= esc($row['ipcr_period'] ?? '', 'attr') ?>"
                                            data-specify-201="<?= esc($row['specify_201'] ?? '', 'attr') ?>"
                                            data-remarks="<?= esc($row['remarks'] ?? '', 'attr') ?>">

                                            <i class="bi bi-eye"></i>

                                        </button>


                                        <?php if (in_array($row['status'], ['Processing', 'For Released'])): ?>

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

                    <button type="button" id="startProcessingBtn" class="btn btn-primary d-none">
                        <i class="bi bi-play-circle"></i>
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
                    class="modal-content updateRequestForm">

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
                                <select name="status" class="form-select requestStatus">

                                    <?php if ($row['status'] == 'Processing'): ?>

                                        <option value="Processing" selected>Processing</option>
                                        <option value="For Released">For Released</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Rejected">Rejected</option>

                                    <?php elseif ($row['status'] == 'For Released'): ?>

                                        <option value="For Released" selected>For Released</option>
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
                    "<?= site_url('export/hr-request') ?>" + currentParams,
                    {
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


    function addDetail(label, value) {

        const column = document.createElement('div');

        column.className = 'col-md-6 mb-3';


        column.innerHTML = `
            <small class="text-muted d-block">
                ${label}
            </small>

            <div class="fw-semibold">
                ${value || '-'}
            </div>
        `;


        requestDetails.appendChild(column);

    }



    function addEmploymentHistory(histories) {


        if (!Array.isArray(histories) || histories.length === 0) {

            addDetail(
                'Employment History',
                'No employment records found.'
            );

            return;

        }



        histories.forEach((history, index) => {


            const wrapper = document.createElement('div');

            wrapper.className = 'col-12 mb-3';



            wrapper.innerHTML = `

                <div class="card border shadow-sm">

                    <div class="card-header bg-light fw-bold">

                        #${index + 1}

                    </div>


                    <div class="card-body">


                        <div class="row g-3">


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Period of Service
                                </small>

                                <div class="fw-semibold">
                                    ${history.period_of_service ?? '-'}
                                </div>

                            </div>



                            <div class="col-md-4">

                                <small class="text-muted">
                                    Position
                                </small>

                                <div class="fw-semibold">
                                    ${history.position ?? '-'}
                                </div>

                            </div>



                            <div class="col-md-4">

                                <small class="text-muted">
                                    Office / Division
                                </small>

                                <div class="fw-semibold">
                                    ${history.office_division ?? '-'}
                                </div>

                            </div>


                        </div>


                    </div>

                </div>

            `;


            requestDetails.appendChild(wrapper);


        });


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



        /*
        |--------------------------------------------------------------------------
        | Certificate of Employment
        |--------------------------------------------------------------------------
        */

        if (data.service === 'Certificate of Employment') {


            addDetail(
                'Employee Category',
                data.category
            );


            const category = (data.category || '')
                .trim()
                .toLowerCase();



            if (category === 'cos' || category === 'cos employee') {


                let histories = [];


                try {

                    histories = JSON.parse(data.history || '[]');

                } catch (error) {

                    console.error(
                        'Employment history error:',
                        error
                    );

                }



                addEmploymentHistory(histories);


            } else {


                addDetail(
                    'Position',
                    data.position
                );


            }


        }
        const startBtn = document.getElementById("startProcessingBtn");

        if (data.status === "New Application") {

            startBtn.classList.remove("d-none");
            startBtn.dataset.id = data.id;

        } else {

            startBtn.classList.add("d-none");

        }



        /*
        |--------------------------------------------------------------------------
        | Certification
        |--------------------------------------------------------------------------
        */

        if (data.service === 'Certification') {

            addDetail(
                'Certification Needed',
                data.certification
            );

        }



        /*
        |--------------------------------------------------------------------------
        | IPCR
        |--------------------------------------------------------------------------
        */

        if (data.service === 'IPCR') {

            addDetail(
                'IPCR Period',
                data.ipcrPeriod
            );

        }



        /*
        |--------------------------------------------------------------------------
        | 201 Documents
        |--------------------------------------------------------------------------
        */

        if (data.service === '201 Docs') {

            addDetail(
                'Specified Document',
                data.specify201
            );

        }



        addDetail(
            'Remarks',
            data.remarks
        );


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

            if (!confirm("Start processing this request?")) {
                return;
            }

            overlay.classList.remove("d-none");

            processingText.innerHTML =
                "Updating request...<br>Please wait.";

            fetch("<?= site_url('request/start-processing') ?>", {

                method: "POST",

                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },

                body: JSON.stringify({
                    id: id
                })

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

                case "For Released":
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