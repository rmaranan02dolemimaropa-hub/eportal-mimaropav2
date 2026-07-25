<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'HR Request Report', ENT_QUOTES, 'UTF-8') ?></title>

    <?php
    // Dompdf reads these local files through its configured chroot (FCPATH).
    $headerImage = 'assets/img/dole-header.png';
    $footerImage = 'assets/img/dole-footer.png';
    ?>

    <style>
        @page {
            size: A4 portrait;
            margin: 6cm 1cm 3cm 1cm;
        }

        body {
            margin: 0;
            color: #222;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .header,
        .footer {
            position: fixed;
            left: 0;
            width: 100%;
        }

        .header {
            top: -5.5cm;
        }

        .footer {
            bottom: -2.5cm;
        }

        .header img {
            display: block;
            width: 100%;
            height: auto;
        }

        .footer img {
            display: block;
            width: 100%;
            height: auto;
        }

        .content {
            margin-top: 10px;
        }

        .report-title {
            margin: 0 0 20px;
            color: #10058C;
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
            padding: 9px 8px;
            background-color: #10058C;
            border: 1px solid #10058C;
            color: #fff;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            border: 1px solid #c9c9c9;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background-color: #f5f6fa;
        }

        .no-records {
            padding: 18px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= $headerImage ?>" alt="DOLE Header">
    </div>

    <div class="footer">
        <img src="<?= $footerImage ?>" alt="">
    </div>

    <main class="content">
        <h1 class="report-title">
            <?= esc($title) ?>
        </h1>

        <table>
            <thead>
                <tr>
                    <th>Reference No.</th>
                    <th>Name</th>
                    <th>Service</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $row): ?>
                        <tr>
                            <td><?= esc($row['reference'] ?? '') ?></td>
                            <td><?= esc($row['name'] ?? '') ?></td>
                            <td><?= esc($row['service'] ?? '') ?></td>
                            <td><?= esc($row['status'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No request records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>

</html>