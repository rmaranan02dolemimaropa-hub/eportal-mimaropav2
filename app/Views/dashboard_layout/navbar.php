<!-- ============ TOP NAVBAR ============ -->
<header class="topbar">
  <div class="topbar-left">

    <button class="sidebar-toggle-btn d-lg-none" type="button"
      data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar"
      aria-controls="mobileSidebar" aria-label="Open menu">
      <i class="bi bi-list"></i>
    </button>

    <div class="brand">
      <div class="brand-mark"><i class="bi bi-shield-check"></i></div>
      <span class="brand-name">DOLE MIMAROPA <strong>E-Portal</strong></span>
    </div>
  </div>

  <div class="topbar-right">

    <!-- User profile -->
    <div class="dropdown">
      <button class="user-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php
        $fullname = session('fullname') ?? '';
        $words    = array_filter(explode(' ', trim($fullname)));

        $initials = '';
        foreach ($words as $word) {
          $initials .= strtoupper(substr($word, 0, 1));
        }
        $initials = substr($initials, 0, 2) ?: '?';
        ?>

        <span class="avatar"><?= esc($initials) ?></span>
        <span class="user-info d-none d-md-flex">
          <span class="user-name"><?= esc($fullname) ?></span>
          <span class="user-role"><?= esc(session('role')) ?></span>
        </span>
        <i class="bi bi-chevron-down small-chevron"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</header>