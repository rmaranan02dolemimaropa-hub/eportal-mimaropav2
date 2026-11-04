<div class="app-body">

  <?php
  $currentPage = trim(uri_string(), '/');

  /*
  |--------------------------------------------------------------------------
  | ROLES  (add new roles here)
  |--------------------------------------------------------------------------
  */
  $role = strtoupper(trim((string) session('role')));

  $isIT = ($role === 'IT ADMINISTRATOR');
  $isHR = ($role === 'HR ADMINISTRATOR');
  $isSuperior = ($role === 'SUPERIOR');

  $isAdmin = $isIT || $isHR;   // IT + HR together
  
  /*
  |--------------------------------------------------------------------------
  | ACTIVE LINK  (also highlights sub-pages, e.g. request/history/5)
  |--------------------------------------------------------------------------
  */
  $isActive = static function (string $route) use ($currentPage): string {
    return ($currentPage === $route || str_starts_with($currentPage, $route . '/')) ? ' active' : '';
  };

  /*
  |--------------------------------------------------------------------------
  | MENU LINKS  (written once, used by desktop + mobile)
  | No data-bs-dismiss on links: it blocks navigation, and the page
  | reloads after every click anyway.
  |--------------------------------------------------------------------------
  */
  $navLinks = static function (bool $mobile = false) use ($currentPage, $isActive, $isIT, $isAdmin, $isSuperior): string {
    ob_start();

    /*
     * Head menu + submenu.
     * $items: each is [url, icon, label] or [url, icon, label, 'exact'] (exact = highlight only on that URL).
     * The submenu opens by itself when one of its pages is the current page.
     * $headUrl: when set, the head button is a link to that page; when null, it just expands/collapses.
     */
    $group = static function (string $id, string $icon, string $label, array $items, bool $forceOpen = false, ?string $headUrl = null) use ($currentPage, $isActive, $mobile): void {
      $subId = $id . ($mobile ? 'Mobile' : '');
      $open = $forceOpen;
      $rows = [];

      foreach ($items as $it) {
        $exact = ($it[3] ?? '') === 'exact';
        $cls = $exact ? ($currentPage === $it[0] ? ' active' : '') : $isActive($it[0]);
        if ($cls !== '') {
          $open = true;
        }
        $rows[] = [$it[0], $it[1], $it[2], $cls];
      }
      ?>
      <div class="nav-group">
        <?php if ($headUrl !== null): ?>
          <!-- Head button is a link: clicking it opens $headUrl (its menu then opens by itself) -->
          <a href="<?= site_url($headUrl) ?>" class="nav-link nav-parent<?= $open ? ' active' : '' ?>">
            <i class="bi <?= $icon ?>"></i><span><?= $label ?></span>
          </a>
        <?php else: ?>
          <!-- Head button only expands / collapses the submenu -->
          <button type="button" class="nav-link nav-parent nav-parent-btn<?= $open ? ' active' : '' ?>"
            data-bs-toggle="collapse" data-bs-target="#<?= $subId ?>" aria-expanded="<?= $open ? 'true' : 'false' ?>"
            aria-controls="<?= $subId ?>">
            <i class="bi <?= $icon ?>"></i><span><?= $label ?></span>
          </button>
        <?php endif; ?>
        <button type="button" class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#<?= $subId ?>"
          aria-expanded="<?= $open ? 'true' : 'false' ?>" aria-controls="<?= $subId ?>"
          aria-label="Toggle <?= $label ?> menu">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>
      <div class="collapse<?= $open ? ' show' : '' ?>" id="<?= $subId ?>">
        <div class="nav-sub">
          <?php foreach ($rows as [$url, $ico, $text, $cls]): ?>
            <a href="<?= site_url($url) ?>" class="nav-link<?= $cls ?>">
              <i class="bi <?= $ico ?>"></i><span><?= $text ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php
    };

    // TIA also stays open on any tia-report* page
    $tiaOpen = str_starts_with($currentPage, 'tia-report');
    ?>

    <?php if ($isAdmin): ?>

      <!-- OVERVIEW (combined HR + TIA: Needs Attention and Quick Actions) -->
      <a href="<?= site_url('overview') ?>" class="nav-link<?= $isActive('overview') ?>">
        <i class="bi bi-speedometer2"></i><span>Overview</span>
      </a>

      <!-- HR REQUEST (head opens the HR dashboard; menu stays open there) -->
      <?php
      $group('hrMenu', 'bi-folder2-open', 'HR Request', [
        ['manual', 'bi-plus-circle-fill', 'Add New Request'],
        ['request', 'bi-file-earmark-text', 'Requests'],
        ['reports', 'bi-bar-chart', 'Reports'],
        ['feedback', 'bi-chat-right-text-fill', 'Feedback'],
      ], $isActive('dashboard') !== '', 'dashboard');
      ?>

      <!-- TIA (head opens the TIA dashboard; menu stays open there) -->
      <?php
      $group('tiaMenu', 'bi-file-earmark-post', 'TIA', [
        ['tia_page', 'bi-card-checklist', 'TIA'],
        ['tia-report-monitoring', 'bi-hourglass-split', 'Superior Rating Monitoring'],
        ['tia-report-participant-monitoring', 'bi-person-lines-fill', 'Participant Rating Monitoring'],
        ['tia-report', 'bi-person-check', 'Annex D-1 (Self-Rater)', 'exact'],
        ['tia-report-superior', 'bi-person-workspace', 'Annex E-1 (Superior)'],
        ['tia-report-annex-g', 'bi-journal-text', 'Annex G (Directory)'],
        ['tia-report-annex-h', 'bi-people', 'Annex H (Public Offerings)'],
        ['tia-report-annex-j', 'bi-file-earmark-richtext', 'Annex J (TIA Report)'],
        ['tia-report-bulk', 'bi-printer', 'Bulk Print by Quarter'],
      ], $tiaOpen || $isActive('tia-dashboard') !== '', 'tia-dashboard');
      ?>

      <!-- SETTINGS (expand / collapse only) -->
      <?php
      $settings = [
        ['users', 'bi-people', 'Users'],
      ];
      if ($isIT) {
        $settings[] = ['activity-logs', 'bi-clock-history', 'Activity Logs'];   // IT Administrator only
      }
      $settings[] = ['employees/permanent', 'bi-person-check-fill', 'Employees: Permanent', 'exact'];
      $settings[] = ['employees/non-permanent', 'bi-person-badge', 'Employees: Non-Permanent', 'exact'];
      $settings[] = ['superiors', 'bi-person-vcard-fill', 'Superiors'];

      $group('settingsMenu', 'bi-gear-fill', 'Settings', $settings);
      ?>

    <?php endif; ?>

    <?php if ($isSuperior): ?>
      <a href="<?= site_url('tia-superior/monitoring') ?>" class="nav-link<?= $isActive('tia-superior/monitoring') ?>">
        <i class="bi bi-file-earmark-post"></i><span>TIA (Superior)</span>
      </a>
    <?php endif; ?>

    <div class="nav-divider"></div>

    <form method="post" action="<?= site_url('logout') ?>" class="m-0">
      <?php helper('form'); ?>   <?= csrf_field() ?>
      <button type="submit" class="nav-link nav-link-logout"
        style="width:100%;background:none;border:0;text-align:left;font:inherit;font-weight:600;font-size:.9rem;cursor:pointer;">
        <i class="bi bi-box-arrow-right"></i><span>Logout</span>
      </button>
    </form>
    <?php
    return (string) ob_get_clean();
  };
  ?>

  <!-- ============ SIDEBAR (desktop, ≥992px) ============ -->
  <aside class="sidebar d-none d-lg-flex">
    <nav class="sidebar-nav">
      <?= $navLinks(false) ?>
    </nav>
  </aside>

  <!-- ============ OFFCANVAS SIDEBAR (mobile / tablet, <992px) ============ -->
  <div class="offcanvas offcanvas-start sidebar-offcanvas" tabindex="-1" id="mobileSidebar"
    aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
      <span class="offcanvas-title" id="mobileSidebarLabel">Menu</span>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
      <nav class="sidebar-nav">
        <?= $navLinks(true) ?>
      </nav>
    </div>
  </div>

  <!-- ...rest of app-body (main content) continues here... -->