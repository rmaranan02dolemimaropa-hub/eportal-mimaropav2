<?php
$fmt = static fn($v) => $v === null ? '—' : number_format((float) $v, 2);
$label = $label ?? 'Name of Participants';
?>
<div class="table-responsive">
    <table class="table table-bordered table-sm mb-1">
        <thead>
            <tr>
                <th><?= esc($label) ?></th>
                <?php foreach ($heads as $h): ?>
                    <th class="text-center"><?= esc($h) ?></th>
                <?php endforeach; ?>
                <th class="avg-col">AVERAGE (per pax)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($g['people'] as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?>. <?= esc($p['name']) ?></td>
                    <?php foreach ($p['values'] as $v): ?>
                        <td class="num"><?= $v ?? '' ?></td>
                    <?php endforeach; ?>
                    <td class="avg-col"><?= $fmt($p['avg']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="avg-col">
                <td class="text-start">AVERAGE (as a group):</td>
                <?php foreach ($g['colAvg'] as $a): ?>
                    <td class="num"><?= $fmt($a) ?></td>
                <?php endforeach; ?>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
<div>
    <span class="overall px-3 py-1">Overall</span>
    <strong class="ms-2"><?= $fmt($g['overall']) ?></strong>
</div>