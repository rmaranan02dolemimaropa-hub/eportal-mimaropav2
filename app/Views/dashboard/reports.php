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
    <div class="report-page">

        <!-- ---------- Header ---------- -->
        <div class="r-header">
            <div>
                <span class="r-eyebrow">Dashboard / Reports</span>
                <h1 class="r-title">Service Requests Report</h1>

            </div>
            <div class="r-date">As of <?= date('F d, Y') ?></div>
        </div>

        <!-- ---------- Filters ---------- -->
        <form class="r-filters no-print" method="get">
            <div class="r-field">
                <label for="fYear">Year</label>
                <select id="fYear" name="year">
                    <option value="">All Years</option>

                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= (isset($_GET['year']) && $_GET['year'] == $y) ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>

                </select>
            </div>


            <div class="r-field">
                <label for="fMonth">Month</label>
                <select id="fMonth" name="month">
                    <option value="">All Months</option>
                    <option value="1" <?= $month == '1' ? 'selected' : '' ?>>January</option>
                    <option value="2" <?= $month == '2' ? 'selected' : '' ?>>February</option>
                    <option value="3" <?= $month == '3' ? 'selected' : '' ?>>March</option>
                    <option value="4" <?= $month == '4' ? 'selected' : '' ?>>April</option>
                    <option value="5" <?= $month == '5' ? 'selected' : '' ?>>May</option>
                    <option value="6" <?= $month == '6' ? 'selected' : '' ?>>June</option>
                    <option value="7" <?= $month == '7' ? 'selected' : '' ?>>July</option>
                    <option value="8" <?= $month == '8' ? 'selected' : '' ?>>August</option>
                    <option value="9" <?= $month == '9' ? 'selected' : '' ?>>September</option>
                    <option value="10" <?= $month == '10' ? 'selected' : '' ?>>October</option>
                    <option value="11" <?= $month == '11' ? 'selected' : '' ?>>November</option>
                    <option value="12" <?= $month == '12' ? 'selected' : '' ?>>December</option>
                </select>
            </div>

            <div class="r-field">
                <label for="fService">Service Availed</label>
                <select id="fService" name="service">
                    <option value="">Select a service</option>
                    <option value="Service Record" <?= $service == 'Service Record' ? 'selected' : '' ?>>
                        Service Record
                    </option>
                    <option value="Certificate of Employment" <?= $service == 'Certificate of Employment' ? 'selected' : '' ?>>
                        Certificate of Employment
                    </option>
                    <option value="Certificate with Compensation" <?= $service == 'Certificate with Compensation' ? 'selected' : '' ?>>
                        Certificate with Compensation
                    </option>
                    <option value="Certification" <?= $service == 'Certification' ? 'selected' : '' ?>>
                        Certification
                    </option>
                    <option value="IPCR" <?= $service == 'IPCR' ? 'selected' : '' ?>>
                        IPCR
                    </option>
                    <option value="201 Documents" <?= $service == '201 Documents' ? 'selected' : '' ?>>
                        201 Documents
                    </option>
                    <option value="Loan Assistance" <?= $service == 'Loan Assistance' ? 'selected' : '' ?>>
                        Loan Assistance
                    </option>
                    <option value="Inquiry of Leave Balance" <?= $service == 'Inquiry of Leave Balance' ? 'selected' : '' ?>>
                        Inquiry of Leave Balance
                    </option>
                    <option value="Others" <?= $service == 'Others' ? 'selected' : '' ?>>
                        Others
                    </option>
                </select>
            </div>
            <div class="r-field">
                <label for="fStatus">Status</label>
                <select id="fStatus" name="status">
                    <option value="">All Status</option>
                    <option value="New Application" <?= ($status ?? '') == 'New Application' ? 'selected' : '' ?>>
                        New Application
                    </option>
                    <option value="Processing" <?= ($status ?? '') == 'Processing' ? 'selected' : '' ?>>
                        Processing
                    </option>
                    <option value="For Released" <?= ($status ?? '') == 'For Released' ? 'selected' : '' ?>>
                        For Released
                    </option>
                    <option value="Completed" <?= ($status ?? '') == 'Completed' ? 'selected' : '' ?>>
                        Completed
                    </option>
                    <option value="Rejected" <?= ($status ?? '') == 'Rejected' ? 'selected' : '' ?>>
                        Rejected
                    </option>
                </select>
            </div>

            <div class="r-field grow">
                <label for="fSearch">Search</label>
                <input id="fSearch" name="q" type="text" class="form-control" value="<?= esc($search ?? '') ?>"
                    placeholder="Search by name, reference no., or service...">
            </div>

            <button type="submit" class="r-btn r-btn-primary">Search</button>
            <button type="button" class="r-btn r-btn-text" id="btnReset">
                Reset
            </button>
        </form>

        <!-- ---------- Toolbar ---------- -->
        <div class="r-toolbar no-print">
            <div class="r-count">Showing <b><?= count($requests) ?></b> of <b><?= $pager->getTotal() ?></b> requests
            </div>
            <div class="r-export-group">
                <button type="button" class="r-btn r-btn-outline" id="btnPrint">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button type="button" class="r-btn r-btn-outline" id="btnCsv">
                    <i class="bi bi-filetype-csv"></i> Export CSV
                </button>
                <button class="btn tool-btn" type="button" id="exportButton">
                    <i class="bi bi-download"></i> Export PDF
                </button>

                <div id="exportOverlay" class="export-overlay">
                    <div class="export-card">
                        <h3 id="exportTitle">Preparing PDF Report</h3>
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

        <div class="table-responsive">
            <table class="table requests-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Reference No.</th>
                        <th>Requestor</th>
                        <th>Service</th>
                        <th>Date Requested</th>
                        <th class="text-end">Status</th>
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

                                <td class="text-end">

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
                <!-- Showing <?= count($requests) ?> of <?= $pager->getTotal() ?> requests -->
            </span>

            <ul class="pagination mb-0">

                Previous
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

    </div>
</main>
</div>
<!-- Bootstrap 5 JS Bundle (needed for offcanvas / dropdown components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

    document.addEventListener("DOMContentLoaded", function () {

        const searchInput = document.getElementById("fSearch");
        const tableRows = document.querySelectorAll(".requests-table tbody tr");

        searchInput.addEventListener("keyup", function () {

            const keyword = this.value.toLowerCase().trim();

            tableRows.forEach(function (row) {

                const text = row.textContent.toLowerCase();

                if (text.includes(keyword)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }

            });

        });

    });

    document.addEventListener('DOMContentLoaded', function () {

        function getFilters() {
            return new URLSearchParams(window.location.search).toString();
        }

        // PRINT
        document.getElementById('btnPrint').addEventListener('click', function () {
            window.open(
                "<?= site_url('reports/print') ?>?" + getFilters(),
                "_blank"
            );
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const overlay = document.getElementById("exportOverlay");
        const bar = document.getElementById("progressBar");
        const percent = document.getElementById("progressValue");
        const text = document.getElementById("exportText");
        const title = document.getElementById("exportTitle");
        async function exportReport(button, url, filename, format) {

            button.disabled = true;
            overlay.classList.add("is-visible");

            document.getElementById("exportTitle").textContent =
                format === "pdf" ? "Preparing PDF Report" : "Preparing CSV Report";

            let progress = 0;

            bar.style.width = "0%";
            percent.textContent = "0%";
            text.textContent = "Preparing report...";

            const timer = setInterval(() => {

                if (progress < 90) {

                    // Smaller, steadier steps so the bar doesn't rush to 90%
                    // and then idle — paced to roughly match real generation time.
                    progress += Math.floor(Math.random() * 3) + 1; // was: * 8 + 3

                    if (progress > 90)
                        progress = 90;

                    bar.style.width = progress + "%";
                    percent.textContent = progress + "%";

                    if (progress > 25)
                        text.textContent = "Collecting records...";

                    if (progress > 55)
                        text.textContent = format === "pdf" ? "Generating PDF..." : "Generating CSV...";

                    if (progress > 80 && format === "pdf")
                        text.textContent = "Almost finished...";
                }

            }, 250); // was: 180

            try {

                const form = document.querySelector(".r-filters");
                const params = new URLSearchParams(new FormData(form));

                const response = await fetch(url + "?" + params.toString());

                if (!response.ok)
                    throw new Error();

                const blob = await response.blob();

                clearInterval(timer);

                progress = 100;
                bar.style.width = "100%";
                percent.textContent = "100%";
                text.textContent = "Download ready.";

                const objectUrl = URL.createObjectURL(blob);

                const a = document.createElement("a");
                a.href = objectUrl;
                a.download = filename;
                a.click();

                URL.revokeObjectURL(objectUrl);

                setTimeout(() => {

                    overlay.classList.remove("is-visible");
                    button.disabled = false;

                    bar.style.width = "0%";
                    percent.textContent = "0%";

                }, 700);

            } catch (e) {

                clearInterval(timer);

                overlay.classList.remove("is-visible");
                button.disabled = false;

                alert("Unable to export " + format.toUpperCase() + ".");

            }

        }

        document.getElementById("exportButton").addEventListener("click", function () {
            exportReport(this, "<?= site_url('export/reports-pdf') ?>", "HR_Request_Report.pdf", "pdf");
        });

        document.getElementById("btnCsv").addEventListener("click", function () {
            exportReport(this, "<?= site_url('export/reports-csv') ?>", "HR_Request_Report.csv", "csv");
        });

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const loadingScreen = document.getElementById('doleLoadingScreen');

        if (!loadingScreen) return;

        const sidebarLinks = document.querySelectorAll('.nav-link');

        let isNavigating = false;

        function showLoadingScreen() {
            loadingScreen.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function hideLoadingScreen() {
            loadingScreen.classList.remove('show');
            document.body.style.overflow = '';
        }

        sidebarLinks.forEach(function (link) {

            link.addEventListener('click', function (event) {

                const href = this.getAttribute('href');

                if (
                    !href ||
                    href === '#' ||
                    href.startsWith('#') ||
                    href === 'javascript:void(0)'
                ) {
                    return;
                }

                // Skip external links and anything opening in a new tab
                if (
                    this.target === '_blank' ||
                    this.hasAttribute('download') ||
                    this.getAttribute('rel') === 'external'
                ) {
                    return;
                }

                // Skip if link points to the current page (no real navigation happening)
                let resolvedHref;
                try {
                    resolvedHref = new URL(href, window.location.href).href;
                } catch (e) {
                    resolvedHref = null;
                }

                if (resolvedHref && resolvedHref === window.location.href) {
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

                // Guard against rapid double-clicks queuing multiple navigations
                if (isNavigating) {
                    event.preventDefault();
                    return;
                }

                isNavigating = true;

                /* Stop normal navigation temporarily */

                event.preventDefault();

                /* Show loading screen */

                showLoadingScreen();

                /*
                 * Navigate immediately.
                 * The loading screen will remain visible
                 * while the next page is loading.
                 */

                window.location.href = href;

                // Fallback: if navigation stalls (offline, server error,
                // blocked request) don't leave the user stuck behind the
                // overlay forever.
                setTimeout(function () {
                    isNavigating = false;
                    hideLoadingScreen();
                }, 15000);

            });

        });

        /* Back / Forward, including bfcache restores */

        window.addEventListener('pageshow', function () {
            isNavigating = false;
            hideLoadingScreen();
        });

        // Belt-and-suspenders: if the page is somehow still visible
        // with the overlay up (e.g. blocked navigation), clear it once
        // the tab regains focus.
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {
                isNavigating = false;
                hideLoadingScreen();
            }
        });

    });
</script>