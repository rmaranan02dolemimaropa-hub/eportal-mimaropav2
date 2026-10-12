/**
 * "Notify Superior" button in the Manage Participants modal.
 * POSTs the training code to TIA.urls.notifySuperior.
 */
(function () {
  "use strict";

  var T = window.TIA;
  var btn = document.getElementById("notifySuperiorBtn");
  var alertBox = document.getElementById("participantsAlert");

  if (!T || !T.urls || !T.urls.notifySuperior || !btn) return;

  function showAlert(type, message) {
    if (!alertBox) return;
    alertBox.className = "alert alert-" + type;
    alertBox.textContent = message;
    alertBox.scrollIntoView({ block: "nearest", behavior: "smooth" });
  }

  btn.addEventListener("click", function () {
    var code = document.getElementById("manage_training_code").value;
    var title = document.getElementById("manage_training_title").value;

    if (!code) {
      showAlert("warning", "No training selected.");
      return;
    }
    if (
      !confirm(
        'Email the immediate superior to rate the participants of:\n\n"' +
          title +
          '"?',
      )
    ) {
      return;
    }

    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML =
      '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

    var body = new FormData();
    body.append("training_code", code);
    body.append(T.csrf.name, T.csrf.hash);

    var headers = { "X-Requested-With": "XMLHttpRequest" };
    headers[T.csrf.header] = T.csrf.hash;

    fetch(T.urls.notifySuperior, {
      method: "POST",
      body: body,
      headers: headers,
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        if (res.csrf) T.csrf.hash = res.csrf;
        showAlert(res.success ? "success" : "danger", res.message || "Done.");
      })
      .catch(function (err) {
        console.error(err);
        showAlert("danger", "Could not send the email. Please try again.");
      })
      .finally(function () {
        btn.disabled = false;
        btn.innerHTML = original;
      });
  });
})();
