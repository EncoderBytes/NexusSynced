<?php
$body_class = '';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1>Our Services</h1>
    <p>Full-spectrum digital product development — from ideation to deployment.</p>
  </div>
</section>

<section class="prof-section">
  <div class="container">
    <div class="prof-label">/ WHAT WE DO</div>
    <h2 class="prof-title">Every Layer of<br>the Technology Stack</h2>
    <p class="prof-sub">We cover the full lifecycle of digital product development, backed by AI expertise and proven delivery.</p>
    <div class="services-grid">
      <div class="service-card">
        <div class="icon">🛠️</div>
        <h3>Custom Software Development</h3>
        <p>Tailored web applications, enterprise systems, API development, and internal tools. We use modern, scalable architectures that grow with your business.</p>
      </div>
      <div class="service-card">
        <div class="icon">🤖</div>
        <h3>AI & Machine Learning</h3>
        <p>From predictive models to NLP pipelines and computer vision — we integrate intelligence into your product. Custom model training, LLM integration, and automation.</p>
      </div>
      <div class="service-card">
        <div class="icon">📱</div>
        <h3>Mobile App Development</h3>
        <p>Native iOS (Swift) and Android (Kotlin) applications with clean architecture, offline support, push notifications, and seamless backend integration.</p>
      </div>
      <div class="service-card">
        <div class="icon">☁️</div>
        <h3>SaaS Platform Development</h3>
        <p>Multi-tenant cloud platforms with subscription billing, role-based access, analytics dashboards, and enterprise-grade security and compliance.</p>
      </div>
      <div class="service-card">
        <div class="icon">🎨</div>
        <h3>UI/UX Design</h3>
        <p>User research, information architecture, wireframing, high-fidelity prototyping, and visual design. We design for conversion, retention, and accessibility.</p>
      </div>
      <div class="service-card">
        <div class="icon">💡</div>
        <h3>Tech Consulting</h3>
        <p>Architecture reviews, technology selection, code audits, digital transformation strategy, and roadmap planning for technical leaders.</p>
      </div>
    </div>
  </div>
  </div>
</section>

<!-- Training Programs -->
<section class="prof-section" id="trainings" style="background:var(--bg-secondary);">
  <div class="container">
    <div class="prof-label">/ TRAINING PROGRAMS</div>
    <h2 class="prof-title">Empowering Women & Girls<br>Through Digital Education</h2>
    <p class="prof-sub">We deliver high-impact training programs in digital skills, cyber security, online safety, and privacy — designed for women, young girls, and NGO partners. Ready for institutional and corporate sponsorship.</p>

    <?php
    try {
        $tStmt = $pdo->query("SELECT * FROM trainings WHERE is_published = 1 ORDER BY sort_order ASC");
        $trainings = $tStmt->fetchAll();
    } catch (Exception $e) {
        $trainings = [];
    }
    $catIcons = ['digital_skills' => '💻', 'cyber_security' => '🛡️', 'online_safety' => '🔒', 'privacy' => '👁️', 'leadership' => '🌟', 'stem' => '🔬'];
    $catColors = ['digital_skills' => '#1E2946', 'cyber_security' => '#DC3545', 'online_safety' => '#28A745', 'privacy' => '#6F42C1', 'leadership' => '#FFA600', 'stem' => '#17A2B8'];
    $catLabels = ['digital_skills' => 'Digital Skills', 'cyber_security' => 'Cyber Security', 'online_safety' => 'Online Safety', 'privacy' => 'Privacy', 'leadership' => 'Leadership', 'stem' => 'STEM'];
    ?>

    <?php if (!empty($trainings)): ?>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1.5rem;margin-top:2rem;">
      <?php foreach ($trainings as $t): ?>
      <?php $icon = $catIcons[$t['category']] ?? '📘'; $color = $catColors[$t['category']] ?? '#1E2946'; ?>
      <div style="background:var(--bg-primary);border:1px solid var(--border);border-radius:12px;padding:2rem;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 30px rgba(0,0,0,0.06)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
        <div style="display:flex;align-items:flex-start;gap:1rem;">
          <div style="font-size:2rem;line-height:1;"><?php echo $icon; ?></div>
          <div>
            <div style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.5rem;">
              <span style="background:<?php echo $color; ?>;color:#fff;font-family:var(--font-mono);font-size:0.65rem;padding:0.2rem 0.6rem;border-radius:3px;font-weight:600;"><?php echo sanitize($catLabels[$t['category']] ?? $t['category']); ?></span>
              <?php if ($t['duration']): ?>
              <span style="font-family:var(--font-mono);font-size:0.65rem;color:var(--text-muted);"><?php echo sanitize($t['duration']); ?></span>
              <?php endif; ?>
            </div>
            <h3 style="font-family:var(--font-display);font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;"><?php echo sanitize($t['title']); ?></h3>
            <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.6;margin-bottom:0.75rem;"><?php echo sanitize($t['description']); ?></p>
            <?php if ($t['target_audience']): ?>
            <p style="font-family:var(--font-mono);font-size:0.7rem;color:var(--text-muted);">👥 <?php echo sanitize($t['target_audience']); ?></p>
            <?php endif; ?>
            <?php if ($t['learning_outcomes']): ?>
            <details style="margin-top:0.75rem;">
              <summary style="font-family:var(--font-mono);font-size:0.72rem;color:var(--brand-primary);cursor:pointer;font-weight:600;">View Learning Outcomes →</summary>
              <ul style="margin-top:0.5rem;padding-left:1.2rem;">
                <?php foreach (explode("\n", $t['learning_outcomes']) as $outcome): ?>
                <?php $outcome = trim($outcome); if ($outcome): ?>
                <li style="color:var(--text-secondary);font-size:0.82rem;line-height:1.6;"><?php echo sanitize($outcome); ?></li>
                <?php endif; endforeach; ?>
              </ul>
            </details>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1.5rem;margin-top:2rem;">
      <div style="background:var(--bg-primary);border:1px solid var(--border);border-radius:12px;padding:2rem;">
        <div style="display:flex;align-items:flex-start;gap:1rem;">
          <div style="font-size:2rem;line-height:1;">💻</div>
          <div>
            <div style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.5rem;">
              <span style="background:#1E2946;color:#fff;font-family:var(--font-mono);font-size:0.65rem;padding:0.2rem 0.6rem;border-radius:3px;font-weight:600;">Digital Skills</span>
              <span style="font-family:var(--font-mono);font-size:0.65rem;color:var(--text-muted);">6 Weeks</span>
            </div>
            <h3 style="font-family:var(--font-display);font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">Digital Skills for Women</h3>
            <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.6;">Comprehensive digital literacy program covering computer basics, internet navigation, online communication, document creation, and digital financial tools. Designed for women with limited prior exposure to technology.</p>
            <p style="font-family:var(--font-mono);font-size:0.7rem;color:var(--text-muted);margin-top:0.5rem;">👥 Women & young girls</p>
          </div>
        </div>
      </div>
      <div style="background:var(--bg-primary);border:1px solid var(--border);border-radius:12px;padding:2rem;">
        <div style="display:flex;align-items:flex-start;gap:1rem;">
          <div style="font-size:2rem;line-height:1;">🛡️</div>
          <div>
            <div style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.5rem;">
              <span style="background:#DC3545;color:#fff;font-family:var(--font-mono);font-size:0.65rem;padding:0.2rem 0.6rem;border-radius:3px;font-weight:600;">Cyber Security</span>
              <span style="font-family:var(--font-mono);font-size:0.65rem;color:var(--text-muted);">4 Weeks</span>
            </div>
            <h3 style="font-family:var(--font-display);font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">Cyber Security Awareness</h3>
            <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.6;">Practical cyber security training covering password hygiene, phishing detection, safe browsing, device security, and data protection. Real-world scenarios and hands-on exercises.</p>
            <p style="font-family:var(--font-mono);font-size:0.7rem;color:var(--text-muted);margin-top:0.5rem;">👥 Women, students, NGO staff</p>
          </div>
        </div>
      </div>
      <div style="background:var(--bg-primary);border:1px solid var(--border);border-radius:12px;padding:2rem;">
        <div style="display:flex;align-items:flex-start;gap:1rem;">
          <div style="font-size:2rem;line-height:1;">🔒</div>
          <div>
            <div style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.5rem;">
              <span style="background:#28A745;color:#fff;font-family:var(--font-mono);font-size:0.65rem;padding:0.2rem 0.6rem;border-radius:3px;font-weight:600;">Online Safety</span>
              <span style="font-family:var(--font-mono);font-size:0.65rem;color:var(--text-muted);">3 Weeks</span>
            </div>
            <h3 style="font-family:var(--font-display);font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">Online Safety for Young Girls</h3>
            <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.6;">Empowering young girls with the knowledge to navigate the internet safely. Covers social media privacy, recognizing online predators, cyberbullying response, and safe sharing practices.</p>
            <p style="font-family:var(--font-mono);font-size:0.7rem;color:var(--text-muted);margin-top:0.5rem;">👥 Teenage girls, young women</p>
          </div>
        </div>
      </div>
      <div style="background:var(--bg-primary);border:1px solid var(--border);border-radius:12px;padding:2rem;">
        <div style="display:flex;align-items:flex-start;gap:1rem;">
          <div style="font-size:2rem;line-height:1;">👁️</div>
          <div>
            <div style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.5rem;">
              <span style="background:#6F42C1;color:#fff;font-family:var(--font-mono);font-size:0.65rem;padding:0.2rem 0.6rem;border-radius:3px;font-weight:600;">Privacy</span>
              <span style="font-family:var(--font-mono);font-size:0.65rem;color:var(--text-muted);">2 Weeks</span>
            </div>
            <h3 style="font-family:var(--font-display);font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">Digital Privacy & Data Protection</h3>
            <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.6;">Understand your digital footprint, data rights, and how to protect personal information online. Covers privacy laws in Pakistan, social media data mining, and secure communication tools.</p>
            <p style="font-family:var(--font-mono);font-size:0.7rem;color:var(--text-muted);margin-top:0.5rem;">👥 General audience, professionals</p>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <div style="text-align:center;margin-top:3rem;padding:2rem;background:var(--brand-primary);border-radius:12px;">
      <h3 style="font-family:var(--font-display);font-weight:700;font-size:1.3rem;color:#fff;margin-bottom:0.75rem;">📋 Want to Partner With Us?</h3>
      <p style="color:rgba(255,255,255,0.8);font-size:0.9rem;max-width:500px;margin:0 auto 1.5rem;">NGOs, corporate sponsors, and institutions — we design custom training programs tailored to your community's needs.</p>
      <a href="/contact.php" class="btn-prof btn-prof-primary" style="display:inline-flex;">CONTACT US FOR PARTNERSHIP →</a>
    </div>
  </div>
</section>

<section class="prof-section">
  <div class="container">
    <div class="prof-label">/ OUR PROCESS</div>
    <h2 class="prof-title">How We Deliver</h2>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-top:2rem;">
      <div style="text-align:center;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🔍</div>
        <h3 style="font-family:var(--font-display);font-weight:700;font-size:1rem;margin-bottom:0.3rem;">Discovery</h3>
        <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.5;">We understand your vision, users, and market.</p>
      </div>
      <div style="text-align:center;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">✏️</div>
        <h3 style="font-family:var(--font-display);font-weight:700;font-size:1rem;margin-bottom:0.3rem;">Design</h3>
        <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.5;">Wireframes, prototypes, and visual design locked in.</p>
      </div>
      <div style="text-align:center;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">⚡</div>
        <h3 style="font-family:var(--font-display);font-weight:700;font-size:1rem;margin-bottom:0.3rem;">Build</h3>
        <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.5;">Agile development with continuous deployment.</p>
      </div>
      <div style="text-align:center;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">🚀</div>
        <h3 style="font-family:var(--font-display);font-weight:700;font-size:1rem;margin-bottom:0.3rem;">Launch</h3>
        <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.5;">Deployment, testing, and post-launch support.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
