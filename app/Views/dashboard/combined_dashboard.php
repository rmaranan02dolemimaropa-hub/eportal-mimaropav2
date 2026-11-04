<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">

<?php
/* ---------- HR requests (from the HR dashboard controller) ---------- */
$requests = $requests ?? [];
$hrStatusClass = [
    'New Application' => 'badge-new-application',
    'Processing' => 'badge-processing',
    'For Releasing' => 'badge-for-released',
];

$hrDateFor = static function (array $row): ?string {

    $valid = static fn($v) => !empty($v) && strpos((string) $v, '0000') !== 0;

    if ($row['status'] === 'Processing' && $valid($row['date_processing'] ?? null)) {
        return $row['date_processing'];
    }
    if ($row['status'] === 'For Releasing' && $valid($row['date_released'] ?? null)) {
        return $row['date_released'];
    }
    return $row['date_requested'] ?? null;
};

// Overdue: New waiting 3+ days, Processing older than 7 days.
// DashboardController passes $hr_attention (all pages); fall back to $requests if it is missing.
$hrAttention = [];
foreach (($hr_attention ?? $requests) as $r) {
    if (!isset($hrStatusClass[$r['status'] ?? ''])) {
        continue;
    }
    $d = $hrDateFor($r);
    $t = $d ? strtotime($d) : false;
    $age = $t ? max(0, (int) floor((time() - $t) / 86400)) : 0;
    if (($r['status'] === 'New Application' && $age >= 3) || ($r['status'] === 'Processing' && $age >= 7)) {
        $r['_age'] = $age;
        $hrAttention[] = $r;
    }
}
usort($hrAttention, static fn($a, $b) => $b['_age'] <=> $a['_age']);
$hrAttentionTotal = isset($hr_attention_total) ? (int) $hr_attention_total : count($hrAttention);
$hrAttention = array_slice($hrAttention, 0, 8);

/* ---------- TIA trainings (from the TIA dashboard controller) ---------- */
$tiaLoaded = isset($attention);   // false until the controller passes TIA data
$attention = $attention ?? [];
$attentionTotal = (int) ($attentionTotal ?? 0);

$pct = static fn(int $n, int $d): int => $d > 0 ? (int) min(100, round($n / $d * 100)) : 0;
$progress = static function (int $done, int $of, string $colour = 'primary') use ($pct): string {
    $p = $pct($done, $of);
    return "<div class=\"cd-prog\"><div class=\"cd-prog-bar\"><div class=\"cd-prog-fill bg-{$colour}\" style=\"inline-size:{$p}%\"></div></div>"
        . '<span class="cd-prog-text">' . $done . ' / ' . $of . '</span></div>';
};

$isAdmin = session('role') == 'IT ADMINISTRATOR';
$totalAttention = $hrAttentionTotal + $attentionTotal;
?>

<style>
    /* Only what the shared dashboard CSS doesn't cover (prefix cd-) */
    .cd-main-col {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-inline-size: 0;
    }

    .cd-sidepanel-title {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #64748b;
        margin: 1rem 0 .5rem;
    }

    .cd-sidepanel-title:first-of-type {
        margin-block-start: .75rem;
    }

    .cd-prog {
        display: flex;
        align-items: center;
        gap: .6rem;
        min-inline-size: 105px;
    }

    .cd-prog-bar {
        flex: 1;
        block-size: 8px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }

    .cd-prog-fill {
        block-size: 100%;
        border-radius: 99px;
    }

    .cd-prog-text {
        font-size: .75rem;
        color: #475569;
        white-space: nowrap;
    }

    .cd-age {
        font-size: .75rem;
        font-weight: 600;
        padding: .15rem .55rem;
        border-radius: 99px;
        background: #fef3c7;
        color: #92400e;
        white-space: nowrap;
    }

    .cd-age.hot {
        background: #fee2e2;
        color: #991b1b;
    }

    .cd-empty {
        padding: 2rem 1rem;
        text-align: center;
        color: #64748b;
    }
</style>

<main class="main-content tia-page">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Overview</h1>
            <p class="page-subtitle">
                Welcome back, <?= esc(session('fullname')) ?> —
                <?php if ($totalAttention > 0): ?>
                    <strong><?= $totalAttention ?></strong> item<?= $totalAttention === 1 ? '' : 's' ?>
                    need<?= $totalAttention === 1 ? 's' : '' ?> your attention.
                <?php else: ?>
                    nothing needs your attention right now.
                <?php endif; ?>
            </p>
        </div>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" id="loginSuccess" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="page-date">
            <i class="bi bi-calendar3"></i>
            <span id="currentDate"></span>
        </div>
    </div>

    <div class="content-grid">

        <!-- ============ NEEDS ATTENTION ============ -->
        <div class="cd-main-col">

            <!-- HR requests -->
            <section class="panel table-panel">
                <div class="panel-header">
                    <h2 class="panel-title">
                        <i class="bi bi-exclamation-circle text-warning me-1"></i>Needs Attention · HR Requests
                        <?php if ($hrAttentionTotal > 0): ?>
                            <span class="badge bg-warning text-dark ms-1"><?= $hrAttentionTotal ?></span>
                        <?php endif; ?>
                    </h2>
                    <a href="<?= site_url('request') ?>" class="btn btn-sm btn-outline-secondary text-nowrap">View
                        all</a>
                </div>

                <?php if (empty($hrAttention)): ?>
                    <div class="cd-empty">
                        <i class="bi bi-check2-circle text-success fs-3 d-block mb-1"></i>
                        No HR requests are overdue.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table requests-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Requestor</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Waiting</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($hrAttention as $r): ?>
                                    <tr>
                                        <td class="ref-no" data-label="Reference No."><?= esc($r['reference_no']) ?></td>
                                        <td data-label="Requestor"><?= esc($r['fullname']) ?></td>
                                        <td data-label="Service"><?= esc($r['service_availed']) ?></td>
                                        <td data-label="Status"><span
                                                class="badge <?= $hrStatusClass[$r['status']] ?>"><?= esc($r['status']) ?></span>
                                        </td>
                                        <td data-label="Waiting">
                                            <span class="cd-age <?= $r['_age'] >= 14 ? 'hot' : '' ?>"><?= $r['_age'] ?>
                                                day<?= $r['_age'] === 1 ? '' : 's' ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="small text-muted px-3 py-2">
                        New requests waiting 3+ days and processing requests older than 7 days, oldest first.
                        <?php if ($hrAttentionTotal > count($hrAttention)): ?>
                            Showing <?= count($hrAttention) ?> of <?= $hrAttentionTotal ?>.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- TIA trainings (shown only when the controller passes TIA data) -->
            <?php if ($tiaLoaded): ?>
                <section class="panel table-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">
                            <i class="bi bi-exclamation-circle text-warning me-1"></i>Needs Attention · TIA
                            <?php if ($attentionTotal > 0): ?>
                                <span class="badge bg-warning text-dark ms-1"><?= $attentionTotal ?></span>
                            <?php endif; ?>
                        </h2>
                        <a href="<?= site_url('tia-report-monitoring') ?>"
                            class="btn btn-sm btn-outline-secondary text-nowrap">Monitoring</a>
                    </div>

                    <?php if (empty($attention)): ?>
                        <div class="cd-empty">
                            <i class="bi bi-check2-circle text-success fs-3 d-block mb-1"></i>
                            Every participant has been rated and has answered. Nothing is pending.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table requests-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Training</th>
                                        <th>Immediate Superior</th>
                                        <th>Self-Rater (D)</th>
                                        <th>Superior (E)</th>
                                        <th class="text-end">Open</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attention as $t): ?>
                                        <?php $code = rawurlencode((string) $t['training_code']); ?>
                                        <tr>
                                            <td data-label="Training">
                                                <strong><?= esc($t['training_title']) ?></strong>
                                                <div class="small text-muted"><?= esc($t['training_dates']) ?></div>
                                            </td>
                                            <td data-label="Immediate Superior">
                                                <?= esc($t['immediate_superior'] ?: '—') ?>
                                                <?php if ((int) ($t['superior_notify_count'] ?? 0) > 0): ?>
                                                    <div class="small text-muted">Notified <?= (int) $t['superior_notify_count'] ?>×
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Self-Rater (D)"><?= $progress($t['_self'], $t['_p'], 'success') ?></td>
                                            <td data-label="Superior (E)"><?= $progress($t['_sup'], $t['_p'], 'warning') ?></td>
                                            <td class="text-end text-nowrap" data-label="Open">
                                                <a class="btn btn-sm btn-outline-secondary"
                                                    href="<?= site_url('tia-report-participant-monitoring') ?>?t=<?= $code ?>"
                                                    title="Participants who have not submitted Annex D">Self</a>
                                                <a class="btn btn-sm btn-outline-secondary"
                                                    href="<?= site_url('tia-report-monitoring') ?>?t=<?= $code ?>"
                                                    title="Participants the superior has not rated">Superior</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($attentionTotal > count($attention)): ?>
                            <div class="small text-muted px-3 py-2">
                                Showing the 8 with the most pending. <?= $attentionTotal - count($attention) ?> more in the
                                <a href="<?= site_url('tia-report-monitoring') ?>">monitoring pages</a>.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </section>
            <?php else: ?>
                <section class="panel table-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">
                            <i class="bi bi-exclamation-circle text-warning me-1"></i>Needs Attention · TIA
                        </h2>
                    </div>
                    <div class="cd-empty">
                        <i class="bi bi-plug fs-3 d-block mb-1"></i>
                        TIA data isn't connected to this page yet.<br>
                        <span class="small">The controller needs to pass <code>$attention</code> and
                            <code>$attentionTotal</code>. Meanwhile see the
                            <a href="<?= site_url('tia-dashboard') ?>">TIA dashboard</a>.</span>
                    </div>
                </section>
            <?php endif; ?>

        </div>

        <!-- ============ QUICK ACTIONS (HR + TIA merged) ============ -->
        <div class="side-column">
            <section class="panel quick-actions-panel">
                <h2 class="panel-title">Quick Actions</h2>

                <div class="cd-sidepanel-title">HR Requests</div>
                <div class="quick-actions-grid">
                    <a href="<?= site_url('reports') ?>" class="quick-action-btn">
                        <i class="bi bi-bar-chart"></i>
                        <span>Generate Report</span>
                    </a>
                    <?php if ($isAdmin): ?>
                        <a href="<?= base_url('users') ?>" class="quick-action-btn">
                            <i class="bi bi-people"></i>
                            <span>Manage Users</span>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="cd-sidepanel-title">TIA</div>
                <div class="quick-actions-grid">
                    <a href="<?= site_url('tia_page') ?>" class="quick-action-btn">
                        <i class="bi bi-card-checklist"></i>
                        <span>Manage Trainings</span>
                    </a>
                    <a href="<?= site_url('tia-report-monitoring') ?>" class="quick-action-btn">
                        <i class="bi bi-hourglass-split"></i>
                        <span>Superior Rating Monitoring</span>
                    </a>
                    <a href="<?= site_url('tia-report-participant-monitoring') ?>" class="quick-action-btn">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Participant Rating Monitoring</span>
                    </a>
                    <a href="<?= site_url('tia-report') ?>" class="quick-action-btn">
                        <i class="bi bi-person-check"></i>
                        <span>Annex D-1</span>
                    </a>
                    <a href="<?= site_url('tia-report-superior') ?>" class="quick-action-btn">
                        <i class="bi bi-person-workspace"></i>
                        <span>Annex E-1</span>
                    </a>
                    <a href="<?= site_url('tia-report-annex-j') ?>" class="quick-action-btn">
                        <i class="bi bi-file-earmark-richtext"></i>
                        <span>Annex J</span>
                    </a>
                </div>
            </section>
        </div>

    </div>

</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateEl = document.getElementById("currentDate");
        if (dateEl) {
            dateEl.textContent = new Date().toLocaleDateString("en-US", {
                weekday: "long", year: "numeric", month: "long", day: "numeric"
            });
        }

        const alertEl = document.getElementById("loginSuccess");
        if (alertEl) {
            setTimeout(function () {
                alertEl.style.transition = "opacity .5s";
                alertEl.style.opacity = "0";
                setTimeout(function () { alertEl.remove(); }, 500);
            }, 3000);
        }
    });
</script>

<?= $this->include('dashboard_layout/loading') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>