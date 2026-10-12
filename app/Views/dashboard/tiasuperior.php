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
            <p class="page-subtitle">Rate the participants of your trainings</p>
        </div>
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
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($trainings)): ?>
                        <?php foreach ($trainings as $index => $row): ?>
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
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><span class="ref-no"><?= esc($row['training_title']) ?></span></td>
                                <td><?= esc($row['training_dates']) ?></td>
                                <td><?= esc($row['conducted_by'] ?? '—') ?></td>
                                <td><?= esc($row['office'] ?? '—') ?></td>
                                <td><?= esc($row['immediate_superior'] ?? '—') ?></td>
                                <td>
                                    <span class="badge <?= esc($badge, 'attr') ?>" <?= $reason ? 'title="' . esc($reason, 'attr') . '"' : '' ?>>
                                        <?= esc(ucfirst($status)) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        <button type="button" class="btn btn-sm tool-btn rate-participants-btn"
                                            title="Rate Participants" aria-label="Rate Participants" data-bs-toggle="modal"
                                            data-bs-target="#rateParticipantsModal"
                                            data-training-code="<?= esc($row['training_code'], 'attr') ?>"
                                            data-training-title="<?= esc($row['training_title'], 'attr') ?>">
                                            <i class="bi bi-star me-1"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                No trainings found for you yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===================== RATE PARTICIPANTS MODAL ===================== -->
    <div class="modal fade" id="rateParticipantsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content manage-participants-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-star me-2"></i> Rate Participants
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="participantsAlert" class="alert d-none" role="alert"></div>

                    <div class="mb-4">
                        <label class="form-label">Training</label>
                        <input type="text" class="form-control" id="rate_training_title" readonly>
                        <input type="hidden" id="rate_training_code">
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h6 class="detail-section-title mb-0">
                            <i class="bi bi-person-check"></i> Current Participants
                        </h6>
                        <small class="text-muted" id="ratingSummary"></small>
                    </div>

                    <div class="table-responsive">
                        <table class="table requests-table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Email</th>
                                    <th style="width:150px;" class="text-center">TIA Status</th>
                                    <th style="width:110px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="existingParticipantsBody"></tbody>
                        </table>
                    </div>
                    <div id="existingEmpty" class="text-muted text-center py-3 d-none">
                        No participants yet.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== RATING SHEET MODAL ===================== -->
    <div class="modal fade" id="ratingSheetModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-text me-2"></i> Rating Sheet (Annex E)
                        <span class="text-muted fs-6 ms-2" id="ratingSheetFor"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="ratingSheetFrame" title="Rating Sheet" src="about:blank"
                        style="width:100%;height:78vh;border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>

</main>

<?= $this->include('dashboard_layout/loading') ?>

<!-- remove this line if dashboard_layout/head already loads Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.TIA_SUP = {
        urls: {
            participants: '<?= site_url('tia-superior/participants') ?>',
            rate: '<?= site_url('tia-superior/rate') ?>'
        }
    };
    // Hover labels for the action buttons
    document.querySelectorAll('.tool-btn[title]').forEach(function (el) {
        var tip = new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top', container: 'body' });
        el.addEventListener('click', function () { tip.hide(); });
    });
</script>
<script src="<?= base_url('assets/js/tia-superior.js') ?>"></script>