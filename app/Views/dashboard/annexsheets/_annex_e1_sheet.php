<?php
/** Annex E-1 sheet. Needs: $report (from TiaReportController::buildSuperiorReport) */
$fmt = static fn($v) => $v === null ? '—' : number_format((float) $v, 2);
$grid = static fn(array $g, array $heads) => view('dashboard/_d1_grid', [
    'g' => $g,
    'heads' => $heads,
    'label' => 'Name of Superior-rater',
], ['saveData' => false]);

$compHeads = [];
foreach ($report['comp']['labels'] as $i => $t) {
    $compHeads[] = $t !== '' ? $t : "Competency No $i";
}
?>
<div class="panel d1 annex-e sheet">

    <div class="header-banner">
        <div class="dole-cell"><img src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE"></div>
        <div class="agency-title">
            <p class="country">Republic of the Philippines</p>
            <p class="dept">DEPARTMENT OF LABOR AND EMPLOYMENT</p>
            <p class="region">MIMAROPA REGION</p>
        </div>
        <div class="iso-cell"><img src="<?= base_url('assets/img/iso.png') ?>" alt="ISO 9001:2015 Certification"></div>
        <div class="bp-cell"><img src="<?= base_url('assets/img/bagong_pilipinas.png') ?>" alt="Bagong Pilipinas"></div>
    </div>

    <div class="text-center mb-3">
        <h5 class="mt-2 mb-1 fw-bold">SUMMARY OF SUPERIOR-RATER RESPONSES</h5>
        <div><em>Title of the Training:</em> <strong><?= esc($report['training']['training_title']) ?></strong></div>
        <div class="text-muted small"><?= esc($report['training']['training_dates']) ?></div>
    </div>

    <div class="small mb-3">
        1 = Strongly Disagree &nbsp; 2 = Disagree &nbsp; 3 = Agree &nbsp; 4 = Strongly Agree
    </div>

    <h6>I. Attitudinal Outcomes</h6>
    <?= $grid($report['att'], [
        '1. The participant has adopted a more positive attitude towards work.',
        '2. The participant has shown improved work habits after the training.',
        '3. The participant\'s motivation to perform the job has increased.',
        '4. The participant\'s interpersonal skill was enhanced.',
        '5. The participant has been open to learning new things at work.',
    ]) ?>

    <h6>II. Performance Impact</h6>
    <?= $grid($report['perf'], [
        '1. The participant\'s quality of work has improved.',
        '2. The participant completed tasks with fewer errors.',
        '3. The participant accomplishes tasks at a faster rate.',
        '4. The participant was able to manage workload better.',
        '5. The participant became confident in performing work.',
    ]) ?>

    <div class="sub">"After attending the training, participant's productivity on the following areas have improved"</div>
    <?= $grid($report['comp'], $compHeads) ?>

    <h6>III. Critical Success Factors</h6>
    <?= $grid($report['app'], [
        '1. The training attended by the participant was relevant and directly applicable to the job.',
        '2. The participant used the new knowledge gained from the training in the job.',
        '3. The participant used the skills learned from the training in performing tasks.',
        '4. The participant was able to introduce new systems, procedures or innovations at work.',
        '5. The participant has frequently applied the new knowledge/skill at work.',
    ]) ?>

    <table class="table table-bordered table-sm mt-4" style="max-width:420px;">
        <tbody>
            <tr>
                <th>I. Attitudinal Outcomes</th>
                <td class="num"><?= $fmt($report['summary']['att']) ?></td>
            </tr>
            <tr>
                <th>II. Performance Impact</th>
                <td class="num"><?= $fmt($report['summary']['perf']) ?></td>
            </tr>
            <tr>
                <td></td>
                <td class="num"><?= $fmt($report['summary']['comp']) ?></td>
            </tr>
            <tr>
                <th>III. Critical Success Factors</th>
                <td class="num"><?= $fmt($report['summary']['app']) ?></td>
            </tr>
            <?php if ($report['summary']['csf_twice']): ?>
                <tr>
                    <td></td>
                    <td class="num"><?= $fmt($report['summary']['app']) ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th class="text-end">Average:</th>
                <td class="num fw-bold"><?= number_format((float) $report['summary']['average'], 3) ?></td>
            </tr>
            <tr>
                <th class="text-end">Adjectival Rating:</th>
                <td class="fw-bold"><?= esc($report['summary']['rating']) ?></td>
            </tr>
        </tbody>
    </table>

</div>
