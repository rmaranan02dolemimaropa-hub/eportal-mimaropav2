/**
 * "Send Form to All" button in the Manage Participants modal.
 * Asks for confirmation, then POSTs the training code to TIA.urls.sendFormEmails.
 * Needs window.TIA (urls.sendFormEmails + csrf) from the page.
 */
(function () {
  "use strict";

  var T = window.TIA;
  var btn = document.getElementById("sendFormEmailsBtn");
  var confirmEl = document.getElementById("sendEmailsModal");
  var goBtn = document.getElementById("confirmSendEmailsBtn");
  var alertBox = document.getElementById("participantsAlert");

  if (!T || !T.urls || !T.urls.sendFormEmails || !btn || !confirmEl || !goBtn) {
    return;
  }

  function participantRows() {
    return Array.prototype.filter.call(
      document.querySelectorAll("#existingParticipantsBody tr"),
      function (tr) {
        return tr.querySelectorAll("td").length >= 4;
      },
    );
  }

  function showAlert(type, message) {
    if (!alertBox) return;
    alertBox.className = "alert alert-" + type;
    alertBox.textContent = message;
    alertBox.scrollIntoView({ block: "nearest", behavior: "smooth" });
  }

  // open confirmation
  btn.addEventListener("click", function () {
    var count = participantRows().length;
    if (count === 0) {
      showAlert("warning", "There are no participants to email yet.");
      return;
    }
    document.getElementById("sendEmails_title").textContent =
      document.getElementById("manage_training_title").value;
    document.getElementById("sendEmails_count").textContent =
      count + (count === 1 ? " participant" : " participants");
    bootstrap.Modal.getOrCreateInstance(confirmEl).show();
  });

  // keep the parent modal scrollable after the confirm modal closes
  confirmEl.addEventListener("hidden.bs.modal", function () {
    if (document.querySelector(".modal.show")) {
      document.body.classList.add("modal-open");
    }
  });

  // send
  goBtn.addEventListener("click", function () {
    var original = goBtn.innerHTML;
    goBtn.disabled = true;
    goBtn.innerHTML =
      '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

    var body = new FormData();
    body.append(
      "training_code",
      document.getElementById("manage_training_code").value,
    );
    body.append(T.csrf.name, T.csrf.hash);

    var headers = { "X-Requested-With": "XMLHttpRequest" };
    headers[T.csrf.header] = T.csrf.hash;

    fetch(T.urls.sendFormEmails, {
      method: "POST",
      body: body,
      headers: headers,
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        if (res.csrf) T.csrf.hash = res.csrf;
        bootstrap.Modal.getOrCreateInstance(confirmEl).hide();
        showAlert(res.success ? "success" : "danger", res.message || "Done.");
      })
      .catch(function (err) {
        console.error(err);
        bootstrap.Modal.getOrCreateInstance(confirmEl).hide();
        showAlert("danger", "Could not send the emails. Please try again.");
      })
      .finally(function () {
        goBtn.disabled = false;
        goBtn.innerHTML = original;
      });
  });
})();
