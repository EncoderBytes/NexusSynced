<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = $_POST['category'] ?? 'web';
        $description = trim($_POST['description'] ?? '');
        $techStack = trim($_POST['tech_stack'] ?? '');
        $demoUrl = trim($_POST['demo_url'] ?? '');
        $caseStudyUrl = trim($_POST['case_study_url'] ?? '');
        $isPublished = (int) ($_POST['is_published'] ?? 0);
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);

        $screenshotUrl = null;
        if (!empty($_FILES['screenshot']['name'])) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            $ext = pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION);
            $filename = 'portfolio_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $uploadDir . $filename)) {
                $screenshotUrl = '/assets/uploads/' . $filename;
            }
        }

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO portfolio (title, category, description, tech_stack, screenshot_url, demo_url, case_study_url, is_published, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $category, $description, $techStack ?: null, $screenshotUrl, $demoUrl ?: null, $caseStudyUrl ?: null, $isPublished, $sortOrder]);
        } else {
            if ($screenshotUrl) {
                $stmt = $pdo->prepare("UPDATE portfolio SET title=?, category=?, description=?, tech_stack=?, screenshot_url=?, demo_url=?, case_study_url=?, is_published=?, sort_order=? WHERE id=?");
                $stmt->execute([$title, $category, $description, $techStack ?: null, $screenshotUrl, $demoUrl ?: null, $caseStudyUrl ?: null, $isPublished, $sortOrder, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE portfolio SET title=?, category=?, description=?, tech_stack=?, demo_url=?, case_study_url=?, is_published=?, sort_order=? WHERE id=?");
                $stmt->execute([$title, $category, $description, $techStack ?: null, $demoUrl ?: null, $caseStudyUrl ?: null, $isPublished, $sortOrder, $id]);
            }
        }

        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $pdo->prepare("DELETE FROM portfolio WHERE id = ?")->execute([$id]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    if ($action === 'get') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM portfolio WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch() ?: []);
            exit;
        }
    }

    echo json_encode(['success' => false]);
    exit;
}

$items = $pdo->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id DESC")->fetchAll();
$categories = ['web', 'mobile', 'ai', 'saas', 'training'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portfolio — NexusSynced Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main">
  <div class="main-header">
    <div>
      <h1>Portfolio</h1>
      <div class="subtitle">Manage portfolio projects</div>
    </div>
  </div>

  <div class="admin-section">
    <h2>Add New Portfolio Item</h2>
    <form class="admin-form" method="POST" enctype="multipart/form-data" onsubmit="return addPortfolio(event)">
      <input type="hidden" name="action" value="add">
      <div class="form-row">
        <div class="form-group">
          <label>Title *</label>
          <input type="text" name="title" required placeholder="Project name">
        </div>
        <div class="form-group">
          <label>Category</label>
          <select name="category">
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c; ?>"><?php echo ucfirst($c); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group full">
          <label>Description *</label>
          <textarea name="description" required placeholder="Project description"></textarea>
        </div>
      </div>
      <div class="form-row three">
        <div class="form-group">
          <label>Tech Stack (comma separated)</label>
          <input type="text" name="tech_stack" placeholder="PHP, React, MySQL">
        </div>
        <div class="form-group">
          <label>Demo URL</label>
          <input type="url" name="demo_url" placeholder="https://">
        </div>
        <div class="form-group">
          <label>Case Study URL</label>
          <input type="url" name="case_study_url" placeholder="https://">
        </div>
      </div>
      <div class="form-row three">
        <div class="form-group">
          <label>Screenshot</label>
          <input type="file" name="screenshot" accept="image/*">
        </div>
        <div class="form-group">
          <label>Sort Order</label>
          <input type="number" name="sort_order" value="0">
        </div>
        <div class="form-group">
          <label><input type="checkbox" name="is_published" value="1" checked> Published</label>
        </div>
      </div>
      <button type="submit" class="btn-sm primary">ADD PORTFOLIO ITEM</button>
    </form>
  </div>

  <div class="admin-section">
    <h2>All Portfolio Items</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Order</th>
            <th>Title</th>
            <th>Category</th>
            <th>Tech Stack</th>
            <th>Published</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $row): ?>
          <tr>
            <td><?php echo $row['sort_order']; ?></td>
            <td><?php echo sanitize($row['title']); ?></td>
            <td><span class="status status-submitted"><?php echo strtoupper($row['category']); ?></span></td>
            <td style="font-size:0.75rem;"><?php echo sanitize($row['tech_stack'] ?: '-'); ?></td>
            <td><span class="pub-badge <?php echo $row['is_published'] ? 'pub-yes' : 'pub-no'; ?>"><?php echo $row['is_published'] ? 'YES' : 'NO'; ?></span></td>
            <td class="actions">
              <button class="btn-sm" onclick="editPortfolio(<?php echo $row['id']; ?>)">Edit</button>
              <button class="btn-sm danger confirm-delete" onclick="deletePortfolio(<?php echo $row['id']; ?>)">Delete</button>
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
function addPortfolio(e) {
  e.preventDefault();
  var fd = new FormData(e.target);
  fetch('/admin/portfolio.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); else alert('Error.'); });
  return false;
}

function deletePortfolio(id) {
  if (!confirm('Delete this portfolio item?')) return;
  fetch('/admin/portfolio.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=delete&id=' + id
  })
  .then(r => r.json())
  .then(d => { if (d.success) location.reload(); });
}

function editPortfolio(id) {
  fetch('/admin/portfolio.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=get&id=' + id
  })
  .then(r => r.json())
  .then(d => {
    if (!d || !d.id) return;
    var html = '<h2 style="margin-bottom:1rem;">Edit Portfolio</h2>';
    html += '<form class="admin-form" onsubmit="return updatePortfolio(event, ' + id + ')">';
    html += '<div class="form-row"><div class="form-group"><label>Title</label><input type="text" name="title" value="' + d.title.replace(/"/g,'&quot;') + '"></div>';
    html += '<div class="form-group"><label>Category</label><select name="category">';
    <?php foreach ($categories as $c): ?>
    html += '<option value="<?php echo $c; ?>" ' + (d.category === '<?php echo $c; ?>' ? 'selected' : '') + '><?php echo ucfirst($c); ?></option>';
    <?php endforeach; ?>
    html += '</select></div></div>';
    html += '<div class="form-group full"><label>Description</label><textarea name="description">' + d.description.replace(/</g,'&lt;') + '</textarea></div>';
    html += '<div class="form-row three"><div class="form-group"><label>Tech Stack</label><input type="text" name="tech_stack" value="' + (d.tech_stack||'') + '"></div>';
    html += '<div class="form-group"><label>Demo URL</label><input type="url" name="demo_url" value="' + (d.demo_url||'') + '"></div>';
    html += '<div class="form-group"><label>Case Study URL</label><input type="url" name="case_study_url" value="' + (d.case_study_url||'') + '"></div></div>';
    html += '<div class="form-row three"><div class="form-group"><label>Screenshot</label><input type="file" name="screenshot" accept="image/*"></div>';
    html += '<div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" value="' + (d.sort_order||0) + '"></div>';
    html += '<div class="form-group"><label><input type="checkbox" name="is_published" value="1" ' + (d.is_published == 1 ? 'checked' : '') + '> Published</label></div></div>';
    html += '<button type="submit" class="btn-sm primary">UPDATE</button> ';
    html += '<button type="button" class="btn-sm" onclick="document.getElementById(\'edit-modal\').style.display=\'none\'">Cancel</button>';
    html += '</form>';
    document.getElementById('edit-content').innerHTML = html;
    document.getElementById('edit-modal').style.display = 'flex';
  });
}

function updatePortfolio(e, id) {
  e.preventDefault();
  var fd = new FormData(e.target);
  fd.append('action', 'update');
  fd.append('id', id);
  fetch('/admin/portfolio.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); });
  return false;
}
</script>

<div id="edit-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;justify-content:center;align-items:center;">
  <div style="background:#fff;border-radius:16px;padding:2rem;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;" id="edit-content"></div>
</div>
</body>
</html>
