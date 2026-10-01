<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<!-- TIA dashboard styles (remove this line if your head layout already loads it) -->
<link rel="stylesheet" href="<?= base_url('assets/css/tia-dashboard.css') ?>">

<main class="main-content">
    <div class="container-fluid py-3 employees-page">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h4 class="mb-0">Employees</h4>
            <div class="d-flex flex-wrap gap-2">
                <input type="text" id="searchBox" class="form-control" placeholder="Search..." style="width:220px">
                <a href="<?= site_url('employees/template') ?>" class="btn btn-outline-secondary">CSV Template</a>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">Import
                    CSV</button>
                <a href="<?= site_url('employees/export') ?>" class="btn btn-outline-success">Export CSV</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Add
                    Employee</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tia-table" id="employeeTable">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th style="width:150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No records found.</td>
                        </tr>
                    <?php else:
                        foreach ($employees as $i => $e): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($e['name']) ?></td>
                                <td><?= esc($e['email']) ?></td>
                                <td><?= esc($e['position']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="<?= $e['id'] ?>"
                                        data-name="<?= esc($e['name'], 'attr') ?>" data-email="<?= esc($e['email'], 'attr') ?>"
                                        data-position="<?= esc($e['position'], 'attr') ?>">Edit</button>
                                    <form action="<?= site_url('employees/delete/' . $e['id']) ?>" method="post"
                                        class="d-inline" onsubmit="return confirm('Delete this employee?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade add-training-modal" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="<?= site_url('employees/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= esc(old('name')) ?>" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" value="<?= esc(old('position')) ?>"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade edit-participant-modal" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="editForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name</label>
                        <input type="text" name="name" id="e_name" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label>
                        <input type="email" name="email" id="e_email" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Position</label>
                        <input type="text" name="position" id="e_position" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade add-training-modal" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="<?= site_url('employees/import') ?>" method="post"
                enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Import CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        Columns required: <code>NAME, EMAIL, POSITION</code>.<br>
                        <code>EMPLOYEE CODE</code> is optional: leave it out (or blank) to auto-generate. If a code
                        already exists, that record is updated.
                    </p>
                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>window.EMPLOYEE_LIST_URL = '<?= site_url('employees/all') ?>';</script>
<script src="<?= base_url('assets/js/employee-select.js') ?>"></script>

<script>
    (function () {
        function initEmployees() {
            // Edit button -> fill modal
            document.querySelectorAll('.btn-edit').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('editForm').action = '<?= site_url('employees/update') ?>/' + this.dataset.id;
                    document.getElementById('e_name').value = this.dataset.name;
                    document.getElementById('e_email').value = this.dataset.email;
                    document.getElementById('e_position').value = this.dataset.position;
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
                });
            });

            // Quick search
            document.getElementById('searchBox').addEventListener('keyup', function () {
                var q = this.value.toLowerCase();
                document.querySelectorAll('#employeeTable tbody tr').forEach(function (tr) {
                    tr.style.display = tr.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
                });
            });
        }

        function ready() {
            // Load Bootstrap JS only if the layout doesn't already provide it
            if (typeof bootstrap === 'undefined') {
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
                s.onload = initEmployees;
                document.body.appendChild(s);
            } else {
                initEmployees();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', ready);
        } else {
            ready();
        }
    })();
</script>