/**
 * tia-superior.js
 * Training Impact Assessment - Superior view (Rate Participants)
 *
 * The view must define window.TIA_SUP before loading this file:
 *   window.TIA_SUP = { urls: { participants, rate } };
 *
 * FLOW:
 *   Rate Participants button -> #rateParticipantsModal -> GET participants/{code}
 *   Rating Sheet button      -> #ratingSheetModal (iframe of the Annex E form)
 *   After the form is saved the form tells this page (postMessage), the
 *   sheet modal closes and the participant list reloads.
 */

document.addEventListener("DOMContentLoaded", () => {
  const URLS = window.TIA_SUP.urls;

  const $ = (id) => document.getElementById(id);
  const alertBox = $("participantsAlert");
  const tbody = $("existingParticipantsBody");
  const emptyMsg = $("existingEmpty");
  const summary = $("ratingSummary");

  const sheetEl = $("ratingSheetModal");
  const sheetFrame = $("ratingSheetFrame");
  const sheetFor = $("ratingSheetFor");
  const sheetModal = bootstrap.Modal.getOrCreateInstance(sheetEl);

  /* ---------- helpers ---------- */
  function escapeHtml(text) {
    if (text === null || text === undefined) return "";
    return String(text)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function showAlert(type, message) {
    clearTimeout(alertBox._timer);
    alertBox.className = `alert alert-${type} mb-3`;
    alertBox.textContent = message;
    alertBox.scrollIntoView({ behavior: "smooth", block: "nearest" });
    alertBox._timer = setTimeout(() => alertBox.classList.add("d-none"), 6000);
  }

  function hideAlert() {
    clearTimeout(alertBox._timer);
    alertBox.classList.add("d-none");
  }

  // Rated / Pending badge (rating done by the superior)
  function ratingBadge(p) {
    if (!p.rated) {
      return '<span class="badge bg-secondary"><i class="bi bi-hourglass-split me-1"></i>Pending</span>';
    }

    let when = "";
    if (p.rated_at) {
      const d = new Date(String(p.rated_at).replace(" ", "T"));
      if (!isNaN(d)) when = d.toLocaleDateString();
    }
    const title = `Ref: ${p.rating_ref || ""} ${when}`.trim();

    return `<span class="badge bg-success" title="${escapeHtml(title)}">
              <i class="bi bi-check-circle me-1"></i>Rated
            </span>`;
  }

  // Action cell: opens the rating sheet modal, or a disabled "Rated" button
  function actionCell(p) {
    if (p.rated) {
      return `<button type="button" class="btn btn-sm tool-btn" disabled>
                <i class="bi bi-check-circle me-1"></i> Rated
              </button>`;
    }
    return `<button type="button" class="btn btn-sm tool-btn btn-open-rating"
              data-id="${escapeHtml(p.id)}"
              data-name="${escapeHtml(p.participant_name)}">
              <i class="bi bi-file-earmark-text me-1"></i> Rating Sheet
            </button>`;
  }

  /* ---------- load participants ---------- */
  async function loadParticipants(trainingCode) {
    tbody.innerHTML =
      '<tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>';
    emptyMsg.classList.add("d-none");
    summary.textContent = "";

    try {
      const response = await fetch(
        `${URLS.participants}/${encodeURIComponent(trainingCode)}`,
        {
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
          },
        },
      );
      const result = await response.json();
      tbody.innerHTML = "";

      if (!result.success) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${escapeHtml(result.message || "Failed to load participants.")}</td></tr>`;
        return;
      }

      if (!Array.isArray(result.data) || result.data.length === 0) {
        emptyMsg.classList.remove("d-none");
        return;
      }

      result.data.forEach((p, index) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td class="text-center text-muted">${index + 1}</td>
          <td class="fw-semibold">${escapeHtml(p.participant_name)}</td>
          <td>${escapeHtml(p.position)}</td>
          <td>${escapeHtml(p.email)}</td>
          <td class="text-center">${ratingBadge(p)}</td>
          <td class="text-center">${actionCell(p)}</td>
        `;
        tbody.appendChild(tr);
      });

      const done = result.data.filter((p) => p.rated).length;
      summary.textContent = `${done} of ${result.data.length} rated`;
    } catch (err) {
      console.error(err);
      tbody.innerHTML =
        '<tr><td colspan="6" class="text-center text-danger">Failed to load participants.</td></tr>';
    }
  }

  /* ---------- open the Rate Participants modal ---------- */
  document.querySelectorAll(".rate-participants-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      hideAlert();
      $("rate_training_code").value = this.dataset.trainingCode;
      $("rate_training_title").value = this.dataset.trainingTitle;
      loadParticipants(this.dataset.trainingCode);
    });
  });

  /* ---------- open the Rating Sheet modal ---------- */
  // delegated: the buttons are created by JS
  tbody.addEventListener("click", (e) => {
    const btn = e.target.closest(".btn-open-rating");
    if (!btn) return;

    const code = $("rate_training_code").value;
    sheetFor.textContent = btn.dataset.name || "";
    sheetFrame.src = `${URLS.rate}/${encodeURIComponent(code)}/${encodeURIComponent(btn.dataset.id)}`;
    sheetModal.show();
  });

  // clear the iframe when the sheet closes, and keep the parent modal scrollable
  sheetEl.addEventListener("hidden.bs.modal", () => {
    sheetFrame.src = "about:blank";
    sheetFor.textContent = "";
    if (document.querySelector("#rateParticipantsModal.show")) {
      document.body.classList.add("modal-open");
    }
  });

  /* ---------- the form tells us when it was saved ---------- */
  window.addEventListener("message", (e) => {
    if (e.origin !== window.location.origin) return;
    if (!e.data || e.data.type !== "tia-rated") return;

    sheetModal.hide();
    loadParticipants($("rate_training_code").value);
    showAlert(
      "success",
      `Assessment submitted. Reference no.: ${e.data.reference_no || ""}`,
    );
  });
});
