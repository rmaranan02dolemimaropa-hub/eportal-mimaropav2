<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>

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
            font-family: 'Manrope', Arial, Helvetica, sans-serif;
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

        /* A4 preview on screen: 210mm x 297mm @ 96dpi ≈ 794 x 1123px */
        .page {
            position: relative;
            background: #fff;
            width: 794px;
            height: 1123px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .35);
            font-size: 10.4px;
            line-height: 1.35;
            padding: 5.5cm 2cm 2.5cm;
            overflow: hidden;
        }

        .page .header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        .page .header img {
            width: 100%;
            display: block;
        }

        .page .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        .page .footer img {
            width: 100%;
            display: block;
        }

        .page-number {
            position: absolute;
            bottom: 0.6cm;
            right: 2cm;
            font-size: 9px;
            color: #444;
        }

        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 14px;
        }

        /* ===================== REPORT TABLE — fixed column widths ===================== */
        table.report-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 10px;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid var(--line);
            padding: 8px;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        table.report-table th {
            background: #f0f0f3;
            font-weight: 700;
        }

        table.report-table .text-center {
            text-align: center;
        }

        /* Column width distribution (must total 100%) */
        table.report-table col.col-reference {
            width: 22%;
        }

        table.report-table col.col-name {
            width: 24%;
        }

        table.report-table col.col-service {
            width: 34%;
        }

        table.report-table col.col-status {
            width: 20%;
        }

        /* ===================== PRINT RULES ===================== */
        @media print {
            body {
                background: #fff;
                margin: 0;
                font-family: 'Manrope', sans-serif;
                font-size: 10px;
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
                height: auto;
                min-height: 100vh;
                padding: 5.5cm 2cm 2.5cm;
                overflow: visible;
                break-after: page;
                page-break-after: always;
            }

            .page:last-child {
                break-after: auto;
                page-break-after: auto;
            }

            .page .header,
            .page .footer {
                position: fixed;
            }

            table {
                width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 8px;
            }
        }

        @media print {

            .page {
                position: relative;
            }

            .page .header {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .page .footer {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
            }

        }
    </style>
</head>

<body>

    <!-- ===================== TOOLBAR ===================== -->
    <div class="toolbar no-print">
        <div>
            <h1><?= esc($title) ?></h1>
            <div class="sub">Official DOLE MIMAROPA report, ready for printing.</div>
        </div>
        <div class="toolbar-actions">
            <button type="button" class="tbtn primary" onclick="window.location.href='<?= base_url('reports') ?>'">
                Back
            </button>
            <button class="tbtn primary" onclick="window.print()">🖨️ Print Report</button>
        </div>
    </div>

    <div class="sheets" id="sheetsContainer">
        <!-- Pages are generated here by JS -->
    </div>

    <!-- ===================== HIDDEN SOURCE DATA (not printed/shown) ===================== -->
    <table id="sourceTable" style="display:none;">
        <thead>
            <tr>
                <th>Reference No.</th>
                <th>Name</th>
                <th>Service</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $row): ?>
                <tr>
                    <td><?= esc($row['reference_no'] ?? '') ?></td>
                    <td><?= esc($row['fullname'] ?? '') ?></td>
                    <td><?= esc($row['service_availed'] ?? '') ?></td>
                    <td class="text-center"><?= esc($row['status'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        (function () {
            const HEADER_IMG = '<?= base_url('assets/img/dole-header.png') ?>';
            const FOOTER_IMG = '<?= base_url('assets/img/dole-footer.png') ?>';
            const REPORT_TITLE = 'Service Requests Report';

            // Shared colgroup markup so every generated page's table
            // keeps identical column widths
            const COLGROUP_HTML = `
                <colgroup>
                    <col class="col-reference">
                    <col class="col-name">
                    <col class="col-service">
                    <col class="col-status">
                </colgroup>
            `;

            const sourceTable = document.getElementById('sourceTable');
            const theadHTML = sourceTable.querySelector('thead').innerHTML;
            const allRows = Array.from(sourceTable.querySelectorAll('tbody tr'));
            const sheetsContainer = document.getElementById('sheetsContainer');

            function buildPage(pageNumber, isFirstPage) {
                const page = document.createElement('div');
                page.className = 'page';
                page.dataset.page = pageNumber;

                page.innerHTML = `
                    <div class="header"><img src="${HEADER_IMG}" alt="DOLE Header"></div>
                    <div class="footer"><img src="${FOOTER_IMG}" alt="DOLE Footer"></div>
                    ${isFirstPage ? `<h1 class="report-title">${REPORT_TITLE}</h1>` : ''}
                    <table class="report-table">
                        ${COLGROUP_HTML}
                        <thead>${theadHTML}</thead>
                        <tbody></tbody>
                    </table>
                    <div class="page-number">Page ${pageNumber}</div>
                `;
                return page;
            }

            function paginate() {
                sheetsContainer.innerHTML = '';

                if (allRows.length === 0) {
                    const label = document.createElement('div');
                    label.className = 'sheet-label';
                    label.textContent = 'Page 1';
                    sheetsContainer.appendChild(label);

                    const page = buildPage(1, true);
                    sheetsContainer.appendChild(page);
                    return;
                }

                let pageNumber = 1;
                let page = buildPage(pageNumber, true);
                let tbody = page.querySelector('tbody');

                const label = document.createElement('div');
                label.className = 'sheet-label';
                label.textContent = 'Page ' + pageNumber;
                sheetsContainer.appendChild(label);
                sheetsContainer.appendChild(page);

                // Content box the table has to fit inside (page minus its own top/bottom padding)
                const pageContentHeight = page.clientHeight
                    - parseFloat(getComputedStyle(page).paddingTop)
                    - parseFloat(getComputedStyle(page).paddingBottom);

                const titleEl = page.querySelector('.report-title');
                let usedHeight = (titleEl ? titleEl.offsetHeight + 14 : 0);

                for (let i = 0; i < allRows.length; i++) {
                    const row = allRows[i].cloneNode(true);
                    tbody.appendChild(row);

                    const table = page.querySelector('.report-table');
                    const currentHeight = usedHeight + table.offsetHeight;

                    if (currentHeight > pageContentHeight && tbody.children.length > 1) {
                        // Row doesn't fit — move it to a new page
                        tbody.removeChild(row);

                        pageNumber++;
                        page = buildPage(pageNumber, false);
                        tbody = page.querySelector('tbody');

                        const nextLabel = document.createElement('div');
                        nextLabel.className = 'sheet-label';
                        nextLabel.textContent = 'Page ' + pageNumber;
                        sheetsContainer.appendChild(nextLabel);
                        sheetsContainer.appendChild(page);

                        usedHeight = 0;
                        tbody.appendChild(row);
                    }
                }
            }

            window.addEventListener('load', paginate);
            window.addEventListener('resize', paginate);
        })();
    </script>

</body>

</html>