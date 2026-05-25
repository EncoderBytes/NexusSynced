<?php
$body_class = '';
require_once __DIR__ . '/includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM portfolio WHERE is_published = 1 ORDER BY sort_order ASC");
    $items = $stmt->fetchAll();
} catch (Exception $e) {
    $items = [];
}

// Group by category for filter
$categories = ['web', 'mobile', 'ai', 'saas'];
?>

<section class="page-hero">
  <div class="container">
    <h1>Portfolio</h1>
    <p>A selection of products and platforms we've designed and built.</p>
  </div>
</section>

<section class="prof-section">
  <div class="container">
    <div class="prof-label">/ OUR WORK</div>
    <h2 class="prof-title">Featured Projects</h2>
    <p class="prof-sub">From web apps to AI platforms — here's what we've delivered.</p>

    <div class="portfolio-filters">
      <button class="active" data-filter="all">All</button>
      <button data-filter="web">Web</button>
      <button data-filter="mobile">Mobile</button>
      <button data-filter="ai">AI</button>
      <button data-filter="saas">SaaS</button>
    </div>

    <?php if (!empty($items)): ?>
    <div class="portfolio-grid">
      <?php foreach ($items as $item): ?>
      <div class="portfolio-card" data-category="<?php echo sanitize($item['category']); ?>">
        <div class="thumb">
          <?php if ($item['screenshot_url']): ?>
          <img src="<?php echo sanitize($item['screenshot_url']); ?>" alt="<?php echo sanitize($item['title']); ?>" loading="lazy">
          <?php else: ?>
          <span>📸 Screenshot</span>
          <?php endif; ?>
        </div>
        <div class="info">
          <span class="cat"><?php echo strtoupper(sanitize($item['category'])); ?></span>
          <h3><?php echo sanitize($item['title']); ?></h3>
          <p style="color:var(--text-secondary);font-size:0.82rem;line-height:1.5;margin-bottom:0.5rem;"><?php echo sanitize(substr($item['description'], 0, 120)); ?></p>
          <?php if ($item['tech_stack']): ?>
          <div class="tags">
            <?php foreach (explode(',', $item['tech_stack']) as $tag): ?>
            <span><?php echo sanitize(trim($tag)); ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <?php if ($item['demo_url']): ?><a href="<?php echo sanitize($item['demo_url']); ?>" target="_blank">View Project →</a><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="padding:3rem;text-align:center;border:1px dashed var(--border);border-radius:12px;">
      <p style="color:var(--text-muted);">Portfolio items are being added. Check back soon.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
