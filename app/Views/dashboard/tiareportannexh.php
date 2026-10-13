<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-dashboard.css') ?>">

<?php
$fmt = static fn($v) => $v === null ? '' : number_format((float) $v, 2);
?>

<main class="main-content">

    <div class="page-header no-print">
        <div>
            <h1 class="page-title">TIA Report</h1>
            <p class="page-subtitle">Annex H – Training Impact Assessment Form for Public Offerings</p>
        </div>
        <?php if ($report && ($report['n'] > 0 || $report['participants'] > 0)): ?>
            <button type="button" class="btn btn-dole-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print / Save as PDF
            </button>
        <?php endif; ?>
    </div>

    <!-- Training picker -->
    <div class="panel mb-3 no-print tia-picker">
        <form method="get" action="<?= site_url('tia-report-annex-h') ?>" class="row g-2 align-items-end p-3">
            <div class="col-md-9">
                <label class="form-label">Training</label>
                <select name="t" class="form-select" onchange="this.form.submit()">
                    <option value="">— Select a training —</option>
                    <?php foreach ($trainings as $t): ?>
                        <option value="<?= esc($t['training_code'], 'attr') ?>" <?= $t['training_code'] === $code ? 'selected' : '' ?>>
                            <?= esc($t['training_title']) ?> (<?= esc($t['training_dates']) ?>)
                            – <?= (int) $t['respondents'] ?> respondent<?= (int) $t['respondents'] === 1 ? '' : 's' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" type="submit">View Form</button>
            </div>
        </form>
    </div>

    <?php if (!$report): ?>
        <div class="panel p-5 text-center text-muted">Select a training to view its Annex H form.</div>

    <?php elseif ($report['n'] === 0 && $report['participants'] === 0): ?>
        <div class="panel p-5 text-center text-muted">
            This training has no participants and no submitted self-rater forms (Annex D) yet.
        </div>

    <?php else: ?>
        <?php if ($report['participants'] > $maxAttendees): ?>
            <div class="alert alert-warning no-print">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Annex H is meant for public trainings with <?= (int) $maxAttendees ?> attendees or fewer.
                This training has <?= (int) $report['participants'] ?> participants. Use Annex D-1 / E-1 instead.
            </div>
        <?php endif; ?>

        <?php
        $attendeeRows = max(5, count($report['attendees']));
        $compRows = max(3, count($report['competencies']));
        ?>

        <div class="panel d1 annex-h">

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

            <!-- Attendees -->
            <table class="ah-table">
                <thead>
                    <tr>
                        <th style="width:55%">NAME OF ATTENDEES &amp; POSITION</th>
                        <th>WITH SUBMITTED INDIVIDUAL TRAINING REPORT?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $attendeeRows; $i++):
                        $a = $report['attendees'][$i] ?? null; ?>
                        <tr>
                            <td class="ah-left">
                                <?= $i + 1 ?>.
                                <?php if ($a): ?>
                                    <?= esc($a['name']) ?>             <?= $a['position'] !== '' ? ', ' . esc($a['position']) : '' ?>
                                <?php endif; ?>
                            </td>
                            <td class="ah-yesno">
                                <span class="ah-yn"><span class="cb"><?= ($a && $a['submitted']) ? '☑' : '☐' ?></span>
                                    Yes</span>
                                <span class="ah-yn"><span class="cb"><?= ($a && !$a['submitted']) ? '☑' : '☐' ?></span>
                                    No</span>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <!-- Competencies -->
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

            <!-- Survey results -->
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

            <!-- Challenges / inputs -->
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

            <!-- Signatories -->
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
    <?php endif; ?>

</main>

<?= $this->include('dashboard_layout/loading') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>