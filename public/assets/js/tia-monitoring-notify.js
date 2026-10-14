// "Notify Superior" buttons on the Superior Rating Monitoring page.
// Needs window.TIA = { urls: { notifySuperior }, csrf: { name, header, hash } } (set in the view).
(function () {
  var cfg = window.TIA || {};
  if (!cfg.urls || !cfg.urls.notifySuperior || !cfg.csrf) return;

  var alertBox = document.getElementById("notifyAlert");

  function show(message, ok) {
    if (!alertBox) return;
    alertBox.className = "alert " + (ok ? "alert-success" : "alert-danger");
    alertBox.textContent = message;
    alertBox.scrollIntoView({ behavior: "smooth", block: "nearest" });
  }

  // update every row of the same training: "Sent N times" + last sent
  function renderInfo(code, count, at) {
    document
      .querySelectorAll('.notify-cell[data-code="' + code + '"] .notify-info')
      .forEach(function (el) {
        el.innerHTML = "";

        var badge = document.createElement("span");
        badge.className = "badge bg-info text-dark";
        badge.innerHTML = '<i class="bi bi-envelope-check me-1"></i>';
        badge.appendChild(
          document.createTextNode(
            "Sent " + count + " time" + (count === 1 ? "" : "s"),
          ),
        );

        var last = document.createElement("div");
        last.className = "small text-muted";
        last.textContent = "Last: " + at;

        el.appendChild(badge);
        el.appendChild(last);
      });

    document
      .querySelectorAll('.notify-superior-btn[data-code="' + code + '"] .lbl')
      .forEach(function (l) {
        l.textContent = "Resend";
      });
  }

  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".notify-superior-btn");
    if (!btn || btn.disabled) return;

    var code = btn.getAttribute("data-code");
    var who = btn.getAttribute("data-superior") || "the superior";
    var title = btn.getAttribute("data-training") || "this training";
    if (
      !window.confirm(
        "Email " + who + ' to rate the participants of "' + title + '"?',
      )
    )
      return;

    var body = new FormData();
    body.append("training_code", code);
    body.append(cfg.csrf.name, cfg.csrf.hash);

    var headers = { "X-Requested-With": "XMLHttpRequest" };
    headers[cfg.csrf.header] = cfg.csrf.hash;

    var lbl = btn.querySelector(".lbl");
    var oldLabel = lbl.textContent;
    btn.disabled = true;
    lbl.textContent = "Sending…";

    fetch(cfg.urls.notifySuperior, {
      method: "POST",
      body: body,
      headers: headers,
      credentials: "same-origin",
    })
      .then(function (r) {
        return r.json().catch(function () {
          return { success: false, message: "Unexpected server response." };
        });
      })
      .then(function (res) {
        if (res.csrf) cfg.csrf.hash = res.csrf; // the server issues a fresh token each time

        show(
          res.message ||
            (res.success
              ? "Superior notified."
              : "Could not notify the superior."),
          !!res.success,
        );

        if (res.success && res.notify_count) {
          renderInfo(code, res.notify_count, res.notified_at);
        } else {
          lbl.textContent = res.success ? "Resend" : oldLabel;
        }
      })
      .catch(function () {
        show("Network error. Please try again.", false);
        lbl.textContent = oldLabel;
      })
      .finally(function () {
        btn.disabled = false;
      });
  });
})();
