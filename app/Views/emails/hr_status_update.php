<!DOCTYPE html>
<html>

<head>
    <title>HR Request Update</title>
</head>

<body style="font-family:Arial; color:#333;">

    <h2 style="color:#10058C;">
        DOLE MIMAROPA HR Request Update
    </h2>


    <p>Dear <?= esc($fullname) ?>,</p>


    <p>
        <?= esc($message) ?>
    </p>


    <hr>


    <p>
        <strong>Reference No:</strong>
        <?= esc($reference_no) ?>
    </p>


    <p>
        <strong>Service Requested:</strong>
        <?= esc($service) ?>
    </p>


    <p>
        <strong>Status:</strong>
        <?= esc($status) ?>
    </p>


    <?php if (!empty($remarks)): ?>

        <p>
            <strong>Remarks:</strong><br>
            <?= nl2br(esc($remarks)) ?>
        </p>

    <?php endif; ?>


    <br>

    <p>
        Thank you for using the
        <strong>DOLE MIMAROPA Client Portal.</strong>
    </p>


    <p>
        Human Resource Unit<br>
        DOLE MIMAROPA
    </p>


</body>

</html>