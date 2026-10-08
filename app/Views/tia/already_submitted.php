<?php
// Expected: $response (row from tia_responses)
$r = $response ?? [];

// Mask email: juan.delacruz@dole.gov.ph -> ju***@dole.gov.ph
$maskEmail = function (string $email): string {
    if (strpos($email, '@') === false) {
        return $email;
    }
    [$user, $domain] = explode('@', $email, 2);
    return substr($user, 0, 2) . str_repeat('*', max(strlen($user) - 2, 3)) . '@' . $domain;
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Already Submitted | DOLE Training Impact Assessment</title>
    <meta name="description"
        content="One portal, multiple digital government services. Submit requests, access HR services, and connect with DOLE MIMAROPA online.">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/doleicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --dole-blue: #0b3d91;
            --dole-navy: #10058c;
            --amber: #d98200;
            --amber-soft: #fff6e5;
            --ink: #1f2a44;
            --muted: #6b7690;
            --line: #e3e8f2;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', system-ui, sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #eef3fb 0%, #f7f9fc 60%);
            min-height: 100vh;
            margin: 0;
            padding: 32px 16px;
        }

        .wrap {
            max-width: 640px;
            margin: 0 auto;
        }

        .brand {
            text-align: center;
            margin-bottom: 20px;
        }

        .brand .annex {
            display: inline-block;
            font-weight: 800;
            font-size: .75rem;
            letter-spacing: .14em;
            color: #fff;
            background: var(--dole-blue);
            padding: 4px 12px;
            border-radius: 999px;
        }

        .brand .gov {
            margin-top: 10px;
            font-size: .72rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            line-height: 1.5;
        }

        .brand h1 {
            margin: 8px 0 0;
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--dole-blue);
            letter-spacing: .04em;
        }

        .card-box {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(11, 61, 145, .08);
            overflow: hidden;
        }

        .card-top {
            background: var(--amber-soft);
            border-bottom: 1px solid #f3dfb8;
            text-align: center;
            padding: 32px 24px 26px;
        }

        .icon-ring {
            width: 84px;
            height: 84px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            box-shadow: 0 0 0 8px rgba(217, 130, 0, .12);
        }

        .icon-ring i {
            font-size: 2.6rem;
            color: var(--amber);
        }

        .card-top h2 {
            margin: 0 0 6px;
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--dole-blue);
        }

        .card-top p {
            margin: 0;
            color: #7a5a1c;
            font-size: .95rem;
        }

        .card-body-x {
            padding: 26px 26px 8px;
        }

        .ref-box {
            text-align: center;
            background: #f4f7fd;
            border: 1px dashed #b9c8e8;
            border-radius: 14px;
            padding: 16px 12px;
            margin-bottom: 22px;
        }

        .ref-box small {
            display: block;
            text-transform: uppercase;
            letter-spacing: .12em;
            font-size: .68rem;
            font-weight: 700;
            color: var(--muted);
        }

        .ref-box .ref {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--dole-navy);
            letter-spacing: .04em;
            word-break: break-all;
        }

        .ref-box button {
            margin-top: 6px;
            border: 0;
            background: transparent;
            color: var(--dole-blue);
            font-weight: 700;
            font-size: .8rem;
            cursor: pointer;
        }

        dl.details {
            margin: 0 0 18px;
        }

        .details .row-x {
            display: flex;
            gap: 12px;
            padding: 11px 0;
            border-bottom: 1px solid var(--line);
        }

        .details .row-x:last-child {
            border-bottom: 0;
        }

        .details dt {
            flex: 0 0 38%;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
        }

        .details dd {
            flex: 1;
            margin: 0;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .notice {
            display: flex;
            gap: 10px;
            background: #f4f7fd;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: .85rem;
            color: var(--muted);
            margin-bottom: 22px;
        }

        .notice i {
            color: var(--dole-blue);
            margin-top: 2px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 0 26px 26px;
        }

        .actions .btn {
            flex: 1 1 160px;
            font-weight: 700;
            border-radius: 10px;
            padding: 10px 14px;
        }

        .btn-dole {
            background: var(--dole-blue);
            border-color: var(--dole-blue);
            color: #fff;
        }

        .btn-dole:hover {
            background: #092f70;
            color: #fff;
        }

        .foot {
            text-align: center;
            margin-top: 18px;
            font-size: .78rem;
            color: var(--muted);
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .actions,
            .ref-box button {
                display: none;
            }

            .card-box {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="brand">
            <span class="annex">ANNEX D</span>
            <div class="gov">Republic of the Philippines<br>Department of Labor and Employment</div>
            <h1>TRAINING IMPACT ASSESSMENT</h1>
        </div>

        <div class="card-box">
            <div class="card-top">
                <div class="icon-ring"><i class="bi bi-check2-circle"></i></div>
                <h2>Already Submitted</h2>
                <p>You have already completed this assessment. Thank you!</p>
            </div>

            <div class="card-body-x">
                <div class="ref-box">
                    <small>Reference No.</small>
                    <div class="ref" id="refNo"><?= esc($r['reference_no'] ?? '—') ?></div>
                    <button type="button" id="copyRef"><i class="bi bi-clipboard"></i> Copy reference number</button>
                </div>

                <dl class="details">
                    <div class="row-x">
                        <dt>Training</dt>
                        <dd><?= esc($r['training_title'] ?? '') ?></dd>
                    </div>
                    <?php if (!empty($r['training_dates'])): ?>
                        <div class="row-x">
                            <dt>Date/s</dt>
                            <dd><?= esc($r['training_dates']) ?></dd>
                        </div>
                    <?php endif; ?>
                    <div class="row-x">
                        <dt>Participant</dt>
                        <dd><?= esc($r['participant_name'] ?? '') ?></dd>
                    </div>
                    <?php if (!empty($r['email'])): ?>
                        <div class="row-x">
                            <dt>Email</dt>
                            <dd><?= esc($maskEmail($r['email'])) ?></dd>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($r['created_at'])): ?>
                        <div class="row-x">
                            <dt>Submitted on</dt>
                            <dd><?= esc(date('F j, Y \a\t g:i A', strtotime($r['created_at']))) ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>

                <div class="notice">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Each participant can submit only once per training. Please keep your reference number for
                        your records. If you think this is a mistake, contact your HRMO.</span>
                </div>
            </div>

            <div class="actions">
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button type="button" class="btn btn-dole"
                    onclick="window.close(); setTimeout(function(){ document.getElementById('closeHint').style.display='block'; }, 300);">
                    <i class="bi bi-x-lg"></i> Close
                </button>
            </div>
            <div id="closeHint" class="text-center text-muted pb-3" style="display:none; font-size:.8rem;">
                You may now close this tab.
            </div>
        </div>

        <div class="foot">DOLE MIMAROPA &middot; Training Impact Assessment</div>
    </div>

    <script>
        document.getElementById('copyRef').addEventListener('click', function () {
            var ref = document.getElementById('refNo').textContent.trim();
            var btn = this;
            var done = function () {
                btn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
                setTimeout(function () { btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy reference number'; }, 1800);
            };
            if (navigator.clipboard) {
                navigator.clipboard.writeText(ref).then(done);
            } else {
                var t = document.createElement('textarea');
                t.value = ref; document.body.appendChild(t); t.select();
                document.execCommand('copy'); document.body.removeChild(t); done();
            }
        });
    </script>
</body>

</html>