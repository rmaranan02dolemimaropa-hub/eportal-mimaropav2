<?php
$training = $training ?? null;
$participant = $participant ?? null;   // staff being rated (name, position)
$superior = $superior ?? null;      // rater (name, position)

// 1-4 radio buttons (same markup as the Annex D form)
$ratingRadios = function (string $name, bool $required = false): string {
    $html = '<div class="rating-options">';
    for ($v = 1; $v <= 4; $v++) {
        $req = ($required && $v === 1) ? ' required' : '';
        $html .= '<label><input type="radio" name="' . $name . '" value="' . $v . '"' . $req . '>' . $v . '</label>';
    }
    return $html . '</div>';
};

// Table rows for rating statements
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
    <title>DOLE Training Impact Assessment (Superior Rater Form)</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/img/doleicon.png') ?>">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Same styles as the Annex D form -->
    <link rel="stylesheet" href="<?= base_url('assets/css/tia-annexd.css') ?>">

    <style>
        .locked-field,
        .locked-field:focus {
            background: #f1f3f5;
            color: #495057;
            cursor: not-allowed;
            border-color: #ced4da;
            box-shadow: none;
            outline: none;
        }

        .deferment-hint {
            display: none;
            color: #b45309;
            font-weight: 600;
            font-size: .85rem;
            margin: 0 0 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="annex">ANNEX E</div>
            <div class="gov">Republic of the Philippines<br>DEPARTMENT OF LABOR AND EMPLOYMENT<br>Intramuros, Manila
            </div>
            <h1>TRAINING IMPACT ASSESSMENT</h1>
            <div class="sub">For In-house and Public Trainings<br>(SUPERIOR RATER FORM)</div>
        </div>

        <div class="form-body">

            <?php if (empty($training['training_code'])): ?>
                <div class="alert alert-warning">
                    Please open this form using the link sent to your email so your assessment can be tied to the
                    training.
                </div>
            <?php endif; ?>

            <form id="tiaForm" method="POST" action="">
                <?= csrf_field() ?>
                <?php if (!empty($training['training_code'])): ?>
                    <input type="hidden" name="training_code" value="<?= esc($training['training_code']) ?>">
                <?php endif; ?>

                <!-- Participant / Rater Info -->
                <div class="section">
                    <div class="section-title"><span>1</span> Training and Participant Information</div>
                    <div class="info-grid">
                        <div class="field">

                            <?php if (!empty($participant['id'])): ?>
                                <input type="hidden" name="participant_id" value="<?= (int) $participant['id'] ?>">
                            <?php endif; ?>

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
                            <label>Name of Immediate Superior *</label>
                            <input type="text" name="superior_name" value="<?= esc($superior['name'] ?? '') ?>"
                                required>
                        </div>
                        <div class="field">
                            <label>Position of Immediate Superior *</label>
                            <input type="text" name="superior_position" value="<?= esc($superior['position'] ?? '') ?>"
                                required>
                        </div>
                        <div class="field">
                            <label>Name of Staff/Participant *</label>
                            <input type="text" name="participant_name"
                                value="<?= esc($participant['participant_name'] ?? '') ?>" required>
                        </div>
                        <div class="field">
                            <label>Position of Staff/Participant *</label>
                            <input type="text" name="participant_position"
                                value="<?= esc($participant['position'] ?? '') ?>" required>
                        </div>
                    </div>
                    <p class="note" style="margin-top:8px;">Use a separate form for 2 or more participants.</p>
                </div>

                <!-- Scale Legend -->
                <div class="scale-legend">
                    <strong>DIRECTION:</strong> This survey intends to measure the training impact of the program on
                    the behavior, attitude and performance of your staff after attending the above training. Please
                    take your time to provide accurate, complete and honest responses. On a scale of 1-4, rate the
                    extent of your agreement on the areas below.
                    <div class="scale-items">
                        <span><strong>1</strong> = Strongly Disagree</span>
                        <span><strong>2</strong> = Disagree</span>
                        <span><strong>3</strong> = Agree</span>
                        <span><strong>4</strong> = Strongly Agree</span>
                    </div>
                </div>

                <!-- PART I -->
                <div class="section">
                    <div class="section-title"><span>I</span> PART I – Attitudinal Outcomes & Performance Impact</div>

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
                                'The participant has adopted a more positive attitude towards work.',
                                'The participant has shown improved work habits after the training.',
                                'The participant’s motivation to perform the job has increased.',
                                'The participant’s interpersonal skill was enhanced.',
                                'The participant has been open to learning new things at work.',
                            ]) ?>
                        </tbody>
                    </table>

                    <div class="subsection">PERFORMANCE IMPACT – Changes in work performance</div>
                    <table class="rating-table">
                        <thead>
                            <tr>
                                <th>To what extent do you agree with the following:</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $ratingRows('perf', [
                                'The participant’s quality of work has improved.',
                                'The participant’s completed tasks with fewer errors.',
                                'The participant’s accomplishes task at a faster rate.',
                                'The participant was able to manage workload better.',
                                'The participant became confident in performing work.',
                            ]) ?>
                        </tbody>
                    </table>

                    <div class="subsection">“After attending the training, I have observed improved productivity on
                        the following areas”</div>
                    <p class="note">List the areas where you observed improved productivity, then rate each.</p>
                    <div id="competencies">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="custom-row">
                                <input type="text" name="comp_<?= $i ?>_text" placeholder="<?= $i ?>. Area">
                                <?= $ratingRadios("comp_{$i}") ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- CRITICAL SUCCESS FACTORS -->
                <div class="section">
                    <div class="section-title"><span>CSF</span> Critical Success Factors</div>

                    <div class="subsection">Application of new knowledge and skills in the workplace</div>
                    <table class="rating-table" id="csfTable">
                        <thead>
                            <tr>
                                <th>To what extent do you agree with the following:</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $ratingRows('app', [
                                'The training attended by the participant was relevant and directly applicable to the job.',
                                'The participant used the new knowledge gained from the training in the job.',
                                'The participant used the skills learned from the training in performing tasks.',
                                'The participant was able to introduce new systems, procedures or innovations at work.',
                                'The participant has frequently applied the new knowledge/skill at work.',
                            ]) ?>
                        </tbody>
                    </table>
                </div>

                <!-- PART II -->
                <div class="section">
                    <div class="section-title"><span>II</span> PART II – Changes in Behavior and Performance</div>
                    <div class="field" style="margin-bottom:16px;">
                        <label>1. Kindly cite other changes in the participant’s behavior as a result of attending the
                            training.</label>
                        <textarea name="other_behavior_changes"></textarea>
                    </div>
                    <div class="field">
                        <label>2. Please list any areas at work where the participant has shown measurable
                            improvement.</label>
                        <textarea name="measurable_improvements"></textarea>
                    </div>
                </div>

                <!-- PART III -->
                <div class="section">
                    <div class="section-title"><span>III</span> PART III – Deferment on the Application of Knowledge
                        and Skills</div>
                    <p>If you have 2-3 questions answered with Disagree to Strongly Disagree under Part I, Critical
                        Success Factors, please explain why the new knowledge and skill gained from the training was
                        not applied by the participant at work.</p>
                    <p class="deferment-hint" id="defermentHint">Two or more Critical Success Factors were rated
                        Disagree or Strongly Disagree. Please explain below.</p>
                    <div class="field">
                        <textarea name="deferment_reason" id="defermentReason"></textarea>
                    </div>
                </div>

                <!-- PART IV -->
                <div class="section">
                    <div class="section-title"><span>IV</span> PART IV – Reason for Sending Staff to Training</div>
                    <p>What is/are the primary reason/s for sending your staff to the program (check all that
                        apply)</p>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="reason[]" value="long_term_strategy"> Crucial to DOLE’s
                            long-term strategy</label>
                        <label><input type="checkbox" name="reason[]" value="competitive_advantage"> Gain competitive
                            advantage</label>
                        <label><input type="checkbox" name="reason[]" value="operational_efficiency"> Improve
                            operational efficiency</label>
                        <label><input type="checkbox" name="reason[]" value="ongoing_projects"> Critical to on-going
                            projects/initiatives</label>
                        <label><input type="checkbox" name="reason[]" value="employee_development"> Support employee
                            development</label>
                        <label>
                            <input type="checkbox" name="reason[]" value="others" id="reasonOthers">
                            Others (please specify)
                        </label>
                        <div class="field" style="margin-left:28px; margin-top:6px; display:none;" id="othersSpecify">
                            <input type="text" name="reason_others_text" placeholder="Please specify...">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="resetBtn">Reset</button>
                    <button type="submit" class="btn-primary" <?= (empty($training['training_code']) || empty($participant['id'])) ? 'disabled' : '' ?>>Submit
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

                    <h3 class="fw-bold my-3" style="color: #10058C;" id="referenceNumber"></h3>

                    <p class="text-muted mb-4">Please keep this reference number for your records.</p>

                    <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal"
                        style="background-color: #0b3d91; border-color: #0b3d91;">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const form = document.getElementById('tiaForm');
        const csrfName = '<?= csrf_token() ?>';

        function refreshCsrf(token) {
            if (!token) return;
            const input = form.querySelector('input[name="' + csrfName + '"]');
            if (input) input.value = token;
        }

        // Show/hide "Others" field
        document.getElementById('reasonOthers').addEventListener('change', function () {
            document.getElementById('othersSpecify').style.display = this.checked ? 'block' : 'none';
        });

        // Part III: if 2+ Critical Success Factors are rated 1 or 2, an explanation is required
        function checkDeferment() {
            let low = 0;
            for (let i = 1; i <= 5; i++) {
                const sel = form.querySelector('input[name="app_' + i + '"]:checked');
                if (sel && parseInt(sel.value, 10) <= 2) low++;
            }
            const needed = low >= 2;
            document.getElementById('defermentHint').style.display = needed ? 'block' : 'none';
            document.getElementById('defermentReason').required = needed;
        }
        document.getElementById('csfTable').addEventListener('change', checkDeferment);

        // Reset
        document.getElementById('resetBtn').addEventListener('click', function () {
            form.reset();
            document.getElementById('othersSpecify').style.display = 'none';
            checkDeferment();
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!form.reportValidity()) return;

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            try {
                const response = await fetch('<?= site_url('tia_superior_store') ?>', {
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
                    checkDeferment();
                } else {
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
    </script>
</body>

</html>