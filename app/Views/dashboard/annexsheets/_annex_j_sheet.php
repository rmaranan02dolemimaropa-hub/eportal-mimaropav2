<?php
/** Annex J sheet. Needs: $training (trainings row) and $j (saved Annex J text fields) */
$v = static fn($k) => esc((string) ($j[$k] ?? ''));
?>
<div class="panel d1 annex-h annex-j-sheet">

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
    <div class="ah-annex-tag">ANNEX J</div>

    <div class="aj-title">
        Training Impact Assessment Report on<br>
        <strong><?= esc($training['training_title']) ?></strong> held on <?= esc($training['training_dates']) ?>
    </div>

    <div class="aj-h">I.&nbsp;&nbsp;&nbsp; Background</div>
    <div class="aj-body top"><?= $v('background') ?></div>

    <div class="aj-h">II.&nbsp;&nbsp; Methodology</div>
    <div class="aj-body top"><?= $v('methodology') ?></div>

    <div class="aj-h">III.&nbsp; Findings and Observations</div>

    <div class="aj-lvl">Level 1&nbsp; Evaluation on the aspects of the program.</div>
    <div class="aj-note">• This focuses on design, facilitation and administration as reflected in the Terminal
        Report</div>
    <div class="aj-body"><?= $v('level1') ?></div>

    <div class="aj-lvl">Level 2&nbsp; Evaluation on the knowledge level.</div>
    <div class="aj-note">• Pertains to the knowledge level of the participants before and after the conduct of the
        course as shown in the Pre- and Post-test results.</div>
    <div class="aj-body"><?= $v('level2') ?></div>

    <div class="aj-lvl">Level 3&nbsp; Assessment and Focus Group Discussion Results</div>

    <div class="aj-sub">A.&nbsp; Level of agreement of the participants in achieving the learning objectives</div>
    <div class="aj-body"><?= $v('level3_a') ?></div>

    <div class="aj-sub">B.&nbsp; Application of learning</div>
    <div class="aj-note deep">o Percentage of participants' application of learning</div>
    <div class="aj-body"><?= $v('level3_b') ?></div>

    <div class="aj-sub">C.&nbsp; Achievement with the application of learning</div>
    <div class="aj-note deep">o Percentage on organizational and personal application of
        participants' learning</div>
    <div class="aj-body"><?= $v('level3_c') ?></div>

    <div class="aj-sub">D.&nbsp; Focus Group Discussion Result</div>
    <div class="aj-note deep">• Qualitative discussion on the result of the FGD according to:
        Application of Learning, Benefits Gained, Planned Sustainability</div>
    <div class="aj-body"><?= $v('level3_d') ?></div>

    <div class="aj-h">IV.&nbsp; Analysis</div>
    <div class="aj-body top"><?= $v('analysis') ?></div>

    <div class="aj-h">V.&nbsp;&nbsp; Conclusion</div>
    <div class="aj-body top"><?= $v('conclusion') ?></div>

    <div class="aj-h">VI.&nbsp; Recommendations</div>
    <div class="aj-body top"><?= $v('recommendations') ?></div>

</div>
