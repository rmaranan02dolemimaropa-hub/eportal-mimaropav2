<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<!-- TIA dashboard styles (remove this line if your head layout already loads it) -->
<!-- TIA shared styles -->
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<style>
    /* keep Tom Select dropdown above Bootstrap modals */
    .ts-dropdown {
        z-index: 2000;
    }

    .modal .ts-wrapper {
        width: 100%;
    }
</style>

<?php
// Label shown in dropdowns: "CODE - Name (email)"
$superiorLabel = function (array $s): string {
    return (!empty($s['employee_code']) ? $s['employee_code'] . ' - ' : '')
        . $s['name']
        . (!empty($s['email']) ? ' (' . $s['email'] . ')' : '');
};
?>

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
            <h4 class="mb-0">Employees<?= !empty($currentType) ? ' – ' . esc($currentType) : '' ?></h4>
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
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Position Type</th>
                        <th>Office</th>
                        <th>Immediate Superior</th>
                        <th style="width:150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No records found.</td>
                        </tr>
                    <?php else:
                        foreach ($employees as $i => $e): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <?= esc($e['employee_code']) ?>
                                </td>
                                <td><?= esc($e['name']) ?></td>
                                <td><?= esc($e['email']) ?></td>
                                <td><?= esc($e['position']) ?></td>
                                <td>
                                    <?= esc($e['position_type'] ?? '') ?>
                                </td>
                                <td><?= esc($e['office'] ?? '') ?></td>
                                <td>
                                    <?= esc($e['superior_name'] ?? '') ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary btn-edit" data-id="<?= $e['id'] ?>"
                                        data-code="<?= esc($e['employee_code'], 'attr') ?>"
                                        data-name="<?= esc($e['name'], 'attr') ?>" data-email="<?= esc($e['email'], 'attr') ?>"
                                        data-position="<?= esc($e['position'], 'attr') ?>"
                                        data-position-type="<?= esc($e['position_type'] ?? '', 'attr') ?>"
                                        data-office="<?= esc($e['office'] ?? '', 'attr') ?>"
                                        data-superior-id="<?= (int) ($e['superior_id'] ?? 0) ?>">Edit</button>
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
                    <div class="mb-3"><label class="form-label">Employee Code</label>
                        <input type="text" name="employee_code" class="form-control"
                            value="<?= esc(old('employee_code')) ?>" required>
                    </div>
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
                    <div class="mb-3"><label class="form-label">Type of Position</label>
                        <select name="position_type" class="form-select" required>
                            <option value="">-- Select type --</option>
                            <?php foreach (($positionTypes ?? []) as $t): ?>
                                <option value="<?= esc($t, 'attr') ?>" <?= (old('position_type') ?? ($currentType ?? '')) === $t ? 'selected' : '' ?>>
                                    <?= esc($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Office</label>
                        <select name="office" class="form-select">
                            <option value="">-- Select office --</option>
                            <?php foreach (($offices ?? []) as $o): ?>
                                <option value="<?= esc($o, 'attr') ?>" <?= old('office') === $o ? 'selected' : '' ?>>
                                    <?= esc($o) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Immediate Superior</label>
                        <select name="superior_id" class="form-select superior-select"
                            placeholder="Search or add superior...">
                            <option value=""></option>
                            <?php foreach (($superiors ?? []) as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= (string) old('superior_id') === (string) $s['id'] ? 'selected' : '' ?>>
                                    <?= esc($superiorLabel($s)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                    <div class="mb-3"><label class="form-label">Employee Code</label>
                        <input type="text" name="employee_code" id="e_code" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Name</label>
                        <input type="text" name="name" id="e_name" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label>
                        <input type="email" name="email" id="e_email" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Position</label>
                        <input type="text" name="position" id="e_position" class="form-control" required>
                    </div>
                    <div class="mb-3"><label class="form-label">Type of Position</label>
                        <select name="position_type" id="e_position_type" class="form-select" required>
                            <option value="">-- Select type --</option>
                            <?php foreach (($positionTypes ?? []) as $t): ?>
                                <option value="<?= esc($t, 'attr') ?>">
                                    <?= esc($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Office</label>
                        <select name="office" id="e_office" class="form-select">
                            <option value="">-- Select office --</option>
                            <?php foreach (($offices ?? []) as $o): ?>
                                <option value="<?= esc($o, 'attr') ?>"><?= esc($o) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Immediate Superior</label>
                        <select name="superior_id" id="e_superior" class="form-select superior-select"
                            placeholder="Search or add superior...">
                            <option value=""></option>
                            <?php foreach (($superiors ?? []) as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= esc($superiorLabel($s)) ?></option>
                            <?php endforeach; ?>
                        </select>
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
                        Optional: <code>EMPLOYEE CODE</code> (leave out or blank to auto-generate; if a code already
                        exists, that record is updated), <code>IMMEDIATE SUPERIOR CODE</code>,
                        <code>IMMEDIATE SUPERIOR</code>, <code>IMMEDIATE SUPERIOR EMAIL</code>
                        (a new superior is created automatically if not found).
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
    (function () {
        function initEmployees() {
            // Searchable superior dropdowns (with "add new" on the fly)
            var csrfName = '<?= csrf_token() ?>';
            var csrfHash = '<?= csrf_hash() ?>';

            document.querySelectorAll('.superior-select').forEach(function (el) {
                new TomSelect(el, {
                    allowEmptyOption: true,
                    maxOptions: null,
                    create: function (input, callback) {
                        var fd = new FormData();
                        fd.append('name', input);
                        fd.append(csrfName, csrfHash);
                        fetch('<?= site_url('superiors/quick-add') ?>', {
                            method: 'POST',
                            body: fd,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(function (r) { return r.json(); })
                            .then(function (res) {
                                if (res.csrf) {
                                    csrfHash = res.csrf;
                                    document.querySelectorAll('input[name="' + csrfName + '"]')
                                        .forEach(function (i) { i.value = csrfHash; });
                                }
                                if (res.id) {
                                    callback({
                                        value: String(res.id),
                                        text: (res.employee_code ? res.employee_code + ' - ' : '') + res.name
                                            + (res.email ? ' (' + res.email + ')' : '')
                                    });
                                } else {
                                    alert(res.error || 'Could not add superior.');
                                    callback();
                                }
                            })
                            .catch(function () { callback(); });
                    }
                });
            });

            // Edit button -> fill modal
            document.querySelectorAll('.btn-edit').forEach(function (btn) {
                btn.addEventListener('click', function () {

                    document.getElementById('editForm').action = '<?= site_url('employees/update') ?>/' + this.dataset.id;
                    document.getElementById('e_code').value = this.dataset.code;
                    document.getElementById('e_name').value = this.dataset.name;
                    document.getElementById('e_email').value = this.dataset.email;
                    document.getElementById('e_position').value = this.dataset.position;
                    document.getElementById('e_position_type').value = this.dataset.positionType || '';
                    document.getElementById('e_office').value = this.dataset.office || '';

                    var sid = this.dataset.superiorId;
                    document.getElementById('e_superior').tomselect
                        .setValue(sid && sid !== '0' ? sid : '');

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