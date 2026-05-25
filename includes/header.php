<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
$current_page = basename($_SERVER['PHP_SELF']);
$is_devil = (isset($body_class) && strpos($body_class, 'devil') !== false);
if (!$is_devil && isset($_COOKIE['nexus_mode']) && $_COOKIE['nexus_mode'] === 'devil') {
    $is_devil = true;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php if ($is_devil): ?>
<title>NexusSynced Devil Mode — MVP in 72 Hours</title>
<meta name="description" content="Turn your startup idea into a working MVP in 72 hours. Web app, mobile app, AI-powered. Pitch deck included. Built by NexusSynced.">
<meta property="og:title" content="Your Idea. Live in 72 Hours.">
<meta property="og:image" content="/assets/img/og-devil.jpg">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<?php else: ?>
<title>NexusSynced — AI-Powered Software Development | Pakistan</title>
<meta name="description" content="NexusSynced is a SECP-registered women-led software house building AI-powered digital products for startups and enterprises. Based in Pakistan.">
<meta property="og:title" content="NexusSynced — AI-Powered Software Development">
<meta property="og:image" content="/assets/img/og-professional.jpg">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<?php endif; ?>

<link rel="stylesheet" href="/assets/css/main.css">
<?php if (strpos($current_page, 'admin') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false): ?>
<link rel="stylesheet" href="/assets/css/admin.css">
<?php endif; ?>
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
</head>
<body<?php echo isset($body_class) ? ' class="' . $body_class . '"' : ''; ?>>

<!-- Navbar -->
<nav class="navbar">
  <a href="<?php echo $is_devil ? '/devil.php' : '/index.php'; ?>" class="logo">NEXUS<span>SYNCED</span></a>
  <ul class="nav-links" id="nav-links">
    <?php if ($is_devil): ?>
    <li><a href="/devil.php#services">SERVICES</a></li>
    <li><a href="/devil.php#queue">QUEUE</a></li>
    <li><a href="/devil.php#worst">WORST APPS</a></li>
    <li><a href="/devil.php#packages">PRICING</a></li>
    <?php else: ?>
    <li><a href="/about.php">ABOUT</a></li>
    <li><a href="/services.php">SERVICES</a></li>
    <li><a href="/portfolio.php">PORTFOLIO</a></li>
    <li><a href="/contact.php">CONTACT</a></li>
    <?php endif; ?>
  </ul>
  <div style="display:flex;align-items:center;gap:0.75rem;">
    <button class="nav-toggle-btn" id="mode-toggle-btn">
      <?php echo $is_devil ? 'EXIT HELL 👼' : 'DEVIL MODE 🔥'; ?>
    </button>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>
