/**
 * Searchable dropdown for "Name of Immediate Superior"
 * (Add Training + Edit Training modals).
 *
 *  - Loads names once from window.SUPERIORS_URL (route: superiors/all).
 *  - The field stays a normal text input named "immediate_superior",
 *    so you can still type a name that is not in the list.
 *  - Works with tia-dashboard.js filling the Edit field by code
 *    (it only sets .value; the list reads it when you focus or type).
 */
(function () {
    'use strict';

    var URL_ALL = window.SUPERIORS_URL;
    var names = null;      // cached, sorted list of names
    var loading = null;    // in-flight promise

    // ---------- styles ----------
    var css = document.createElement('style');
    css.textContent =
        '.ss-wrap{position:relative}' +
        '.ss-list{position:absolute;left:0;right:0;top:100%;z-index:1065;margin-top:2px;max-height:220px;overflow-y:auto;' +
        'background:#fff;border:1px solid #cbd5e1;border-radius:8px;box-shadow:0 8px 24px rgba(15,23,42,.15);display:none}' +
        '.ss-list.show{display:block}' +
        '.ss-item{padding:.45rem .75rem;font-size:.9rem;cursor:pointer;color:#1e293b}' +
        '.ss-item:hover,.ss-item.active{background:#dbeafe;color:#1e40af}' +
        '.ss-item mark{background:#fef08a;color:inherit;padding:0}' +
        '.ss-empty{padding:.55rem .75rem;font-size:.82rem;color:#64748b}' +
        '.ss-caret{position:absolute;right:.75rem;top:50%;transform:translateY(-50%);color:#64748b;pointer-events:none}';
    document.head.appendChild(css);

    // ---------- data ----------
    function pickName(item) {
        if (typeof item === 'string') return item;
        if (!item || typeof item !== 'object') return '';
        return item.name || item.fullname || item.full_name ||
            item.superior_name || item.immediate_superior || '';
    }

    function loadNames() {
        if (names) return Promise.resolve(names);
        if (loading) return loading;
        if (!URL_ALL) { names = []; return Promise.resolve(names); }

        loading = fetch(URL_ALL, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (r) { return r.ok ? r.json() : []; })
            .then(function (json) {
                var rows = Array.isArray(json) ? json : (json.data || json.superiors || []);
                var seen = {};
                names = rows.map(pickName)
                    .map(function (n) { return String(n).trim(); })
                    .filter(function (n) {
                        var k = n.toLowerCase();
                        if (!n || seen[k]) return false;
                        seen[k] = true;
                        return true;
                    })
                    .sort(function (a, b) { return a.localeCompare(b); });
                return names;
            })
            .catch(function () { names = []; return names; });   // plain text input still works

        return loading;
    }

    // ---------- helpers ----------
    function esc(s) {
        return s.replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function highlight(name, q) {
        if (!q) return esc(name);
        var i = name.toLowerCase().indexOf(q.toLowerCase());
        if (i < 0) return esc(name);
        return esc(name.slice(0, i)) + '<mark>' + esc(name.slice(i, i + q.length)) + '</mark>' + esc(name.slice(i + q.length));
    }

    // ---------- one combobox per input ----------
    function enhance(input) {
        if (input.dataset.ssReady) return;
        input.dataset.ssReady = '1';
        input.setAttribute('autocomplete', 'off');
        input.setAttribute('placeholder', input.getAttribute('placeholder') || 'Search or type a name...');

        var wrap = document.createElement('div');
        wrap.className = 'ss-wrap';
        input.parentNode.insertBefore(wrap, input);
        wrap.appendChild(input);
        input.style.paddingRight = '2rem';

        var caret = document.createElement('i');
        caret.className = 'bi bi-chevron-down ss-caret';
        wrap.appendChild(caret);

        var list = document.createElement('div');
        list.className = 'ss-list';
        list.setAttribute('role', 'listbox');
        wrap.appendChild(list);

        var shown = [];
        var active = -1;

        function close() { list.classList.remove('show'); active = -1; }

        function setActive(i) {
            var items = list.querySelectorAll('.ss-item');
            items.forEach(function (el) { el.classList.remove('active'); });
            if (i >= 0 && items[i]) {
                items[i].classList.add('active');
                items[i].scrollIntoView({ block: 'nearest' });
            }
            active = i;
        }

        function choose(name) {
            input.value = name;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            close();
        }

        function render() {
            loadNames().then(function (all) {
                var q = input.value.trim();
                shown = q
                    ? all.filter(function (n) { return n.toLowerCase().indexOf(q.toLowerCase()) !== -1; })
                    : all.slice();

                if (!all.length) {
                    list.innerHTML = '<div class="ss-empty">No saved superiors yet. Type a name.</div>';
                } else if (!shown.length) {
                    list.innerHTML = '<div class="ss-empty">No match. “' + esc(q) + '” will be used as typed.</div>';
                } else {
                    list.innerHTML = shown.map(function (n, i) {
                        return '<div class="ss-item" role="option" data-i="' + i + '">' + highlight(n, q) + '</div>';
                    }).join('');
                }
                list.classList.add('show');
                active = -1;
            });
        }

        // mousedown (not click) so it fires before the input loses focus
        list.addEventListener('mousedown', function (e) {
            var item = e.target.closest('.ss-item');
            if (!item) return;
            e.preventDefault();
            choose(shown[parseInt(item.dataset.i, 10)]);
        });

        input.addEventListener('focus', render);
        input.addEventListener('input', render);
        input.addEventListener('blur', function () { setTimeout(close, 120); });

        input.addEventListener('keydown', function (e) {
            var open = list.classList.contains('show');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!open) { render(); return; }
                setActive(Math.min(active + 1, shown.length - 1));
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                setActive(Math.max(active - 1, 0));
            } else if (e.key === 'Enter' && open && active >= 0) {
                e.preventDefault();          // do not submit the form
                choose(shown[active]);
            } else if (e.key === 'Escape' && open) {
                e.stopPropagation();         // do not close the modal
                close();
            }
        });

        // reopen on caret click
        caret.style.pointerEvents = 'auto';
        caret.style.cursor = 'pointer';
        caret.addEventListener('mousedown', function (e) {
            e.preventDefault();
            if (list.classList.contains('show')) { close(); } else { input.focus(); render(); }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll(
            '#addTrainingForm input[name="immediate_superior"], #editTrainingForm input[name="immediate_superior"]'
        ).forEach(enhance);
    });
})();
