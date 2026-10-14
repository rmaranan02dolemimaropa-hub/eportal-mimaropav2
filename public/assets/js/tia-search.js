// Live search + quarter filter for the TIA tables (Trainings, Superior / Participant monitoring).
// Search matches every column except the first (#) and the last (Actions).
// Quarter filter uses each row's data-quarter (e.g. "2026-Q1").
(function () {
  var input = document.getElementById("trainingSearch");
  var quarter = document.getElementById("quarterFilter");
  var table = document.querySelector(".tia-table");
  if (!table || !table.tBodies.length || (!input && !quarter)) return;

  var body = table.tBodies[0];

  // real data rows only (skips the "nothing found" row)
  var rows = Array.prototype.filter.call(body.rows, function (r) {
    return !r.querySelector("td[colspan]");
  });
  if (!rows.length) return;

  // shown when nothing matches
  var cols =
    table.tHead && table.tHead.rows.length
      ? table.tHead.rows[0].cells.length
      : 8;
  var empty = document.createElement("tr");
  empty.className = "d-none";
  empty.innerHTML =
    '<td colspan="' +
    cols +
    '" class="text-center text-muted py-4">No results match your filters.</td>';
  body.appendChild(empty);

  // text of every column except # (first) and Actions (last)
  function rowText(r) {
    return Array.prototype.slice
      .call(r.cells, 1, -1)
      .map(function (c) {
        return c.textContent;
      })
      .join(" ")
      .replace(/\s+/g, " ")
      .toLowerCase();
  }
  var texts = rows.map(rowText);

  function run() {
    var q = input ? input.value.trim().toLowerCase() : "";
    var qtr = quarter ? quarter.value : "";
    var shown = 0;

    rows.forEach(function (r, i) {
      var matchText = q === "" || texts[i].indexOf(q) !== -1;
      var matchQtr = qtr === "" || r.getAttribute("data-quarter") === qtr;
      var match = matchText && matchQtr;

      r.classList.toggle("d-none", !match);
      if (match) {
        shown++;
        r.cells[0].textContent = shown; // keep the # column in order
      }
    });

    empty.classList.toggle("d-none", shown !== 0);
  }

  if (input) input.addEventListener("input", run);
  if (quarter) quarter.addEventListener("change", run);
})();
