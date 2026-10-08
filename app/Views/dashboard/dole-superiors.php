<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

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
            <h4 class="mb-0">Immediate Superiors</h4>
            <div class="d-flex flex-wrap gap-2">
                <input type="text" id="searchBox" class="form-control" placeholder="Search..." style="width:220px">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Add
                    Superior</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tia-table" id="superiorTable">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th style="width:150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($superiors)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No records found.</td>
                        </tr>
                    <?php else:
                        foreach ($superiors as $i => $s): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($s['employee_code'] ?? '') ?></td>
                                <td><?= esc($s['name']) ?></td>
                                <td><?= esc($s['email'] ?? '') ?></td>
                                <td><?= esc($s['position'] ?? '') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="<?= $s['id'] ?>"
                                        data-code="<?= esc($s['employee_code'] ?? '', 'attr') ?>"
                                        data-name="<?= esc($s['name'], 'attr') ?>"
                                        data-email="<?= esc($s['email'] ?? '', 'attr') ?>"
                                        data-position="<?= esc($s['position'] ?? '', 'attr') ?>">Edit</button>
                                    <form action="<?= site_url('superiors/delete/' . $s['id']) ?>" method="post"
                                        class="d-inline" onsubmit="return confirm('Delete this superior?')">
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
            <form class="modal-content" action="<?= site_url('superiors/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add Superior</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Employee Code</label>
                        <input type="text" name="employee_code" class="form-control"
                            value="<?= esc(old('employee_code')) ?>" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= esc(old('name')) ?>" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>">
                    </div>
                    <div class="mb-3"><label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" value="<?= esc(old('position')) ?>">
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
                    <h5 class="modal-title">Edit Superior</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Employee Code</label>
                        <input type="text" name="employee_code" id="e_code" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Name</label>
                        <input type="text" name="name" id="e_name" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label>
                        <input type="email" name="email" id="e_email" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Position</label>
                        <input type="text" name="position" id="e_position" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    (function () {
        function initSuperiors() {
            // Edit button -> fill modal
            document.querySelectorAll('.btn-edit').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('editForm').action = '<?= site_url('superiors/update') ?>/' + this.dataset.id;
                    document.getElementById('e_code').value = this.dataset.code || '';
                    document.getElementById('e_name').value = this.dataset.name || '';
                    document.getElementById('e_email').value = this.dataset.email || '';
                    document.getElementById('e_position').value = this.dataset.position || '';
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
                });
            });

            // Quick search
            document.getElementById('searchBox').addEventListener('keyup', function () {
                var q = this.value.toLowerCase();
                document.querySelectorAll('#superiorTable tbody tr').forEach(function (tr) {
                    tr.style.display = tr.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
                });
            });
        }

        function ready() {
            if (typeof bootstrap === 'undefined') {
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
                s.onload = initSuperiors;
                document.body.appendChild(s);
            } else {
                initSuperiors();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', ready);
        } else {
            ready();
        }
    })();
</script>