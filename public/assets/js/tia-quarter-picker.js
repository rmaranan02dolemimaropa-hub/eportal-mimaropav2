// Quarter filter + search for the training picker (report pages).
//  - #quarterFilter narrows <select name="t"> by each option's data-quarter (e.g. "2026-Q1")
//  - #trainingSearch narrows it by text (title, dates, respondent count)
// Both work together. Enter in the search box opens the report when exactly one training matches.
(function () {
  var quarter = document.getElementById("quarterFilter");
  var search = document.getElementById("trainingSearch");
  var select = document.querySelector('select[name="t"]');
  if (!select || (!quarter && !search)) return;

  // remember every option once, so filtering never loses any
  var all = Array.prototype.map.call(select.options, function (o) {
    return {
      value: o.value,
      text: o.text,
      lower: o.text.toLowerCase(),
      quarter: o.getAttribute("data-quarter") || "",
    };
  });

  function render() {
    var q = quarter ? quarter.value : "";
    var term = search ? search.value.trim().toLowerCase() : "";
    var current = select.value;
    var matches = 0;

    select.innerHTML = "";

    all.forEach(function (o) {
      var isPlaceholder = o.value === "";
      if (!isPlaceholder) {
        if (q !== "" && o.quarter !== q) return;
        if (term !== "" && o.lower.indexOf(term) === -1) return;
      }

      var opt = document.createElement("option");
      opt.value = o.value;
      opt.textContent = o.text;
      opt.setAttribute("data-quarter", o.quarter);
      if (o.value === current) opt.selected = true;
      select.appendChild(opt);
      if (!isPlaceholder) matches++;
    });

    if (matches === 0) {
      var none = document.createElement("option");
      none.value = "";
      none.disabled = true;
      none.textContent = "No trainings found";
      select.appendChild(none);
    }
  }

  if (quarter) quarter.addEventListener("change", render);

  if (search) {
    search.addEventListener("input", render);

    // Enter: don't submit an empty form; open the report if only one training matches
    search.addEventListener("keydown", function (e) {
      if (e.key !== "Enter") return;
      e.preventDefault();

      var found = Array.prototype.filter.call(select.options, function (o) {
        return o.value !== "";
      });
      if (found.length === 1) {
        select.value = found[0].value;
        select.form.submit();
      }
    });
  }
})();
