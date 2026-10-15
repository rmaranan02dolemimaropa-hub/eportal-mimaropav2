<?php
/** Annex H sheet. Needs: $report (from TiaReportController::buildAnnexH) */
$fmt = static fn($v) => $v === null ? '' : number_format((float) $v, 2);
$attendeeRows = max(5, count($report['attendees']));
$compRows = max(3, count($report['competencies']));
?>
<div class="panel d1 annex-h">

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

    <div class="ah-annex-tag">ANNEX H</div>

    <div class="ah-date">
        Date of Evaluation:
        <span class="ah-fill ah-fill-date"><?= esc(date('F j, Y')) ?></span>
    </div>

    <div class="ah-title">
        <div class="t1">TRAINING IMPACT ASSESSMENT FORM</div>
        <div class="t2">For Public Offerings</div>
        <div>Office/Regional Office No. <span class="ah-fill">MIMAROPA (Region IV-B)</span></div>
        <div>Program Title: <span class="ah-fill"><?= esc($report['training']['training_title']) ?></span></div>
        <div>Date/s: <span class="ah-fill"><?= esc($report['training']['training_dates']) ?></span></div>
    </div>

    <table class="ah-table">
        <thead>
            <tr>
                <th style="width:55%">NAME OF ATTENDEES &amp; POSITION</th>
                <th>WITH SUBMITTED INDIVIDUAL TRAINING REPORT?</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < $attendeeRows; $i++): ?>
                <?php
                $a = $report['attendees'][$i] ?? null;
                $who = $a ? $a['name'] . ($a['position'] !== '' ? ', ' . $a['position'] : '') : '';
                $yes = ($a && $a['submitted']) ? '☑' : '☐';
                $no = ($a && !$a['submitted']) ? '☑' : '☐';
                ?>
                <tr>
                    <td class="ah-left"><?= $i + 1 ?>. <?= esc($who) ?></td>
                    <td class="ah-yesno">
                        <span class="ah-yn"><span class="cb"><?= $yes ?></span> Yes</span>
                        <span class="ah-yn"><span class="cb"><?= $no ?></span> No</span>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="ah-heading">COMPETENCIES ADDRESSED*</div>
    <table class="ah-table">
        <thead>
            <tr>
                <th style="width:50%">COMPETENCY CLUSTER</th>
                <th>COMPETENCY</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < $compRows; $i++): ?>
                <tr>
                    <td class="ah-left">&nbsp;</td>
                    <td class="ah-left"><?= esc($report['competencies'][$i] ?? '') ?></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="ah-heading">SURVEY RESULTS</div>
    <table class="ah-table">
        <thead>
            <tr>
                <th style="width:34%">AREAS</th>
                <th style="width:33%">NUMERICAL RATING</th>
                <th>ADJECTIVAL RATING</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report['areas'] as $a): ?>
                <tr>
                    <td class="ah-left">
                        <?= esc($a['label']) ?>
                        <?php if ($a['note'] !== ''): ?>
                            <br><?= esc($a['note']) ?>
                        <?php endif; ?>
                    </td>
                    <td class="ah-center"><?= $fmt($a['score']) ?></td>
                    <td class="ah-center"><?= $a['score'] === null ? '' : esc($a['rating']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td class="ah-right"><strong>Overall Evaluation:</strong></td>
                <td class="ah-center"><strong><?= $fmt($report['overall']['score']) ?></strong></td>
                <td class="ah-center">
                    <strong><?= $report['overall']['score'] === null ? '' : esc($report['overall']['rating']) ?></strong>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="ah-challenge-label">
        List down responses on the challenges encountered and any additional significant inputs:
    </p>
    <div class="ah-lines">
        <?php foreach ($report['challenge_counts'] as $c): ?>
            <div>• <?= esc($c['label']) ?> (<?= (int) $c['total'] ?>)</div>
        <?php endforeach; ?>
        <?php foreach ($report['challenge_others'] as $o): ?>
            <div>• <em>Others:</em> <?= esc($o) ?></div>
        <?php endforeach; ?>
        <?php foreach ($report['ideas'] as $o): ?>
            <div>• <em>Proposed project/innovation:</em> <?= esc($o) ?></div>
        <?php endforeach; ?>
    </div>

    <div class="ah-sign">
        <div class="ah-sign-row">
            <div class="ah-sign-box">
                <div>Prepared by:</div>
                <div class="ah-sign-space"></div>
                <div><em>HRMO</em></div>
            </div>
            <div class="ah-sign-box">
                <div>Endorsed by:</div>
                <div class="ah-sign-space"></div>
                <div><em>IMSD/Division Chief</em></div>
            </div>
        </div>
        <div class="ah-sign-row ah-center-row">
            <div class="ah-sign-box">
                <div>Approved by:</div>
                <div class="ah-sign-space"></div>
                <div>Head of Office</div>
            </div>
        </div>
    </div>

    <div class="ah-foot">
        <em>Only use this Training Impact Assessment Form for Public Trainings with 1-5 attendees.</em><br>
        <em>*Refer to the Approved DOLE Competency Tables</em><br>
        <em>**Very High Impact, High Impact, Moderate Impact, Low Impact and No Impact</em>
    </div>

</div>
