<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'mark_read') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'mark_unread') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("UPDATE contacts SET is_read = 0 WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['success' => false]);
    exit;
}

$contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contacts — NexusSynced Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
<style>
tr.unread td { font-weight:600; }
tr.unread td:first-child { border-left:3px solid #1E2946; }
</style>
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main">
  <div class="main-header">
    <div>
      <h1>Contacts</h1>
      <div class="subtitle">Messages from the contact form</div>
    </div>
  </div>

  <div class="admin-section">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Company</th>
            <th>Message</th>
            <th>Date</th>
            <th>Read</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($contacts as $row): ?>
          <tr class="<?php echo !$row['is_read'] ? 'unread' : ''; ?>">
            <td><?php echo sanitize($row['name']); ?></td>
            <td><a href="mailto:<?php echo sanitize($row['email']); ?>" style="color:#1E2946;"><?php echo sanitize($row['email']); ?></a></td>
            <td><?php echo sanitize($row['company'] ?: '-'); ?></td>
            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;" title="<?php echo sanitize($row['message']); ?>">
              <?php echo sanitize(mb_strimwidth($row['message'], 0, 80, '...')); ?>
            </td>
            <td style="white-space:nowrap;font-size:0.75rem;"><?php echo date('M j, g:ia', strtotime($row['created_at'])); ?></td>
            <td>
              <?php if ($row['is_read']): ?>
              <span class="pub-badge pub-yes">Read</span>
              <?php else: ?>
              <span class="pub-badge pub-no">New</span>
              <?php endif; ?>
            </td>
            <td class="actions">
              <button class="btn-sm" onclick="viewContact(<?php echo $row['id']; ?>, '<?php echo sanitize(addslashes($row['name'])); ?>', '<?php echo sanitize(addslashes($row['email'])); ?>', '<?php echo sanitize(addslashes($row['company'])); ?>', '<?php echo sanitize(addslashes($row['message'])); ?>', '<?php echo $row['created_at']; ?>')">View</button>
              <?php if (!$row['is_read']): ?>
              <button class="btn-sm success" onclick="markRead(<?php echo $row['id']; ?>)">Mark Read</button>
              <?php else: ?>
              <button class="btn-sm warning" onclick="markUnread(<?php echo $row['id']; ?>)">Unread</button>
              <?php endif; ?>
              <button class="btn-sm danger confirm-delete" onclick="deleteContact(<?php echo $row['id']; ?>)">Delete</button>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($contacts)): ?>
          <tr><td colspan="7" style="color:#9AA5BC;text-align:center;padding:2rem;">No messages yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div id="view-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;justify-content:center;align-items:center;">
  <div style="background:#fff;border-radius:16px;padding:2rem;max-width:600px;width:90%;">
    <div id="view-content"></div>
    <button class="btn-sm" style="margin-top:1rem;" onclick="document.getElementById('view-modal').style.display='none'">Close</button>
  </div>
</div>

<script src="/assets/js/admin.js"></script>
<script>
function viewContact(id, name, email, company, message, date) {
  var html = '<h2 style="margin-bottom:1rem;">Message from ' + name + '</h2>';
  html += '<div style="margin-bottom:1rem;"><strong>Name:</strong> ' + name + '<br><strong>Email:</strong> <a href="mailto:' + email + '">' + email + '</a><br><strong>Company:</strong> ' + (company || 'N/A') + '<br><strong>Date:</strong> ' + date + '</div>';
  html += '<div style="padding:1rem;background:#F4F6FB;border-radius:8px;line-height:1.6;white-space:pre-wrap;">' + message + '</div>';
  document.getElementById('view-content').innerHTML = html;
  document.getElementById('view-modal').style.display = 'flex';
  // Auto mark as read
  markRead(id);
}

function markRead(id) {
  fetch('/admin/contacts.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=mark_read&id=' + id
  })
  .then(r => r.json())
  .then(d => { if (d.success) location.reload(); });
}

function markUnread(id) {
  fetch('/admin/contacts.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=mark_unread&id=' + id
  })
  .then(r => r.json())
  .then(d => { if (d.success) location.reload(); });
}

function deleteContact(id) {
  if (!confirm('Delete this message?')) return;
  fetch('/admin/contacts.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=delete&id=' + id
  })
  .then(r => r.json())
  .then(d => { if (d.success) location.reload(); });
}
</script>
</body>
</html>
