<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Satisfaction Measurement (CSM) | DOLE MIMAROPA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --dole-blue: #10058C;
            --dole-blue-light: #2a1fb0;
            --ink: #1c1d21;
            --muted: #6b6f7b;
            --line: #dfe1e8;
            --bg: #eef0f6;
            --card: #ffffff;
            --danger: #c0392b;
            --radius: 14px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            font-family: "Manrope", "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.5;
            padding: clamp(16px, 5vw, 32px) clamp(12px, 4vw, 16px) 64px;
        }

        .page-wrap {
            max-width: 880px;
            margin: 0 auto;
            width: 100%;
        }

        img,
        svg {
            max-width: 100%;
        }

        /* ===== HEADER ===== */
        .header-card {
            background: linear-gradient(135deg, var(--dole-blue), var(--dole-blue-light));
            border-radius: var(--radius);
            padding: clamp(18px, 4vw, 26px) clamp(18px, 5vw, 30px);
            color: #fff;
            margin-bottom: 18px;
            box-shadow: 0 8px 24px rgba(16, 5, 140, .18);
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .logo-badge {
            width: 52px;
            height: 52px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 800;
            color: var(--dole-blue);
            font-size: .72rem;
            text-align: center;
            line-height: 1.1;
        }

        .agency-line {
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .7rem;
            opacity: .85;
            font-weight: 600;
        }

        .header-title {
            margin: 2px 0 0;
            font-size: clamp(1.15rem, 4vw, 1.5rem);
            font-weight: 700;
            line-height: 1.25;
        }

        .header-subtitle {
            margin: 14px 0 0;
            font-size: .86rem;
            opacity: .92;
            max-width: 640px;
        }

        .control-row {
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 18px;
            font-size: .78rem;
        }

        .control-row span.tag {
            background: rgba(255, 255, 255, .15);
            padding: 5px 11px;
            border-radius: 999px;
        }

        /* ===== CARD ===== */
        .form-card {
            background: var(--card);
            border-radius: var(--radius);
            padding: clamp(18px, 4vw, 28px) clamp(16px, 4vw, 30px) clamp(22px, 4vw, 34px);
            margin-bottom: 18px;
            box-shadow: 0 2px 10px rgba(20, 20, 43, .06);
            border: 1px solid var(--line);
        }

        .form-card+.form-card {
            margin-top: 18px;
        }

        .card-label {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--dole-blue);
            margin: 0 0 4px;
        }

        .card-desc {
            font-size: .85rem;
            color: var(--muted);
            margin: 0 0 20px;
        }

        .required-mark {
            color: var(--danger);
        }

        /* office-use box */
        .office-use {
            border: 1px dashed #b9bdd6;
            border-radius: 10px;
            padding: 14px 16px;
            background: #f7f7fc;
            font-size: .85rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px 20px;
        }

        .office-use .full {
            grid-column: 1 / -1;
        }

        .office-use label {
            display: block;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            margin-bottom: 4px;
            font-weight: 600;
        }

        .office-use input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #c7cadd;
            background: transparent;
            padding: 6px 2px;
            font-size: 16px;
            color: var(--ink);
        }

        .office-use input:focus {
            outline: none;
            border-bottom-color: var(--dole-blue);
        }

        /* ===== GRID / FIELDS ===== */
        .row {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 16px;
            margin-bottom: 18px;
        }

        .row:last-child {
            margin-bottom: 0;
        }

        .col-3 {
            grid-column: span 3;
        }

        .col-4 {
            grid-column: span 4;
        }

        .col-6 {
            grid-column: span 6;
        }

        .col-8 {
            grid-column: span 8;
        }

        .col-12 {
            grid-column: span 12;
        }

        @media (max-width:640px) {

            .col-3,
            .col-4,
            .col-6,
            .col-8 {
                grid-column: span 12;
            }

            .office-use {
                grid-template-columns: 1fr;
            }

            .row {
                gap: 14px;
            }
        }

        .field label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .field input[type="text"],
        .field input[type="number"],
        .field input[type="email"],
        .field input[type="date"],
        .field textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 11px 13px;
            font-size: 16px;
            font-family: inherit;
            color: var(--ink);
            background: #fbfbfd;
            transition: border-color .15s, box-shadow .15s;
        }

        .field input:focus,
        .field textarea:focus {
            outline: none;
            border-color: var(--dole-blue);
            box-shadow: 0 0 0 3px rgba(16, 5, 140, .1);
            background: #fff;
        }

        .field .hint {
            font-size: .76rem;
            color: var(--muted);
            margin-top: 5px;
        }

        /* ===== PILL / CHOICE GROUPS (client type, sex) ===== */
        .choice-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .choice-pill {
            position: relative;
        }

        .choice-pill input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .choice-pill label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border: 1.5px solid var(--line);
            border-radius: 999px;
            font-size: .86rem;
            font-weight: 500;
            cursor: pointer;
            background: #fbfbfd;
            transition: all .15s;
            user-select: none;
            margin: 0;
        }

        .choice-pill input:checked+label {
            border-color: var(--dole-blue);
            background: rgba(16, 5, 140, .07);
            color: var(--dole-blue);
            font-weight: 700;
        }

        .choice-pill label::before {
            content: "";
            width: 15px;
            height: 15px;
            border-radius: 50%;
            border: 1.5px solid #c3c6d6;
            flex-shrink: 0;
            transition: all .15s;
        }

        .choice-pill input:checked+label::before {
            border-color: var(--dole-blue);
            background: radial-gradient(circle, var(--dole-blue) 0 45%, transparent 50%);
        }

        /* ===== CC QUESTION BLOCKS ===== */
        .cc-block {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 14px;
            background: #fbfbfd;
        }

        .cc-block:last-child {
            margin-bottom: 0;
        }

        .cc-block .cc-tag {
            display: inline-block;
            background: var(--dole-blue);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 6px;
            margin-bottom: 8px;
            letter-spacing: .03em;
        }

        .cc-block .cc-question {
            font-size: .9rem;
            font-weight: 600;
            margin: 0 0 12px;
        }

        .cc-options {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .cc-options.two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px 20px;
        }

        @media (max-width:560px) {
            .cc-options.two-col {
                grid-template-columns: 1fr;
            }
        }

        .cc-option {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            font-size: .86rem;
            cursor: pointer;
        }

        .cc-option input {
            margin-top: 3px;
            accent-color: var(--dole-blue);
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            cursor: pointer;
        }

        /* ===== SQD SATISFACTION TABLE ===== */
        .sqd-table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 12px;
        }

        table.sqd-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
            font-size: .85rem;
        }

        table.sqd-table thead th {
            background: var(--dole-blue);
            color: #fff;
            font-weight: 600;
            font-size: .74rem;
            text-align: center;
            padding: 12px 8px;
            line-height: 1.25;
        }

        table.sqd-table thead th:first-child {
            text-align: left;
            padding-left: 16px;
            min-width: 260px;
        }

        table.sqd-table tbody td {
            padding: 12px 8px;
            text-align: center;
            border-top: 1px solid var(--line);
        }

        table.sqd-table tbody td:first-child {
            text-align: left;
            padding-left: 16px;
            font-weight: 500;
        }

        table.sqd-table tbody tr:nth-child(even) {
            background: #fafaff;
        }

        .sqd-emoji {
            font-size: 1.15rem;
            display: block;
            margin-bottom: 2px;
        }

        table.sqd-table input[type="radio"] {
            width: 17px;
            height: 17px;
            accent-color: var(--dole-blue);
            cursor: pointer;
        }

        .sqd-code {
            display: inline-block;
            background: rgba(16, 5, 140, .08);
            color: var(--dole-blue);
            font-weight: 700;
            font-size: .72rem;
            border-radius: 5px;
            padding: 1px 6px;
            margin-right: 6px;
        }

        /* ---- SQD table -> stacked cards below 760px ---- */
        @media (max-width:760px) {
            .sqd-table-wrap {
                overflow-x: visible;
                border: none;
                border-radius: 0;
            }

            table.sqd-table {
                min-width: 0;
                font-size: .88rem;
            }

            table.sqd-table thead {
                position: absolute;
                width: 1px;
                height: 1px;
                overflow: hidden;
                clip: rect(0 0 0 0);
                white-space: nowrap;
            }

            table.sqd-table,
            table.sqd-table tbody,
            table.sqd-table tr,
            table.sqd-table td {
                display: block;
                width: 100%;
            }

            table.sqd-table tbody tr {
                background: #fff;
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 14px 14px 6px;
                margin-bottom: 12px;
            }

            table.sqd-table tbody tr:nth-child(even) {
                background: #fff;
            }

            table.sqd-table tbody td:first-child {
                padding: 0 0 12px;
                font-weight: 600;
                font-size: .92rem;
                border-top: none;
            }

            table.sqd-table tbody td:not(:first-child) {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 2px;
                border-top: 1px solid #eef0f6;
                text-align: left;
            }

            table.sqd-table tbody td:not(:first-child)::before {
                content: attr(data-answer-label);
                font-size: .84rem;
                color: var(--muted);
                display: flex;
                align-items: center;
                gap: 8px;
            }

            table.sqd-table input[type="radio"] {
                width: 20px;
                height: 20px;
            }
        }

        /* ===== ACTIONS ===== */
        .action-row {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 13px 26px;
            font-size: .92rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: transform .1s, box-shadow .15s, background .15s;
            -webkit-tap-highlight-color: transparent;
        }

        @media (max-width:560px) {
            .action-row {
                flex-direction: column-reverse;
            }

            .action-row .btn {
                width: 100%;
            }
        }

        .btn-outline {
            background: #fff;
            border: 1.5px solid var(--line);
            color: var(--ink);
        }

        .btn-outline:hover {
            border-color: var(--dole-blue);
            color: var(--dole-blue);
        }

        .btn-primary {
            background: var(--dole-blue);
            color: #fff;
            box-shadow: 0 6px 16px rgba(16, 5, 140, .25);
        }

        .btn-primary:hover {
            background: #0c0470;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
        }

        .footer-note {
            text-align: center;
            font-size: .78rem;
            color: var(--muted);
            margin-top: 22px;
        }

        .section-divider-note {
            font-size: .78rem;
            color: var(--muted);
            background: #f4f6fb;
            border-left: 3px solid var(--dole-blue);
            padding: 9px 12px;
            border-radius: 6px;
            margin-bottom: 18px;
        }

        /* Success Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            opacity: 0;
            transition: .25s;
            z-index: 9999;
        }

        .modal-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        .modal-box {
            width: 420px;
            max-width: 92%;
            background: #fff;
            border-radius: 14px;
            padding: 35px 28px;
            text-align: center;
            animation: popup .25s ease;
        }

        .success-icon {
            width: 75px;
            height: 75px;
            margin: auto;
            border-radius: 50%;
            background: #28a745;
            color: #fff;
            font-size: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .modal-box h2 {
            margin: 0 0 12px;
            color: #10058C;
        }

        .modal-box p {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .modal-ref {
            font-size: .82rem;
            color: var(--muted);
            background: #f4f6fb;
            border-radius: 8px;
            padding: 8px 12px;
            margin: -10px 0 22px;
        }

        .modal-ref strong {
            color: var(--dole-blue);
        }

        .modal-btn {
            background: #10058C;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-family: inherit;
            font-size: .9rem;
        }

        .modal-btn:hover {
            background: #0d0473;
        }

        @keyframes popup {
            from {
                transform: scale(.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <main class="page-wrap">

        <!-- ===================== HEADER ===================== -->
        <div class="header-card">
            <div class="header-top">
                <div class="logo-badge">DOLE</div>
                <div>
                    <div class="agency-line">Department of Labor and Employment &middot; MIMAROPA</div>
                    <h1 class="header-title">Client Satisfaction Measurement (CSM)</h1>
                </div>
            </div>
            <p class="header-subtitle">Help us serve you better. This survey tracks your experience with our recently
                concluded transaction. Your feedback is confidential, and answering is always optional.</p>
            <div class="control-row">
                <span class="tag">Online Version</span>
                <span class="tag">Control No. — to be filled by DOLE personnel</span>
            </div>
        </div>

        <form method="post" action="<?= base_url('submit-csm') ?>" id="csmForm">
            <?= csrf_field() ?>
            <input type="hidden" name="reference_no" value="<?= esc($reference_no) ?>">

            <!-- ===================== TO BE ACCOMPLISHED BY DOLE PERSONNEL ===================== -->
            <div class="form-card">
                <p class="card-label">For DOLE Personnel Use</p>
                <p class="card-desc">To be accomplished by DOLE staff prior to handing this form to the client.</p>
                <div class="office-use">
                    <div>
                        <label for="officeName">Name of Office / Operating Unit</label>
                        <input type="text" id="officeName" name="office_name" value="DOLE MIMAROPA" readonly>
                    </div>
                    <div>
                        <label for="transactionNo">Number of Transaction</label>
                        <input type="text" id="transactionNo" name="transaction_no" value="1" readonly>
                    </div>
                    <div class="full">
                        <label for="service_availed">Name of Service Provided</label>
                        <input type="text" name="service_availed" value="<?= esc($service_availed) ?>" readonly>
                    </div>
                </div>
            </div>

            <!-- ===================== CLIENT PROFILE ===================== -->
            <div class="form-card">
                <p class="card-label">Client Profile</p>
                <p class="card-desc">Personal information shared will be kept confidential. You may choose not to
                    answer.</p>

                <div class="row">
                    <div class="col-12 field">
                        <label>Client Type <span class="required-mark">*</span></label>
                        <div class="choice-group">
                            <div class="choice-pill">
                                <input type="radio" name="client_type" id="clientCitizen" value="Citizen" required>
                                <label for="clientCitizen">Citizen</label>
                            </div>
                            <div class="choice-pill">
                                <input type="radio" name="client_type" id="clientBusiness" value="Business">
                                <label for="clientBusiness">Business</label>
                            </div>
                            <div class="choice-pill">
                                <input type="radio" name="client_type" id="clientGovernment" value="Government">
                                <label for="clientGovernment">Government (Employee or another agency)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-4 field">
                        <label for="csmDate">Date <span class="required-mark">*</span></label>
                        <input type="date" id="csmDate" name="date" required>
                    </div>
                    <div class="col-4 field">
                        <label>Sex <span class="required-mark">*</span></label>
                        <div class="choice-group">
                            <div class="choice-pill">
                                <input type="radio" name="sex" id="sexMale" value="Male" required>
                                <label for="sexMale">Male</label>
                            </div>
                            <div class="choice-pill">
                                <input type="radio" name="sex" id="sexFemale" value="Female">
                                <label for="sexFemale">Female</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 field">
                        <label for="csmAge">Age <span class="required-mark">*</span></label>
                        <input type="number" id="csmAge" name="age" min="0" max="120" placeholder="e.g. 34" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 field">
                        <label for="csmName">Name <span class="hint"
                                style="display:inline;font-weight:400;">(Optional)</span></label>
                        <input type="text" id="csmName" name="name" placeholder="Juan Dela Cruz">
                    </div>
                    <div class="col-6 field">
                        <label for="csmAddress">Address <span class="hint"
                                style="display:inline;font-weight:400;">(Optional)</span></label>
                        <input type="text" id="csmAddress" name="address" placeholder="City / Municipality">
                    </div>
                </div>
            </div>

            <!-- ===================== CITIZEN'S CHARTER ===================== -->
            <div class="form-card">
                <p class="card-label">Citizen's Charter (CC) Awareness</p>
                <p class="card-desc">The Citizen's Charter is an official document reflecting a government office's
                    services, requirements, fees, and processing times, among others.</p>

                <div class="cc-block">
                    <span class="cc-tag">CC1</span>
                    <p class="cc-question">Which of the following best describes your awareness of a CC?</p>
                    <div class="cc-options">
                        <label class="cc-option"><input type="radio" name="cc1" value="1"> I know what a CC is and I saw
                            this office's CC.</label>
                        <label class="cc-option"><input type="radio" name="cc1" value="2"> I know what a CC is but I did
                            NOT see this office's CC.</label>
                        <label class="cc-option"><input type="radio" name="cc1" value="3"> I learned of the CC only when
                            I saw this office's CC.</label>
                        <label class="cc-option"><input type="radio" name="cc1" value="4"> I do not know what a CC is
                            and I did not see one in this office. <em>(Answer 'N/A' on CC2 and CC3)</em></label>
                    </div>
                </div>

                <div class="cc-block">
                    <span class="cc-tag">CC2</span>
                    <p class="cc-question">If aware of CC (answered 1&ndash;3 in CC1), would you say that the CC of this
                        office was&hellip;?</p>
                    <div class="cc-options two-col">
                        <label class="cc-option"><input type="radio" name="cc2" value="1"> Easy to see</label>
                        <label class="cc-option"><input type="radio" name="cc2" value="4"> Not visible at all</label>
                        <label class="cc-option"><input type="radio" name="cc2" value="2"> Somewhat easy to see</label>
                        <label class="cc-option"><input type="radio" name="cc2" value="5"> Not Applicable</label>
                        <label class="cc-option"><input type="radio" name="cc2" value="3"> Difficult to see</label>
                    </div>
                </div>

                <div class="cc-block">
                    <span class="cc-tag">CC3</span>
                    <p class="cc-question">If aware of CC (answered codes 1&ndash;3 in CC1), how much did the CC help
                        you in your transaction?</p>
                    <div class="cc-options two-col">
                        <label class="cc-option"><input type="radio" name="cc3" value="1"> Helped very much</label>
                        <label class="cc-option"><input type="radio" name="cc3" value="3"> Did not help</label>
                        <label class="cc-option"><input type="radio" name="cc3" value="2"> Somewhat helped</label>
                        <label class="cc-option"><input type="radio" name="cc3" value="4"> Not Applicable</label>
                    </div>
                </div>
            </div>

            <!-- ===================== SQD SATISFACTION TABLE ===================== -->
            <div class="form-card">
                <p class="card-label">Service Quality Dimensions (SQD)</p>
                <p class="card-desc">For SQD0&ndash;8, please mark the column that best corresponds to your answer.</p>

                <div class="sqd-table-wrap">
                    <table class="sqd-table">
                        <thead>
                            <tr>
                                <th>Statement</th>
                                <th><span class="sqd-emoji">😄</span>Strongly Agree</th>
                                <th><span class="sqd-emoji">🙂</span>Agree</th>
                                <th><span class="sqd-emoji">😐</span>Neither Agree nor Disagree</th>
                                <th><span class="sqd-emoji">🙁</span>Disagree</th>
                                <th><span class="sqd-emoji">😣</span>Strongly Disagree</th>
                                <th>N/A<br>Not Applicable</th>
                            </tr>
                        </thead>
                        <tbody id="sqdTableBody">
                            <!-- rows injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===================== SUGGESTIONS ===================== -->
            <div class="form-card">
                <p class="card-label">Suggestions</p>
                <p class="card-desc">How can we further improve our services? <span class="hint"
                        style="display:inline;">(Optional)</span></p>

                <div class="row">
                    <div class="col-12 field">
                        <textarea id="suggestions" name="suggestions" rows="4"
                            placeholder="Share your suggestions here..."></textarea>
                    </div>
                    <div class="col-6 field">
                        <label for="csmEmail">Email address <span class="hint"
                                style="display:inline;font-weight:400;">(Optional)</span></label>
                        <input type="email" id="csmEmail" name="email" placeholder="name@example.com">
                    </div>
                </div>

                <div class="action-row">
                    <button type="reset" class="btn btn-outline">Clear Form</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Submit Feedback</button>
                </div>
            </div>

        </form>

        <p class="footer-note">DOLE MIMAROPA &middot; Client Satisfaction Measurement &middot; Thank you for helping us
            serve you better.</p>

        <!-- Success Modal -->
        <div id="successModal" class="modal-overlay">
            <div class="modal-box">
                <div class="success-icon">✓</div>

                <h2>Successfully Filled Up!</h2>

                <p>
                    Thank you for completing the Client Satisfaction Measurement (CSM) form.
                    Your feedback has been successfully recorded.
                </p>

                <p class="modal-ref">Reference No.: <strong><?= esc($reference_no ?? '') ?></strong></p>

                <button id="btnSuccessOk" class="modal-btn" type="button">
                    Continue
                </button>
            </div>
        </div>

    </main>


    <script>
        (function () {
            'use strict';

            const sqdItems = [
                ["SQD0", "I am satisfied with the service that I availed."],
                ["SQD1", "I spent a reasonable amount of time for my transaction."],
                ["SQD2", "The office followed the transaction's requirements and steps based on the information provided."],
                ["SQD3", "The steps (including payment) I needed to do for my transaction were easy and simple."],
                ["SQD4", "I easily found information about my transaction from the office's website."],
                ["SQD5", "I paid a reasonable amount of fees for my transaction. (If service was free, mark the 'N/A' column)"],
                ["SQD6", "I am confident my online transaction was secure."],
                ["SQD7", "The office's online support was available, and (if asked questions) online support was quick to respond."],
                ["SQD8", "I got what I needed from the government office, or (if denied) denial of request was sufficiently explained to me."]
            ];

            const answers = [
                { value: "strongly_agree", label: "😄 Strongly Agree" },
                { value: "agree", label: "🙂 Agree" },
                { value: "neutral", label: "😐 Neither Agree nor Disagree" },
                { value: "disagree", label: "🙁 Disagree" },
                { value: "strongly_disagree", label: "😣 Strongly Disagree" },
                { value: "na", label: "— N/A" }
            ];
            const tbody = document.getElementById('sqdTableBody');

            sqdItems.forEach(function (item) {
                const code = item[0];
                const text = item[1];
                const tr = document.createElement('tr');

                const labelTd = document.createElement('td');
                labelTd.innerHTML = '<span class="sqd-code">' + code + '</span>' + text;
                tr.appendChild(labelTd);

                answers.forEach(function (ans) {
                    const td = document.createElement('td');
                    td.setAttribute('data-answer-label', ans.label);
                    const input = document.createElement('input');
                    input.type = 'radio';
                    input.name = code.toLowerCase();
                    input.value = ans.value;
                    input.required = true;
                    td.appendChild(input);
                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });

            // Default date to today
            const dateField = document.getElementById('csmDate');
            if (dateField) {
                const today = new Date();
                const iso = today.toISOString().slice(0, 10);
                dateField.value = iso;
            }

            const form = document.getElementById('csmForm');
            const modal = document.getElementById('successModal');
            const btnSubmit = document.getElementById('btnSubmit');
            const btnSuccessOk = document.getElementById('btnSuccessOk');

            function showSuccessModal() {
                modal.classList.add('show');
            }

            function hideSuccessModal() {
                modal.classList.remove('show');
            }

            form.addEventListener('submit', function (e) {
                // Let native HTML5 validation run first; if the form is invalid,
                // the browser blocks submission and this handler exits safely.
                if (!form.checkValidity()) {
                    return;
                }

                e.preventDefault();

                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Submitting...';

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Request failed with status ' + response.status);
                        }
                        showSuccessModal();
                        form.reset();
                    })
                    .catch(function (err) {
                        alert('Something went wrong while submitting your feedback. Please try again.');
                        console.error(err);
                    })
                    .finally(function () {
                        btnSubmit.disabled = false;
                        btnSubmit.textContent = 'Submit Feedback';
                    });
            });

            // Where to send the client if this tab can't be closed
            // (e.g. it wasn't opened via script, so window.close() is blocked by the browser).
            const fallbackRedirectUrl = <?= json_encode($redirect_url ?? base_url('/')) ?>;

            btnSuccessOk.addEventListener('click', function () {
                hideSuccessModal();

                // Try to close this tab/window (works if it was opened via a link
                // target="_blank" from the email, or via window.open()).
                window.close();

                // If we're still here after a brief delay, the browser blocked the close
                // (most browsers won't let a script close a tab it didn't open).
                // Fall back to sending the client to a safe landing page.
                setTimeout(function () {
                    window.location.href = fallbackRedirectUrl;
                }, 300);
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    hideSuccessModal();
                }
            });
        })();
    </script>

</body>

</html>