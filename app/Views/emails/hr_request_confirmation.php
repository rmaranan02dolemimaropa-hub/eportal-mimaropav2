<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Request Submitted</title>
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
                        <td style="background:linear-gradient(135deg, #10058C 0%, #2A1AB8 100%); padding:28px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <img src="<?= base_url('assets/images/dole_logo.png') ?>" alt="DOLE MIMAROPA Logo" style="width: 120px; height: auto;">

                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#ffffff; font-size:20px; font-weight:bold; padding-top:4px;">
                                        Request Submitted Successfully
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
                                Your HR request has been <strong>successfully submitted</strong>. Please find the
                                details below.
                            </p>

                            <!-- Success banner -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#E6F4EA; border-radius:8px; margin-bottom:24px;">
                                <tr>
                                    <td style="padding:12px 18px; font-size:13px; color:#1E7E34; font-weight:bold;">
                                        &#10003; Submission received &mdash; you will be notified as your request
                                        progresses.
                                    </td>
                                </tr>
                            </table>

                            <!-- Details card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#f8f8fc; border:1px solid #eceafc; border-radius:10px; margin-bottom:24px;">
                                <tr>
                                    <td style="padding:20px 24px;">

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777; width:40%;">
                                                    Reference Number</td>
                                                <td
                                                    style="padding:6px 0; font-size:14px; color:#222222; font-weight:bold;">
                                                    <?= esc($reference_no) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777;">Service
                                                    Requested</td>
                                                <td style="padding:6px 0; font-size:14px; color:#222222;">
                                                    <?= esc($service) ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; font-size:13px; color:#777777;">Date Requested
                                                </td>
                                                <td style="padding:6px 0; font-size:14px; color:#222222;">
                                                    <?= esc($date_requested) ?></td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                            <!-- Tracking note -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background-color:#FFF8E1; border-radius:8px; margin-bottom:8px;">
                                <tr>
                                    <td style="padding:14px 18px; font-size:13px; color:#8a6d00; line-height:1.5;">
                                        <strong>Please keep your Reference Number.</strong> You will need it to track
                                        the status of your request.
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color:#f8f8fc; padding:20px 32px; text-align:center; border-top:1px solid #eeeeee;">
                            <p style="margin:0; font-size:13px; color:#555555; font-weight:bold;">
                                Thank you for using DOLE MIMAROPA E-Portal.
                            </p>
                            <p style="margin:8px 0 0 0; font-size:12px; color:#999999;">
                                This is an automated email from DOLE MIMAROPA E-Portal. Please do not reply to this
                                email.
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