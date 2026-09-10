<div class="app-body">

  <?php
  $currentPage = trim(uri_string(), '/');

  $isActive = static function (string $route) use ($currentPage): string {
    return $currentPage === $route ? ' active' : '';
  };

  // Reusable nav links so desktop + mobile always stay in sync
  $navLinks = static function (bool $mobile = false) use ($isActive): string {
    ob_start();
    $dismiss = $mobile ? 'data-bs-dismiss="offcanvas"' : '';
  ?>
    <a href="dashboard" class="nav-link<?= $isActive('dashboard') ?>" <?= $dismiss ?>>
      <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
    </a>
    <a href="manual" class="nav-link<?= $isActive('manual') ?>" <?= $dismiss ?>>
      <i class="bi bi-plus-circle-fill"></i><span>Add New Request</span>
    </a>
    <a href="request" class="nav-link<?= $isActive('request') ?>" <?= $dismiss ?>>
      <i class="bi bi-file-earmark-text"></i><span>Requests</span>
    </a>
    <?php if (session('role') == 'IT ADMINISTRATOR'): ?>
      <a href="users" class="nav-link<?= $isActive('users') ?>" <?= $dismiss ?>>
        <i class="bi bi-people"></i><span>Users</span>
      </a>
    <?php endif; ?>
    <a href="reports" class="nav-link<?= $isActive('reports') ?>" <?= $dismiss ?>>
      <i class="bi bi-bar-chart"></i><span>Reports</span>
    </a>
    <a href="feedback" class="nav-link<?= $isActive('feedback') ?>" <?= $dismiss ?>>
      <i class="bi bi-chat-right-text-fill"></i><span>Feedback</span>
    </a>


    <div class="nav-divider"></div>

    <a href="logout" class="nav-link nav-link-logout" <?= $dismiss ?>>
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
  <div class="offcanvas offcanvas-start sidebar-offcanvas" tabindex="-1"
    id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
      <span class="offcanvas-title" id="mobileSidebarLabel">Menu</span>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
      <nav class="sidebar-nav">
        <?= $navLinks(false) ?>
      </nav>
    </div>
  </div>

  <!-- ...rest of app-body (main content) continues here... -->