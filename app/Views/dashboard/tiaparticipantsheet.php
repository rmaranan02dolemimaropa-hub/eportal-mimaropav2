<?php
$val = static fn($k) => trim((string) ($r[$k] ?? ''));
$score = static fn($k) => (isset($r[$k]) && $r[$k] !== '') ? (int) $r[$k] : '';

$attitudinal = [
    'I adopted a more positive attitude towards work.',
    'I have shown improved work habits after the training.',
    'My motivation to perform the job has increased.',
    'My interpersonal skill was enhanced.',
    'I have been open to learning new things at work.',
];
$performance = [
    'The quality of my work has improved.',
    'I have completed tasks with fewer errors.',
    'I have accomplished my task at a faster rate.',
    'I was able to manage workload better.',
    'I became confident in performing work.',
];
$application = [
    'The training I attended was relevant and directly applicable to my job.',
    'I was able to use the new knowledge gained from the training in my job.',
    'I was able to use the skills learned from the training in performing my tasks.',
    'I was able to introduce new systems, procedures or innovations at work.',
    'I have frequently applied the new knowledge/skill at work.',
];

// Part III keys (same keys used by the D-1 report)
$barrierOptions = [
    'no_opportunity' => 'I had no opportunity to use the skills',
    'not_enough_time' => "I didn't have enough time to apply the skills/knowledge",
    'work_environment' => 'My work environment is not appropriate for the use of these skills/knowledge',
    'superior_no_support' => 'My superior does not support this type of program',
];
$picked = json_decode((string) ($r['barriers'] ?? ''), true);
$picked = is_array($picked) ? $picked : [];
$tick = static fn(bool $on) => '<span class="cb">' . ($on ? '✓' : '') . '</span>';

$presented = $val('presented_ideas');

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
    <title>Annex D — <?= esc($val('participant_name')) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/tia-dashboard.css') ?>">
    <style>
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

        .annex-d-sheet {
            font-size: 10pt;
        }

        .annex-d-sheet table {
            width: 100%;
            border-collapse: collapse;
        }

        .annex-d-sheet .title {
            text-align: center;
            margin: 4px 0 10px;
            line-height: 1.4;
        }

        .annex-d-sheet .title b {
            font-size: 12pt;
        }

        .annex-d-sheet .title .for {
            font-style: italic;
        }

        .annex-d-sheet .title .subtitle {
            color: #2f75b5;
            font-weight: 700;
            font-size: 10pt;
        }

        .annex-d-sheet table.info td {
            border: 1px solid #000;
            padding: 4px 8px;
        }

        .annex-d-sheet table.info td.l {
            text-align: right;
            width: 24%;
        }

        .annex-d-sheet .direction {
            margin: 14px 0 8px;
            text-align: justify;
        }

        .annex-d-sheet .scale {
            margin: 0 0 12px 40px;
            font-style: italic;
            line-height: 1.6;
        }

        .annex-d-sheet h4 {
            margin: 14px 0 6px;
            font-size: 10pt;
        }

        .annex-d-sheet h4 em {
            font-weight: 400;
        }

        .annex-d-sheet .sub {
            margin: 8px 0 2px;
        }

        .annex-d-sheet table.rate {
            width: 80%;
            margin: 6px auto 12px;
        }

        .annex-d-sheet table.rate td {
            border: 1px solid #000;
            padding: 3px 8px;
            vertical-align: top;
        }

        .annex-d-sheet table.rate td.s {
            width: 90px;
            text-align: center;
            font-weight: 700;
        }

        .annex-d-sheet table.rate td.head {
            font-weight: 400;
        }

        .annex-d-sheet .line {
            border-bottom: 1px solid #000;
            min-height: 22px;
            margin: 4px 0 10px 24px;
            white-space: pre-wrap;
            padding: 2px 0;
        }

        .annex-d-sheet .line.flat {
            margin-left: 0;
        }

        .annex-d-sheet .opt {
            padding: 2px 0 2px 40px;
        }

        .annex-d-sheet .cb {
            display: inline-block;
            width: 26px;
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: 700;
            margin-right: 4px;
            height: 15px;
            line-height: 14px;
        }

        .annex-d-sheet .others-line {
            display: inline-block;
            min-width: 260px;
            border-bottom: 1px solid #000;
            padding: 0 4px;
            white-space: pre-wrap;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .annex-d-sheet table.rate,
            .annex-d-sheet .line {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="toolbar no-print"><button onclick="window.print()">Print</button></div>

    <div class="d1 annex-h annex-d-sheet">

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
        <div class="ah-annex-tag">ANNEX D</div>

        <div class="title">
            <b>TRAINING IMPACT ASSESSMENT</b><br>
            <span class="for">For In-house and Public Trainings</span><br>
            <span class="subtitle">(SELF-RATER FORM)</span>
        </div>

        <table class="info">
            <tr>
                <td class="l">Training Title:</td>
                <td><?= esc($training['training_title'] ?? '') ?></td>
                <td class="l" style="width:16%">Training Date/s:</td>
                <td style="width:20%"><?= esc($training['training_dates'] ?? '') ?></td>
            </tr>
            <tr>
                <td class="l">Name of Participant:</td>
                <td><?= esc($val('participant_name')) ?></td>
                <td class="l">Position:</td>
                <td><?= esc($val('position')) ?></td>
            </tr>
            <tr>
                <td class="l">Office:</td>
                <td colspan="3"><?= esc($val('office') ?: ($training['office'] ?? '')) ?></td>
            </tr>
            <tr>
                <td class="l">Name of Immediate Superior:</td>
                <td colspan="3"><?= esc($training['immediate_superior'] ?? '') ?></td>
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

        <h4>PERFORMANCE IMPACT. <em>Changes in work performance and competencies.</em></h4>
        <?php $ratingTable('To what extent do you agree with the following:', $performance, 'perf', $score); ?>

        <table class="rate">
            <tr>
                <td class="q head"><em>"After attending the training, my productivity on the following areas have
                        improved"</em></td>
                <td class="s head">Rating</td>
            </tr>
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <tr>
                    <td class="q"><?= $i ?>.&nbsp; <?= esc($val("comp_{$i}_text")) ?></td>
                    <td class="s"><?= $score("comp_{$i}") ?></td>
                </tr>
            <?php endfor; ?>
        </table>

        <h4 class="page-break">CRITICAL SUCCESS FACTORS.</h4>
        <div class="sub"><em>A.&nbsp; Achieving Training Objectives.</em></div>
        <table class="rate">
            <tr>
                <td class="q head">To what extent do you agree in achieving or applying the following training
                    objectives at work:</td>
                <td class="s head">Rating</td>
            </tr>
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <tr>
                    <td class="q"><?= $i ?>.&nbsp; I was able to <?= esc($val("obj_{$i}_text")) ?></td>
                    <td class="s"><?= $score("obj_{$i}") ?></td>
                </tr>
            <?php endfor; ?>
        </table>

        <div class="sub"><em>B.&nbsp; Application of new knowledge and skills in the workplace</em></div>
        <?php $ratingTable('To what extent do you agree with the following:', $application, 'app', $score); ?>

        <h4>PART II. <em>Changes in Behavior and Performance.</em></h4>
        <div>1. Kindly cite other changes in behavior you have developed as a result of your attendance to the training.
        </div>
        <div class="line"><?= esc($val('other_behavior_changes')) ?></div>
        <div>2. Please list any areas at work where you believe you have shown measurable improvement.</div>
        <div class="line"><?= esc($val('measurable_improvements')) ?></div>

        <h4>PART III. <em>Challenges.</em></h4>
        <div>What barriers (if any) have you encountered that have prevented you from using skills/knowledge gained from
            the training. Check all that apply.</div>
        <?php foreach ($barrierOptions as $key => $label): ?>
            <div class="opt"><?= $tick(in_array($key, $picked, true)) ?><?= esc($label) ?></div>
        <?php endforeach; ?>
        <div class="opt">
            <?= $tick(in_array('others', $picked, true)) ?>Others (please specify)
            <span
                class="others-line"><?= in_array('others', $picked, true) ? esc($val('barrier_others_text')) : '' ?></span>
        </div>
        <div class="opt"><?= $tick(in_array('none', $picked, true)) ?>None</div>

        <h4>PART IV. <em>Proposed Project and/ or Innovations.</em></h4>
        <div>
            Have you presented new ideas for possible projects to your immediate superior, the management and/or senior
            officials after attending the training?
            &nbsp; <?= $tick($presented === 'Yes') ?>Yes
            &nbsp; <?= $tick($presented !== '' && $presented !== 'Yes') ?>No
        </div>
        <div style="margin-left:40px;">If yes, what are these ideas?</div>
        <div class="line"><?= $presented === 'Yes' ? esc($val('ideas_yes')) : '' ?></div>
        <div style="margin-left:40px;">If no, why?</div>
        <div class="line"><?= ($presented !== '' && $presented !== 'Yes') ? esc($val('ideas_no')) : '' ?></div>

    </div>
</body>

</html>