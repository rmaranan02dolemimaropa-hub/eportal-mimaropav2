/* ==========================================================================
   DOLE MIMAROPA E-PORTAL — SCRIPT
   Vanilla JavaScript only, no dependencies beyond Bootstrap's own JS bundle.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------- Loading fade ---------------- */
  document.body.classList.add('is-loading');
  window.addEventListener('load', function () {
    setTimeout(function () {
      document.body.classList.remove('is-loading');
    }, 300);
  });

  /* ---------------- Navbar scroll state ---------------- */
  var navbar = document.getElementById('mainNavbar');
  function updateNavbarState() {
    if (!navbar) return;
    if (window.scrollY > 40) {
      navbar.classList.add('is-scrolled');
    } else {
      navbar.classList.remove('is-scrolled');
    }
  }
  updateNavbarState();
  window.addEventListener('scroll', updateNavbarState, { passive: true });

  /* Close mobile menu after a link is tapped */
  var navCollapseEl = document.getElementById('navMain');
  if (navCollapseEl && window.bootstrap) {
    var bsCollapse = new bootstrap.Collapse(navCollapseEl, { toggle: false });
    navCollapseEl.querySelectorAll('.nav-link-gov, .btn-login').forEach(function (link) {
      link.addEventListener('click', function () {
        if (navCollapseEl.classList.contains('show')) {
          bsCollapse.hide();
        }
      });
    });
  }

  /* Highlight active nav link based on section in view */
  var sections = document.querySelectorAll('main section[id]');
  var navLinks = document.querySelectorAll('.nav-link-gov');
  function setActiveLink() {
    var scrollPos = window.scrollY + 140;
    sections.forEach(function (sec) {
      var top = sec.offsetTop;
      var bottom = top + sec.offsetHeight;
      var id = sec.getAttribute('id');
      var link = document.querySelector('.nav-link-gov[href="#' + id + '"]');
      if (!link) return;
      if (scrollPos >= top && scrollPos < bottom) {
        navLinks.forEach(function (l) { l.classList.remove('active'); });
        link.classList.add('active');
      }
    });
  }
  window.addEventListener('scroll', setActiveLink, { passive: true });
  setActiveLink();

  /* ---------------- Hero particles (generated) ---------------- */
  var particleField = document.querySelector('.hero-particles');
  if (particleField && !prefersReducedMotion) {
    var particleCount = window.innerWidth < 768 ? 12 : 26;
    for (var i = 0; i < particleCount; i++) {
      var p = document.createElement('span');
      p.className = 'particle';
      p.style.left = Math.random() * 100 + '%';
      p.style.bottom = (Math.random() * 20) + '%';
      p.style.animationDuration = (6 + Math.random() * 8) + 's';
      p.style.animationDelay = (Math.random() * 8) + 's';
      p.style.opacity = (0.3 + Math.random() * 0.5).toFixed(2);
      particleField.appendChild(p);
    }
  }

  /* ---------------- Mouse parallax on hero visual ---------------- */
  var heroSection = document.querySelector('.hero');
  var heroVisual = document.querySelector('.hero-visual .visual-wrap');
  var heroBlobs = document.querySelectorAll('.hero-blob');
  if (heroSection && !prefersReducedMotion) {
    heroSection.addEventListener('mousemove', function (e) {
      var rect = heroSection.getBoundingClientRect();
      var relX = (e.clientX - rect.left) / rect.width - 0.5;
      var relY = (e.clientY - rect.top) / rect.height - 0.5;

      if (heroVisual) {
        heroVisual.style.transform = 'translate(' + (relX * 18) + 'px,' + (relY * 18) + 'px)';
      }
      heroBlobs.forEach(function (blob, idx) {
        var depth = (idx + 1) * 6;
        blob.style.transform = 'translate(' + (relX * depth) + 'px,' + (relY * depth) + 'px)';
      });
    });
    heroSection.addEventListener('mouseleave', function () {
      if (heroVisual) heroVisual.style.transform = '';
      heroBlobs.forEach(function (blob) { blob.style.transform = ''; });
    });
  }

  /* ---------------- Scroll-reveal (IntersectionObserver) ---------------- */
  var revealEls = document.querySelectorAll('[data-aos]');
  if ('IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

    revealEls.forEach(function (el) { revealObserver.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in-view'); });
  }

  /* ---------------- Animated counters ---------------- */
  var counters = document.querySelectorAll('[data-counter]');
  function animateCounter(el) {
    var target = parseInt(el.getAttribute('data-counter'), 10) || 0;
    var duration = 1800;
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var value = Math.floor(eased * target);
      el.textContent = value.toLocaleString();
      if (progress < 1) {
        window.requestAnimationFrame(step);
      } else {
        el.textContent = target.toLocaleString();
      }
    }
    window.requestAnimationFrame(step);
  }

  if (counters.length && 'IntersectionObserver' in window) {
    var counterObserver = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    counters.forEach(function (el) { counterObserver.observe(el); });
  } else {
    counters.forEach(function (el) { el.textContent = el.getAttribute('data-counter'); });
  }

  /* ---------------- Ripple effect on .btn-gov ---------------- */
  document.querySelectorAll('.btn-gov').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      var rect = btn.getBoundingClientRect();
      var ripple = document.createElement('span');
      var size = Math.max(rect.width, rect.height);
      ripple.className = 'ripple';
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
      ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
      btn.appendChild(ripple);
      window.setTimeout(function () { ripple.remove(); }, 650);
    });
  });

  /* ---------------- Back to top button ---------------- */
  var backToTop = document.getElementById('backToTop');
  if (backToTop) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 500) {
        backToTop.classList.add('is-visible');
      } else {
        backToTop.classList.remove('is-visible');
      }
    }, { passive: true });

    backToTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
    });
  }

  /* ---------------- HR service request buttons ---------------- */
  document.querySelectorAll('.btn-request').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var service = btn.getAttribute('data-service') || 'this service';
      alert('Your request for "' + service + '" will open the secure request form once the E-Portal login is connected.');
    });
  });

  /* Login button placeholder */
  document.querySelectorAll('.js-login-trigger').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      alert('Employee login is coming soon. This will connect to the DOLE MIMAROPA staff authentication system.');
    });
  });

  /* Current year in footer */
  var yearEl = document.getElementById('currentYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();
});
