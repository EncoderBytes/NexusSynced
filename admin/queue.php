<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle status/public updates via AJAX
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

    echo json_encode(['success' => false]);
    exit;
}

// Get all submissions ordered by queue number
$all = $pdo->query("SELECT * FROM submissions ORDER BY queue_number ASC")->fetchAll();
$columns = ['submitted' => 'Submitted', 'building' => 'Building Now', 'done' => 'Done — Available', 'sold' => 'Sold'];
$statuses = ['submitted', 'building', 'done', 'sold'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Queue Board — NexusSynced Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
<style>
.kanban { display:flex; gap:1rem; overflow-x:auto; align-items:flex-start; }
.kanban-col { flex:1; min-width:240px; background:#F4F6FB; border-radius:12px; padding:1rem; }
.kanban-col h3 { font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:0.85rem; margin-bottom:0.75rem; color:#5A6A8A; text-transform:uppercase; letter-spacing:0.05em; }
.kanban-card { background:#fff; border:1px solid #D8DEF0; border-radius:8px; padding:0.75rem; margin-bottom:0.5rem; }
.kanban-card .id { font-family:'JetBrains Mono',monospace; font-size:0.65rem; color:#9AA5BC; }
.kanban-card .title { font-family:'Plus Jakarta Sans',sans-serif; font-weight:600; font-size:0.9rem; margin:0.2rem 0; }
.kanban-card .type { font-size:0.72rem; color:#9AA5BC; }
.kanban-card .pub-badge { font-size:0.6rem; padding:0.15rem 0.4rem; border-radius:3px; display:inline-block; margin-top:0.3rem; }
.kanban-card select { margin-top:0.5rem; width:100%; font-size:0.72rem; padding:0.25rem; border:1px solid #D8DEF0; border-radius:4px; }
.kanban-card .btn-sm { margin-top:0.3rem; width:100%; }
</style>
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main">
  <div class="main-header">
    <div>
      <h1>Queue Board</h1>
      <div class="subtitle">Kanban view of all submissions — update statuses live</div>
    </div>
  </div>

  <div class="admin-section">
    <div class="kanban">
      <?php foreach ($columns as $key => $label): ?>
      <div class="kanban-col">
        <h3><?php echo $label; ?> (<?php echo count(array_filter($all, function($s) use ($key) { return $s['status'] === $key; })); ?>)</h3>
        <?php foreach ($all as $row): ?>
          <?php if ($row['status'] !== $key) continue; ?>
          <div class="kanban-card">
            <div class="id">#<?php echo $row['queue_number'] ?: $row['id']; ?></div>
            <div class="title"><?php echo sanitize($row['idea_title']); ?></div>
            <div class="type"><?php echo strtoupper(str_replace('_', ' ', $row['submission_type'])); ?></div>
            <div>
              <span class="pub-badge" style="background:<?php echo $row['is_public'] ? '#d4edda' : '#e8e8e8'; ?>;color:<?php echo $row['is_public'] ? '#155724' : '#999'; ?>;"><?php echo $row['is_public'] ? 'PUBLIC' : 'PRIVATE'; ?></span>
            </div>
            <select onchange="updateStatus(this, <?php echo $row['id']; ?>)">
              <?php foreach ($statuses as $s): ?>
              <option value="<?php echo $s; ?>" <?php echo $row['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
              <?php endforeach; ?>
            </select>
            <label style="font-size:0.7rem;display:flex;align-items:center;gap:0.3rem;margin-top:0.3rem;">
              <input type="checkbox" <?php echo $row['is_public'] ? 'checked' : ''; ?> onchange="togglePublic(this, <?php echo $row['id']; ?>)"> Public
            </label>
          </div>
        <?php endforeach; ?>
        <?php if (!array_filter($all, function($s) use ($key) { return $s['status'] === $key; })): ?>
          <div style="color:#9AA5BC;font-size:0.8rem;padding:1rem;text-align:center;border:1px dashed #D8DEF0;border-radius:8px;">Empty</div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script src="/assets/js/admin.js"></script>
<script>
function updateStatus(sel, id) {
  fetch('/admin/queue.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=update_status&id=' + id + '&status=' + sel.value
  })
  .then(r => r.json())
  .then(d => { if (d.success) setTimeout(function(){ location.reload(); }, 300); });
}

function togglePublic(cb, id) {
  fetch('/admin/queue.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=toggle_public&id=' + id + '&is_public=' + (cb.checked ? 1 : 0)
  });
}
</script>
</body>
</html>
