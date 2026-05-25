/* NexusSynced Admin Panel JS */

document.addEventListener('DOMContentLoaded', function() {

  // Sidebar active link
  const links = document.querySelectorAll('.sidebar nav a');
  const currentPath = window.location.pathname;
  links.forEach(a => {
    if (a.getAttribute('href') === currentPath) {
      a.classList.add('active');
    }
  });

  // Confirm delete on any .confirm-delete button
  document.querySelectorAll('.confirm-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
      if (!confirm('Are you sure you want to delete this item? This cannot be undone.')) {
        e.preventDefault();
      }
    });
  });

  // Status dropdown inline update via fetch
  document.querySelectorAll('.status-update').forEach(select => {
    select.addEventListener('change', function() {
      const row = this.closest('tr');
      const id = row ? row.dataset.id : null;
      if (!id) return;
      const status = this.value;
      const type = this.dataset.type || 'submission';
      const btn = row.querySelector('.save-status');
      if (btn) btn.style.display = 'inline-block';
    });
  });

  // Save status buttons
  document.querySelectorAll('.save-status').forEach(btn => {
    btn.addEventListener('click', function() {
      const row = this.closest('tr');
      const id = row ? row.dataset.id : null;
      if (!id) return;
      const select = row.querySelector('.status-update');
      const status = select ? select.value : null;
      if (!status) return;
      const type = select ? select.dataset.type || 'submission' : 'submission';

      this.disabled = true;
      this.textContent = 'Saving...';

      let url = '';
      let body = {};
      if (type === 'submission') {
        url = '/admin/submissions.php';
        body = { action: 'update_status', id: id, status: status };
      } else if (type === 'worst_app') {
        url = '/admin/worst-apps.php';
        body = { action: 'update_status', id: id, status: status };
      }

      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(body)
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          this.textContent = 'Saved';
          this.style.background = '#28a745';
          setTimeout(() => {
            this.disabled = false;
            this.textContent = 'Save';
            this.style.background = '';
          }, 1500);
        } else {
          alert('Error saving status.');
          this.disabled = false;
          this.textContent = 'Save';
        }
      })
      .catch(() => {
        alert('Request failed.');
        this.disabled = false;
        this.textContent = 'Save';
      });
    });
  });

  // Toggle public/private checkbox
  document.querySelectorAll('.toggle-public').forEach(cb => {
    cb.addEventListener('change', function() {
      const row = this.closest('tr');
      const id = row ? row.dataset.id : null;
      if (!id) return;
      const isPublic = this.checked ? 1 : 0;
      const type = this.dataset.type || 'submission';

      let url = '';
      if (type === 'submission') url = '/admin/submissions.php';
      else if (type === 'worst_app') url = '/admin/worst-apps.php';

      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'toggle_public', id: id, is_public: isPublic })
      });
    });
  });

  // Search/filter submissions table
  const searchInput = document.getElementById('search-input');
  if (searchInput) {
    searchInput.addEventListener('keyup', function() {
      const q = this.value.toLowerCase();
      document.querySelectorAll('tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // Filter buttons
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const filter = this.dataset.filter || 'all';
      document.querySelectorAll('tbody tr').forEach(row => {
        if (filter === 'all') { row.style.display = ''; return; }
        const type = row.dataset.type || '';
        const status = row.dataset.status || '';
        row.style.display = (type === filter || status === filter) ? '' : 'none';
      });
    });
  });
});
