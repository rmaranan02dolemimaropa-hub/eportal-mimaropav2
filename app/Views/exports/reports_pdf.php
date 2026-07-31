<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>
        @font-face {
            font-family: 'Manrope';
            src: url('<?= FCPATH ?>assets/fonts/Manrope-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Manrope';
            src: url('<?= FCPATH ?>assets/fonts/Manrope-Bold.ttf') format('truetype');
            font-weight: bolder;
            font-style: normal;
        }

        @page {
            size: A4 portrait;
            margin: 6cm 2cm 3cm;
        }

        body {
            margin: 0;
            color: #000;
            font-family: 'Manrope', sans-serif;
            font-size: 10px;
        }

        .header,
        .footer {
            position: fixed;
            left: -2cm;
            width: 21cm;
            display: flex;
            justify-content: center;
        }

        .header {
            top: -5.5cm;
        }

        .footer {
            bottom: -2.5cm;
        }

        .header img,
        .footer img {
            display: block;
            width: 21cm;
            height: auto;
        }

        h1 {
            margin: 0 0 20px;
            color: #000;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 9px;
            border: 1px solid #000;
            background: #fff;
            color: #000;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        td {
            padding: 8px;
            border: 1px solid #000;
            background: #fff;
            text-align: left;
            vertical-align: middle;
        }

        td.text-center,
        th.text-center {
            text-align: center;
        }

        tr:nth-child(even) td {
            background: #fff;
        }
    </style>

</head>

<body>

    <div class="header">
        <img src="<?= esc($headerImage, 'attr') ?>">
    </div>

    <div class="footer">
        <img src="<?= esc($footerImage, 'attr') ?>">
    </div>

    <h1 style="text-align:center;">Service Requests Report</h1>

    <table>

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

            <?php if (!empty($requests)): ?>

                <?php foreach ($requests as $row): ?>

                    <tr>
                        <td><?= esc($row['reference_no']) ?></td>
                        <td><?= esc($row['fullname']) ?></td>
                        <td><?= esc($row['service_availed']) ?></td>
                        <td class="text-center">
                            <?= date('F d, Y', strtotime($row['date_requested'])) ?>
                        </td>
                        <td class="text-center">
                            <?= esc($row['status']) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="text-center">
                        No records found.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</body>

</html>