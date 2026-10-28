<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<?php
$users = $users ?? [];

// Roles stored in the database (ENUM values are UPPERCASE) => label shown to the user
$roles = [
    'IT ADMINISTRATOR' => 'IT Administrator',
    'HR ADMINISTRATOR' => 'HR Administrator',
    'SUPERIOR' => 'Superior',
];

// Display label for a role value coming from the database (case-insensitive)
$roleLabel = static function (?string $role) use ($roles): string {
    $key = strtoupper(trim((string) $role));
    return $roles[$key] ?? (string) $role;
};
?>


<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">


    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">User Management</h1>
            <p class="page-subtitle">
                Welcome back, <?= esc(session('fullname')) ?> — create, update, and manage user accounts and system
                access.
            </p>
        </div>
        <div class="page-date">
            <i class="bi bi-calendar3"></i>
            <span id="currentDate"></span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" id="successAlert">
            <?= esc(session()->getFlashdata('success')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= esc(session()->getFlashdata('error')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
                <div><?= esc($err) ?></div>
            <?php endforeach; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- User Accounts Table -->
    <section class="panel table-panel">
        <div class="panel-header">
            <h2 class="panel-title">User Accounts</h2>
            <div class="panel-tools">
                <button class="btn tool-btn" type="button" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill"></i><span class="d-none d-md-inline">Add User</span>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table requests-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Two-Step</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>

                        <?php foreach ($users as $row): ?>
                            <tr>
                                <td data-label="Full Name"><?= esc($row['fullname']) ?></td>
                                <td data-label="Username"><?= esc($row['username']) ?></td>
                                <td data-label="Role"><?= esc($roleLabel($row['role'])) ?></td>

                                <td data-label="Status">
                                    <?php if ($row['status'] === 'ACTIVE'): ?>
                                        <span class="badge badge-completed">ACTIVE</span>
                                    <?php else: ?>
                                        <span class="badge badge-rejected">INACTIVE</span>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Two-Step">
                                    <?php if ((int) ($row['mfa_enabled'] ?? 0) === 1): ?>
                                        <span class="badge badge-completed"><i class="bi bi-shield-check"></i> ENABLED</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="bi bi-shield-exclamation"></i> NOT SET UP</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end" data-label="Action">
                                    <div class="row-actions">
                                        <button class="row-action-btn" title="View" data-bs-toggle="modal"
                                            data-bs-target="#view<?= $row['id'] ?>"><i class="bi bi-eye"></i></button>

                                        <button class="row-action-btn" title="Edit" data-bs-toggle="modal"
                                            data-bs-target="#edit<?= $row['id'] ?>"><i class="bi bi-pencil"></i></button>

                                        <?php if ($row['status'] === 'ACTIVE'): ?>
                                            <button class="row-action-btn row-action-danger" title="Deactivate"
                                                data-bs-toggle="modal" data-bs-target="#status<?= $row['id'] ?>">
                                                <i class="bi bi-person-slash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="row-action-btn" title="Activate" data-bs-toggle="modal"
                                                data-bs-target="#status<?= $row['id'] ?>">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php $mfaOn = (int) ($row['mfa_enabled'] ?? 0) === 1; ?>
                                        <form action="<?= base_url('users/reset-mfa/' . $row['id']) ?>" method="post"
                                            class="d-inline"
                                            onsubmit="return confirm('Reset two-step verification for this user? They will have to set it up again at next login.');">
                                            <?= csrf_field() ?>
                                            <button class="row-action-btn"
                                                title="<?= $mfaOn ? 'Reset two-step verification' : 'Not set up yet' ?>"
                                                <?= $mfaOn ? '' : 'disabled' ?>><i class="bi bi-shield-lock"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr class="empty-row">
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted"></i><br>
                                No users found.
                            </td>
                        </tr>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <nav class="table-pagination">

                <span class="pagination-summary">
                    Showing <?= count($users) ?> of <?= $pager->getTotal() ?> users
                </span>

                <ul class="pagination mb-0">

                    <!-- Previous -->
                    <li class="page-item <?= $pager->getCurrentPage() == 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="<?= $pager->getCurrentPage() == 1 ? '#' : $pager->getPageURI($pager->getCurrentPage() - 1) ?>">
                            <i class="bi bi-chevron-left"></i> Previous
                        </a>
                    </li>

                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $pager->getPageCount(); $i++): ?>
                        <li class="page-item <?= $i == $pager->getCurrentPage() ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $pager->getPageURI($i) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next -->
                    <li class="page-item <?= $pager->getCurrentPage() == $pager->getPageCount() ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="<?= $pager->getCurrentPage() == $pager->getPageCount() ? '#' : $pager->getPageURI($pager->getCurrentPage() + 1) ?>">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>

                </ul>

            </nav>
        <?php endif; ?>

    </section>
</main>

<!-- ===================== ADD USER MODAL ===================== -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?= base_url('users/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus"></i> Add User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="example@dole.gov.ph"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter a strong password" minlength="8" autocomplete="new-password"
                                    required>

                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>

                            <small class="text-muted">
                                Minimum 8 characters. Use uppercase, lowercase, number, and special character.
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Role</label>

                            <select name="role" id="addRole" class="form-select" required>
                                <option value="" selected disabled>Select Role</option>
                                <?php foreach ($roles as $value => $label): ?>
                                    <option value="<?= esc($value, 'attr') ?>"><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>

                            <small class="text-muted" id="roleHint"></small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Status</label>

                            <input type="hidden" name="status" value="INACTIVE">

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="statusToggle" name="status"
                                    value="ACTIVE" checked>

                                <label class="form-check-label" for="statusToggle" id="statusLabel">
                                    ACTIVE
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary">Save User</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- ===================== EDIT USER MODALS ===================== -->
<?php foreach ($users as $row): ?>

    <div class="modal fade edit-user-modal" id="edit<?= $row['id'] ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form action="<?= base_url('users/update/' . $row['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Full Name</label>
                                <input type="text" name="fullname" class="form-control"
                                    value="<?= esc($row['fullname'], 'attr') ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control"
                                    value="<?= esc($row['username'], 'attr') ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= esc($row['email'], 'attr') ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password<?= $row['id'] ?>" class="form-label">New Password</label>

                                <div class="input-group">
                                    <input type="password" id="password<?= $row['id'] ?>" name="password"
                                        class="form-control password-input"
                                        placeholder="Leave blank to keep current password" minlength="8"
                                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
                                        title="Minimum 8 characters with uppercase, lowercase, number and special character."
                                        autocomplete="new-password">

                                    <button class="btn btn-outline-secondary toggle-password" type="button"
                                        data-target="password<?= $row['id'] ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>

                                <small class="text-muted">
                                    Leave blank if you don't want to change the password.
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Role</label>

                                <select name="role" class="form-select" required>
                                    <?php foreach ($roles as $value => $label): ?>
                                        <?php $sel = strtoupper(trim((string) $row['role'])) === $value ? 'selected' : ''; ?>
                                        <option value="<?= esc($value, 'attr') ?>" <?= $sel ?>>
                                            <?= esc($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>

                                <input type="hidden" name="status" value="INACTIVE">

                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" type="checkbox"
                                        id="statusToggle<?= $row['id'] ?>" name="status" value="ACTIVE"
                                        <?= $row['status'] === 'ACTIVE' ? 'checked' : '' ?>>

                                    <label class="form-check-label status-label" for="statusToggle<?= $row['id'] ?>">
                                        <?= $row['status'] === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE' ?>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-warning">Update User</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

<?php endforeach; ?>

<!-- ===================== VIEW USER MODALS ===================== -->
<?php foreach ($users as $row): ?>

    <div class="modal fade view-user-modal" id="view<?= $row['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">View User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Full Name</th>
                            <td><?= esc($row['fullname']) ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><?= esc($row['username']) ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= esc($row['email']) ?></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td><?= esc($roleLabel($row['role'])) ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($row['status'] === 'ACTIVE'): ?>
                                    <span class="badge badge-completed">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge badge-rejected">INACTIVE</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Two-Step Verification</th>
                            <td>
                                <?php if ((int) ($row['mfa_enabled'] ?? 0) === 1): ?>
                                    <span class="badge badge-completed">ENABLED</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">NOT SET UP</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

<?php endforeach; ?>

<!-- ===================== ACTIVATE / DEACTIVATE MODALS ===================== -->
<?php foreach ($users as $row): ?>
    <?php $isActive = $row['status'] === 'ACTIVE'; ?>

    <div class="modal fade" id="status<?= $row['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="<?= base_url(($isActive ? 'users/deactivate/' : 'users/activate/') . $row['id']) ?>"
                    method="post">
                    <?= csrf_field() ?>

                    <?php $headerClass = $isActive ? 'bg-danger' : 'bg-success'; ?>
                    <div class="modal-header <?= $headerClass ?> text-white">
                        <h5 class="modal-title">
                            <?= $isActive ? 'Deactivate User' : 'Activate User' ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <h5 class="text-center">
                            Are you sure you want to <?= $isActive ? 'deactivate' : 'activate' ?> this user?
                        </h5>

                        <hr>

                        <h4 class="text-center"><?= esc($row['fullname']) ?></h4>

                        <?php if ($isActive): ?>
                            <p class="text-muted text-center small mb-0">
                                They will no longer be able to log in. You can activate the account again anytime.
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn <?= $isActive ? 'btn-danger' : 'btn-success' ?>">
                            <?= $isActive ? 'Deactivate' : 'Activate' ?>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

<?php endforeach; ?>


</div>


<?= $this->include('dashboard_layout/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ==========================================
           ACTIVE SIDEBAR MENU
        ========================================== */

        const currentPath = window.location.pathname.replace(/\/+$/, '');

        document.querySelectorAll('.nav-link:not(.nav-link-logout)').forEach(function (link) {

            const href = link.getAttribute('href');

            if (!href || href === '#') return;

            try {

                const linkPath = new URL(link.href).pathname.replace(/\/+$/, '');

                const active =
                    currentPath === linkPath ||
                    currentPath.startsWith(linkPath + '/');

                link.classList.toggle('active', active);

            } catch (error) {

                console.warn('Invalid navigation link:', href);

            }

        });

        /* ==========================================
           MODAL FUNCTIONS
        ========================================== */

        document.querySelectorAll('.modal').forEach(function (modal) {

            // Focus first input when modal opens
            modal.addEventListener('shown.bs.modal', function () {

                const firstField = modal.querySelector(
                    'input:not([type="hidden"]), select, textarea'
                );

                if (firstField) {
                    firstField.focus();
                }

            });

        });

        /* ==========================================
           RESET ONLY ADD USER MODAL
        ========================================== */

        const addUserModal = document.getElementById('addUserModal');

        if (addUserModal) {

            addUserModal.addEventListener('hidden.bs.modal', function () {

                const form = this.querySelector('form');

                if (form) {

                    form.reset();

                    form.querySelectorAll('.is-invalid').forEach(function (input) {
                        input.classList.remove('is-invalid');
                    });

                    form.querySelectorAll('.is-valid').forEach(function (input) {
                        input.classList.remove('is-valid');
                    });

                    document.getElementById('statusLabel').textContent = 'ACTIVE';
                    document.getElementById('roleHint').textContent = '';

                }

            });

        }

    });

    document.addEventListener('DOMContentLoaded', function () {

        const successAlert = document.getElementById('successAlert');

        if (successAlert) {
            setTimeout(function () {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(successAlert);
                bsAlert.close();
            }, 3000); // Auto close after 3 seconds
        }

    });

    document.addEventListener("DOMContentLoaded", function () {
        setCurrentDate();
    });

    /**
     * Displays today's date in a readable, localized format
     * inside the #currentDate element in the page header.
     */
    function setCurrentDate() {
        const dateEl = document.getElementById("currentDate");
        if (!dateEl) return;

        const today = new Date();
        const options = {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
        };
        dateEl.textContent = today.toLocaleDateString("en-US", options);
    }
</script>

<script>
    const statusToggle = document.getElementById('statusToggle');
    const statusLabel = document.getElementById('statusLabel');

    if (statusToggle) {
        statusToggle.addEventListener('change', function () {
            statusLabel.textContent = this.checked ? 'ACTIVE' : 'INACTIVE';
        });
    }

    // Short explanation of what each role can access
    const roleDescriptions = {
        'IT ADMINISTRATOR': 'Full access, including user management.',
        'HR ADMINISTRATOR': 'Dashboard, requests, reports, feedback, TIA, employees and superiors.',
        'SUPERIOR': 'Training Impact Assessment (TIA) page only.'
    };

    document.getElementById('addRole').addEventListener('change', function () {
        document.getElementById('roleHint').textContent = roleDescriptions[this.value] || '';
    });

    document.querySelectorAll(".toggle-password").forEach(button => {

        button.addEventListener("click", function () {

            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("bi-eye", "bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.replace("bi-eye-slash", "bi-eye");
            }

        });

    });
</script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {

        const password = document.getElementById('password');
        const icon = this.querySelector('i');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>

<script>
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const label = this.closest('.form-switch').querySelector('.status-label');
            label.textContent = this.checked ? 'ACTIVE' : 'INACTIVE';
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const loadingScreen =
            document.getElementById('doleLoadingScreen');

        if (!loadingScreen) return;

        const sidebarLinks =
            document.querySelectorAll('.nav-link');


        sidebarLinks.forEach(function (link) {

            link.addEventListener('click', function (event) {

                const href = this.getAttribute('href');

                if (
                    !href ||
                    href === '#' ||
                    href === 'javascript:void(0)'
                ) {
                    return;
                }


                /* Allow Ctrl / CMD / Shift / Middle Click */

                if (
                    event.ctrlKey ||
                    event.metaKey ||
                    event.shiftKey ||
                    event.button === 1
                ) {
                    return;
                }


                /* Stop normal navigation temporarily */

                event.preventDefault();


                /* Show loading screen */

                loadingScreen.classList.add('show');

                document.body.style.overflow = 'hidden';


                /*
                 * Navigate immediately.
                 * The loading screen will remain visible
                 * while the next page is loading.
                 */

                window.location.href = href;

            });

        });


        /* Back / Forward */

        window.addEventListener('pageshow', function () {

            loadingScreen.classList.remove('show');

            document.body.style.overflow = '';

        });

    });
</script>

<?= $this->include('dashboard_layout/loading') ?>