<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>




<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">User Management</h1>
            <p class="page-subtitle">
                Welcome back, <?= session('fullname') ?> — create, update, and manage user accounts and system access.
            </p>
        </div>
        <div class="page-date">
            <i class="bi bi-calendar3"></i>
            <span id="currentDate"></span>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>

        <div class="alert alert-success alert-dismissible fade show" id="successAlert">

            <?= session()->getFlashdata('success'); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>

        </div>

    <?php endif; ?>

    <!-- Recent Requests Table -->
    <section class="panel table-panel">
        <div class="panel-header">
            <h2 class="panel-title">Recent Service Requests</h2>
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
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $row): ?>
                        <tr>
                            <td><?= esc($row['fullname']) ?></td>
                            <td><?= esc($row['username']) ?></td>
                            <td><?= esc($row['role']) ?></td>
                            <td>
                                <?php if ($row['status'] == 'ACTIVE'): ?>
                                    <span class="badge badge-completed">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge badge-rejected">INACTIVE</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end">
                                <div class="row-actions">
                                    <button class="row-action-btn" title="View" data-bs-toggle="modal"
                                        data-bs-target="#view<?= $row['id'] ?>"><i class="bi bi-eye"></i></button>
                                    <button class="row-action-btn" title="Edit" data-bs-toggle="modal"
                                        data-bs-target="#edit<?= $row['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <button class="row-action-btn row-action-danger" title="Delete" data-bs-toggle="modal"
                                        data-bs-target="#delete<?= $row['id'] ?>"><i class="bi bi-trash"></i></button>


                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

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


    </section>
</main>
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?= base_url('users/store') ?>" method="post">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus"></i> Add User
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    </button>
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

                            <select name="role" class="form-select" required>

                                <option value="" disabled>Select Role</option>

                                <option value="IT Administrator">
                                    IT Administrator
                                </option>

                                <option value="HR Administrator">
                                    HR Administrator
                                </option>

                            </select>

                        </div>

                        <!-- <div class="col-md-6 mb-3">
                            <label>Status</label>

                            <select name="status" class="form-select" required>

                                <option value="ACTIVE">ACTIVE</option>
                                <option value="INACTIVE">INACTIVE</option>

                            </select>

                        </div> -->

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

                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                        Cancel
                    </button>

                    <button class="btn btn-primary">
                        Save User
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<?php foreach ($users as $row): ?>

    <div class="modal fade edit-user-modal" id="edit<?= $row['id'] ?>">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form action="<?= base_url('users/update/' . $row['id']) ?>" method="post">

                    <div class="modal-header bg-warning">

                        <h5 class="modal-title">
                            Edit User
                        </h5>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label>Full Name</label>

                                <input type="text" name="fullname" class="form-control" value="<?= $row['fullname'] ?>"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label>Username</label>

                                <input type="text" name="username" class="form-control" value="<?= $row['username'] ?>"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= esc($row['email']) ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password<?= $row['id'] ?>" class="form-label">
                                    New Password
                                </label>

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

                                <select name="role" class="form-select">

                                    <option <?= $row['role'] == 'IT Administrator' ? 'selected' : '' ?>>
                                        IT Administrator
                                    </option>

                                    <option <?= $row['role'] == 'HR Administrator' ? 'selected' : '' ?>>
                                        HR Administrator
                                    </option>

                                </select>

                            </div>

                            <!-- <div class="col-md-6">

                                <label>Status</label>

                                <select name="status" class="form-select">

                                    <option <?= $row['status'] == 'ACTIVE' ? 'selected' : '' ?>>
                                        ACTIVE
                                    </option>

                                    <option <?= $row['status'] == 'INACTIVE' ? 'selected' : '' ?>>
                                        INACTIVE
                                    </option>

                                </select>

                            </div> -->
                            <div class="col-md-6">
                                <label class="form-label d-block">Status</label>

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

                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                            Cancel
                        </button>

                        <button class="btn btn-warning">
                            Update User
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

<?php endforeach; ?>

<?php foreach ($users as $row): ?>

    <div class="modal fade view-user-modal" id="view<?= $row['id'] ?>">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header bg-info text-white">

                    <h5 class="modal-title">

                        View User

                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    </button>

                </div>

                <div class="modal-body">

                    <table class="table table-bordered">

                        <tr>
                            <th>Full Name</th>
                            <td><?= $row['fullname'] ?></td>
                        </tr>

                        <tr>
                            <th>Username</th>
                            <td><?= $row['username'] ?></td>
                        </tr>

                        <tr>
                            <th>Role</th>
                            <td><?= $row['role'] ?></td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($row['status'] == "ACTIVE"): ?>
                                    <span class="badge badge-completed">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge badge-rejected">INACTIVE</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                    </table>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>

            </div>

        </div>

    </div>

<?php endforeach; ?>

<?php foreach ($users as $row): ?>

    <div class="modal fade delete-user-modal" id="delete<?= $row['id'] ?>">

        <div class="modal-dialog">

            <div class="modal-content">

                <form action="<?= base_url('users/delete/' . $row['id']) ?>" method="post">

                    <div class="modal-header bg-danger text-white">

                        <h5 class="modal-title">

                            Delete User

                        </h5>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                        </button>

                    </div>

                    <div class="modal-body">

                        <h5 class="text-center">

                            Are you sure you want to delete?

                        </h5>

                        <hr>

                        <h4 class="text-center">

                            <?= esc($row['fullname']) ?>

                        </h4>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button class="btn btn-danger">

                            Delete

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
        animateStatCounters();
        enableTableSearch();
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

    statusToggle.addEventListener('change', function () {
        statusLabel.textContent = this.checked ? 'ACTIVE' : 'INACTIVE';
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