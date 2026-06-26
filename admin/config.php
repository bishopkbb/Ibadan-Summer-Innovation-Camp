<?php
/* ============================================================
   Admin Panel — Credentials & Settings
   ============================================================
   FIRST-TIME SETUP:
   1. Open  /admin/setup-hash.php  in your browser
   2. Enter your chosen password and click Generate
   3. Copy the hash it shows
   4. Paste it below as ADMIN_PASS_HASH
   5. DELETE setup-hash.php from your server immediately after
   ============================================================ */

define('ADMIN_USER',      'admin');
define('ADMIN_PASS_HASH', '');          // ← paste your generated hash here

define('ADMIN_SESSION_KEY', 'isc_admin_auth_2026');
define('ADMIN_TITLE',       'ISC 2026 — Admin Panel');
define('CAMP_EMAIL',        'summercamp@traceworka.ng');
