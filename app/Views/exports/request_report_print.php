<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>

    <style>
        @media print {

            @page {
                size: A4;
                margin: 0;
            }

            body {
                margin: 5.5cm 2cm 2.5cm;
                font-family: 'Manrope', sans-serif;
                font-size: 10px;
            }

            .header {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
            }

            .header img {
                width: 100%;
                display: block;
            }

            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
            }

            .footer img {
                width: 100%;
                display: block;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="<?= base_url('assets/img/dole-header.png') ?>" alt="DOLE Header">
    </div>

    <div class="footer">
        <img src="<?= base_url('assets/img/dole-footer.png') ?>" alt="DOLE Footer">
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
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>

</html>