<div class="app-body">

  <?php
  $currentPage = trim(uri_string(), '/');

  $isActive = static function (string $route) use ($currentPage): string {
    return $currentPage === $route ? ' active' : '';
  };
  ?>
  <!-- ============ SIDEBAR (desktop) ============ -->
  <aside class="sidebar d-none d-lg-flex">
    <nav class="sidebar-nav">
      <a href="dashboard" class="nav-link <?= $isActive('dashboard') ?>">
        <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
      </a>
      <a href="request" class="nav-link <?= $isActive('request') ?>">
        <i class="bi bi-file-earmark-text"></i><span>Requests</span>
      </a>
      <a href="#" class="nav-link">
        <i class="bi bi-clipboard-check"></i><span>My Requests</span>
      </a>
      <a href="#" class="nav-link">
        <i class="bi bi-folder2-open"></i><span>Services</span>
      </a>
      <?php if (session('role') == 'IT ADMINISTRATOR'): ?>
        <a href="users" class="nav-link">
          <i class="bi bi-people"></i><span>Users</span>
        </a>
      <?php endif; ?>
      <a href="#" class="nav-link">
        <i class="bi bi-bar-chart"></i><span>Reports</span>
      </a>
      <a href="#" class="nav-link">
        <i class="bi bi-gear"></i><span>Settings</span>
      </a>

      <div class="nav-divider"></div>

      <a href="logout" class="nav-link nav-link-logout">
        <i class="bi bi-box-arrow-right"></i><span>Logout</span>
      </a>
    </nav>
  </aside>