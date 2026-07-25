<!-- ============ TOP NAVBAR ============ -->
<header class="topbar">
  <div class="topbar-left">


    <div class="brand">
      <div class="brand-mark"><i class="bi bi-shield-check"></i></div>
      <span class="brand-name">DOLE MIMAROPA <strong>E-Portal</strong></span>
    </div>
  </div>

  <div class="topbar-right">
    <!-- Notifications -->
    <div class="dropdown">
      <button class="icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
        <i class="bi bi-bell"></i>
        <span class="notif-dot"></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end notif-menu">
        <li class="dropdown-header">Notifications</li>
        <li><a class="dropdown-item" href="#"><i class="bi bi-file-earmark-check text-success me-2"></i>Request
            HR-202607240001 completed</a></li>
        <li><a class="dropdown-item" href="#"><i class="bi bi-hourglass-split text-warning me-2"></i>New request
            awaiting review</a></li>
        <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-up-right text-purple me-2"></i>Certificate
            released to requestor</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item text-center small" href="#">View all notifications</a></li>
      </ul>
    </div>

    <!-- User profile -->
    <div class="dropdown">
      <button class="user-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php
        $name = session('fullname');
        $words = explode(' ', trim($name));

        $initials = '';

        foreach ($words as $word) {
          $initials .= strtoupper(substr($word, 0, 1));
        }

        $initials = substr($initials, 0, 2);
        ?>

        <span class="avatar">
          <?= esc($initials) ?>
        </span>
        <span class="user-info d-none d-md-flex">
          <span class="user-name"><?= session('fullname') ?></span>
          <span class="user-role"><?= session('role') ?></span>
        </span>
        <i class="bi bi-chevron-down small-chevron"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
        <li><a class="dropdown-item" href="#"><i class="bi bi-key me-2"></i>Change Password</a></li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</header>