<?php
$val = static fn($k) => trim((string) ($r[$k] ?? ''));
$score = static fn($k) => (isset($r[$k]) && $r[$k] !== '') ? (int) $r[$k] : '';

$attitudinal = [
    'The participant has adopted a more positive attitude towards work.',
    'The participant has shown improved work habits after the training.',
    "The participant's motivation to perform the job has increased.",
    "The participant's interpersonal skill was enhanced.",
    'The participant has been open to learning new things at work.',
];
$performance = [
    "The participant's quality of work has improved.",
    "The participant's completed tasks with fewer errors.",
    "The participant's accomplishes task at a faster rate.",
    'The participant was able to manage workload better.',
    "The participant' became confident in performing work.",
];
$csf = [
    'The training attended by the participant was relevant and directly applicable to the job.',
    'The participant used the new knowledge gained from the training in the job.',
    'The participant used the skills learned from the training in performing tasks.',
    'The participant was able to introduce new systems, procedures or innovations at work.',
    'The participant has frequently applied the new knowledge/skill at work.',
];

$reasonOptions = [
    'dole_strategy' => "crucial to DOLE's long-term strategy",
    'gain_advantage' => 'gain competitive advantage',
    'operational' => 'improve operational efficiency',
    'ongoing_projects' => 'critical to on-going projects/initiatives',
    'employee_dev' => 'support employee development',
];
$picked = json_decode((string) ($r['reasons'] ?? ''), true);
$picked = is_array($picked) ? $picked : [];
$has = static fn($k, $label) => in_array($k, $picked, true) || in_array($label, $picked, true);

$ratingTable = static function (string $heading, array $items, string $prefix, callable $score): void { ?>
    <table class="rate">
        <tr>
            <td class="q head"><?= $heading ?></td>
            <td class="s head">Rating</td>
        </tr>
        <?php foreach ($items as $i => $text): ?>
            <tr>
                <td class="q"><?= $i + 1 ?>.&nbsp; <?= esc($text) ?></td>
                <td class="s"><?= $score($prefix . '_' . ($i + 1)) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php };
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Annex E — <?= esc($val('participant_name')) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/tia-dashboard.css') ?>">
    <style>
        /* only what is specific to this sheet; page, header and print rules come from tia-dashboard.css */
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f1f1;
            margin: 0;
            padding: 16px;
        }

        .toolbar {
            width: 210mm;
            margin: 0 auto 10px;
            text-align: right;
        }

        .toolbar button {
            padding: 5px 14px;
            cursor: pointer;
        }

        .annex-e-sheet {
            font-size: 10pt;
        }

        .annex-e-sheet table {
            width: 100%;
            border-collapse: collapse;
        }

        .annex-e-sheet .title {
            text-align: center;
            margin: 4px 0 10px;
        }

        .annex-e-sheet .title b {
            font-size: 12pt;
        }

        .annex-e-sheet .title .subtitle {
            color: #f26522;
            font-weight: 700;
            font-size: 10pt;
        }

        .annex-e-sheet table.info td {
            border: 1px solid #000;
            padding: 4px 8px;
        }

        .annex-e-sheet table.info td.l {
            text-align: right;
            width: 22%;
        }

        .annex-e-sheet table.info td.l small {
            font-style: italic;
            font-size: 8pt;
        }

        .annex-e-sheet .direction {
            margin: 14px 0 8px;
            text-align: justify;
        }

        .annex-e-sheet .scale {
            margin: 0 0 12px 40px;
            font-style: italic;
            line-height: 1.6;
        }

        .annex-e-sheet h4 {
            margin: 14px 0 6px;
            font-size: 10pt;
        }

        .annex-e-sheet h4 em {
            font-weight: 400;
        }

        .annex-e-sheet table.rate {
            width: 80%;
            margin: 6px auto 12px;
        }

        .annex-e-sheet table.rate td {
            border: 1px solid #000;
            padding: 3px 8px;
            vertical-align: top;
        }

        .annex-e-sheet table.rate td.s {
            width: 90px;
            text-align: center;
            font-weight: 700;
        }

        .annex-e-sheet table.rate td.head {
            font-weight: 400;
        }

        .annex-e-sheet table.rate td.s.head {
            font-weight: 400;
        }

        .annex-e-sheet .line {
            border-bottom: 1px solid #000;
            min-height: 22px;
            margin: 4px 0 10px 24px;
            white-space: pre-wrap;
            padding: 2px 0;
        }

        .annex-e-sheet .line.flat {
            margin-left: 0;
        }

        .annex-e-sheet .reasons {
            display: flex;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .annex-e-sheet .reasons div {
            width: 50%;
            padding: 2px 0 2px 26px;
        }

        .annex-e-sheet .cb {
            display: inline-block;
            width: 26px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: 700;
            margin-right: 4px;
            height: 15px;
            line-height: 14px;
        }

        .annex-e-sheet .others-line {
            display: inline-block;
            min-width: 200px;
            border-bottom: 1px solid #000;
            padding: 0 4px;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .annex-e-sheet table.rate,
            .annex-e-sheet .line {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar no-print"><button onclick="window.print()">Print</button></div>

    <div class="d1 annex-h annex-e-sheet">

        <!-- Header (same as CSF form) -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE">
                </td>
                <td class="agency-title">
                    <p class="country">Republic of the Philippines</p>
                    <p class="dept">DEPARTMENT OF LABOR AND EMPLOYMENT</p>
                    <p class="region">MIMAROPA REGION</p>
                </td>
                <td class="cert-cell">
                    <img src="<?= base_url('assets/img/bagong_pilipinas.png') ?>" alt="Bagong Pilipinas">
                    <img src="<?= base_url('assets/img/iso.png') ?>" alt="ISO 9001:2015 Certification">
                </td>
            </tr>
        </table>
        <div class="ah-annex-tag">ANNEX E</div>

        <div class="title">
            <b>TRAINING IMPACT ASSESSMENT</b><br>
            <span class="subtitle">(SUPERIOR RATER FORM)</span>
        </div>

        <table class="info">
            <tr>
                <td class="l">Training Title:</td>
                <td><?= esc($training['training_title'] ?? '') ?></td>
                <td class="l" style="width:14%">Training Date:</td>
                <td style="width:18%"><?= esc($training['training_dates'] ?? '') ?></td>
            </tr>
            <tr>
                <td class="l">Immediate Superior:</td>
                <td><?= esc($val('superior_name')) ?></td>
                <td class="l">Position:</td>
                <td><?= esc($val('superior_position')) ?></td>
            </tr>
            <tr>
                <td class="l">Name of Staff/Participant:<br><small>(use separate form for 2 pax or more)</small></td>
                <td><?= esc($val('participant_name')) ?></td>
                <td class="l">Position:</td>
                <td><?= esc($val('participant_position')) ?></td>
            </tr>
        </table>

        <p class="direction">
            <b>DIRECTION</b>: This survey intends to measure the training impact of the program on your behavior,
            attitude and performance after attending the above training. Please take your time to provide accurate,
            complete and honest responses. On a scale of 1-4, rate the extent of your agreement on the areas below.
        </p>
        <div class="scale">
            1 = Strongly Disagree<br>2 = Disagree<br>3 = Agree<br>4 = Strongly Agree
        </div>

        <h4>PART I.<br>ATTITUDINAL OUTCOMES. <em>Changes in behavior/attitude.</em></h4>
        <?php $ratingTable('To what extent do you agree with the following:', $attitudinal, 'att', $score); ?>

        <h4>PERFORMANCE IMPACT. <em>Changes in work performance.</em></h4>
        <?php $ratingTable('To what extent do you agree with the following:', $performance, 'perf', $score); ?>

        <table class="rate">
            <tr>
                <td class="q head"><em>"After attending the training, I have observed improved productivity on the
                        following areas"</em></td>
                <td class="s head">Rating</td>
            </tr>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <tr>
                    <td class="q"><?= $i ?>.&nbsp; <?= esc($val("comp_{$i}_text")) ?></td>
                    <td class="s"><?= $score("comp_{$i}") ?></td>
                </tr>
            <?php endfor; ?>
        </table>

        <h4>CRITICAL SUCCESS FACTORS. <em>Application of new knowledge and skills in the workplace.</em></h4>
        <?php $ratingTable('To what extent do you agree with the following:', $csf, 'app', $score); ?>

        <h4>PART II. <em>Changes in Behavior and Performance.</em></h4>
        <div>1. Kindly cite other changes in the participant's behavior as a result of attending the training.</div>
        <div class="line"><?= esc($val('other_behavior_changes')) ?></div>
        <div>2. Please list any areas at work where the participant has shown measurable improvement.</div>
        <div class="line"><?= esc($val('measurable_improvements')) ?></div>

        <h4>PART III. <em>Deferment on the Application of Knowledge and Skills.</em></h4>
        <div>If you have 2-3 questions answered with Disagree to Strongly Disagree under Part I, Critical Success
            Factors, please explain why the new knowledge and skill gained from the training was not applied by the
            participant at work.</div>
        <div class="line flat"><?= esc($val('deferment_reason')) ?></div>

        <h4>PART IV. <em>Reason for sending staff to training.</em></h4>
        <div>What is/are the primary reason/s for sending your staff to the program (check all that applies)</div>
        <div class="reasons">
            <?php foreach ($reasonOptions as $key => $label): ?>
                <div><span class="cb"><?= $has($key, $label) ? '✓' : '' ?></span><?= esc($label) ?></div>
            <?php endforeach; ?>
            <div>
                <span
                    class="cb"><?= (in_array('others', $picked, true) || $val('reason_others_text') !== '') ? '✓' : '' ?></span>others:
                <span class="others-line"><?= esc($val('reason_others_text')) ?></span>
            </div>
        </div>

    </div>
</body>

</html>