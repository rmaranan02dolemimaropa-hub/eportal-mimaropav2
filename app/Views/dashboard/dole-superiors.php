<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>?v=2">

<main class="main-content">
    <div class="container-fluid py-3 tia-page">

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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Add
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
                        <tr class="empty-row">
                            <td colspan="6" class="text-center text-muted py-5">No records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($superiors as $i => $s): ?>
                            <tr>
                                <td data-label="#"><?= $i + 1 ?></td>
                                <td data-label="Employee Code"><?= esc($s['employee_code'] ?? '') ?></td>
                                <td data-label="Name"><?= esc($s['name']) ?></td>
                                <td data-label="Email"><?= esc($s['email'] ?? '') ?></td>
                                <td data-label="Position"><?= esc($s['position'] ?? '') ?></td>

                                <td class="text-end" data-label="Actions">
                                    <div class="row-actions">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-edit"
                                            data-id="<?= (int) $s['id'] ?>"
                                            data-code="<?= esc($s['employee_code'] ?? '', 'attr') ?>"
                                            data-name="<?= esc($s['name'], 'attr') ?>"
                                            data-email="<?= esc($s['email'] ?? '', 'attr') ?>"
                                            data-position="<?= esc($s['position'] ?? '', 'attr') ?>">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </button>

                                        <form action="<?= site_url('superiors/delete/' . (int) $s['id']) ?>" method="post"
                                            class="d-inline" onsubmit="return confirm('Delete this superior?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

<!-- Bootstrap JS (head.php only loads the CSS) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function () {
        'use strict';

        var updateUrl = '<?= site_url('superiors/update') ?>/';

        // Edit button -> fill modal
        function initEditButtons() {
            document.querySelectorAll('.btn-edit').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var d = this.dataset;

                    document.getElementById('editForm').action = updateUrl + d.id;
                    document.getElementById('e_code').value = d.code || '';
                    document.getElementById('e_name').value = d.name || '';
                    document.getElementById('e_email').value = d.email || '';
                    document.getElementById('e_position').value = d.position || '';

                    bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal')).show();
                });
            });
        }

        // Quick search
        function initSearch() {
            var box = document.getElementById('searchBox');
            if (!box) return;
            box.addEventListener('input', function () {
                var q = this.value.toLowerCase();
                document.querySelectorAll('#superiorTable tbody tr').forEach(function (tr) {
                    tr.style.display = tr.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
                });
            });
        }

        function init() {
            [initEditButtons, initSearch].forEach(function (fn) {
                try { fn(); } catch (err) { console.error('[superiors] ' + fn.name + ' failed:', err); }
            });
        }

        // 'load' fires after Bootstrap above has finished loading
        if (document.readyState === 'complete') {
            init();
        } else {
            window.addEventListener('load', init);
        }
    })();
</script>