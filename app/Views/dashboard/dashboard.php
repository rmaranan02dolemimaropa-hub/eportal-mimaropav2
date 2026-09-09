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
                <span class="stat-number" data-target="<?= $for_released ?? 0 ?>">0</span>
                <span class="stat-label">For Released</span>
                <span class="stat-trend trend-up"><i class="bi bi-box-seam me-1"></i><?= $released_today ?? 0 ?> ready today
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

            <div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-labelledby="serviceDetailsModalLabel"
                aria-hidden="true">

                <div class="modal-dialog modal-lg modal-dialog-scrollable">

                    <form id="serviceRequestForm" method="post" action="<?= site_url('request/save') ?>"
                        class="modal-content needs-validation">

                        <?= csrf_field() ?>

                        <!-- ===================== HEADER ===================== -->
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title fw-bold" id="serviceDetailsModalLabel">
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    HR Service Request
                                </h5>
                                <small class="text-muted">
                                    Please complete all required fields.
                                </small>
                            </div>

                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>

                        <!-- ===================== BODY ===================== -->
                        <div class="modal-body">

                            <!-- =====================================================
                     COMMON REQUESTER INFORMATION
                ====================================================== -->

                            <div class="mb-4">

                                <div class="section-label mb-3">
                                    <i class="bi bi-person"></i>
                                    Requester Information
                                </div>

                                <div class="row g-3">

                                    <!-- FULL NAME -->
                                    <div class="col-md-6">
                                        <div class="form-floating">

                                            <input type="text" name="fullname" id="modalFullName" class="form-control"
                                                placeholder="Juan Dela Cruz" required>

                                            <label for="modalFullName">
                                                Full Name
                                                <span class="text-danger">*</span>
                                            </label>

                                            <div class="invalid-feedback">
                                                Please enter your full name.
                                            </div>

                                        </div>
                                    </div>

                                    <!-- CONTACT NUMBER -->
                                    <div class="col-md-6">
                                        <div class="form-floating">

                                            <input type="tel" name="contact_number" id="modalContactNumber"
                                                class="form-control" placeholder="09XXXXXXXXX"
                                                pattern="^(09\d{9}|\+639\d{9})$" maxlength="13" required>

                                            <label for="modalContactNumber">
                                                Contact Number
                                                <span class="text-danger">*</span>
                                            </label>

                                            <div class="invalid-feedback">
                                                Please enter a valid PH mobile number.
                                            </div>

                                        </div>
                                    </div>

                                    <!-- EMAIL -->
                                    <div class="col-md-12">
                                        <div class="form-floating">

                                            <input type="email" name="email_address" id="modalEmailAddress"
                                                class="form-control" placeholder="name@example.com" required>

                                            <label for="modalEmailAddress">
                                                Email Address
                                                <span class="text-danger">*</span>
                                            </label>

                                            <div class="invalid-feedback">
                                                Please enter a valid email address.
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>


                            <!-- =====================================================
                     EMPLOYEE CATEGORY
                ====================================================== -->

                            <div class="mb-4">

                                <div class="section-label mb-3">
                                    <i class="bi bi-person-badge"></i>
                                    Employee Category
                                </div>

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <div class="choice-card">

                                            <input class="form-check-input" type="radio" name="employee_category"
                                                id="modalEmployeeCategoryCOS" value="cos" required>

                                            <label for="modalEmployeeCategoryCOS">
                                                COS Employee
                                            </label>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="choice-card">

                                            <input class="form-check-input" type="radio" name="employee_category"
                                                id="modalEmployeeCategoryPermanent" value="permanent" required>

                                            <label for="modalEmployeeCategoryPermanent">
                                                Permanent Employee
                                            </label>

                                        </div>

                                    </div>

                                </div>

                                <div class="invalid-feedback" id="modalEmployeeCategoryError" style="display:none;">
                                    Please select your employee category.
                                </div>

                            </div>


                            <!-- =====================================================
                     DATE REQUESTED
                ====================================================== -->

                            <div class="mb-4">

                                <div class="section-label mb-3">
                                    <i class="bi bi-calendar3"></i>
                                    Request Information
                                </div>

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <div class="form-floating">

                                            <input type="date" name="date_requested" id="modalDateRequested"
                                                class="form-control" value="<?= date('Y-m-d') ?>" required>

                                            <label for="modalDateRequested">
                                                Date Requested
                                                <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- =====================================================
                     SERVICE AVAILED
                ====================================================== -->

                            <div class="mb-4">

                                <div class="section-label mb-3">
                                    <i class="bi bi-list-check"></i>
                                    Service Request
                                </div>

                                <div class="form-floating">

                                    <select id="modalServiceAvailed" name="service_availed" class="form-select"
                                        required>

                                        <option value="" selected disabled>
                                            Select a service
                                        </option>

                                        <option value="service1" data-category="permanent">
                                            Service Record
                                        </option>

                                        <option value="service5" data-category="cos,permanent">
                                            Certificate of Employment
                                        </option>

                                        <option value="service2" data-category="permanent">
                                            Certificate with Compensation
                                        </option>

                                        <option value="service6" data-category="permanent">
                                            Certification
                                        </option>

                                        <option value="service7" data-category="permanent">
                                            IPCR
                                        </option>

                                        <option value="service8" data-category="permanent">
                                            201 Documents
                                        </option>

                                        <option value="service10" data-category="cos">
                                            Performance Evaluation
                                        </option>

                                        <option value="service11" data-category="cos">
                                            Payslip
                                        </option>

                                        <option value="service12" data-category="cos">
                                            Contract of Service
                                        </option>

                                        <option value="service9" data-category="cos,permanent">
                                            Others
                                        </option>

                                    </select>

                                    <label for="modalServiceAvailed">
                                        Service Availed
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="invalid-feedback">
                                        Please select a service.
                                    </div>

                                </div>

                            </div>


                            <!-- =====================================================
                     SERVICE PURPOSE
                ====================================================== -->

                            <div class="mb-4">

                                <div class="form-floating">

                                    <textarea name="service_purpose" id="modalServicePurpose" class="form-control"
                                        placeholder="Please state your purpose" maxlength="500" style="height:130px;"
                                        required></textarea>

                                    <label for="modalServicePurpose">
                                        Purpose / Request Details
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="invalid-feedback">
                                        Please specify the purpose of your request.
                                    </div>

                                </div>

                                <div class="form-text">
                                    Please provide the reason why you need this HR service.
                                </div>

                            </div>


                            <!-- =====================================================
                     CERTIFICATE OF EMPLOYMENT
                ====================================================== -->

                            <div id="modalCOESection" class="dynamic-section d-none">

                                <hr>

                                <div class="section-label mb-3">
                                    <i class="bi bi-briefcase"></i>
                                    Employment Information
                                </div>


                                <!-- COS EMPLOYEE -->
                                <div id="modalCOSSection" class="d-none">

                                    <div class="d-flex justify-content-between align-items-center mb-3">

                                        <h6 class="fw-bold mb-0">
                                            Employment History
                                        </h6>

                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            id="modalAddEmployment">

                                            <i class="bi bi-plus-circle me-1"></i>
                                            Add Employment Record

                                        </button>

                                    </div>

                                    <div id="modalEmploymentContainer"></div>

                                    <div id="modalNoEmployment" class="text-center text-muted border rounded p-4">

                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                        No employment records added.

                                    </div>

                                </div>

                            </div>


                            <!-- =====================================================
                     CERTIFICATION
                ====================================================== -->

                            <div id="modalCertificationSection" class="dynamic-section d-none">

                                <hr>

                                <div class="section-label mb-3">
                                    <i class="bi bi-patch-check"></i>
                                    Certification
                                </div>

                                <label class="form-label fw-semibold">
                                    Certification Needed
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <div class="choice-card">

                                            <input type="checkbox" class="form-check-input" name="certification[]"
                                                value="No Pending Case" id="modalCertNoPending">

                                            <label for="modalCertNoPending">
                                                No Pending Case
                                            </label>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="choice-card">

                                            <input type="checkbox" class="form-check-input" name="certification[]"
                                                value="Last Salary Received" id="modalCertLastSalary">

                                            <label for="modalCertLastSalary">
                                                Last Salary Received
                                            </label>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="choice-card">

                                            <input type="checkbox" class="form-check-input" name="certification[]"
                                                value="Leave Credits" id="modalCertLeaveCredits">

                                            <label for="modalCertLeaveCredits">
                                                Leave Credits
                                            </label>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="choice-card">

                                            <input type="checkbox" class="form-check-input" name="certification[]"
                                                value="Leave Without Pay (LWOP)" id="modalCertLWOP">

                                            <label for="modalCertLWOP">
                                                Leave Without Pay (LWOP)
                                            </label>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="choice-card">

                                            <input type="checkbox" class="form-check-input" name="certification[]"
                                                value="Leave Without Pay (NO LWOP)" id="modalCertNoLWOP">

                                            <label for="modalCertNoLWOP">
                                                Leave Without Pay (NO LWOP)
                                            </label>

                                        </div>
                                    </div>

                                </div>

                                <div id="modalCertificationError" class="text-danger small mt-2 d-none">

                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Please select at least one certification.

                                </div>

                            </div>


                            <!-- =====================================================
                     IPCR
                ====================================================== -->

                            <div id="modalIPCRSection" class="dynamic-section d-none">

                                <hr>

                                <div class="section-label mb-3">
                                    <i class="bi bi-bar-chart-line"></i>
                                    IPCR
                                </div>

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <div class="form-floating">

                                            <select name="ipcr_semester" id="modalIPCRSemester" class="form-select">

                                                <option value="" selected disabled>
                                                    Select Semester
                                                </option>

                                                <option value="1st Semester">
                                                    1st Semester (January - June)
                                                </option>

                                                <option value="2nd Semester">
                                                    2nd Semester (July - December)
                                                </option>

                                            </select>

                                            <label for="modalIPCRSemester">
                                                Semester
                                                <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-floating">

                                            <select name="ipcr_year" id="modalIPCRYear" class="form-select">

                                                <option value="" selected disabled>
                                                    Select Year
                                                </option>

                                                <?php for ($year = date('Y'); $year >= 2000; $year--): ?>

                                                    <option value="<?= $year ?>">
                                                        <?= $year ?>
                                                    </option>

                                                <?php endfor; ?>

                                            </select>

                                            <label for="modalIPCRYear">
                                                Year
                                                <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- =====================================================
                     201 DOCUMENTS
                ====================================================== -->

                            <div id="modal201Section" class="dynamic-section d-none">

                                <hr>

                                <div class="section-label mb-3">
                                    <i class="bi bi-folder2-open"></i>
                                    201 Documents
                                </div>

                                <label class="form-label fw-semibold">
                                    Select Document/s Needed
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="document-tag-group">

                                    <?php
                                    $documents201 = [
                                        'Appointment (CSC33)',
                                        'Assumption of Duty',
                                        'Oath of Office',
                                        'Personal Data Sheet (CSC Form 212)',
                                        'Position Description Form',
                                        'Certificate of Eligibilities/License',
                                        'Designation Orders (If Applicable)',
                                        'Statement of Assets, Liabilities and Net Worth (SALN)',
                                        'Notices of Salary Adjustments/Step Increments',
                                        'Medical Certificate (CSC Form 211)',
                                        'Psychiatric Test/Drugs Test (If Applicable)',
                                        'NBI Clearance',
                                        'School Diploma and Transcript of Records',
                                        'Marriage Contract/Certificate',
                                        'Certificate of Leave Balances (For Transferees)',
                                        'Clearance from Property and Accountabilities (For Transferees)',
                                        'Commendations/Awards or Copies of Disciplinary Actions (If Any)',
                                        'Contract of Service (If Applicable)',
                                        'Individual Performance Commitment and Review (IPCR)',
                                        'BIR (2316)',
                                        'Ombudsman',
                                        'Service Record',
                                        'Office Order as OIC',
                                        'Certificate of Trainings',
                                        'Certification'
                                    ];
                                    ?>

                                    <?php foreach ($documents201 as $index => $document): ?>

                                        <label class="document-tag">

                                            <input type="checkbox" name="documents_201[]" value="<?= esc($document) ?>">

                                            <span>
                                                <?= esc($document) ?>
                                            </span>

                                        </label>

                                    <?php endforeach; ?>


                                    <!-- OTHER FILES -->
                                    <label class="document-tag">

                                        <input type="checkbox" id="modalOtherFiles" name="documents_201[]"
                                            value="Other Files">

                                        <span>Other Files</span>

                                    </label>

                                </div>

                                <div id="modalSpecify201Wrapper" class="mt-3 d-none">

                                    <div class="form-floating">

                                        <textarea name="specify_201" id="modalSpecify201" class="form-control"
                                            maxlength="255" placeholder="Please specify"
                                            style="height:100px;"></textarea>

                                        <label for="modalSpecify201">
                                            Please Specify Other Document/s
                                        </label>

                                    </div>

                                </div>

                                <div id="modalDocuments201Error" class="text-danger small mt-2 d-none">

                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Please select at least one document.

                                </div>

                            </div>


                            <!-- =====================================================
                     PERFORMANCE EVALUATION
                ====================================================== -->

                            <div id="modalPerformanceSection" class="dynamic-section d-none">

                                <hr>

                                <div class="section-label mb-3">
                                    <i class="bi bi-chart-bar"></i>
                                    Performance Evaluation
                                </div>

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <div class="form-floating">

                                            <select name="performance_evaluation_semester" id="modalPerformanceSemester"
                                                class="form-select">

                                                <option value="" selected disabled>
                                                    Select Semester
                                                </option>

                                                <option value="1st Semester">
                                                    1st Semester (January - June)
                                                </option>

                                                <option value="2nd Semester">
                                                    2nd Semester (July - December)
                                                </option>

                                            </select>

                                            <label for="modalPerformanceSemester">
                                                Semester
                                                <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-floating">

                                            <select name="performance_evaluation_year" id="modalPerformanceYear"
                                                class="form-select">

                                                <option value="" selected disabled>
                                                    Select Year
                                                </option>

                                                <?php for ($year = date('Y'); $year >= 2000; $year--): ?>

                                                    <option value="<?= $year ?>">
                                                        <?= $year ?>
                                                    </option>

                                                <?php endfor; ?>

                                            </select>

                                            <label for="modalPerformanceYear">
                                                Year
                                                <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- =====================================================
                     CONTRACT OF SERVICE
                ====================================================== -->

                            <div id="modalContractSection" class="dynamic-section d-none">

                                <hr>

                                <div class="section-label mb-3">
                                    <i class="bi bi-file-earmark-ruled"></i>
                                    Contract of Service
                                </div>

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <div class="form-floating">

                                            <select name="contract_of_service_year" id="modalContractYear"
                                                class="form-select">

                                                <option value="" selected disabled>
                                                    Select Year
                                                </option>

                                                <?php for ($year = date('Y'); $year >= 2000; $year--): ?>

                                                    <option value="<?= $year ?>">
                                                        <?= $year ?>
                                                    </option>

                                                <?php endfor; ?>

                                            </select>

                                            <label for="modalContractYear">
                                                Contract Year
                                                <span class="text-danger">*</span>
                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- =====================================================
                     OTHERS
                ====================================================== -->

                            <div id="modalOthersSection" class="dynamic-section d-none">

                                <hr>

                                <div class="section-label mb-3">
                                    <i class="bi bi-chat-square-text"></i>
                                    Others
                                </div>

                                <div class="form-floating">

                                    <textarea name="other_request" id="modalOtherRequest" class="form-control"
                                        maxlength="255" placeholder="Please specify" style="height:130px;"></textarea>

                                    <label for="modalOtherRequest">
                                        Please Specify
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="invalid-feedback">
                                        Please specify your request.
                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- ===================== FOOTER ===================== -->

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                <i class="bi bi-x-lg me-1"></i>
                                Cancel

                            </button>

                            <button type="submit" class="btn btn-primary">

                                <i class="bi bi-send-check me-1"></i>
                                Submit Request

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

    serviceRequestForm.addEventListener('submit', function(event) {
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
<script>
    (function() {
        'use strict';

        const modalForm = document.getElementById('serviceRequestForm');

        if (!modalForm) return;

        /* =========================================================
           ELEMENTS
        ========================================================= */

        const categoryRadios = modalForm.querySelectorAll(
            'input[name="employee_category"]'
        );

        const serviceSelect = document.getElementById('modalServiceAvailed');

        const dateRequested = document.getElementById('modalDateRequested');

        const coeSection = document.getElementById('modalCOESection');
        const cosSection = document.getElementById('modalCOSSection');

        const certificationSection =
            document.getElementById('modalCertificationSection');

        const ipcrSection =
            document.getElementById('modalIPCRSection');

        const documents201Section =
            document.getElementById('modal201Section');

        const performanceSection =
            document.getElementById('modalPerformanceSection');

        const contractSection =
            document.getElementById('modalContractSection');

        const othersSection =
            document.getElementById('modalOthersSection');

        const employeeCategoryError =
            document.getElementById('modalEmployeeCategoryError');

        const certificationError =
            document.getElementById('modalCertificationError');

        const documents201Error =
            document.getElementById('modalDocuments201Error');

        const otherFilesCheckbox =
            document.getElementById('modalOtherFiles');

        const specify201Wrapper =
            document.getElementById('modalSpecify201Wrapper');

        const specify201 =
            document.getElementById('modalSpecify201');

        const otherRequest =
            document.getElementById('modalOtherRequest');

        const employmentContainer =
            document.getElementById('modalEmploymentContainer');

        const noEmployment =
            document.getElementById('modalNoEmployment');

        const addEmploymentBtn =
            document.getElementById('modalAddEmployment');


        /* =========================================================
           SERVICE LABELS
        ========================================================= */

        const serviceLabels = {
            service1: 'Service Record',
            service2: 'Certificate with Compensation',
            service5: 'Certificate of Employment',
            service6: 'Certification',
            service7: 'IPCR',
            service8: '201 Documents',
            service9: 'Others',
            service10: 'Performance Evaluation',
            service11: 'Payslip',
            service12: 'Contract of Service'
        };


        /* =========================================================
           GET EMPLOYEE CATEGORY
        ========================================================= */

        function getEmployeeCategory() {

            const checked = modalForm.querySelector(
                'input[name="employee_category"]:checked'
            );

            return checked ? checked.value : '';

        }


        /* =========================================================
           RESET SERVICE SECTIONS
        ========================================================= */

        function hideAllSections() {

            [
                coeSection,
                certificationSection,
                ipcrSection,
                documents201Section,
                performanceSection,
                contractSection,
                othersSection
            ].forEach(function(section) {

                if (section) {
                    section.classList.add('d-none');
                }

            });

            if (cosSection) {
                cosSection.classList.add('d-none');
            }

            clearSectionValidation();

        }


        /* =========================================================
           CLEAR VALIDATION
        ========================================================= */

        function clearSectionValidation() {

            if (employeeCategoryError) {
                employeeCategoryError.style.display = 'none';
            }

            if (certificationError) {
                certificationError.classList.add('d-none');
            }

            if (documents201Error) {
                documents201Error.classList.add('d-none');
            }

            modalForm.querySelectorAll(
                '.is-invalid'
            ).forEach(function(element) {

                element.classList.remove('is-invalid');

            });

        }


        /* =========================================================
           FILTER SERVICES BASED ON CATEGORY
        ========================================================= */

        function filterServices() {

            const category = getEmployeeCategory();

            Array.from(serviceSelect.options).forEach(function(option) {

                if (!option.value) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }

                const categories =
                    (option.dataset.category || '')
                    .split(',')
                    .map(function(item) {
                        return item.trim();
                    });

                const allowed =
                    category &&
                    categories.includes(category);

                option.hidden = !allowed;
                option.disabled = !allowed;

            });

            /*
             * If current selected service is no longer allowed,
             * reset it.
             */
            if (
                serviceSelect.value &&
                serviceSelect.selectedOptions.length &&
                serviceSelect.selectedOptions[0].disabled
            ) {

                serviceSelect.value = '';

                hideAllSections();

            }

        }


        /* =========================================================
           SHOW SERVICE SECTION
        ========================================================= */

        function updateServiceSection() {

            hideAllSections();

            const service = serviceSelect.value;
            const category = getEmployeeCategory();

            if (!service) return;


            /* -------------------------
               Certificate of Employment
            ------------------------- */

            if (service === 'service5') {

                coeSection.classList.remove('d-none');

                if (category === 'cos') {
                    cosSection.classList.remove('d-none');
                }

                return;

            }


            /* -------------------------
               Certification
            ------------------------- */

            if (service === 'service6') {

                certificationSection.classList.remove('d-none');

                return;

            }


            /* -------------------------
               IPCR
            ------------------------- */

            if (service === 'service7') {

                ipcrSection.classList.remove('d-none');

                return;

            }


            /* -------------------------
               201 Documents
            ------------------------- */

            if (service === 'service8') {

                documents201Section.classList.remove('d-none');

                return;

            }


            /* -------------------------
               Others
            ------------------------- */

            if (service === 'service9') {

                othersSection.classList.remove('d-none');

                return;

            }


            /* -------------------------
               Performance Evaluation
            ------------------------- */

            if (service === 'service10') {

                performanceSection.classList.remove('d-none');

                return;

            }


            /* -------------------------
               Contract of Service
            ------------------------- */

            if (service === 'service12') {

                contractSection.classList.remove('d-none');

                return;

            }

        }


        /* =========================================================
           EMPLOYEE CATEGORY CHANGE
        ========================================================= */

        categoryRadios.forEach(function(radio) {

            radio.addEventListener('change', function() {

                if (employeeCategoryError) {
                    employeeCategoryError.style.display = 'none';
                }

                filterServices();

                updateServiceSection();

            });

        });


        /* =========================================================
           SERVICE CHANGE
        ========================================================= */

        serviceSelect.addEventListener('change', function() {

            updateServiceSection();

        });


        /* =========================================================
           201 DOCUMENTS - OTHER FILES
        ========================================================= */

        if (otherFilesCheckbox) {

            otherFilesCheckbox.addEventListener('change', function() {

                if (this.checked) {

                    specify201Wrapper.classList.remove('d-none');

                    specify201.setAttribute('required', 'required');

                } else {

                    specify201Wrapper.classList.add('d-none');

                    specify201.removeAttribute('required');

                    specify201.value = '';

                    specify201.classList.remove(
                        'is-valid',
                        'is-invalid'
                    );

                }

            });

        }


        /* =========================================================
           ADD COS EMPLOYMENT RECORD
        ========================================================= */

        let employmentCount = 0;

        function addEmploymentRecord() {

            employmentCount++;

            const card = document.createElement('div');

            card.className =
                'employment-card card border-0 shadow-sm mb-3';

            card.innerHTML = `

            <div class="card-header bg-light
                        d-flex justify-content-between
                        align-items-center">

                <strong class="employment-number">
                    #${employmentCount}
                </strong>

                <button type="button"
                        class="btn btn-sm btn-outline-danger
                               remove-modal-employment">

                    <i class="bi bi-trash"></i>

                </button>

            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <div class="form-floating">

                            <input type="text"
                                   name="period_of_service[]"
                                   class="form-control"
                                   placeholder="Period of Service"
                                   required>

                            <label>
                                Period of Service
                                <span class="text-danger">*</span>
                            </label>

                            <div class="invalid-feedback">
                                Please enter the period of service.
                            </div>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="form-floating">

                            <input type="text"
                                   name="position[]"
                                   class="form-control"
                                   placeholder="Position"
                                   required>

                            <label>
                                Position
                                <span class="text-danger">*</span>
                            </label>

                            <div class="invalid-feedback">
                                Please enter the position.
                            </div>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="form-floating">

                            <select name="office_division[]"
                                    class="form-select"
                                    required>

                                <option value="" selected disabled>
                                    Select Office
                                </option>

                                <option value="Regional Office">
                                    Regional Office
                                </option>

                                <option value="Occidental Mindoro">
                                    Occidental Mindoro
                                </option>

                                <option value="Oriental Mindoro">
                                    Oriental Mindoro
                                </option>

                                <option value="Marinduque">
                                    Marinduque
                                </option>

                                <option value="Romblon">
                                    Romblon
                                </option>

                                <option value="Palawan">
                                    Palawan
                                </option>

                                <option value="IMSD">
                                    IMSD
                                </option>

                                <option value="TSSD">
                                    TSSD
                                </option>

                                <option value="MALSU">
                                    MALSU
                                </option>

                                <option value="ORD">
                                    ORD
                                </option>

                            </select>

                            <label>
                                Office / Division
                                <span class="text-danger">*</span>
                            </label>

                            <div class="invalid-feedback">
                                Please select an office.
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        `;

            employmentContainer.appendChild(card);

            updateEmploymentNumbers();

        }


        /* =========================================================
           UPDATE EMPLOYMENT NUMBERS
        ========================================================= */

        function updateEmploymentNumbers() {

            const cards =
                employmentContainer.querySelectorAll(
                    '.employment-card'
                );

            cards.forEach(function(card, index) {

                const number =
                    card.querySelector('.employment-number');

                if (number) {
                    number.textContent = '#' + (index + 1);
                }

            });

            if (noEmployment) {

                noEmployment.classList.toggle(
                    'd-none',
                    cards.length > 0
                );

            }

            employmentCount = cards.length;

        }


        /* =========================================================
           ADD EMPLOYMENT BUTTON
        ========================================================= */

        if (addEmploymentBtn) {

            addEmploymentBtn.addEventListener(
                'click',
                addEmploymentRecord
            );

        }


        /* =========================================================
           REMOVE EMPLOYMENT
        ========================================================= */

        employmentContainer.addEventListener(
            'click',
            function(event) {

                const button =
                    event.target.closest(
                        '.remove-modal-employment'
                    );

                if (!button) return;

                const card =
                    button.closest('.employment-card');

                if (card) {
                    card.remove();
                }

                updateEmploymentNumbers();

            }
        );


        /* =========================================================
           CERTIFICATION VALIDATION
        ========================================================= */

        function validateCertification() {

            if (serviceSelect.value !== 'service6') {
                return true;
            }

            const checked =
                modalForm.querySelectorAll(
                    'input[name="certification[]"]:checked'
                );

            if (checked.length === 0) {

                certificationError.classList.remove(
                    'd-none'
                );

                return false;

            }

            certificationError.classList.add(
                'd-none'
            );

            return true;

        }


        /* =========================================================
           201 DOCUMENT VALIDATION
        ========================================================= */

        function validate201Documents() {

            if (serviceSelect.value !== 'service8') {
                return true;
            }

            const checked =
                modalForm.querySelectorAll(
                    'input[name="documents_201[]"]:checked'
                );

            if (checked.length === 0) {

                documents201Error.classList.remove(
                    'd-none'
                );

                return false;

            }

            documents201Error.classList.add(
                'd-none'
            );

            return true;

        }


        /* =========================================================
           COS EMPLOYMENT VALIDATION
        ========================================================= */

        function validateEmployment() {

            if (
                serviceSelect.value !== 'service5' ||
                getEmployeeCategory() !== 'cos'
            ) {
                return true;
            }

            const cards =
                employmentContainer.querySelectorAll(
                    '.employment-card'
                );

            if (cards.length === 0) {

                alert(
                    'Please add at least one employment record.'
                );

                return false;

            }

            return true;

        }


        /* =========================================================
           CUSTOM FORM VALIDATION
        ========================================================= */

        modalForm.addEventListener(
            'submit',
            function(event) {

                event.preventDefault();
                event.stopPropagation();

                let valid = true;


                /* EMPLOYEE CATEGORY */

                if (!getEmployeeCategory()) {

                    employeeCategoryError.style.display =
                        'block';

                    valid = false;

                } else {

                    employeeCategoryError.style.display =
                        'none';

                }


                /* BOOTSTRAP VALIDATION */

                if (!modalForm.checkValidity()) {
                    valid = false;
                }


                /* CERTIFICATION */

                if (!validateCertification()) {
                    valid = false;
                }


                /* 201 DOCUMENTS */

                if (!validate201Documents()) {
                    valid = false;
                }


                /* COS EMPLOYMENT */

                if (!validateEmployment()) {
                    valid = false;
                }


                modalForm.classList.add(
                    'was-validated'
                );


                if (!valid) {

                    const firstInvalid =
                        modalForm.querySelector(
                            ':invalid'
                        );

                    if (firstInvalid) {

                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        firstInvalid.focus();

                    }

                    return;

                }


                /* =====================================================
                   SUBMIT
                ===================================================== */

                const submitButton =
                    modalForm.querySelector(
                        'button[type="submit"]'
                    );

                if (submitButton) {

                    submitButton.disabled = true;

                    submitButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>' +
                        'Submitting...';

                }


                modalForm.submit();

            }
        );


        /* =========================================================
           LIVE CERTIFICATION VALIDATION
        ========================================================= */

        modalForm.addEventListener(
            'change',
            function(event) {

                if (
                    event.target.matches(
                        'input[name="certification[]"]'
                    )
                ) {

                    validateCertification();

                }

                if (
                    event.target.matches(
                        'input[name="documents_201[]"]'
                    )
                ) {

                    validate201Documents();

                }

            }
        );


        /* =========================================================
           RESET MODAL WHEN CLOSED
        ========================================================= */

        const serviceModal =
            document.getElementById(
                'serviceDetailsModal'
            );

        if (serviceModal) {

            serviceModal.addEventListener(
                'hidden.bs.modal',
                function() {

                    modalForm.reset();

                    modalForm.classList.remove(
                        'was-validated'
                    );

                    hideAllSections();

                    employmentContainer.innerHTML = '';

                    employmentCount = 0;

                    updateEmploymentNumbers();

                    filterServices();

                    if (dateRequested) {

                        dateRequested.value =
                            new Date()
                            .toISOString()
                            .split('T')[0];

                    }

                    const submitButton =
                        modalForm.querySelector(
                            'button[type="submit"]'
                        );

                    if (submitButton) {

                        submitButton.disabled = false;

                        submitButton.innerHTML =
                            '<i class="bi bi-send-check me-1"></i>' +
                            'Submit Request';

                    }

                }
            );

        }


        /* =========================================================
           INITIALIZE
        ========================================================= */

        filterServices();
        hideAllSections();

    })();
</script>



<?= $this->include('dashboard_layout/loading') ?>