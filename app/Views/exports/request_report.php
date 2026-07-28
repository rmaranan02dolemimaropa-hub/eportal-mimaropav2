<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>

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
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $row): ?>
                <tr>
                    <td><?= esc($row['reference'] ?? '') ?></td>
                    <td><?= esc($row['name'] ?? '') ?></td>
                    <td><?= esc($row['service'] ?? '') ?></td>
                    <td class="text-center"><?= esc($row['status'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>