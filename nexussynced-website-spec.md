# NexusSynced — Full Website Development Specification
> Hand this entire file to DeepSeek. Build exactly as specified.

---

## 1. Project Overview

**Company:** NexusSynced (SMC Private Limited, SECP Registered, Pakistan)
**Hosting:** Namecheap Shared Hosting (cPanel)
**Domain:** nexussynced.com
**Type:** Dual-personality software company website with lightweight admin panel

### Stack — MUST use this exactly (shared hosting constraints)
```
Frontend:  HTML5 + CSS3 + Vanilla JavaScript (no build tools, no npm)
Backend:   PHP 8.x (available on all Namecheap shared hosting)
Database:  MySQL (via cPanel MySQL Databases)
Styling:   Single CSS file with CSS custom properties for dual theming
Fonts:     Google Fonts (loaded via <link> in HTML)
Animation: Pure CSS animations + minimal vanilla JS
Email:     PHP mail() function OR PHPMailer (include via composer or manual)
Admin:     PHP session-based auth (no external auth service)
```

### What NOT to use
- No Node.js / Next.js / React / Vue / Angular
- No Supabase / Firebase / Vercel
- No npm / composer (except PHPMailer if needed — include manually)
- No heavy JS frameworks
- No build steps — files must work as-is when uploaded to cPanel

---

## 2. File & Folder Structure

```
public_html/
├── index.php                  (Professional Mode — home page)
├── about.php
├── services.php
├── portfolio.php
├── contact.php
├── devil.php                  (Devil Mode page)
├── worst-apps.php             (public Worst App Series gallery)
│
├── admin/
│   ├── index.php              (redirect to login if not authed)
│   ├── login.php
│   ├── dashboard.php
│   ├── submissions.php
│   ├── queue.php
│   ├── worst-apps.php
│   ├── portfolio.php
│   ├── contacts.php
│   ├── settings.php
│   └── logout.php
│
├── api/
│   ├── submit-idea.php        (handles idea/worst app form POST)
│   ├── contact.php            (handles contact form POST)
│   └── queue-data.php         (returns JSON of public queue items)
│
├── assets/
│   ├── css/
│   │   ├── main.css           (all styles, dual theme via CSS variables)
│   │   └── admin.css          (admin panel styles)
│   ├── js/
│   │   ├── main.js            (mode toggle, animations, queue polling)
│   │   └── admin.js           (admin panel interactions)
│   ├── img/
│   │   └── (screenshots, OG images, logo)
│   └── uploads/               (portfolio screenshots uploaded by admin)
│       └── .htaccess          (deny PHP execution in uploads folder)
│
├── includes/
│   ├── db.php                 (MySQL PDO connection)
│   ├── auth.php               (session check functions)
│   ├── functions.php          (shared helper functions)
│   └── header.php / footer.php (shared HTML partials)
│
└── .htaccess                  (URL rules, protect /admin, protect /includes)
```

---

## 3. Database Setup (MySQL)

Create via cPanel → MySQL Databases. One database, one user, full privileges.

### db.php connection
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_name');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die("DB connection failed.");
}
```

### MySQL Schema (run in cPanel → phpMyAdmin)

```sql
-- Idea submissions
CREATE TABLE submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  submission_type ENUM('mvp','worst_app') NOT NULL,
  name VARCHAR(100) NOT NULL,
  contact VARCHAR(150) NOT NULL,
  idea_title VARCHAR(200) NOT NULL,
  idea_description TEXT NOT NULL,
  package ENUM('validate','launch','raise') DEFAULT NULL,
  status ENUM('submitted','building','done','sold','rejected') DEFAULT 'submitted',
  queue_number INT UNIQUE,
  is_public TINYINT(1) DEFAULT 0,
  admin_notes TEXT,
  sale_price DECIMAL(10,2) DEFAULT NULL,
  sold_at DATETIME DEFAULT NULL
);

-- Worst App Series
CREATE TABLE worst_apps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  week_number INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  submission_id INT DEFAULT NULL,
  status ENUM('idea','building','built','sold') DEFAULT 'idea',
  buy_price DECIMAL(10,2) DEFAULT NULL,
  sold_price DECIMAL(10,2) DEFAULT NULL,
  sold_at DATETIME DEFAULT NULL,
  app_url VARCHAR(255) DEFAULT NULL,
  screenshot_url VARCHAR(255) DEFAULT NULL,
  is_featured TINYINT(1) DEFAULT 0,
  FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE SET NULL
);

-- Portfolio
CREATE TABLE portfolio (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  title VARCHAR(200) NOT NULL,
  category ENUM('web','mobile','ai','saas') NOT NULL,
  description TEXT NOT NULL,
  tech_stack VARCHAR(500),
  screenshot_url VARCHAR(255),
  demo_url VARCHAR(255),
  case_study_url VARCHAR(255),
  is_published TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0
);

-- Contact messages
CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  company VARCHAR(150) DEFAULT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0
);

-- Site settings
CREATE TABLE settings (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` TEXT NOT NULL,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin user
CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Seed settings
INSERT INTO settings (`key`, `value`) VALUES
  ('mvps_shipped_count', '23'),
  ('devil_hero_tagline', 'YOUR IDEA. LIVE IN 72 HOURS.'),
  ('devil_mode_enabled', '1'),
  ('instagram_url', ''),
  ('whatsapp_number', ''),
  ('admin_email', 'info@nexussynced.com'),
  ('secp_number', '');

-- Seed worst apps dummy data
INSERT INTO worst_apps (week_number, title, description, status, buy_price, is_featured) VALUES
  (1, 'Uncle Excuse Generator', 'AI that generates culturally accurate Pakistani family excuses for avoiding rishta meetings.', 'sold', 220.00, 1),
  (2, 'Load Shedding Tracker', 'Hyperlocal WAPDA outage predictor with mood rating. How angry is your neighbourhood?', 'built', 180.00, 0),
  (3, 'Rate My Chai', 'Crowd-sourced chai shop rating app with a dunki ratio metric and late-night delivery mode.', 'building', 150.00, 0),
  (4, 'Rishta Swipe', 'Tinder but every match gets sent to your ammi first for approval. Two-stage consent flow.', 'built', 200.00, 0),
  (5, 'Biryani Dispute AI', 'AI mediator for settling Karachi vs Lahore biryani arguments. Peer-reviewed by aunties.', 'idea', NULL, 0);

-- Create admin user (password: change this immediately after setup)
-- Run this separately after choosing your password:
-- INSERT INTO admin_users (email, password_hash) VALUES ('admin@nexussynced.com', password_hash('YOUR_PASSWORD_HERE', PASSWORD_BCRYPT));
```

**Note for DeepSeek:** To create the admin user, add a one-time setup script `admin/setup.php` that creates the admin account with `password_hash()`, then delete the file after running it once.

---

## 4. The Dual Mode Concept

Two complete personalities on the same website, toggled by one button always visible in the navbar.

### Mode A — Professional (Default)
- URL: `index.php`, `about.php`, `services.php`, `portfolio.php`, `contact.php`
- Brand: Navy `#1E2946` + Amber `#FFA600`
- Audience: Enterprise clients, investors, corporate Pakistan, international B2B
- Tone: Professional, credible, SECP-registered software house

### Mode B — Devil Mode
- URL: `devil.php`
- Brand: Near-black `#0A0A0A` + Red `#FF3D00` + Electric Yellow `#FFD600`
- Audience: Startup founders, hustlers, early-stage international companies
- Tone: Raw, dark, brutalist — "we ship while others sleep"
- Toggle button: **"DEVIL MODE 🔥"** on professional → **"EXIT HELL 👼"** on devil mode

**Toggle behavior:**
- Clicking the button saves preference to `localStorage`
- On professional pages: clicking navigates to `devil.php`
- On `devil.php`: clicking navigates to `index.php`
- Button is ALWAYS visible top-right on every page, every mode

---

## 5. CSS Theme System (main.css)

```css
/* PROFESSIONAL MODE — Default */
:root {
  --bg-primary: #ffffff;
  --bg-secondary: #F4F6FB;
  --bg-surface: #EEF1F8;
  --brand-primary: #1E2946;
  --brand-accent: #FFA600;
  --text-primary: #1E2946;
  --text-secondary: #5A6A8A;
  --text-muted: #9AA5BC;
  --border: #D8DEF0;
  --font-display: 'Plus Jakarta Sans', sans-serif;
  --font-body: 'Inter', sans-serif;
  --font-mono: 'JetBrains Mono', monospace;
}

/* DEVIL MODE — applied via <body class="devil"> on devil.php */
body.devil {
  --bg-primary: #0A0A0A;
  --bg-secondary: #111111;
  --bg-surface: #181818;
  --brand-primary: #FF3D00;
  --brand-accent: #FFD600;
  --text-primary: #F0EDE6;
  --text-secondary: #999999;
  --text-muted: #555555;
  --border: #2A2A2A;
  --font-display: 'Syne', sans-serif;
  --font-body: 'DM Sans', sans-serif;
  --font-mono: 'DM Mono', monospace;
}
```

**Google Fonts — load in `<head>` of all pages:**
```html
<!-- Professional fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<!-- Devil fonts -->
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
```

---

## 6. JavaScript — Mode Toggle (main.js)

```javascript
const DEVIL_KEY = 'nexus_mode';
const isDevilPage = document.body.classList.contains('devil');

function toggleMode() {
  if (isDevilPage) {
    localStorage.setItem(DEVIL_KEY, 'professional');
    window.location.href = 'index.php';
  } else {
    localStorage.setItem(DEVIL_KEY, 'devil');
    window.location.href = 'devil.php';
  }
}

// Transition overlay on toggle
document.getElementById('mode-toggle-btn').addEventListener('click', function() {
  const overlay = document.createElement('div');
  overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:#000;z-index:9999;opacity:0;transition:opacity 0.3s';
  document.body.appendChild(overlay);
  setTimeout(() => overlay.style.opacity = '1', 10);
  setTimeout(() => toggleMode(), 350);
});
```

---

## 7. Professional Mode Pages

### index.php — Home

**Navbar:**
- Logo: NEXUS**SYNCED** (bold differentiation), navy
- Links: About, Services, Portfolio, Contact
- Right: Devil Mode button (amber pill, "DEVIL MODE 🔥")
- Sticky on scroll

**Hero:**
- Full viewport height, navy background with subtle geometric pattern (CSS)
- Left: Big headline ("Building AI-Powered Digital Products With Purpose"), subtext, two CTAs: "See Our Work" + "Get In Touch"
- Right: CSS/SVG abstract grid or code animation
- Bottom stats strip: "SECP Registered · Women-Led · 5+ Years · 50+ Projects · Pakistan"

**About:**
- Two columns, text left, visual right
- "We are NexusSynced — a women-led AI software house registered with SECP, Pakistan. We build products for femtech, e-commerce, and social impact."
- Three pillars: Innovation / Impact / Speed
- SDG badges: SDG 4 / SDG 5 / SDG 8 / SDG 9

**Services (6 cards grid):**
1. Custom Software Development
2. AI & Machine Learning Products
3. Mobile App Development (iOS + Android)
4. SaaS Platform Development
5. UI/UX Design
6. Tech Consulting

**Products (3 cards):**
1. Cycle Sync — AI period tracking app (Live)
2. Amazon AI Content Generator (Beta)
3. AI Domain Name Generator (Beta)

**Portfolio:**
- Grid pulled from `portfolio` WHERE `is_published = 1` ORDER BY `sort_order`
- Filter tabs: All / Web / Mobile / AI / SaaS (JS filter, no page reload)
- Each card: screenshot, title, category badge, tech stack tags, "View →"

**Contact:**
- Split: form left (Name, Email, Company, Message → POST to `api/contact.php`), info right
- Success/error message shown inline after submission

**Footer:**
- Logo, tagline, links, SECP badge, © 2025 NexusSynced SMC Pvt Ltd

### about.php, services.php, portfolio.php, contact.php
- Standard inner pages, same navbar/footer
- All use the professional color scheme
- Devil Mode toggle button always in navbar

---

## 8. Devil Mode — devil.php

`devil.php` has `<body class="devil">` so all CSS variables switch to dark theme automatically.

### Navbar (Devil)
- Logo white with red accent
- Links: Services, Queue, Worst Apps, Pricing
- Right: "EXIT HELL 👼" button (white outline)

### Hero
- Full viewport, near-black background
- Subtle dot-grid CSS background pattern (very faint)
- Green pulsing dot badge: "SECP REGISTERED · SMC · PAKISTAN"
- Massive staggered headline (CSS animation on load): "YOUR IDEA. / LIVE IN / 72 HOURS."
- Sub: "We turn startup ideas into working MVPs — web, mobile, AI-powered. Fast enough for investors."
- Two buttons: "SUBMIT YOUR IDEA" (red filled) + "SEE WORST APPS ↓" (outline)
- Counter bottom-right: pulls `mvps_shipped_count` from settings via `api/queue-data.php`
- CSS marquee ticker below hero: "WEB APP → MOBILE APP → AI POWERED → PITCH DECK → MVP IN DAYS →"

### Services (two big cards)
**Card 01 — MVP Studio:**
- Title: "Your Startup. Built Fast."
- Tags: WEB APP / MOBILE / AI SAAS / PITCH DECK
- Bottom hover: red line sweep (CSS)

**Card 02 — Worst App Series** (yellow 2px top border):
- Title: "Worst Idea? We'll Build It."
- Tags: WEEKLY DROP / BUY THE SOURCE / HALL OF SHAME
- Bottom hover: yellow line sweep (CSS)

### Live Queue
Four columns: Submitted / Building Now / Done — Available / Sold

Data: fetched from `api/queue-data.php` via `fetch()` on page load, then every 30 seconds.

`api/queue-data.php` returns:
```json
{
  "submissions": [...],
  "mvps_count": "23"
}
```
Only returns rows where `is_public = 1`.

Each card shows: queue number, idea title, category, type badge (WEB/MOBILE/AI/WORST).
"Building Now" cards show animated CSS progress bar.

JS renders the columns dynamically from the JSON response.

### Packages (3 columns)

| | Validate | Launch ⭐ | Raise |
|---|---|---|---|
| Price | $800 | $4,500 | $9,500 |
| Delivery | 48 hours | 5–7 days | 10–14 days |
| Clickable prototype | ✓ | ✓ | ✓ |
| Pitch deck | ✓ | ✓ | ✓ |
| Web app MVP | — | ✓ | ✓ |
| Mobile app | — | ✓ | ✓ |
| AI integration | — | ✓ | ✓ |
| Investor one-pager | — | — | ✓ |
| Demo video | — | — | ✓ |
| Financial model | — | — | ✓ |
| Post-launch support | — | — | 2 weeks |

Launch is featured (red border, "MOST POPULAR" badge).

### Idea Submission Form
POST to `api/submit-idea.php`

Fields:
- Idea Type: radio toggle (MVP IDEA / WORST APP)
- Name
- Email / WhatsApp
- The Idea (textarea)
- Package (dropdown — only shown for MVP type, hidden for Worst App via JS)
- Submit: "JOIN THE QUEUE →"

On success: show confirmation message inline — "You're #[X] in the queue. We'll be in touch within 24 hours."

### Worst App Series Section
Grid of cards (3 cols) from `worst_apps` WHERE `status != 'idea'` + one "submit yours" dashed card.
Links to `worst-apps.php` for full gallery.

---

## 9. Backend API Files

### api/submit-idea.php
```php
<?php
// Accepts POST, validates input, inserts into submissions table
// Auto-assigns next queue_number (MAX(queue_number) + 1)
// Sends email notification to admin
// Returns JSON: {"success": true, "queue_number": 42}
// Use PHP mail() or PHPMailer
```

### api/contact.php
```php
<?php
// Accepts POST, validates, inserts into contacts table
// Sends email to admin
// Returns JSON: {"success": true}
```

### api/queue-data.php
```php
<?php
// Returns JSON of all submissions where is_public = 1
// Also returns mvps_count from settings
// No auth required — public endpoint
// Set header: Content-Type: application/json
// Cache-Control: no-cache
```

---

## 10. Admin Panel (admin/)

### Auth (auth.php)
```php
<?php
session_start();
function requireAdmin() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}
```

Every admin page starts with:
```php
<?php
require_once '../includes/auth.php';
requireAdmin();
```

### admin/login.php
- Centered form: email + password
- On POST: verify with `password_verify()` against `admin_users.password_hash`
- On success: set `$_SESSION['admin_id']`, redirect to `admin/dashboard.php`
- On fail: show error message

### admin/dashboard.php
Stats cards (pull from DB):
- Total submissions this month
- Currently building (status = 'building')
- MVPs shipped (from settings)
- Unread contacts
- Revenue (sum of sale_price where status = 'sold')

Recent 10 submissions table below stats.

### admin/submissions.php
Full table with columns: # | Queue No | Name | Contact | Type | Idea | Package | Status | Date | Actions

Actions per row:
- Change status (inline dropdown + save button → AJAX POST)
- Toggle is_public (checkbox)
- View details (expand row or modal)
- Add/edit admin notes
- Mark sold (enter price, set sold_at)
- Delete (with confirm)

Filters at top: All / MVP / Worst App / By Status
Search bar: live filter by name or idea title (JS, no reload)

### admin/queue.php
Visual Kanban board (4 columns: Submitted / Building / Done / Sold).
Cards show: queue number, title, type, public/private badge.
Each card has: Change Status dropdown + Update button + Toggle Public checkbox.
Note: drag-and-drop is optional (JS). If not implementing drag, use the dropdown.

### admin/worst-apps.php
Table view of all worst_apps entries.

Add New form (at top of page):
- Week number, Title, Description, Status, Buy price, App URL, Screenshot upload, Featured toggle
- Screenshot uploads to `assets/uploads/` via PHP move_uploaded_file()

Edit / Delete each entry.

### admin/portfolio.php
Same pattern as worst-apps — table + add/edit/delete form.
Screenshot upload to `assets/uploads/`.
Sort order: number input field.

### admin/contacts.php
Table: Name | Email | Company | Date | Read? | Actions
Actions: Mark read, View full message (expand), Delete.
Unread rows highlighted with left border color.

### admin/settings.php
Simple form with all settings key-value pairs as labeled inputs.
On submit: UPDATE each setting in the `settings` table.
Fields:
- MVPs Shipped Count
- Devil Mode Hero Tagline
- Devil Mode Enabled (checkbox)
- Instagram URL
- WhatsApp Number
- Admin Email (for notifications)
- SECP Registration Number

---

## 11. Email Notifications (PHP)

Use PHP `mail()` function. If it doesn't work on Namecheap (some shared hosts restrict it), include PHPMailer manually (download PHPMailer, put in `includes/phpmailer/`).

**New submission email:**
```
To: admin email from settings table
Subject: New [MVP/Worst App] Submission — Queue #[number]
Body:
  Name: [name]
  Contact: [contact]
  Type: [type]
  Package: [package]
  Idea: [description]
  Submitted: [timestamp]
```

**New contact email:**
```
To: admin email from settings
Subject: New Contact Message from [name]
Body: [full message details]
```

---

## 12. Security Requirements

- All DB queries use PDO prepared statements — NO raw string interpolation
- All form inputs sanitized with `htmlspecialchars()` before display
- Admin panel fully protected by session check on every page
- Upload directory (`assets/uploads/`) has `.htaccess` to block PHP execution:
  ```
  <Files "*.php">
    Deny from all
  </Files>
  ```
- `includes/` directory has `.htaccess`:
  ```
  Deny from all
  ```
- CSRF: add a CSRF token to all forms (generate in session, verify on POST)
- Admin password stored with `password_hash()` using `PASSWORD_BCRYPT`

---

## 13. .htaccess (root)

```apache
Options -Indexes
RewriteEngine On

# Remove .php extension from URLs (optional, clean URLs)
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}.php -f
RewriteRule ^(.+)$ $1.php [L,QSA]

# Protect includes and admin config
RewriteRule ^includes/ - [F,L]
```

---

## 14. SEO & Meta Tags

### Professional pages:
```html
<title>NexusSynced — AI-Powered Software Development | Pakistan</title>
<meta name="description" content="NexusSynced is a SECP-registered women-led software house building AI-powered digital products for startups and enterprises. Based in Pakistan.">
<meta property="og:title" content="NexusSynced — AI-Powered Software Development">
<meta property="og:image" content="/assets/img/og-professional.jpg">
```

### Devil mode:
```html
<title>NexusSynced Devil Mode — MVP in 72 Hours</title>
<meta name="description" content="Turn your startup idea into a working MVP in 72 hours. Web app, mobile app, AI-powered. Pitch deck included. Built by NexusSynced.">
<meta property="og:title" content="Your Idea. Live in 72 Hours.">
<meta property="og:image" content="/assets/img/og-devil.jpg">
```

Create two OG images (1200x630px): one navy/professional, one dark/red for devil mode.

---

## 15. Responsive Breakpoints

| Breakpoint | Width |
|---|---|
| Mobile | < 768px |
| Tablet | 768px – 1024px |
| Desktop | > 1024px |

Mobile adjustments:
- Queue board: horizontal scroll with snap (`overflow-x: auto; scroll-snap-type: x mandatory`)
- Service cards: stack to 1 column
- Packages: stack to 1 column
- Navbar: hamburger menu, devil mode button always visible (floating pill if hamburger is open)

---

## 16. Performance

- No JS frameworks — vanilla only, page loads fast on shared hosting
- Images: use `loading="lazy"` on all `<img>` tags
- Google Fonts: add `&display=swap` to all font URLs
- Queue fetch: use `fetch()` with 30s polling, pause with Page Visibility API:
```javascript
document.addEventListener('visibilitychange', () => {
  if (document.hidden) clearInterval(queueInterval);
  else startQueuePolling();
});
```
- Admin panel loads no external JS libraries (no jQuery needed)

---

## 17. Deployment Steps (Namecheap cPanel)

1. Create MySQL database + user in cPanel → MySQL Databases
2. Run SQL schema in cPanel → phpMyAdmin
3. Update `includes/db.php` with correct credentials
4. Upload all files via cPanel File Manager or FTP
5. Set `assets/uploads/` folder permissions to `755`
6. Visit `admin/setup.php` once to create admin account, then DELETE that file
7. Test submission form, test admin login, test mode toggle
8. Add domain email (info@nexussynced.com) in cPanel → Email Accounts for PHP mail() to work

---

## 18. Notes for DeepSeek

1. **The devil mode toggle button is the single most important UI element.** It must appear on every page, every mode, always in the top-right of the navbar. Never hide it.

2. **The live queue is the heart of `devil.php`.** The JS fetches from `api/queue-data.php` on load and every 30 seconds. Make the columns look alive — pulsing dot on "Building Now", green dot on "Done", etc.

3. **No build steps.** Every file must work by uploading directly to cPanel. No webpack, no npm, no compilation.

4. **PHP sessions for admin auth.** Simple, reliable, works on every shared host. `session_start()` at the top of every admin page.

5. **The two modes have completely different visual personalities.** Professional = navy + amber, clean, corporate. Devil = near-black + red + yellow, dark, aggressive. The CSS variable swap on `body.devil` handles this.

6. **Queue numbers are permanent and public-facing.** Never reuse. Auto-increment by taking `MAX(queue_number) + 1` on insert.

7. **Admin panel style = professional mode colors.** Clean, navy, amber. No dark theme in admin.

8. **Worst App Series seed data uses Pakistani cultural humor** — Uncle Excuse Generator, Load Shedding Tracker, Rate My Chai, Rishta Swipe, Biryani Dispute AI. These are the first 5 entries.

9. **All form submissions show inline success/error messages** — no page reload for the user experience. Use `fetch()` to POST to the API files and update the DOM on response.

10. **PHPMailer note:** If `mail()` is unreliable on Namecheap, download PHPMailer from GitHub, put the `src/` folder in `includes/phpmailer/`, and use SMTP with the cPanel email account credentials.
