<?php
$body_class = '';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1>Contact Us</h1>
    <p>Have a project in mind? Let's talk.</p>
  </div>
</section>

<section class="prof-section">
  <div class="container contact-grid">
    <div>
      <div class="prof-label">/ LET'S TALK</div>
      <h2 class="prof-title">Start Your<br>Project Today</h2>
      <p class="prof-sub" style="margin-bottom:2rem;">Fill in the form and we'll get back to you within 24 hours. We're excited to hear about your idea.</p>
      <form id="contact-form">
        <div class="form-group">
          <label>Your Name *</label>
          <input type="text" name="name" required placeholder="Full name">
        </div>
        <div class="form-group">
          <label>Email Address *</label>
          <input type="email" name="email" required placeholder="your@email.com">
        </div>
        <div class="form-group">
          <label>Company</label>
          <input type="text" name="company" placeholder="Company name (optional)">
        </div>
        <div class="form-group">
          <label>Message *</label>
          <textarea name="message" required placeholder="Describe your project, budget, timeline, and any specific requirements..."></textarea>
        </div>
        <button type="submit" class="btn-prof btn-prof-primary">SEND MESSAGE</button>
        <div class="form-message"></div>
      </form>
    </div>
    <div class="contact-info">
      <h3>Contact Information</h3>
      <p>We're always available for a chat. Reach out through any of these channels.</p>
      <div class="item">
        <span class="icon">📧</span>
        <div>
          <strong>Email</strong><br>
          <span style="color:var(--text-secondary);font-size:0.85rem;">info@nexussynced.com</span>
        </div>
      </div>
      <div class="item">
        <span class="icon">💬</span>
        <div>
          <strong>WhatsApp</strong><br>
          <span style="color:var(--text-secondary);font-size:0.85rem;">
            <?php
            $wa = getSetting($pdo, 'whatsapp_number');
            echo $wa ? sanitize($wa) : 'Available on request';
            ?>
          </span>
        </div>
      </div>
      <div class="item">
        <span class="icon">🌐</span>
        <div>
          <strong>Instagram</strong><br>
          <span style="color:var(--text-secondary);font-size:0.85rem;">
            <?php
            $ig = getSetting($pdo, 'instagram_url');
            echo $ig ? '<a href="' . sanitize($ig) . '" target="_blank" style="color:var(--brand-accent);">@nexussynced</a>' : 'Coming soon';
            ?>
          </span>
        </div>
      </div>
      <div class="item">
        <span class="icon">🏛️</span>
        <div>
          <strong>Registration</strong><br>
          <span style="color:var(--text-secondary);font-size:0.85rem;">
            <?php
            $secp = getSetting($pdo, 'secp_number');
            echo 'SECP Registered · SMC · Pvt Ltd' . ($secp ? ' (' . sanitize($secp) . ')' : '');
            ?>
          </span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
