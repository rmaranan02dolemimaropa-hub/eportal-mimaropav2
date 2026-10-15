<?php
/** Annex G sheet. Needs: $report (from TiaReportController::buildAnnexG) */
?>
<div class="annex-g annex-h">

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
    <div class="ah-annex-tag">ANNEX G</div>

    <div class="ag-title">
        <b>DIRECTORY</b><br>
        <b>Participants and Immediate Superior</b><br>
        <?= esc($report['training']['training_title']) ?> held on <?= esc($report['training']['training_dates']) ?>
    </div>

    <table class="ag-table">
        <thead>
            <tr>
                <th>Participants<br>Name &amp; Position<br>(while in training)</th>
                <th>Current Position</th>
                <th>Active email address</th>
                <th>Employment Status<br>(active, transferred<br>(offices), resigned etc)</th>
                <th>Name, Position<br>&amp; Email Address<br>of Immediate Superior</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report['groups'] as $office => $people): ?>
                <tr>
                    <td colspan="5" class="ag-office"><?= esc($office) ?></td>
                </tr>
                <?php foreach ($people as $i => $p): ?>
                    <tr class="ag-row">
                        <td class="ag-name">
                            <?= $i + 1 ?>. <?= esc($p['name']) ?>
                            <?php if ($p['position'] !== ''): ?>
                                <br><span style="margin-left:14px;"><?= esc($p['position']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td></td>
                        <td><?= esc($p['email']) ?></td>
                        <td></td>
                        <td><?= esc($report['superior']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
