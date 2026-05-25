/* NexusSynced Main JS — Mode Toggle, Queue Polling, UI Interactions */

const DEVIL_KEY = 'nexus_mode';
const isDevilPage = document.body.classList.contains('devil');

// ===== MODE TOGGLE =====
function toggleMode() {
  if (isDevilPage) {
    localStorage.setItem(DEVIL_KEY, 'professional');
    window.location.href = 'index.php';
  } else {
    localStorage.setItem(DEVIL_KEY, 'devil');
    window.location.href = 'devil.php';
  }
}

document.getElementById('mode-toggle-btn').addEventListener('click', function(e) {
  e.preventDefault();
  const overlay = document.createElement('div');
  overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:#000;z-index:9999;opacity:0;transition:opacity 0.3s';
  document.body.appendChild(overlay);
  requestAnimationFrame(function() {
    overlay.style.opacity = '1';
  });
  setTimeout(function() { toggleMode(); }, 350);
});

// Check localStorage and redirect if coming from other mode
(function() {
  const saved = localStorage.getItem(DEVIL_KEY);
  if (saved === 'devil' && !isDevilPage && window.location.pathname !== '/devil.php') {
    // Only redirect on initial page load, not on every page
  }
})();

// ===== HAMBURGER MENU =====
document.addEventListener('DOMContentLoaded', function() {
  const hamburger = document.getElementById('hamburger');
  const navLinks = document.getElementById('nav-links');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', function() {
      navLinks.classList.toggle('open');
      const spans = hamburger.querySelectorAll('span');
      if (navLinks.classList.contains('open')) {
        spans[0].style.transform = 'rotate(45deg) translate(5px,5px)';
        spans[1].style.opacity = '0';
        spans[2].style.transform = 'rotate(-45deg) translate(5px,-5px)';
      } else {
        spans[0].style.transform = '';
        spans[1].style.opacity = '';
        spans[2].style.transform = '';
      }
    });
  }
});

// ===== QUEUE POLLING (Devil Mode) =====
let queueInterval = null;

function renderQueue(data) {
  const board = document.getElementById('queue-board');
  if (!board) return;

  const columns = {
    submitted: { title: 'Submitted', css: 'status-submitted' },
    building: { title: 'Building Now', css: 'status-building' },
    done: { title: 'Done — Available', css: 'status-done' },
    sold: { title: 'Sold', css: 'status-sold' }
  };

  let html = '';
  for (const [key, col] of Object.entries(columns)) {
    html += '<div class="queue-col ' + col.css + '">';
    html += '<div class="queue-col-header"><div class="status-dot"></div><span class="queue-col-title">' + col.title + '</span></div>';

    if (data.submissions) {
      data.submissions.filter(function(s) { return s.status === key; }).forEach(function(s) {
        let typeLabel = 'WEB';
        let badgeClass = 'badge-web';
        if (s.submission_type === 'worst_app') { typeLabel = 'WORST'; badgeClass = 'badge-ai'; }
        else if (s.package && s.package !== '') {
          if (s.package === 'mobile') { typeLabel = 'MOBILE'; badgeClass = 'badge-mobile'; }
          else if (s.package === 'ai' || s.package === 'raise') { typeLabel = 'AI'; badgeClass = 'badge-ai'; }
        }

        html += '<div class="queue-item">';
        html += '<div class="qi-id mono">#' + (s.queue_number || s.id) + (s.submission_type === 'worst_app' ? ' ← WORST' : '') + '</div>';
        html += '<div class="qi-name syne">' + s.idea_title + '</div>';
        html += '<div class="qi-type">' + (s.idea_description ? s.idea_description.substring(0, 40) + (s.idea_description.length > 40 ? '...' : '') : '') + '</div>';
        html += '<span class="qi-badge ' + badgeClass + '">' + typeLabel + '</span>';
        if (key === 'building') {
          html += '<div class="building-bar"><div class="building-bar-fill"></div></div>';
        }
        html += '</div>';
      });
    }

    html += '</div>';
  }
  board.innerHTML = html;

  // Update MVP counter if present
  const counterEl = document.getElementById('mvp-counter');
  if (counterEl && data.mvps_count) {
    counterEl.textContent = data.mvps_count;
  }
}

function fetchQueueData() {
  fetch('/api/queue-data.php', { cache: 'no-store' })
    .then(function(r) { return r.json(); })
    .then(function(data) { renderQueue(data); })
    .catch(function() { /* silent fail, will retry */ });
}

function startQueuePolling() {
  if (document.getElementById('queue-board')) {
    fetchQueueData();
    queueInterval = setInterval(fetchQueueData, 30000);
  }
}

function stopQueuePolling() {
  if (queueInterval) {
    clearInterval(queueInterval);
    queueInterval = null;
  }
}

// Page Visibility API — pause polling when tab hidden
document.addEventListener('visibilitychange', function() {
  if (document.hidden) {
    stopQueuePolling();
  } else {
    startQueuePolling();
  }
});

// ===== FORM HANDLING =====

// Radio button toggle for idea type
function setRadio(id) {
  document.querySelectorAll('.radio-btn').forEach(function(b) { b.classList.remove('active'); });
  var el = document.getElementById(id);
  if (el) el.classList.add('active');

  // Show/hide package dropdown based on idea type
  var pkgGroup = document.getElementById('package-group');
  if (pkgGroup) {
    if (id === 'r2') { // Worst App
      pkgGroup.style.display = 'none';
    } else {
      pkgGroup.style.display = 'block';
    }
  }
}

// Submit idea form
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('idea-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(form);
      var btn = form.querySelector('button[type="submit"]');
      var msg = form.querySelector('.form-message');
      btn.disabled = true;
      btn.textContent = 'SUBMITTING...';

      fetch('/api/submit-idea.php', {
        method: 'POST',
        body: formData
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          if (msg) {
            msg.className = 'form-message success';
            msg.textContent = "You're #" + data.queue_number + " in the queue. We'll be in touch within 24 hours.";
            msg.style.display = 'block';
          }
          form.reset();
          document.querySelectorAll('.radio-btn').forEach(function(b) { b.classList.remove('active'); });
          var r1 = document.getElementById('r1');
          if (r1) r1.classList.add('active');
          // Refresh queue
          fetchQueueData();
        } else {
          if (msg) {
            msg.className = 'form-message error';
            msg.textContent = data.error || 'Something went wrong. Please try again.';
            msg.style.display = 'block';
          }
        }
        btn.disabled = false;
        btn.textContent = 'JOIN THE QUEUE →';
      })
      .catch(function() {
        if (msg) {
          msg.className = 'form-message error';
          msg.textContent = 'Network error. Please try again.';
          msg.style.display = 'block';
        }
        btn.disabled = false;
        btn.textContent = 'JOIN THE QUEUE →';
      });
    });
  }

  // Contact form
  var contactForm = document.getElementById('contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(contactForm);
      var btn = contactForm.querySelector('button[type="submit"]');
      var msg = contactForm.querySelector('.form-message');
      btn.disabled = true;
      btn.textContent = 'SENDING...';

      fetch('/api/contact.php', {
        method: 'POST',
        body: formData
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          if (msg) {
            msg.className = 'form-message success';
            msg.textContent = 'Message sent! We\'ll get back to you within 24 hours.';
            msg.style.display = 'block';
          }
          contactForm.reset();
        } else {
          if (msg) {
            msg.className = 'form-message error';
            msg.textContent = data.error || 'Something went wrong.';
            msg.style.display = 'block';
          }
        }
        btn.disabled = false;
        btn.textContent = 'SEND MESSAGE';
      })
      .catch(function() {
        if (msg) {
          msg.className = 'form-message error';
          msg.textContent = 'Network error. Please try again.';
          msg.style.display = 'block';
        }
        btn.disabled = false;
        btn.textContent = 'SEND MESSAGE';
      });
    });
  }

  // Portfolio filter
  var filterBtns = document.querySelectorAll('.portfolio-filters button');
  if (filterBtns.length) {
    filterBtns.forEach(function(btn) {
      btn.addEventListener('click', function() {
        filterBtns.forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
        var cat = this.dataset.filter || 'all';
        document.querySelectorAll('.portfolio-card').forEach(function(card) {
          if (cat === 'all' || card.dataset.category === cat) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(function(a) {
    a.addEventListener('click', function(e) {
      var target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });

  // Start queue polling on devil mode pages
  startQueuePolling();
});
