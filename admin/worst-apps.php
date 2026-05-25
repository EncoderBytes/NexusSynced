<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    // Add new
    if ($action === 'add') {
        $week = (int) ($_POST['week_number'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'idea';
        $buyPrice = $_POST['buy_price'] !== '' ? (float) $_POST['buy_price'] : null;
        $appUrl = trim($_POST['app_url'] ?? '');
        $isFeatured = (int) ($_POST['is_featured'] ?? 0);

        if ($title && $description) {
            // Handle screenshot upload
            $screenshotUrl = null;
            if (!empty($_FILES['screenshot']['name'])) {
                $uploadDir = __DIR__ . '/../assets/uploads/';
                $ext = pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION);
                $filename = 'worst_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $uploadDir . $filename)) {
                    $screenshotUrl = '/assets/uploads/' . $filename;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO worst_apps (week_number, title, description, status, buy_price, app_url, is_featured, screenshot_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$week, $title, $description, $status, $buyPrice, $appUrl ?: null, $isFeatured, $screenshotUrl]);
            echo json_encode(['success' => true]);
            exit;
        }
        echo json_encode(['success' => false, 'error' => 'Title and description required.']);
        exit;
    }

    // Update
    if ($action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $week = (int) ($_POST['week_number'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'idea';
        $buyPrice = $_POST['buy_price'] !== '' ? (float) $_POST['buy_price'] : null;
        $appUrl = trim($_POST['app_url'] ?? '');
        $isFeatured = (int) ($_POST['is_featured'] ?? 0);

        if ($id && $title && $description) {
            $screenshotUrl = null;
            if (!empty($_FILES['screenshot']['name'])) {
                $uploadDir = __DIR__ . '/../assets/uploads/';
                $ext = pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION);
                $filename = 'worst_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $uploadDir . $filename)) {
                    $screenshotUrl = '/assets/uploads/' . $filename;
                }
            }

            if ($screenshotUrl) {
                $stmt = $pdo->prepare("UPDATE worst_apps SET week_number=?, title=?, description=?, status=?, buy_price=?, app_url=?, is_featured=?, screenshot_url=? WHERE id=?");
                $stmt->execute([$week, $title, $description, $status, $buyPrice, $appUrl ?: null, $isFeatured, $screenshotUrl, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE worst_apps SET week_number=?, title=?, description=?, status=?, buy_price=?, app_url=?, is_featured=? WHERE id=?");
                $stmt->execute([$week, $title, $description, $status, $buyPrice, $appUrl ?: null, $isFeatured, $id]);
            }
            echo json_encode(['success' => true]);
            exit;
        }
        echo json_encode(['success' => false]);
        exit;
    }

    if ($action === 'update_status') {
        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if ($id && in_array($status, ['idea', 'building', 'built', 'sold'])) {
            $pdo->prepare("UPDATE worst_apps SET status = ? WHERE id = ?")->execute([$status, $id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'toggle_public') {
        $id = (int) ($_POST['id'] ?? 0);
        $isFeatured = (int) ($_POST['is_public'] ?? 0);
        if ($id) {
            $pdo->prepare("UPDATE worst_apps SET is_featured = ? WHERE id = ?")->execute([$isFeatured, $id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM worst_apps WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'get') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM worst_apps WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch() ?: []);
            exit;
        }
    }

    echo json_encode(['success' => false]);
    exit;
}

$apps = $pdo->query("SELECT * FROM worst_apps ORDER BY week_number ASC")->fetchAll();
$statuses = ['idea', 'building', 'built', 'sold'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Worst Apps — NexusSynced Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main">
  <div class="main-header">
    <div>
      <h1>Worst Apps</h1>
      <div class="subtitle">The Worst App Series — manage entries</div>
    </div>
  </div>

  <div class="admin-section">
    <h2>Add New Worst App</h2>
    <form class="admin-form" method="POST" enctype="multipart/form-data" onsubmit="return addWorst(event)">
      <input type="hidden" name="action" value="add">
      <div class="form-row">
        <div class="form-group">
          <label>Week Number</label>
          <input type="number" name="week_number" required value="<?php echo count($apps) + 1; ?>">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <?php foreach ($statuses as $s): ?>
            <option value="<?php echo $s; ?>"><?php echo ucfirst($s); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group full">
          <label>Title</label>
          <input type="text" name="title" required placeholder="App name">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group full">
          <label>Description</label>
          <textarea name="description" required placeholder="What does this terrible app do?"></textarea>
        </div>
      </div>
      <div class="form-row three">
        <div class="form-group">
          <label>Buy Price ($)</label>
          <input type="number" step="0.01" name="buy_price" placeholder="e.g. 150">
        </div>
        <div class="form-group">
          <label>App URL</label>
          <input type="url" name="app_url" placeholder="https://">
        </div>
        <div class="form-group">
          <label>Screenshot</label>
          <input type="file" name="screenshot" accept="image/*">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label><input type="checkbox" name="is_featured" value="1"> Featured</label>
        </div>
      </div>
      <button type="submit" class="btn-sm primary">ADD WORST APP</button>
    </form>
  </div>

  <div class="admin-section">
    <h2>All Worst Apps</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Week</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Price</th>
            <th>Featured</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($apps as $row): ?>
          <tr data-id="<?php echo $row['id']; ?>">
            <td>#<?php echo $row['week_number']; ?></td>
            <td><?php echo sanitize($row['title']); ?></td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;"><?php echo sanitize(mb_strimwidth($row['description'], 0, 60, '...')); ?></td>
            <td>
              <select class="status-update" data-type="worst_app">
                <?php foreach ($statuses as $s): ?>
                <option value="<?php echo $s; ?>" <?php echo $row['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn-sm primary save-status" style="display:none;margin-left:0.3rem;">Save</button>
            </td>
            <td><?php echo $row['buy_price'] ? '$' . number_format($row['buy_price'], 2) : '-'; ?></td>
            <td><input type="checkbox" class="toggle-public" data-type="worst_app" <?php echo $row['is_featured'] ? 'checked' : ''; ?>></td>
            <td class="actions">
              <button class="btn-sm" onclick="editWorst(<?php echo $row['id']; ?>)">Edit</button>
              <button class="btn-sm danger confirm-delete" onclick="deleteWorst(<?php echo $row['id']; ?>)">Delete</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="/assets/js/admin.js"></script>
<script>
function addWorst(e) {
  e.preventDefault();
  var form = e.target;
  var fd = new FormData(form);
  fetch('/admin/worst-apps.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert('Error adding.'); });
  return false;
}

function deleteWorst(id) {
  if (!confirm('Delete this worst app?')) return;
  fetch('/admin/worst-apps.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=delete&id=' + id
  })
  .then(r => r.json())
  .then(d => { if (d.success) location.reload(); });
}

function editWorst(id) {
  fetch('/admin/worst-apps.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=get&id=' + id
  })
  .then(r => r.json())
  .then(d => {
    if (!d || !d.id) return;
    var fields = [
      'week_number', 'title', 'description', 'status', 'buy_price', 'app_url', 'is_featured'
    ];
    var vals = {};
    fields.forEach(function(f) { vals[f] = d[f] || ''; });
    var html = '<h2 style="margin-bottom:1rem;">Edit Worst App</h2>';
    html += '<form class="admin-form" onsubmit="return updateWorst(event, ' + id + ')">';
    html += '<div class="form-row"><div class="form-group"><label>Week #</label><input type="number" name="week_number" value="' + vals.week_number + '"></div>';
    html += '<div class="form-group"><label>Status</label><select name="status">';
    <?php foreach ($statuses as $s): ?>
    html += '<option value="<?php echo $s; ?>" ' + (vals.status === '<?php echo $s; ?>' ? 'selected' : '') + '><?php echo ucfirst($s); ?></option>';
    <?php endforeach; ?>
    html += '</select></div></div>';
    html += '<div class="form-group full"><label>Title</label><input type="text" name="title" value="' + vals.title.replace(/"/g,'&quot;') + '"></div>';
    html += '<div class="form-group full"><label>Description</label><textarea name="description">' + vals.description.replace(/</g,'&lt;') + '</textarea></div>';
    html += '<div class="form-row three"><div class="form-group"><label>Price ($)</label><input type="number" step="0.01" name="buy_price" value="' + vals.buy_price + '"></div>';
    html += '<div class="form-group"><label>URL</label><input type="url" name="app_url" value="' + vals.app_url + '"></div>';
    html += '<div class="form-group"><label>Screenshot</label><input type="file" name="screenshot" accept="image/*"></div></div>';
    html += '<div class="form-group"><label><input type="checkbox" name="is_featured" value="1" ' + (vals.is_featured == 1 ? 'checked' : '') + '> Featured</label></div>';
    html += '<button type="submit" class="btn-sm primary" style="padding:0.6rem 1.5rem;">UPDATE</button> ';
    html += '<button type="button" class="btn-sm" onclick="document.getElementById(\'edit-modal\').style.display=\'none\'">Cancel</button>';
    html += '</form>';
    document.getElementById('edit-content').innerHTML = html;
    document.getElementById('edit-modal').style.display = 'flex';
  });
}

function updateWorst(e, id) {
  e.preventDefault();
  var fd = new FormData(e.target);
  fd.append('action', 'update');
  fd.append('id', id);
  fetch('/admin/worst-apps.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert('Error updating.'); });
  return false;
}
</script>

<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;justify-content:center;align-items:center;">
  <div style="background:#fff;border-radius:16px;padding:2rem;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;" id="edit-content"></div>
</div>
</body>
</html>
