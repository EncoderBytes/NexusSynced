<?php
/**
 * ONE-TIME SETUP SCRIPT
 * Run this ONCE to create the admin user, then DELETE this file.
 */

require_once __DIR__ . '/../includes/db.php';

$message = '';
$success = false;

// Check if admin already exists
$check = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$check) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$email || !$password) {
        $message = 'Email and password are required.';
    } elseif ($password !== $confirm) {
        $message = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (email, password_hash) VALUES (?, ?)");
        $stmt->execute([$email, $hash]);
        $message = 'Admin user created successfully! You can now <a href="/admin/login.php">log in</a>.';
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Setup — NexusSynced</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Inter', sans-serif; background: #1E2946; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
.box { background: #fff; border-radius: 16px; padding: 3rem; width: 100%; max-width: 420px; text-align: center; }
.box h1 { font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; margin-bottom: 0.5rem; }
.box h1 span { color: #FFA600; }
.box .sub { color: #9AA5BC; font-size: 0.85rem; margin-bottom: 2rem; }
.box .done { background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; }
.box .msg { background: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; text-align: left; }
.form-group { margin-bottom: 1.25rem; text-align: left; }
.form-group label { display: block; font-size: 0.78rem; font-weight: 600; color: #5A6A8A; margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.05em; }
.form-group input { width: 100%; padding: 0.75rem 1rem; border: 1px solid #D8DEF0; border-radius: 8px; font-size: 0.9rem; outline: none; }
.form-group input:focus { border-color: #1E2946; }
.btn { width: 100%; padding: 0.75rem; background: #1E2946; color: #fff; border: none; border-radius: 8px; font-weight: 700; font-size: 0.9rem; cursor: pointer; }
.btn:hover { background: #2a3a5c; }
.warn { background: #fff3cd; color: #856404; padding: 1rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1rem; }
</style>
</head>
<body>
<div class="box">
  <h1>NEXUS<span>SYNCED</span></h1>
  <p class="sub">Admin Account Setup</p>

  <?php if ($check > 0): ?>
  <div class="warn">⚠️ Admin user already exists. This script is no longer needed.<br><br><a href="/admin/login.php" style="color:#1E2946;font-weight:600;">Go to Login →</a></div>
  <?php elseif ($success): ?>
  <div class="done"><?php echo $message; ?></div>
  <?php else: ?>
  <?php if ($message): ?><div class="msg"><?php echo $message; ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" required placeholder="admin@nexussynced.com">
    </div>
    <div class="form-group">
      <label>Password (min 8 characters)</label>
      <input type="password" name="password" required placeholder="Create strong password">
    </div>
    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" name="confirm" required placeholder="Repeat password">
    </div>
    <button type="submit" class="btn">CREATE ADMIN ACCOUNT</button>
  </form>
  <p style="margin-top:1.5rem;font-size:0.7rem;color:#9AA5BC;">⚠️ DELETE THIS FILE AFTER SETUP</p>
  <?php endif; ?>
</div>
</body>
</html>
