/**
 * Report pages (D-1, E-1, G, H, J): narrow the "Training" dropdown by
 * Year + Quarter + text search.
 *
 * Needs:
 *   #filterYear      <select>  value "" or "2026"
 *   #filterQuarter   <select>  value "" or "1".."4"
 *   #trainingSearch  <input>
 *   select[name="t"] with <option data-quarter="2026-Q1"> per training
 *                    (the "— Select a training —" option has no data-quarter)
 */
(function () {
  "use strict";

  var yearSel = document.getElementById("filterYear");
  var quarterSel = document.getElementById("filterQuarter");
  var search = document.getElementById("trainingSearch");
  var training = document.querySelector('select[name="t"]');

  if (!yearSel || !quarterSel || !search || !training) {
    console.warn(
      "tia-quarter-picker: #filterYear, #filterQuarter, #trainingSearch or select[name=t] not found",
    );
    return;
  }

  var options = Array.prototype.slice.call(
    training.querySelectorAll("option[data-quarter]"),
  );

  function apply() {
    var year = yearSel.value; // "" or "2026"
    var qtr = quarterSel.value; // "" or "1".."4"
    var text = search.value.trim().toLowerCase();

    options.forEach(function (opt) {
      var dq = opt.getAttribute("data-quarter") || ""; // "2026-Q1" or ""
      var okYear = !year || dq.slice(0, 4) === year;
      var okQtr = !qtr || dq.slice(-1) === qtr;
      var okText = !text || opt.textContent.toLowerCase().indexOf(text) > -1;

      // never hide the training that is currently open
      var show = (okYear && okQtr && okText) || opt.selected;

      opt.hidden = !show;
      opt.disabled = !show; // Safari ignores `hidden` on <option>
    });
  }

  yearSel.addEventListener("change", apply);
  quarterSel.addEventListener("change", apply);
  search.addEventListener("input", apply);

  apply();
})();
