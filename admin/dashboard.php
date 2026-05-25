<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Stats
$monthStart = date('Y-m-01');
$totalThisMonth = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE created_at >= ?");
$totalThisMonth->execute([$monthStart]);
$totalThisMonth = (int) $totalThisMonth->fetchColumn();

$currentlyBuilding = $pdo->query("SELECT COUNT(*) FROM submissions WHERE status = 'building'")->fetchColumn();
$unreadContacts = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();

$revenue = $pdo->query("SELECT COALESCE(SUM(sale_price), 0) FROM submissions WHERE status = 'sold'")->fetchColumn();

$mvpsCount = getSetting($pdo, 'mvps_shipped_count') ?: '0';

// Recent submissions
$recent = $pdo->query("SELECT id, queue_number, name, idea_title, submission_type, package, status, created_at FROM submissions ORDER BY id DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — NexusSynced Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main">
  <div class="main-header">
    <div>
      <h1>Dashboard</h1>
      <div class="subtitle">Overview of your NexusSynced operations</div>
    </div>
    <div style="font-size:0.82rem;color:#9AA5BC;"><?php echo date('l, F j, Y'); ?></div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="num"><?php echo $totalThisMonth; ?></div>
      <div class="label">Submissions This Month</div>
    </div>
    <div class="stat-card">
      <div class="num"><?php echo $currentlyBuilding; ?></div>
      <div class="label">Currently Building</div>
    </div>
    <div class="stat-card">
      <div class="num accent"><?php echo sanitize($mvpsCount); ?></div>
      <div class="label">MVPs Shipped</div>
    </div>
    <div class="stat-card">
      <div class="num"><?php echo $unreadContacts; ?></div>
      <div class="label">Unread Messages</div>
    </div>
    <div class="stat-card">
      <div class="num accent">$<?php echo number_format($revenue, 0); ?></div>
      <div class="label">Revenue</div>
    </div>
  </div>

  <div class="admin-section">
    <h2>Recent Submissions</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Queue</th>
            <th>Name</th>
            <th>Idea</th>
            <th>Type</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $row): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['queue_number'] ? '#' . $row['queue_number'] : '-'; ?></td>
            <td><?php echo sanitize($row['name']); ?></td>
            <td><?php echo sanitize(mb_strimwidth($row['idea_title'], 0, 40, '...')); ?></td>
            <td><span class="status <?php echo $row['submission_type'] === 'worst_app' ? 'status-building' : 'status-submitted'; ?>"><?php echo strtoupper($row['submission_type']); ?></span></td>
            <td><span class="status status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
            <td><?php echo date('M j, g:ia', strtotime($row['created_at'])); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recent)): ?>
          <tr><td colspan="7" style="color:#9AA5BC;text-align:center;padding:2rem;">No submissions yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="/assets/js/admin.js"></script>
</body>
</html>
