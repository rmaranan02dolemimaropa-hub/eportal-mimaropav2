<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">

<?php
$filters = $filters ?? ['year' => 0, 'quarter' => 0];
$years = $years ?? [];
$totals = $totals ?? ['trainings' => 0, 'participants' => 0, 'self' => 0, 'superior' => 0];
$statuses = $statuses ?? [];
$statusCounts = $statusCounts ?? [];
$quarterly = $quarterly ?? [];
$attention = $attention ?? [];
$attentionTotal = (int) ($attentionTotal ?? 0);
$latest = $latest ?? [];

$qMonths = [1 => 'Jan–Mar', 2 => 'Apr–Jun', 3 => 'Jul–Sep', 4 => 'Oct–Dec'];
$hasFilter = $filters['year'] > 0 || $filters['quarter'] > 0;

$pct = static fn(int $n, int $d): int => $d > 0 ? (int) min(100, round($n / $d * 100)) : 0;
$avg = static fn($v): string => $v === null ? '—' : number_format((float) $v, 2);

// Safe defaults for values the controller may or may not pass
$selfRate = (int) ($selfRate ?? $pct((int) $totals['self'], (int) $totals['participants']));
$supRate = (int) ($supRate ?? $pct((int) $totals['superior'], (int) $totals['participants']));
$selfAvg = $selfAvg ?? null;
$supAvg = $supAvg ?? null;
$selfRating = $selfRating ?? '';
$supRating = $supRating ?? '';

// "Q2 2026 (Apr–Jun)" from "2026-Q2"
$qLabel = static function (string $k) use ($qMonths): string {
    if (!preg_match('/^(\d{4})-Q([1-4])$/', $k, $m)) {
        return $k;
    }
    return 'Q' . $m[2] . ' ' . $m[1] . ' (' . $qMonths[(int) $m[2]] . ')';
};

// Small progress bar: "3 / 5" with a coloured fill
$progress = static function (int $done, int $of, string $colour = 'primary') use ($pct): string {
    $p = $pct($done, $of);
    return '<div class="td-prog"><div class="td-prog-bar"><div class="td-prog-fill bg-' . $colour . '" style="width:' . $p . '%"></div></div>'
        . '<span class="td-prog-text">' . $done . ' / ' . $of . '</span></div>';
};
$badgeOf = static fn(string $key): string => 'bg-' . ($statuses[$key][2] ?? 'secondary') . ($key === 'rescheduled' ? ' text-dark' : '');

$periodText = ($filters['year'] > 0 ? $filters['year'] : 'All years')
    . ' · ' . ($filters['quarter'] > 0 ? 'Q' . $filters['quarter'] . ' (' . $qMonths[$filters['quarter']] . ')' : 'All quarters');
?>

<style>
    /* Only what the shared dashboard CSS doesn't already cover (prefix td-) */
    .td-filterbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        background: #fff;
        border: 1px solid rgba(30, 64, 175, .06);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(30, 64, 175, .06);
        padding: .85rem 1.25rem;
        margin-bottom: 1rem;
    }

    .td-filterbar .td-period {
        font-size: .85rem;
        color: #64748b;
    }

    .td-filterbar .td-period strong {
        color: #1e293b;
    }

    .td-filter {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: end;
    }

    .td-filter label {
        font-size: .75rem;
        color: #64748b;
        margin-bottom: .15rem;
        display: block;
    }

    .td-filter .form-select {
        padding-right: 2.5rem !important;
        background-position: right .75rem center !important;
        text-overflow: ellipsis;
    }

    #tdYear {
        min-width: 110px;
    }

    #tdQuarter {
        min-width: 175px;
    }

    .td-filter .btn {
        white-space: nowrap;
    }

    /* 6 cards = 3 x 2, so none are left alone on a row */
    .td-stats {
        grid-template-columns: repeat(3, 1fr) !important;
    }

    @media (max-width: 991px) {
        .td-stats {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 575px) {
        .td-stats {
            grid-template-columns: 1fr !important;
        }
    }

    .td-main-col {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-width: 0;
    }

    .td-pad {
        padding: 1.1rem 1.25rem;
    }

    .td-sidepanel {
        margin-top: 1rem;
    }

    .td-break-row {
        margin-bottom: .85rem;
    }

    .td-break-head {
        display: flex;
        justify-content: space-between;
        font-size: .85rem;
        margin-bottom: .25rem;
    }

    .td-break-head i {
        margin-right: .35rem;
    }

    .td-prog {
        display: flex;
        align-items: center;
        gap: .6rem;
        min-width: 105px;
    }

    .td-prog-bar {
        flex: 1;
        height: 8px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }

    .td-prog-fill {
        height: 100%;
        border-radius: 99px;
    }

    .td-prog-text {
        font-size: .75rem;
        color: #475569;
        white-space: nowrap;
    }

    .td-empty {
        padding: 2rem 1rem;
        text-align: center;
        color: #64748b;
    }
</style>

<main class="main-content tia-page">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">TIA Dashboard</h1>
            <p class="page-subtitle">
                Training Impact Assessment at a glance — trainings, rating forms and who still needs to respond.
            </p>
        </div>
        <div class="page-date">
            <i class="bi bi-calendar3"></i>
            <span id="currentDate"></span>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="td-filterbar">
        <div class="td-period">
            <i class="bi bi-funnel me-1"></i>Showing: <strong><?= esc($periodText) ?></strong>
        </div>
        <form method="get" action="<?= site_url('tia-dashboard') ?>" class="td-filter">
            <div>
                <label for="tdYear">Year</label>
                <select id="tdYear" name="year" class="form-select form-select-sm">
                    <option value="0">All years</option>
                    <?php foreach ($years as $yr): ?>
                        <option value="<?= $yr ?>" <?= $filters['year'] === (int) $yr ? 'selected' : '' ?>><?= $yr ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="tdQuarter">Quarter</label>
                <select id="tdQuarter" name="quarter" class="form-select form-select-sm">
                    <option value="0">All quarters</option>
                    <?php foreach ($qMonths as $qn => $label): ?>
                        <option value="<?= $qn ?>" <?= $filters['quarter'] === $qn ? 'selected' : '' ?>>Q<?= $qn ?>
                            (<?= $label ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel me-1"></i>Apply</button>
            <?php if ($hasFilter): ?>
                <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('tia-dashboard') ?>">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Statistic Cards -->
    <section class="stats-grid td-stats" aria-label="TIA summary">

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-journal-bookmark"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= (int) $totals['trainings'] ?>">0</span>
                <span class="stat-label">Trainings</span>
                <span class="stat-trend trend-up"><i
                        class="bi bi-calendar-range me-1"></i><?= $hasFilter ? 'in selected period' : 'all time' ?></span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-people"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= (int) $totals['participants'] ?>">0</span>
                <span class="stat-label">Participants</span>
                <span class="stat-trend trend-up"><i class="bi bi-slash-circle me-1"></i>excl. canceled</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-person-check"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $selfRate ?>" data-suffix="%">0%</span>
                <span class="stat-label">Self-Rater Forms (Annex D)</span>
                <span class="stat-trend trend-up"><i
                        class="bi bi-check2-square me-1"></i><?= number_format($totals['self']) ?> of
                    <?= number_format($totals['participants']) ?> submitted</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-orange"><i class="bi bi-person-workspace"></i></div>
            <div class="stat-body">
                <span class="stat-number" data-target="<?= $supRate ?>" data-suffix="%">0%</span>
                <span class="stat-label">Superior Ratings (Annex E)</span>
                <span class="stat-trend trend-up"><i
                        class="bi bi-check2-square me-1"></i><?= number_format($totals['superior']) ?> of
                    <?= number_format($totals['participants']) ?> submitted</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="stat-body">
                <span class="stat-number"><?= $avg($selfAvg) ?></span>
                <span class="stat-label">Self-Rater Average</span>
                <span class="stat-trend trend-up"><i
                        class="bi bi-star me-1"></i><?= esc($selfRating ?: 'No ratings yet') ?></span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-graph-up"></i></div>
            <div class="stat-body">
                <span class="stat-number"><?= $avg($supAvg) ?></span>
                <span class="stat-label">Superior-Rater Average</span>
                <span class="stat-trend trend-up"><i
                        class="bi bi-star me-1"></i><?= esc($supRating ?: 'No ratings yet') ?></span>
            </div>
        </div>

    </section>

    <!-- Tables + Side Column -->
    <div class="content-grid">

        <div class="td-main-col">

            <!-- Needs attention -->
            <section class="panel table-panel">
                <div class="panel-header">
                    <h2 class="panel-title">
                        <i class="bi bi-exclamation-circle text-warning me-1"></i>Needs Attention
                        <?php if ($attentionTotal > 0): ?>
                            <span class="badge bg-warning text-dark ms-1"><?= $attentionTotal ?></span>
                        <?php endif; ?>
                    </h2>
                </div>

                <?php if (empty($attention)): ?>
                    <div class="td-empty">
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

            <!-- Latest trainings -->
            <section class="panel table-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Latest Trainings</h2>
                    <div class="panel-tools d-flex align-items-center gap-2">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" id="tdSearch" class="form-control" placeholder="Search trainings..."
                                aria-label="Search trainings" />
                        </div>
                        <a href="<?= site_url('tia_page') ?>" class="btn btn-sm btn-outline-secondary text-nowrap">View
                            all</a>
                    </div>
                </div>

                <?php if (empty($latest)): ?>
                    <div class="td-empty">No trainings for this period.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table requests-table align-middle mb-0" id="tdLatestTable">
                            <thead>
                                <tr>
                                    <th>Training</th>
                                    <th>Status</th>
                                    <th>Participants</th>
                                    <th>Self-Rater (D)</th>
                                    <th>Superior (E)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latest as $t): ?>
                                    <tr>
                                        <td data-label="Training">
                                            <strong><?= esc($t['training_title']) ?></strong>
                                            <div class="small text-muted"><?= esc($t['training_dates']) ?></div>
                                        </td>
                                        <td data-label="Status">
                                            <span class="badge <?= $badgeOf($t['_status']) ?>">
                                                <?= esc($statuses[$t['_status']][0] ?? ucfirst($t['_status'])) ?>
                                            </span>
                                        </td>
                                        <td data-label="Participants"><?= (int) $t['_p'] ?></td>
                                        <td data-label="Self-Rater (D)"><?= $progress($t['_self'], $t['_p'], 'success') ?></td>
                                        <td data-label="Superior (E)"><?= $progress($t['_sup'], $t['_p'], 'warning') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Quarterly conduct -->
            <section class="panel table-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Quarterly Conduct of TIA</h2>
                    <a href="<?= site_url('tia-report-bulk') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-printer me-1"></i>Bulk print
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table requests-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Quarter</th>
                                <th class="text-center">Trainings</th>
                                <th class="text-center">Participants</th>
                                <th class="text-center">Annex D</th>
                                <th class="text-center">Annex E</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($quarterly)): ?>
                                <tr>
                                    <td colspan="5" class="td-empty">Nothing to show for this period.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($quarterly as $key => $q): ?>
                                <tr>
                                    <td data-label="Quarter"><strong><?= esc($qLabel((string) $key)) ?></strong></td>
                                    <td class="text-center" data-label="Trainings"><?= $q['trainings'] ?></td>
                                    <td class="text-center" data-label="Participants"><?= $q['participants'] ?></td>
                                    <td class="text-center" data-label="Annex D"><?= $q['self'] ?></td>
                                    <td class="text-center" data-label="Annex E"><?= $q['superior'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="small text-muted px-3 py-2">Canceled trainings are not counted.</div>
            </section>

        </div>

        <!-- Side Column -->
        <div class="side-column">

            <!-- Quick Actions -->
            <section class="panel quick-actions-panel">
                <h2 class="panel-title">Quick Actions</h2>
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

            <!-- Training Status -->
            <section class="panel td-sidepanel">
                <div class="td-pad">
                    <h2 class="panel-title">Training Status</h2>
                    <?php if ($totals['trainings'] === 0): ?>
                        <div class="td-empty">No trainings for this period.</div>
                    <?php else: ?>
                        <div class="mt-3">
                            <?php foreach ($statuses as $key => [$label, $icon, $colour]): ?>
                                <?php $n = (int) ($statusCounts[$key] ?? 0); ?>
                                <div class="td-break-row">
                                    <div class="td-break-head">
                                        <span><i class="bi <?= $icon ?> text-<?= $colour ?>"></i><?= esc($label) ?></span>
                                        <span><?= $n ?> · <?= $pct($n, $totals['trainings']) ?>%</span>
                                    </div>
                                    <div class="td-prog-bar">
                                        <div class="td-prog-fill bg-<?= $colour ?>"
                                            style="width:<?= $pct($n, $totals['trainings']) ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </div>
    </div>

</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Today's date
        const dateEl = document.getElementById("currentDate");
        if (dateEl) {
            dateEl.textContent = new Date().toLocaleDateString("en-US", {
                weekday: "long", year: "numeric", month: "long", day: "numeric"
            });
        }

        // Count-up for stat numbers that have a data-target
        const duration = 900;
        document.querySelectorAll(".stat-number[data-target]").forEach(function (el) {
            const target = parseInt(el.dataset.target, 10) || 0;
            const suffix = el.dataset.suffix || "";
            const start = performance.now();
            function tick(now) {
                const p = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(eased * target).toLocaleString("en-US") + suffix;
                if (p < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        });

        // Search the Latest Trainings table
        const search = document.getElementById("tdSearch");
        const rows = document.querySelectorAll("#tdLatestTable tbody tr");
        if (search && rows.length) {
            search.addEventListener("input", function () {
                const q = search.value.trim().toLowerCase();
                rows.forEach(function (row) {
                    row.style.display = row.textContent.toLowerCase().includes(q) ? "" : "none";
                });
            });
        }
    });
</script>

<?= $this->include('dashboard_layout/loading') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>