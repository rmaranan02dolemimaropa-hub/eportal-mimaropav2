<?= $this->include('layouts/headform') ?>

<main id="main-content">

    <!-- ===================== LOADING OVERLAY ===================== -->
    <div id="loadingOverlay" class="loading-overlay d-none">
        <div class="loading-box">
            <div class="spinner-border" role="status" style="width:3rem;height:3rem;color:#10058C;"></div>
            <p class="mt-3 fw-semibold mb-0">Submitting your request...</p>
            <p class="text-muted small">Please wait, do not close or refresh this page.</p>
        </div>
    </div>

    <div class="page-wrap">

        <!-- ===================== HEADER CARD ===================== -->
        <div class="header-card">
            <div class="header-top">
                <div class="logo-badge">
                    <img src="assets/img/dole_blogo.png" alt="DOLE Logo" class="logo-img">
                </div>
                <div>
                    <div class="agency-line">DOLE MIMAROPA ePortal</div>
                    <h1 class="header-title">Human Resource Request Form</h1>
                </div>
            </div>
            <p class="header-subtitle">Please complete the form below to request HR services. Fields marked with <span
                    class="required-mark">*</span> are required.</p>
        </div>

        <!-- ===================== FORM CARD ===================== -->
        <div class="form-card">

            <div id="formAlert" class="alert alert-dole d-none" role="alert">
                <i class="bi bi-info-circle me-2"></i><span id="formAlertText"></span>
            </div>

            <div id="draftRestoredAlert" class="alert alert-dole d-none" role="alert">
                <i class="bi bi-clock-history me-2"></i>Your previous unsaved entries were restored.
                <button type="button" class="btn btn-sm btn-outline-dole-outline ms-2" id="clearDraftBtn">
                    Clear and start over
                </button>
            </div>

            <form id="hrRequestForm" action="<?= site_url('service-record/save') ?>" method="post"
                class="needs-validation">

                <!-- ---------- COMMON FIELDS ---------- -->
                <?= csrf_field() ?>
                <!-- ---------- DATE REQUESTED (STATIC, TODAY'S DATE, ALWAYS VISIBLE) ---------- -->
                <div class="col-md-6 py-3 ms-md-auto text-md-end" style="max-width: 300px;" id="dateRequestedDisplay">
                    <p class="form-control-plaintext fw-bold mb-0">
                        <?= date('F d, Y') ?>
                    </p>
                    <input type="hidden" name="date_requested" id="dateRequestedHidden" value="<?= date('Y-m-d') ?>">
                </div>

                <!-- ---------- REQUESTER INFO (entered once, applies to all entries) ---------- -->
                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="fullname" class="form-control" id="fullName"
                                placeholder="Juan Dela Cruz" required>
                            <label for="fullName">Full Name <span class="required-mark">*</span></label>
                            <div class="invalid-feedback">Please enter your name.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" name="contact_number" class="form-control" id="contactNumber"
                                placeholder="09XXXXXXXXX" pattern="^(09\d{9}|\+639\d{9})$" maxlength="11" required>
                            <label for="contactNumber">Contact Number <span class="required-mark">*</span></label>
                            <div class="invalid-feedback">Please enter a valid PH mobile number (e.g. 09171234567).
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="email" name="email_address" class="form-control" id="emailAddress"
                                placeholder="name@example.com" required>
                            <label for="emailAddress">Email Address <span class="required-mark">*</span></label>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                    </div>
                </div>
                <!-- ---------- EMPLOYEE CATEGORY (entered once, shared by all entries) ---------- -->
                <div class="col-12 mt-3">
                    <label class="form-label fw-semibold mb-2">Employee Category <span
                            class="required-mark">*</span></label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="radio" name="employee_category"
                                    id="employeeCategoryCOS" value="cos" required>
                                <label for="employeeCategoryCOS">COS Employee</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="radio" name="employee_category"
                                    id="employeeCategoryPermanent" value="permanent" required>
                                <label for="employeeCategoryPermanent">Permanent Employee</label>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block" id="employeeCategoryError" style="display:none !important;">
                        Please select your employee category.
                    </div>
                </div>

                <!-- ---------- REPEATABLE ENTRIES ---------- -->
                <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllEntries">
                        <label class="form-check-label small text-muted" for="selectAllEntries">Select all</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger d-none" id="deleteSelectedEntriesBtn">
                        <i class="bi bi-trash3 me-1"></i>Delete Selected
                    </button>
                </div>

                <div id="entriesContainer"><!-- entry blocks are appended here by JS, cloned from #entryTemplate -->
                </div>

                <div class="text-center my-3">
                    <button type="button" class="btn btn-outline-dole-outline" id="addEntryBtn">
                        <i class="bi bi-plus-circle me-1"></i>Add Another
                    </button>
                </div>

                <!-- ---------- ACTION BUTTONS ---------- -->
                <div class="action-row">
                    <button type="button" class="btn btn-dole-outline" id="cancelBtn">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-dole-primary" id="submitBtn">
                        <i class="bi bi-send-check me-1"></i>Submit Request
                    </button>
                </div>

            </form>

            <!-- =================================================================== -->
            <!-- ENTRY TEMPLATE                                                       -->
            <!-- Inert <template> content is never rendered or submitted as-is.       -->
            <!-- __IDX__  -> replaced with the entry's numeric index (0, 1, 2, ...)   -->
            <!-- __NUM__  -> replaced with the human-facing entry number (index + 1)  -->
            <!-- =================================================================== -->
            <template id="entryTemplate">
                <div class="entry-block card border-0 shadow-sm mb-4" data-entry-index="__IDX__">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3 entry-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check mb-0 entrySelectWrap d-none">
                                    <input class="form-check-input entrySelectCheckbox" type="checkbox"
                                        aria-label="Select this entry">
                                </div>
                                <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Request Entry
                                    #<span class="entry-number">__NUM__</span></h6>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger removeEntryBtn d-none">
                                <i class="bi bi-x-lg"></i> Remove
                            </button>
                        </div>

                        <div class="col-12 py-3">
                            <div class="form-floating">

                                <select class="form-select" name="entries[__IDX__][service_availed]"
                                    id="serviceAvailed__IDX__" required>
                                    <option value="" selected disabled>Select a service</option>
                                    <option value="service1" data-category="permanent">Service Record</option>
                                    <option value="service5" data-category="cos,permanent">Certificate of Employment
                                    </option>
                                    <option value="service2" data-category="permanent">Certificate with Compensation
                                    </option>
                                    <option value="service6" data-category="permanent">Certification</option>
                                    <option value="service7" data-category="permanent">IPCR</option>
                                    <option value="service8" data-category="permanent">201 Documents</option>
                                    <option value="service10" data-category="cos">Performance Evaluation</option>
                                    <option value="service11" data-category="cos">Payslip</option>
                                    <option value="service12" data-category="cos">Contract of Service</option>
                                    <option value="service9" data-category="cos,permanent">Others</option>
                                </select>
                                <label for="serviceAvailed__IDX__">Service Availed <span
                                        class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please select a service.</div>
                            </div>
                        </div>
                        <!-- ---------- SERVICE PURPOSE ---------- -->
                        <div class="col-12 py-3">
                            <div class="form-floating">

                                <textarea class="form-control" name="entries[__IDX__][service_purpose]"
                                    id="servicePurpose__IDX__" placeholder="Please state your purpose" maxlength="500"
                                    style="height: 140px;" required></textarea>

                                <label for="servicePurpose__IDX__">
                                    Purpose / Request Details <span class="required-mark">*</span>
                                </label>

                                <div class="invalid-feedback">
                                    Please specify the purpose of your request.
                                </div>

                                <div class="form-text">
                                    Please provide the reason why you need this HR service.
                                </div>

                            </div>
                        </div>

                        <!-- ================================================= -->
                        <!-- SERVICE-SPECIFIC SECTIONS (shown/hidden by JS)     -->
                        <!-- ================================================= -->

                        <!-- ---------- SERVICE 5: Certificate of Employment ---------- -->
                        <div class="dynamic-section" id="section-service5__IDX__" data-service="service5">
                            <!-- COS Employee fields -->
                            <div class="dynamic-section" id="section-cos__IDX__" data-employee-category="cos">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bi bi-briefcase me-2"></i>Employment History <span
                                            class="required-mark">*</span>
                                    </h6>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger d-none"
                                            id="deleteAllEmployment__IDX__">
                                            <i class="bi bi-trash3"></i> Delete All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            id="addEmployment__IDX__">
                                            <i class="bi bi-plus-circle"></i> Add Employment Record
                                        </button>
                                    </div>
                                </div>

                                <!-- Employment cards are appended here dynamically, cloned from
                                     #employmentCardTemplate__IDX__. Starts empty by design. -->
                                <div id="employmentContainer__IDX__"></div>

                                <div id="noEmploymentRecords__IDX__"
                                    class="text-center text-muted py-4 border rounded-3 bg-light">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    No records found. Click "Add Employment Record" to add one.
                                </div>

                                <div class="text-danger small mt-2" id="employmentError__IDX__" style="display:none;">
                                    <i class="bi bi-exclamation-circle me-1"></i>Please add at least one employment
                                    record.
                                </div>

                                <!-- Inert template used to clone new employment cards from.
                                     Content inside <template> is never rendered or submitted. -->
                                <template id="employmentCardTemplate__IDX__">
                                    <div class="employment-card card border-0 shadow-sm mb-3">

                                        <div
                                            class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <strong class="employment-card-number">#1</strong>

                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger removeEmployment">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <div class="card-body">

                                            <div class="row g-3">

                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control"
                                                            name="entries[__IDX__][period_of_service][]"
                                                            placeholder="Period of Service" data-required-group="cos">

                                                        <label>
                                                            Period of Service
                                                            <span class="required-mark">*</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control"
                                                            name="entries[__IDX__][position][]" placeholder="Position"
                                                            data-required-group="cos">

                                                        <label>
                                                            Position
                                                            <span class="required-mark">*</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-floating">

                                                        <select class="form-select"
                                                            name="entries[__IDX__][office_division][]"
                                                            data-required-group="cos">

                                                            <option value="" selected disabled>Select Office</option>
                                                            <option value="Occidental Mindoro">Occidental Mindoro
                                                            </option>
                                                            <option value="Oriental Mindoro">Oriental Mindoro</option>
                                                            <option value="Marinduque">Marinduque</option>
                                                            <option value="Romblon">Romblon</option>
                                                            <option value="Palawan">Palawan</option>
                                                            <option value="IMSD">IMSD</option>
                                                            <option value="TSSD">TSSD</option>
                                                            <option value="MALSU">MALSU</option>
                                                            <option value="ORD">ORD</option>

                                                        </select>

                                                        <label>
                                                            Office / Division
                                                            <span class="required-mark">*</span>
                                                        </label>

                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                </template>

                            </div>
                        </div>

                        <!-- ---------- SERVICE 6: Certification ---------- -->
                        <div class="dynamic-section" id="section-service6__IDX__" data-service="service6">
                            <hr class="section-divider">
                            <p class="section-label"><i class="bi bi-patch-check"></i>Certification</p>

                            <label class="form-label fw-semibold mb-2">Certification Needed <span
                                    class="required-mark">*</span>
                                <span class="text-muted fw-normal">(select at least one)</span></label>
                            <div class="row g-3" id="certificationChecks__IDX__">
                                <div class="col-md-6">
                                    <div class="choice-card">
                                        <input class="form-check-input mt-0" type="checkbox"
                                            name="entries[__IDX__][certification][]" id="certNoPending__IDX__"
                                            value="No Pending Case">
                                        <label for="certNoPending__IDX__">No Pending Case</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="choice-card">
                                        <input class="form-check-input mt-0" type="checkbox"
                                            name="entries[__IDX__][certification][]" id="certLastSalary__IDX__"
                                            value="Last Salary Received">
                                        <label for="certLastSalary__IDX__">Last Salary Received</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="choice-card">
                                        <input class="form-check-input mt-0" type="checkbox"
                                            name="entries[__IDX__][certification][]" id="certLeaveCredits__IDX__"
                                            value="Leave Credits">
                                        <label for="certLeaveCredits__IDX__">Leave Credits</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="choice-card">
                                        <input class="form-check-input mt-0" type="checkbox"
                                            name="entries[__IDX__][certification][]" id="certLeaveWithoutPay__IDX__"
                                            value="Leave Without Pay (LWOP)">
                                        <label for="certLeaveWithoutPay__IDX__">Leave Without Pay (LWOP)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="choice-card">
                                        <input class="form-check-input mt-0" type="checkbox"
                                            name="entries[__IDX__][certification][]" id="certLeaveWithoutPayno__IDX__"
                                            value="No Leave Without Pay (NO LWOP)">
                                        <label for="certLeaveWithoutPayno__IDX__">No Leave Without Pay (NO LWOP)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-danger small mt-2" id="certificationError__IDX__" style="display:none;">
                                <i class="bi bi-exclamation-circle me-1"></i>Please select at least one certification.
                            </div>
                        </div>

                        <!-- ---------- SERVICE 7: IPCR ---------- -->
                        <div class="dynamic-section" id="section-service7__IDX__" data-service="service7">
                            <hr class="section-divider">
                            <p class="section-label">
                                <i class="bi bi-bar-chart-line"></i> IPCR
                            </p>

                            <div class="row g-3">

                                <!-- SEMESTER -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="entries[__IDX__][ipcr_semester]"
                                            id="ipcrSemester__IDX__" data-required-group="service7">
                                            <option value="" selected disabled>Select Semester</option>
                                            <option value="1st Semester">1st Semester (January - June)</option>
                                            <option value="2nd Semester">2nd Semester (July - December)</option>
                                        </select>
                                        <label for="ipcrSemester__IDX__">Semester <span
                                                class="required-mark">*</span></label>
                                    </div>
                                </div>

                                <!-- YEAR -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="entries[__IDX__][ipcr_year]"
                                            id="ipcrYear__IDX__" data-required-group="service7">
                                            <option value="" selected disabled>Select Year</option>
                                            <?php
                                            for ($year = date('Y'); $year >= 2000; $year--) {
                                                echo "<option value='{$year}'>{$year}</option>";
                                            }
                                            ?>
                                        </select>
                                        <label for="ipcrYear__IDX__">Year <span class="required-mark">*</span></label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ---------- SERVICE 8: 201 Documents ---------- -->
                        <div class="dynamic-section" id="section-service8__IDX__" data-service="service8">
                            <hr class="section-divider">
                            <p class="section-label"><i class="bi bi-folder2-open"></i>201 Documents</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">
                                        Select Document/s Needed <span class="required-mark">*</span>
                                    </label>

                                    <div class="document-tag-group" data-required-group="service8">
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Appointment (CSC33)">
                                            <span>Appointment (CSC33)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Assumption of Duty">
                                            <span>Assumption of Duty</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Oath of Office">
                                            <span>Oath of Office</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Personal Data Sheet (CSC Form 212)">
                                            <span>Personal Data Sheet (CSC Form 212)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Position Description Form">
                                            <span>Position Description Form</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Certificate of Eligibilities/License">
                                            <span>Certificate of Eligibilities/License</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Designation Orders (If Applicable)">
                                            <span>Designation Orders (If Applicable)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Statement of Assets, Liabilities and Net Worth (SALN)">
                                            <span>Statement of Assets, Liabilities and Net Worth (SALN)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Notices of Salary Adjustments/Step Increments">
                                            <span>Notices of Salary Adjustments/Step Increments</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Medical Certificate (CSC Form 211)">
                                            <span>Medical Certificate (CSC Form 211)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Psychiatric Test/Drugs Test (If Applicable)">
                                            <span>Psychiatric Test/Drugs Test (If Applicable)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="NBI Clearance">
                                            <span>NBI Clearance</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="School Diploma and Transcript of Records">
                                            <span>School Diploma and Transcript of Records</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Marriage Contract/Certificate">
                                            <span>Marriage Contract/Certificate</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Certificate of Leave Balances (For Transferees)">
                                            <span>Certificate of Leave Balances (For Transferees)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Clearance from Property and Accountabilities (For Transferees)">
                                            <span>Clearance from Property and Accountabilities (For Transferees)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Commendations/Awards or Copies of Disciplinary Actions (If Any)">
                                            <span>Commendations/Awards or Copies of Disciplinary Actions (If Any)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Contract of Service (If Applicable)">
                                            <span>Contract of Service (If Applicable)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Individual Performance Commitment and Review (IPCR)">
                                            <span>Individual Performance Commitment and Review (IPCR)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="BIR (2316)">
                                            <span>BIR (2316)</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Ombudsman">
                                            <span>Ombudsman</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Service Record">
                                            <span>Service Record</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Office Order as OIC">
                                            <span>Office Order as OIC</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Certificate of Trainings">
                                            <span>Certificate of Trainings</span>
                                        </label>
                                        <label class="document-tag">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Certification">
                                            <span>Certification</span>
                                        </label>
                                        <label class="document-tag document-tag-other"
                                            data-other-toggle="specify201__IDX__">
                                            <input type="checkbox" name="entries[__IDX__][documents_201][]"
                                                value="Other Files" data-other-checkbox>
                                            <span>Other Files</span>
                                        </label>
                                    </div>

                                    <div class="invalid-feedback d-block" id="documents201Error__IDX__"
                                        style="display:none;">
                                        Please select at least one document.
                                    </div>
                                </div>

                                <div class="col-12" id="specify201Wrapper__IDX__" style="display:none;">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="specify201__IDX__"
                                            name="entries[__IDX__][specify_201]" placeholder="Please specify"
                                            maxlength="255" style="height: 100px;"></textarea>

                                        <label for="specify201__IDX__">
                                            Please Specify Other Document/s
                                        </label>

                                        <div class="invalid-feedback">
                                            Only letters, numbers, spaces, commas (,), periods (.), hyphens (-),
                                            parentheses ( ), and slashes (/) are allowed.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ---------- SERVICE 9: Others ---------- -->
                        <div class="dynamic-section" id="section-service9__IDX__" data-service="service9">
                            <hr class="section-divider">
                            <p class="section-label">
                                <i class="bi bi-chat-square-text"></i> Others
                            </p>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="otherRequest__IDX__"
                                            name="entries[__IDX__][other_request]" placeholder="Please specify"
                                            data-required-group="service9" maxlength="255"
                                            style="height: 140px;"></textarea>

                                        <label for="otherRequest__IDX__">
                                            Please Specify <span class="required-mark">*</span>
                                        </label>

                                        <div class="invalid-feedback">
                                            Please specify your request.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ---------- SERVICE 10: Performance Evaluation ---------- -->
                        <div class="dynamic-section" id="section-service10__IDX__" data-service="service10">
                            <hr class="section-divider">
                            <p class="section-label">
                                <i class="bi bi-chart-bar"></i> Performance Evaluation
                            </p>

                            <div class="row g-3">

                                <!-- SEMESTER -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select"
                                            name="entries[__IDX__][performance_evaluation_semester]"
                                            id="performanceEvaluationSemester__IDX__" data-required-group="service10">
                                            <option value="" selected disabled>Select Semester</option>
                                            <option value="1st Semester">1st Semester (January - June)</option>
                                            <option value="2nd Semester">2nd Semester (July - December)</option>
                                        </select>
                                        <label for="performanceEvaluationSemester__IDX__">Semester <span
                                                class="required-mark">*</span></label>
                                    </div>
                                </div>

                                <!-- YEAR -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="entries[__IDX__][performance_evaluation_year]"
                                            id="performanceEvaluationYear__IDX__" data-required-group="service10">
                                            <option value="" selected disabled>Select Year</option>
                                            <?php
                                            for ($year = date('Y'); $year >= 2000; $year--) {
                                                echo "<option value='{$year}'>{$year}</option>";
                                            }
                                            ?>
                                        </select>
                                        <label for="performanceEvaluationYear__IDX__">Year <span
                                                class="required-mark">*</span></label>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <!-- ---------- SERVICE 12: Contract of Service (COS only) ---------- -->
                        <div class="dynamic-section" id="section-service12__IDX__" data-service="service12">
                            <hr class="section-divider">
                            <p class="section-label"><i class="bi bi-file-earmark-ruled"></i>Contract of Service</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="entries[__IDX__][contract_of_service_year]"
                                            id="contractYear__IDX__" data-required-group="service12">
                                            <option value="" selected disabled>Select Year</option>
                                            <?php for ($year = date('Y'); $year >= 2000; $year--): ?>
                                                <option value="<?= $year ?>"><?= $year ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <label for="contractYear__IDX__">Contract Year <span
                                                class="required-mark">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </template>

        </div>


        <p class="footer-note">DOLE MIMAROPA ePortal &middot; Human Resource Unit &middot; For assistance, contact your
            HR focal person.</p>

    </div>

    <!-- ===================== CONFIRMATION MODAL ===================== -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel"><i class="bi bi-clipboard2-check me-2"></i>Confirm
                        Your Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Please review your details before submitting. Once submitted, your
                        request will be forwarded to the HR Unit.</p>
                    <dl id="summaryList" class="mb-0"></dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dole-outline" data-bs-dismiss="modal">
                        <i class="bi bi-pencil me-1"></i>Go Back &amp; Edit
                    </button>
                    <button type="button" class="btn btn-dole-primary" id="confirmSubmitBtn">
                        <i class="bi bi-check2-circle me-1"></i>Confirm &amp; Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== SUCCESS MODAL ===================== -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <i class="bi bi-check-circle-fill" style="font-size: 3.5rem; color: var(--dole-blue);"></i>
                    <h4 class="fw-bold mt-3 mb-2">Request Submitted</h4>
                    <h3 class="fw-bold mt-3 mb-2" style="color:#10058C;">
                        <?= esc(session()->getFlashdata('reference_no')); ?>
                    </h3>

                    <p class="text-muted mb-4">Your HR request has been received. <br> Keep your reference number for
                        tracking your request.</p>
                    <button type="button" class="btn btn-dole-primary px-4" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

</main>


<!-- Bootstrap 5 Bundle (includes Popper) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script>
    (function () {
        'use strict';

        /* =========================================================
           ELEMENT REFERENCES (shared, not per-entry)
        ========================================================= */
        const form = document.getElementById('hrRequestForm');
        const entriesContainer = document.getElementById('entriesContainer');
        const entryTemplate = document.getElementById('entryTemplate');
        const addEntryBtn = document.getElementById('addEntryBtn');
        const selectAllEntries = document.getElementById('selectAllEntries');
        const deleteSelectedEntriesBtn = document.getElementById('deleteSelectedEntriesBtn');
        const fullNameField = document.getElementById('fullName');
        const contactNumberField = document.getElementById('contactNumber');
        const emailAddressField = document.getElementById('emailAddress');
        const employeeCategoryError = document.getElementById('employeeCategoryError');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmModalEl = document.getElementById('confirmModal');
        const confirmModal = new bootstrap.Modal(confirmModalEl);
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const summaryList = document.getElementById('summaryList');
        const formAlert = document.getElementById('formAlert');
        const formAlertText = document.getElementById('formAlertText');
        const dateRequestedDisplay = document.getElementById('dateRequestedDisplay');
        const dateRequestedHidden = document.getElementById('dateRequestedHidden');
        const draftRestoredAlert = document.getElementById('draftRestoredAlert');
        const clearDraftBtn = document.getElementById('clearDraftBtn');


        const allowedPattern = /^[A-Za-z0-9\s.,()\/-]*$/;
        let entryCount = 0;
        let restoringDraft = false; // guards against saving while we are restoring

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
           SMALL SCOPED-QUERY HELPERS
           Every lookup below takes an .entry-block element and finds
           fields by id-PREFIX, so it works no matter what index that
           entry ended up with.
        ========================================================= */
        function entries() {
            return Array.from(entriesContainer.querySelectorAll('.entry-block'));
        }
        function byPrefix(entryEl, prefix) {
            return entryEl.querySelector('[id^="' + prefix + '"]');
        }
        function serviceSelectOf(entryEl) { return byPrefix(entryEl, 'serviceAvailed'); }
        function servicePurposeOf(entryEl) { return byPrefix(entryEl, 'servicePurpose'); }
        function empCategoryErrorOf(entryEl) { return byPrefix(entryEl, 'empCategoryError'); }
        function employmentErrorOf(entryEl) { return byPrefix(entryEl, 'employmentError'); }
        function certificationErrorOf(entryEl) { return byPrefix(entryEl, 'certificationError'); }
        function employmentContainerOf(entryEl) { return byPrefix(entryEl, 'employmentContainer'); }
        function employmentTemplateOf(entryEl) { return byPrefix(entryEl, 'employmentCardTemplate'); }
        function noEmploymentRecordsOf(entryEl) { return byPrefix(entryEl, 'noEmploymentRecords'); }
        function deleteAllEmploymentBtnOf(entryEl) { return byPrefix(entryEl, 'deleteAllEmployment'); }
        function addEmploymentBtnOf(entryEl) { return byPrefix(entryEl, 'addEmployment'); }
        function otherRequestOf(entryEl) { return byPrefix(entryEl, 'otherRequest'); }
        function specify201Of(entryEl) { return entryEl.querySelector('textarea[id^="specify201"]'); }
        function serviceSectionsOf(entryEl) { return entryEl.querySelectorAll('.dynamic-section[data-service]'); }
        function employeeCategorySectionsOf(entryEl) { return entryEl.querySelectorAll('.dynamic-section[data-employee-category]'); }
        function employeeCategoryRadiosOf(entryEl) { return entryEl.querySelectorAll('input[id^="empCategory"]'); }
        function certificationCheckboxesOf(entryEl) { return entryEl.querySelectorAll('input[type="checkbox"][name*="[certification]"]'); }
        function checkedEmployeeCategoryOf(entryEl) { return entryEl.querySelector('input[id^="empCategory"]:checked'); }

        /* =========================================================
           DRAFT PERSISTENCE (localStorage) — keeps user-entered data
           across page reloads/refreshes until the form is submitted
           or the user clears it manually. Now stores an ARRAY of
           entries.
        ========================================================= */
        const DRAFT_KEY = 'doleHrRequestFormDraft';

        function collectEntryData(entryEl) {
            const employment = [];
            entryEl.querySelectorAll('.employment-card').forEach(function (card) {
                employment.push({
                    period: card.querySelector('input[name*="[period_of_service]"]').value,
                    position: card.querySelector('input[name*="[position]"]').value,
                    office: card.querySelector('select[name*="[office_division]"]').value
                });
            });

            const category = checkedEmployeeCategoryOf(entryEl);
            const otherRequest = otherRequestOf(entryEl);
            const specify201 = specify201Of(entryEl);

            return {
                service_availed: serviceSelectOf(entryEl).value,
                service_purpose: servicePurposeOf(entryEl).value,
                employment: employment,
                certification: Array.from(certificationCheckboxesOf(entryEl))
                    .filter(function (cb) { return cb.checked; })
                    .map(function (cb) { return cb.value; }),
                contract_of_service_year: byPrefix(entryEl, 'contractYear') ? byPrefix(entryEl, 'contractYear').value : '',
                ipcr_from_month: byPrefix(entryEl, 'ipcrFromMonth') ? byPrefix(entryEl, 'ipcrFromMonth').value : '',
                ipcr_from_year: byPrefix(entryEl, 'ipcrFromYear') ? byPrefix(entryEl, 'ipcrFromYear').value : '',
                ipcr_to_month: byPrefix(entryEl, 'ipcrToMonth') ? byPrefix(entryEl, 'ipcrToMonth').value : '',
                ipcr_to_year: byPrefix(entryEl, 'ipcrToYear') ? byPrefix(entryEl, 'ipcrToYear').value : '',
                ipcr_semester: byPrefix(entryEl, 'ipcrSemester') ? byPrefix(entryEl, 'ipcrSemester').value : '',
                ipcr_year: byPrefix(entryEl, 'ipcrYear') ? byPrefix(entryEl, 'ipcrYear').value : '',
                performance_evaluation_semester: byPrefix(entryEl, 'performanceEvaluationSemester') ? byPrefix(entryEl, 'performanceEvaluationSemester').value : '',
                performance_evaluation_year: byPrefix(entryEl, 'performanceEvaluationYear') ? byPrefix(entryEl, 'performanceEvaluationYear').value : '',
                specify_201: specify201 ? specify201.value : '',
                other_request: otherRequest ? otherRequest.value : ''
            };
        }

        function collectDraftData() {
            return {
                fullname: fullNameField.value,
                contact_number: contactNumberField.value,
                email_address: emailAddressField.value,
                employee_category: getGlobalCategory(),
                entries: entries().map(collectEntryData)
            };
        }

        function saveDraft() {
            if (restoringDraft) return;
            try {
                localStorage.setItem(DRAFT_KEY, JSON.stringify(collectDraftData()));
            } catch (e) {
                // localStorage unavailable (private browsing, quota, etc.) — fail silently
            }
        }

        function clearDraft() {
            try {
                localStorage.removeItem(DRAFT_KEY);
            } catch (e) { }
        }

        function entryHasContent(data) {
            return !!(data.service_availed || data.service_purpose ||
                (data.employment && data.employment.some(function (r) { return r.period || r.position || r.office; })) ||
                (data.certification && data.certification.length) ||
                data.specify_201 || data.other_request);
        }

        function fillEntryFromData(entryEl, data) {
            servicePurposeOf(entryEl).value = data.service_purpose || '';

            const serviceSelect = serviceSelectOf(entryEl);
            if (data.service_availed) {
                serviceSelect.value = data.service_availed;
                serviceSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }

            const contractYear = byPrefix(entryEl, 'contractYear');
            if (contractYear && data.contract_of_service_year) contractYear.value = data.contract_of_service_year;

            const addEmploymentBtn = addEmploymentBtnOf(entryEl);
            if (data.employment && data.employment.length > 0 && addEmploymentBtn) {
                while (entryEl.querySelectorAll('.employment-card').length < data.employment.length) {
                    addEmploymentBtn.click();
                }
                const cards = entryEl.querySelectorAll('.employment-card');
                data.employment.forEach(function (rec, index) {
                    if (!cards[index]) return;
                    cards[index].querySelector('input[name*="[period_of_service]"]').value = rec.period || '';
                    cards[index].querySelector('input[name*="[position]"]').value = rec.position || '';
                    cards[index].querySelector('select[name*="[office_division]"]').value = rec.office || '';
                });
            }

            if (data.certification && data.certification.length) {
                certificationCheckboxesOf(entryEl).forEach(function (cb) {
                    cb.checked = data.certification.indexOf(cb.value) !== -1;
                });
            }

            if (data.ipcr_from_month) byPrefix(entryEl, 'ipcrFromMonth').value = data.ipcr_from_month;
            if (data.ipcr_from_year) byPrefix(entryEl, 'ipcrFromYear').value = data.ipcr_from_year;
            if (data.ipcr_to_month) byPrefix(entryEl, 'ipcrToMonth').value = data.ipcr_to_month;
            if (data.ipcr_to_year) byPrefix(entryEl, 'ipcrToYear').value = data.ipcr_to_year;
            // Performance Evaluation restoration
            if (data.performance_evaluation_semester) byPrefix(entryEl, 'performanceEvaluationSemester').value = data.performance_evaluation_semester;
            if (data.performance_evaluation_year) byPrefix(entryEl, 'performanceEvaluationYear').value = data.performance_evaluation_year;

            const otherRequest = otherRequestOf(entryEl);
            const specify201 = specify201Of(entryEl);
            if (specify201 && data.specify_201) specify201.value = data.specify_201;
            if (otherRequest && data.other_request) otherRequest.value = data.other_request;
        }

        function restoreDraft() {
            let data;
            try {
                const raw = localStorage.getItem(DRAFT_KEY);
                if (!raw) return;
                data = JSON.parse(raw);
            } catch (e) {
                return;
            }

            if (!data) return;
            const savedEntries = Array.isArray(data.entries) ? data.entries : [];
            const meaningfulEntries = savedEntries.filter(entryHasContent);
            const hasCommonContent = !!(data.fullname || data.contact_number || data.email_address);
            if (meaningfulEntries.length === 0 && !hasCommonContent) return;

            restoringDraft = true;

            fullNameField.value = data.fullname || '';
            contactNumberField.value = data.contact_number || '';
            emailAddressField.value = data.email_address || '';

            if (data.employee_category) {
                const radio = document.querySelector('input[name="employee_category"][value="' + data.employee_category + '"]');
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            // Ensure we have exactly as many entry blocks as saved entries.
            while (entries().length < savedEntries.length) {
                addEntry();
            }

            const entryEls = entries();
            savedEntries.forEach(function (entryData, i) {
                if (entryEls[i]) fillEntryFromData(entryEls[i], entryData);
            });

            restoringDraft = false;

            draftRestoredAlert.classList.remove('d-none');
        }

        if (clearDraftBtn) {
            clearDraftBtn.addEventListener('click', function () {
                clearDraft();
                resetForm();
                draftRestoredAlert.classList.add('d-none');
            });
        }

        // Autosave on every field change/input across the whole form.
        // Delegated on the form, so dynamically added entries are covered automatically.
        form.addEventListener('input', saveDraft);
        form.addEventListener('change', saveDraft);

        /* =========================================================
           DATE REQUESTED: SET ONCE, ALWAYS TODAY'S DATE
        ========================================================= */
        function getTodayDateString() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function formatDisplayDate(isoDateStr) {
            const [y, m, d] = isoDateStr.split('-');
            const dateObj = new Date(Number(y), Number(m) - 1, Number(d));
            return dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        }

        function setDateRequested() {
            const todayStr = getTodayDateString();
            dateRequestedHidden.value = todayStr;
            dateRequestedDisplay.textContent = formatDisplayDate(todayStr);
        }


        /* =========================================================
           SHOW/HIDE DYNAMIC SECTIONS (scoped to one entry)
           *** KEY FIX: Always hide sections properly ***
        ========================================================= */
        function hideAllServiceSections(entryEl) {
            serviceSectionsOf(entryEl).forEach(function (section) {
                section.classList.remove('active');
                section.style.display = 'none';  // ✅ EXPLICIT HIDE
                toggleRequiredGroup(section, false);
            });
        }

        function hideAllEmployeeCategorySections(entryEl) {
            employeeCategorySectionsOf(entryEl).forEach(function (section) {
                section.classList.remove('active');
                section.style.display = 'none';  // ✅ EXPLICIT HIDE
                toggleRequiredGroup(section, false);
            });
        }

        function toggleRequiredGroup(container, isRequired) {
            const fields = container.querySelectorAll('[data-required-group]');
            fields.forEach(function (field) {
                if (field.type === 'radio' || field.type === 'checkbox') {
                    return;
                }
                if (isRequired) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                    // Don't wipe the value while a draft restore is in progress
                    if (!restoringDraft) {
                        field.value = '';
                    }
                    field.classList.remove('is-valid', 'is-invalid');
                }
            });
        }

        function resetEmployeeCategorySelection(entryEl) {
            employeeCategoryRadiosOf(entryEl).forEach(function (radio) {
                radio.checked = false;
            });
            const empCategoryError = empCategoryErrorOf(entryEl);
            const employmentError = employmentErrorOf(entryEl);
            if (empCategoryError) empCategoryError.style.setProperty('display', 'none', 'important');
            if (employmentError) employmentError.style.display = 'none';
        }

        /* =========================================================
           CUSTOM VALIDATION (scoped to one entry)
        ========================================================= */
        function isCertificationValid(entryEl) {
            if (serviceSelectOf(entryEl).value !== 'service6') return true;
            return Array.from(certificationCheckboxesOf(entryEl)).some(function (cb) { return cb.checked; });
        }
        function is201DocumentsValid(entryEl) {
            if (serviceSelectOf(entryEl).value !== 'service8') return true;

            return Array.from(
                entryEl.querySelectorAll('input[name*="[documents_201][]"]')
            ).some(function (cb) {
                return cb.checked;
            });
        }

        function isEmployeeCategoryValid(entryEl) {
            if (serviceSelectOf(entryEl).value !== 'service5') return true;
            return Array.from(employeeCategoryRadiosOf(entryEl)).some(function (r) { return r.checked; });
        }

        function isEmploymentValid(entryEl) {
            if (serviceSelectOf(entryEl).value !== 'service5') return true;
            if (getGlobalCategory() !== 'cos') return true;
            return entryEl.querySelectorAll('.employment-card').length > 0;
        }

        /* =========================================================
           HTML ESCAPE HELPER (for safely inserting user text into innerHTML)
        ========================================================= */
        function escapeHTML(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        /* =========================================================
           EMPLOYMENT HISTORY: numbering / empty-state (scoped)
        ========================================================= */
        function updateEmploymentNumbers(entryEl) {
            const cards = entryEl.querySelectorAll('.employment-card');

            cards.forEach(function (card, index) {
                const numberEl = card.querySelector('.employment-card-number');
                if (numberEl) numberEl.textContent = '#' + (index + 1);
            });

            const hasRecords = cards.length > 0;
            const noEmploymentRecords = noEmploymentRecordsOf(entryEl);
            const deleteAllEmploymentBtn = deleteAllEmploymentBtnOf(entryEl);
            const employmentError = employmentErrorOf(entryEl);

            if (noEmploymentRecords) noEmploymentRecords.classList.toggle('d-none', hasRecords);
            if (deleteAllEmploymentBtn) deleteAllEmploymentBtn.classList.toggle('d-none', !hasRecords);
            if (employmentError && isEmploymentValid(entryEl)) employmentError.style.display = 'none';
        }

        function applyEmploymentCardRequired(entryEl, card) {
            const cosSection = entryEl.querySelector('[id^="section-cos"]');
            const isRequired = cosSection && cosSection.classList.contains('active');

            card.querySelectorAll('[data-required-group="cos"]').forEach(function (field) {
                if (isRequired) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            });
        }

        function addEmploymentCard(entryEl) {
            const employmentTemplate = employmentTemplateOf(entryEl);
            const employmentContainer = employmentContainerOf(entryEl);
            if (!employmentTemplate || !employmentContainer) return;

            const fragment = employmentTemplate.content.cloneNode(true);
            const card = fragment.querySelector('.employment-card');

            employmentContainer.appendChild(fragment);

            applyEmploymentCardRequired(entryEl, card);
            updateEmploymentNumbers(entryEl);

            if (!restoringDraft) saveDraft();
        }

        /* =========================================================
           CHARACTER RESTRICTION (Service 8 / Service 9 textareas)
        ========================================================= */
        function applyCharRestriction(field) {
            if (!allowedPattern.test(field.value)) {
                field.setCustomValidity(
                    'Only letters, numbers, spaces, commas, periods, hyphens, parentheses, and slashes are allowed.'
                );
                field.classList.add('is-invalid');
            } else {
                field.setCustomValidity('');
                field.classList.remove('is-invalid');
            }
        }

        function getGlobalCategory() {
            const checked = document.querySelector('input[name="employee_category"]:checked');
            return checked ? checked.value : '';
        }

        function filterServiceOptionsForCategory(category) {
            entries().forEach(function (entryEl) {
                const select = serviceSelectOf(entryEl);
                Array.from(select.options).forEach(function (opt) {
                    if (!opt.value) return; // keep the placeholder option
                    const cats = (opt.dataset.category || '').split(',');
                    const allowed = !!category && cats.indexOf(category) !== -1;
                    opt.hidden = !allowed;
                    opt.disabled = !allowed;
                });
                if (select.value && select.selectedOptions[0] && select.selectedOptions[0].disabled) {
                    select.value = '';
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }

        /* =========================================================
           Employee Category Changes
        ========================================================= */

        function updateCosSectionVisibility(entryEl) {
            const cosSection = entryEl.querySelector('[id^="section-cos"]');
            if (!cosSection) return;
            const shouldShow = serviceSelectOf(entryEl).value === 'service5' && getGlobalCategory() === 'cos';
            cosSection.classList.toggle('active', shouldShow);
            cosSection.style.display = shouldShow ? 'block' : 'none';  // ✅ EXPLICIT CONTROL
            toggleRequiredGroup(cosSection, shouldShow);
            if (!shouldShow) {
                const employmentError = employmentErrorOf(entryEl);
                if (employmentError) employmentError.style.display = 'none';
            }
        }

        document.querySelectorAll('input[name="employee_category"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                employeeCategoryError.style.setProperty('display', 'none', 'important');
                filterServiceOptionsForCategory(getGlobalCategory());

                // Explicitly hide ALL dynamic sections when category changes
                document.querySelectorAll('.dynamic-section').forEach(function (section) {
                    section.style.display = 'none';
                    section.classList.remove('active');
                });

                entries().forEach(function (entryEl) {
                    updateCosSectionVisibility(entryEl);
                });
                if (!restoringDraft) saveDraft();
            });
        });

        /* =========================================================
           ADD / REMOVE ENTRIES
        ========================================================= */
        function initEntryInitialState(entryEl) {
            hideAllServiceSections(entryEl);
            updateCosSectionVisibility(entryEl);
            updateEmploymentNumbers(entryEl);
        }

        filterServiceOptionsForCategory(getGlobalCategory());

        function renumberEntries() {
            entries().forEach(function (entryEl, i) {
                const numEl = entryEl.querySelector('.entry-number');
                if (numEl) numEl.textContent = i + 1;
            });
            updateBulkDeleteUI();
        }

        function updateBulkDeleteUI() {
            const checked = entriesContainer.querySelectorAll('.entrySelectCheckbox:checked').length;
            deleteSelectedEntriesBtn.classList.toggle('d-none', checked === 0);
            if (checked > 0) {
                deleteSelectedEntriesBtn.innerHTML =
                    '<i class="bi bi-trash3 me-1"></i>Delete Selected (' + checked + ')';
            }

            const selectable = entriesContainer.querySelectorAll('.entrySelectCheckbox');
            selectAllEntries.checked = selectable.length > 0 && checked === selectable.length;
            selectAllEntries.indeterminate = checked > 0 && checked < selectable.length;
        }

        function addEntry() {
            const idx = entryCount++;
            const html = entryTemplate.innerHTML
                .split('__IDX__').join(idx)
                .split('__NUM__').join(idx + 1);

            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const entryEl = wrapper.firstElementChild;

            entriesContainer.appendChild(entryEl);

            if (idx > 0) {
                const removeBtn = entryEl.querySelector('.removeEntryBtn');
                if (removeBtn) removeBtn.classList.remove('d-none');
                const selectWrap = entryEl.querySelector('.entrySelectWrap');
                if (selectWrap) selectWrap.classList.remove('d-none');
            }

            // CRITICAL: Explicitly hide all dynamic sections in this entry
            entryEl.querySelectorAll('.dynamic-section').forEach(function (section) {
                section.style.display = 'none';
                section.classList.remove('active');
            });

            initEntryInitialState(entryEl);
            filterServiceOptionsForCategory(getGlobalCategory());
            renumberEntries();
            return entryEl;
        }

        addEntryBtn.addEventListener('click', function () {
            addEntry();
        });

        selectAllEntries.addEventListener('change', function () {
            entriesContainer.querySelectorAll('.entrySelectCheckbox').forEach(function (cb) {
                cb.checked = selectAllEntries.checked;
            });
            updateBulkDeleteUI();
        });

        deleteSelectedEntriesBtn.addEventListener('click', function () {
            const selected = Array.from(entriesContainer.querySelectorAll('.entrySelectCheckbox:checked'))
                .map(function (cb) { return cb.closest('.entry-block'); })
                .filter(Boolean);

            if (selected.length === 0) return;
            if (!window.confirm('Remove ' + selected.length + ' selected entr' +
                (selected.length === 1 ? 'y' : 'ies') + '? This cannot be undone.')) return;

            selected.forEach(function (entryEl) { entryEl.remove(); });
            renumberEntries();
            saveDraft();
        });

        /* =========================================================
           EVENT DELEGATION — handles all entries, including ones
           added after page load, with a single set of listeners.
        ========================================================= */
        form.addEventListener('change', function (e) {
            const target = e.target;
            const entryEl = target.closest('.entry-block');
            if (!entryEl) return;

            // Service Availed change
            if (target.matches('select[id^="serviceAvailed"]')) {
                hideAllServiceSections(entryEl);  // ✅ HIDE ALL FIRST
                formAlert.classList.add('d-none');

                const targetSection = entryEl.querySelector('[data-service="' + target.value + '"]');
                if (targetSection) {
                    targetSection.classList.add('active');
                    targetSection.style.display = 'block';  // ✅ THEN SHOW SELECTED
                    toggleRequiredGroup(targetSection, true);
                }
                updateCosSectionVisibility(entryEl);
                return;
            }

            // "Other Files" checkbox toggle (Service 8 / 201 Documents)
            if (target.matches('[data-other-checkbox]')) {
                const specifyWrapper = entryEl.querySelector('[id^="specify201Wrapper"]');
                const specifyField = specify201Of(entryEl);

                if (specifyWrapper) {
                    specifyWrapper.style.display = target.checked ? 'block' : 'none';
                }

                // Kapag na-uncheck, i-clear yung textarea para hindi masave yung
                // dating laman kung sakaling i-re-check pa ito ulit nang mali.
                if (!target.checked && specifyField) {
                    specifyField.value = '';
                    specifyField.classList.remove('is-invalid');
                }

                return;
            }

            // Employee category radio change
            if (target.matches('input[id^="empCategory"]')) {
                hideAllEmployeeCategorySections(entryEl);
                const empCategoryError = empCategoryErrorOf(entryEl);
                const employmentError = employmentErrorOf(entryEl);
                if (empCategoryError) empCategoryError.style.setProperty('display', 'none', 'important');
                if (employmentError) employmentError.style.display = 'none';

                const targetSection = entryEl.querySelector('[id^="section-' + target.value + '"][data-employee-category]');
                if (targetSection) {
                    targetSection.classList.add('active');
                    targetSection.style.display = 'block';  // ✅ EXPLICIT SHOW
                    toggleRequiredGroup(targetSection, true);
                }
                return;
            }

            // Certification checkbox change
            if (target.matches('input[type="checkbox"]') && target.name.indexOf('[certification]') !== -1) {
                if (isCertificationValid(entryEl)) {
                    const certificationError = certificationErrorOf(entryEl);
                    if (certificationError) certificationError.style.display = 'none';
                }
                return;
            }

            // Entry select checkbox (bulk delete)
            if (target.matches('.entrySelectCheckbox')) {
                updateBulkDeleteUI();
            }
        });

        form.addEventListener('input', function (e) {
            const target = e.target;

            if (target.matches('textarea[id^="otherRequest"]') || target.matches('textarea[id^="specify201"]')) {
                applyCharRestriction(target);
            }
        });

        form.addEventListener('click', function (e) {
            const target = e.target;

            // Remove an entire entry
            const removeEntryBtn = target.closest('.removeEntryBtn');
            if (removeEntryBtn) {
                const entryEl = removeEntryBtn.closest('.entry-block');
                if (entryEl) {
                    entryEl.remove();
                    renumberEntries();
                    saveDraft();
                }
                return;
            }

            // Add employment record
            const addEmploymentBtn = target.closest('[id^="addEmployment"]');
            if (addEmploymentBtn) {
                const entryEl = addEmploymentBtn.closest('.entry-block');
                if (entryEl) addEmploymentCard(entryEl);
                return;
            }

            // Remove a single employment record
            const removeEmploymentBtn = target.closest('.removeEmployment');
            if (removeEmploymentBtn) {
                const entryEl = removeEmploymentBtn.closest('.entry-block');
                removeEmploymentBtn.closest('.employment-card').remove();
                if (entryEl) {
                    updateEmploymentNumbers(entryEl);
                    saveDraft();
                }
                return;
            }

            // Delete all employment records
            const deleteAllBtn = target.closest('[id^="deleteAllEmployment"]');
            if (deleteAllBtn) {
                const entryEl = deleteAllBtn.closest('.entry-block');
                if (!entryEl) return;
                const employmentContainer = employmentContainerOf(entryEl);
                if (!employmentContainer || employmentContainer.querySelectorAll('.employment-card').length === 0) return;
                if (!window.confirm('Remove all employment records? This cannot be undone.')) return;

                employmentContainer.innerHTML = '';
                updateEmploymentNumbers(entryEl);
                saveDraft();
            }
        });

        /* =========================================================
           FORM SUBMISSION FLOW
        ========================================================= */
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            let isValid = form.checkValidity();
            let firstInvalidEntry = null;

            if (!getGlobalCategory()) {
                isValid = false;
                employeeCategoryError.style.setProperty('display', 'block', 'important');
            } else {
                employeeCategoryError.style.setProperty('display', 'none', 'important');
            }

            entries().forEach(function (entryEl) {
                if (!serviceSelectOf(entryEl).value) isValid = false;

                const employmentError = employmentErrorOf(entryEl);
                if (!isEmploymentValid(entryEl)) {
                    isValid = false;
                    if (employmentError) employmentError.style.display = 'block';
                    if (!firstInvalidEntry) firstInvalidEntry = entryEl;
                } else if (employmentError) {
                    employmentError.style.display = 'none';
                }

                const certificationError = certificationErrorOf(entryEl);
                if (!isCertificationValid(entryEl)) {
                    isValid = false;
                    if (certificationError) certificationError.style.display = 'block';
                    if (!firstInvalidEntry) firstInvalidEntry = entryEl;
                } else if (certificationError) {
                    certificationError.style.display = 'none';
                }
                // 201 Documents validation
                if (!is201DocumentsValid(entryEl)) {
                    isValid = false;

                    const documents201Error = byPrefix(entryEl, 'documents201Error');

                    if (documents201Error) {
                        documents201Error.style.display = 'block';
                    }

                    if (!firstInvalidEntry) {
                        firstInvalidEntry = entryEl;
                    }
                } else {
                    const documents201Error = byPrefix(entryEl, 'documents201Error');

                    if (documents201Error) {
                        documents201Error.style.display = 'none';
                    }
                }
            });

            form.classList.add('was-validated');

            if (!isValid) {
                formAlertText.textContent = 'Please complete all required fields before submitting.';
                formAlert.classList.remove('d-none');
                const firstInvalid = form.querySelector(':invalid, .is-invalid') || firstInvalidEntry;
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            formAlert.classList.add('d-none');
            buildSummary();
            confirmModal.show();
        });

        /* =========================================================
           BUILD CONFIRMATION MODAL SUMMARY
           Iterates every entry and renders it as its own subsection.
           (rows[2] === true means render that value as HTML)
        ========================================================= */
        function buildEntryRows(entryEl) {
            const serviceValue = serviceSelectOf(entryEl).value;
            const rows = [];

            rows.push(['Service Availed', serviceLabels[serviceValue] || '—']);
            rows.push(['Purpose / Request Details', servicePurposeOf(entryEl).value]);

            switch (serviceValue) {
                case 'service1':
                case 'service2':
                    // No additional service-specific fields beyond the common ones.
                    break;

                case 'service5': {
                    if (getGlobalCategory() === 'cos') {
                        const periods = entryEl.querySelectorAll('input[name*="[period_of_service]"]');
                        const positions = entryEl.querySelectorAll('input[name*="[position]"]');
                        const offices = entryEl.querySelectorAll('select[name*="[office_division]"]');

                        let tableHTML = '<table class="summary-table"><thead><tr>' +
                            '<th>#</th><th>Period of Service</th><th>Position</th><th>Office / Division</th>' +
                            '</tr></thead><tbody>';

                        periods.forEach((item, index) => {
                            tableHTML += '<tr><td>' + (index + 1) + '</td><td>' + escapeHTML(item.value) + '</td>' +
                                '<td>' + escapeHTML(positions[index].value) + '</td>' +
                                '<td>' + escapeHTML(offices[index].value) + '</td></tr>';
                        });

                        tableHTML += '</tbody></table>';
                        rows.push(['Employment History', tableHTML, true]);
                    }
                    break;
                }

                case 'service6': {
                    const checked = Array.from(certificationCheckboxesOf(entryEl))
                        .filter(function (cb) { return cb.checked; })
                        .map(function (cb) { return cb.value; });
                    rows.push(['Certification Needed', checked.join(', ')]);
                    break;
                }

                case 'service7': {
                    const semester = byPrefix(entryEl, 'ipcrSemester').value;
                    const year = byPrefix(entryEl, 'ipcrYear').value;
                    rows.push(['IPCR Period', semester && year ? semester + ' ' + year : '—']);
                    break;
                }

                case 'service8': {
                    const checkedDocs = Array.from(
                        entryEl.querySelectorAll('input[name*="[documents_201][]"]:checked')
                    ).map(function (cb) { return cb.value; });

                    const otherText = specify201Of(entryEl).value.trim();

                    // Replace generic "Other Files" label with the actual specified text, if provided
                    const displayDocs = checkedDocs.map(function (doc) {
                        return (doc === 'Other Files' && otherText) ? otherText : doc;
                    });

                    const listHtml = displayDocs.length
                        ? '<ul class="mb-0 ps-3">' + displayDocs.map(function (d) {
                            return '<li>' + d.replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</li>';
                        }).join('') + '</ul>'
                        : '—';

                    rows.push(['201 Documents', listHtml, true]);
                    break;
                }

                case 'service9':
                    rows.push(['Request', otherRequestOf(entryEl).value]);
                    break;

                case 'service10': {
                    const semester = byPrefix(entryEl, 'performanceEvaluationSemester').value;
                    const year = byPrefix(entryEl, 'performanceEvaluationYear').value;
                    rows.push(['Performance Evaluation Period', semester && year ? semester + ' ' + year : '—']);
                    break;
                }
                case 'service11':
                    // No additional fields beyond the common Service Purpose.
                    break;

                case 'service12':
                    rows.push(['Contract Year', byPrefix(entryEl, 'contractYear').value]);
                    break;

                default:
                    break;
            }

            return rows;
        }

        function buildSummary() {
            summaryList.innerHTML = '';

            const category = getGlobalCategory();

            [
                ['Name', fullNameField.value],
                ['Contact Number', contactNumberField.value],
                ['Email Address', emailAddressField.value],
                ['Employee Category', category === 'cos' ? 'COS Employee' : (category === 'permanent' ? 'Permanent Employee' : '—')],
                ['Date Requested', dateRequestedHidden.value]
            ].forEach(function (pair) {
                const dt = document.createElement('dt');
                dt.textContent = pair[0];
                const dd = document.createElement('dd');
                dd.textContent = pair[1] || '—';
                summaryList.appendChild(dt);
                summaryList.appendChild(dd);
            });

            const entryEls = entries();
            const multiple = entryEls.length > 1;

            entryEls.forEach(function (entryEl, i) {
                if (multiple) {
                    const heading = document.createElement('div');
                    heading.className = 'fw-bold mt-3 mb-1 summary-entry-heading';
                    heading.textContent = 'Request Entry #' + (i + 1);
                    summaryList.appendChild(heading);
                }

                buildEntryRows(entryEl).forEach(function (pair) {
                    const dt = document.createElement('dt');
                    dt.textContent = pair[0];
                    const dd = document.createElement('dd');

                    if (pair[2] === true) {
                        dd.innerHTML = pair[1] || '—';
                    } else {
                        dd.textContent = pair[1] || '—';
                    }

                    summaryList.appendChild(dt);
                    summaryList.appendChild(dd);
                });
            });
        }

        /* =========================================================
           CONFIRM & SUBMIT -> CLEAR DRAFT -> SHOW LOADING OVERLAY -> SUBMIT FORM
        ========================================================= */
        confirmSubmitBtn.addEventListener('click', function () {
            confirmModal.hide();
            confirmSubmitBtn.disabled = true;
            loadingOverlay.classList.remove('d-none');

            clearDraft();

            setTimeout(function () {
                form.submit();
            }, 150);
        });

        /* =========================================================
           CANCEL BUTTON
        ========================================================= */
        cancelBtn.addEventListener('click', function () {
            window.location.href = "<?= site_url('/') ?>";
        });

        /* =========================================================
           RESET FORM (also used by "Clear and start over")
        ========================================================= */
        function resetForm() {
            form.reset();
            form.classList.remove('was-validated');
            entriesContainer.innerHTML = '';
            entryCount = 0;
            addEntry();
            formAlert.classList.add('d-none');
            document.querySelectorAll('.form-control, .form-select').forEach(function (el) {
                el.classList.remove('is-valid', 'is-invalid');
            });
            setDateRequested();
            clearDraft();
        }

        /* =========================================================
           INITIALIZE ON PAGE LOAD
        ========================================================= */
        document.addEventListener('DOMContentLoaded', function () {
            setDateRequested();

            // CRITICAL: Hide all dynamic sections globally first
            document.querySelectorAll('.dynamic-section').forEach(function (section) {
                section.style.display = 'none';
                section.classList.remove('active');
            });

            addEntry();
            restoreDraft();
        });
    })();
</script>

<?php if (session()->getFlashdata('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Request was successfully saved server-side — make sure no stale
            // draft lingers for next time, then show the success modal.
            try {
                localStorage.removeItem('doleHrRequestFormDraft');
            } catch (e) { }

            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        });
    </script>
<?php endif; ?>