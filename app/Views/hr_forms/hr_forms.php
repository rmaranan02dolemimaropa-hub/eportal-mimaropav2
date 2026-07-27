<?= $this->include('layouts/headform') ?>

<main id="main-content">

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
                <div class="col-12 py-3">
                    <div class="form-floating">

                        <select class="form-select" name="service_availed" id="serviceAvailed" required>
                            <option value="" selected disabled>Select a service</option>
                            <option value="service1">Service Record</option>
                            <option value="service5">Certificate of Employment</option>
                            <option value="service2">Certificate with Compensation</option>
                            <option value="service6">Certification</option>
                            <option value="service7">IPCR</option>
                            <option value="service8">201 Documents</option>
                            <option value="service3">Loan Assistance</option>
                            <option value="service4">Inquiry of Leave Balance</option>
                            <option value="service9">Others</option>
                        </select>
                        <label for="serviceAvailed">Service Availed <span class="required-mark">*</span></label>
                        <div class="invalid-feedback">Please select a service.</div>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- SERVICE-SPECIFIC SECTIONS (shown/hidden by JS)     -->
                <!-- ================================================= -->



                <!-- ---------- SERVICE 5: Certificate of Employment ---------- -->
                <div class="dynamic-section" id="section-service5" data-service="service5">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-person-badge"></i>Certificate of Employment</p>

                    <label class="form-label fw-semibold mb-2">Employee Category <span
                            class="required-mark">*</span></label>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="radio" name="employee_category"
                                    id="empCategoryCOS" value="cos" data-required-group="service5">
                                <label for="empCategoryCOS">COS Employee</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="radio" name="employee_category"
                                    id="empCategoryPermanent" value="permanent" data-required-group="service5">
                                <label for="empCategoryPermanent">Permanent Employee</label>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block" id="empCategoryError" style="display:none !important;">Please
                        select an employee category.</div>

                    <!-- COS Employee fields -->
                    <div class="dynamic-section" id="section-cos" data-employee-category="cos">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="period_of_service"
                                        id="periodOfService" placeholder="Period of Service" data-required-group="cos">
                                    <label for="periodOfService">Period of Service <span
                                            class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please enter the period of service.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="position" id="cosPosition"
                                        placeholder="Position" data-required-group="cos">
                                    <label for="cosPosition">Position <span class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please enter your position.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="office_division" id="officeAssigned"
                                        data-required-group="cos">
                                        <option value="" selected disabled>Select Office / Division</option>
                                        <option value="Regional Office">Regional Office</option>
                                        <option value="Occidental Mindoro">Occidental Mindoro</option>
                                        <option value="Oriental Mindoro">Oriental Mindoro</option>
                                        <option value="Marinduque">Marinduque</option>
                                        <option value="Romblon">Romblon</option>
                                        <option value="Palawan">Palawan</option>
                                        <option value="IMSD">IMSD</option>
                                        <option value="TSSD">TSSD</option>
                                        <option value="MALSU">MALSU</option>
                                        <option value="ORD">ORD</option>
                                    </select>
                                    <label for="officeAssigned">Office / Division Assigned <span
                                            class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please select an office/division.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permanent Employee fields -->
                    <div class="dynamic-section" id="section-permanent" data-employee-category="permanent">
                        <!-- No additional fields required -->
                    </div>
                </div>

                <!-- ---------- SERVICE 6: Certification ---------- -->
                <div class="dynamic-section" id="section-service6" data-service="service6">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-patch-check"></i>Certification</p>

                    <label class="form-label fw-semibold mb-2">Certification Needed <span class="required-mark">*</span>
                        <span class="text-muted fw-normal">(select at least one)</span></label>
                    <div class="row g-3" id="certificationChecks">
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" name="certification[]"
                                    id="certNoPending" value="No Pending Case">
                                <label for="certNoPending">No Pending Case</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" name="certification[]"
                                    id="certLastSalary" value="Last Salary Received">
                                <label for="certLastSalary">Last Salary Received</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" name="certification[]"
                                    id="certLeaveCredits" value="Leave Credits">
                                <label for="certLeaveCredits">Leave Credits</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" name="certification[]"
                                    id="certLeaveWithoutPay" value="Leave Without Pay (LWOP)">
                                <label for="certLeaveWithoutPay">Leave Without Pay (LWOP)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" name="certification[]"
                                    id="certLeaveWithoutPayno" value="Leave Without Pay (NO LWOP)">
                                <label for="certLeaveWithoutPayno">Leave Without Pay (NO LWOP)</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-danger small mt-2" id="certificationError" style="display:none;">
                        <i class="bi bi-exclamation-circle me-1"></i>Please select at least one certification.
                    </div>
                </div>

                <!-- ---------- SERVICE 7: IPCR ---------- -->
                <!-- ---------- SERVICE 7: IPCR ---------- -->
                <div class="dynamic-section" id="section-service7" data-service="service7">
                    <hr class="section-divider">
                    <p class="section-label">
                        <i class="bi bi-bar-chart-line"></i> IPCR
                    </p>

                    <div class="row g-3">

                        <!-- FROM MONTH -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" name="ipcr_from_month" id="ipcrFromMonth"
                                    data-required-group="service7">
                                    <option value="" selected disabled>Select Month</option>
                                    <option>January</option>
                                    <option>February</option>
                                    <option>March</option>
                                    <option>April</option>
                                    <option>May</option>
                                    <option>June</option>
                                    <option>July</option>
                                    <option>August</option>
                                    <option>September</option>
                                    <option>October</option>
                                    <option>November</option>
                                    <option>December</option>
                                </select>
                                <label>From Month <span class="required-mark">*</span></label>
                            </div>
                        </div>

                        <!-- FROM YEAR -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" name="ipcr_from_year" id="ipcrFromYear"
                                    data-required-group="service7">
                                    <option value="" selected disabled>Select Year</option>
                                    <?php
                                    for ($year = date('Y'); $year >= 2000; $year--) {
                                        echo "<option value='{$year}'>{$year}</option>";
                                    }
                                    ?>
                                </select>
                                <label>From Year <span class="required-mark">*</span></label>
                            </div>
                        </div>

                        <!-- TO MONTH -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" name="ipcr_to_month" id="ipcrToMonth"
                                    data-required-group="service7">
                                    <option value="" selected disabled>Select Month</option>
                                    <option>January</option>
                                    <option>February</option>
                                    <option>March</option>
                                    <option>April</option>
                                    <option>May</option>
                                    <option>June</option>
                                    <option>July</option>
                                    <option>August</option>
                                    <option>September</option>
                                    <option>October</option>
                                    <option>November</option>
                                    <option>December</option>
                                </select>
                                <label>To Month <span class="required-mark">*</span></label>
                            </div>
                        </div>

                        <!-- TO YEAR -->
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" name="ipcr_to_year" id="ipcrToYear"
                                    data-required-group="service7">
                                    <option value="" selected disabled>Select Year</option>
                                    <?php
                                    for ($year = date('Y'); $year >= 2000; $year--) {
                                        echo "<option value='{$year}'>{$year}</option>";
                                    }
                                    ?>
                                </select>
                                <label>To Year <span class="required-mark">*</span></label>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ---------- SERVICE 8: 201 Documents ---------- -->
                <div class="dynamic-section" id="section-service8" data-service="service8">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-folder2-open"></i>201 Documents</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="specify201" name="specify_201"
                                    placeholder="Please specify" data-required-group="service8" maxlength="255"
                                    style="height: 140px;"></textarea>

                                <label for="specify201">
                                    Please Specify <span class="required-mark">*</span>
                                </label>

                                <div class="invalid-feedback">
                                    Only letters, numbers, spaces, commas (,), periods (.), hyphens (-), parentheses (
                                    ), and slashes (/) are allowed.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------- SERVICE 9: Others ---------- -->
                <!-- ---------- SERVICE 9: Others ---------- -->
                <div class="dynamic-section" id="section-service9" data-service="service9">
                    <hr class="section-divider">
                    <p class="section-label">
                        <i class="bi bi-chat-square-text"></i> Others
                    </p>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="otherRequest" name="other_request"
                                    placeholder="Please specify" data-required-group="service9" maxlength="255"
                                    style="height: 140px;"></textarea>

                                <label for="otherRequest">
                                    Please Specify <span class="required-mark">*</span>
                                </label>

                                <div class="invalid-feedback">
                                    Please specify your request.
                                </div>
                            </div>
                        </div>
                    </div>
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
           ELEMENT REFERENCES
        ========================================================= */
        const form = document.getElementById('hrRequestForm');
        const serviceSelect = document.getElementById('serviceAvailed');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmModalEl = document.getElementById('confirmModal');
        const confirmModal = new bootstrap.Modal(confirmModalEl);
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
        const summaryList = document.getElementById('summaryList');
        const certificationError = document.getElementById('certificationError');
        const empCategoryError = document.getElementById('empCategoryError');
        const formAlert = document.getElementById('formAlert');
        const formAlertText = document.getElementById('formAlertText');
        const dateRequestedDisplay = document.getElementById('dateRequestedDisplay');
        const dateRequestedHidden = document.getElementById('dateRequestedHidden');


        // All service-specific sections keyed by service value
        const serviceSections = document.querySelectorAll('.dynamic-section[data-service]');
        // Employee-category sub-sections (only inside Service 5)
        const employeeCategorySections = document.querySelectorAll('.dynamic-section[data-employee-category]');
        const employeeCategoryRadios = document.querySelectorAll('input[name="employee_category"]');
        const certificationCheckboxes = document.querySelectorAll('#certificationChecks input[type="checkbox"]');

        /* =========================================================
           DATE REQUESTED: SET ONCE, ALWAYS TODAY'S DATE
           (static, top of form — no longer tied to a service section)
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
           HIDE ALL DYNAMIC SECTIONS INITIALLY
        ========================================================= */
        function hideAllServiceSections() {
            serviceSections.forEach(function (section) {
                section.classList.remove('active');
                // Disable required-group inputs inside hidden sections so they don't block validation
                toggleRequiredGroup(section, false);
            });
        }

        function hideAllEmployeeCategorySections() {
            employeeCategorySections.forEach(function (section) {
                section.classList.remove('active');
                toggleRequiredGroup(section, false);
            });
        }

        // Enables/disables the "required" attribute for inputs within a section
        function toggleRequiredGroup(container, isRequired) {
            const fields = container.querySelectorAll('[data-required-group]');
            fields.forEach(function (field) {
                if (field.type === 'radio' || field.type === 'checkbox') {
                    // Radios are validated manually (custom logic below)
                    return;
                }
                if (isRequired) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                    field.value = '';
                    field.classList.remove('is-valid', 'is-invalid');
                }
            });
        }

        /* =========================================================
           SHOW SECTION BASED ON SELECTED SERVICE
        ========================================================= */
        serviceSelect.addEventListener('change', function () {

            console.log("Selected:", this.value);

            hideAllServiceSections();
            hideAllEmployeeCategorySections();
            resetEmployeeCategorySelection();
            formAlert.classList.add('d-none');

            const targetSection = document.getElementById('section-' + this.value);

            console.log(targetSection);

            if (targetSection) {
                targetSection.classList.add('active');
                toggleRequiredGroup(targetSection, true);
            }
        });

        /* =========================================================
           SERVICE 5: EMPLOYEE CATEGORY SWITCH (COS vs PERMANENT)
        ========================================================= */
        function resetEmployeeCategorySelection() {
            employeeCategoryRadios.forEach(function (radio) {
                radio.checked = false;
            });
            empCategoryError.style.setProperty('display', 'none', 'important');
        }

        employeeCategoryRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                hideAllEmployeeCategorySections();
                empCategoryError.style.setProperty('display', 'none', 'important');

                const chosen = this.value; // 'cos' or 'permanent'
                const targetSection = document.getElementById('section-' + chosen);
                if (targetSection) {
                    targetSection.classList.add('active');
                    toggleRequiredGroup(targetSection, true);
                }
            });
        });

        /* =========================================================
           SERVICE 6: AT LEAST ONE CERTIFICATION CHECKBOX
        ========================================================= */
        function isCertificationValid() {
            if (serviceSelect.value !== 'service6') return true;
            return Array.from(certificationCheckboxes).some(function (cb) {
                return cb.checked;
            });
        }

        certificationCheckboxes.forEach(function (cb) {
            cb.addEventListener('change', function () {
                if (isCertificationValid()) {
                    certificationError.style.display = 'none';
                }
            });
        });

        /* =========================================================
           CUSTOM VALIDATION: EMPLOYEE CATEGORY RADIO (SERVICE 5)
        ========================================================= */
        function isEmployeeCategoryValid() {
            if (serviceSelect.value !== 'service5') return true;
            return Array.from(employeeCategoryRadios).some(function (r) {
                return r.checked;
            });
        }

        /* =========================================================
           FORM SUBMISSION FLOW
        ========================================================= */
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            let isValid = form.checkValidity();

            // Custom check: a service must be selected
            if (!serviceSelect.value) {
                isValid = false;
            }

            // Custom check: employee category radio (Service 5 only)
            if (!isEmployeeCategoryValid()) {
                isValid = false;
                empCategoryError.style.setProperty('display', 'block', 'important');
            } else {
                empCategoryError.style.setProperty('display', 'none', 'important');
            }

            // Custom check: certification checkboxes (Service 6 only)
            if (!isCertificationValid()) {
                isValid = false;
                certificationError.style.display = 'block';
            } else {
                certificationError.style.display = 'none';
            }

            form.classList.add('was-validated');

            if (!isValid) {
                formAlertText.textContent = 'Please complete all required fields before submitting.';
                formAlert.classList.remove('d-none');
                // Scroll to first invalid field
                const firstInvalid = form.querySelector(':invalid, .is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
                return;
            }

            formAlert.classList.add('d-none');
            buildSummary();
            confirmModal.show();
        });

        /* =========================================================
           BUILD CONFIRMATION MODAL SUMMARY
        ========================================================= */
        function buildSummary() {
            summaryList.innerHTML = '';

            const serviceLabels = {
                service1: 'Service Record',
                service2: 'Certificate with Compensation',
                service3: 'Loan Assistance',
                service4: 'Inquiry of Leave Balance',
                service5: 'Certificate of Employment',
                service6: 'Certification',
                service7: 'IPCR',
                service8: '201 Documents',
                service9: 'Others'
            };

            const rows = [];

            rows.push(['Service Availed', serviceLabels[serviceSelect.value] || '—']);
            rows.push(['Name', document.getElementById('fullName').value]);
            rows.push(['Contact Number', document.getElementById('contactNumber').value]);
            rows.push(['Email Address', document.getElementById('emailAddress').value]);
            rows.push(['Date Requested', dateRequestedHidden.value]);

            switch (serviceSelect.value) {
                case 'service1':
                case 'service2':
                case 'service3':
                case 'service4':
                    // No additional service-specific fields beyond the common ones.
                    break;
                case 'service5': {
                    const category = document.querySelector('input[name="employee_category"]:checked');
                    const categoryValue = category ? category.value : '';
                    rows.push(['Employee Category', categoryValue === 'cos' ? 'COS Employee' : 'Permanent Employee']);
                    if (categoryValue === 'cos') {
                        rows.push(['Period of Service', document.getElementById('periodOfService').value]);
                        rows.push(['Position', document.getElementById('cosPosition').value]);
                        rows.push(['Office / Division Assigned', document.getElementById('officeAssigned').value]);
                    }
                    break;
                }
                case 'service6': {
                    const checked = Array.from(certificationCheckboxes)
                        .filter(function (cb) {
                            return cb.checked;
                        })
                        .map(function (cb) {
                            return cb.value;
                        });
                    rows.push(['Certification Needed', checked.join(', ')]);
                    break;
                }
                case 'service7': {

                    const from =
                        document.getElementById('ipcrFromMonth').value +
                        ' ' +
                        document.getElementById('ipcrFromYear').value;

                    const to =
                        document.getElementById('ipcrToMonth').value +
                        ' ' +
                        document.getElementById('ipcrToYear').value;

                    rows.push(['IPCR Period', from + ' to ' + to]);
                    break;
                }
                case 'service8':
                    rows.push(['Please Specify', document.getElementById('specify201').value]);
                    break;
                case 'service9':
                    rows.push([
                        'Request',
                        document.getElementById('otherRequest').value
                    ]);
                    break;

                default:
                    break;
            }

            rows.forEach(function (pair) {
                const dt = document.createElement('dt');
                dt.textContent = pair[0];
                const dd = document.createElement('dd');
                dd.textContent = pair[1] || '—';
                summaryList.appendChild(dt);
                summaryList.appendChild(dd);
            });
        }

        confirmSubmitBtn.addEventListener('click', function () {
            confirmModal.hide();
            form.submit();
        });

        /* =========================================================
           CANCEL BUTTON
        ========================================================= */
        cancelBtn.addEventListener('click', function () {
            window.location.href = "<?= site_url('eportal') ?>";
        });

        const otherRequest = document.getElementById('otherRequest');

        if (otherRequest) {

            const allowedPattern = /^[A-Za-z0-9\s.,()\/-]*$/;

            otherRequest.addEventListener('input', function () {

                if (!allowedPattern.test(this.value)) {
                    this.setCustomValidity(
                        'Only letters, numbers, spaces, commas, periods, hyphens, parentheses, and slashes are allowed.'
                    );
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                }

            });

        }

        function resetForm() {
            form.reset();
            form.classList.remove('was-validated');
            hideAllServiceSections();
            hideAllEmployeeCategorySections();
            certificationError.style.display = 'none';
            empCategoryError.style.setProperty('display', 'none', 'important');
            formAlert.classList.add('d-none');
            document.querySelectorAll('.form-control, .form-select').forEach(function (el) {
                el.classList.remove('is-valid', 'is-invalid');
            });
            setDateRequested();
        }

        /* Initialize: make sure everything is hidden on page load, date requested is set once */
        document.addEventListener("DOMContentLoaded", function () {
            hideAllServiceSections();
            hideAllEmployeeCategorySections();
            setDateRequested();
        });
    })();

    const specify201 = document.getElementById('specify201');

    if (specify201) {

        const allowedPattern = /^[A-Za-z0-9\s.,()\/-]*$/;

        specify201.addEventListener('input', function () {

            if (!allowedPattern.test(this.value)) {
                this.setCustomValidity(
                    'Only letters, numbers, spaces, commas, periods, hyphens, parentheses, and slashes are allowed.'
                );
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }

        });

    }
</script>


<?php if (session()->getFlashdata('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();
        });
    </script>
<?php endif; ?>