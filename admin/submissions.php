<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if ($id && in_array($status, ['submitted', 'building', 'done', 'sold', 'rejected'])) {
            $pdo->prepare("UPDATE submissions SET status = ? WHERE id = ?")->execute([$status, $id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'toggle_public') {
        $id = (int) ($_POST['id'] ?? 0);
        $isPublic = (int) ($_POST['is_public'] ?? 0);
        if ($id) {
            $pdo->prepare("UPDATE submissions SET is_public = ? WHERE id = ?")->execute([$isPublic, $id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'mark_sold') {
        $id = (int) ($_POST['id'] ?? 0);
        $price = (float) ($_POST['price'] ?? 0);
        if ($id && $price > 0) {
            $pdo->prepare("UPDATE submissions SET status = 'sold', sale_price = ?, sold_at = NOW() WHERE id = ?")->execute([$price, $id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'update_notes') {
        $id = (int) ($_POST['id'] ?? 0);
        $notes = $_POST['notes'] ?? '';
        if ($id) {
            $pdo->prepare("UPDATE submissions SET admin_notes = ? WHERE id = ?")->execute([$notes, $id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM submissions WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'get_details') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM submissions WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            echo json_encode($row ?: []);
            exit;
        }
    }

    echo json_encode(['success' => false]);
    exit;
}

// Get all submissions
$submissions = $pdo->query("SELECT * FROM submissions ORDER BY id DESC")->fetchAll();
$statuses = ['submitted', 'building', 'done', 'sold', 'rejected'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submissions — NexusSynced Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main">
  <div class="main-header">
    <div>
      <h1>Submissions</h1>
      <div class="subtitle">All idea submissions — <?php echo count($submissions); ?> total</div>
    </div>
  </div>

  <div class="admin-section">
    <div class="filters">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="mvp">MVP</button>
      <button class="filter-btn" data-filter="worst_app">Worst App</button>
      <button class="filter-btn" data-filter="submitted">Submitted</button>
      <button class="filter-btn" data-filter="building">Building</button>
      <button class="filter-btn" data-filter="done">Done</button>
      <button class="filter-btn" data-filter="sold">Sold</button>
    </div>

    <div class="search-bar">
      <input type="text" id="search-input" placeholder="Search by name or idea title...">
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Queue</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Type</th>
            <th>Idea</th>
            <th>Package</th>
            <th>Status</th>
            <th>Public</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($submissions as $row): ?>
          <tr data-id="<?php echo $row['id']; ?>" data-type="<?php echo $row['submission_type']; ?>" data-status="<?php echo $row['status']; ?>">
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['queue_number'] ? '#' . $row['queue_number'] : '-'; ?></td>
            <td><?php echo sanitize($row['name']); ?></td>
            <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;"><?php echo sanitize($row['contact']); ?></td>
            <td><span class="status <?php echo $row['submission_type'] === 'worst_app' ? 'status-building' : 'status-submitted'; ?>"><?php echo strtoupper(str_replace('_', ' ', $row['submission_type'])); ?></span></td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;" title="<?php echo sanitize($row['idea_description']); ?>"><?php echo sanitize($row['idea_title']); ?></td>
            <td><?php echo $row['package'] ? sanitize($row['package']) : '-'; ?></td>
            <td>
              <select class="status-update" data-type="submission">
                <?php foreach ($statuses as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $row['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn-sm primary save-status" style="display:none;margin-left:0.3rem;">Save</button>
            </td>
            <td>
              <input type="checkbox" class="toggle-public" data-type="submission" <?php echo $row['is_public'] ? 'checked' : ''; ?>>
            </td>
            <td style="white-space:nowrap;font-size:0.75rem;"><?php echo date('M j, g:ia', strtotime($row['created_at'])); ?></td>
            <td class="actions">
              <button class="btn-sm view-details" onclick="viewSubmission(<?php echo $row['id']; ?>)">View</button>
              <button class="btn-sm danger confirm-delete" onclick="deleteSubmission(<?php echo $row['id']; ?>)">Delete</button>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($submissions)): ?>
          <tr><td colspan="11" style="color:#9AA5BC;text-align:center;padding:2rem;">No submissions yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;justify-content:center;align-items:center;">
  <div style="background:#fff;border-radius:16px;padding:2rem;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;">
    <div id="modal-content"></div>
    <button class="btn-sm" style="margin-top:1rem;" onclick="document.getElementById('detail-modal').style.display='none'">Close</button>
  </div>
</div>

<script src="/assets/js/admin.js"></script>
<script>
function viewSubmission(id) {
  fetch('/admin/submissions.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=get_details&id=' + id
  })
  .then(r => r.json())
  .then(d => {
    if (d && d.id) {
      var html = '<h2 style="margin-bottom:1rem;">#' + d.queue_number + ' — ' + d.idea_title + '</h2>';
      html += '<div style="margin-bottom:1rem;"><strong>Name:</strong> ' + d.name + '<br><strong>Contact:</strong> ' + d.contact + '<br><strong>Type:</strong> ' + d.submission_type + '<br><strong>Package:</strong> ' + (d.package || 'N/A') + '<br><strong>Status:</strong> ' + d.status + '<br><strong>Queue:</strong> #' + d.queue_number + '<br><strong>Date:</strong> ' + d.created_at + '</div>';
      html += '<div style="margin-bottom:1rem;"><strong>Description:</strong><br><p style="color:#5A6A8A;line-height:1.6;margin-top:0.3rem;">' + d.idea_description + '</p></div>';
      if (d.admin_notes) {
        html += '<div style="margin-bottom:1rem;"><strong>Admin Notes:</strong><br><p style="color:#5A6A8A;line-height:1.6;margin-top:0.3rem;">' + d.admin_notes + '</p></div>';
      }
      if (d.sale_price) {
        html += '<div style="margin-bottom:1rem;"><strong>Sale Price:</strong> $' + parseFloat(d.sale_price).toFixed(2) + (d.sold_at ? ' (sold ' + d.sold_at + ')' : '') + '</div>';
      }
      document.getElementById('modal-content').innerHTML = html;
      document.getElementById('detail-modal').style.display = 'flex';
    }
  });
}

function deleteSubmission(id) {
  if (!confirm('Delete this submission permanently?')) return;
  fetch('/admin/submissions.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=delete&id=' + id
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) location.reload();
    else alert('Error deleting.');
  });
}
</script>
</body>
</html>
