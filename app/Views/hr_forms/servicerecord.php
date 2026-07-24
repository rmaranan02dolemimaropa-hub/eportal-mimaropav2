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
            <p class="header-subtitle">Please complete the form below to request HR services. Fields marked with <span class="required-mark">*</span> are required.</p>
        </div>

        <!-- ===================== FORM CARD ===================== -->
        <div class="form-card">

            <div id="formAlert" class="alert alert-dole d-none" role="alert">
                <i class="bi bi-info-circle me-2"></i><span id="formAlertText"></span>
            </div>

            <form id="hrRequestForm" class="needs-validation" novalidate>

                <!-- ---------- COMMON FIELDS ---------- -->
                <p class="section-label"><i class="bi bi-clipboard2-check"></i>Request Details</p>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-floating">

                            <select class="form-select" id="serviceAvailed" required>
                                <option value="" selected disabled>Select a service</option>
                                <option value="service1">Service Record</option>
                                <option value="service2">Certificate with Compensation</option>
                                <option value="service3">Loan Assistance</option>
                                <option value="service4">Inquiry of Leave Balance</option>
                                <option value="service5">Certificate of Employment</option>
                                <option value="service6">Certification</option>
                                <option value="service7">IPCR</option>
                                <option value="service8">201 Documents</option>
                            </select>
                            <label for="serviceAvailed">Service Availed <span class="required-mark">*</span></label>
                            <div class="invalid-feedback">Please select a service.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="fullName" placeholder="Juan Dela Cruz" required>
                            <label for="fullName">Full Name <span class="required-mark">*</span></label>
                            <div class="invalid-feedback">Please enter your name.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="contactNumber" placeholder="09XXXXXXXXX"
                                pattern="^(09\d{9}|\+639\d{9})$" required>
                            <label for="contactNumber">Contact Number <span class="required-mark">*</span></label>
                            <div class="invalid-feedback">Please enter a valid PH mobile number (e.g. 09171234567).</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="emailAddress" placeholder="name@example.com" required>
                            <label for="emailAddress">Email Address <span class="required-mark">*</span></label>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                    </div>
                </div>

                <!-- ================================================= -->
                <!-- SERVICE-SPECIFIC SECTIONS (shown/hidden by JS)     -->
                <!-- ================================================= -->

                <!-- ---------- SERVICE 1: Service Record ---------- -->
                <div class="dynamic-section" id="section-service1" data-service="service1">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-file-earmark-text"></i>Service Record</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateRequested1" data-required-group="service1">
                                <label for="dateRequested1">Date Requested <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please select the date requested.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------- SERVICE 2: Certificate with Compensation ---------- -->
                <div class="dynamic-section" id="section-service2" data-service="service2">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-file-earmark-text"></i>Certificate with Compensation</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateRequested2" data-required-group="service2">
                                <label for="dateRequested2">Date Requested <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please select the date requested.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------- SERVICE 3: Loan Assistance ---------- -->
                <div class="dynamic-section" id="section-service3" data-service="service3">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-cash-coin"></i>Loan Assistance</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateRequested3" data-required-group="service3">
                                <label for="dateRequested3">Date Requested <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please select the date requested.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------- SERVICE 4: Inquiry of Leave Balance ---------- -->
                <div class="dynamic-section" id="section-service4" data-service="service4">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-calendar2-check"></i>Inquiry of Leave Balance</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateRequested4" data-required-group="service4">
                                <label for="dateRequested4">Date Requested <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please select the date requested.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------- SERVICE 5: Certificate of Employment ---------- -->
                <div class="dynamic-section" id="section-service5" data-service="service5">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-person-badge"></i>Certificate of Employment</p>

                    <label class="form-label fw-semibold mb-2">Employee Category <span class="required-mark">*</span></label>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="radio" name="employeeCategory" id="empCategoryCOS" value="cos" data-required-group="service5">
                                <label for="empCategoryCOS">COS Employee</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="radio" name="employeeCategory" id="empCategoryPermanent" value="permanent" data-required-group="service5">
                                <label for="empCategoryPermanent">Permanent Employee</label>
                            </div>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block" id="empCategoryError" style="display:none !important;">Please select an employee category.</div>

                    <!-- COS Employee fields -->
                    <div class="dynamic-section" id="section-cos" data-employee-category="cos">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="periodOfService" placeholder="Period of Service" data-required-group="cos">
                                    <label for="periodOfService">Period of Service <span class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please enter the period of service.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="cosPosition" placeholder="Position" data-required-group="cos">
                                    <label for="cosPosition">Position <span class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please enter your position.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="officeAssigned" placeholder="Office / Division Assigned" data-required-group="cos">
                                    <label for="officeAssigned">Office / Division Assigned <span class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please enter your office or division.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="dateRequestedCOS" data-required-group="cos">
                                    <label for="dateRequestedCOS">Date Requested <span class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please select the date requested.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permanent Employee fields -->
                    <div class="dynamic-section" id="section-permanent" data-employee-category="permanent">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="permanentPosition" placeholder="Position" data-required-group="permanent">
                                    <label for="permanentPosition">Position <span class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please enter your position.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="dateRequestedPermanent" data-required-group="permanent">
                                    <label for="dateRequestedPermanent">Date Requested <span class="required-mark">*</span></label>
                                    <div class="invalid-feedback">Please select the date requested.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------- SERVICE 6: Certification ---------- -->
                <div class="dynamic-section" id="section-service6" data-service="service6">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-patch-check"></i>Certification</p>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateRequested6" data-required-group="service6">
                                <label for="dateRequested6">Date Requested <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please select the date requested.</div>
                            </div>
                        </div>
                    </div>

                    <label class="form-label fw-semibold mb-2">Certification Needed <span class="required-mark">*</span> <span class="text-muted fw-normal">(select at least one)</span></label>
                    <div class="row g-3" id="certificationChecks">
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" id="certNoPending" value="No Pending Case">
                                <label for="certNoPending">No Pending Case</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" id="certLastSalary" value="Last Salary Received">
                                <label for="certLastSalary">Last Salary Received</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" id="certLeaveCredits" value="Leave Credits">
                                <label for="certLeaveCredits">Leave Credits</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="choice-card">
                                <input class="form-check-input mt-0" type="checkbox" id="certLeaveWithoutPay" value="Leave Without Pay">
                                <label for="certLeaveWithoutPay">Leave Without Pay</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-danger small mt-2" id="certificationError" style="display:none;">
                        <i class="bi bi-exclamation-circle me-1"></i>Please select at least one certification.
                    </div>
                </div>

                <!-- ---------- SERVICE 7: IPCR ---------- -->
                <div class="dynamic-section" id="section-service7" data-service="service7">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-bar-chart-line"></i>IPCR</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateRequested7" data-required-group="service7">
                                <label for="dateRequested7">Date Requested <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please select the date requested.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="ipcrPeriod" placeholder="e.g. January - June 2026" data-required-group="service7">
                                <label for="ipcrPeriod">Please Indicate Period <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please indicate the period.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ---------- SERVICE 8: 201 Documents ---------- -->
                <div class="dynamic-section" id="section-service8" data-service="service8">
                    <hr class="section-divider">
                    <p class="section-label"><i class="bi bi-folder2-open"></i>201 Documents</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateRequested8" data-required-group="service8">
                                <label for="dateRequested8">Date Requested <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please select the date requested.</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="specify201" placeholder="Please specify" data-required-group="service8" style="height: 140px;"></textarea>
                                <label for="specify201">Please Specify <span class="required-mark">*</span></label>
                                <div class="invalid-feedback">Please specify your request.</div>
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

        <p class="footer-note">DOLE MIMAROPA ePortal &middot; Human Resource Unit &middot; For assistance, contact your HR focal person.</p>

    </div>

    <!-- ===================== CONFIRMATION MODAL ===================== -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel"><i class="bi bi-clipboard2-check me-2"></i>Confirm Your Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Please review your details before submitting. Once submitted, your request will be forwarded to the HR Unit.</p>
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
                    <p class="text-muted mb-4">Your HR request has been received. The HR Unit will get in touch with you via the contact details provided.</p>
                    <button type="button" class="btn btn-dole-primary px-4" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

</main>


<!-- Bootstrap 5 Bundle (includes Popper) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script>
    (function() {
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

        // All service-specific sections keyed by service value
        const serviceSections = document.querySelectorAll('.dynamic-section[data-service]');
        // Employee-category sub-sections (only inside Service 5)
        const employeeCategorySections = document.querySelectorAll('.dynamic-section[data-employee-category]');
        const employeeCategoryRadios = document.querySelectorAll('input[name="employeeCategory"]');
        const certificationCheckboxes = document.querySelectorAll('#certificationChecks input[type="checkbox"]');

        /* =========================================================
           HIDE ALL DYNAMIC SECTIONS INITIALLY
        ========================================================= */
        function hideAllServiceSections() {
            serviceSections.forEach(function(section) {
                section.classList.remove('active');
                // Disable required-group inputs inside hidden sections so they don't block validation
                toggleRequiredGroup(section, false);
            });
        }

        function hideAllEmployeeCategorySections() {
            employeeCategorySections.forEach(function(section) {
                section.classList.remove('active');
                toggleRequiredGroup(section, false);
            });
        }

        // Enables/disables the "required" attribute for inputs within a section
        function toggleRequiredGroup(container, isRequired) {
            const fields = container.querySelectorAll('[data-required-group]');
            fields.forEach(function(field) {
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
        serviceSelect.addEventListener('change', function() {
            const selected = serviceSelect.value;

            hideAllServiceSections();
            hideAllEmployeeCategorySections();
            resetEmployeeCategorySelection();
            formAlert.classList.add('d-none');

            const targetSection = document.getElementById('section-' + selected);
            if (targetSection) {
                targetSection.classList.add('active');
                toggleRequiredGroup(targetSection, true);
            }
        });

        /* =========================================================
           SERVICE 5: EMPLOYEE CATEGORY SWITCH (COS vs PERMANENT)
        ========================================================= */
        function resetEmployeeCategorySelection() {
            employeeCategoryRadios.forEach(function(radio) {
                radio.checked = false;
            });
            empCategoryError.style.setProperty('display', 'none', 'important');
        }

        employeeCategoryRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
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
            return Array.from(certificationCheckboxes).some(function(cb) {
                return cb.checked;
            });
        }

        certificationCheckboxes.forEach(function(cb) {
            cb.addEventListener('change', function() {
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
            return Array.from(employeeCategoryRadios).some(function(r) {
                return r.checked;
            });
        }

        /* =========================================================
           FORM SUBMISSION FLOW
        ========================================================= */
        form.addEventListener('submit', function(event) {
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
                service8: '201 Documents'
            };

            const rows = [];

            rows.push(['Service Availed', serviceLabels[serviceSelect.value] || '—']);
            rows.push(['Name', document.getElementById('fullName').value]);
            rows.push(['Contact Number', document.getElementById('contactNumber').value]);
            rows.push(['Email Address', document.getElementById('emailAddress').value]);

            switch (serviceSelect.value) {
                case 'service1':
                    rows.push(['Date Requested', document.getElementById('dateRequested1').value]);
                    break;
                case 'service2':
                    rows.push(['Date Requested', document.getElementById('dateRequested2').value]);
                    break;
                case 'service3':
                    rows.push(['Date Requested', document.getElementById('dateRequested3').value]);
                    break;
                case 'service4':
                    rows.push(['Date Requested', document.getElementById('dateRequested4').value]);
                    break;
                case 'service5': {
                    const category = document.querySelector('input[name="employeeCategory"]:checked');
                    const categoryValue = category ? category.value : '';
                    rows.push(['Employee Category', categoryValue === 'cos' ? 'COS Employee' : 'Permanent Employee']);
                    if (categoryValue === 'cos') {
                        rows.push(['Period of Service', document.getElementById('periodOfService').value]);
                        rows.push(['Position', document.getElementById('cosPosition').value]);
                        rows.push(['Office / Division Assigned', document.getElementById('officeAssigned').value]);
                        rows.push(['Date Requested', document.getElementById('dateRequestedCOS').value]);
                    } else if (categoryValue === 'permanent') {
                        rows.push(['Position', document.getElementById('permanentPosition').value]);
                        rows.push(['Date Requested', document.getElementById('dateRequestedPermanent').value]);
                    }
                    break;
                }
                case 'service6': {
                    rows.push(['Date Requested', document.getElementById('dateRequested6').value]);
                    const checked = Array.from(certificationCheckboxes)
                        .filter(function(cb) {
                            return cb.checked;
                        })
                        .map(function(cb) {
                            return cb.value;
                        });
                    rows.push(['Certification Needed', checked.join(', ')]);
                    break;
                }
                case 'service7':
                    rows.push(['Date Requested', document.getElementById('dateRequested7').value]);
                    rows.push(['Period Indicated', document.getElementById('ipcrPeriod').value]);
                    break;
                case 'service8':
                    rows.push(['Date Requested', document.getElementById('dateRequested8').value]);
                    rows.push(['Please Specify', document.getElementById('specify201').value]);
                    break;
                default:
                    break;
            }

            rows.forEach(function(pair) {
                const dt = document.createElement('dt');
                dt.textContent = pair[0];
                const dd = document.createElement('dd');
                dd.textContent = pair[1] || '—';
                summaryList.appendChild(dt);
                summaryList.appendChild(dd);
            });
        }

        /* =========================================================
           FINAL SUBMIT (from confirmation modal)
        ========================================================= */
        confirmSubmitBtn.addEventListener('click', function() {
            // In production, this is where the form data would be sent to the server (e.g. via fetch()).
            confirmModal.hide();

            // Small delay so the confirm modal fully closes before the success modal opens
            setTimeout(function() {
                successModal.show();
                resetForm();
            }, 300);
        });

        /* =========================================================
           CANCEL BUTTON
        ========================================================= */
        cancelBtn.addEventListener('click', function() {
            window.location.href = "eportal";
        });

        function resetForm() {
            form.reset();
            form.classList.remove('was-validated');
            hideAllServiceSections();
            hideAllEmployeeCategorySections();
            certificationError.style.display = 'none';
            empCategoryError.style.setProperty('display', 'none', 'important');
            formAlert.classList.add('d-none');
            document.querySelectorAll('.form-control, .form-select').forEach(function(el) {
                el.classList.remove('is-valid', 'is-invalid');
            });
        }

        /* Initialize: make sure everything is hidden on page load */
        hideAllServiceSections();
        hideAllEmployeeCategorySections();
    })();
</script>