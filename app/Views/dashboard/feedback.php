<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<?php
$feedbacks = $feedbacks ?? [];
$pager = $pager ?? null;
?>




<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">



    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Feedback</h1>
            <p class="page-subtitle">
                Welcome back, <?= session('fullname') ?> — access and review Client Satisfaction Measurement (CSM)
                responses submitted by clients.
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
                                        There are currently no client satisfaction responses available.
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

    </section>









</main>

</div>



<!-- Bootstrap 5 JS Bundle (needed for offcanvas / dropdown components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchRequest");
        const table = document.querySelector("table tbody");

        searchInput.addEventListener("keyup", function() {
            const filter = this.value.toLowerCase();
            const rows = table.querySelectorAll("tr");

            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();

                if (text.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
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

<?= $this->include('dashboard_layout/loading') ?>