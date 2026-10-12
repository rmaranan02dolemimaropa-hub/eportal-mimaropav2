<?php $participantCount = (int) ($participantCount ?? 0); ?>
<!DOCTYPE html>
<html>

<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:24px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0b3d91;color:#ffffff;padding:20px 28px;">
                            <h2 style="margin:0;font-size:18px;">Training Impact Assessment</h2>
                            <p style="margin:4px 0 0;font-size:13px;opacity:.9;">Superior Rating (Annex E)</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 14px;">Good day, <strong><?= esc($superiorName) ?></strong>,</p>

                            <p style="margin:0 0 14px;line-height:1.5;">
                                Your staff attended the training below. Please open your dashboard and rate
                                the participants.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;margin:0 0 18px;">
                                <tr>
                                    <td style="padding:14px 16px;">
                                        <div style="font-size:12px;color:#6b7280;">Training</div>
                                        <div style="font-weight:bold;margin-bottom:8px;"><?= esc($training_title) ?>
                                        </div>
                                        <?php if (!empty($training_dates)): ?>
                                            <div style="font-size:12px;color:#6b7280;">Date/s</div>
                                            <div><?= esc($training_dates) ?></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 22px;line-height:1.5;">
                                <strong><?= $participantCount ?></strong>
                                participant<?= $participantCount === 1 ? '' : 's' ?> attended this training.
                                The complete, up-to-date list is in your dashboard under
                                <strong>Rate Participants</strong>.
                            </p>

                            <table cellpadding="0" cellspacing="0" style="margin:0 0 22px;">
                                <tr>
                                    <td style="background:#0b3d91;border-radius:6px;">
                                        <a href="<?= esc($dashboardUrl, 'attr') ?>"
                                            style="display:inline-block;padding:12px 24px;color:#ffffff;text-decoration:none;font-weight:bold;">
                                            Open My Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 6px;font-size:12px;color:#6b7280;">
                                If the button doesn't work, copy this link into your browser:<br>
                                <a href="<?= esc($dashboardUrl, 'attr') ?>"
                                    style="color:#0b3d91;"><?= esc($dashboardUrl) ?></a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="background:#f9fafb;padding:14px 28px;font-size:11px;color:#9ca3af;text-align:center;">
                            This is an automated message. Please do not reply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>