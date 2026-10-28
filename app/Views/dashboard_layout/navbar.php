<?php
$fullname = session('fullname') ?? '';
$email = session('email') ?? '';
$words = array_filter(explode(' ', trim($fullname)));

$initials = '';
foreach ($words as $word) {
  $initials .= strtoupper(substr($word, 0, 1));
}
$initials = substr($initials, 0, 2) ?: '?';

/* ---------- Notification data (admins only) ---------- */
$notifRole = strtoupper(trim((string) session('role')));
$showNotif = in_array($notifRole, ['IT ADMINISTRATOR', 'HR ADMINISTRATOR'], true);

$notifTable = 'hr_requests';
$notifItems = [];
$notifTotal = 0;

if ($showNotif) {
  try {
    $notifTotal = db_connect()->table($notifTable)
      ->where('status', 'New Application')
      ->countAllResults();

    $notifItems = db_connect()->table($notifTable)
      ->select('id, reference_no, fullname, service_availed, created_at')
      ->where('status', 'New Application')
      ->orderBy('id', 'DESC')
      ->limit(5)
      ->get()->getResultArray();
  } catch (\Throwable $e) {
    log_message('error', 'Navbar notifications failed: ' . $e->getMessage());
  }
}
?>

<!-- ============ TOP NAVBAR ============ -->
<header class="topbar">
  <div class="topbar-left">

    <button class="sidebar-toggle-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
      data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Open menu">
      <i class="bi bi-list"></i>
    </button>

    <div class="brand">
      <div class="brand-mark">
        <img src="<?= base_url('assets/img/dole_blogo.png') ?>" alt="DOLE MIMAROPA Logo" class="brand-logo">
      </div>

      <span class="brand-name">DOLE MIMAROPA <strong>E-Portal</strong></span>
    </div>
  </div>

  <!-- Global search -->
  <div class="gsearch" id="gsearch" role="search">
    <i class="bi bi-search gsearch-icon"></i>
    <input type="search" id="gsearchInput" class="form-control" placeholder="Search requests, people, trainings…"
      autocomplete="off" maxlength="100" aria-label="Search everything" aria-controls="gsearchResults"
      aria-expanded="false">
    <kbd class="gsearch-kbd d-none d-xl-inline">Ctrl K</kbd>
    <div class="gsearch-results d-none" id="gsearchResults" role="listbox" aria-label="Search results"></div>
  </div>

  <div class="topbar-right d-flex align-items-center gap-2">

    <!-- Search toggle (phones only) -->
    <button type="button" class="notif-btn d-md-none" id="gsearchToggle" aria-label="Search">
      <i class="bi bi-search"></i>
    </button>

    <?php if ($showNotif): ?>
      <!-- Notification bell -->
      <div class="dropdown">
        <button type="button" class="notif-btn" id="notifBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside"
          aria-expanded="false" aria-label="Notifications">
          <i class="bi bi-bell"></i>
          <span class="notif-badge<?= $notifTotal > 0 ? '' : ' d-none' ?>">
            <?= $notifTotal > 99 ? '99+' : (int) $notifTotal ?>
          </span>
        </button>

        <div class="dropdown-menu dropdown-menu-end notif-menu">
          <div class="notif-head">
            <strong>Notifications</strong>
            <span class="text-muted small"><?= (int) $notifTotal ?> new request(s)</span>
          </div>

          <div class="notif-list" id="notifList">
            <?php if (empty($notifItems)): ?>
              <div class="notif-empty">
                <i class="bi bi-bell-slash"></i>
                <span>No new requests.</span>
              </div>
            <?php else: ?>
              <?php foreach ($notifItems as $n): ?>
                <?php
                $notifUrl = site_url('request') . '?' . http_build_query([
                  'status' => 'New Application',
                  'notif' => 1,
                  'ref' => $n['reference_no'],
                  'svc' => $n['service_availed'],
                ]);
                ?>
                <a href="<?= esc($notifUrl, 'attr') ?>" class="notif-item" data-id="<?= (int) $n['id'] ?>">
                  <span class="notif-icon"><i class="bi bi-file-earmark-text"></i></span>
                  <span class="notif-text">
                    <span class="notif-title"><?= esc($n['fullname']) ?></span>
                    <span class="notif-sub"><?= esc($n['service_availed']) ?></span>
                    <span class="notif-time">
                      <?= esc($n['reference_no']) ?> &middot;
                      <?= date('M d, Y g:i A', strtotime($n['created_at'])) ?>
                    </span>
                  </span>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <a href="<?= esc(site_url('request') . '?status=' . rawurlencode('New Application') . '&notif=1', 'attr') ?>"
            class="notif-footer">View all requests</a>
        </div>
      </div>
    <?php endif; ?>

    <!-- User profile -->
    <div class="dropdown">
      <button class="user-menu-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
        <li>
          <hr class="dropdown-divider">
        </li>
        <li><a class="dropdown-item text-danger" href="<?= site_url('logout') ?>"><i
              class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
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

<script>
  (function () {
    'use strict';

    var box = document.getElementById('gsearch');
    var input = document.getElementById('gsearchInput');
    var panel = document.getElementById('gsearchResults');
    var toggle = document.getElementById('gsearchToggle');
    if (!box || !input || !panel) return;

    var ENDPOINT = <?= json_encode(site_url('search')) ?>;
    var STATUS_CLASS = {
      'New Application': 'badge-new-application',
      'Processing': 'badge-processing',
      'For Releasing': 'badge-for-released',
      'Completed': 'badge-completed',
      'Rejected': 'badge-rejected',
      'Canceled': 'badge-canceled',
      'ACTIVE': 'bg-success',
      'INACTIVE': 'bg-secondary'
    };

    var timer = null, controller = null, active = -1;

    function el(tag, cls, text) {
      var n = document.createElement(tag);
      if (cls) n.className = cls;
      if (text !== undefined) n.textContent = text;
      return n;
    }

    function open() {
      panel.classList.remove('d-none');
      input.setAttribute('aria-expanded', 'true');
    }

    function close() {
      panel.classList.add('d-none');
      input.setAttribute('aria-expanded', 'false');
      active = -1;
    }

    function message(text) {
      panel.replaceChildren(el('div', 'gsearch-empty', text));
      open();
    }

    function items() {
      return panel.querySelectorAll('.gsearch-item, .gsearch-more');
    }

    function setActive(i) {
      var list = items();
      if (!list.length) return;
      active = (i + list.length) % list.length;
      list.forEach(function (n, idx) { n.classList.toggle('is-active', idx === active); });
      list[active].scrollIntoView({ block: 'nearest' });
    }

    function render(groups, q) {
      if (!groups.length) {
        message('No results for “' + q + '”');
        return;
      }

      var frag = document.createDocumentFragment();

      groups.forEach(function (g) {
        frag.appendChild(el('div', 'gsearch-group', g.label));

        g.items.forEach(function (it) {
          var a = el('a', 'gsearch-item');
          a.href = it.url;
          a.setAttribute('role', 'option');

          var ico = el('span', 'gsearch-ico');
          ico.appendChild(el('i', 'bi ' + (it.icon || 'bi-search')));
          a.appendChild(ico);

          var txt = el('span', 'gsearch-text');
          txt.appendChild(el('span', 'gsearch-title', it.title));
          txt.appendChild(el('span', 'gsearch-sub', it.sub || ''));
          a.appendChild(txt);

          if (it.badge) {
            a.appendChild(el('span', 'badge ' + (STATUS_CLASS[it.badge] || 'bg-secondary'), it.badge));
          }
          frag.appendChild(a);
        });

        if (g.more) {
          var m = el('a', 'gsearch-more', g.more.text);
          m.href = g.more.url;
          frag.appendChild(m);
        }
      });

      panel.replaceChildren(frag);
      active = -1;
      open();
    }

    function run() {
      var q = input.value.trim();
      if (controller) controller.abort();

      if (q.length < 2) {
        if (q.length === 0) close(); else message('Keep typing…');
        return;
      }

      controller = new AbortController();
      message('Searching…');

      fetch(ENDPOINT + '?q=' + encodeURIComponent(q), {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        signal: controller.signal
      })
        .then(function (r) {
          if (!r.ok) throw new Error('HTTP ' + r.status);
          return r.json();
        })
        .then(function (data) { render(data.groups || [], q); })
        .catch(function (err) {
          if (err.name !== 'AbortError') message('Search is unavailable right now.');
        });
    }

    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(run, 250);
    });

    input.addEventListener('focus', function () {
      if (input.value.trim().length >= 2 && panel.children.length) open();
    });

    input.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown') { e.preventDefault(); setActive(active + 1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(active - 1); }
      else if (e.key === 'Enter') {
        var list = items();
        var target = list[active] || list[0];
        if (target && !panel.classList.contains('d-none')) { e.preventDefault(); window.location.href = target.href; }
      }
      else if (e.key === 'Escape') {
        if (input.value) { input.value = ''; }
        close();
        box.classList.remove('is-open');
        input.blur();
      }
    });

    // Ctrl/Cmd + K from anywhere
    document.addEventListener('keydown', function (e) {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        box.classList.add('is-open');
        input.focus();
        input.select();
      }
    });

    // Click outside closes the list
    document.addEventListener('click', function (e) {
      if (!box.contains(e.target) && !(toggle && toggle.contains(e.target))) {
        close();
        box.classList.remove('is-open');
      }
    });

    // Phones: the search icon shows / hides the box
    if (toggle) {
      toggle.addEventListener('click', function () {
        var opening = !box.classList.contains('is-open');
        box.classList.toggle('is-open', opening);
        if (opening) input.focus(); else close();
      });
    }
  })();
</script>