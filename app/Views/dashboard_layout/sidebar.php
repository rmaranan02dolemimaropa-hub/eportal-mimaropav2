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
  $navLinks = static function (bool $mobile = false) use ($isActive, $isIT, $isAdmin, $isSuperior): string {
    ob_start();
    $dismiss = $mobile ? 'data-bs-dismiss="offcanvas"' : '';
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

    <?php if ($isIT): ?>
      <a href="<?= site_url('users') ?>" class="nav-link<?= $isActive('users') ?>" <?= $dismiss ?>>
        <i class="bi bi-people"></i><span>Users</span>
      </a>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
      <a href="<?= site_url('reports') ?>" class="nav-link<?= $isActive('reports') ?>" <?= $dismiss ?>>
        <i class="bi bi-bar-chart"></i><span>Reports</span>
      </a>
      <a href="<?= site_url('feedback') ?>" class="nav-link<?= $isActive('feedback') ?>" <?= $dismiss ?>>
        <i class="bi bi-chat-right-text-fill"></i><span>Feedback</span>
      </a>
      <a href="<?= site_url('tia-report') ?>" class="nav-link<?= $isActive('tia-report') ?>" <?= $dismiss ?>>
        <i class="bi bi-clipboard-data"></i><span>TIA Report</span>
      </a>
    <?php endif; ?>

    <!-- TIA: visible to every logged-in role, including SUPERIOR -->


    <?php if ($isSuperior): ?>
      <a href="<?= site_url('tia-superior') ?>" class="nav-link<?= $isActive('tia-superior') ?>" <?= $dismiss ?>>
        <i class="bi bi-file-earmark-post"></i><span>TIA (Superior)</span>
      </a>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
      <a href="<?= site_url('tia_page') ?>" class="nav-link<?= $isActive('tia_page') ?>" <?= $dismiss ?>>
        <i class="bi bi-file-earmark-post"></i><span>TIA</span>
      </a>
      <a href="<?= site_url('employees') ?>" class="nav-link<?= $isActive('employees') ?>" <?= $dismiss ?>>
        <i class="bi bi-person-vcard-fill"></i><span>Employees</span>
      </a>
      <a href="<?= site_url('superiors') ?>" class="nav-link<?= $isActive('superiors') ?>" <?= $dismiss ?>>
        <i class="bi bi-person-vcard-fill"></i><span>Superiors</span>
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