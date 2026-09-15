<!--
        View: app/Views/exports/satisfaction_excel.php
        Rendered by ExportController::exportSatisfactionExcel()
        Vars in: $title, $periodLabel, $categories, $rows, $totals, $noResponse, $totalClients

        Same approach as reports_excel.php: a normal styled HTML table that
        SheetJS reads straight out of the DOM and writes as a real .xlsx —
        no PhpSpreadsheet, no composer install, no server round-trip.
    -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #5a5c66;
            font-family: 'Manrope', sans-serif;
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
            background: #10058C;
            border-color: #10058C;
        }

        .tbtn.primary:hover {
            background: #2a1fb0;
        }

        #exportStatus {
            font-size: .78rem;
            color: #9dffb0;
            align-self: center;
        }

        /* ===================== REPORT SHEET (wide — landscape feel) ===================== */
        .sheet-wrap {
            display: flex;
            justify-content: center;
            padding: 32px 16px 64px;
            overflow-x: auto;
        }

        .page {
            background: #fff;
            width: max-content;
            min-width: 1200px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .35);
            padding: 24px 28px 32px;
        }

        /* ===================== HEADER ===================== */

        .header-banner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding-bottom: 10px;
            margin-bottom: 14px;
            border-bottom: 1px solid #000;
            width: 45%;
            margin: 0 auto 14px;
            /* combines centering + your existing bottom margin */
        }

        .header-banner img {
            height: auto;
        }

        .dole-cell img {
            width: 50px;
            height: auto;
            margin: 0;
            /* Remove margins */
        }

        .iso-cell img {
            width: 50px;
            height: auto;
            vertical-align: middle;
            margin: 0;
            /* Remove margins */
        }

        .bp-cell img {
            width: 50px;
            height: auto;
            vertical-align: middle;
            margin: 0;
            /* Remove margins */
        }

        .agency-title {
            text-align: center;
            padding: 0 8px;
            flex: 0;
            white-space: nowrap;
        }

        .agency-title .country {
            font-size: 10pt;
            margin: 0;
            line-height: 1.2;
        }

        .agency-title .dept {
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }

        .agency-title .region {
            font-size: 9.5pt;
            margin: 0;
            letter-spacing: .5px;
            line-height: 1.2;
        }

        .agency-lines {
            margin: 0 0 4px;
            font-size: 12px;
            line-height: 1.5;
        }

        .report-title {
            margin-block-start: 5px;
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            margin: 4px 0 2px;
        }

        .report-meta {
            text-align: center;
            font-size: 10.5px;
            color: #444;
            margin: 0 0 16px;
        }

        table.report-table {
            border-collapse: collapse;
            font-size: 9px;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
            text-align: center;
            white-space: nowrap;
        }

        table.report-table td.details-cell,
        table.report-table td.text-cell {
            white-space: normal;
            text-align: left;
            min-width: 130px;
        }

        table.report-table th {
            background: #f0f0f3;
            font-weight: 700;
        }

        table.report-table .region-row td {
            background: #e6f4ea;
            font-weight: 700;
            text-align: left;
        }

        table.report-table .totals-row td {
            background: #fafafa;
            font-weight: 700;
        }

        table.report-table .pct-row td {
            color: #b91c1c;
            font-weight: 700;
        }

        table.report-table .check-row td {
            background: #eef2ff;
            font-weight: 700;
        }

        table.report-table .check-row td.mismatch {
            background: #fee2e2;
            color: #b91c1c;
        }

        .no-records {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        .signature-block {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            max-width: 700px;
        }

        .signature-block .sig {
            width: 45%;
            text-align: center;
        }

        .signature-block .sig .line {
            border-top: 1px solid #000;
            margin-top: 46px;
            padding-top: 4px;
            font-weight: 700;
            font-size: 10.5px;
        }

        .signature-block .sig .role {
            font-size: 10px;
            margin-top: 2px;
        }

        /* ===================== PRINT (A4 landscape) ===================== */
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm 8mm;
            }

            .toolbar {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .sheet-wrap {
                padding: 0;
                overflow: visible;
                display: block;
            }

            .page {
                box-shadow: none;
                width: 100%;
                min-width: 0;
                padding: 0;
                transform-origin: top left;
            }

            table.report-table {
                width: 100%;
                font-size: 7.5px;
            }

            table.report-table th,
            table.report-table td {
                padding: 3px 4px;
            }

            .signature-block {
                max-width: 50%;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar no-print">
        <div>
            <h1><?= esc($title) ?></h1>
            <div class="sub">Exports the tabulation table below straight to a downloadable .xlsx.</div>
        </div>
        <div class="toolbar-actions">
            <span id="exportStatus"></span>
            <button type="button" class="tbtn" onclick="window.print()">Print</button>

        </div>
    </div>

    <div class="sheet-wrap">
        <div class="page">

            <div class="header-banner">
                <div class="dole-cell"><img src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE"></div>
                <div class="iso-cell"><img src="<?= base_url('assets/img/iso.png') ?>"
                        alt="ISO 9001:2015 Certification"></div>
                <div class="agency-title">
                    <p class="country">Republic of the Philippines</p>
                    <p class="dept">DEPARTMENT OF LABOR AND EMPLOYMENT</p>
                    <p class="region">MIMAROPA REGION</p>
                </div>
                <div class="bp-cell"><img src="<?= base_url('assets/img/bagong_pilipinas.png') ?>"
                        alt="Bagong Pilipinas"></div>
            </div>

            <h2 class="report-title">
                <?= esc($title) ?>
            </h2>
            <p class="report-meta">Generated on
                <?= esc(date('F j, Y \a\t g:i A')) ?>
            </p>
            <div class="agency-lines">

                <strong>Regional/Provincial/Field Office:</strong> IMSD - HR UNIT<br>
                <strong>Period Covered:</strong>
                <?= esc(strtoupper($periodLabel)) ?><br>
                <strong>Program/Service:</strong>
                <?= esc($servicesList !== '' ? $servicesList : 'N/A') ?><br>
                <?php if (!empty($filterSummary)): ?>
                    <strong>Exported by Filter:</strong>
                    <?= esc($filterSummary) ?><br>
                <?php endif; ?>
            </div>




            <!-- id="exportTable" is what SheetJS reads to build the .xlsx -->
            <table class="report-table" id="exportTable">
                <thead>
                    <tr>
                        <th rowspan="4">No.</th>
                        <th rowspan="4">Client/Citizen Details</th>
                        <?php foreach ($categories as $cat): ?>
                            <th colspan="5"><?= esc($cat['group']) ?></th>
                        <?php endforeach; ?>
                        <th rowspan="4">Complaints</th>
                        <th rowspan="4">Comment/Suggestion/<br>Recommendation</th>
                        <th rowspan="4">Details/Actions Taken</th>
                    </tr>
                    <tr>
                        <?php foreach ($categories as $cat): ?>
                            <th colspan="5"><?= esc($cat['question']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($categories as $cat): ?>
                            <th>5</th>
                            <th>4</th>
                            <th>3</th>
                            <th>2</th>
                            <th>1</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td class="no-records" colspan="<?= 2 + count($categories) * 5 + 3 ?>">
                                No survey responses found for the selected period.
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr class="region-row">
                            <td colspan="<?= 2 + count($categories) * 5 + 3 ?>">MIMAROPA Region</td>
                        </tr>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= esc($row['no']) ?>.</td>
                                <td class="details-cell">
                                    <?= esc($row['name']) ?><br>
                                    <em><?= esc($row['gender']) ?></em><br>
                                    <?= esc($row['contact']) ?><br>
                                    <?= esc($row['email']) ?><br>
                                    <?= esc($row['date']) ?><br>


                                </td>
                                <?php foreach (array_keys($categories) as $ci): ?>
                                    <?php $score = $row['ratings'][$ci]; ?>
                                    <td><?= $score === 5 ? '1' : '' ?></td>
                                    <td><?= $score === 4 ? '1' : '' ?></td>
                                    <td><?= $score === 3 ? '1' : '' ?></td>
                                    <td><?= $score === 2 ? '1' : '' ?></td>
                                    <td><?= $score === 1 ? '1' : '' ?></td>
                                <?php endforeach; ?>
                                <td class="text-cell"><?= esc($row['complaint']) ?></td>
                                <td class="text-cell"><?= esc($row['suggestion']) ?></td>
                                <td class="text-cell"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($rows)): ?>
                    <tfoot>
                        <tr class="totals-row">
                            <td colspan="2">(Total # of Clients) <?= esc($totalClients) ?></td>
                            <?php foreach (array_keys($categories) as $ci): ?>
                                <?php foreach ([5, 4, 3, 2, 1] as $s): ?>
                                    <td><?= esc($totals[$ci][$s]) ?></td>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            <td colspan="3"></td>
                        </tr>
                        <tr class="pct-row">
                            <td colspan="2">%</td>
                            <?php foreach (array_keys($categories) as $ci): ?>
                                <?php foreach ([5, 4, 3, 2, 1] as $s): ?>
                                    <?php
                                    $pct = $totalClients > 0
                                        ? round(($totals[$ci][$s] / $totalClients) * 100, 1)
                                        : 0;
                                    ?>
                                    <td><?= esc($pct) ?>%</td>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            <td colspan="3"></td>
                        </tr>
                        <tr class="check-row">
                            <td colspan="2">No Response</td>
                            <?php foreach (array_keys($categories) as $ci): ?>
                                <?php
                                $categoryTotal = array_sum($totals[$ci]) + $noResponse[$ci];
                                $isMismatch = (int) $categoryTotal !== (int) $totalClients;
                                ?>
                                <td colspan="5" class="<?= $isMismatch ? 'mismatch' : '' ?>">
                                    <?= esc($categoryTotal) ?>
                                    <?= $isMismatch ? ' ⚠' : '' ?>
                                </td>
                            <?php endforeach; ?>
                            <td colspan="3"></td>
                        </tr>
                        <tr class="pct-row">
                            <td colspan="2">%</td>
                            <?php foreach (array_keys($categories) as $ci): ?>
                                <?php
                                $nrPct = $totalClients > 0
                                    ? round(($noResponse[$ci] / $totalClients) * 100, 1)
                                    : 0;
                                ?>
                                <td><?= esc($nrPct) ?>%</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php endforeach; ?>
                            <td colspan="3"></td>
                        </tr>

                    </tfoot>
                <?php endif; ?>
            </table>

            <div class="signature-block">
                <div class="sig">
                    <div class="line">KRYSTEL CHARISMA L. JUMANOY</div>
                    <div class="role">AOV/HRMO</div>
                </div>
                <div class="sig">
                    <div class="line">ROSEMARIE C. HUPANDA</div>
                    <div class="role">IMSD CHIEF</div>
                </div>
            </div>

        </div>
    </div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

</body>

</html>