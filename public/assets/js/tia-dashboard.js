/**
 * tia-dashboard.js
 * Training Impact Assessment - Dashboard (Trainings + Participants)
 *
 * Kailangan ng view ang window.TIA config bago i-load ang file na ito:
 *   window.TIA = { urls: {...}, csrf: { name, header, hash } };
 *
 * MAPA NG BUTTON -> MODAL -> ENDPOINT -> CONTROLLER
 * ---------------------------------------------------------------------------
 * Add Training      -> #addTrainingModal        -> POST store-training          -> storeTraining
 * Edit              -> #editTrainingModal       -> POST update-training/{code}  -> updateTraining
 * Status            -> #statusTrainingModal     -> POST update-status/{code}    -> updateTrainingStatus
 * Delete            -> confirm()                -> POST delete-training/{code}  -> deleteTraining
 * Manage            -> #manageParticipantsModal -> GET  participants/{code}     -> getParticipants
 * Save Participants -> (same modal)             -> POST add-participants-bulk   -> addParticipantsBulk
 * Export CSV        -> (same modal)             -> GET  export-participants/{c} -> exportParticipants
 * Edit participant  -> #editParticipantModal    -> POST update-participant/{id} -> updateParticipant
 *
 * DALOY NG HALOS LAHAT NG FORM:
 *   buksan modal -> lagyan ng data -> submit -> apiFetch -> success: reload / error: alert
 */

document.addEventListener("DOMContentLoaded", () => {
  initAddTraining();
  initEditTraining();
  initStatusTraining();
  initDeleteTraining();
  initManageParticipants(); // load, add/remove row, paste, import/export, save
  initEditParticipant();
});

/* =========================================================================
 * 1. CONFIG + CSRF
 * ========================================================================= */
const URLS = window.TIA.urls;
const csrfName = window.TIA.csrf.name;
const csrfHeader = window.TIA.csrf.header;
let csrfHash = window.TIA.csrf.hash; // let: puwedeng ma-refresh pagkatapos ng request

/* =========================================================================
 * 2. API HELPERS
 * ========================================================================= */

/**
 * Central fetch helper.
 * - laging may CSRF header + AJAX header
 * - ina-update ang CSRF token kung may data.csrf ang response
 * - laging JSON ang ibinabalik, o may readable na Error
 */
async function apiFetch(url, options = {}) {
  options.headers = Object.assign(
    {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
      [csrfHeader]: csrfHash,
    },
    options.headers || {},
  );

  const response = await fetch(url, options);

  let data = null;
  try {
    data = await response.json();
  } catch (e) {
    /* hindi JSON */
  }

  if (data && data.csrf) csrfHash = data.csrf;

  if (!data) {
    throw new Error(
      "Server returned an invalid response (HTTP " + response.status + ").",
    );
  }
  if (!response.ok && !data.message) {
    throw new Error("Request failed (HTTP " + response.status + ").");
  }
  return data;
}

function buildFormData(form) {
  const fd = form ? new FormData(form) : new FormData();
  fd.append(csrfName, csrfHash);
  return fd;
}

/**
 * Pangkaraniwang pattern ng form submit:
 * busy button -> POST -> success/error -> ibalik ang button
 */
async function submitForm({ form, url, busyText, errorText, onSuccess }) {
  const restore = setBusy(form.querySelector('[type="submit"]'), busyText);
  try {
    const result = await apiFetch(url, {
      method: "POST",
      body: buildFormData(form),
    });
    if (result.success) onSuccess(result);
    else showErrors(result, errorText);
  } catch (err) {
    console.error(err);
    alert(err.message || "Network error. Please try again.");
  } finally {
    restore();
  }
}

/* =========================================================================
 * 3. SMALL HELPERS
 * ========================================================================= */
const $ = (id) => document.getElementById(id);

function getModal(el) {
  return bootstrap.Modal.getOrCreateInstance(
    typeof el === "string" ? $(el) : el,
  );
}

function escapeHtml(text) {
  if (text === null || text === undefined) return "";
  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function setBusy(btn, busyText) {
  const original = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = busyText;
  return () => {
    btn.disabled = false;
    btn.innerHTML = original;
  };
}

function showErrors(result, fallback) {
  let msg = (result && result.message) || fallback;
  if (result && result.errors)
    msg += "\n\n" + Object.values(result.errors).join("\n");
  alert(msg);
}

// CSV parser (kaya ang quotes, comma sa loob ng quotes, BOM, CRLF)
function parseCsv(text, delimiter = ",") {
  text = text.replace(/^\uFEFF/, "");
  const rows = [];
  let row = [],
    cur = "",
    inQuotes = false;

  for (let i = 0; i < text.length; i++) {
    const ch = text[i];
    if (inQuotes) {
      if (ch === '"' && text[i + 1] === '"') {
        cur += '"';
        i++;
      } else if (ch === '"') {
        inQuotes = false;
      } else {
        cur += ch;
      }
    } else if (ch === '"') {
      inQuotes = true;
    } else if (ch === delimiter) {
      row.push(cur);
      cur = "";
    } else if (ch === "\n" || ch === "\r") {
      if (ch === "\r" && text[i + 1] === "\n") i++;
      row.push(cur);
      rows.push(row);
      row = [];
      cur = "";
    } else {
      cur += ch;
    }
  }
  if (cur !== "" || row.length) {
    row.push(cur);
    rows.push(row);
  }
  return rows.filter((r) => r.some((c) => c.trim() !== ""));
}

function detectDelimiter(text) {
  const firstLine = text.replace(/^\uFEFF/, "").split(/\r\n|\n|\r/)[0] || "";
  if (firstLine.includes("\t")) return "\t";
  if (firstLine.includes(";") && !firstLine.includes(",")) return ";";
  return ",";
}

/**
 * In-page alert.
 * type: "success" | "danger" | "warning"
 * Success alerts disappear on their own after 4 seconds.
 */
function showAlert(box, type, title, lines = []) {
  clearTimeout(box._timer);
  box.className = `alert alert-${type} mb-3`;
  box.innerHTML = "";

  const strong = document.createElement("strong");
  strong.textContent = title;
  box.appendChild(strong);

  if (lines.length) {
    const ul = document.createElement("ul");
    ul.className = "mb-0 mt-1";
    lines.forEach((text) => {
      const li = document.createElement("li");
      li.textContent = text; // textContent = safe, no HTML injection
      ul.appendChild(li);
    });
    box.appendChild(ul);
  }

  box.scrollIntoView({ behavior: "smooth", block: "nearest" });

  if (type === "success") {
    box._timer = setTimeout(() => hideAlert(box), 4000);
  }
}

function hideAlert(box) {
  clearTimeout(box._timer);
  box.classList.add("d-none");
}

/* =========================================================================
 * 4. ADD TRAINING
 * Trigger : button na may data-bs-target="#addTrainingModal"
 * Modal   : #addTrainingModal
 * Endpoint: POST tia/store-training
 * Fields  : training_title*, training_dates*, conducted_by, office, immediate_superior
 * ========================================================================= */
function initAddTraining() {
  const form = $("addTrainingForm");

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    submitForm({
      form,
      url: URLS.storeTraining,
      busyText: "Saving...",
      errorText: "Failed to save training.",
      onSuccess: () => {
        getModal("addTrainingModal").hide();
        form.reset();
        location.reload();
      },
    });
  });
}

/* =========================================================================
 * 5. EDIT TRAINING
 * Trigger : .edit-training-btn  (data-code, title, dates, conducted-by, office, superior)
 * Modal   : #editTrainingModal
 * Endpoint: POST tia/update-training/{code}
 * ========================================================================= */
function initEditTraining() {
  const form = $("editTrainingForm");

  // 1. buksan ang modal at punuin ng data
  document.querySelectorAll(".edit-training-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      $("edit_training_code").value = this.dataset.code;
      $("edit_training_title").value = this.dataset.title;
      $("edit_training_dates").value = this.dataset.dates;
      $("edit_conducted_by").value = this.dataset.conductedBy;
      $("edit_office").value = this.dataset.office;
      $("edit_immediate_superior").value = this.dataset.superior;
      getModal("editTrainingModal").show();
    });
  });

  // 2. i-submit
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const code = $("edit_training_code").value;
    submitForm({
      form,
      url: `${URLS.updateTraining}/${encodeURIComponent(code)}`,
      busyText: "Updating...",
      errorText: "Failed to update training.",
      onSuccess: () => {
        getModal("editTrainingModal").hide();
        location.reload();
      },
    });
  });
}

/* =========================================================================
 * 6. STATUS  (dating WALANG JS - ito ang dahilan kung bakit hindi gumagana)
 * Trigger : .status-training-btn  (data-code, title, status, dates)
 * Modal   : #statusTrainingModal
 * Endpoint: POST tia/update-status/{code}
 * Fields  : status*, reason (required kung canceled/rescheduled), new_dates (rescheduled lang)
 * ========================================================================= */
function initStatusTraining() {
  const form = $("statusTrainingForm");
  const select = $("status_select");
  const newDatesWrap = $("statusNewDatesWrap");
  const reasonWrap = $("statusReasonWrap");
  const reasonInput = $("status_reason");
  const newDatesInput = $("status_new_dates");

  // values of the training currently being edited (set when the modal opens)
  let currentStatus = "active";
  let currentDates = "";

  // show/hide fields AND set which ones are required
  function toggleFields() {
    const v = select.value;
    const isResched = v === "rescheduled";
    const needsReason = isResched || v === "canceled";

    newDatesWrap.classList.toggle("d-none", !isResched);
    reasonWrap.classList.toggle("d-none", !needsReason);

    // hidden fields must NOT be required, or the browser blocks the submit
    newDatesInput.required = isResched;
    reasonInput.required = needsReason;
  }
  select.addEventListener("change", toggleFields);

  // 1. open the modal
  document.querySelectorAll(".status-training-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      currentStatus = this.dataset.status || "active";
      currentDates = this.dataset.dates || "";

      $("status_training_code").value = this.dataset.code;
      $("status_training_title").value = this.dataset.title;
      select.value = currentStatus;
      reasonInput.value = "";
      newDatesInput.value = "";
      toggleFields();
      getModal("statusTrainingModal").show();
    });
  });

  // 2. validate, then submit
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const status = select.value;
    const reason = reasonInput.value.trim();
    const newDates = newDatesInput.value.trim();

    // CONDITION 1: nothing changed (same status, and not a reschedule)
    if (status === currentStatus && status !== "rescheduled") {
      alert(`This training is already "${status}". Choose a different status.`);
      return;
    }

    // CONDITION 2: rescheduled needs NEW dates that differ from the current ones
    if (status === "rescheduled") {
      if (!newDates) {
        alert("Please enter the new training date/s.");
        newDatesInput.focus();
        return;
      }
      if (newDates.toLowerCase() === currentDates.trim().toLowerCase()) {
        alert("The new date/s must be different from the current date/s.");
        newDatesInput.focus();
        return;
      }
    }

    // CONDITION 3: canceled / rescheduled need a reason
    if ((status === "canceled" || status === "rescheduled") && !reason) {
      alert("Please provide a reason.");
      reasonInput.focus();
      return;
    }

    const code = $("status_training_code").value;
    submitForm({
      form,
      url: `${URLS.updateStatus}/${encodeURIComponent(code)}`,
      busyText: "Updating...",
      errorText: "Failed to update status.",
      onSuccess: () => {
        getModal("statusTrainingModal").hide();
        location.reload();
      },
    });
  });
}

/* =========================================================================
 * 7. DELETE TRAINING  (buburahin din ang lahat ng participants)
 * Trigger : .delete-training-btn  (data-code, title)
 * Modal   : wala, confirm() lang
 * Endpoint: POST tia/delete-training/{code}
 * ========================================================================= */
function initDeleteTraining() {
  const modalEl = $("deleteTrainingModal");
  const codeInput = $("delete_training_code");
  const titleEl = $("delete_training_title");
  const confirmBtn = $("confirmDeleteTrainingBtn");

  // 1. Delete button on a row: fill in the modal and open it
  document.querySelectorAll(".delete-training-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      codeInput.value = this.dataset.code;
      titleEl.textContent = this.dataset.title; // textContent = safe, no HTML injection
      getModal(modalEl).show();
    });
  });

  // 2. "Yes, Delete" button inside the modal: send the request
  confirmBtn.addEventListener("click", async function () {
    const code = codeInput.value;
    const restore = setBusy(this, "Deleting...");

    try {
      const result = await apiFetch(
        `${URLS.deleteTraining}/${encodeURIComponent(code)}`,
        { method: "POST", body: buildFormData() },
      );

      if (result.success) {
        getModal(modalEl).hide();
        location.reload();
      } else {
        showErrors(result, "Failed to delete training.");
      }
    } catch (err) {
      console.error(err);
      alert(err.message || "Network error. Please try again.");
    } finally {
      restore();
    }
  });
}

/* =========================================================================
 * 8. MANAGE PARTICIPANTS
 * Trigger : .manage-participants-btn  (data-training-code, data-training-title)
 * Modal   : #manageParticipantsModal
 * May tatlong bahagi:
 *   A. Current participants  -> GET participants/{code}
 *   B. Table ng bagong rows  -> add/remove row, paste mula Excel, import CSV
 *   C. Export CSV            -> GET export-participants/{code}
 *   D. Save                  -> POST add-participants-bulk (JSON)
 * ========================================================================= */
function initManageParticipants() {
  const saveBtn = $("saveNewParticipantsBtn");
  const newBody = $("newParticipantsBody");
  const alertBox = $("participantsAlert");

  // ---------- A. CURRENT PARTICIPANTS ----------
  async function loadExistingParticipants(trainingCode) {
    const tbody = $("existingParticipantsBody");
    const emptyMsg = $("existingEmpty");

    tbody.innerHTML =
      '<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>';
    emptyMsg.classList.add("d-none");

    try {
      const result = await apiFetch(
        `${URLS.participants}/${encodeURIComponent(trainingCode)}`,
      );
      tbody.innerHTML = "";

      if (
        result.success &&
        Array.isArray(result.data) &&
        result.data.length > 0
      ) {
        result.data.forEach((p, index) => {
          const tr = document.createElement("tr");
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
        emptyMsg.classList.remove("d-none");
      }
    } catch (err) {
      console.error(err);
      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center text-danger">Failed to load participants.</td></tr>';
    }
  }

  // gagamitin din ng initEditParticipant() pagkatapos mag-update
  window.reloadParticipants = () =>
    loadExistingParticipants($("manage_training_code").value);

  // ---------- B. BAGONG ROWS ----------
  function createRow(name = "", position = "", email = "") {
    const tr = document.createElement("tr");
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

  function renumberRows() {
    newBody.querySelectorAll("tr").forEach((row, i) => {
      row.querySelector(".row-number").textContent = i + 1;
    });
  }

  function isRowBlank(tr) {
    return ![...tr.querySelectorAll("input")].some(
      (i) => i.value.trim() !== "",
    );
  }
  // show the Save button only if at least one row has data
  function updateSaveBtn() {
    const hasData = [...newBody.querySelectorAll("tr")].some(
      (tr) => !isRowBlank(tr),
    );
    saveBtn.classList.toggle("d-none", !hasData);
  }

  function resetRows() {
    newBody.innerHTML = "";
    newBody.appendChild(createRow());
    renumberRows();
    updateSaveBtn();
  }

  // dagdagan ng rows (galing paste o CSV); tanggalin muna ang blangkong rows
  function appendRows(list) {
    newBody.querySelectorAll("tr").forEach((tr) => {
      if (isRowBlank(tr)) tr.remove();
    });
    list.forEach((c) =>
      newBody.appendChild(
        createRow(
          (c[0] || "").trim(),
          (c[1] || "").trim(),
          (c[2] || "").trim(),
        ),
      ),
    );
    if (newBody.children.length === 0) newBody.appendChild(createRow());
    renumberRows();
    updateSaveBtn();
  }

  // ---------- BUKSAN ANG MODAL ----------
  document.querySelectorAll(".manage-participants-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      $("manage_training_code").value = this.dataset.trainingCode;
      $("manage_training_title").value = this.dataset.trainingTitle;
      resetRows();
      loadExistingParticipants(this.dataset.trainingCode);
    });
  });

  // ---------- ADD / REMOVE ROW ----------
  $("addRowBtn").addEventListener("click", () => {
    newBody.appendChild(createRow());
    renumberRows();
    updateSaveBtn();
  });

  newBody.addEventListener("click", (e) => {
    const removeBtn = e.target.closest(".btn-remove-new-row");
    if (!removeBtn) return;
    removeBtn.closest("tr").remove();
    if (newBody.children.length === 0) newBody.appendChild(createRow());
    renumberRows();
  });

  newBody.addEventListener("input", updateSaveBtn);

  // ---------- PASTE MULA EXCEL (multi-cell lang) ----------
  newBody.addEventListener("paste", (e) => {
    const text = (e.clipboardData || window.clipboardData).getData("text");
    if (!/[\t\r\n]/.test(text)) return; // iisang value -> normal na paste

    e.preventDefault();
    const list = text
      .trim()
      .split(/\r\n|\n|\r/)
      .filter((r) => r.trim() !== "")
      .map((r) => r.split("\t"));
    appendRows(list);
  });

  // ---------- IMPORT CSV ----------
  $("importCsvInput").addEventListener("change", (e) => {
    const input = e.target;
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (event) => {
      const text = event.target.result;
      const rows = parseCsv(text, detectDelimiter(text));

      // laktawan ang header row kung "name" / "participant name" ang unang cell
      if (rows.length && /^(participant\s*)?name/i.test(rows[0][0].trim()))
        rows.shift();

      if (rows.length === 0)
        alert("No participant rows found in the CSV file.");
      else appendRows(rows);
      input.value = "";
    };
    reader.onerror = () => {
      alert("Could not read the file.");
      input.value = "";
    };
    reader.readAsText(file);
  });

  // ---------- C. EXPORT CSV ----------
  $("exportCsvBtn").addEventListener("click", () => {
    const code = $("manage_training_code").value;
    if (!code) {
      alert("No training selected.");
      return;
    }
    window.location.href = `${URLS.exportCsv}/${encodeURIComponent(code)}`;
  });

  // ---------- D. SAVE NEW PARTICIPANTS ----------
  $("saveNewParticipantsBtn").addEventListener("click", async function () {
    const trainingCode = $("manage_training_code").value;

    hideAlert(alertBox);
    const participants = [];
    const badRows = [];

    newBody.querySelectorAll("tr").forEach((row, i) => {
      const name = row.querySelector(".new-name").value.trim();
      const position = row.querySelector(".new-position").value.trim();
      const email = row.querySelector(".new-email").value.trim();

      if (!name && !position && !email) return; // skip blank rows

      const missing = [];
      if (!name) missing.push("Name");
      if (!position) missing.push("Position");
      if (!email) missing.push("Email");
      else if (!isValidEmail(email)) missing.push("valid Email");

      if (missing.length) {
        badRows.push(`Row ${i + 1}: ${missing.join(", ")}`);
        return;
      }
      participants.push({ participant_name: name, position, email });
    });

    if (badRows.length) {
      showAlert(alertBox, "danger", "Please fix the following:", badRows);
      return;
    }
    if (participants.length === 0) {
      showAlert(alertBox, "danger", "Nothing to save.", [
        "Fill in at least one participant (Name, Position, and a valid Email).",
      ]);
      return;
    }

    const restore = setBusy(this, "Saving...");
    try {
      const result = await apiFetch(URLS.addBulk, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          training_code: trainingCode,
          participants,
          [csrfName]: csrfHash,
        }),
      });

      if (result.success) {
        resetRows();
        loadExistingParticipants(trainingCode);

        const failed = Array.isArray(result.failed) ? result.failed : [];
        showAlert(
          alertBox,
          failed.length ? "warning" : "success",
          `Successfully added ${result.count ?? participants.length} participant(s)!`,
          failed.length ? ["Could not save: " + failed.join(", ")] : [],
        ); // must come AFTER resetRows(), because resetRows() hides the alert
      } else {
        showAlert(
          alertBox,
          "danger",
          result.message || "Failed to save participants.",
          result.errors ? Object.values(result.errors) : [],
        );
      }
    } catch (err) {
      console.error(err);
      showAlert(
        alertBox,
        "danger",
        err.message || "Network error. Please try again.",
      );
    } finally {
      restore();
    }
  });
}

/* =========================================================================
 * 9. EDIT PARTICIPANT  (modal sa ibabaw ng Manage modal)
 * Trigger : .btn-edit-participant (nilikha ng JS, kaya delegated click)
 * Modal   : #editParticipantModal
 * Endpoint: POST tia/update-participant/{id}
 * ========================================================================= */
function initEditParticipant() {
  const modalEl = $("editParticipantModal");
  const form = $("editParticipantForm");
  const editAlert = $("editParticipantAlert"); // inside the Edit modal
  const manageAlert = $("participantsAlert"); // inside the Manage modal

  // open the Edit modal (delegated: the buttons are created by JS)
  $("existingParticipantsBody").addEventListener("click", (e) => {
    const btn = e.target.closest(".btn-edit-participant");
    if (!btn) return;

    hideAlert(editAlert);
    hideAlert(manageAlert);

    $("edit_participant_id").value = btn.dataset.id;
    $("edit_participant_name").value = btn.dataset.name;
    $("edit_position").value = btn.dataset.position;
    $("edit_email").value = btn.dataset.email;
    getModal(modalEl).show();
  });

  // hide the error as soon as the user edits a field
  form.addEventListener("input", () => hideAlert(editAlert));

  // keep the Manage modal scrollable after the stacked Edit modal closes
  modalEl.addEventListener("hidden.bs.modal", () => {
    if (document.querySelector("#manageParticipantsModal.show")) {
      document.body.classList.add("modal-open");
    }
  });

  // submit
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hideAlert(editAlert);

    const id = $("edit_participant_id").value;
    const restore = setBusy(
      form.querySelector('[type="submit"]'),
      "Updating...",
    );

    try {
      const result = await apiFetch(
        `${URLS.updateParticipant}/${encodeURIComponent(id)}`,
        { method: "POST", body: buildFormData(form) },
      );

      if (result.success) {
        getModal(modalEl).hide();
        window.reloadParticipants();
        showAlert(
          manageAlert,
          "success",
          result.message || "Participant updated successfully!",
        );
      } else {
        showAlert(
          editAlert,
          "danger",
          result.message || "Failed to update participant.",
          result.errors ? Object.values(result.errors) : [],
        );
      }
    } catch (err) {
      console.error(err);
      showAlert(
        editAlert,
        "danger",
        err.message || "Network error. Please try again.",
      );
    } finally {
      restore();
    }
  });
}
