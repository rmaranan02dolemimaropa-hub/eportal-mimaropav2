<!-- Bootstrap Bundle JS (load first, so anything depending on it later is safe) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ==========================================================================
   DOLE MIMAROPA E-Portal — Dashboard Script
   Handles: current date display, animated stat counters, table search filter.
   No external dependencies beyond the Bootstrap bundle already loaded.
   ========================================================================== */

document.addEventListener("DOMContentLoaded", function () {
  setCurrentDate();
  animateStatCounters();
  enableTableSearch();
});

/**
 * Displays today's date in a readable, localized format
 * inside the #currentDate element in the page header.
 */
function setCurrentDate() {
  const dateEl = document.getElementById("currentDate");
  if (!dateEl) return;

  const today = new Date();
  const options = { weekday: "long", year: "numeric", month: "long", day: "numeric" };
  dateEl.textContent = today.toLocaleDateString("en-US", options);
}

/**
 * Animates each .stat-number element from 0 up to its data-target value.
 * Uses requestAnimationFrame for smooth, performant counting.
 */
function animateStatCounters() {
  const counters = document.querySelectorAll(".stat-number");
  const duration = 900; // milliseconds

  counters.forEach(function (counter) {
    const target = parseInt(counter.getAttribute("data-target"), 10) || 0;
    const startTime = performance.now();

    function tick(now) {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      // Ease-out for a smoother finish
      const eased = 1 - Math.pow(1 - progress, 3);
      const current = Math.round(eased * target);

      counter.textContent = current.toLocaleString("en-US");

      if (progress < 1) {
        requestAnimationFrame(tick);
      } else {
        counter.textContent = target.toLocaleString("en-US");
      }
    }

    requestAnimationFrame(tick);
  });
}

/**
 * Filters visible rows of the Recent Service Requests table
 * as the user types in the search box. Matches against
 * reference number, requestor name, and service name.
 */
function enableTableSearch() {
  const searchInput = document.querySelector(".search-box input");
  const tableRows = document.querySelectorAll(".requests-table tbody tr");

  if (!searchInput || !tableRows.length) return;

  searchInput.addEventListener("input", function () {
    const query = searchInput.value.trim().toLowerCase();

    tableRows.forEach(function (row) {
      const rowText = row.textContent.toLowerCase();
      row.style.display = rowText.includes(query) ? "" : "none";
    });
  });
}
</script>

