<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOLE Training Impact Assessment (Self-Rater Form)</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="assets/css/tia-annexd.css">

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

            <form id="tiaForm" method="POST" action="">
                <?= csrf_field() ?>
                <!-- Participant Info -->
                <div class="section">
                    <div class="section-title"><span>1</span> Participant Information</div>
                    <div class="info-grid">
                        <div class="field">
                            <label>Training Title *</label>
                            <input type="text" name="training_title" required>
                        </div>
                        <div class="field">
                            <label>Training Date/s *</label>
                            <input type="text" name="training_dates" placeholder="e.g. Jan 15-17, 2026" required>
                        </div>
                        <div class="field">
                            <label>Name of Participant *</label>
                            <input type="text" name="participant_name" required>
                        </div>
                        <div class="field">
                            <label>Email Address *</label>
                            <input type="email" name="email" placeholder="juan.delacruz@dole.gov.ph" required>
                        </div>
                        <div class="field">
                            <label>Position *</label>
                            <input type="text" name="position" required>
                        </div>
                        <div class="field">
                            <label>Office *</label>
                            <input type="text" name="office" required>
                        </div>
                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Name of Immediate Superior *</label>
                            <input type="text" name="immediate_superior" required>
                        </div>
                    </div>
                </div>

                <!-- Scale Legend -->
                <div class="scale-legend">
                    <strong>DIRECTION:</strong> This survey intends to measure the training impact of the program on
                    your behavior, attitude and performance after attending the above training. Please take your time to
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
                            <tr>
                                <td>1. I adopted a more positive attitude towards work.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="att_1" value="1" required>1</label>
                                        <label><input type="radio" name="att_1" value="2">2</label>
                                        <label><input type="radio" name="att_1" value="3">3</label>
                                        <label><input type="radio" name="att_1" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2. I have shown improved work habits after the training.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="att_2" value="1" required>1</label>
                                        <label><input type="radio" name="att_2" value="2">2</label>
                                        <label><input type="radio" name="att_2" value="3">3</label>
                                        <label><input type="radio" name="att_2" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3. My motivation to perform the job has increased.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="att_3" value="1" required>1</label>
                                        <label><input type="radio" name="att_3" value="2">2</label>
                                        <label><input type="radio" name="att_3" value="3">3</label>
                                        <label><input type="radio" name="att_3" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4. My interpersonal skill was enhanced.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="att_4" value="1" required>1</label>
                                        <label><input type="radio" name="att_4" value="2">2</label>
                                        <label><input type="radio" name="att_4" value="3">3</label>
                                        <label><input type="radio" name="att_4" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>5. I have been open to learning new things at work.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="att_5" value="1" required>1</label>
                                        <label><input type="radio" name="att_5" value="2">2</label>
                                        <label><input type="radio" name="att_5" value="3">3</label>
                                        <label><input type="radio" name="att_5" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
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
                            <tr>
                                <td>1. The quality of my work has improved.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="perf_1" value="1" required>1</label>
                                        <label><input type="radio" name="perf_1" value="2">2</label>
                                        <label><input type="radio" name="perf_1" value="3">3</label>
                                        <label><input type="radio" name="perf_1" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2. I have completed tasks with fewer errors.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="perf_2" value="1" required>1</label>
                                        <label><input type="radio" name="perf_2" value="2">2</label>
                                        <label><input type="radio" name="perf_2" value="3">3</label>
                                        <label><input type="radio" name="perf_2" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3. I have accomplished my task at a faster rate.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="perf_3" value="1" required>1</label>
                                        <label><input type="radio" name="perf_3" value="2">2</label>
                                        <label><input type="radio" name="perf_3" value="3">3</label>
                                        <label><input type="radio" name="perf_3" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4. I was able to manage workload better.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="perf_4" value="1" required>1</label>
                                        <label><input type="radio" name="perf_4" value="2">2</label>
                                        <label><input type="radio" name="perf_4" value="3">3</label>
                                        <label><input type="radio" name="perf_4" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>5. I became confident in performing work.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="perf_5" value="1" required>1</label>
                                        <label><input type="radio" name="perf_5" value="2">2</label>
                                        <label><input type="radio" name="perf_5" value="3">3</label>
                                        <label><input type="radio" name="perf_5" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="subsection">“After attending the training, my productivity on the following areas have
                        improved”</div>
                    <p class="note">HRMOs to list down ALL target competencies of the program</p>
                    <div id="competencies">
                        <div class="custom-row">
                            <input type="text" name="comp_1_text" placeholder="1. Competency / Area">
                            <div class="rating-options">
                                <label><input type="radio" name="comp_1" value="1">1</label>
                                <label><input type="radio" name="comp_1" value="2">2</label>
                                <label><input type="radio" name="comp_1" value="3">3</label>
                                <label><input type="radio" name="comp_1" value="4">4</label>
                            </div>
                        </div>
                        <div class="custom-row">
                            <input type="text" name="comp_2_text" placeholder="2. Competency / Area">
                            <div class="rating-options">
                                <label><input type="radio" name="comp_2" value="1">1</label>
                                <label><input type="radio" name="comp_2" value="2">2</label>
                                <label><input type="radio" name="comp_2" value="3">3</label>
                                <label><input type="radio" name="comp_2" value="4">4</label>
                            </div>
                        </div>
                        <div class="custom-row">
                            <input type="text" name="comp_3_text" placeholder="3. Competency / Area">
                            <div class="rating-options">
                                <label><input type="radio" name="comp_3" value="1">1</label>
                                <label><input type="radio" name="comp_3" value="2">2</label>
                                <label><input type="radio" name="comp_3" value="3">3</label>
                                <label><input type="radio" name="comp_3" value="4">4</label>
                            </div>
                        </div>
                        <div class="custom-row">
                            <input type="text" name="comp_4_text" placeholder="4. Competency / Area">
                            <div class="rating-options">
                                <label><input type="radio" name="comp_4" value="1">1</label>
                                <label><input type="radio" name="comp_4" value="2">2</label>
                                <label><input type="radio" name="comp_4" value="3">3</label>
                                <label><input type="radio" name="comp_4" value="4">4</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CRITICAL SUCCESS FACTORS -->
                <div class="section">
                    <div class="section-title"><span>CSF</span> Critical Success Factors</div>

                    <div class="subsection">A. Achieving Training Objectives</div>
                    <p class="note">HRMO to align each statement with the objectives of the program</p>
                    <div id="objectives">
                        <div class="custom-row">
                            <input type="text" name="obj_1_text" placeholder="1. I was able to ...">
                            <div class="rating-options">
                                <label><input type="radio" name="obj_1" value="1">1</label>
                                <label><input type="radio" name="obj_1" value="2">2</label>
                                <label><input type="radio" name="obj_1" value="3">3</label>
                                <label><input type="radio" name="obj_1" value="4">4</label>
                            </div>
                        </div>
                        <div class="custom-row">
                            <input type="text" name="obj_2_text" placeholder="2. I was able to ...">
                            <div class="rating-options">
                                <label><input type="radio" name="obj_2" value="1">1</label>
                                <label><input type="radio" name="obj_2" value="2">2</label>
                                <label><input type="radio" name="obj_2" value="3">3</label>
                                <label><input type="radio" name="obj_2" value="4">4</label>
                            </div>
                        </div>
                        <div class="custom-row">
                            <input type="text" name="obj_3_text" placeholder="3. I was able to ...">
                            <div class="rating-options">
                                <label><input type="radio" name="obj_3" value="1">1</label>
                                <label><input type="radio" name="obj_3" value="2">2</label>
                                <label><input type="radio" name="obj_3" value="3">3</label>
                                <label><input type="radio" name="obj_3" value="4">4</label>
                            </div>
                        </div>
                        <div class="custom-row">
                            <input type="text" name="obj_4_text" placeholder="4. I was able to ...">
                            <div class="rating-options">
                                <label><input type="radio" name="obj_4" value="1">1</label>
                                <label><input type="radio" name="obj_4" value="2">2</label>
                                <label><input type="radio" name="obj_4" value="3">3</label>
                                <label><input type="radio" name="obj_4" value="4">4</label>
                            </div>
                        </div>
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
                            <tr>
                                <td>1. The training I attended was relevant and directly applicable to my job.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="app_1" value="1" required>1</label>
                                        <label><input type="radio" name="app_1" value="2">2</label>
                                        <label><input type="radio" name="app_1" value="3">3</label>
                                        <label><input type="radio" name="app_1" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2. I was able to use the new knowledge gained from the training in my job.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="app_2" value="1" required>1</label>
                                        <label><input type="radio" name="app_2" value="2">2</label>
                                        <label><input type="radio" name="app_2" value="3">3</label>
                                        <label><input type="radio" name="app_2" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3. I was able to use the skills learned from the training in performing my tasks.
                                </td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="app_3" value="1" required>1</label>
                                        <label><input type="radio" name="app_3" value="2">2</label>
                                        <label><input type="radio" name="app_3" value="3">3</label>
                                        <label><input type="radio" name="app_3" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4. I was able to introduce new systems, procedures or innovations at work.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="app_4" value="1" required>1</label>
                                        <label><input type="radio" name="app_4" value="2">2</label>
                                        <label><input type="radio" name="app_4" value="3">3</label>
                                        <label><input type="radio" name="app_4" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>5. I have frequently applied the new knowledge/skill at work.</td>
                                <td>
                                    <div class="rating-options">
                                        <label><input type="radio" name="app_5" value="1" required>1</label>
                                        <label><input type="radio" name="app_5" value="2">2</label>
                                        <label><input type="radio" name="app_5" value="3">3</label>
                                        <label><input type="radio" name="app_5" value="4">4</label>
                                    </div>
                                </td>
                            </tr>
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
                    <p>What barriers (if any) have you encountered that have prevented you from using skills/knowledge
                        gained from the training. Check all that apply.</p>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="barrier[]" value="no_opportunity"> I had no opportunity to
                            use the skills</label>
                        <label><input type="checkbox" name="barrier[]" value="not_enough_time"> I didn’t have enough
                            time to apply the skills/knowledge</label>
                        <label><input type="checkbox" name="barrier[]" value="work_environment"> My work environment is
                            not appropriate for the use of these skills/knowledge</label>
                        <label><input type="checkbox" name="barrier[]" value="superior_no_support"> My superior does not
                            support this type of program</label>
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
                    <button type="button" class="btn-secondary"
                        onclick="document.getElementById('tiaForm').reset(); document.getElementById('successMsg').style.display='none';">Reset</button>
                    <button type="submit" class="btn-primary">Submit Assessment</button>
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

                    <h4 class="fw-bold mt-3 mb-2" style="color: #0b3d91;">Assessment Submitted!</h4>

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

    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        // Show/hide Others field
        document.getElementById('barrierOthers').addEventListener('change', function () {
            document.getElementById('othersSpecify').style.display = this.checked ? 'block' : 'none';
        });

        document.getElementById('tiaForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            // Disable button while submitting
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            const formData = new FormData(form);

            try {
                const response = await fetch('http://localhost:8080/tia', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Show reference number in the modal
                    document.getElementById('referenceNumber').textContent = result.reference_no;

                    // Show Bootstrap success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    // Reset form
                    form.reset();
                    document.getElementById('othersSpecify').style.display = 'none';
                } else {
                    // Show validation errors
                    let errorMsg = result.message || 'Validation failed.';
                    if (result.errors) {
                        errorMsg += '\n\n' + Object.values(result.errors).join('\n');
                    }
                    alert(errorMsg);
                    console.log(result.errors);
                }
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
    </script>
</body>

</html>