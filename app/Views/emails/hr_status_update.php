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

    <?php if (isset($csm_link)): ?>

        <hr>

        <p>
            We value your feedback. After receiving your requested document,
            we kindly invite you to answer our Client Satisfaction Measurement (CSM) Survey.
        </p>

        <p>
            Your feedback will help us improve our services.
        </p>

        <p style="text-align:center;">
            <a href="<?= $csm_link ?>" style="
background:#10058C;
color:#ffffff;
padding:12px 20px;
border-radius:8px;
text-decoration:none;
display:inline-block;
font-weight:bold;
">
                Answer Client Satisfaction Survey
            </a>
        </p>

        <p>
            Reference No.: <strong>
                <?= $reference_no ?>
            </strong>
        </p>

    <?php endif; ?>


</body>

</html>