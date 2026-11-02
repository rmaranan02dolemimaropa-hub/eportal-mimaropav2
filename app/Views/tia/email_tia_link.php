<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Impact Assessment</title>
</head>

<body style="margin:0; padding:0; background-color:#f2f3f8; font-family:Arial, Helvetica, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f2f3f8; padding:24px 0;">
        <tr>
            <td align="center">

                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(16,5,140,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg, #10058C 0%, #2A1AB8 100%); padding:28px 32px;">
                            <div
                                style="color:#ffffff; font-size:12px; letter-spacing:1px; text-transform:uppercase; opacity:0.85;">
                                Department of Labor and Employment
                            </div>
                            <div style="color:#ffffff; font-size:20px; font-weight:bold; padding-top:4px;">
                                DOLE MIMAROPA &mdash; Training Impact Assessment
                            </div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px;">

                            <p style="margin:0 0 16px 0; font-size:15px; color:#333333;">
                                Dear <strong><?= esc($fullname ?: 'Participant') ?></strong>,
                            </p>

                            <p style="margin:0 0 24px 0; font-size:15px; color:#333333; line-height:1.6;">
                                Thank you for attending the training. Kindly answer the
                                <strong>Training Impact Assessment (Self-Rater Form)</strong> by clicking the button
                                below.
                                <br><br>
                                <span style="color:#555555; font-style:italic;">
                                    Maraming salamat sa pagdalo sa training. Mangyaring sagutan ang
                                    Training Impact Assessment sa pamamagitan ng pag-click sa button sa ibaba.
                                </span>
                            </p>

                            <!-- Details card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#f8f8fc; border:1px solid #eceafc; border-radius:10px; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777; width:35%;">
                                                    Training Title</td>
                                                <td
                                                    style="padding:6px 0; font-size:14px; color:#222222; font-weight:bold;">
                                                    <?= esc($training_title) ?>
                                                </td>
                                            </tr>
                                            <?php if (!empty($training_dates)): ?>
                                                <tr>
                                                    <td style="padding:6px 0; font-size:13px; color:#777777;">Training
                                                        Date/s</td>
                                                    <td style="padding:6px 0; font-size:14px; color:#222222;">
                                                        <?= esc($training_dates) ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if (!empty($deadline)): ?>
                                                <tr>
                                                    <td style="padding:6px 0; font-size:13px; color:#777777;">
                                                        Submit on or before</td>
                                                    <td
                                                        style="padding:6px 0; font-size:14px; color:#c0392b; font-weight:bold;">
                                                        <?= esc($deadline) ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <?php if (!empty($deadline)): ?>
                                <p style="margin:0 0 24px 0; font-size:14px; color:#333333; line-height:1.6;">
                                    Please submit your assessment on or before
                                    <strong style="color:#c0392b;"><?= esc($deadline) ?></strong>
                                    (within 5 days). The form will close after this date.
                                    <br>
                                    <span style="color:#555555; font-style:italic;">
                                        Mangyaring isumite ito bago o sa mismong
                                        <strong><?= esc($deadline) ?></strong> (sa loob ng 5 araw).
                                        Magsasara ang form pagkatapos ng petsang ito.
                                    </span>
                                </p>
                            <?php endif; ?>

                            <!-- Button -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding-bottom:8px;">
                                        <a href="<?= esc($formUrl, 'attr') ?>" style="
                                            background-color:#10058C;
                                            color:#ffffff;
                                            font-size:14px;
                                            font-weight:bold;
                                            text-decoration:none;
                                            padding:14px 28px;
                                            border-radius:8px;
                                            display:inline-block;">
                                            Answer Training Impact Assessment
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color:#f8f8fc; padding:20px 32px; text-align:center; border-top:1px solid #eeeeee;">
                            <p style="margin:0; font-size:12px; color:#999999;">DOLE MIMAROPA</p>
                        </td>
                    </tr>

                </table>

                <p style="font-size:11px; color:#aaaaaa; margin-top:16px; max-width:600px;">
                    This is an automated message from the DOLE MIMAROPA Client Portal. Please do not reply directly to
                    this email.
                </p>

            </td>
        </tr>
    </table>

</body>

</html>