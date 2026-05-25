<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle save
$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $fields = ['mvps_shipped_count', 'devil_hero_tagline', 'devil_mode_enabled', 'instagram_url', 'whatsapp_number', 'admin_email', 'secp_number'];
    foreach ($fields as $key) {
        $val = trim($_POST[$key] ?? '');
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
        $stmt->execute([$key, $val]);
    }
    $saved = true;
}

// Load current settings
$settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
foreach ($stmt->fetchAll() as $row) {
    $settings[$row['key']] = $row['value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings — NexusSynced Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/sidebar.php'; ?>

<div class="main">
  <div class="main-header">
    <div>
      <h1>Settings</h1>
      <div class="subtitle">Site-wide configuration</div>
    </div>
  </div>

  <?php if ($saved): ?>
  <div style="background:#d4edda;color:#155724;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:0.85rem;">Settings saved successfully.</div>
  <?php endif; ?>

  <div class="admin-section">
    <form method="POST" class="admin-form">
      <div class="form-row">
        <div class="form-group">
          <label>MVPs Shipped Count</label>
          <input type="text" name="mvps_shipped_count" value="<?php echo sanitize($settings['mvps_shipped_count'] ?? '23'); ?>">
        </div>
        <div class="form-group">
          <label>Devil Mode Hero Tagline</label>
          <input type="text" name="devil_hero_tagline" value="<?php echo sanitize($settings['devil_hero_tagline'] ?? 'YOUR IDEA. LIVE IN 72 HOURS.'); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Devil Mode Enabled</label>
          <select name="devil_mode_enabled">
            <option value="1" <?php echo ($settings['devil_mode_enabled'] ?? '1') === '1' ? 'selected' : ''; ?>>Enabled</option>
            <option value="0" <?php echo ($settings['devil_mode_enabled'] ?? '1') === '0' ? 'selected' : ''; ?>>Disabled</option>
          </select>
        </div>
        <div class="form-group">
          <label>Admin Email (notifications)</label>
          <input type="email" name="admin_email" value="<?php echo sanitize($settings['admin_email'] ?? 'info@nexussynced.com'); ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Instagram URL</label>
          <input type="url" name="instagram_url" value="<?php echo sanitize($settings['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/nexussynced">
        </div>
        <div class="form-group">
          <label>WhatsApp Number</label>
          <input type="text" name="whatsapp_number" value="<?php echo sanitize($settings['whatsapp_number'] ?? ''); ?>" placeholder="+92XXXXXXXXXX">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>SECP Registration Number</label>
          <input type="text" name="secp_number" value="<?php echo sanitize($settings['secp_number'] ?? ''); ?>" placeholder="Optional">
        </div>
      </div>
      <button type="submit" name="save" class="btn-sm primary" style="padding:0.6rem 2rem;">SAVE SETTINGS</button>
    </form>
  </div>
</div>

<script src="/assets/js/admin.js"></script>
</body>
</html>
