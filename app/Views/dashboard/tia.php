<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<main class="main-content">

    <div class="container-fluid py-4">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: #0b3d91;">Training Impact Assessment</h4>
                <p class="text-muted mb-0">List of trainings and participants</p>
            </div>
            <button class="btn btn-primary" style="background:#0b3d91; border-color:#0b3d91;" data-bs-toggle="modal"
                data-bs-target="#addTrainingModal">
                <i class="bi bi-plus-lg me-1"></i> Add Training
            </button>
        </div>

        <!-- Training Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 tia-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Training Title</th>
                                <th>Training Date</th>
                                <th>Conducted by</th>
                                <th>Office</th>
                                <th>Immediate Superior</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($trainings)): ?>
                                    <?php foreach ($trainings as $index => $row): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td class="fw-semibold"><?= esc($row['training_title']) ?></td>
                                                <td><?= esc($row['training_dates']) ?></td>
                                                <td><?= esc($row['conducted_by'] ?? '—') ?></td>
                                                <td><?= esc($row['office'] ?? '—') ?></td>
                                                <td><?= esc($row['immediate_superior'] ?? '—') ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary manage-participants-btn"
                                                        data-bs-toggle="modal" data-bs-target="#manageParticipantsModal"
                                                        data-training-code="<?= esc($row['training_code']) ?>"
                                                        data-training-title="<?= esc($row['training_title']) ?>">
                                                        <i class="bi bi-people me-1"></i> Manage Participants
                                                    </button>
                                                </td>
                                            </tr>
                                    <?php endforeach; ?>
                            <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            No trainings found.
                                        </td>
                                    </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== ADD TRAINING MODAL ===================== -->
    <div class="modal fade" id="addTrainingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background:#0b3d91; color:white;">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i> Add New Training
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addTrainingForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Training Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="training_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Training Date/s <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="training_dates"
                                placeholder="e.g. March 10-12, 2026" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Conducted by</label>
                            <input type="text" class="form-control" name="conducted_by" placeholder="e.g. HR Division">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Office</label>
                            <input type="text" class="form-control" name="office">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name of Immediate Superior</label>
                            <input type="text" class="form-control" name="immediate_superior">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background:#0b3d91; border-color:#0b3d91;">
                            Save Training
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================== MANAGE PARTICIPANTS MODAL (Combined) ===================== -->
    <div class="modal fade" id="manageParticipantsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background:#0b3d91; color:white;">
                    <h5 class="modal-title">
                        <i class="bi bi-people me-2"></i> Manage Participants
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Training info -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Training</label>
                        <input type="text" class="form-control" id="manage_training_title" readonly>
                        <input type="hidden" id="manage_training_code">
                    </div>

                    <!-- ===== EXISTING PARTICIPANTS ===== -->
                    <h6 class="fw-bold mb-2">Current Participants</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Email</th>
                                    <th style="width:100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="existingParticipantsBody">
                                <!-- loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="existingEmpty" class="text-muted text-center mb-4 d-none">
                        No participants yet.
                    </div>

                    <hr>

                    <!-- ===== ADD NEW PARTICIPANTS ===== -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0">Add New Participants</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success" id="exportCsvBtn">
                                <i class="bi bi-download me-1"></i> Export CSV
                            </button>
                            <label class="btn btn-sm btn-outline-primary mb-0">
                                <i class="bi bi-upload me-1"></i> Import CSV
                                <input type="file" id="importCsvInput" accept=".csv" hidden>
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="addRowBtn">
                                <i class="bi bi-plus-lg"></i> Add Row
                            </button>
                        </div>
                    </div>

                    <small class="text-muted d-block mb-2">
                        Paste from Excel or import a CSV file (columns: Name, Position, Email)
                    </small>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0" id="newParticipantsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Name of Participant</th>
                                    <th>Position</th>
                                    <th>Email Address</th>
                                    <th style="width:50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="newParticipantsBody">
                                <!-- empty rows for adding -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveNewParticipantsBtn"
                        style="background:#0b3d91; border-color:#0b3d91;">
                        Save New Participants
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== EDIT PARTICIPANT MODAL ===================== -->
    <div class="modal fade" id="editParticipantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background:#0b3d91; color:white;">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> Edit Participant
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editParticipantForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_participant_id" name="id">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name of Participant <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_participant_name" name="participant_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_position" name="position" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background:#0b3d91; border-color:#0b3d91;">
                            Update Participant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<?= $this->include('dashboard_layout/loading') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-dashboard.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ========== ADD TRAINING ==========
    document.getElementById('addTrainingForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Saving...';

        try {
            const response = await fetch('<?= site_url('tia/store-training') ?>', {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();

            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('addTrainingModal')).hide();
                form.reset();
                location.reload();
            } else {
                let msg = result.message || 'Failed to save training.';
                if (result.errors) msg += '\n\n' + Object.values(result.errors).join('\n');
                alert(msg);
            }
        } catch (err) {
            console.error(err);
            alert('Network error. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // ========== HELPERS ==========
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    let newRowCount = 0;

    function createNewParticipantRow(name = '', position = '', email = '') {
        newRowCount++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center text-muted">${newRowCount}</td>
            <td><input type="text" class="form-control form-control-sm new-name" value="${escapeHtml(name)}" placeholder="Full Name"></td>
            <td><input type="text" class="form-control form-control-sm new-position" value="${escapeHtml(position)}" placeholder="Position"></td>
            <td><input type="email" class="form-control form-control-sm new-email" value="${escapeHtml(email)}" placeholder="email@example.com"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-new-row" title="Remove">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        return tr;
    }

    function renumberNewRows() {
        const rows = document.querySelectorAll('#newParticipantsBody tr');
        rows.forEach((row, i) => {
            row.querySelector('td:first-child').textContent = i + 1;
        });
        newRowCount = rows.length;
    }

    // ========== LOAD EXISTING PARTICIPANTS ==========
    async function loadExistingParticipants(trainingCode) {
        const tbody = document.getElementById('existingParticipantsBody');
        const emptyMsg = document.getElementById('existingEmpty');

        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>';
        emptyMsg.classList.add('d-none');

        try {
            const response = await fetch(`<?= site_url('tia/participants') ?>/${trainingCode}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();

            tbody.innerHTML = '';

            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach((p, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center text-muted">${index + 1}</td>
                        <td class="fw-semibold">${escapeHtml(p.participant_name)}</td>
                        <td>${escapeHtml(p.position)}</td>
                        <td>${escapeHtml(p.email)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-participant"
                                data-id="${p.id}"
                                data-name="${escapeHtml(p.participant_name)}"
                                data-position="${escapeHtml(p.position)}"
                                data-email="${escapeHtml(p.email)}"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-participant"
                                data-id="${p.id}"
                                title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                emptyMsg.classList.remove('d-none');
            }
        } catch (err) {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load participants.</td></tr>';
        }
    }

    // ========== OPEN MANAGE MODAL ==========
    document.querySelectorAll('.manage-participants-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const code = this.dataset.trainingCode;
            const title = this.dataset.trainingTitle;

            document.getElementById('manage_training_code').value = code;
            document.getElementById('manage_training_title').value = title;

            // Reset add-new table
            const newBody = document.getElementById('newParticipantsBody');
            newBody.innerHTML = '';
            newRowCount = 0;
            newBody.appendChild(createNewParticipantRow());

            // Load existing
            loadExistingParticipants(code);
        });
    });

    // ========== ADD ROW ==========
    document.getElementById('addRowBtn').addEventListener('click', function () {
        document.getElementById('newParticipantsBody').appendChild(createNewParticipantRow());
    });

    // ========== REMOVE NEW ROW ==========
    document.getElementById('newParticipantsBody').addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-new-row')) {
            e.target.closest('tr').remove();
            renumberNewRows();
        }
    });

    // ========== PASTE FROM EXCEL ==========
    document.getElementById('newParticipantsBody').addEventListener('paste', function (e) {
        e.preventDefault();
        const clipboardData = e.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('text');
        const rows = pastedData.trim().split(/\r\n|\n|\r/).filter(r => r.trim() !== '');

        const tbody = document.getElementById('newParticipantsBody');
        tbody.innerHTML = '';
        newRowCount = 0;

        rows.forEach(row => {
            const cols = row.split(/\t/);
            tbody.appendChild(createNewParticipantRow(
                cols[0] ? cols[0].trim() : '',
                cols[1] ? cols[1].trim() : '',
                cols[2] ? cols[2].trim() : ''
            ));
        });
    });

    // ========== EXPORT CSV ==========
    document.getElementById('exportCsvBtn').addEventListener('click', function () {
        const code = document.getElementById('manage_training_code').value;
        if (!code) {
            alert('No training selected.');
            return;
        }
        window.location.href = `<?= site_url('tia/export-participants') ?>/${code}`;
    });

    // ========== IMPORT CSV ==========
    document.getElementById('importCsvInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            const text = event.target.result;
            let rows = text.trim().split(/\r\n|\n|\r/).filter(r => r.trim() !== '');

            if (rows.length > 0 && (rows[0].toLowerCase().includes('name') || rows[0].toLowerCase().includes('email'))) {
                rows.shift();
            }

            const tbody = document.getElementById('newParticipantsBody');
            tbody.innerHTML = '';
            newRowCount = 0;

            rows.forEach(row => {
                const cols = row.includes('\t') ? row.split('\t') : row.split(',');
                const name = cols[0] ? cols[0].replace(/^"|"$/g, '').trim() : '';
                const position = cols[1] ? cols[1].replace(/^"|"$/g, '').trim() : '';
                const email = cols[2] ? cols[2].replace(/^"|"$/g, '').trim() : '';
                if (name || position || email) {
                    tbody.appendChild(createNewParticipantRow(name, position, email));
                }
            });

            e.target.value = '';
        };
        reader.readAsText(file);
    });

    // ========== SAVE NEW PARTICIPANTS ==========
    document.getElementById('saveNewParticipantsBtn').addEventListener('click', async function () {
        const trainingCode = document.getElementById('manage_training_code').value;
        const rows = document.querySelectorAll('#newParticipantsBody tr');

        if (rows.length === 0) {
            alert('Please add at least one participant.');
            return;
        }

        const participants = [];
        let hasError = false;

        rows.forEach(row => {
            const name = row.querySelector('.new-name').value.trim();
            const position = row.querySelector('.new-position').value.trim();
            const email = row.querySelector('.new-email').value.trim();

            if (!name || !position || !email) {
                hasError = true;
                return;
            }
            participants.push({ participant_name: name, position, email });
        });

        if (hasError) {
            alert('Please fill in all fields (Name, Position, Email) for every row.');
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Saving...';

        try {
            const response = await fetch('<?= site_url('tia/add-participants-bulk') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ training_code: trainingCode, participants })
            });
            const result = await response.json();

            if (result.success) {
                alert(`Successfully added ${result.count} participant(s)!`);
                // Clear new rows and refresh existing list
                const newBody = document.getElementById('newParticipantsBody');
                newBody.innerHTML = '';
                newRowCount = 0;
                newBody.appendChild(createNewParticipantRow());
                loadExistingParticipants(trainingCode);
            } else {
                alert(result.message || 'Failed to save participants.');
            }
        } catch (err) {
            console.error(err);
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // ========== EDIT / DELETE (Existing table) ==========
    document.getElementById('existingParticipantsBody').addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit-participant');
        if (editBtn) {
            document.getElementById('edit_participant_id').value = editBtn.dataset.id;
            document.getElementById('edit_participant_name').value = editBtn.dataset.name;
            document.getElementById('edit_position').value = editBtn.dataset.position;
            document.getElementById('edit_email').value = editBtn.dataset.email;
            new bootstrap.Modal(document.getElementById('editParticipantModal')).show();
            return;
        }

        const deleteBtn = e.target.closest('.btn-delete-participant');
        if (deleteBtn) {
            if (!confirm('Are you sure you want to delete this participant?')) return;
            deleteParticipant(deleteBtn.dataset.id);
        }
    });

    // ========== UPDATE PARTICIPANT ==========
    document.getElementById('editParticipantForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('edit_participant_id').value;
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Updating...';

        try {
            const response = await fetch(`<?= site_url('tia/update-participant') ?>/${id}`, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();

            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('editParticipantModal')).hide();
                loadExistingParticipants(document.getElementById('manage_training_code').value);
            } else {
                alert(result.message || 'Failed to update participant.');
            }
        } catch (err) {
            console.error(err);
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // ========== DELETE PARTICIPANT ==========
    async function deleteParticipant(id) {
        try {
            const response = await fetch(`<?= site_url('tia/delete-participant') ?>/${id}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();

            if (result.success) {
                loadExistingParticipants(document.getElementById('manage_training_code').value);
            } else {
                alert(result.message || 'Failed to delete participant.');
            }
        } catch (err) {
            console.error(err);
            alert('Network error. Please try again.');
        }
    }

});
</script>