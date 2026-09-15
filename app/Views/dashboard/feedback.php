<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<?php
$feedbacks = $feedbacks ?? [];
$pager = $pager ?? null;

$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';
$service = $_GET['service'] ?? '';
$rating = $_GET['rating'] ?? '';
$search = $_GET['search'] ?? '';
?>



<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">


    <div class="report-page">

        <!-- ---------- Header ---------- -->
        <div class="r-header">
            <div>
                <span class="r-eyebrow">Dashboard / Feedback</span>
                <h1 class="r-title">Client Satisfaction Feedback</h1>
            </div>
            <div class="r-date">As of <?= date('F d, Y') ?></div>
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

        <!-- ---------- Filters ---------- -->
        <form class="r-filters no-print" method="get">

            <div class="r-field">
                <label for="fYear">Year</label>
                <select id="fYear" name="year">
                    <option value="">All Years</option>

                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
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
                    <option value="">All Services</option>
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
                    <option value="Payslip" <?= $service == 'Payslip' ? 'selected' : '' ?>>
                        Payslip
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
                <label for="fRating">Overall Rating</label>
                <select id="fRating" name="rating">
                    <option value="">All Ratings</option>
                    <option value="Excellent" <?= $rating == 'Excellent' ? 'selected' : '' ?>>
                        Excellent
                    </option>
                    <option value="Very Satisfied" <?= $rating == 'Very Satisfied' ? 'selected' : '' ?>>
                        Very Satisfied
                    </option>
                    <option value="Needs Review" <?= $rating == 'Needs Review' ? 'selected' : '' ?>>
                        Needs Review
                    </option>
                </select>
            </div>

            <div class="r-field grow">
                <label for="fSearch">Search</label>
                <input id="fSearch" name="search" type="text" class="form-control" value="<?= esc($search) ?>"
                    placeholder="Search by name, reference no., or service...">
            </div>

            <button type="submit" class="r-btn r-btn-primary">Search</button>
            <button type="button" class="r-btn r-btn-text" id="btnReset">
                Reset
            </button>
        </form>

        <!-- ---------- Toolbar ---------- -->
        <div class="r-toolbar no-print">
            <div class="r-count">
                Showing <b><?= count($feedbacks) ?></b> of
                <b><?= $pager !== null ? $pager->getTotal() : count($feedbacks) ?></b> feedback(s)
            </div>
            <div class="r-export-group">
                <button class="btn tool-btn" type="button" id="exportButton" title="Export this response to Excel">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> TSR Report
                </button>
                <button class="btn tool-btn" type="button" id="exportcsrButton" title="Export this response to Excel">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> CSR Report
                </button>

            </div>
        </div>


        <div class="table-responsive">
            <table class="table requests-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Reference No.</th>
                        <th>Service Availed</th>
                        <th>Client</th>
                        <th>Client Type</th>
                        <th>Date Submitted</th>
                        <th>Overall Rating</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (!empty($feedbacks)): ?>

                        <?php foreach ($feedbacks as $row): ?>

                            <tr>

                                <td>
                                    <?= esc($row['reference_no']) ?>
                                </td>

                                <td>
                                    <?= esc($row['other_service']) ?>
                                </td>

                                <td>
                                    <?= esc($row['name'] ?: 'Anonymous') ?>
                                </td>

                                <td>
                                    <?= esc($row['client_type']) ?>
                                </td>

                                <td>
                                    <?= date('F d, Y', strtotime($row['created_at'])) ?>
                                </td>

                                <td>

                                    <?php

                                    $scores = [
                                        $row['rating_1'],
                                        $row['rating_2'],
                                        $row['rating_3'],
                                        $row['rating_6'],
                                        $row['rating_7'],
                                        $row['rating_8'],
                                    ];

                                    $count = array_count_values($scores);

                                    if (($count['Strongly agree'] ?? 0) >= 4) {

                                        echo '<span class="badge bg-success">Excellent</span>';
                                    } elseif (($count['Agree'] ?? 0) >= 4) {

                                        echo '<span class="badge bg-primary">Very Satisfied</span>';
                                    } else {

                                        echo '<span class="badge bg-warning text-dark">Needs Review</span>';
                                    }

                                    ?>

                                </td>

                                <td>

                                    <a href="<?= base_url('feedback/print/hr/' . $row['reference_no']) ?>" target="_blank"
                                        class="btn btn-sm btn-outline-secondary">

                                        <i class="bi bi-printer"></i>

                                    </a>
                                    <a href="<?= base_url('exports/satisfaction-excel/' . $row['reference_no']) ?>"
                                        target="_blank" class="btn btn-sm btn-outline-secondary"
                                        title="Export this response to Excel">

                                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="text-center py-5">

                                <div class="text-muted">

                                    <i class="bi bi-inbox" style="font-size: 2.5rem;"></i>

                                    <div class="mt-2 fw-semibold">
                                        No Feedback found.
                                    </div>

                                    <small>
                                        There are currently no client satisfaction responses matching your filters.
                                    </small>

                                </div>

                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <?php if ($pager !== null): ?>
            <nav class="table-pagination">

                <span class="pagination-summary">
                    Showing <?= count($feedbacks) ?> of <?= $pager->getTotal() ?> feedback(s)
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

    </div>
</main>

</div>



<!-- Bootstrap 5 JS Bundle (needed for offcanvas / dropdown components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        document.getElementById("btnReset").addEventListener("click", function () {
            window.location.href = window.location.pathname;
        });

    });

    document.addEventListener("DOMContentLoaded", function () {
        setCurrentDate();
    });

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


</script>

<script>
    document.getElementById('exportButton').addEventListener('click', function () {
        const params = new URLSearchParams({
            year: document.getElementById('fYear').value,
            month: document.getElementById('fMonth').value,
            service: document.getElementById('fService').value,
            rating: document.getElementById('fRating').value,
            search: document.getElementById('fSearch').value,
        });
        for (const [key, val] of [...params.entries()]) {
            if (!val) params.delete(key);
        }
        window.open('<?= base_url('exports/satisfaction-excel') ?>?' + params.toString(), '_blank');
    });


</script>
<script>
    document.getElementById('exportcsrButton').addEventListener('click', function () {
        const params = new URLSearchParams({
            year: document.getElementById('fYear').value,
            month: document.getElementById('fMonth').value,
            service: document.getElementById('fService').value,
            rating: document.getElementById('fRating').value,
            search: document.getElementById('fSearch').value,
        });
        for (const [key, val] of [...params.entries()]) {
            if (!val) params.delete(key);
        }
        window.open('<?= base_url('client-satisfaction-report') ?>?' + params.toString(), '_blank');
    });


</script>



<?= $this->include('dashboard_layout/loading') ?>