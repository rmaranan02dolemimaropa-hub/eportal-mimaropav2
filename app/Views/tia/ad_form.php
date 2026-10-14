<?php
$training = $training ?? null;
$participant = $participant ?? null;   // optional: ipasa mula sa controller kung gusto ng prefill
$locked = !empty($training) ? 'readonly' : '';
$alreadySubmitted = $alreadySubmitted ?? null;

// Gumagawa ng 1-4 radio buttons (parehong markup ng dati)
$ratingRadios = function (string $name, bool $required = false): string {
    $html = '<div class="rating-options">';
    for ($v = 1; $v <= 4; $v++) {
        $req = ($required && $v === 1) ? ' required' : '';
        $html .= '<label><input type="radio" name="' . $name . '" value="' . $v . '"' . $req . '>' . $v . '</label>';
    }
    return $html . '</div>';
};

// Gumagawa ng table rows para sa rating statements
$ratingRows = function (string $prefix, array $statements) use ($ratingRadios): string {
    $html = '';
    foreach ($statements as $i => $text) {
        $n = $i + 1;
        $html .= '<tr><td>' . $n . '. ' . esc($text) . '</td><td>' . $ratingRadios("{$prefix}_{$n}", true) . '</td></tr>';
    }
    return $html;
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOLE Training Impact Assessment (Self-Rater Form)</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <meta name="description"
        content="One portal, multiple digital government services. Submit requests, access HR services, and connect with DOLE MIMAROPA online.">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/doleicon.png">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Form styles (absolute path para gumana kahit may ?t= sa URL) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tia-annexd.css') ?>">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="annex">ANNEX D</div>
            <div class="gov">Republic of the Philippines<br>DEPARTMENT OF LABOR AND EMPLOYMENT<br>Intramuros, Manila
            </div>
            <h1>TRAINING IMPACT ASSESSMENT</h1>
            <div class="sub">For In-house and Public Trainings<br>(SELF-RATER FORM)</div>
        </div>

        <div class="form-body">
            <div id="successMsg">
                <span id="successText">Form submitted successfully! Data is ready for the database.</span>
                <button type="button" id="closeSuccess" aria-label="Close">&times;</button>
            </div>

            <?php if (empty($training['training_code'])): ?>
                <div class="alert alert-warning">
                    Please open this form using the link sent to your email so your assessment can be tied to your
                    training.
                </div>
            <?php endif; ?>

            <form id="tiaForm" method="POST" action="">
                <?= csrf_field() ?>
                <?php if (!empty($training['training_code'])): ?>
                    <input type="hidden" name="training_code" value="<?= esc($training['training_code']) ?>">
                <?php endif; ?>

                <!-- Participant Info -->
                <div class="section">
                    <div class="section-title"><span>1</span> Participant Information</div>
                    <div class="info-grid">
                        <div class="field">
                            <label>Training Title *</label>
                            <input type="text" name="training_title" class="locked-field" required readonly
                                tabindex="-1" value="<?= esc($training['training_title'] ?? '') ?>">
                        </div>
                        <div class="field">
                            <label>Training Date/s *</label>
                            <input type="text" name="training_dates" class="locked-field" required readonly
                                tabindex="-1" value="<?= esc($training['training_dates'] ?? '') ?>">
                        </div>
                        <div class="field">
                            <label>Name of Participant *</label>
                            <input type="text" name="participant_name"
                                value="<?= esc($participant['participant_name'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label>Email Address *</label>
                            <input type="email" name="email" value="<?= esc($participant['email'] ?? '') ?>"
                                placeholder="juan.delacruz@dole.gov.ph" required>
                        </div>
                        <div class="field">
                            <label>Position *</label>
                            <input type="text" name="position" value="<?= esc($participant['position'] ?? '') ?>"
                                required>
                        </div>
                        <div class="field">
                            <label>Office *</label>
                            <select name="office" required>
                                <option value="">-- Select office --</option>
                                <?php foreach (($offices ?? []) as $o): ?>
                                    <option value="<?= esc($o, 'attr') ?>" <?= ($participant['office'] ?? '') === $o ? 'selected' : '' ?>>
                                        <?= esc($o) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Name of Immediate Superior *</label>
                            <input type="text" name="immediate_superior" list="superiorList" required
                                value="<?= esc($participant['immediate_superior'] ?? '') ?>">
                            <datalist id="superiorList">
                                <?php foreach (($superiors ?? []) as $s): ?>
                                    <option value="<?= esc($s['name'], 'attr') ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                    </div>
                </div>

                <!-- Scale Legend -->
                <div class="scale-legend">
                    <strong>DIRECTION:</strong> This survey intends to measure the training impact of the program on
                    your behavior, attitude and performance after attending the above training. Please take your
                    time to
                    provide accurate, complete and honest responses. On a scale of 1-4, rate the extent of your
                    agreement on the areas below.
                    <div class="scale-items">
                        <span><strong>1</strong> = Strongly Disagree</span>
                        <span><strong>2</strong> = Disagree</span>
                        <span><strong>3</strong> = Agree</span>
                        <span><strong>4</strong> = Strongly Agree</span>
                    </div>
                </div>

                <!-- PART I -->
                <div class="section">
                    <div class="section-title"><span>I</span> PART I – Attitudinal Outcomes & Performance Impact
                    </div>

                    <div class="subsection">ATTITUDINAL OUTCOMES – Changes in behavior/attitude</div>
                    <table class="rating-table">
                        <thead>
                            <tr>
                                <th>To what extent do you agree with the following:</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $ratingRows('att', [
                                'I adopted a more positive attitude towards work.',
                                'I have shown improved work habits after the training.',
                                'My motivation to perform the job has increased.',
                                'My interpersonal skill was enhanced.',
                                'I have been open to learning new things at work.',
                            ]) ?>
                        </tbody>
                    </table>

                    <div class="subsection">PERFORMANCE IMPACT – Changes in work performance and competencies</div>
                    <table class="rating-table">
                        <thead>
                            <tr>
                                <th>To what extent do you agree with the following:</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $ratingRows('perf', [
                                'The quality of my work has improved.',
                                'I have completed tasks with fewer errors.',
                                'I have accomplished my task at a faster rate.',
                                'I was able to manage workload better.',
                                'I became confident in performing work.',
                            ]) ?>
                        </tbody>
                    </table>

                    <div class="subsection">“After attending the training, my productivity on the following areas
                        have
                        improved”</div>
                    <p class="note">HRMOs to list down ALL target competencies of the program</p>
                    <div class="comp-heading">
                        <div class="comp-heading-title">
                            “After attending the training, my productivity on the following areas have improved”
                        </div>
                        <div class="comp-heading-note">
                            <i class="bi bi-info-circle"></i>
                            HRMOs to list down ALL target competencies of the program
                        </div>
                    </div>

                    <?php $comps = $competencies ?? []; ?>
                    <table class="rating-table" id="competencies">
                        <thead>
                            <tr>
                                <th>Target competency / area</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($comps): ?>
                                <?php foreach ($comps as $k => $label):
                                    $n = $k + 1; ?>
                                    <tr>
                                        <td>
                                            <?= $n ?>. <?= esc($label) ?>
                                            <input type="hidden" name="comp_<?= $n ?>_text" value="<?= esc($label, 'attr') ?>">
                                        </td>
                                        <td><?= $ratingRadios("comp_{$n}", true) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <tr>
                                        <td>
                                            <input type="text" class="table-input" name="comp_<?= $i ?>_text"
                                                placeholder="<?= $i ?>. Competency / Area">
                                        </td>
                                        <td><?= $ratingRadios("comp_{$i}") ?></td>
                                    </tr>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- CRITICAL SUCCESS FACTORS -->
                <div class="section">
                    <div class="section-title"><span>CSF</span> Critical Success Factors</div>

                    <div class="subsection">A. Achieving Training Objectives</div>
                    <p class="note">HRMO to align each statement with the objectives of the program</p>
                    <div id="objectives">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="custom-row">
                                <input type="text" name="obj_<?= $i ?>_text" placeholder="<?= $i ?>. I was able to ...">
                                <?= $ratingRadios("obj_{$i}") ?>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <div class="subsection">B. Application of new knowledge and skills in the workplace</div>
                    <table class="rating-table">
                        <thead>
                            <tr>
                                <th>To what extent do you agree with the following:</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $ratingRows('app', [
                                'The training I attended was relevant and directly applicable to my job.',
                                'I was able to use the new knowledge gained from the training in my job.',
                                'I was able to use the skills learned from the training in performing my tasks.',
                                'I was able to introduce new systems, procedures or innovations at work.',
                                'I have frequently applied the new knowledge/skill at work.',
                            ]) ?>
                        </tbody>
                    </table>
                </div>

                <!-- PART II -->
                <div class="section">
                    <div class="section-title"><span>II</span> PART II – Changes in Behavior and Performance</div>
                    <div class="field" style="margin-bottom:16px;">
                        <label>1. Kindly cite other changes in behavior you have developed as a result of your
                            attendance to the training.</label>
                        <textarea name="other_behavior_changes"></textarea>
                    </div>
                    <div class="field">
                        <label>2. Please list any areas at work where you believe you have shown measurable
                            improvement.</label>
                        <textarea name="measurable_improvements"></textarea>
                    </div>
                </div>

                <!-- PART III -->
                <div class="section">
                    <div class="section-title"><span>III</span> PART III – Challenges</div>
                    <p>What barriers (if any) have you encountered that have prevented you from using
                        skills/knowledge
                        gained from the training. Check all that apply.</p>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="barrier[]" value="no_opportunity"> I had no opportunity
                            to use the skills</label>
                        <label><input type="checkbox" name="barrier[]" value="not_enough_time"> I didn’t have enough
                            time to apply the skills/knowledge</label>
                        <label><input type="checkbox" name="barrier[]" value="work_environment"> My work environment
                            is not appropriate for the use of these skills/knowledge</label>
                        <label><input type="checkbox" name="barrier[]" value="superior_no_support"> My superior does
                            not support this type of program</label>
                        <label>
                            <input type="checkbox" name="barrier[]" value="others" id="barrierOthers">
                            Others (please specify)
                        </label>
                        <div class="field" style="margin-left:28px; margin-top:6px; display:none;" id="othersSpecify">
                            <input type="text" name="barrier_others_text" placeholder="Please specify...">
                        </div>
                        <label><input type="checkbox" name="barrier[]" value="none"> None</label>
                    </div>
                </div>

                <!-- PART IV -->
                <div class="section">
                    <div class="section-title"><span>IV</span> PART IV – Proposed Project and/or Innovations</div>
                    <p>Have you presented new ideas for possible projects to your immediate superior, the management
                        and/or senior officials after attending the training?</p>
                    <div class="yesno">
                        <label><input type="radio" name="presented_ideas" value="Yes" required> Yes</label>
                        <label><input type="radio" name="presented_ideas" value="No"> No</label>
                    </div>
                    <div class="field" style="margin-top:12px;">
                        <label>If yes, what are these ideas?</label>
                        <textarea name="ideas_yes"></textarea>
                    </div>
                    <div class="field" style="margin-top:12px;">
                        <label>If no, why?</label>
                        <textarea name="ideas_no"></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="resetBtn">Reset</button>
                    <button type="submit" class="btn-primary" <?= (empty($training['training_code']) || $alreadySubmitted) ? 'disabled' : '' ?>>Submit
                        Assessment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===================== SUCCESS MODAL ===================== -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center py-5 px-4">
                    <i class="bi bi-check-circle-fill" style="font-size: 3.8rem; color: #0d7a3f;"></i>

                    <h4 class="fw-bold mt-3 mb-2" id="successModalLabel" style="color: #0b3d91;">Assessment Submitted!
                    </h4>

                    <p class="text-muted mb-1">Your Training Impact Assessment has been received.</p>

                    <h3 class="fw-bold my-3" style="color: #10058C;" id="referenceNumber">
                        <!-- Reference number will appear here -->
                    </h3>

                    <p class="text-muted mb-4">
                        Please keep this reference number for your records.
                    </p>

                    <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal"
                        style="background-color: #0b3d91; border-color: #0b3d91;">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== ALREADY SUBMITTED MODAL ===================== -->
    <div class="modal fade" id="alreadyModal" tabindex="-1" aria-labelledby="alreadyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center py-5 px-4">
                    <i class="bi bi-exclamation-circle-fill" style="font-size: 3.8rem; color: #d98200;"></i>

                    <h4 class="fw-bold mt-3 mb-2" id="alreadyModalLabel" style="color: #0b3d91;">
                        Already Submitted
                    </h4>

                    <p class="text-muted mb-1">
                        You have already submitted a Training Impact Assessment for this training.
                    </p>

                    <h3 class="fw-bold my-3" style="color: #10058C;" id="alreadyReferenceNumber">
                        <?= esc($alreadySubmitted['reference_no'] ?? '') ?>
                    </h3>

                    <p class="text-muted mb-1" id="alreadySubmittedAt">
                        <?php if (!empty($alreadySubmitted['created_at'])): ?>
                            Submitted on
                            <?= esc(date('F j, Y g:i A', strtotime($alreadySubmitted['created_at']))) ?>
                        <?php endif; ?>
                    </p>

                    <p class="text-muted mb-4">
                        Each participant can submit only once. Please keep this reference number.
                    </p>

                    <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal"
                        style="background-color: #0b3d91; border-color: #0b3d91;">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const form = document.getElementById('tiaForm');
        const csrfName = '<?= csrf_token() ?>';

        // I-update ang CSRF token (tawagin pagkatapos ng form.reset())
        function refreshCsrf(token) {
            if (!token) return;
            const input = form.querySelector('input[name="' + csrfName + '"]');
            if (input) input.value = token;
        }

        // Show/hide Others field
        document.getElementById('barrierOthers').addEventListener('change', function () {
            document.getElementById('othersSpecify').style.display = this.checked ? 'block' : 'none';
        });

        // Reset button
        document.getElementById('resetBtn').addEventListener('click', function () {
            form.reset();
            document.getElementById('othersSpecify').style.display = 'none';
            document.getElementById('successMsg').style.display = 'none';
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Show the browser's own validation message instead of failing silently
            if (!form.reportValidity()) return;

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            try {
                const response = await fetch('<?= site_url('tia_store') ?>', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const raw = await response.text();
                let result = null;
                try { result = JSON.parse(raw); } catch (_) { /* not JSON */ }

                if (!result) {
                    console.error('HTTP ' + response.status, raw.slice(0, 500));
                    alert('Server error (HTTP ' + response.status + '). Open the browser console (F12) for details.');
                    return;
                }

                if (result.success) {
                    document.getElementById('referenceNumber').textContent = result.reference_no;
                    new bootstrap.Modal(document.getElementById('successModal')).show();
                    form.reset();
                    document.getElementById('othersSpecify').style.display = 'none';
                } else if (result.already_submitted) {
                    document.getElementById('alreadyReferenceNumber').textContent = result.reference_no || '';
                    document.getElementById('alreadySubmittedAt').textContent = result.submitted_at
                        ? 'Submitted on ' + new Date(result.submitted_at.replace(' ', 'T')).toLocaleString('en-PH', {
                            year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit'
                        })
                        : '';
                    new bootstrap.Modal(document.getElementById('alreadyModal')).show();
                }
                else {
                    let msg = result.message || 'Validation failed.';
                    if (result.errors) msg += '\n\n' + Object.values(result.errors).join('\n');
                    alert(msg);
                }

                refreshCsrf(result.csrf);
            } catch (error) {
                console.error(error);
                alert('Network error. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });

        // Close success message
        document.getElementById('closeSuccess').addEventListener('click', function () {
            document.getElementById('successMsg').style.display = 'none';
        });

        <?php if (!empty($alreadySubmitted)): ?>
            new bootstrap.Modal(document.getElementById('alreadyModal')).show();
        <?php endif; ?>
    </script>
</body>

</html>