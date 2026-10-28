<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<?php
$logs = $logs ?? [];
$filters = $filters ?? [];
$modules = $modules ?? [];
$actions = $actions ?? [];
$groupCounts = $groupCounts ?? [];   // optional, see controller patch
$total = (int) ($total ?? 0);
$page = (int) ($page ?? 1);
$pages = (int) ($pages ?? 1);
$perPage = (int) ($perPage ?? 25);

$view = $filters['view'] ?? '';
$active = array_filter($filters, static fn($v) => $v !== '' && $v !== null);
$activeNoView = array_diff_key($active, ['view' => 1]);
$hasPanelFilters = (bool) $activeNoView;

$pageUrl = static fn(int $p): string => base_url('activity-logs') . '?' . http_build_query($active + ['page' => $p]);
$viewUrl = static fn(string $v): string => base_url('activity-logs') . ($v !== '' || $activeNoView
    ? '?' . http_build_query($activeNoView + ($v !== '' ? ['view' => $v] : []))
    : '');
$exportUrl = base_url('activity-logs/export') . ($active ? '?' . http_build_query($active) : '');

$first = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$last = min($total, $page * $perPage);

// Row highlight: critical = red, needs attention = amber (Bootstrap table variants)
$rowClass = static function (string $action): string {
    $a = strtoupper($action);
    foreach (['FAILED', 'BLOCKED', 'THROTTLED', 'DELETE'] as $k) {
        if (str_contains($a, $k))
            return 'table-danger';
    }
    foreach (['RESET', 'DEACTIVATE'] as $k) {
        if (str_contains($a, $k))
            return 'table-warning';
    }
    return '';
};

// Badge tone by what the action means (colours live in the stylesheet)
$badgeClass = static function (string $action): string {
    $a = strtoupper($action);
    // Request statuses: same colours as the status badges on the dashboard / Requests page
    $status = [
        'NEW_APPLICATION' => 'badge-new-application',
        'PROCESSING' => 'badge-processing',
        'RELEASING' => 'badge-for-released',
        'COMPLETE' => 'badge-completed',
        'REJECT' => 'badge-rejected',
        'CANCEL' => 'badge-canceled',
    ];
    foreach ($status as $k => $class) {
        if (str_contains($a, $k))
            return $class;
    }
    foreach (['FAILED', 'BLOCKED', 'THROTTLED', 'DELETE'] as $k) {
        if (str_contains($a, $k))
            return 'log-badge-danger';
    }
    foreach (['RESET', 'DEACTIVATE'] as $k) {
        if (str_contains($a, $k))
            return 'log-badge-warning';
    }
    foreach (['LOGIN_SUCCESS', 'ACTIVATE', 'ENROLLED'] as $k) {
        if (str_contains($a, $k))
            return 'log-badge-success';
    }
    foreach (['CREATE', 'ADD', 'SAVE', 'SUBMITTED', 'IMPORT'] as $k) {
        if (str_contains($a, $k))
            return 'log-badge-primary';
    }
    foreach (['UPDATE', 'STATUS', 'PROCESSING'] as $k) {
        if (str_contains($a, $k))
            return 'log-badge-info';
    }
    if (str_contains($a, 'EXPORT'))
        return 'log-badge-purple';
    return 'log-badge-neutral';
};

// Bootstrap icon per action
$icon = static function (string $action): string {
    $a = strtoupper($action);
    $map = [
        'NEW_APPLICATION' => 'bi-hourglass-split',
        'RELEASING' => 'bi-box-arrow-up-right',
        'COMPLETE' => 'bi-check-circle',
        'REJECT' => 'bi-x-circle',
        'CANCEL' => 'bi-slash-circle',
        'FAILED' => 'bi-shield-exclamation',
        'BLOCKED' => 'bi-slash-circle',
        'THROTTLED' => 'bi-speedometer2',
        'DELETE' => 'bi-trash3',
        'RESET' => 'bi-key',
        'DEACTIVATE' => 'bi-person-dash',
        'ACTIVATE' => 'bi-person-check',
        'LOGIN' => 'bi-box-arrow-in-right',
        'LOGOUT' => 'bi-box-arrow-right',
        'EXPORT' => 'bi-download',
        'IMPORT' => 'bi-upload',
        'PROCESSING' => 'bi-arrow-repeat',
        'UPDATE' => 'bi-pencil-square',
        'STATUS' => 'bi-arrow-left-right',
        'CREATE' => 'bi-plus-circle',
        'ADD' => 'bi-plus-circle',
        'SAVE' => 'bi-check2-circle',
        'SUBMITTED' => 'bi-send-check',
        'ENROLLED' => 'bi-phone',
    ];
    foreach ($map as $k => $i) {
        if (str_contains($a, $k))
            return $i;
    }
    return 'bi-activity';
};

$actionLabel = static fn(string $a): string => ucwords(strtolower(str_replace('_', ' ', $a)));
$eventsWord = static fn(int $n): string => $n . ($n === 1 ? ' event' : ' events');

// Group the rows on this page: year > month
$groups = [];
foreach ($logs as $log) {
    $ts = strtotime($log['created_at']);
    $groups[date('Y', $ts)][date('Y-m', $ts)][] = $log;
}
?>

<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Activity Logs</h1>
            <p class="page-subtitle">Who signed in, what they changed and when. Newest first. Highlighted entries are
                worth a closer look.</p>
        </div>
    </div>

    <!-- Filters (collapsible) -->
    <div id="logsFilters" class="collapse <?= $hasPanelFilters ? 'show' : '' ?>">
        <section class="panel mb-3 p-0">
            <form method="get" action="<?= base_url('activity-logs') ?>" class="logs-filter">
                <?php if ($view !== ''): ?>
                    <input type="hidden" name="view" value="<?= esc($view, 'attr') ?>">
                <?php endif; ?>
                <div class="lf-search">
                    <label for="fq" class="form-label small mb-1">Search</label>
                    <input type="text" id="fq" name="q" class="form-control" maxlength="100"
                        placeholder="Name, username or description" value="<?= esc($filters['q'] ?? '', 'attr') ?>">
                </div>
                <div>
                    <label for="fmodule" class="form-label small mb-1">Module</label>
                    <select id="fmodule" name="module" class="form-select">
                        <option value="">All modules</option>
                        <?php foreach ($modules as $m): ?>
                            <option value="<?= esc($m, 'attr') ?>" <?= ($filters['module'] ?? '') === $m ? 'selected' : '' ?>>
                                <?= esc($m) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="faction" class="form-label small mb-1">Action</label>
                    <select id="faction" name="action" class="form-select">
                        <option value="">All actions</option>
                        <?php foreach ($actions as $a): ?>
                            <option value="<?= esc($a, 'attr') ?>" <?= ($filters['action'] ?? '') === $a ? 'selected' : '' ?>>
                                <?= esc($actionLabel($a)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="ffrom" class="form-label small mb-1">From</label>
                    <input type="date" id="ffrom" name="date_from" class="form-control"
                        value="<?= esc($filters['date_from'] ?? '', 'attr') ?>">
                </div>
                <div>
                    <label for="fto" class="form-label small mb-1">To</label>
                    <input type="date" id="fto" name="date_to" class="form-control"
                        value="<?= esc($filters['date_to'] ?? '', 'attr') ?>">
                </div>
                <div class="lf-actions">
                    <button type="submit" class="btn tool-btn" title="Apply filters">
                        <i class="bi bi-funnel"></i> Apply
                    </button>
                    <a href="<?= esc($viewUrl($view), 'attr') ?>" class="btn tool-btn" title="Clear filters">
                        <i class="bi bi-x-lg"></i> Clear
                    </a>
                </div>
            </form>
        </section>
    </div>

    <!-- Log entries -->
    <section class="panel table-panel logs-panel">
        <div class="panel-header">
            <h2 class="panel-title">Log Entries</h2>
            <div class="panel-tools">
                <button type="button" class="btn tool-btn" data-bs-toggle="collapse" data-bs-target="#logsFilters"
                    aria-expanded="<?= $hasPanelFilters ? 'true' : 'false' ?>" aria-controls="logsFilters">
                    <i class="bi bi-sliders"></i><span
                        class="d-none d-md-inline">Filters<?= $hasPanelFilters ? ' (' . count($activeNoView) . ')' : '' ?></span>
                </button>
                <a class="btn tool-btn" href="<?= esc($exportUrl, 'attr') ?>">
                    <i class="bi bi-download"></i><span class="d-none d-md-inline">Export CSV</span>
                </a>
            </div>
        </div>

        <!-- View pills -->
        <nav class="status-filter" aria-label="Log views">
            <a href="<?= esc($viewUrl(''), 'attr') ?>" class="status-pill <?= $view === '' ? 'active' : '' ?>">All
                events</a>
            <a href="<?= esc($viewUrl('activity'), 'attr') ?>"
                class="status-pill <?= $view === 'activity' ? 'active' : '' ?>">Activity only</a>
            <a href="<?= esc($viewUrl('signin'), 'attr') ?>"
                class="status-pill <?= $view === 'signin' ? 'active' : '' ?>">Sign-in activity</a>
        </nav>

        <?php if (empty($logs)): ?>
            <div class="log-empty">
                <?= $active ? 'No entries match these filters. Clear them to see everything.' : 'No activity recorded yet.' ?>
            </div>
        <?php endif; ?>

        <?php foreach ($groups as $year => $months):
            $yearCount = $groupCounts['years'][$year] ?? array_sum(array_map('count', $months)); ?>
            <details class="log-year" open>
                <summary>
                    <h3 class="panel-title"><?= esc($year) ?></h3>
                    <span class="log-count"><?= esc($eventsWord((int) $yearCount)) ?></span>
                </summary>

                <?php foreach ($months as $ym => $rows):
                    $monthCount = $groupCounts['months'][$ym] ?? count($rows); ?>
                    <details class="log-month" open>
                        <summary>
                            <span><?= esc(date('F', strtotime($ym . '-01'))) ?></span>
                            <span class="log-count"><?= esc($eventsWord((int) $monthCount)) ?></span>
                        </summary>

                        <div class="table-responsive mh-100">
                            <table class="table requests-table logs-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Person</th>
                                        <th>Module</th>
                                        <th>When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $log):
                                        $ts = strtotime($log['created_at']); ?>
                                        <tr class="<?= $rowClass($log['action']) ?>">
                                            <td data-label="Event">
                                                <div>
                                                    <span class="badge <?= $badgeClass($log['action']) ?>">
                                                        <i class="bi <?= $icon($log['action']) ?>"></i>
                                                        <?= esc($actionLabel($log['action'])) ?>
                                                    </span>
                                                    <?php if (!empty($log['description'])): ?>
                                                        <div class="log-desc"><?= esc($log['description']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td data-label="Person">
                                                <div>
                                                    <?php if (!empty($log['fullname'])): ?>
                                                        <div><?= esc($log['fullname']) ?></div>
                                                        <div class="small text-muted"><?= esc($log['username']) ?></div>
                                                    <?php else: ?>
                                                        <div><?= esc($log['username'] ?: 'Unknown') ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td data-label="Module"><?= esc($log['module']) ?></td>
                                            <td class="text-nowrap" data-label="When"
                                                title="<?= esc(date('M d, Y h:i:s A', $ts), 'attr') ?>">
                                                <div>
                                                    <div><?= esc(date('D j', $ts)) ?></div>
                                                    <div class="small text-muted"><?= esc(date('g:i A', $ts)) ?></div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </details>
                <?php endforeach; ?>
            </details>
        <?php endforeach; ?>

        <nav class="table-pagination">
            <span class="pagination-summary">Showing <?= $first ?>–<?= $last ?> of <?= $total ?> entries</span>

            <?php if ($pages > 1): ?>
                <ul class="pagination mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $page <= 1 ? '#' : esc($pageUrl($page - 1), 'attr') ?>">
                            <i class="bi bi-chevron-left"></i> Previous</a>
                    </li>

                    <?php
                    $from = max(1, $page - 2);
                    $to = min($pages, $page + 2);
                    if ($from > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= esc($pageUrl(1), 'attr') ?>">1</a></li>
                        <?php if ($from > 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $from; $i <= $to; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= esc($pageUrl($i), 'attr') ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($to < $pages): ?>
                        <?php if ($to < $pages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= esc($pageUrl($pages), 'attr') ?>"><?= $pages ?></a>
                        </li>
                    <?php endif; ?>

                    <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $page >= $pages ? '#' : esc($pageUrl($page + 1), 'attr') ?>">
                            Next <i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>
    </section>
</main>

</div>

<?= $this->include('dashboard_layout/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Highlight the current sidebar link
        const currentPath = window.location.pathname.replace(/\/+$/, '');
        document.querySelectorAll('.nav-link:not(.nav-link-logout)').forEach(function (link) {
            const href = link.getAttribute('href');
            if (!href || href === '#') return;
            try {
                const linkPath = new URL(link.href).pathname.replace(/\/+$/, '');
                link.classList.toggle('active', currentPath === linkPath || currentPath.startsWith(linkPath + '/'));
            } catch (e) { /* ignore malformed links */ }
        });

        // Loading screen while moving between pages
        const loadingScreen = document.getElementById('doleLoadingScreen');
        if (!loadingScreen) return;

        document.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function (event) {
                const href = this.getAttribute('href');
                if (!href || href === '#' || href === 'javascript:void(0)') return;
                if (event.ctrlKey || event.metaKey || event.shiftKey || event.button === 1) return;
                event.preventDefault();
                loadingScreen.classList.add('show');
                document.body.style.overflow = 'hidden';
                window.location.href = href;
            });
        });

        window.addEventListener('pageshow', function () {
            loadingScreen.classList.remove('show');
            document.body.style.overflow = '';
        });
    });
</script>

<?= $this->include('dashboard_layout/loading') ?>