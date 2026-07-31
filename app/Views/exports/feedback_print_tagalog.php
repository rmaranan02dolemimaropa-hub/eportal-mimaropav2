<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSM Questionnaire &ndash; Tagalog (Print Version)</title>
    <style>
        :root {
            --dole-blue: #10058C;
            --ink: #000;
            --line: #000;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            margin: 0;
            background: #5a5c66;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
        }

        /* ===================== TOOLBAR (screen only) ===================== */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #1c1d21;
            color: #fff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .25);
        }

        .toolbar h1 {
            font-size: 1rem;
            margin: 0;
            font-weight: 700;
        }

        .toolbar .sub {
            font-size: .76rem;
            color: #b8b9c4;
            margin-top: 2px;
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tbtn {
            border: 1.5px solid #3a3b45;
            background: #26272f;
            color: #fff;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background .15s, border-color .15s;
        }

        .tbtn:hover {
            border-color: #5a5cff;
        }

        .tbtn.primary {
            background: var(--dole-blue);
            border-color: var(--dole-blue);
        }

        .tbtn.primary:hover {
            background: #2a1fb0;
        }

        /* ===================== SCREEN LAYOUT (sheet preview) ===================== */
        .sheets {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 32px;
            padding: 32px 16px 64px;
        }

        .sheet-label {
            color: #e7e7ee;
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: -14px;
        }

        .page {
            background: #fff;
            width: 816px;
            /* 8.5in @ 96dpi */
            min-height: 1056px;
            /* 11in @ 96dpi */
            padding: 34px 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .35);
            font-size: 10.4px;
            line-height: 1.35;
        }

        /* ===================== FORM CONTENT (shared) ===================== */
        .ctrl-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 6px;
            font-size: 11px;
        }

        .ctrl-row .ver {
            font-weight: 700;
        }

        table.top-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        table.top-table td {
            border: 1px solid var(--line);
            padding: 5px 8px;
            vertical-align: middle;
            font-size: 10.2px;
        }

        table.top-table td.dole-cell {
            background: #000;
            color: #fff;
            font-weight: 700;
            width: 130px;
            text-align: left;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color-adjust: exact;
            /* older Firefox */
        }

        .brand-row {
            text-align: center;
            margin: 6px 0 10px;
        }

        .brand-row .logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .brand-text .agency {
            font-size: 10.5px;
        }

        .brand-text .tagline {
            font-size: 12.5px;
            font-weight: 800;
            margin-top: 1px;
        }

        .intro-text {
            font-size: 9.6px;
            margin: 0 0 8px;
            text-align: justify;
        }

        .intro-text u {
            text-underline-offset: 2px;
        }

        .inline-field {
            font-size: 10px;
            margin-bottom: 6px;
        }

        .inline-field strong {
            font-weight: 700;
        }

        .box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            margin: 0 4px 0 10px;
            position: relative;
            top: 1px;
            font-size: 9px;
            line-height: 1;
            font-weight: 800;
            color: #000;
            overflow: hidden;
            flex-shrink: 0;
        }

        .box:first-of-type {
            margin-left: 6px;
        }

        .fill-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 90px;
            height: 10px;
        }

        hr.divider {
            border: none;
            border-top: 1px solid #000;
            margin: 8px 0;
        }

        .instructions {
            font-size: 9.6px;
            margin: 6px 0 6px;
        }

        .instructions strong {
            font-weight: 700;
        }

        table.cc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9.6px;
        }

        table.cc-table td {
            border: 1px solid var(--line);
            padding: 6px 8px;
            vertical-align: top;
        }

        table.cc-table td.cc-code {
            width: 34px;
            font-weight: 700;
            text-align: left;
        }

        table.cc-table .cc-opt {
            margin: 2px 0;
        }

        table.cc-table .cc-two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 14px;
        }

        table.sqd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.2px;
            margin-bottom: 10px;
        }

        table.sqd-table th,
        table.sqd-table td {
            border: 1px solid var(--line);
            padding: 4px 6px;
        }

        table.sqd-table thead th {
            text-align: center;
            font-weight: 700;
            vertical-align: bottom;
            font-size: 8.6px;
            padding: 5px 4px;
        }

        table.sqd-table thead th:first-child {
            width: 34%;
        }

        table.sqd-table tbody td:not(:first-child) {
            width: 9.5%;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
            font-weight: 700;
            color: #000;
        }

        table.sqd-table .emoji {
            font-size: 15px;
            display: block;
            line-height: 1;
            margin-bottom: 2px;
        }

        table.sqd-table tbody td:first-child {
            text-align: left;
        }

        .suggestions-block {
            font-size: 9.8px;
            margin-top: 4px;
        }

        .suggest-line {
            border-bottom: 1px solid #000;
            height: 16px;
            margin-bottom: 2px;
        }

        .email-line {
            font-size: 9.8px;
            margin-top: 8px;
        }

        .thank-you {
            text-align: center;
            font-weight: 700;
            margin-top: 12px;
            font-size: 11px;
        }

        /* ===================== PRINT RULES ===================== */
        @media print {
            body {
                background: #fff;
            }

            .toolbar {
                display: none !important;
            }

            .sheets {
                padding: 0;
                gap: 0;
            }

            .sheet-label {
                display: none;
            }

            .page {
                box-shadow: none;
                width: auto;
                min-height: 0;
                padding: 0.35in 0.45in;
            }
        }

        @page {
            size: letter portrait;
            margin: 0;
        }
    </style>
</head>

<body>

    <!-- ===================== TOOLBAR ===================== -->
    <div class="toolbar no-print">
        <div>
            <h1>CSM Questionnaire &mdash; Tagalog</h1>
            <div class="sub">Official-layout replica of the DOLE MIMAROPA paper form, ready for printing.</div>
        </div>
        <div class="toolbar-actions">
            <button type="button" class="tbtn primary" onclick="window.location.href='<?= base_url('feedback') ?>'">
                Back
            </button>
            <button class="tbtn primary" onclick="window.print()">🖨️ Print Form</button>
        </div>
    </div>

    <div class="sheets">

        <!-- ===================== TAGALOG SHEET ===================== -->
        <div class="sheet-label">Tagalog &middot; Preview</div>
        <div class="page" id="formTagalog">

            <div class="ctrl-row">
                <div>Control No: <span class="fill-line" style="min-width:110px;"></span></div>
                <div class="ver">(Online Version)</div>
            </div>

            <table class="top-table">
                <tr>
                    <td class="dole-cell" rowspan="2">Para sa Pagkumpleto<br>ng mga Kawani ng DOLE</td>
                    <td colspan="2">Pangalan ng Opisina/Yunit na Operatibo:
                        <?= esc($feedback['office_name']) ?>
                    </td>
                </tr>
                <tr>
                    <td style="width:50%;">Pangalan ng Serbisyong Ibinigay:
                        <?= esc($feedback['service_availed']) ?>
                    </td>
                    <td>Bilang ng Transaksyon:
                        <?= esc($feedback['transaction_no']) ?>
                    </td>
                </tr>
            </table>

            <div class="brand-row">
                <div class="logos">
                    <img src="<?= base_url('assets/img/dole_mimaropa.png') ?>" alt="DOLE Logo" class="brand-logo">
                    <div class="brand-text">
                        <div class="agency">Department of Labor and Employment</div>
                        <div class="tagline">TULUNGAN NINYO KAMING MAS MAPABUTI ANG AMING SERBISYO!</div>
                    </div>
                    <img src="<?= base_url('assets/img/bagong_pilipinas.png') ?>" alt="Bagong Pilipinas Logo"
                        class="brand-logo">
                </div>
            </div>

            <p class="intro-text">Ang Client Satisfaction Measurement (CSM) ay naglalayong masubaybayan ang karanasan ng
                taumbayan hinggil sa kanilang pakikitransaksyon sa mga tanggapan ng gobyerno. Makatutulong ang inyong
                kasagutan ukol sa inyong naging karanasan sa <u>kakatapos lamang na transaksyon,</u> upang mas mapabuti
                at lalong mapahusay ang aming serbisyo publiko. Ang personal na impormasyon na iyong ibabahagi ay
                mananatiling kumpidensyal. Maaari ring piliin na hindi sagutan ang sarbey na ito.</p>

            <div class="inline-field">
                <strong>Uri ng Kliyente:</strong>
                <span class="box">
                    <?= $feedback['client_type'] == 'Mamamayan' ? '✓' : '' ?>
                </span> Mamamayan
                <span class="box">
                    <?= $feedback['client_type'] == 'Negosyo' ? '✓' : '' ?>
                </span> Negosyo
                <span class="box">
                    <?= $feedback['client_type'] == 'Gobyerno' ? '✓' : '' ?>
                </span> Gobyerno (Empleyado o Ahensya)
            </div>

            <div class="inline-field">
                <strong>Petsa:</strong>
                <span style="display:inline-block; border-bottom:1px solid #000; min-width:100px; text-align: center;">
                    <?= date('F d, Y', strtotime($feedback['date'])) ?>
                </span>
                &nbsp;&nbsp;<strong>Kasarian:</strong>
                <span class="box">
                    <?= $feedback['sex'] == 'Lalaki' ? '✓' : '' ?>
                </span> Lalaki
                <span class="box">
                    <?= $feedback['sex'] == 'Babae' ? '✓' : '' ?>
                </span> Babae
                &nbsp;&nbsp;<strong>Edad:</strong>
                <span style="display:inline-block; border-bottom:1px solid #000; min-width:50px; text-align: center;">
                    <?= esc($feedback['age']) ?>
                </span>
            </div>

            <div class="inline-field">
                <strong>Pangalan:</strong> (Opsyonal)
                <span style="display:inline-block; border-bottom:1px solid #000; min-width:120px; text-align: center;">
                    <?= esc($feedback['name']) ?>
                </span>
                &nbsp;&nbsp;<strong>Tirahan</strong> (Opsyonal):
                <span style="display:inline-block; border-bottom:1px solid #000; min-width:80px; text-align: center;">
                    <?= esc($feedback['address']) ?>
                </span>
            </div>

            <hr class="divider">

            <p class="instructions"><strong>PANUTO:</strong> Lagyan ng tsek (&#10003;) sa itinalagang kahon ng iyong
                sagot sa mga sumusunod na katanungan tungkol sa Citizen&rsquo;s Charter (CC). Ito ay isang opisyal na
                dokumento na naglalaman ng mga serbisyo sa isang ahensya/opisina ng gobyerno, makikita rito ang mga
                kinakailangan na dokumento, kaukulang bayarin, at pangkabuuang oras ng pagproseso.</p>

            <table class="cc-table">
                <tr>
                    <td class="cc-code">CC1</td>
                    <td>
                        Alin sa mga sumusunod ang naglalarawan sa iyong kaalaman sa CC?
                        <div class="cc-opt"><span class="box">
                                <?= $feedback['cc1'] == 1 ? '✓' : '' ?>
                            </span> 1. Alam ko ang CC at nakita ko ito sa napuntahang opisina</div>
                        <div class="cc-opt"><span class="box">
                                <?= $feedback['cc1'] == 2 ? '✓' : '' ?>
                            </span> 2. Alam ko ang CC pero hindi ko ito nakita sa napuntahang opisina</div>
                        <div class="cc-opt"><span class="box">
                                <?= $feedback['cc1'] == 3 ? '✓' : '' ?>
                            </span> 3. Nalaman ko ang CC nang makita ko ito sa napuntahang opisina</div>
                        <div class="cc-opt"><span class="box">
                                <?= $feedback['cc1'] == 4 ? '✓' : '' ?>
                            </span> 4. Hindi ko alam kung ano ang CC at wala akong nakita sa napuntahang opisina (Lagyan
                            ng tsek ang &lsquo;N/A&rsquo; sa CC2 at CC3 kapag ito ang iyong sagot)</div>
                    </td>
                </tr>
                <tr>
                    <td class="cc-code">CC2</td>
                    <td>
                        Kung alam ang CC (Nag-tsek sa opsyon 1-3 sa CC1), masasabi mo ba na ang CC nang napuntahang
                        opisina ay&hellip;
                        <div class="cc-two-col">
                            <div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc2'] == 1 ? '✓' : '' ?>
                                    </span> 1. Madaling makita</div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc2'] == 2 ? '✓' : '' ?>
                                    </span> 2. Medyo madaling makita</div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc2'] == 3 ? '✓' : '' ?>
                                    </span> 3. Mahirap makita</div>
                            </div>
                            <div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc2'] == 4 ? '✓' : '' ?>
                                    </span> 4. Hindi makita</div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc2'] == 5 ? '✓' : '' ?>
                                    </span> 5. Hindi angkop</div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="cc-code">CC3</td>
                    <td>
                        Kung alam ang CC (nag-tsek sa opsyon 1-3 sa CC1), gaano nakatulong ang CC sa transaksyon mo?
                        <div class="cc-two-col">
                            <div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc3'] == 1 ? '✓' : '' ?>
                                    </span> 1. Sobrang nakatulong</div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc3'] == 2 ? '✓' : '' ?>
                                    </span> 2. Nakatulong naman</div>
                            </div>
                            <div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc3'] == 3 ? '✓' : '' ?>
                                    </span> 3. Hindi nakatulong</div>
                                <div class="cc-opt"><span class="box">
                                        <?= $feedback['cc3'] == 4 ? '✓' : '' ?>
                                    </span> 4. Hindi angkop</div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <p class="instructions"><strong>PANUTO:</strong><br>Para sa SQD 0-8, lagyan ng tsek (&#10003;) ang hanay na
                pinakaangkop sa iyong sagot.</p>

            <table class="sqd-table">
                <thead>
                    <tr>
                        <th></th>
                        <th><span class="emoji">&#128512;</span>Labis na Sumasangayon</th>
                        <th><span class="emoji">&#128578;</span>Sumasangayon</th>
                        <th><span class="emoji">&#128528;</span>Walang Kinikilingan</th>
                        <th><span class="emoji">&#128577;</span>Hindi Sumasangayon</th>
                        <th><span class="emoji">&#128548;</span>Lubos na Hindi Sumasangayon</th>
                        <th>N/A<br>Not Applicable</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>SQD0.</strong> Nasiyahan ako sa serbisyo na aking natanggap sa napuntahan na
                            tanggapan.</td>
                        <?php
                        function checkSQD($value, $answer)
                        {
                            return (string) $value === (string) $answer ? '✓' : '';
                        }
                        ?>
                        <td>
                            <?= checkSQD($feedback['sqd0'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd0'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd0'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd0'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd0'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd0'], 'na') ?>
                        </td>
                    </tr>

                    <tr>
                        <td><strong>SQD1.</strong> Makatwiran ang oras na aking ginugol para sa pagproseso ng aking
                            transaksyon.</td>
                        <td>
                            <?= checkSQD($feedback['sqd1'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd1'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd1'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd1'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd1'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd1'], 'na') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>SQD2.</strong> Ang opisina ay sumusunod sa mga kinakailangang dokumento at mga
                            hakbang batay sa impormasyong ibinigay.</td>
                        <td>
                            <?= checkSQD($feedback['sqd2'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd2'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd2'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd2'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd2'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd2'], 'na') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>SQD3.</strong> Ang mga hakbang sa pagproseso, kasama na ang pagbayad ay madali at
                            simple lamang.</td>
                        <td>
                            <?= checkSQD($feedback['sqd3'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd3'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd3'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd3'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd3'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd3'], 'na') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>SQD4.</strong> Mabilis at madali akong nakahanap ng impormasyon tungkol sa aking
                            transaksyon mula sa opisina o sa website nito.</td>
                        <td>
                            <?= checkSQD($feedback['sqd4'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd4'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd4'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd4'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd4'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd4'], 'na') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>SQD5.</strong> Nagbayad ako ng makatwirang halaga para sa aking transaksyon.
                            <em>(Kung ang sebisyo ay ibinigay ng libre, maglagay ng tsek sa hanay ng N/A.)</em>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd5'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd5'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd5'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd5'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd5'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd5'], 'na') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>SQD6.</strong> Pakiramdam ko ay patas ang opisina sa lahat, o &ldquo;walang
                            palakasan&rdquo;, sa aking transaksyon.</td>
                        <td>
                            <?= checkSQD($feedback['sqd6'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd6'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd6'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd6'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd6'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd6'], 'na') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>SQD7.</strong> Magalang akong trinato ng mga tauhan, at (kung sakali ako ay humingi
                            ng tulong) alam ko na sila ay handang tumulong sa akin.</td>
                        <td>
                            <?= checkSQD($feedback['sqd7'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd7'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd7'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd7'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd7'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd7'], 'na') ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>SQD8.</strong> Nakuha ko ang kinakailangan ko mula sa tanggapan ng gobyerno, kung
                            tinanggihan man, ito ay sapat na ipinaliwanag sa akin.</td>
                        <td>
                            <?= checkSQD($feedback['sqd8'], 'strongly_agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd8'], 'agree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd8'], 'neutral') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd8'], 'disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd8'], 'strongly_disagree') ?>
                        </td>
                        <td>
                            <?= checkSQD($feedback['sqd8'], 'na') ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="suggestions-block">
                Mga suhestiyon kung paano pa mapapabuti pa ang aming mga serbisyo (opsyonal):
                <div class="suggest-line">
                    <?= nl2br(esc($feedback['suggestions'])) ?>
                </div>
            </div>

            <div class="email-line">Email address (opsyonal):
                <?= esc($feedback['email']) ?>
            </div>

            <div class="thank-you">MARAMING SALAMAT!</div>

        </div>

    </div>

    <script>

    </script>

</body>

</html>