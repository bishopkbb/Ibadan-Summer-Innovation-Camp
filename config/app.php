<?php
// ─── SMTP Mail (PHPMailer) ────────────────────────────────────────────────────
// Use your cPanel email credentials.
// Find the correct hostname in cPanel → Email Accounts → Connect Devices.
define('SMTP_HOST',      'mail.traceworka.ng');   // ← usually mail.yourdomain.com
define('SMTP_PORT',      587);                    // 587 = STARTTLS  |  465 = SSL
define('SMTP_ENCRYPTION','tls');                  // 'tls' for 587   |  'ssl' for 465
define('SMTP_USER',      'hello@traceworka.ng');  // full email address
define('SMTP_PASS',      '');                     // ← paste your email account password here
define('SMTP_FROM',      'hello@traceworka.ng');
define('SMTP_FROM_NAME', 'Ibadan Summer Innovation Camp');

// ─── Google Analytics 4 ──────────────────────────────────────────────────────
// 1. Go to analytics.google.com → Admin → Create Property
// 2. Copy the Measurement ID (format: G-XXXXXXXXXX) and paste below
define('GA_MEASUREMENT_ID', '');   // ← e.g. 'G-ABC123XYZ'

// ─── Seat Capacity ────────────────────────────────────────────────────────────
define('TOTAL_SEATS', 103);

// ─── Site URL (no trailing slash) ────────────────────────────────────────────
define('SITE_URL', 'https://ibadansummercamp.com');  // ← update to your live domain
