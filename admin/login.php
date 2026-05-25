<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            header('Location: /admin/dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please enter email and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — NexusSynced</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="login-page">
<div class="login-box">
  <h1>NEXUS<span style="color:#FFA600;">SYNCED</span></h1>
  <p class="sub">Admin Panel</p>
  <?php if ($error): ?>
  <div class="error"><?php echo sanitize($error); ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required placeholder="admin@nexussynced.com">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required placeholder="Enter your password">
    </div>
    <button type="submit" class="btn">SIGN IN</button>
  </form>
  <div class="secp">SECP REGISTERED · SMC · PVT LTD</div>
</div>
</body>
</html>
