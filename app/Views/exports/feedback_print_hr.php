<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSF Form No. 3 - <?= esc($feedback['reference_no']) ?></title>
    <meta name="description"
        content="One portal, multiple digital government services. Submit requests, access HR services, and connect with DOLE MIMAROPA online.">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/doleicon.png">
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #e9eaee;
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            color: #000;
        }

        /* ===================== TOOLBAR ===================== */
        .toolbar {
            max-width: 210mm;
            margin: 0 auto;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border-bottom: 1px solid #dcdcdc;
        }

        .toolbar h1 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: #1b3a63;
        }

        .toolbar .sub {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
        }

        .tbtn {
            border: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }

        .tbtn.primary {
            background: #1b3a63;
            color: #fff;
        }

        .tbtn.primary:hover {
            background: #142c4c;
        }

        /* ===================== PAGE (A4) ===================== */
        .page-wrap {
            padding: 20px 0 60px;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, .2);
            padding: 10mm;
        }

        .sheet-border {
            border: 2.5px solid #000;
            padding: 8px 10px;
            height: 100%;
        }

        @media print {

            html,
            body {
                background: #fff;
                font-size: 8.3pt;
            }

            .no-print {
                display: none !important;
            }

            .page-wrap {
                padding: 0;
            }

            .sheet {
                width: auto;
                min-height: 0;
                box-shadow: none;
                padding: 6mm;
                margin: 0;
            }

            .sheet-border {
                border: 2.5px solid #000;
                padding: 6px 8px;
            }

            /* Preserve background colors when printing */
            .thankyou-bar {
                background: #1b3a63 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .instruction-bar {
                background: #1b3a63 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .client-info-title {
                background: #f2f2f2 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.rating-table th {
                background: #f2f2f2 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* ===================== HEADER ===================== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .header-table tr {
            height: 70px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 4px 6px;
        }

        .logo-cell {
            width: 70px;
            text-align: center;

        }

        .logo-cell img {
            width: 75px;
            height: auto;
            vertical-align: middle;
            margin-left: 60px;
        }

        .agency-title {
            text-align: center;
            padding: 4px 20px;

        }

        .agency-title .country {
            font-size: 11pt;
            margin: 0;
        }

        .agency-title .dept {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
        }

        .agency-title .region {
            font-size: 11pt;
            margin: 0;
            letter-spacing: .5px;
        }

        .cert-cell {
            width: 90px;
            text-align: center;
            padding: 4px;
        }

        .cert-cell img {
            width: 70px;
            height: auto;
            vertical-align: middle;
            margin-right: 60px;
        }

        /* ===================== FORM CONTENT ===================== */
        .form-caption {
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            letter-spacing: .3px;
            margin: 2px 0 4px;
        }

        .thankyou-bar {
            background: #1b3a63;
            color: #fff;
            text-align: center;
            padding: 4px 8px;
            font-size: 8.5pt;
            line-height: 1.35;
            margin-bottom: 4px;
        }

        .client-info-box {
            border: 1px solid #000;
            margin-bottom: 4px;
        }

        .client-info-title {
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 2px;
            font-size: 8.5pt;
            background: #f2f2f2;
        }

        .client-info-grid {
            display: table;
            width: 100%;
        }

        .client-info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 4px 8px;
        }

        .client-info-col+.client-info-col {
            border-left: 1px solid #000;
        }

        .ci-line {
            margin: 2px 0;
        }

        .checkbox {
            display: inline-block;
            width: 9px;
            height: 9px;
            border: 1px solid #000;
            text-align: center;
            line-height: 8px;
            font-size: 7.5pt;
            margin-right: 4px;
            vertical-align: middle;
        }

        .fill-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 160px;
            padding: 0 2px;
            font-weight: bold;
        }

        .instruction-bar {
            background: #1b3a63;
            color: #fff;
            padding: 3px 8px;
            font-size: 8.5pt;
            text-align: justify;
            margin-bottom: 4px;
        }

        .service-box {
            border: 1px solid #000;
            padding: 4px 8px;
            margin-bottom: 4px;
            background: #d8e1f2;

        }

        .service-title {
            font-weight: bold;
            font-size: 8.5pt;
            margin-bottom: 2px;
        }

        .service-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .service-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0;

            /* ← Divider between columns */
        }

        .service-col:first-child {
            padding-right: 8px;
            /* ← Right spacing for left column */
        }

        .service-item {
            margin: 2px 0;
            line-height: 1.3;
            display: flex;
            /* ← Flexbox for alignment */
            align-items: flex-start;
            /* ← Checkbox aligns with text top */
            gap: 4px;
            /* ← Spacing between checkbox and text */
        }

        .service-item .checkbox {
            flex-shrink: 0;
            /* ← Checkbox stays fixed size */
            margin-top: 1px;
            /* ← Fine-tune vertical alignment */
        }

        .others-line {
            margin-top: 3px;
            padding-top: 3px;
            display: flex;
            /* ← Flexbox alignment */
            align-items: center;
            gap: 4px;
        }

        table.rating-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            font-size: 8pt;
        }

        table.rating-table th,
        table.rating-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            text-align: center;
            vertical-align: middle;
        }

        table.rating-table th {
            font-weight: normal;
            font-size: 7.5pt;
            background: #f2f2f2;
        }

        table.rating-table td.particular {
            text-align: left;
            width: 34%;
            line-height: 1.2;
            font-size: 8pt;
        }

        table.rating-table .rating-checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            text-align: center;
            line-height: 9px;
            font-size: 8pt;
            vertical-align: middle;
        }

        table.rating-table td.na {
            font-weight: bold;
        }

        table.bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            font-size: 8pt;
            background: #d8e1f2;
        }

        table.bottom-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            height: 44px;
            background: #d8e1f2;
        }

        table.bottom-table .label {
            font-weight: normal;
            display: block;
            margin-bottom: 2px;
        }

        .signature-box {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 8.5pt;
            justify-content: center;
            text-align: center;
            background: #d8e1f2;
        }

        .signature-box .sig-line {
            display: inline-block;
            min-width: 240px;
            border-bottom: 1px solid #000;
            text-align: center;
            justify-content: center;
        }

        .form-no {
            text-align: right;
            font-size: 7.5px;
            margin-top: 2px;
        }

        .ref-line {
            text-align: right;
            font-size: 8px;
            font-weight: bold;
            margin-top: 2px;
        }
    </style>
</head>

<body>

    <!-- ===================== TOOLBAR ===================== -->
    <div class="toolbar no-print">
        <div>
            <h1>CSM Questionnaire &mdash; English</h1>
            <div class="sub">Official-layout replica of the DOLE MIMAROPA paper form, ready for printing.</div>
        </div>
        <div class="toolbar-actions">
            <button type="button" class="tbtn primary" onclick="window.location.href='<?= base_url('feedback') ?>'">
                Back
            </button>
            <button class="tbtn primary" onclick="window.print()">&#128424;&#65039; Print Form</button>
        </div>
    </div>

    <div class="page-wrap">
        <div class="sheet">
            <div class="sheet-border">

                <table class="header-table">
                    <tr>
                        <td class="logo-cell"><img src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE"></td>
                        <td class="agency-title">
                            <p class="country">Republic of the Philippines</p>
                            <p class="dept">DEPARTMENT OF LABOR AND EMPLOYMENT</p>
                            <p class="region">MIMAROPA REGION</p>
                        </td>
                        <td class="cert-cell"><img src="<?= base_url('assets/img/iso.png') ?>"
                                alt="ISO 9001:2015 Certification"></td>
                    </tr>
                </table>

                <p class="form-caption">CLIENT FEEDBACK TO IMSD-HR UNIT</p>

                <div class="thankyou-bar">
                    Thank you for giving us the opportunity to serve you.<br>
                    Please help us improve the quality of our services by taking a few minutes to tell us about your
                    experience with us.
                </div>

                <div class="client-info-box">
                    <div class="client-info-title">CLIENT INFORMATION</div>
                    <div class="client-info-grid">
                        <div class="client-info-col">
                            <div class="ci-line">Client Type:</div>
                            <div class="ci-line">
                                <span
                                    class="checkbox"><?= $feedback['client_type'] === 'General Public' ? '&#10003;' : '' ?></span>
                                General Public
                            </div>
                            <div class="ci-line">
                                <span
                                    class="checkbox"><?= $feedback['client_type'] === 'Business' ? '&#10003;' : '' ?></span>
                                Business
                            </div>
                            <div class="ci-line" style="margin-left:16px;">Name of Business: <span
                                    class="fill-line"><?= esc($feedback['business_name'] ?: '') ?></span></div>
                            <div class="ci-line">
                                <span
                                    class="checkbox"><?= $feedback['client_type'] === 'Government Agency/Official/Employee' ? '&#10003;' : '' ?></span>
                                Government Agency/Official/Employee
                            </div>
                            <div class="ci-line" style="margin-left:16px;">Name of Government Agency: <span
                                    class="fill-line"><?= esc($feedback['government_agency'] ?: '') ?></span></div>
                        </div>
                        <div class="client-info-col">
                            <div class="ci-line">Name: <span class="fill-line"
                                    style="min-width:220px;"><?= esc($feedback['name']) ?></span></div>
                            <div class="ci-line">Contact Number: <span class="fill-line"
                                    style="min-width:180px;"><?= esc($feedback['contact_number'] ?: '') ?></span></div>
                            <div class="ci-line">Email Address: <span class="fill-line"
                                    style="min-width:200px;"><?= esc($feedback['email_address'] ?: '') ?></span></div>
                            <div class="ci-line">Office Visited: <span class="fill-line"
                                    style="min-width:200px;"><?= esc($feedback['office_visited'] ?: '') ?></span></div>
                            <div class="ci-line">Date: <span class="fill-line"
                                    style="min-width:140px;"><?= esc(date('F d, Y', strtotime($feedback['date']))) ?></span>
                            </div>
                            <div class="ci-line">
                                Sex:
                                <span class="checkbox"><?= $feedback['sex'] === 'Male' ? '&#10003;' : '' ?></span> Male
                                <span class="checkbox"><?= $feedback['sex'] === 'Female' ? '&#10003;' : '' ?></span>
                                Female
                            </div>
                        </div>
                    </div>
                </div>

                <div class="instruction-bar">
                    Please put a check mark (&#10003;) in the appropriate box corresponding to your response.
                    (Pakilagyan ng tsek (&#10003;) ang kahon na nagsasaad ng iyong tugon.)
                </div>

                <?php
                $selectedServices = array_map('trim', explode(',', (string) $feedback['service_availed']));
                $allServices = [
                    'Application for Alien Employment Permit (New/Renewal)',
                    'Application for Authority to Operate Branch Office of a Private Employment Agency',
                    'Application for Authority to Recruit',
                    'Application for License to Operate Private Employment Agency (PEA)',
                    'Application for Livelihood Project Assistance',
                    'Application for Job Fair Clearance',
                    'Application for Job Fair Permit',
                    "Application for Sugar Workers' Death Benefit Claim",
                    "Application for Sugar Workers' Maternity Benefit Claim",
                ];
                $allServicesRight = [
                    'Issuance of Certificate of Appearance for Professional Mechanical Engineer/Professional Electrical Engineer',
                    'Issuance of Certificate of Exclusion from Alien Employment Permit',
                    'Issuance of Letter of Approval/Disapproval of Construction Safety and Health Program (CSHP) Application',
                    'Issuance of Permit to Operate Mechanical Installation/Certificate of Electrical Inspection (CEI)',
                    'Registration of Establishment under Rule 1020 of the Occupational Safety and Health Standards',
                    'Registration of Collective Bargaining Agreement',
                    'Registration of Contractors',
                    'Registration of Union',
                    "Registration of Workers' Association",
                ];
                ?>

                <div class="service-box">
                    <div class="service-title">SERVICE AVAILED (Serbisyong Kinailangan):</div>
                    <div class="service-grid">
                        <div class="service-col">
                            <?php foreach ($allServices as $svc): ?>
                                <div class="service-item">
                                    <span class="checkbox"><?= in_array($svc, $selectedServices) ? '&#10003;' : '' ?></span>
                                    <?= esc($svc) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="service-col">
                            <?php foreach ($allServicesRight as $svc): ?>
                                <div class="service-item">
                                    <span class="checkbox"><?= in_array($svc, $selectedServices) ? '&#10003;' : '' ?></span>
                                    <?= esc($svc) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="others-line">Others, please specify: <span class="fill-line"
                            style="min-width:320px;"><?= esc($feedback['other_service'] ?: '') ?></span></div>
                </div>

                <?php
                $particulars = [
                    1 => "The requested service/s is/are delivered in a timely manner (Ang hinihinging serbisyo ay naibigay sa takdang oras)",
                    2 => "The client's queries/needs is/are properly responded to by the concerned personnel or unit (Nakatugon ng maayos sa katanungan/pangangailangan)",
                    3 => "The service/s and facility/ies is/are easily accessible (Maayos na serbisyo o pasilidad)",
                    4 => "The instructions are clearly indicated in the Citizen's Charter (Malinaw at nakaayon ang instruksyon sa Citizen's Charter)",
                    5 => "The fees are reasonable (Resonable ang hinihinging bayad)",
                    6 => "The client feels safe and secured in doing the transaction (Walang alinlangan sa pakikipag-transaksyon)",
                    7 => "The Action Officer is knowledgeable on the DOLE programs/services (Ang Action Officer ay may sapat na kaalaman sa programa/serbisyo ng DOLE)",
                    8 => "The client is satisfied with the service/s received (Nasiyahan sa serbisyong natanggap)",
                ];
                $ratingCols = ['Strongly agree', 'Agree', 'Neither', 'Disagree', 'Strongly disagree'];
                ?>

                <table class="rating-table">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Particulars (Mga Detalye)</th>
                            <th>Strongly<br>Agree</th>
                            <th>Agree</th>
                            <th>Neither Agree<br>nor Disagree</th>
                            <th>Disagree</th>
                            <th>Strongly<br>Disagree</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($particulars as $num => $text): ?>
                            <?php $ratingValue = $feedback['rating_' . $num]; ?>
                            <tr>
                                <td class="particular"><?= $num ?>. <?= esc($text) ?></td>
                                <?php if ($ratingValue === 'N/A'): ?>
                                    <td colspan="5" class="na">N/A</td>
                                <?php else: ?>
                                    <?php foreach ($ratingCols as $col): ?>
                                        <td>
                                            <span
                                                class="rating-checkbox"><?= strtolower(trim((string) $ratingValue)) === strtolower(trim($col)) ? '&#10003;' : '' ?></span>
                                        </td>
                                        </td>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <table class="bottom-table">
                    <tr>
                        <td style="width:50%;">
                            <span class="label">Complaint about our service (Reklamo sa serbisyong ibinigay):</span>
                            <?= esc($feedback['complaint'] ?: '') ?>
                        </td>
                        <td style="width:50%;">
                            <span class="label">Comment/Suggestion/Recommendation (Mungkahi/Rekomendasyon)</span>
                            <?= esc($feedback['suggestions'] ?: '') ?>
                        </td>
                    </tr>
                </table>

                <div class="signature-box">
                    Signature (Lagda): <span class="sig-line"><?= esc($feedback['name']) ?></span>
                </div>

                <p class="ref-line">Reference No.: <?= esc($feedback['reference_no']) ?></p>
                <p class="form-no">CSF Form No. 3 (Rev 00 - 15 January 2021)</p>

            </div>
        </div>
    </div>

</body>

</html>