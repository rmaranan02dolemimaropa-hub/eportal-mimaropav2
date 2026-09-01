<!-- ============ TOP NAVBAR ============ -->
<header class="topbar">
  <div class="topbar-left">

    <button class="sidebar-toggle-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
      data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Open menu">
      <i class="bi bi-list"></i>
    </button>

    <div class="brand">
      <div class="brand-mark">
        <img src="assets/img/dole_blogo.png" alt="DOLE MIMAROPA Logo" class="brand-logo">
      </div>
      <span class="brand-name">DOLE MIMAROPA <strong>E-Portal</strong></span>
    </div>
  </div>

  <div class="topbar-right">

    <!-- User profile -->
    <div class="dropdown">
      <button class="user-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php
        $fullname = session('fullname') ?? '';
        $email = session('email') ?? '';
        $words = array_filter(explode(' ', trim($fullname)));

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
        <li>
          <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
            <i class="bi bi-person me-2"></i>Profile
          </a>
        </li>
        <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item text-danger" href="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>



  </div>





</header>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-sm profile-modal-content">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0">
        <div class="d-flex flex-column align-items-center mb-4">
          <span class="avatar avatar-lg mb-3"><?= esc($initials) ?></span>
          <h5 class="mb-0" id="profileModalLabel"><?= esc($fullname) ?></h5>
          <p class="text-muted small mb-0"><?= esc(session('role')) ?></p>
        </div>
      </div>
    </div>
  </div>
</div>