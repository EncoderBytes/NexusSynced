<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = $_POST['category'] ?? 'digital_skills';
        $description = trim($_POST['description'] ?? '');
        $target_audience = trim($_POST['target_audience'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $format = trim($_POST['format'] ?? '');
        $learning_outcomes = trim($_POST['learning_outcomes'] ?? '');
        $is_published = (int) ($_POST['is_published'] ?? 0);
        $sort_order = (int) ($_POST['sort_order'] ?? 0);

        if ($title && $description) {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO trainings (title, category, description, target_audience, duration, format, learning_outcomes, is_published, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $category, $description, $target_audience ?: null, $duration ?: null, $format ?: null, $learning_outcomes ?: null, $is_published, $sort_order]);
            } else {
                $stmt = $pdo->prepare("UPDATE trainings SET title=?, category=?, description=?, target_audience=?, duration=?, format=?, learning_outcomes=?, is_published=?, sort_order=? WHERE id=?");
                $stmt->execute([$title, $category, $description, $target_audience ?: null, $duration ?: null, $format ?: null, $learning_outcomes ?: null, $is_published, $sort_order, $id]);
            }
            echo json_encode(['success' => true]);
            exit;
        }
        echo json_encode(['success' => false, 'error' => 'Title and description required.']);
        exit;
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM trainings WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'get') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM trainings WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch() ?: []);
            exit;
        }
    }

    echo json_encode(['success' => false]);
    exit;
}

$trainings = $pdo->query("SELECT * FROM trainings ORDER BY sort_order ASC, id DESC")->fetchAll();
$cats = ['digital_skills' => 'Digital Skills', 'cyber_security' => 'Cyber Security', 'online_safety' => 'Online Safety', 'privacy' => 'Privacy', 'leadership' => 'Leadership', 'stem' => 'STEM'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trainings — NexusSynced Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main">
  <div class="main-header">
    <div>
      <h1>Trainings</h1>
      <div class="subtitle">Training programs for NGOs, women, and young girls</div>
    </div>
  </div>

  <div class="admin-section">
    <h2>Add New Training Program</h2>
    <form class="admin-form" method="POST" onsubmit="return addTraining(event)">
      <input type="hidden" name="action" value="add">
      <div class="form-row">
        <div class="form-group">
          <label>Title *</label>
          <input type="text" name="title" required placeholder="e.g. Digital Skills for Women">
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category">
            <?php foreach ($cats as $val => $label): ?>
            <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row three">
        <div class="form-group">
          <label>Target Audience</label>
          <input type="text" name="target_audience" placeholder="e.g. Women, young girls, NGO staff">
        </div>
        <div class="form-group">
          <label>Duration</label>
          <input type="text" name="duration" placeholder="e.g. 4 weeks, 2 days">
        </div>
        <div class="form-group">
          <label>Format</label>
          <input type="text" name="format" placeholder="e.g. Online, In-person, Hybrid">
        </div>
      </div>
      <div class="form-group full">
        <label>Description *</label>
        <textarea name="description" required placeholder="What this training covers..."></textarea>
      </div>
      <div class="form-group full">
        <label>Learning Outcomes (one per line)</label>
        <textarea name="learning_outcomes" placeholder="Understand digital security basics&#10;Identify phishing attempts&#10;Secure social media accounts"></textarea>
      </div>
      <div class="form-row three">
        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" value="0">
        </div>
        <div class="form-group">
          <label><input type="checkbox" name="is_published" value="1" checked> Published</label>
        </div>
      </div>
      <button type="submit" class="btn-sm primary">ADD TRAINING</button>
    </form>
  </div>

  <div class="admin-section">
    <h2>All Training Programs</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Order</th>
            <th>Title</th>
            <th>Category</th>
            <th>Audience</th>
            <th>Duration</th>
            <th>Published</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($trainings as $row): ?>
          <tr>
            <td><?php echo $row['sort_order']; ?></td>
            <td><?php echo sanitize($row['title']); ?></td>
            <td><span class="status status-submitted"><?php echo sanitize($cats[$row['category']] ?? $row['category']); ?></span></td>
            <td style="font-size:0.75rem;"><?php echo sanitize($row['target_audience'] ?: '-'); ?></td>
            <td><?php echo sanitize($row['duration'] ?: '-'); ?></td>
            <td><span class="pub-badge <?php echo $row['is_published'] ? 'pub-yes' : 'pub-no'; ?>"><?php echo $row['is_published'] ? 'YES' : 'NO'; ?></span></td>
            <td class="actions">
              <button class="btn-sm" onclick="editTraining(<?php echo $row['id']; ?>)">Edit</button>
              <button class="btn-sm danger confirm-delete" onclick="deleteTraining(<?php echo $row['id']; ?>)">Delete</button>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($trainings)): ?>
          <tr><td colspan="7" style="color:#9AA5BC;text-align:center;padding:2rem;">No training programs yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;justify-content:center;align-items:center;">
  <div style="background:#fff;border-radius:16px;padding:2rem;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;" id="edit-content"></div>
</div>

<script src="/assets/js/admin.js"></script>
<script>
function addTraining(e) {
  e.preventDefault();
  var fd = new FormData(e.target);
  fetch('/admin/trainings.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert('Error: ' + (d.error || 'unknown')); });
  return false;
}

function deleteTraining(id) {
  if (!confirm('Delete this training program?')) return;
  fetch('/admin/trainings.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=delete&id=' + id
  })
  .then(r => r.json())
  .then(d => { if (d.success) location.reload(); });
}

function editTraining(id) {
  fetch('/admin/trainings.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=get&id=' + id
  })
  .then(r => r.json())
  .then(d => {
    if (!d || !d.id) return;
    var html = '<h2 style="margin-bottom:1rem;">Edit Training</h2>';
    html += '<form class="admin-form" onsubmit="return updateTraining(event, ' + id + ')">';
    html += '<div class="form-row"><div class="form-group"><label>Title</label><input type="text" name="title" value="' + (d.title||'').replace(/"/g,'&quot;') + '"></div>';
    html += '<div class="form-group"><label>Category</label><select name="category">';
    <?php foreach ($cats as $val => $label): ?>
    html += '<option value="<?php echo $val; ?>" ' + (d.category === '<?php echo $val; ?>' ? 'selected' : '') + '><?php echo $label; ?></option>';
    <?php endforeach; ?>
    html += '</select></div></div>';
    html += '<div class="form-row three"><div class="form-group"><label>Audience</label><input type="text" name="target_audience" value="' + (d.target_audience||'') + '"></div>';
    html += '<div class="form-group"><label>Duration</label><input type="text" name="duration" value="' + (d.duration||'') + '"></div>';
    html += '<div class="form-group"><label>Format</label><input type="text" name="format" value="' + (d.format||'') + '"></div></div>';
    html += '<div class="form-group full"><label>Description</label><textarea name="description">' + (d.description||'').replace(/</g,'&lt;') + '</textarea></div>';
    html += '<div class="form-group full"><label>Learning Outcomes</label><textarea name="learning_outcomes">' + (d.learning_outcomes||'').replace(/</g,'&lt;') + '</textarea></div>';
    html += '<div class="form-row three"><div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="' + (d.sort_order||0) + '"></div>';
    html += '<div class="form-group"><label><input type="checkbox" name="is_published" value="1" ' + (d.is_published == 1 ? 'checked' : '') + '> Published</label></div></div>';
    html += '<button type="submit" class="btn-sm primary">UPDATE</button> ';
    html += '<button type="button" class="btn-sm" onclick="document.getElementById(\'edit-modal\').style.display=\'none\'">Cancel</button>';
    html += '</form>';
    document.getElementById('edit-content').innerHTML = html;
    document.getElementById('edit-modal').style.display = 'flex';
  });
}

function updateTraining(e, id) {
  e.preventDefault();
  var fd = new FormData(e.target);
  fd.append('action', 'update');
  fd.append('id', id);
  fetch('/admin/trainings.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); });
  return false;
}
</script>
</body>
</html>
