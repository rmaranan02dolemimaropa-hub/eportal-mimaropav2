<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<?php
$logs = $logs ?? [];
$filters = $filters ?? [];
$modules = $modules ?? [];
$actions = $actions ?? [];
$total = (int) ($total ?? 0);
$page = (int) ($page ?? 1);
$pages = (int) ($pages ?? 1);
$perPage = (int) ($perPage ?? 25);

// Only the filters that are actually set, so page links and the export keep them
$active = array_filter($filters, static fn($v) => $v !== '' && $v !== null);
$pageUrl = static fn(int $p): string => base_url('activity-logs') . '?' . http_build_query($active + ['page' => $p]);
$exportUrl = base_url('activity-logs/export') . ($active ? '?' . http_build_query($active) : '');

$first = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$last = min($total, $page * $perPage);

// Badge colour by what the action means
$badgeClass = static function (string $action): string {
    $a = strtoupper($action);
    foreach (['FAILED', 'BLOCKED', 'THROTTLED', 'DELETE'] as $k) {
        if (str_contains($a, $k))
            return 'bg-danger';
    }
    foreach (['RESET', 'DEACTIVATE'] as $k) {
        if (str_contains($a, $k))
            return 'bg-warning text-dark';
    }
    foreach (['LOGIN_SUCCESS', 'ACTIVATE', 'ENROLLED'] as $k) {
        if (str_contains($a, $k))
            return 'bg-success';
    }
    foreach (['CREATE', 'ADD', 'SAVE', 'SUBMITTED', 'IMPORT'] as $k) {
        if (str_contains($a, $k))
            return 'bg-primary';
    }
    if (str_contains($a, 'UPDATE') || str_contains($a, 'STATUS'))
        return 'bg-info text-dark';
    return 'bg-secondary';
};
$actionLabel = static fn(string $a): string => ucwords(strtolower(str_replace('_', ' ', $a)));
?>

<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Activity Logs</h1>
            <p class="page-subtitle">Who signed in, what they changed and when. Newest first.</p>
        </div>
    </div>

    <!-- Filters -->
    <section class="panel mb-3">
        <form method="get" action="<?= base_url('activity-logs') ?>" class="row g-2 align-items-end p-3">
            <div class="col-md-3">
                <label for="fq" class="form-label small mb-1">Search</label>
                <input type="text" id="fq" name="q" class="form-control" maxlength="100"
                    placeholder="Name, username, IP or description" value="<?= esc($filters['q'] ?? '', 'attr') ?>">
            </div>
            <div class="col-md-2">
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
            <div class="col-md-2">
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
            <div class="col-md-2 col-6">
                <label for="ffrom" class="form-label small mb-1">From</label>
                <input type="date" id="ffrom" name="date_from" class="form-control"
                    value="<?= esc($filters['date_from'] ?? '', 'attr') ?>">
            </div>
            <div class="col-md-2 col-6">
                <label for="fto" class="form-label small mb-1">To</label>
                <input type="date" id="fto" name="date_to" class="form-control"
                    value="<?= esc($filters['date_to'] ?? '', 'attr') ?>">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-primary" title="Apply filters"><i class="bi bi-funnel"></i></button>
                <a href="<?= base_url('activity-logs') ?>" class="btn btn-outline-secondary" title="Clear filters"><i
                        class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </section>

    <!-- Log table -->
    <section class="panel table-panel">
        <div class="panel-header">
            <h2 class="panel-title">Log Entries</h2>
            <div class="panel-tools">
                <a class="btn tool-btn" href="<?= esc($exportUrl, 'attr') ?>">
                    <i class="bi bi-download"></i><span class="d-none d-md-inline">Export CSV</span>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table requests-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date &amp; Time</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <?= $active ? 'No entries match these filters. Clear them to see everything.' : 'No activity recorded yet.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-nowrap"><?= esc(date('M d, Y h:i:s A', strtotime($log['created_at']))) ?></td>
                            <td>
                                <?php if (!empty($log['fullname'])): ?>
                                    <div><?= esc($log['fullname']) ?></div>
                                    <div class="small text-muted"><?= esc($log['username']) ?></div>
                                <?php else: ?>
                                    <div><?= esc($log['username'] ?: 'Unknown') ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($log['module']) ?></td>
                            <td><span
                                    class="badge <?= $badgeClass($log['action']) ?>"><?= esc($actionLabel($log['action'])) ?></span>
                            </td>
                            <td><?= esc($log['description']) ?></td>
                            <td class="text-nowrap" title="<?= esc($log['user_agent'] ?? '', 'attr') ?>">
                                <?= esc($log['ip_address'] ?: '—') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

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