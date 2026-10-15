<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/tia-base.css') ?>">

<?php
// Quarter of each training (from its date text; falls back to when it was added)
$monthNames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
$quarters = [];
foreach (($trainings ?? []) as $i => $t) {
    $text = (string) ($t['training_dates'] ?? '');
    $created = strtotime((string) ($t['created_at'] ?? '')) ?: null;
    $month = 0;
    $year = 0;
    $m = [];
    $y = [];

    if (preg_match('/\b(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*/i', $text, $m)) {
        $month = (int) array_search(strtolower($m[1]), $monthNames, true) + 1;
    }
    if (preg_match('/\b(20\d{2})\b/', $text, $y)) {
        $year = (int) $y[1];
    }

    // fall back to when the training was added
    if ($month === 0 && $created) {
        $month = (int) date('n', $created);
    }
    if ($year === 0 && $created) {
        $year = (int) date('Y', $created);
    }

    $quarters[$i] = ($month > 0 && $year > 0) ? $year . '-Q' . (int) ceil($month / 3) : '';
}

// Years shown in the dropdown: every year found in the data + the current year
$years = array_map(fn($q) => (int) substr($q, 0, 4), array_filter($quarters));
$years[] = (int) date('Y');
$years = array_values(array_unique($years));
rsort($years);

$qMonths = [1 => 'Jan–Mar', 2 => 'Apr–Jun', 3 => 'Jul–Sep', 4 => 'Oct–Dec'];
?>

<main class="main-content tia-page">

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

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Training Impact Assessment</h1>
            <p class="page-subtitle">Manage trainings and their participants</p>
        </div>
        <div class="d-flex flex-wrap gap-2 tia-page">
            <a href="<?= site_url('tia/trainings-template') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-arrow-down me-1"></i> CSV Template
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#importTrainingsModal">
                <i class="bi bi-upload me-1"></i> Import CSV
            </button>
            <a href="<?= site_url('tia/export-trainings') ?>" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTrainingModal">
                <i class="bi bi-plus-lg me-1"></i> Add Training
            </button>
        </div>
    </div>

    <!-- Training Table Panel -->
    <div class="panel table-panel">
        <div class="panel-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h2 class="panel-title mb-0">Trainings</h2>

            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                <select id="filterYear" class="form-select form-select-sm" autocomplete="off"
                    style="width:130px; flex:0 0 auto;">
                    <option value="">All years</option>
                    <?php foreach ($years as $yr): ?>
                        <option value="<?= $yr ?>"><?= $yr ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="filterQuarter" class="form-select form-select-sm" autocomplete="off"
                    style="width:170px; flex:0 0 auto;">
                    <option value="">All quarters</option>
                    <?php foreach ($qMonths as $qn => $label): ?>
                        <option value="<?= $qn ?>">Q<?= $qn ?> (<?= $label ?>)</option>
                    <?php endforeach; ?>
                </select>

                <div class="input-group input-group-sm" style="width:280px; flex:0 0 auto;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" id="trainingSearch" class="form-control" placeholder="Search trainings..."
                        autocomplete="off">
                </div>
            </div>
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
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($trainings)): ?>
                        <?php foreach ($trainings as $index => $row): ?>
                            <tr data-quarter="<?= esc($quarters[$index] ?? '', 'attr') ?>">
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <span class="ref-no"><?= esc($row['training_title']) ?></span>
                                </td>
                                <td><?= esc($row['training_dates']) ?></td>
                                <td><?= esc($row['conducted_by'] ?? '—') ?></td>
                                <td><?= esc($row['office'] ?? '—') ?></td>
                                <td><?= esc($row['immediate_superior'] ?? '—') ?>
                                    <?php if (!empty($row['superior_notified_at'])): ?>
                                        <i class="bi bi-envelope-check text-success ms-1"
                                            title="Superior notified <?= (int) ($row['superior_notify_count'] ?? 1) ?>x · last <?= esc(date('M d, Y h:i A', strtotime($row['superior_notified_at'])), 'attr') ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <?php
                                $status = $row['status'] ?? 'active';
                                $badgeMap = [
                                    'active' => 'bg-success',
                                    'canceled' => 'bg-danger',
                                    'rescheduled' => 'bg-warning text-dark',
                                    'completed' => 'bg-primary',
                                ];
                                $badge = $badgeMap[$status] ?? 'bg-secondary';
                                $reason = $status === 'canceled'
                                    ? ($row['cancel_reason'] ?? '')
                                    : ($status === 'rescheduled' ? ($row['reschedule_reason'] ?? '') : '');
                                ?>

                                <td>
                                    <?php if ($reason): ?>
                                        <span class="badge <?= esc($badge, 'attr') ?>" title="<?= esc($reason, 'attr') ?>">
                                        <?php else: ?>
                                            <span class="badge <?= esc($badge, 'attr') ?>">
                                            <?php endif; ?>
                                            <?= esc(ucfirst($status)) ?>
                                        </span>
                                </td>
                                <td class="text-center">

                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        <button type="button" class="btn btn-sm tool-btn manage-participants-btn"
                                            title="Manage Participants" aria-label="Manage Participants" data-bs-toggle="modal"
                                            data-bs-target="#manageParticipantsModal"
                                            data-training-code="<?= esc($row['training_code'], 'attr') ?>"
                                            data-training-title="<?= esc($row['training_title'], 'attr') ?>">
                                            <i class="bi bi-people me-1"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm tool-btn edit-training-btn"
                                            title="Edit Training" aria-label="Edit Training"
                                            data-code="<?= esc($row['training_code'], 'attr') ?>"
                                            data-title="<?= esc($row['training_title'], 'attr') ?>"
                                            data-dates="<?= esc($row['training_dates'], 'attr') ?>"
                                            data-conducted-by="<?= esc($row['conducted_by'] ?? '', 'attr') ?>"
                                            data-office="<?= esc($row['office'] ?? '', 'attr') ?>"
                                            data-superior="<?= esc($row['immediate_superior'] ?? '', 'attr') ?>"
                                            data-competencies="<?= esc(json_encode(json_decode($row['competencies'] ?? '[]', true) ?: []), 'attr') ?>">
                                            <i class="bi bi-pencil me-1"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm tool-btn status-training-btn"
                                            title="Update Status" aria-label="Update Status"
                                            data-code="<?= esc($row['training_code'], 'attr') ?>"
                                            data-title="<?= esc($row['training_title'], 'attr') ?>"
                                            data-status="<?= esc($status, 'attr') ?>"
                                            data-dates="<?= esc($row['training_dates'], 'attr') ?>">
                                            <i class="bi bi-flag me-1"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm tool-btn text-danger delete-training-btn"
                                            title="Delete Training" aria-label="Delete Training"
                                            data-code="<?= esc($row['training_code'], 'attr') ?>"
                                            data-title="<?= esc($row['training_title'], 'attr') ?>">
                                            <i class="bi bi-trash me-1"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
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
                        <div class="mb-3">
                            <label class="form-label">Target Competencies / Areas</label>
                            <small class="text-muted d-block mb-2">Shown on the self-rater form under “my productivity
                                on the following areas have improved”. Up to 4.</small>
                            <div class="competency-inputs" id="add_competencies">
                                <?php for ($c = 1; $c <= 4; $c++): ?>
                                    <input type="text" class="form-control mb-2" name="competencies[]" maxlength="255"
                                        placeholder="<?= $c ?>. Competency / Area">
                                <?php endfor; ?>
                            </div>
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

    <!-- ===================== EDIT TRAINING MODAL ===================== -->
    <div class="modal fade" id="editTrainingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content add-training-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> Edit Training
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editTrainingForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_training_code">
                        <div class="mb-3">
                            <label class="form-label">Training Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_training_title" name="training_title"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Training Date/s <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_training_dates" name="training_dates"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Conducted by</label>
                            <input type="text" class="form-control" id="edit_conducted_by" name="conducted_by">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Office</label>
                            <input type="text" class="form-control" id="edit_office" name="office">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name of Immediate Superior</label>
                            <input type="text" class="form-control" id="edit_immediate_superior"
                                name="immediate_superior">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Competencies / Areas</label>
                            <small class="text-muted d-block mb-2">Shown on the self-rater form under “my productivity
                                on the following areas have improved”. Up to 4.</small>
                            <div class="competency-inputs" id="edit_competencies">
                                <?php for ($c = 1; $c <= 4; $c++): ?>
                                    <input type="text" class="form-control mb-2" name="competencies[]" maxlength="255"
                                        placeholder="<?= $c ?>. Competency / Area">
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Training</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================== STATUS MODAL ===================== -->
    <div class="modal fade" id="statusTrainingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content add-training-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-flag me-2"></i> Update Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="statusTrainingForm">
                    <div class="modal-body">
                        <input type="hidden" id="status_training_code">

                        <div class="mb-3">
                            <label class="form-label">Training</label>
                            <input type="text" class="form-control" id="status_training_title" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_select" name="status" required>
                                <option value="active">Active</option>
                                <option value="rescheduled">Rescheduled</option>
                                <option value="canceled">Canceled</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="statusNewDatesWrap">
                            <label class="form-label">New Training Date/s <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="status_new_dates" name="new_dates"
                                placeholder="e.g. April 10-12, 2026">
                            <small class="text-muted">Required when rescheduling. Must be different from the current
                                date.</small>
                        </div>

                        <div class="mb-3 d-none" id="statusReasonWrap">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="status_reason" name="reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
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
                    <div id="participantsAlert" class="alert d-none" role="alert"></div>
                    <!-- Training info -->
                    <div class="mb-4">
                        <label class="form-label">Training</label>
                        <input type="text" class="form-control" id="manage_training_title" readonly>
                        <input type="hidden" id="manage_training_code">
                    </div>

                    <!-- Current participants -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h6 class="detail-section-title mb-0">
                            <i class="bi bi-person-check"></i> Current Participants
                        </h6>
                        <div class="d-flex gap-2 flex-wrap">

                            <button type="button" class="btn btn-sm tool-btn" id="sendFormEmailsBtn">
                                <i class="bi bi-envelope-paper me-1"></i> Send Form to All
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive mb-2">
                        <table class="table requests-table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Email</th>
                                    <th style="width:130px;" class="text-center">TIA Status</th>
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
                    <button type="button" class="btn btn-primary d-none" id="saveNewParticipantsBtn">
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
                        <div id="editParticipantAlert" class="alert d-none" role="alert"></div>
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

    <!-- ===================== DELETE TRAINING MODAL ===================== -->
    <div class="modal fade" id="deleteTrainingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content add-training-modal">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i> Delete Training
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delete_training_code">
                    <p class="mb-2">Are you sure you want to delete this training?</p>
                    <p class="fw-semibold mb-3" id="delete_training_title"></p>
                    <div class="alert alert-danger mb-0 py-2">
                        All participants of this training will also be deleted.
                        This cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteTrainingBtn">
                        <i class="bi bi-trash me-1"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== DELETE PARTICIPANT MODAL ===================== -->
    <div class="modal fade" id="deleteParticipantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content add-training-modal">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i> Delete Participant
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="deleteParticipantAlert" class="alert d-none" role="alert"></div>
                    <input type="hidden" id="delete_participant_id">

                    <p class="mb-2">Are you sure you want to delete this participant?</p>

                    <div class="border rounded p-3 mb-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Name of Participant</small>
                            <span class="fw-semibold" id="delete_participant_name"></span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Position</small>
                            <span id="delete_participant_position"></span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Email Address</small>
                            <span id="delete_participant_email"></span>
                        </div>
                    </div>

                    <div class="alert alert-danger mb-0 py-2">This cannot be undone.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteParticipantBtn">
                        <i class="bi bi-trash me-1"></i> Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== SEND FORM EMAILS MODAL ===================== -->
    <div class="modal fade" id="sendEmailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content add-training-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-envelope-paper me-2"></i> Send Form to All Participants
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">The TIA Self-Rater Form (Annex D) link will be emailed to
                        <strong id="sendEmails_count"></strong> of:
                    </p>
                    <p class="fw-semibold mb-3" id="sendEmails_title"></p>
                    <div class="alert alert-warning mb-0 py-2">
                        Everyone listed under Current Participants will receive the email.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSendEmailsBtn">
                        <i class="bi bi-send me-1"></i> Send Emails
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== IMPORT TRAININGS MODAL ===================== -->
    <div class="modal fade" id="importTrainingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content add-training-modal" action="<?= site_url('tia/import-trainings') ?>"
                method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload me-2"></i> Import Trainings (CSV)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        Columns required: <code>TRAINING TITLE, TRAINING DATES</code>.<br>
                        Optional: <code>TRAINING CODE</code> (blank = auto-generate; if the code, or the same
                        title and dates, already exists, that training is updated),
                        <code>CONDUCTED BY</code>, <code>OFFICE</code>, <code>IMMEDIATE SUPERIOR</code>,
                        <code>COMPETENCY 1</code> to <code>COMPETENCY 4</code>.
                    </p>
                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>

</main>

<?= $this->include('dashboard_layout/loading') ?>

<!-- remove this line if dashboard_layout/head already loads Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.TIA = {
        urls: {
            storeTraining: '<?= site_url('tia/store-training') ?>',
            participants: '<?= site_url('tia/participants') ?>',
            exportCsv: '<?= site_url('tia/export-participants') ?>',
            addBulk: '<?= site_url('tia/add-participants-bulk') ?>',
            updateParticipant: '<?= site_url('tia/update-participant') ?>',
            updateTraining: '<?= site_url('tia/update-training') ?>',
            deleteTraining: '<?= site_url('tia/delete-training') ?>',
            updateStatus: '<?= site_url('tia/update-status') ?>',
            deleteParticipant: '<?= site_url('tia/delete-participant') ?>',
            sendFormEmails: '<?= site_url('tia/send-form-emails') ?>',
            notifySuperior: '<?= site_url('tia/notify-superior') ?>',
        },
        csrf: {
            name: '<?= csrf_token() ?>',
            header: '<?= csrf_header() ?>',
            hash: '<?= csrf_hash() ?>'
        }
    };
    // Hover labels for the action buttons
    document.querySelectorAll('.tool-btn[title]').forEach(function (el) {
        var tip = new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top', container: 'body' });

        // hide the bubble when the button is clicked (so it doesn't stick over a modal)
        el.addEventListener('click', function () { tip.hide(); });
    });
</script>
<script src="<?= base_url('assets/js/tia-dashboard.js') ?>"></script>
<script src="<?= base_url('assets/js/tia-send-emails.js') ?>"></script>

<script>window.EMPLOYEE_LIST_URL = '<?= site_url('employees/all') ?>';</script>
<script src="<?= base_url('assets/js/employee-select.js') ?>"></script>
<script src="<?= base_url('assets/js/tia-notify-superior.js') ?>"></script>
<script src="<?= base_url('assets/js/tia-search.js') ?>"></script>
<script src="<?= base_url('assets/js/tia-competencies.js') ?>"></script>