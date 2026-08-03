<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New HR Request Submitted</title>
</head>

<body style="margin:0; padding:0; background-color:#f2f3f8; font-family:Arial, Helvetica, sans-serif;">

    <!-- Outer wrapper -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f2f3f8; padding:24px 0;">
        <tr>
            <td align="center">

                <!-- Card -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(16,5,140,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg, #10058C 0%, #2A1AB8 100%); padding:24px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="color:#ffffff; font-size:12px; letter-spacing:1px; text-transform:uppercase; opacity:0.85;">
                                        DOLE MIMAROPA E-Portal &mdash; HR Dashboard Alert
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#ffffff; font-size:20px; font-weight:bold; padding-top:4px;">
                                        New HR Request Submitted
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px;">

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                                <tr>
                                    <td
                                        style="background-color:#E8EAF6; color:#10058C; font-size:12px; font-weight:bold; padding:5px 12px; border-radius:20px;">
                                        NEW APPLICATION
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 24px 0; font-size:15px; color:#333333; line-height:1.6;">
                                A new HR request has been submitted through the E-Portal and is awaiting action.
                            </p>

                            <!-- Details card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#f8f8fc; border:1px solid #eceafc; border-radius:10px; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:20px 24px;">

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777; width:40%;">
                                                    Reference No.</td>
                                                <td
                                                    style="padding:6px 0; font-size:14px; color:#222222; font-weight:bold;">
                                                    <?= esc($reference_no) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777;">Applicant</td>
                                                <td style="padding:6px 0; font-size:14px; color:#222222;">
                                                    <?= esc($fullname) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777;">Service</td>
                                                <td style="padding:6px 0; font-size:14px; color:#222222;">
                                                    <?= esc($service) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777;">Date Requested
                                                </td>
                                                <td style="padding:6px 0; font-size:14px; color:#222222;">
                                                    <?= esc($date_requested) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777;">Status</td>
                                                <td style="padding:6px 0;">
                                                    <span style="
                                                    display:inline-block;
                                                    background-color:#FFF8E1;
                                                    color:#B8860B;
                                                    font-size:13px;
                                                    font-weight:bold;
                                                    padding:4px 12px;
                                                    border-radius:20px;
                                                ">
                                                        New Application
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <?php if (!empty($dashboard_link)): ?>
                                            <a href="<?= $dashboard_link ?>" style="
                                        background-color:#10058C;
                                        color:#ffffff;
                                        font-size:14px;
                                        font-weight:bold;
                                        text-decoration:none;
                                        padding:13px 26px;
                                        border-radius:8px;
                                        display:inline-block;
                                    ">
                                                Open HR Dashboard
                                            </a>
                                        <?php else: ?>
                                            <p style="margin:0; font-size:14px; color:#555555;">
                                                Please log in to the HR Dashboard to process this request.
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color:#f8f8fc; padding:18px 32px; text-align:center; border-top:1px solid #eeeeee;">
                            <p style="margin:0; font-size:12px; color:#999999;">
                                This is an automated notification from the DOLE MIMAROPA E-Portal HR system.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Card -->

            </td>
        </tr>
    </table>

</body>

</html>