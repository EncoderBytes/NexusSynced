<?php
$body_class = 'devil';
require_once __DIR__ . '/includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM worst_apps ORDER BY week_number ASC");
    $apps = $stmt->fetchAll();
} catch (Exception $e) {
    $apps = [];
}
?>

<section class="hero" style="min-height:60vh;padding-top:8rem;">
  <div class="hero-grid-bg"></div>
  <div class="hero-tag"><span class="dot"></span> <span class="mono">THE WORST APP SERIES</span></div>
  <h1 class="hero-title">
    <span class="accent2">WORST</span> IDEAS.<br>
    <span class="accent">SHIPPED</span> WEEKLY.
  </h1>
  <p class="hero-sub">Every week, one terrible idea becomes real. Some get bought. Some haunt the internet forever. All of them are magnificent failures.</p>
</section>

<section class="worst-section" style="padding-top:2rem;">
  <div style="max-width:var(--container-max);margin:0 auto;padding:0 2rem;">
    <div class="worst-grid" style="border:1px solid var(--border);">
      <?php if (!empty($apps)): ?>
        <?php foreach ($apps as $app): ?>
        <div class="worst-item">
          <div class="worst-num mono">#<?php echo sprintf('%03d', $app['week_number']); ?> — WEEK <?php echo $app['week_number']; ?></div>
          <div class="worst-name"><?php echo sanitize($app['title']); ?></div>
          <div class="worst-desc"><?php echo sanitize($app['description']); ?></div>
          <?php
          $statusLabel = 'IDEA ACCEPTED';
          $statusClass = 'ws-idea';
          if ($app['status'] === 'building') { $statusLabel = 'BUILDING NOW'; $statusClass = 'ws-building'; }
          elseif ($app['status'] === 'built') { $statusLabel = 'BUILT — AVAILABLE'; $statusClass = 'ws-built'; }
          elseif ($app['status'] === 'sold') { $statusLabel = 'BUILT + SOLD'; $statusClass = 'ws-built'; }
          ?>
          <span class="worst-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
          <div class="worst-price">
            <?php if ($app['status'] === 'sold' && $app['sold_price']): ?>
              SOLD — $<?php echo number_format($app['sold_price'], 2); ?>
            <?php elseif ($app['buy_price']): ?>
              BUY FOR $<?php echo number_format($app['buy_price'], 2); ?> →
            <?php else: ?>
              COMING SOON
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Static fallback -->
        <div class="worst-item">
          <div class="worst-num mono">#001 — WEEK 1</div>
          <div class="worst-name">Uncle Excuse Generator</div>
          <div class="worst-desc">AI that generates culturally accurate Pakistani family excuses for avoiding rishta meetings.</div>
          <span class="worst-status ws-built">BUILT + SOLD</span>
          <div class="worst-price">SOLD — $220</div>
        </div>
        <div class="worst-item">
          <div class="worst-num mono">#002 — WEEK 2</div>
          <div class="worst-name">Load Shedding Tracker</div>
          <div class="worst-desc">Hyperlocal WAPDA outage predictor with mood rating. "How angry is your neighbourhood?"</div>
          <span class="worst-status ws-built">BUILT + SOLD</span>
          <div class="worst-price">SOLD — $180</div>
        </div>
        <div class="worst-item">
          <div class="worst-num mono">#003 — WEEK 3</div>
          <div class="worst-name">Rate My Chai</div>
          <div class="worst-desc">Crowd-sourced chai shop rating app with a "dunki ratio" metric and late-night delivery mode.</div>
          <span class="worst-status ws-building">BUILDING NOW</span>
          <div class="worst-price" style="color:var(--brand-accent);">BUY FOR $150 →</div>
        </div>
        <div class="worst-item">
          <div class="worst-num mono">#004 — WEEK 4</div>
          <div class="worst-name">Rishta Swipe</div>
          <div class="worst-desc">Tinder but every match gets sent to your ammi first for approval. Two-stage consent flow.</div>
          <span class="worst-status ws-built">BUILT — AVAILABLE</span>
          <div class="worst-price">BUY FOR $200 →</div>
        </div>
        <div class="worst-item">
          <div class="worst-num mono">#005 — WEEK 5</div>
          <div class="worst-name">Biryani Dispute AI</div>
          <div class="worst-desc">AI mediator for settling Karachi vs Lahore biryani arguments. Peer-reviewed by aunties.</div>
          <span class="worst-status ws-idea">IDEA ACCEPTED</span>
          <div class="worst-price" style="color:var(--text-muted);">COMING SOON</div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div>
    <div class="footer-logo">NEXUS<span>SYNCED</span></div>
    <div class="footer-sub mono">MVP STUDIO · WORST APP SERIES · PAKISTAN</div>
    <div style="margin-top:1rem;"><span class="secp-badge mono">SECP REGISTERED · SMC · PVT LTD</span></div>
  </div>
  <div class="footer-links">
    <a href="/devil.php">← Back to Devil Mode</a>
    <a href="/index.php">Professional Mode</a>
  </div>
</footer>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
