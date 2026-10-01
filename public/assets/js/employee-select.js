/**
 * Searchable employee dropdown (Tom Select) for the "add participants" table.
 *
 * - Every <input class="new-name"> becomes a searchable dropdown fed by the
 *   `employees` table (works for rows that are added later, too).
 * - Each option shows the name, with position and email underneath.
 * - Picking an employee fills .new-position and .new-email in the same row and
 *   stores the id in row.dataset.employeeId.
 * - The original <input class="new-name"> keeps the NAME as its value, so any
 *   existing save code that reads `.new-name` .value keeps working.
 * - A name that is not in the list can still be typed (no employee id is set).
 *
 * Requires:  window.EMPLOYEE_LIST_URL = '<?= site_url('employees/all') ?>';
 * Tom Select is loaded from the jsDelivr CDN automatically if not on the page.
 */
(function () {
  "use strict";

  var LIST_URL = window.EMPLOYEE_LIST_URL;
  if (!LIST_URL) {
    console.warn("employee-select: set window.EMPLOYEE_LIST_URL first.");
    return;
  }

  var TS_VERSION = "2.3.1";
  var TS_JS =
    "https://cdn.jsdelivr.net/npm/tom-select@" +
    TS_VERSION +
    "/dist/js/tom-select.complete.min.js";
  var TS_CSS =
    "https://cdn.jsdelivr.net/npm/tom-select@" +
    TS_VERSION +
    "/dist/css/tom-select.bootstrap5.min.css";

  var employees = [];

  // ---------- 1) load Tom Select (only if the page doesn't have it) ----------
  var tsReady = new Promise(function (resolve, reject) {
    if (window.TomSelect) {
      resolve();
      return;
    }

    var link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = TS_CSS;
    document.head.appendChild(link);

    var s = document.createElement("script");
    s.src = TS_JS;
    s.onload = resolve;
    s.onerror = function () {
      reject(new Error("Could not load Tom Select"));
    };
    document.head.appendChild(s);
  });

  // ---------- 2) TIA styling (added after Tom Select's CSS so it wins) ----------
  var style = document.createElement("style");
  style.textContent =
    ".ts-wrapper.form-control,.ts-wrapper.form-control-sm{padding:0;border:0;background:transparent;box-shadow:none}" +
    ".ts-wrapper .ts-control{border:1px solid #e5e7eb;border-radius:6px;background:#fafafa;" +
    "font-size:.88rem;padding:8px 12px;box-shadow:none;min-height:38px}" +
    ".ts-wrapper.focus .ts-control{border-color:#9ca3af;background:#fff;box-shadow:none}" +
    ".ts-wrapper .ts-control input{font-size:.88rem;color:#111827}" +
    ".ts-dropdown{z-index:2000;border:1px solid #e5e7eb;border-radius:8px;" +
    "box-shadow:0 10px 30px rgba(0,0,0,.10);font-size:.88rem;margin-top:2px}" +
    ".ts-dropdown .option,.ts-dropdown .create,.ts-dropdown .no-results{padding:9px 14px}" +
    ".ts-dropdown .active{background:#f3f4f6;color:#111827}" +
    ".ts-emp-name{font-weight:600;color:#111827}" +
    ".ts-emp-meta{font-size:12px;color:#6b7280}" +
    ".ts-dropdown .no-results,.ts-dropdown .create{color:#6b7280}";
  document.head.appendChild(style);

  // ---------- 3) load employees ----------
  var dataReady = fetch(LIST_URL, {
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then(function (r) {
      return r.json();
    })
    .then(function (rows) {
      employees = rows.map(function (e) {
        return {
          id: String(e.id),
          employee_code: e.employee_code || "",
          name: e.name || "",
          email: e.email || "",
          position: e.position || "",
          custom: false,
        };
      });
    })
    .catch(function (err) {
      console.error("employee-select: could not load employees", err);
      employees = [];
    });

  // ---------- helpers ----------
  function setValue(el, value) {
    if (!el) return;
    el.value = value || "";
    el.dispatchEvent(new Event("input", { bubbles: true }));
    el.dispatchEvent(new Event("change", { bubbles: true }));
  }

  function initInput(input) {
    if (input.tomselect) return; // already converted

    var ts = new TomSelect(input, {
      valueField: "id",
      labelField: "name",
      searchField: ["name", "email", "position", "employee_code"],
      options: employees.map(function (e) {
        return Object.assign({}, e);
      }),
      maxItems: 1,
      maxOptions: 100,
      openOnFocus: true,
      selectOnTab: true,
      persist: false,
      createOnBlur: true,
      dropdownParent: "body", // not clipped by table-responsive / modals

      // allow a name that is not in the employees table
      create: function (text) {
        text = text.trim();
        return {
          id: text,
          name: text,
          email: "",
          position: "",
          employee_code: "",
          custom: true,
        };
      },
      createFilter: function (text) {
        return text.trim().length >= 2;
      },

      render: {
        option: function (d, esc) {
          var meta = [d.position, d.email].filter(Boolean).join(" \u00b7 ");
          return (
            '<div class="ts-emp"><div class="ts-emp-name">' +
            esc(d.name) +
            "</div>" +
            (meta ? '<div class="ts-emp-meta">' + esc(meta) + "</div>" : "") +
            "</div>"
          );
        },
        item: function (d, esc) {
          return "<div>" + esc(d.name) + "</div>";
        },
        option_create: function (d, esc) {
          return (
            '<div class="create">Use <strong>' +
            esc(d.input) +
            "</strong> (not in list)</div>"
          );
        },
        no_results: function () {
          return '<div class="no-results">No matching employee</div>';
        },
      },

      onChange: function (value) {
        var el = this.input;
        var row = el.closest("tr");
        var opt = value ? this.options[value] : null;

        if (!opt) {
          // cleared
          el.value = "";
          if (row) delete row.dataset.employeeId;
          return;
        }

        // keep the NAME in the original input (Tom Select would store the id)
        el.value = opt.name;

        if (!row) return;
        if (opt.custom) {
          // free-typed name: not linked to an employee
          delete row.dataset.employeeId;
          return;
        }
        setValue(row.querySelector(".new-position"), opt.position);
        setValue(row.querySelector(".new-email"), opt.email);
        row.dataset.employeeId = opt.id;
      },
    });

    // Tom Select copies the input's classes onto its wrapper; remove ".new-name"
    // so row.querySelector('.new-name') still returns the real <input>.
    ts.wrapper.classList.remove("new-name");
  }

  function attach(root) {
    var found = [];
    if (root.matches && root.matches("input.new-name")) found.push(root);
    if (root.querySelectorAll) {
      root.querySelectorAll("input.new-name").forEach(function (i) {
        found.push(i);
      });
    }
    found.forEach(initInput);
  }

  // ---------- start once Tom Select + employees are ready ----------
  Promise.all([tsReady, dataReady])
    .then(function () {
      attach(document);

      // rows added later (Add row button, paste, etc.)
      new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
          m.addedNodes.forEach(function (n) {
            if (n.nodeType === 1) attach(n);
          });
        });
      }).observe(document.body, { childList: true, subtree: true });
    })
    .catch(function (err) {
      console.error("employee-select:", err);
    });
})();
