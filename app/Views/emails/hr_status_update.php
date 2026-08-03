<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HR Request Update</title>
</head>

<body style="margin:0; padding:0; background-color:#f2f3f8; font-family:Arial, Helvetica, sans-serif;">

<?php
    // Simple status color mapping for the badge
    $statusColors = [
        'approved'   => ['bg' => '#E6F4EA', 'text' => '#1E7E34'],
        'released'   => ['bg' => '#E6F4EA', 'text' => '#1E7E34'],
        'completed'  => ['bg' => '#E6F4EA', 'text' => '#1E7E34'],
        'pending'    => ['bg' => '#FFF8E1', 'text' => '#B8860B'],
        'processing' => ['bg' => '#FFF8E1', 'text' => '#B8860B'],
        'rejected'   => ['bg' => '#FDECEA', 'text' => '#C0392B'],
        'denied'     => ['bg' => '#FDECEA', 'text' => '#C0392B'],
    ];
    $statusKey   = strtolower(trim($status ?? ''));
    $badgeColors = $statusColors[$statusKey] ?? ['bg' => '#E8EAF6', 'text' => '#10058C'];
?>

<!-- Outer wrapper -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f3f8; padding:24px 0;">
    <tr>
        <td align="center">

            <!-- Card -->
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(16,5,140,0.08);">

                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg, #10058C 0%, #2A1AB8 100%); padding:28px 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="color:#ffffff; font-size:12px; letter-spacing:1px; text-transform:uppercase; opacity:0.85;">
                                    Department of Labor and Employment
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#ffffff; font-size:20px; font-weight:bold; padding-top:4px;">
                                    DOLE MIMAROPA &mdash; <?= esc($service) ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:32px;">

                        <p style="margin:0 0 16px 0; font-size:15px; color:#333333;">
                            Dear <strong><?= esc($fullname) ?></strong>,
                        </p>

                        <p style="margin:0 0 24px 0; font-size:15px; color:#333333; line-height:1.6;">
                            <?= esc($message) ?>
                        </p>

                        <!-- Details card -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f8fc; border:1px solid #eceafc; border-radius:10px; margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">

                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding:6px 0; font-size:13px; color:#777777; width:40%;">Reference No.</td>
                                            <td style="padding:6px 0; font-size:14px; color:#222222; font-weight:bold;"><?= esc($reference_no) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; font-size:13px; color:#777777;">Service Requested</td>
                                            <td style="padding:6px 0; font-size:14px; color:#222222;"><?= esc($service) ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; font-size:13px; color:#777777; vertical-align:top;">Status</td>
                                            <td style="padding:6px 0;">
                                                <span style="
                                                    display:inline-block;
                                                    background-color:<?= $badgeColors['bg'] ?>;
                                                    color:<?= $badgeColors['text'] ?>;
                                                    font-size:13px;
                                                    font-weight:bold;
                                                    padding:4px 12px;
                                                    border-radius:20px;
                                                ">
                                                    <?= esc($status) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>

                                    <?php if (!empty($remarks)): ?>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:14px; border-top:1px solid #eceafc;">
                                            <tr>
                                                <td style="padding-top:14px; font-size:13px, color:#777777;">
                                                    <span style="font-size:13px; color:#777777;">Remarks</span><br>
                                                    <span style="font-size:14px; color:#333333; line-height:1.5;">
                                                        <?= nl2br(esc($remarks)) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        </table>

                        <p style="margin:0; font-size:14px; color:#555555;">
                            Thank you for using the <strong>DOLE MIMAROPA e-Client Portal</strong>.
                        </p>

                    </td>
                </tr>

                <?php if (isset($csm_link) || isset($csmtagalog_Link)): ?>
                <!-- Feedback Form section -->
                <tr>
                    <td style="padding:0 32px 32px 32px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #eeeeee; padding-top:24px;">
                            <tr>
                                <td style="padding-top:24px;">

                                    <p style="margin:0 0 14px 0; font-size:14px; color:#333333; line-height:1.6;">
                                        We value your feedback. After receiving your requested document, we kindly invite
                                        you to answer our <strong>Feedback Form</strong>.
                                        <br><br>
                                        <span style="color:#555555; font-style:italic;">
                                            Lubos naming pinahahalagahan ang inyong opinyon. Matapos ninyong matanggap
                                            ang inyong hinihiling na dokumento, hinihikayat namin kayong maglaan ng
                                            ilang minuto upang sagutan ang aming Feedback Form.
                                            Ang inyong mga kasagutan ay makatutulong sa amin upang higit pang
                                            mapabuti ang aming mga serbisyo.
                                        </span>
                                    </p>

                                    <p style="margin:0 0 20px 0; font-size:13px; color:#777777; line-height:1.6;">
                                        Your feedback will help us improve our services.
                                        <br>
                                        Ang inyong puna at mungkahi ay makatutulong sa amin upang higit pang mapabuti
                                        ang kalidad ng aming mga serbisyo.
                                    </p>

                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="padding:6px 6px;">
                                                <a href="<?= $csm_link ?>" style="
                                                    background-color:#10058C;
                                                    color:#ffffff;
                                                    font-size:14px;
                                                    font-weight:bold;
                                                    text-decoration:none;
                                                    padding:13px 22px;
                                                    border-radius:8px;
                                                    display:inline-block;
                                                ">
                                                    Answer Feedback Form &mdash; English
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="padding:6px 6px;">
                                                <a href="<?= $csmtagalog_Link ?>" style="
                                                    background-color:#ffffff;
                                                    color:#10058C;
                                                    font-size:14px;
                                                    font-weight:bold;
                                                    text-decoration:none;
                                                    padding:12px 22px;
                                                    border-radius:8px;
                                                    display:inline-block;
                                                    border:2px solid #10058C;
                                                ">
                                                    Sagutin ang Feedback Form &mdash; Tagalog
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="margin:20px 0 0 0; font-size:12px; color:#999999; text-align:center;">
                                        Reference No.: <strong style="color:#666666;"><?= esc($reference_no) ?></strong>
                                    </p>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f8f8fc; padding:20px 32px; text-align:center; border-top:1px solid #eeeeee;">
                        <p style="margin:2px 0 0 0; font-size:12px; color:#999999;">
                            DOLE MIMAROPA
                        </p>
                    </td>
                </tr>

            </table>
            <!-- /Card -->

            <p style="font-size:11px; color:#aaaaaa; margin-top:16px; max-width:600px;">
                This is an automated message from the DOLE MIMAROPA Client Portal. Please do not reply directly to this email.
            </p>

        </td>
    </tr>
</table>

</body>
</html>+