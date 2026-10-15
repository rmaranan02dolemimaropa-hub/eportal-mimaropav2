<div class="app-body">

  <?php
  $currentPage = trim(uri_string(), '/');

  /*
  |--------------------------------------------------------------------------
  | ROLES  (add new roles here)
  |--------------------------------------------------------------------------
  */
  $role = strtoupper((string) session('role'));

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
  |--------------------------------------------------------------------------
  */
  $navLinks = static function (bool $mobile = false) use ($currentPage, $isActive, $isIT, $isAdmin, $isSuperior): string {
    ob_start();
    $dismiss = $mobile ? 'data-bs-dismiss="offcanvas"' : '';

    // TIA submenu
    $tiaOpen = ($currentPage === 'tia_page'
      || str_starts_with($currentPage, 'tia_page/')
      || str_starts_with($currentPage, 'tia-report'));
    $subId = $mobile ? 'tiaMenuMobile' : 'tiaMenu';

    // Employees submenu
    $empOpen = ($currentPage === 'employees' || str_starts_with($currentPage, 'employees/'));
    $empSubId = $mobile ? 'empMenuMobile' : 'empMenu';
    ?>

    <?php if ($isAdmin): ?>
      <a href="<?= site_url('dashboard') ?>" class="nav-link<?= $isActive('dashboard') ?>" <?= $dismiss ?>>
        <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
      </a>
      <a href="<?= site_url('manual') ?>" class="nav-link<?= $isActive('manual') ?>" <?= $dismiss ?>>
        <i class="bi bi-plus-circle-fill"></i><span>Add New Request</span>
      </a>
      <a href="<?= site_url('request') ?>" class="nav-link<?= $isActive('request') ?>" <?= $dismiss ?>>
        <i class="bi bi-file-earmark-text"></i><span>Requests</span>
      </a>
    <?php endif; ?>

    <?php if ($isAdmin): ?>

      <a href="<?= site_url('users') ?>" class="nav-link<?= $isActive('users') ?>" <?= $dismiss ?>>
        <i class="bi bi-people"></i><span>Users</span>
      </a>
      <a href="<?= site_url('reports') ?>" class="nav-link<?= $isActive('reports') ?>" <?= $dismiss ?>>
        <i class="bi bi-bar-chart"></i><span>Reports</span>
      </a>
      <a href="<?= site_url('feedback') ?>" class="nav-link<?= $isActive('feedback') ?>" <?= $dismiss ?>>
        <i class="bi bi-chat-right-text-fill"></i><span>Feedback</span>
      </a>

      <!-- TIA (head menu + submenu) -->
      <div class="nav-group">
        <button type="button" class="nav-link nav-parent nav-parent-btn<?= $tiaOpen ? ' active' : '' ?>"
          data-bs-toggle="collapse" data-bs-target="#<?= $subId ?>" aria-expanded="<?= $tiaOpen ? 'true' : 'false' ?>"
          aria-controls="<?= $subId ?>">
          <i class="bi bi-file-earmark-post"></i><span>TIA</span>
        </button>
        <button type="button" class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#<?= $subId ?>"
          aria-expanded="<?= $tiaOpen ? 'true' : 'false' ?>" aria-controls="<?= $subId ?>" aria-label="Toggle TIA menu">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>
      <div class="collapse<?= $tiaOpen ? ' show' : '' ?>" id="<?= $subId ?>">
        <div class="nav-sub">
          <a href="<?= site_url('tia_page') ?>" class="nav-link<?= $isActive('tia_page') ?>" <?= $dismiss ?>>
            <i class="bi bi-card-checklist"></i><span>TIA</span>
          </a>
          <a href="<?= site_url('tia-report-monitoring') ?>" class="nav-link<?= $isActive('tia-report-monitoring') ?>"
            <?= $dismiss ?>>
            <i class="bi bi-hourglass-split"></i><span>Superior Rating Monitoring</span>
          </a>
          <a href="<?= site_url('tia-report-participant-monitoring') ?>"
            class="nav-link<?= $isActive('tia-report-participant-monitoring') ?>" <?= $dismiss ?>>
            <i class="bi bi-person-lines-fill"></i><span>Participant Rating Monitoring</span>
          </a>
          <a href="<?= site_url('tia-report') ?>" class="nav-link<?= $currentPage === 'tia-report' ? ' active' : '' ?>"
            <?= $dismiss ?>>
            <i class="bi bi-person-check"></i><span>Annex D-1 (Self-Rater)</span>
          </a>
          <a href="<?= site_url('tia-report-superior') ?>" class="nav-link<?= $isActive('tia-report-superior') ?>"
            <?= $dismiss ?>>
            <i class="bi bi-person-workspace"></i><span>Annex E-1 (Superior)</span>
          </a>
          <a href="<?= site_url('tia-report-annex-g') ?>" class="nav-link<?= $isActive('tia-report-annex-g') ?>" <?= $dismiss ?>>
            <i class="bi bi-journal-text"></i><span>Annex G (Directory)</span>
          </a>
          <a href="<?= site_url('tia-report-annex-h') ?>" class="nav-link<?= $isActive('tia-report-annex-h') ?>" <?= $dismiss ?>>
            <i class="bi bi-people"></i><span>Annex H (Public Offerings)</span>
          </a>
          <a href="<?= site_url('tia-report-annex-j') ?>" class="nav-link<?= $isActive('tia-report-annex-j') ?>" <?= $dismiss ?>>
            <i class="bi bi-file-earmark-richtext"></i><span>Annex J (TIA Report)</span>
          </a>
          <a href="<?= site_url('tia-report-bulk') ?>" class="nav-link<?= $isActive('tia-report-bulk') ?>" <?= $dismiss ?>>
            <i class="bi bi-printer"></i><span>Bulk Print by Quarter</span>
          </a>
        </div>
      </div>

      <!-- EMPLOYEES (head menu + submenu) -->
      <div class="nav-group">
        <button type="button" class="nav-link nav-parent nav-parent-btn<?= $empOpen ? ' active' : '' ?>"
          data-bs-toggle="collapse" data-bs-target="#<?= $empSubId ?>" aria-expanded="<?= $empOpen ? 'true' : 'false' ?>"
          aria-controls="<?= $empSubId ?>">
          <i class="bi bi-person-vcard-fill"></i><span>Employees</span>
        </button>
        <button type="button" class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#<?= $empSubId ?>"
          aria-expanded="<?= $empOpen ? 'true' : 'false' ?>" aria-controls="<?= $empSubId ?>"
          aria-label="Toggle Employees menu">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>
      <div class="collapse<?= $empOpen ? ' show' : '' ?>" id="<?= $empSubId ?>">
        <div class="nav-sub">
          <a href="<?= site_url('employees/permanent') ?>"
            class="nav-link<?= $currentPage === 'employees/permanent' ? ' active' : '' ?>" <?= $dismiss ?>>
            <i class="bi bi-person-check-fill"></i><span>Permanent</span>
          </a>
          <a href="<?= site_url('employees/non-permanent') ?>"
            class="nav-link<?= $currentPage === 'employees/non-permanent' ? ' active' : '' ?>" <?= $dismiss ?>>
            <i class="bi bi-person-badge"></i><span>Non-Permanent</span>
          </a>
        </div>
      </div>

      <a href="<?= site_url('superiors') ?>" class="nav-link<?= $isActive('superiors') ?>" <?= $dismiss ?>>
        <i class="bi bi-person-vcard-fill"></i><span>Superiors</span>
      </a>
    <?php endif; ?>

    <?php if ($isSuperior): ?>
      <a href="<?= site_url('tia-superior/monitoring') ?>" class="nav-link<?= $isActive('tia-superior/monitoring') ?>"
        <?= $dismiss ?>>
        <i class="bi bi-file-earmark-post"></i><span>TIA (Superior)</span>
      </a>
    <?php endif; ?>

    <div class="nav-divider"></div>

    <a href="<?= site_url('logout') ?>" class="nav-link nav-link-logout" <?= $dismiss ?>>
      <i class="bi bi-box-arrow-right"></i><span>Logout</span>
    </a>
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