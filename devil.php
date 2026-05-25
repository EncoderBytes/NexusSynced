<?php
$body_class = 'devil';
require_once __DIR__ . '/includes/header.php';

// Fetch worst apps for display
try {
    $worstStmt = $pdo->query("SELECT * FROM worst_apps WHERE status != 'idea' ORDER BY week_number ASC LIMIT 5");
    $worstApps = $worstStmt->fetchAll();
} catch (Exception $e) {
    $worstApps = [];
}

$mvpsCount = getSetting($pdo, 'mvps_shipped_count') ?: '23';
$heroTagline = getSetting($pdo, 'devil_hero_tagline') ?: 'YOUR IDEA. LIVE IN 72 HOURS.';
$taglineParts = explode(' ', $heroTagline, 3);
?>

<!-- HERO -->
<section class="hero" id="top">
  <div class="hero-grid-bg"></div>
  <div class="hero-tag"><span class="dot"></span> <span class="mono">SECP REGISTERED · SMC · PAKISTAN</span></div>
  <h1 class="hero-title">
    <?php
    $words = explode(' ', $heroTagline);
    $total = count($words);
    foreach ($words as $i => $word):
      $cls = '';
      if ($i === $total - 1) $cls = 'accent2';
      elseif ($i === $total - 2) $cls = 'accent';
    ?>
    <span class="<?php echo $cls; ?>"><?php echo sanitize($word); ?></span><?php echo $i < $total - 1 ? ' ' : ''; ?>
    <?php endforeach; ?>
  </h1>
  <p class="hero-sub">We turn startup ideas into working MVPs — web, mobile, AI-powered. Fast enough to show investors. Sharp enough to win customers.</p>
  <div class="hero-actions">
    <button class="btn-primary" onclick="document.getElementById('submit').scrollIntoView({behavior:'smooth'})">SUBMIT YOUR IDEA</button>
    <button class="btn-outline" onclick="document.getElementById('worst').scrollIntoView({behavior:'smooth'})">SEE WORST APPS ↓</button>
  </div>
  <div class="hero-counter">
    <div class="num" id="mvp-counter"><?php echo sanitize($mvpsCount); ?></div>
    <div class="label mono">MVPs SHIPPED</div>
  </div>
</section>

<!-- TICKER -->
<div class="ticker">
  <div class="ticker-inner">
    <span class="ticker-item"><span>→</span>WEB APP</span>
    <span class="ticker-item"><span>→</span>MOBILE APP</span>
    <span class="ticker-item"><span>→</span>AI POWERED</span>
    <span class="ticker-item"><span>→</span>PITCH DECK</span>
    <span class="ticker-item"><span>→</span>MVP IN DAYS</span>
    <span class="ticker-item"><span>→</span>INVESTOR READY</span>
    <span class="ticker-item"><span>→</span>SECP REGISTERED</span>
    <span class="ticker-item"><span>→</span>72 HOUR BUILD</span>
    <span class="ticker-item"><span>→</span>WEB APP</span>
    <span class="ticker-item"><span>→</span>MOBILE APP</span>
    <span class="ticker-item"><span>→</span>AI POWERED</span>
    <span class="ticker-item"><span>→</span>PITCH DECK</span>
    <span class="ticker-item"><span>→</span>MVP IN DAYS</span>
    <span class="ticker-item"><span>→</span>INVESTOR READY</span>
    <span class="ticker-item"><span>→</span>SECP REGISTERED</span>
    <span class="ticker-item"><span>→</span>72 HOUR BUILD</span>
  </div>
</div>

<!-- SERVICES -->
<section id="services">
  <div class="section-tag">/ WHAT WE DO</div>
  <h2 class="section-title">TWO THINGS.<br>BOTH DANGEROUS.</h2>
  <p class="section-sub">Submit your idea. We build it. If you want it — buy it. If you don't, someone else will.</p>
  <div class="two-up">
    <div class="service-card">
      <div class="card-num mono">01 / MVP STUDIO</div>
      <div class="card-icon">⚡</div>
      <h3 class="card-title">Your Startup.<br>Built Fast.</h3>
      <p class="card-desc">Submit your startup idea. We design, develop, and deliver a working MVP — web app, mobile app, or AI SaaS — in days. Pitch deck included. Investor-ready from day one.</p>
      <span class="card-tag">WEB APP</span>
      <span class="card-tag">MOBILE</span>
      <span class="card-tag">AI / SAAS</span>
      <span class="card-tag">PITCH DECK</span>
      <div class="card-hover-line"></div>
    </div>
    <div class="service-card worst-card">
      <div class="card-num mono">02 / WORST APPS</div>
      <div class="card-icon" style="color:var(--brand-accent)">💀</div>
      <h3 class="card-title" style="color:var(--brand-accent);">Worst Idea?<br>We'll Build It.</h3>
      <p class="card-desc">Send us your most absurd, useless, or cursed app idea. We build it anyway. Every week, one terrible idea ships. If you want it — it's yours. If not — it lives in the Hall of Shame.</p>
      <span class="card-tag">WEEKLY DROP</span>
      <span class="card-tag">BUY THE SOURCE</span>
      <span class="card-tag">HALL OF SHAME</span>
      <div class="card-hover-line" style="background:var(--brand-accent);"></div>
    </div>
  </div>
</section>

<!-- QUEUE -->
<section id="queue" class="queue-section">
  <div class="section-tag">/ LIVE BUILD QUEUE</div>
  <h2 class="section-title">WATCH IT<br>HAPPEN.</h2>
  <p class="section-sub">Real-time visibility into every idea in the pipeline. Your idea gets a number. We ship in order.</p>
  <div class="queue-board" id="queue-board">
    <!-- Populated by JS -->
  </div>
</section>

<!-- PACKAGES -->
<section id="packages">
  <div class="section-tag">/ PRICING</div>
  <h2 class="section-title">PICK YOUR<br>WEAPON.</h2>
  <p class="section-sub">No retainers. No equity. Just flat packages that actually ship.</p>
  <div class="packages-grid">
    <div class="pkg">
      <div class="pkg-label mono">STARTER</div>
      <div class="pkg-name">Validate</div>
      <div class="pkg-sub">Prove the idea before spending big</div>
      <div class="pkg-price"><sup>$</sup>800</div>
      <div class="pkg-feature">Clickable web prototype</div>
      <div class="pkg-feature">Investor pitch deck (12 slides)</div>
      <div class="pkg-feature">Market size research</div>
      <div class="pkg-feature">Delivered in 48 hours</div>
      <button class="pkg-cta" onclick="document.getElementById('submit').scrollIntoView({behavior:'smooth'})">GET STARTED →</button>
    </div>
    <div class="pkg featured">
      <div class="pkg-label mono">MOST POPULAR</div>
      <div class="pkg-name">Launch</div>
      <div class="pkg-sub">Full MVP, ready to show investors</div>
      <div class="pkg-price"><sup>$</sup>4,500</div>
      <div class="pkg-feature">Web app MVP (functional)</div>
      <div class="pkg-feature">Mobile app (iOS + Android)</div>
      <div class="pkg-feature">AI feature integration</div>
      <div class="pkg-feature">Pitch deck + one-pager</div>
      <div class="pkg-feature">Delivered in 5–7 days</div>
      <button class="pkg-cta" onclick="document.getElementById('submit').scrollIntoView({behavior:'smooth'})">GET STARTED →</button>
    </div>
    <div class="pkg">
      <div class="pkg-label mono">RAISE READY</div>
      <div class="pkg-name">Raise</div>
      <div class="pkg-sub">Go to investors with everything loaded</div>
      <div class="pkg-price"><sup>$</sup>9,500</div>
      <div class="pkg-feature">Everything in Launch</div>
      <div class="pkg-feature">Demo video (2 min)</div>
      <div class="pkg-feature">Investor one-pager</div>
      <div class="pkg-feature">Financial model template</div>
      <div class="pkg-feature">Post-launch support (2 weeks)</div>
      <button class="pkg-cta" onclick="document.getElementById('submit').scrollIntoView({behavior:'smooth'})">GET STARTED →</button>
    </div>
  </div>
</section>

<!-- SUBMIT FORM -->
<section id="submit" class="submit-section">
  <div class="section-tag">/ SUBMIT YOUR IDEA</div>
  <h2 class="section-title">YOUR TURN.</h2>
  <div class="submit-grid">
    <div>
      <form id="idea-form">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <div class="form-group">
          <label class="form-label mono">Idea Type</label>
          <div class="radio-group">
            <div class="radio-btn active" id="r1" onclick="setRadio('r1')">MVP IDEA</div>
            <div class="radio-btn" id="r2" onclick="setRadio('r2')">WORST APP</div>
          </div>
          <input type="hidden" name="submission_type" value="mvp" id="submission_type">
        </div>
        <div class="form-group">
          <label class="form-label mono">Your Name</label>
          <input class="form-input" type="text" name="name" required placeholder="Ahmed / Sarah / whoever">
        </div>
        <div class="form-group">
          <label class="form-label mono">Email / WhatsApp</label>
          <input class="form-input" type="text" name="contact" required placeholder="+92 or email">
        </div>
        <div class="form-group">
          <label class="form-label mono">The Idea</label>
          <textarea class="form-textarea" name="idea_description" required placeholder="Describe your startup idea. Be specific — what problem, who's the user, what does it do?"></textarea>
        </div>
        <div class="form-group" id="package-group">
          <label class="form-label mono">Package</label>
          <select class="form-select" name="package">
            <option value="validate">Validate — $800</option>
            <option value="launch">Launch — $4,500</option>
            <option value="raise">Raise — $9,500</option>
            <option value="">Just browsing for now</option>
          </select>
        </div>
        <button type="submit" class="btn-primary" style="width:100%;padding:1rem">JOIN THE QUEUE →</button>
        <div class="form-message"></div>
      </form>
    </div>
    <div class="submit-info">
      <h3>How it works</h3>
      <div class="info-stat"><div class="stat-num">01</div><div class="stat-label mono">You submit the idea + pay deposit</div></div>
      <div class="info-stat"><div class="stat-num">02</div><div class="stat-label mono">You appear in the live queue</div></div>
      <div class="info-stat"><div class="stat-num">03</div><div class="stat-label mono">We build. You watch progress.</div></div>
      <div class="info-stat"><div class="stat-num">04</div><div class="stat-label mono">MVP delivered. Pitch deck ready.</div></div>
      <div style="border-top:1px solid var(--border);padding-top:2rem;margin-top:1rem">
        <div class="timeline-item"><div class="tl-num mono">48h</div><div class="tl-text">Validate package delivered</div></div>
        <div class="timeline-item"><div class="tl-num mono">5–7d</div><div class="tl-text">Launch package delivered</div></div>
        <div class="timeline-item"><div class="tl-num mono">10–14d</div><div class="tl-text">Raise package delivered</div></div>
        <div class="timeline-item"><div class="tl-num mono">If you pass →</div><div class="tl-text">Idea goes to next buyer in queue</div></div>
      </div>
    </div>
  </div>
</section>

<!-- WORST APPS -->
<section id="worst" class="worst-section">
  <div class="section-tag" style="color:var(--brand-accent);">/ THE WORST APP SERIES</div>
  <h2 class="section-title" style="color:var(--brand-accent);">BEAUTIFULLY<br>TERRIBLE.</h2>
  <p class="section-sub">Every week, one absurd idea ships. Some are bought. Some just exist. All of them are real.</p>
  <div class="worst-grid">
    <?php if (!empty($worstApps)): ?>
      <?php foreach ($worstApps as $app): ?>
      <div class="worst-item">
        <div class="worst-num mono"><?php echo sprintf('#%03d', $app['week_number']); ?> — WEEK <?php echo $app['week_number']; ?></div>
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
          <?php elseif ($app['status'] === 'built' && $app['buy_price']): ?>
            BUY FOR $<?php echo number_format($app['buy_price'], 2); ?> →
          <?php elseif ($app['status'] === 'building' && $app['buy_price']): ?>
            BUY FOR $<?php echo number_format($app['buy_price'], 2); ?> →
          <?php else: ?>
            COMING SOON
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Static fallback from seed data -->
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
    <div class="worst-item" style="display:flex;flex-direction:column;justify-content:center;align-items:center;border:1px dashed var(--border);cursor:pointer;min-height:200px;" onclick="document.getElementById('submit').scrollIntoView({behavior:'smooth'})">
      <div style="font-size:2rem;margin-bottom:1rem;">💀</div>
      <div style="font-weight:700;font-size:1rem;color:var(--brand-accent);font-family:var(--font-display);">SUBMIT YOURS</div>
      <div class="mono" style="font-size:0.72rem;color:var(--text-muted);margin-top:0.4rem;">IT MIGHT SHIP</div>
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
    <a href="#">Instagram</a>
    <a href="#">WhatsApp</a>
    <a href="#">Privacy</a>
    <a href="mailto:info@nexussynced.com">info@nexussynced.com</a>
  </div>
</footer>

<script>
// Override setRadio to update the hidden input
function setRadio(id) {
  document.querySelectorAll('.radio-btn').forEach(function(b) { b.classList.remove('active'); });
  document.getElementById(id).classList.add('active');
  document.getElementById('submission_type').value = (id === 'r2') ? 'worst_app' : 'mvp';
  var pkg = document.getElementById('package-group');
  if (pkg) pkg.style.display = (id === 'r2') ? 'none' : 'block';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
