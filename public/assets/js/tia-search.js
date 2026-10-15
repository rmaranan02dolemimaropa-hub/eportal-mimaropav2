/**
 * Trainings table filters: Year + Quarter + text search.
 *
 * Needs (in the Trainings view):
 *   #filterYear      <select>  value "" or "2026"
 *   #filterQuarter   <select>  value "" or "1".."4"
 *   #trainingSearch  <input>
 *   table rows:      <tr data-quarter="2026-Q1">   (empty when unknown)
 */
(function () {
  "use strict";

  var yearSel = document.getElementById("filterYear");
  var quarterSel = document.getElementById("filterQuarter");
  var search = document.getElementById("trainingSearch");

  if (!yearSel || !quarterSel || !search) {
    console.warn(
      "tia-search: #filterYear, #filterQuarter or #trainingSearch not found",
    );
    return;
  }

  var rows = document.querySelectorAll(".tia-table tbody tr[data-quarter]");

  function apply() {
    var year = yearSel.value; // "" or "2026"
    var qtr = quarterSel.value; // "" or "1".."4"
    var text = search.value.trim().toLowerCase();

    rows.forEach(function (tr) {
      var dq = tr.getAttribute("data-quarter") || ""; // "2026-Q1" or ""
      var okYear = !year || dq.slice(0, 4) === year;
      var okQtr = !qtr || dq.slice(-1) === qtr;
      var okText = !text || tr.textContent.toLowerCase().indexOf(text) > -1;

      tr.style.display = okYear && okQtr && okText ? "" : "none";
    });
  }

  yearSel.addEventListener("change", apply);
  quarterSel.addEventListener("change", apply);
  search.addEventListener("input", apply);

  apply();
})();
