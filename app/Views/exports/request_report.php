<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>

    <style>
        @page {
            size: A4 portrait;
            margin: 6cm 1cm 3cm;
        }

        body {
            margin: 0;
            color: #222;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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

        .header img,
        .footer img {
            display: block;
            width: 100%;
            height: auto;
        }

        h1 {
            margin: 0 0 20px;
            color: #10058C;
            font-size: 16px;
            text-align: center;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 9px;
            border: 1px solid #10058C;
            background: #10058C;
            color: #fff;
            font-size: 9px;
            text-align: left;
        }

        td {
            padding: 8px;
            border: 1px solid #c9c9c9;
        }

        tr:nth-child(even) {
            background: #f5f6fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php if ($headerImage): ?>
            <img src="<?= esc($headerImage, 'attr') ?>" alt="DOLE Header">
        <?php endif; ?>
    </div>

    <div class="footer">
        <?php if ($footerImage): ?>
            <img src="<?= esc($footerImage, 'attr') ?>" alt="">
        <?php endif; ?>
    </div>

    <h1><?= esc($title) ?></h1>

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
            <?php foreach ($requests as $row): ?>
                <tr>
                    <td><?= esc($row['reference'] ?? '') ?></td>
                    <td><?= esc($row['name'] ?? '') ?></td>
                    <td><?= esc($row['service'] ?? '') ?></td>
                    <td><?= esc($row['status'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>