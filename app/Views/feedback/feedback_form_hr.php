<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Feedback to IMSD-HR Unit</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f0ebf8;
            font-family: 'Google Sans', Roboto, Arial, sans-serif;
            color: #202124;
            padding: 24px 0 80px;
        }

        .wrap {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .topbar {
            height: 10px;
            background: #673ab7;
            border-radius: 8px 8px 0 0;
        }

        .header-card,
        .card {
            background: #fff;
            border: 1px solid #dadce0;
            border-radius: 8px;
            padding: 24px 24px;
            margin-bottom: 16px;
        }

        .header-card {
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .agency-line {
            font-size: 12px;
            color: #5f6368;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 4px;
        }

        h1 {
            font-size: 22px;
            font-weight: 400;
            margin: 0 0 4px;
            color: #202124;
        }

        .subtitle {
            font-size: 13px;
            color: #5f6368;
            margin: 0 0 12px;
        }

        .desc {
            font-size: 14px;
            line-height: 1.5;
            color: #202124;
            border-top: 1px solid #f1f3f4;
            padding-top: 12px;
            margin-top: 12px;
        }

        .ref-badge {
            display: inline-block;
            background: #ede7f6;
            color: #4527a0;
            font-size: 13px;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 16px;
            margin-bottom: 4px;
        }

        .service-badge {
            display: inline-block;
            background: #f1f3f4;
            color: #3c4043;
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 6px;
            margin-top: 6px;
            line-height: 1.5;
        }

        .section-title {
            font-size: 16px;
            font-weight: 500;
            margin: 0 0 4px;
        }

        .section-sub {
            font-size: 12px;
            color: #5f6368;
            margin: 0 0 16px;
        }

        .required {
            color: #d93025;
        }

        label.field-label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #202124;
        }

        .field-block {
            margin-bottom: 22px;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="tel"] {
            width: 100%;
            padding: 8px 0;
            border: none;
            border-bottom: 1px solid #dadce0;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            background: transparent;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus,
        input[type="tel"]:focus {
            border-bottom: 2px solid #673ab7;
            padding-bottom: 7px;
        }

        .radio-row,
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 4px;
            border-radius: 4px;
            cursor: pointer;
        }

        .radio-row:hover,
        .checkbox-row:hover {
            background: #f8f7fc;
        }

        .radio-row input,
        .checkbox-row input {
            accent-color: #673ab7;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .radio-row span,
        .checkbox-row span {
            font-size: 14px;
        }

        .service-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 16px;
        }

        @media (max-width: 600px) {
            .service-grid {
                grid-template-columns: 1fr;
            }
        }

        /* rating matrix */
        .matrix {
            overflow-x: auto;
        }

        table.matrix-table {
            border-collapse: collapse;
            width: 100%;
            font-size: 13px;
        }

        table.matrix-table th,
        table.matrix-table td {
            text-align: center;
            padding: 10px 6px;
            border-bottom: 1px solid #f1f3f4;
        }

        table.matrix-table th {
            font-weight: 400;
            color: #5f6368;
            font-size: 12px;
        }

        table.matrix-table td.qtext {
            text-align: left;
            font-size: 13.5px;
            color: #202124;
            max-width: 220px;
            line-height: 1.4;
            padding-right: 10px;
        }

        table.matrix-table input[type="radio"] {
            accent-color: #673ab7;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .na-badge {
            display: inline-block;
            font-size: 12px;
            color: #5f6368;
            background: #f1f3f4;
            padding: 4px 10px;
            border-radius: 12px;
        }

        textarea {
            width: 100%;
            min-height: 70px;
            border: none;
            border-bottom: 1px solid #dadce0;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            outline: none;
            padding: 8px 0;
        }

        textarea:focus {
            border-bottom: 2px solid #673ab7;
        }

        .footer-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 4px 40px;
        }

        button.submit-btn {
            background: #673ab7;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        button.submit-btn:disabled {
            opacity: .6;
            cursor: default;
        }

        button.submit-btn:hover:not(:disabled) {
            background: #5a2ea6;
        }

        .clear-link {
            font-size: 13px;
            color: #673ab7;
            cursor: pointer;
            background: none;
            border: none;
        }

        .error-text {
            color: #d93025;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        .form-error-banner {
            display: none;
            background: #fce8e6;
            color: #c5221f;
            font-size: 13px;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .sex-row {
            display: flex;
            gap: 24px;
            margin-top: 4px;
        }

        .confirm {
            display: none;
            text-align: center;
            padding: 60px 24px;
        }

        .confirm h2 {
            font-weight: 400;
            margin: 16px 0 8px;
        }

        .confirm p {
            color: #5f6368;
            font-size: 14px;
        }

        .other-input {
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <div class="wrap">

        <div class="topbar"></div>
        <div class="header-card">
            <div class="agency-line">Republic of the Philippines &middot; Department of Labor and Employment &middot;
                Mimaropa Region</div>
            <h1>Client feedback to IMSD-HR unit</h1>
            <p class="subtitle">CSF Form No. 3</p>
            <span class="ref-badge">Reference no: <?= esc($reference_no) ?></span><br>
            <span class="service-badge">Service availed: <?= esc($service_availed) ?></span>
            <p class="desc">Thank you for giving us the opportunity to serve you. Please help us improve the quality of
                our services by taking a few minutes to tell us about your experience with us.<br><br>Pakilagyan ng tsek
                ang kahon na nagsasaad ng iyong tugon.</p>
        </div>

        <div class="form-error-banner" id="formErrorBanner"></div>

        <form id="feedbackForm">
            <?= csrf_field() ?>
            <input type="hidden" name="reference_no" value="<?= esc($reference_no) ?>">

            <!-- CLIENT TYPE -->
            <div class="card">
                <p class="section-title">Client type <span class="required">*</span></p>
                <div class="radio-row"><input type="radio" name="clientType" value="General Public" id="ct1"
                        required><label for="ct1"><span>General public</span></label></div>
                <div class="radio-row"><input type="radio" name="clientType" value="Business" id="ct2"><label
                        for="ct2"><span>Business</span></label></div>
                <div class="field-block other-input" id="businessNameWrap" style="display:none; margin-left:30px;">
                    <label class="field-label" for="businessName">Name of business</label>
                    <input type="text" id="businessName" name="businessName">
                </div>
                <div class="radio-row"><input type="radio" name="clientType" value="Government Agency/Official/Employee"
                        id="ct3"><label for="ct3"><span>Government agency / official / employee</span></label></div>
                <div class="field-block other-input" id="govAgencyWrap" style="display:none; margin-left:30px;">
                    <label class="field-label" for="govAgency">Name of government agency</label>
                    <input type="text" id="govAgency" name="govAgency">
                </div>
                <p class="error-text" id="err-clientType">This question is required</p>
            </div>

            <!-- CLIENT INFORMATION -->
            <div class="card">
                <p class="section-title">Client information</p>
                <p class="section-sub">Impormasyon ng kliyente</p>

                <div class="field-block">
                    <label class="field-label" for="name">Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="field-block">
                    <label class="field-label" for="contact">Contact number</label>
                    <input type="tel" id="contact" name="contact">
                </div>
                <div class="field-block">
                    <label class="field-label" for="email">Email address</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="field-block">
                    <label class="field-label" for="office">Office visited</label>
                    <input type="text" id="office" name="office">
                </div>
                <div class="field-block">
                    <label class="field-label" for="date">Date <span class="required">*</span></label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="field-block">
                    <p class="field-label">Sex</p>
                    <div class="sex-row">
                        <div class="radio-row"><input type="radio" name="sex" value="Male" id="sexM"><label
                                for="sexM"><span>Male</span></label></div>
                        <div class="radio-row"><input type="radio" name="sex" value="Female" id="sexF"><label
                                for="sexF"><span>Female</span></label></div>
                    </div>
                </div>
            </div>

            <!-- SERVICE AVAILED -->
            <div class="card">
                <p class="section-title">Service availed <span class="required">*</span></p>
                <p class="section-sub">Serbisyong kinailangan &mdash; select all that apply</p>
                <div class="service-grid">
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for Alien Employment Permit (New/Renewal)" id="s1"><label
                            for="s1"><span>Application for Alien Employment Permit (New/Renewal)</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Issuance of Certificate of Appearance for Professional Mechanical Engineer/Professional Electrical Engineer"
                            id="s2"><label for="s2"><span>Issuance of Certificate of Appearance for Professional
                                Mechanical/Electrical Engineer</span></label></div>

                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for Authority to Operate Branch Office of a Private Employment Agency"
                            id="s3"><label for="s3"><span>Application for Authority to Operate Branch Office of a
                                Private Employment Agency</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Issuance of Certificate of Exclusion from Alien Employment Permit" id="s4"><label
                            for="s4"><span>Issuance of Certificate of Exclusion from Alien Employment
                                Permit</span></label></div>

                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for Authority to Recruit" id="s5"><label for="s5"><span>Application for
                                Authority to Recruit</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Issuance of Letter of Approval/Disapproval of Construction Safety and Health Program (CSHP) Application"
                            id="s6"><label for="s6"><span>Issuance of Letter of Approval/Disapproval of CSHP
                                Application</span></label></div>

                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for License to Operate Private Employment Agency (PEA)" id="s7"><label
                            for="s7"><span>Application for License to Operate Private Employment Agency
                                (PEA)</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Issuance of Permit to Operate Mechanical Installation/Certificate of Electrical Inspection (CEI)"
                            id="s8"><label for="s8"><span>Issuance of Permit to Operate Mechanical
                                Installation/CEI</span></label></div>

                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for Livelihood Project Assistance" id="s9"><label
                            for="s9"><span>Application for Livelihood Project Assistance</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Registration of Establishment under Rule 1020 of the Occupational Safety and Health Standards"
                            id="s10"><label for="s10"><span>Registration of Establishment under Rule 1020
                                OSHS</span></label></div>

                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for Job Fair Clearance" id="s11"><label for="s11"><span>Application for
                                Job Fair Clearance</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Registration of Collective Bargaining Agreement" id="s12"><label
                            for="s12"><span>Registration of Collective Bargaining Agreement</span></label></div>

                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for Job Fair Permit" id="s13"><label for="s13"><span>Application for Job
                                Fair Permit</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Registration of Contractors" id="s14"><label for="s14"><span>Registration of
                                Contractors</span></label></div>

                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for Sugar Workers' Death Benefit Claim" id="s15"><label
                            for="s15"><span>Application for Sugar Workers' Death Benefit Claim</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]" value="Registration of Union"
                            id="s16"><label for="s16"><span>Registration of Union</span></label></div>

                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Application for Sugar Workers' Maternity Benefit Claim" id="s17"><label
                            for="s17"><span>Application for Sugar Workers' Maternity Benefit Claim</span></label></div>
                    <div class="checkbox-row"><input type="checkbox" name="service[]"
                            value="Registration of Workers' Association" id="s18"><label for="s18"><span>Registration of
                                Workers' Association</span></label></div>
                </div>
                <div class="field-block other-input">
                    <label class="field-label" for="otherService">Others, please specify</label>
                    <input type="text" id="otherService" name="otherService" value="<?= esc($service_availed) ?>">
                </div>
                <p class="error-text" id="err-service">Please select at least one service</p>
            </div>

            <!-- RATING MATRIX -->
            <div class="card">
                <p class="section-title">Rate your experience <span class="required">*</span></p>
                <p class="section-sub">Mga detalye</p>
                <div class="matrix">
                    <table class="matrix-table" id="ratingTable">
                        <thead>
                            <tr>
                                <th style="text-align:left;"></th>
                                <th>Strongly agree</th>
                                <th>Agree</th>
                                <th>Neither agree nor disagree</th>
                                <th>Disagree</th>
                                <th>Strongly disagree</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <p class="error-text" id="err-rating">Please answer every row</p>
            </div>

            <!-- COMMENTS -->
            <div class="card">
                <div class="field-block">
                    <label class="field-label" for="complaint">Complaint about our service</label>
                    <p class="section-sub" style="margin-top:-6px;">Reklamo sa serbisyong ibinigay</p>
                    <textarea id="complaint" name="complaint"></textarea>
                </div>
                <div class="field-block" style="margin-bottom:0;">
                    <label class="field-label" for="comment">Comment / suggestion / recommendation</label>
                    <p class="section-sub" style="margin-top:-6px;">Mungkahi / rekomendasyon</p>
                    <textarea id="comment" name="comment"></textarea>
                </div>
            </div>

            <div class="card">
                <div class="field-block" style="margin-bottom:0;">
                    <label class="field-label" for="signature">Signature (type full name) <span
                            class="required">*</span></label>
                    <input type="text" id="signature" name="signature" required>
                </div>
            </div>

            <div class="footer-actions">
                <button type="submit" class="submit-btn" id="submitBtn">Submit</button>
                <button type="button" class="clear-link"
                    onclick="document.getElementById('feedbackForm').reset(); document.getElementById('businessNameWrap').style.display='none'; document.getElementById('govAgencyWrap').style.display='none';">Clear
                    form</button>
            </div>

        </form>

        <div class="confirm" id="confirmScreen">
            <h2>Response recorded</h2>
            <p id="confirmRef">Salamat po! Your feedback has been noted.</p>
        </div>

    </div>

    <script>
        var questions = [
            "The requested service/s is/are delivered in a timely manner",
            "The client's queries/needs is/are properly responded to by the concerned personnel or unit",
            "The service/s and facility/ies is/are easily accessible",
            "The instructions are clearly indicated in the Citizen's Charter",
            "The fees are reasonable",
            "The client feels safe and secured in doing the transaction",
            "The Action Officer is knowledgeable on the DOLE programs/services",
            "The client is satisfied with the service/s received"
        ];
        var naRows = [3, 4];
        var tbody = document.querySelector('#ratingTable tbody');
        questions.forEach(function (q, i) {
            var tr = document.createElement('tr');
            if (naRows.indexOf(i) !== -1) {
                tr.innerHTML = '<td class="qtext">' + (i + 1) + '. ' + q + '</td>' +
                    '<td colspan="5" style="text-align:left;"><span class="na-badge">N/A</span></td>';
            } else {
                var cells = '<td class="qtext">' + (i + 1) + '. ' + q + '</td>';
                ['Strongly agree', 'Agree', 'Neither', 'Disagree', 'Strongly disagree'].forEach(function (val) {
                    cells += '<td><input type="radio" name="q' + i + '" value="' + val + '"></td>';
                });
                tr.innerHTML = cells;
            }
            tbody.appendChild(tr);
        });

        document.getElementById('ct2').addEventListener('change', function () {
            document.getElementById('businessNameWrap').style.display = this.checked ? 'block' : 'none';
        });
        document.getElementById('ct1').addEventListener('change', function () {
            document.getElementById('businessNameWrap').style.display = 'none';
            document.getElementById('govAgencyWrap').style.display = 'none';
        });
        document.getElementById('ct3').addEventListener('change', function () {
            document.getElementById('govAgencyWrap').style.display = this.checked ? 'block' : 'none';
            document.getElementById('businessNameWrap').style.display = 'none';
        });

        document.getElementById('feedbackForm').addEventListener('submit', function (e) {
            e.preventDefault();
            var form = this;
            var valid = true;

            var clientTypeChecked = document.querySelector('input[name="clientType"]:checked');
            document.getElementById('err-clientType').style.display = clientTypeChecked ? 'none' : 'block';
            if (!clientTypeChecked) valid = false;

            var serviceChecked = document.querySelectorAll('input[name="service[]"]:checked').length > 0 || document.getElementById('otherService').value.trim() !== '';
            document.getElementById('err-service').style.display = serviceChecked ? 'none' : 'block';
            if (!serviceChecked) valid = false;

            var ratingComplete = true;
            questions.forEach(function (q, i) {
                if (naRows.indexOf(i) === -1) {
                    if (!document.querySelector('input[name="q' + i + '"]:checked')) ratingComplete = false;
                }
            });
            document.getElementById('err-rating').style.display = ratingComplete ? 'none' : 'block';
            if (!ratingComplete) valid = false;

            if (!form.checkValidity()) {
                valid = false;
                form.reportValidity();
            }

            if (!valid) return;

            var banner = document.getElementById('formErrorBanner');
            banner.style.display = 'none';

            var submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            fetch('<?= base_url('feedback/save') ?>', {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.status === 'success') {
                        form.style.display = 'none';
                        banner.style.display = 'none';
                        document.getElementById('confirmRef').textContent = 'Salamat po! Your feedback for reference no. ' + data.reference_no + ' has been recorded.';
                        document.getElementById('confirmScreen').style.display = 'block';
                        window.scrollTo(0, 0);
                    } else {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit';
                        banner.textContent = data.message || 'Please check the form for errors and try again.';
                        banner.style.display = 'block';
                        window.scrollTo(0, 0);
                    }
                })
                .catch(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit';
                    banner.textContent = 'Something went wrong. Please try again.';
                    banner.style.display = 'block';
                    window.scrollTo(0, 0);
                });
        });
    </script>

</body>

</html>