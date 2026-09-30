<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-dashboard.css') ?>">

<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Training Impact Assessment</h1>
            <p class="page-subtitle">Manage trainings and their participants</p>
        </div>
        <button type="button" class="btn btn-dole-primary" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
            <i class="bi bi-plus-lg me-1"></i> Add Training
        </button>
    </div>

    <!-- Training Table Panel -->
    <div class="panel table-panel">
        <div class="panel-header">
            <h2 class="panel-title">Trainings</h2>
        </div>

        <div class="table-responsive">
            <table class="table requests-table tia-table mb-0">
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
                                <td>
                                    <span class="ref-no"><?= esc($row['training_title']) ?></span>
                                </td>
                                <td><?= esc($row['training_dates']) ?></td>
                                <td><?= esc($row['conducted_by'] ?? '—') ?></td>
                                <td><?= esc($row['office'] ?? '—') ?></td>
                                <td><?= esc($row['immediate_superior'] ?? '—') ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm tool-btn manage-participants-btn"
                                        data-bs-toggle="modal" data-bs-target="#manageParticipantsModal"
                                        data-training-code="<?= esc($row['training_code'], 'attr') ?>"
                                        data-training-title="<?= esc($row['training_title'], 'attr') ?>">
                                        <i class="bi bi-people me-1"></i> Manage
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                No trainings found. Click “Add Training” to create one.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===================== ADD TRAINING MODAL ===================== -->
    <div class="modal fade" id="addTrainingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content add-training-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i> Add New Training
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addTrainingForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Training Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="training_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Training Date/s <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="training_dates"
                                placeholder="e.g. March 10-12, 2026" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Conducted by</label>
                            <input type="text" class="form-control" name="conducted_by" placeholder="e.g. HR Division">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Office</label>
                            <input type="text" class="form-control" name="office">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name of Immediate Superior</label>
                            <input type="text" class="form-control" name="immediate_superior">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Training</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================== MANAGE PARTICIPANTS MODAL ===================== -->
    <div class="modal fade" id="manageParticipantsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content manage-participants-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-people me-2"></i> Manage Participants
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Training info -->
                    <div class="mb-4">
                        <label class="form-label">Training</label>
                        <input type="text" class="form-control" id="manage_training_title" readonly>
                        <input type="hidden" id="manage_training_code">
                    </div>

                    <!-- Current participants -->
                    <h6 class="detail-section-title mb-3">
                        <i class="bi bi-person-check"></i> Current Participants
                    </h6>
                    <div class="table-responsive mb-2">
                        <table class="table requests-table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Email</th>
                                    <th style="width:110px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="existingParticipantsBody">
                                <!-- loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="existingEmpty" class="text-muted text-center py-3 d-none">
                        No participants yet.
                    </div>

                    <hr class="my-4">

                    <!-- Add new participants -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h6 class="detail-section-title mb-0">
                            <i class="bi bi-person-plus"></i> Add New Participants
                        </h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm tool-btn" id="exportCsvBtn">
                                <i class="bi bi-download me-1"></i> Export CSV
                            </button>
                            <label class="btn btn-sm tool-btn mb-0">
                                <i class="bi bi-upload me-1"></i> Import CSV
                                <input type="file" id="importCsvInput" accept=".csv,text/csv" hidden>
                            </label>
                            <button type="button" class="btn btn-sm tool-btn" id="addRowBtn">
                                <i class="bi bi-plus-lg"></i> Add Row
                            </button>
                        </div>
                    </div>

                    <small class="text-muted d-block mb-2">
                        Paste from Excel or import a CSV (columns: Name, Position, Email)
                    </small>

                    <div class="table-responsive">
                        <table class="table requests-table table-sm align-middle mb-0" id="newParticipantsTable">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Name of Participant</th>
                                    <th>Position</th>
                                    <th>Email Address</th>
                                    <th style="width:50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="newParticipantsBody">
                                <!-- rows for adding -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveNewParticipantsBtn">
                        Save New Participants
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== EDIT PARTICIPANT MODAL ===================== -->
    <div class="modal fade" id="editParticipantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content edit-participant-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> Edit Participant
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editParticipantForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_participant_id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Name of Participant <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_participant_name" name="participant_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_position" name="position" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Participant</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

<?= $this->include('dashboard_layout/loading') ?>

<!-- NOTE: remove this line if dashboard_layout/head or /loading already loads Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // =====================================================
        // CONFIG + CSRF
        // =====================================================
        const URLS = {
            storeTraining: '<?= site_url('tia/store-training') ?>',
            participants: '<?= site_url('tia/participants') ?>',
            exportCsv: '<?= site_url('tia/export-participants') ?>',
            addBulk: '<?= site_url('tia/add-participants-bulk') ?>',
            updateParticipant: '<?= site_url('tia/update-participant') ?>'
        };

        const csrfName = '<?= csrf_token() ?>';
        const csrfHeader = '<?= csrf_header() ?>';
        let csrfHash = '<?= csrf_hash() ?>';   // let: it may be refreshed after each request

        /**
         * Central fetch helper.
         * - always sends the CSRF header + AJAX header
         * - refreshes the CSRF token if the server returns result.csrf
         * - always returns parsed JSON, or throws a readable Error
         */
        async function apiFetch(url, options = {}) {
            options.headers = Object.assign({
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                [csrfHeader]: csrfHash
            }, options.headers || {});

            const response = await fetch(url, options);

            let data = null;
            try { data = await response.json(); } catch (e) { /* not JSON */ }

            if (data && data.csrf) csrfHash = data.csrf;

            if (!data) {
                throw new Error('Server returned an invalid response (HTTP ' + response.status + ').');
            }
            if (!response.ok && !data.message) {
                throw new Error('Request failed (HTTP ' + response.status + ').');
            }
            return data;
        }

        function buildFormData(form) {
            const fd = form ? new FormData(form) : new FormData();
            fd.append(csrfName, csrfHash);
            return fd;
        }

        // =====================================================
        // HELPERS
        // =====================================================
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function setBusy(btn, busyText) {
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = busyText;
            return () => { btn.disabled = false; btn.innerHTML = original; };
        }

        function showErrors(result, fallback) {
            let msg = (result && result.message) || fallback;
            if (result && result.errors) msg += '\n\n' + Object.values(result.errors).join('\n');
            alert(msg);
        }

        // CSV parser (handles quotes, commas inside quotes, BOM, CRLF)
        function parseCsv(text, delimiter = ',') {
            text = text.replace(/^\uFEFF/, '');
            const rows = [];
            let row = [], cur = '', inQuotes = false;

            for (let i = 0; i < text.length; i++) {
                const ch = text[i];
                if (inQuotes) {
                    if (ch === '"' && text[i + 1] === '"') { cur += '"'; i++; }
                    else if (ch === '"') { inQuotes = false; }
                    else { cur += ch; }
                } else if (ch === '"') {
                    inQuotes = true;
                } else if (ch === delimiter) {
                    row.push(cur); cur = '';
                } else if (ch === '\n' || ch === '\r') {
                    if (ch === '\r' && text[i + 1] === '\n') i++;
                    row.push(cur); rows.push(row); row = []; cur = '';
                } else {
                    cur += ch;
                }
            }
            if (cur !== '' || row.length) { row.push(cur); rows.push(row); }
            return rows.filter(r => r.some(c => c.trim() !== ''));
        }

        function detectDelimiter(text) {
            const firstLine = text.replace(/^\uFEFF/, '').split(/\r\n|\n|\r/)[0] || '';
            if (firstLine.includes('\t')) return '\t';
            if (firstLine.includes(';') && !firstLine.includes(',')) return ';';
            return ',';
        }

        // =====================================================
        // NEW PARTICIPANT ROWS
        // =====================================================
        const newBody = document.getElementById('newParticipantsBody');

        function createNewParticipantRow(name = '', position = '', email = '') {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center text-muted row-number"></td>
                <td><input type="text" class="form-control form-control-sm new-name" value="${escapeHtml(name)}" placeholder="Full Name"></td>
                <td><input type="text" class="form-control form-control-sm new-position" value="${escapeHtml(position)}" placeholder="Position"></td>
                <td><input type="email" class="form-control form-control-sm new-email" value="${escapeHtml(email)}" placeholder="email@example.com"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm row-action-btn row-action-danger btn-remove-new-row" title="Remove">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            return tr;
        }

        function renumberNewRows() {
            newBody.querySelectorAll('tr').forEach((row, i) => {
                row.querySelector('.row-number').textContent = i + 1;
            });
        }

        function isRowBlank(tr) {
            return ![...tr.querySelectorAll('input')].some(i => i.value.trim() !== '');
        }

        function removeBlankRows() {
            newBody.querySelectorAll('tr').forEach(tr => { if (isRowBlank(tr)) tr.remove(); });
        }

        function resetNewRows() {
            newBody.innerHTML = '';
            newBody.appendChild(createNewParticipantRow());
            renumberNewRows();
        }

        function appendParticipantRows(list) {
            removeBlankRows();
            list.forEach(c => newBody.appendChild(createNewParticipantRow(
                (c[0] || '').trim(), (c[1] || '').trim(), (c[2] || '').trim()
            )));
            if (newBody.children.length === 0) newBody.appendChild(createNewParticipantRow());
            renumberNewRows();
        }

        // =====================================================
        // ADD TRAINING
        // =====================================================
        document.getElementById('addTrainingForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = this;
            const restore = setBusy(form.querySelector('button[type="submit"]'), 'Saving...');

            try {
                const result = await apiFetch(URLS.storeTraining, {
                    method: 'POST',
                    body: buildFormData(form)
                });

                if (result.success) {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('addTrainingModal')).hide();
                    form.reset();
                    location.reload();
                } else {
                    showErrors(result, 'Failed to save training.');
                }
            } catch (err) {
                console.error(err);
                alert(err.message || 'Network error. Please try again.');
            } finally {
                restore();
            }
        });

        // =====================================================
        // LOAD EXISTING PARTICIPANTS
        // =====================================================
        async function loadExistingParticipants(trainingCode) {
            const tbody = document.getElementById('existingParticipantsBody');
            const emptyMsg = document.getElementById('existingEmpty');

            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>';
            emptyMsg.classList.add('d-none');

            try {
                const result = await apiFetch(`${URLS.participants}/${encodeURIComponent(trainingCode)}`);
                tbody.innerHTML = '';

                if (result.success && Array.isArray(result.data) && result.data.length > 0) {
                    result.data.forEach((p, index) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="text-center text-muted">${index + 1}</td>
                            <td class="fw-semibold">${escapeHtml(p.participant_name)}</td>
                            <td>${escapeHtml(p.position)}</td>
                            <td>${escapeHtml(p.email)}</td>
                            <td class="text-center">
                                <div class="row-actions">
                                    <button type="button" class="btn btn-sm row-action-btn btn-edit-participant"
                                        data-id="${escapeHtml(p.id)}"
                                        data-name="${escapeHtml(p.participant_name)}"
                                        data-position="${escapeHtml(p.position)}"
                                        data-email="${escapeHtml(p.email)}"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '';
                    emptyMsg.classList.remove('d-none');
                }
            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load participants.</td></tr>';
            }
        }

        // =====================================================
        // OPEN MANAGE MODAL
        // =====================================================
        document.querySelectorAll('.manage-participants-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const code = this.dataset.trainingCode;
                const title = this.dataset.trainingTitle;

                document.getElementById('manage_training_code').value = code;
                document.getElementById('manage_training_title').value = title;

                resetNewRows();
                loadExistingParticipants(code);
            });
        });

        // =====================================================
        // ADD / REMOVE ROW
        // =====================================================
        document.getElementById('addRowBtn').addEventListener('click', function () {
            newBody.appendChild(createNewParticipantRow());
            renumberNewRows();
        });

        newBody.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.btn-remove-new-row');
            if (!removeBtn) return;
            removeBtn.closest('tr').remove();
            if (newBody.children.length === 0) newBody.appendChild(createNewParticipantRow());
            renumberNewRows();
        });

        // =====================================================
        // PASTE FROM EXCEL (multi-cell only; appends)
        // =====================================================
        newBody.addEventListener('paste', function (e) {
            const text = (e.clipboardData || window.clipboardData).getData('text');
            if (!/[\t\r\n]/.test(text)) return;   // single value -> normal paste

            e.preventDefault();
            const list = text.trim()
                .split(/\r\n|\n|\r/)
                .filter(r => r.trim() !== '')
                .map(r => r.split('\t'));
            appendParticipantRows(list);
        });

        // =====================================================
        // EXPORT CSV
        // =====================================================
        document.getElementById('exportCsvBtn').addEventListener('click', function () {
            const code = document.getElementById('manage_training_code').value;
            if (!code) {
                alert('No training selected.');
                return;
            }
            window.location.href = `${URLS.exportCsv}/${encodeURIComponent(code)}`;
        });

        // =====================================================
        // IMPORT CSV
        // =====================================================
        document.getElementById('importCsvInput').addEventListener('change', function (e) {
            const input = e.target;
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (event) {
                const text = event.target.result;
                let rows = parseCsv(text, detectDelimiter(text));

                // skip header row if first cell is "name" / "participant name" etc.
                if (rows.length && /^(participant\s*)?name/i.test(rows[0][0].trim())) {
                    rows.shift();
                }

                if (rows.length === 0) {
                    alert('No participant rows found in the CSV file.');
                } else {
                    appendParticipantRows(rows);
                }
                input.value = '';
            };
            reader.onerror = function () {
                alert('Could not read the file.');
                input.value = '';
            };
            reader.readAsText(file);
        });

        // =====================================================
        // SAVE NEW PARTICIPANTS
        // =====================================================
        document.getElementById('saveNewParticipantsBtn').addEventListener('click', async function () {
            const trainingCode = document.getElementById('manage_training_code').value;
            const rows = newBody.querySelectorAll('tr');

            const participants = [];
            const badRows = [];

            rows.forEach((row, i) => {
                const name = row.querySelector('.new-name').value.trim();
                const position = row.querySelector('.new-position').value.trim();
                const email = row.querySelector('.new-email').value.trim();

                if (!name && !position && !email) return;   // ignore blank rows

                if (!name || !position || !isValidEmail(email)) {
                    badRows.push(i + 1);
                    return;
                }
                participants.push({ participant_name: name, position, email });
            });

            if (badRows.length) {
                alert('Please complete Name, Position and a valid Email for row(s): ' + badRows.join(', '));
                return;
            }
            if (participants.length === 0) {
                alert('Please add at least one participant.');
                return;
            }

            const restore = setBusy(this, 'Saving...');

            try {
                const result = await apiFetch(URLS.addBulk, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        training_code: trainingCode,
                        participants: participants,
                        [csrfName]: csrfHash
                    })
                });

                if (result.success) {
                    alert(`Successfully added ${result.count ?? participants.length} participant(s)!`);
                    resetNewRows();
                    loadExistingParticipants(trainingCode);
                } else {
                    showErrors(result, 'Failed to save participants.');
                }
            } catch (err) {
                console.error(err);
                alert(err.message || 'Network error. Please try again.');
            } finally {
                restore();
            }
        });

        // =====================================================
        // EDIT / DELETE (existing participants table)
        // =====================================================
        const editModalEl = document.getElementById('editParticipantModal');

        function openEditParticipant(editBtn) {
            document.getElementById('edit_participant_id').value = editBtn.dataset.id;
            document.getElementById('edit_participant_name').value = editBtn.dataset.name;
            document.getElementById('edit_position').value = editBtn.dataset.position;
            document.getElementById('edit_email').value = editBtn.dataset.email;

            bootstrap.Modal.getOrCreateInstance(editModalEl).show();
        }

        // Keep the Manage modal scrollable after the stacked Edit modal closes
        editModalEl.addEventListener('hidden.bs.modal', function () {
            if (document.querySelector('#manageParticipantsModal.show')) {
                document.body.classList.add('modal-open');
            }
        });

        document.getElementById('existingParticipantsBody').addEventListener('click', function (e) {
            const editBtn = e.target.closest('.btn-edit-participant');
            if (editBtn) {
                openEditParticipant(editBtn);
            }
        });

        // =====================================================
        // UPDATE PARTICIPANT
        // =====================================================
        document.getElementById('editParticipantForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = document.getElementById('edit_participant_id').value;
            const restore = setBusy(this.querySelector('button[type="submit"]'), 'Updating...');

            try {
                const result = await apiFetch(`${URLS.updateParticipant}/${encodeURIComponent(id)}`, {
                    method: 'POST',
                    body: buildFormData(this)
                });

                if (result.success) {
                    bootstrap.Modal.getOrCreateInstance(editModalEl).hide();
                    loadExistingParticipants(document.getElementById('manage_training_code').value);
                } else {
                    showErrors(result, 'Failed to update participant.');
                }
            } catch (err) {
                console.error('Update error:', err);
                alert(err.message || 'Network error. Please try again.');
            } finally {
                restore();
            }
        });

    });
</script>