<?php
/** Annex D-1 sheet. Needs: $report (from TiaReportController::buildReport) */
$fmt = static fn($v) => $v === null ? '—' : number_format((float) $v, 2);
$grid = static fn(array $g, array $heads) => view('dashboard/_d1_grid', ['g' => $g, 'heads' => $heads], ['saveData' => false]);

$compHeads = [];
$objHeads = [];
foreach ($report['comp']['labels'] as $i => $t) {
    $compHeads[] = $t !== '' ? $t : "Competency No $i";
}
foreach ($report['obj']['labels'] as $i => $t) {
    $objHeads[] = $t !== '' ? $t : "Objective $i";
}
?>
<div class="panel d1 annex-d sheet">

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
        <h5 class="mt-2 mb-1 fw-bold">SUMMARY OF SELF-RATER RESPONSES</h5>
        <div><em>Title of the Training:</em> <strong><?= esc($report['training']['training_title']) ?></strong></div>
        <div class="text-muted small"><?= esc($report['training']['training_dates']) ?></div>
    </div>

    <div class="small mb-3">
        1 = Strongly Disagree &nbsp; 2 = Disagree &nbsp; 3 = Agree &nbsp; 4 = Strongly Agree
    </div>

    <table class="table table-bordered table-sm" style="max-width:520px;">
        <thead>
            <tr>
                <th>Respondent's Profile</th>
                <th>Headcount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>No. of participants (based on attendance sheet)</td>
                <td><?= (int) $report['profile']['participants'] ?></td>
            </tr>
            <tr>
                <td>Total no. of survey respondents</td>
                <td><?= (int) $report['profile']['respondents'] ?></td>
            </tr>
            <tr>
                <td>No. of respondents per office</td>
                <td>
                    <?php foreach ($report['profile']['offices'] as $o): ?>
                        <?= esc($o['label']) ?> - <?= $o['total'] ?> (<?= $o['percent'] ?>%)<br>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <td>No. of respondents per position</td>
                <td>
                    <?php foreach ($report['profile']['positions'] as $o): ?>
                        <?= esc($o['label']) ?> - <?= $o['total'] ?> (<?= $o['percent'] ?>%)<br>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <td>Gender</td>
                <td class="text-muted">Not collected</td>
            </tr>
            <tr>
                <td>Age</td>
                <td class="text-muted">Not collected</td>
            </tr>
        </tbody>
    </table>

    <h6>PART I. Attitudinal Outcomes</h6>
    <?= $grid($report['att'], [
        '1. I adopted a more positive attitude towards work.',
        '2. I have shown improved work habits after the training.',
        '3. My motivation to perform the job has increased.',
        '4. My interpersonal skill was enhanced.',
        '5. I have been open to learning new things at work.',
    ]) ?>

    <h6>Performance Impact</h6>
    <?= $grid($report['perf'], [
        '1. The quality of my work has improved.',
        '2. I have completed tasks with fewer errors.',
        '3. I have accomplished my task at a faster rate.',
        '4. I was able to manage workload better.',
        '5. I became confident in performing work.',
    ]) ?>

    <div class="sub">"After attending the training, my productivity on the following areas have improved"</div>
    <?= $grid($report['comp'], $compHeads) ?>

    <h6 class="page-break">Critical Success Factors</h6>
    <div class="sub">Achieving Training Objectives</div>
    <?= $grid($report['obj'], $objHeads) ?>

    <div class="sub">Application of new knowledge and skills in the workplace</div>
    <?= $grid($report['app'], [
        '1. The training I attended was relevant and directly applicable to the job.',
        '2. I was able to use the new knowledge gained from the training in the job.',
        '3. I was able to use the skills learned from the training in performing my tasks.',
        '4. I was able to introduce new systems, procedures or innovations at work.',
        '5. I have frequently applied the new knowledge/skill at work.',
    ]) ?>

    <h6>Part II. Changes in Behavior and Performance</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Name of Participants</th>
                    <th>3. Other changes in behavior as a result of the training <em>(verbatim)</em></th>
                    <th>4. Areas at work with measurable improvement <em>(verbatim)</em></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['part2'] as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?>. <?= esc($p['name']) ?></td>
                        <td><?= esc($p['changes']) ?></td>
                        <td><?= esc($p['improvement']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h6>Part III: Challenges</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Name of Participants</th>
                    <th>I had no opportunity to use the skills</th>
                    <th>I didn't have enough time to apply the skills/knowledge</th>
                    <th>My work environment is not appropriate for the use of these skills/knowledge</th>
                    <th>My superior does not support this type of program</th>
                    <th>None</th>
                    <th>Others (please specify) <em>(verbatim)</em></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['challenges']['people'] as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?>. <?= esc($p['name']) ?></td>
                        <?php foreach ($p['flags'] as $f): ?>
                            <td class="num"><?= $f ? 1 : '' ?></td>
                        <?php endforeach; ?>
                        <td><?= esc($p['others']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="avg-col">
                    <td class="text-start">TOTAL:</td>
                    <?php foreach ($report['challenges']['totals'] as $total): ?>
                        <td class="num"><?= number_format($total, 2) ?></td>
                    <?php endforeach; ?>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <span class="overall px-3 py-1">TOTAL</span>
        <strong class="ms-2"><?= number_format($report['challenges']['total'], 2) ?></strong>
    </div>

    <h6>Part IV: Proposed Project and/or Innovations</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Name of Participants</th>
                    <th>Yes</th>
                    <th>If yes, what are these ideas? <em>(verbatim)</em></th>
                    <th>No</th>
                    <th>If no, why? <em>(verbatim)</em></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['ideas']['people'] as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?>. <?= esc($p['name']) ?></td>
                        <td class="num"><?= $p['yes'] ? 1 : '' ?></td>
                        <td><?= esc($p['ideas']) ?></td>
                        <td class="num"><?= $p['yes'] ? '' : 1 ?></td>
                        <td><?= esc($p['why_not']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="avg-col">
                    <td class="text-start">TOTAL:</td>
                    <td class="num"><?= number_format($report['ideas']['yes'], 2) ?></td>
                    <td></td>
                    <td class="num"><?= number_format($report['ideas']['no'], 2) ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

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
                <td class="num"><?= $fmt($report['summary']['obj']) ?></td>
            </tr>
            <tr>
                <td></td>
                <td class="num"><?= $fmt($report['summary']['app']) ?></td>
            </tr>
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
