<?php
$body_class = '';
require_once __DIR__ . '/includes/header.php';
?>
<!-- Hero -->
<section class="prof-hero">
  <div class="container">
    <div>
      <h1>Building AI-Powered Digital Products With Purpose</h1>
      <p>NexusSynced is a SECP-registered women-led software house. We build products for femtech, e-commerce, and social impact — from concept to scale.</p>
      <div class="prof-hero-actions">
        <a href="/portfolio.php" class="btn-prof btn-prof-primary">SEE OUR WORK →</a>
        <a href="/contact.php" class="btn-prof btn-prof-outline">GET IN TOUCH</a>
      </div>
    </div>
    <div class="prof-hero-visual">
      <div class="grid-animation"></div>
    </div>
  </div>
</section>

<div class="prof-stats-bar">
  <span>SECP Registered</span>
  <span>·</span>
  <span>Women-Led</span>
  <span>·</span>
  <span>5+ Years</span>
  <span>·</span>
  <span>50+ Projects</span>
  <span>·</span>
  <span>Pakistan</span>
</div>

<!-- About Section -->
<section class="prof-section">
  <div class="container about-grid">
    <div>
      <div class="prof-label">/ ABOUT</div>
      <h2 class="prof-title">Built Different.<br>Built With Purpose.</h2>
      <p>We are NexusSynced — a women-led AI software house registered with SECP, Pakistan. We build products for femtech, e-commerce, and social impact. Our team combines deep technical expertise with a commitment to building technology that makes a real difference.</p>
      <p>From startups to enterprise clients, we deliver AI-powered digital products that are robust, scalable, and purpose-driven.</p>
      <div class="sdg-badges">
        <span class="sdg-badge">SDG 4</span>
        <span class="sdg-badge">SDG 5</span>
        <span class="sdg-badge">SDG 8</span>
        <span class="sdg-badge">SDG 9</span>
      </div>
    </div>
    <div>
      <div class="pillars">
        <div class="pillar">
          <h4>Innovation</h4>
          <p>Cutting-edge AI solutions tailored to real-world needs</p>
        </div>
        <div class="pillar">
          <h4>Impact</h4>
          <p>Technology that drives social and economic change</p>
        </div>
        <div class="pillar">
          <h4>Speed</h4>
          <p>Rapid delivery without compromising on quality</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Services Section -->
<section class="prof-section">
  <div class="container">
    <div class="prof-label">/ WHAT WE DO</div>
    <h2 class="prof-title">Full-Spectrum<br>Digital Product Development</h2>
    <p class="prof-sub">From concept to deployment, we cover every layer of the technology stack.</p>
    <div class="services-grid">
      <div class="service-card">
        <div class="icon">🛠️</div>
        <h3>Custom Software Development</h3>
        <p>Tailored web applications, enterprise systems, and internal tools built with modern architectures.</p>
      </div>
      <div class="service-card">
        <div class="icon">🤖</div>
        <h3>AI & Machine Learning</h3>
        <p>Intelligent automation, predictive models, NLP solutions, and computer vision products.</p>
      </div>
      <div class="service-card">
        <div class="icon">📱</div>
        <h3>Mobile App Development</h3>
        <p>Native iOS and Android applications with polished UX and robust backend integration.</p>
      </div>
      <div class="service-card">
        <div class="icon">☁️</div>
        <h3>SaaS Platform Development</h3>
        <p>Multi-tenant cloud platforms with billing, analytics, and enterprise-grade security.</p>
      </div>
      <div class="service-card">
        <div class="icon">🎨</div>
        <h3>UI/UX Design</h3>
        <p>User research, wireframing, prototyping, and visual design that converts and retains.</p>
      </div>
      <div class="service-card">
        <div class="icon">💡</div>
        <h3>Tech Consulting</h3>
        <p>Architecture review, technology strategy, and roadmap planning for digital transformation.</p>
      </div>
    </div>
  </div>
</section>

<!-- Products Section -->
<section class="prof-section">
  <div class="container">
    <div class="prof-label">/ OUR PRODUCTS</div>
    <h2 class="prof-title">Built In-House.<br>Shipping to Market.</h2>
    <p class="prof-sub">Products we've conceptualized, built, and launched ourselves.</p>
    <div class="products-grid">
      <div class="product-card">
        <span class="badge">LIVE</span>
        <h3>Cycle Sync</h3>
        <p>AI-powered period tracking and reproductive health app with predictive cycle analytics.</p>
      </div>
      <div class="product-card">
        <span class="badge">BETA</span>
        <h3>Amazon AI Content Generator</h3>
        <p>Automated product listings, SEO-optimized descriptions, and A+ content using generative AI.</p>
      </div>
      <div class="product-card">
        <span class="badge">BETA</span>
        <h3>AI Domain Name Generator</h3>
        <p>Smart domain suggestion tool using NLP to generate memorable, brandable domain names.</p>
      </div>
    </div>
  </div>
</section>

<!-- Portfolio Highlights -->
<section class="prof-section">
  <div class="container">
    <div class="prof-label">/ PORTFOLIO</div>
    <h2 class="prof-title">Recent Work</h2>
    <p class="prof-sub">A selection of projects we've delivered across web, mobile, AI, and SaaS.</p>

    <?php
    try {
        $stmt = $pdo->query("SELECT * FROM portfolio WHERE is_published = 1 ORDER BY sort_order ASC LIMIT 6");
        $items = $stmt->fetchAll();
    } catch (Exception $e) {
        $items = [];
    }
    ?>

    <?php if (!empty($items)): ?>
    <div class="portfolio-grid" id="portfolio-grid-home">
      <?php foreach ($items as $item): ?>
      <div class="portfolio-card" data-category="<?php echo sanitize($item['category']); ?>">
        <div class="thumb">
          <?php if ($item['screenshot_url']): ?>
          <img src="<?php echo sanitize($item['screenshot_url']); ?>" alt="<?php echo sanitize($item['title']); ?>" loading="lazy">
          <?php else: ?>
          <span>📸</span>
          <?php endif; ?>
        </div>
        <div class="info">
          <span class="cat"><?php echo strtoupper(sanitize($item['category'])); ?></span>
          <h3><?php echo sanitize($item['title']); ?></h3>
          <?php if ($item['tech_stack']): ?>
          <div class="tags">
            <?php foreach (explode(',', $item['tech_stack']) as $tag): ?>
            <span><?php echo sanitize(trim($tag)); ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <?php if ($item['demo_url']): ?><a href="<?php echo sanitize($item['demo_url']); ?>" target="_blank">View →</a><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:var(--text-muted);">Portfolio items coming soon.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Contact Section -->
<section class="prof-section">
  <div class="container contact-grid">
    <div>
      <div class="prof-label">/ CONTACT</div>
      <h2 class="prof-title">Let's Build<br>Something Together.</h2>
      <p class="prof-sub" style="margin-bottom:2rem;">Tell us about your project. We'll get back to you within 24 hours.</p>
      <form id="contact-form">
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" required placeholder="Your name">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required placeholder="your@email.com">
        </div>
        <div class="form-group">
          <label>Company</label>
          <input type="text" name="company" placeholder="Company name (optional)">
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea name="message" required placeholder="Tell us about your project..."></textarea>
        </div>
        <button type="submit" class="btn-prof btn-prof-primary">SEND MESSAGE</button>
        <div class="form-message"></div>
      </form>
    </div>
    <div class="contact-info">
      <h3>Get In Touch</h3>
      <p>We're always open to discussing new projects, creative ideas, or opportunities to be part of your vision.</p>
      <div class="item">
        <span class="icon">📧</span>
        <div>
          <strong>Email</strong><br>
          <span style="color:var(--text-secondary);font-size:0.85rem;">info@nexussynced.com</span>
        </div>
      </div>
      <div class="item">
        <span class="icon">📍</span>
        <div>
          <strong>Location</strong><br>
          <span style="color:var(--text-secondary);font-size:0.85rem;">Pakistan — Serving Globally</span>
        </div>
      </div>
      <div class="item">
        <span class="icon">🏛️</span>
        <div>
          <strong>Registration</strong><br>
          <span style="color:var(--text-secondary);font-size:0.85rem;">SECP Registered · SMC · Pvt Ltd</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="prof-footer">
  <div class="container">
    <div>
      <div class="f-logo">NEXUS<span>SYNCED</span></div>
      <p>Building AI-powered digital products with purpose. SECP-registered women-led software house based in Pakistan.</p>
      <div style="margin-top:1rem;">
        <span class="secp-badge" style="border-color:rgba(255,255,255,0.2);color:rgba(255,255,255,0.5);">SECP REGISTERED · SMC · PVT LTD</span>
      </div>
    </div>
    <div class="f-links">
      <a href="/about.php">About</a>
      <a href="/services.php">Services</a>
      <a href="/portfolio.php">Portfolio</a>
      <a href="/contact.php">Contact</a>
      <a href="/devil.php">Devil Mode</a>
    </div>
    <div class="f-bottom">© 2025 NexusSynced SMC Pvt Ltd. All rights reserved.</div>
  </div>
</footer>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
