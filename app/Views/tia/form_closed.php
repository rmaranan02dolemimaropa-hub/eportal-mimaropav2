<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Closed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --tia-navy: #10058C;
            --tia-navy-2: #2A1AB8;
            --tia-danger: #c0392b;
            --tia-bg: #f2f3f8;
            --tia-border: #eceafc;
        }

        body {
            background: var(--tia-bg);
            font-family: 'Manrope', Arial, Helvetica, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 24px 0;
        }

        .closed-card {
            max-width: 560px;
            margin: 0 auto;
            background: #fff;
            border: 0;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(16, 5, 140, 0.10);
        }

        .closed-header {
            background: linear-gradient(135deg, var(--tia-navy) 0%, var(--tia-navy-2) 100%);
            color: #fff;
            padding: 22px 28px;
        }

        .closed-header .agency {
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            opacity: .85;
        }

        .closed-header .title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 4px;
        }

        .closed-body {
            padding: 36px 32px 32px;
            text-align: center;
        }

        .closed-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fdecea;
            color: var(--tia-danger);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 16px;
        }

        .closed-body h4 {
            font-weight: 700;
            color: #222;
            margin-bottom: 6px;
        }

        .closed-body .lead-text {
            color: #666;
            font-size: 15px;
            margin-bottom: 22px;
        }

        .details {
            background: #f8f8fc;
            border: 1px solid var(--tia-border);
            border-radius: 10px;
            padding: 16px 20px;
            text-align: left;
            margin-bottom: 22px;
        }

        .details .row-item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 6px 0;
            font-size: 14px;
        }

        .details .label {
            color: #777;
            flex: 0 0 35%;
        }

        .details .value {
            color: #222;
            font-weight: bold;
            text-align: right;
        }

        .details .value.deadline {
            color: var(--tia-danger);
        }

        .help-text {
            font-size: 13px;
            color: #777;
            margin: 0;
        }

        .closed-footer {
            background: #f8f8fc;
            border-top: 1px solid #eee;
            text-align: center;
            padding: 14px;
            font-size: 12px;
            color: #999;
        }

        @media (max-width: 480px) {
            .closed-body {
                padding: 28px 20px;
            }

            .details .row-item {
                flex-direction: column;
                gap: 2px;
            }

            .details .value {
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <div class="container px-3">
        <div class="closed-card">

            <div class="closed-header">
                <div class="agency">Department of Labor and Employment</div>
                <div class="title">DOLE MIMAROPA &mdash; Training Impact Assessment</div>
            </div>

            <div class="closed-body">
                <div class="closed-icon"><i class="bi bi-calendar-x"></i></div>
                <h4>Submission Closed</h4>
                <p class="lead-text">The deadline for this assessment has passed.</p>

                <div class="details">
                    <div class="row-item">
                        <span class="label">Training Title</span>
                        <span class="value"><?= esc($training['training_title']) ?></span>
                    </div>
                    <div class="row-item">
                        <span class="label">Deadline was</span>
                        <span class="value deadline">
                            <?= esc(date('F j, Y', strtotime($training['form_deadline_at']))) ?>
                        </span>
                    </div>
                </div>

                <p class="help-text">
                    Please contact HR if you still need to submit your Training Impact Assessment.
                </p>
            </div>

            <div class="closed-footer">DOLE MIMAROPA</div>
        </div>
    </div>
</body>

</html>