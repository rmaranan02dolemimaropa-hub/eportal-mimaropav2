(function () {
  var lastEditBtn = null;

  function fillEdit() {
    var list = [];
    if (lastEditBtn) {
      try {
        list = JSON.parse(lastEditBtn.dataset.competencies || "[]");
      } catch (_) {}
    }
    document
      .querySelectorAll("#edit_competencies input")
      .forEach(function (inp, i) {
        inp.value = list[i] || "";
      });
  }

  // Remember which training's Edit button was clicked
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".edit-training-btn");
    if (!btn) return;
    lastEditBtn = btn;
    fillEdit();
  });

  // Fill again once the modal is open, in case tia-dashboard.js resets the form when it opens
  var editModal = document.getElementById("editTrainingModal");
  if (editModal) {
    editModal.addEventListener("shown.bs.modal", fillEdit);
  }

  // Clear the Add modal each time it opens
  var addModal = document.getElementById("addTrainingModal");
  if (addModal) {
    addModal.addEventListener("show.bs.modal", function () {
      document
        .querySelectorAll("#add_competencies input")
        .forEach(function (i) {
          i.value = "";
        });
    });
  }
})();
