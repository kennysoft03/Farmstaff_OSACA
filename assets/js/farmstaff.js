/* =====================================================
   FARMSTAFF REGISTRY — Main JS
   ===================================================== */
(function () {
  'use strict';

  // --- Desktop nav dropdowns (hover with delay so menu stays reachable) ---
  document.querySelectorAll('.nav-dropdown').forEach(function (dropdown) {
    var menu  = dropdown.querySelector('.nav-dropdown-menu');
    var timer = null;

    function openMenu()  {
      clearTimeout(timer);
      // Close any other open dropdowns first
      document.querySelectorAll('.nav-dropdown.open').forEach(function (d) {
        if (d !== dropdown) d.classList.remove('open');
      });
      dropdown.classList.add('open');
    }
    function closeMenu() {
      timer = setTimeout(function () {
        dropdown.classList.remove('open');
      }, 120); // 120ms grace period — enough time to move mouse into menu
    }

    dropdown.addEventListener('mouseenter', openMenu);
    dropdown.addEventListener('mouseleave', closeMenu);
    if (menu) {
      menu.addEventListener('mouseenter', function () { clearTimeout(timer); });
      menu.addEventListener('mouseleave', closeMenu);
    }

    // Also support click for keyboard / touch users
    var toggle = dropdown.querySelector('.dropdown-toggle');
    if (toggle) {
      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = dropdown.classList.contains('open');
        document.querySelectorAll('.nav-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
        if (!isOpen) dropdown.classList.add('open');
      });
    }
  });

  // Close dropdowns when clicking outside
  document.addEventListener('click', function () {
    document.querySelectorAll('.nav-dropdown.open').forEach(function (d) { d.classList.remove('open'); });
  });
  const navToggle = document.querySelector('.nav-toggle');
  const navMenu   = document.querySelector('.fs-navbar nav');
  const navActions= document.querySelector('.fs-navbar .nav-actions');
  if (navToggle) {
    navToggle.addEventListener('click', function () {
      navMenu   && navMenu.classList.toggle('open');
      navActions && navActions.classList.toggle('open');
    });
  }

  // --- Sidebar toggle (dashboard) ---
  const sidebarToggle  = document.querySelector('.sidebar-toggle');
  const sidebar        = document.querySelector('.sidebar');
  const sidebarOverlay = document.querySelector('.sidebar-overlay');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      sidebarOverlay && sidebarOverlay.classList.toggle('open');
    });
    sidebarOverlay && sidebarOverlay.addEventListener('click', function () {
      sidebar.classList.remove('open');
      sidebarOverlay.classList.remove('open');
    });
  }

  // --- Auto-dismiss alerts ---
  setTimeout(function () {
    document.querySelectorAll('.alert.auto-dismiss').forEach(function (el) {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    });
  }, 5000);

  // --- Star rating picker ---
  document.querySelectorAll('.star-rating').forEach(function (container) {
    const input   = container.closest('form').querySelector('[name="' + container.dataset.target + '"]');
    const stars   = container.querySelectorAll('.star-btn');
    let current   = input ? parseInt(input.value) || 0 : 0;

    function renderStars(val) {
      stars.forEach(function (s, i) {
        s.classList.toggle('active', i < val);
        s.textContent = i < val ? '★' : '☆';
      });
    }
    renderStars(current);

    stars.forEach(function (s, i) {
      s.addEventListener('mouseenter', function () { renderStars(i + 1); });
      s.addEventListener('mouseleave', function () { renderStars(current); });
      s.addEventListener('click', function () {
        current = i + 1;
        if (input) input.value = current;
        renderStars(current);
      });
    });
  });

  // --- Skills tag input ---
  const skillInput   = document.getElementById('skill-input');
  const skillTags    = document.getElementById('skill-tags');
  const skillHidden  = document.getElementById('skills-hidden');

  if (skillInput && skillTags) {
    let skills = [];

    function renderTags() {
      skillTags.innerHTML = '';
      skills.forEach(function (s, i) {
        const tag = document.createElement('span');
        tag.className = 'skill-tag';
        tag.innerHTML = s + ' <button type="button" data-i="' + i + '" style="background:none;border:none;cursor:pointer;color:inherit;font-weight:700;margin-left:4px;">&times;</button>';
        tag.querySelector('button').addEventListener('click', function () {
          skills.splice(parseInt(this.dataset.i), 1);
          renderTags();
        });
        skillTags.appendChild(tag);
      });
      if (skillHidden) skillHidden.value = JSON.stringify(skills);
      // Update hidden inputs
      const container = skillTags.closest('form');
      if (container) {
        container.querySelectorAll('input[name="skills[]"]').forEach(function (el) { el.remove(); });
        skills.forEach(function (s) {
          const inp = document.createElement('input');
          inp.type = 'hidden'; inp.name = 'skills[]'; inp.value = s;
          container.appendChild(inp);
        });
      }
    }

    skillInput.addEventListener('keydown', function (e) {
      if ((e.key === 'Enter' || e.key === ',') && this.value.trim()) {
        e.preventDefault();
        const v = this.value.trim().replace(/,/g, '');
        if (v && !skills.includes(v)) { skills.push(v); renderTags(); }
        this.value = '';
      }
    });

    document.getElementById('add-skill-btn') && document.getElementById('add-skill-btn').addEventListener('click', function () {
      const v = skillInput.value.trim();
      if (v && !skills.includes(v)) { skills.push(v); renderTags(); skillInput.value = ''; }
    });
  }

  // --- Confirm dialogs ---
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
  });

  // --- Attendance month nav ---
  const attNav = document.querySelector('.attendance-month-nav');
  if (attNav) {
    attNav.querySelectorAll('[data-month]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('month', this.dataset.month);
        url.searchParams.set('year', this.dataset.year);
        window.location.href = url.toString();
      });
    });
  }

  // --- Chart initialization (if Chart.js loaded) ---
  if (typeof Chart !== 'undefined') {
    // Workers/Employers by month
    const wbmCanvas = document.getElementById('workersChart');
    if (wbmCanvas) {
      const labels = JSON.parse(wbmCanvas.dataset.labels || '[]');
      const data   = JSON.parse(wbmCanvas.dataset.values || '[]');
      new Chart(wbmCanvas, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Workers Registered',
            data: data,
            borderColor: '#1a5c2a',
            backgroundColor: 'rgba(26,92,42,.1)',
            tension: .4, fill: true,
          }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
      });
    }

    const incidentCanvas = document.getElementById('incidentChart');
    if (incidentCanvas) {
      const labels = JSON.parse(incidentCanvas.dataset.labels || '[]');
      const data   = JSON.parse(incidentCanvas.dataset.values || '[]');
      new Chart(incidentCanvas, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{ data: data, backgroundColor: ['#1a5c2a','#d4a017','#dc3545','#0d6efd','#6c757d','#28a745','#fd7e14'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'right' } } }
      });
    }
  }

  // --- Print button ---
  document.querySelectorAll('[data-print]').forEach(function (btn) {
    btn.addEventListener('click', function () { window.print(); });
  });

  // --- Photo preview ---
  document.querySelectorAll('.photo-input').forEach(function (input) {
    input.addEventListener('change', function () {
      const preview = document.getElementById(this.dataset.preview);
      if (preview && this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          if (preview.tagName === 'IMG') preview.src = e.target.result;
          else preview.style.backgroundImage = 'url(' + e.target.result + ')';
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });

})();
