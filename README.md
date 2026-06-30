# Ibadan Summer Innovation Camp 2026

Official website and registration system for the **Ibadan Summer Innovation Camp 2026** — a 4-week transformational holiday programme for children and teenagers aged 7–18, organised by [Traceworka Innovative Solutions Limited](https://traceworka.ng), Ibadan, Oyo State, Nigeria.

**Live site:** [https://traceworka.ng/summercamp](https://traceworka.ng/summercamp)

---

## Table of Contents

- [Overview](#overview)
- [Camp Details](#camp-details)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Installation & Setup](#installation--setup)
- [Admin Panel](#admin-panel)
- [Database Schema](#database-schema)
- [Security](#security)
- [Responsive Design](#responsive-design)
- [Learning Tracks](#learning-tracks)
- [Pricing Packages](#pricing-packages)
- [Contact & Support](#contact--support)

---

## Overview

This repository contains the complete front-end and back-end source code for the Ibadan Summer Innovation Camp 2026 website. It is a custom-built PHP/MySQL web application that handles:

- Public-facing marketing pages (homepage, learning tracks, pricing, contact, how-to-attend)
- A dynamic multi-child online registration form with per-child Camp IDs and mode-of-instruction selection
- A contact/enquiry form
- A password-protected admin dashboard for managing registrations and messages
- Automated SMTP email confirmations sent to parents upon registration, and on status change (confirmed/cancelled)
- CSV export of registration data

The site is built without any PHP framework — pure PHP 8.2+, vanilla JavaScript, jQuery, and Bootstrap 4 — making it straightforward to deploy on any standard cPanel shared hosting environment.

---

## Camp Details

| Field | Details |
|---|---|
| **Camp Name** | Ibadan Summer Innovation Camp 2026 |
| **Organiser** | Traceworka Innovative Solutions Limited |
| **Venue** | No 6, Hon Tunde Sarumi Close, Off Adenuga Street, Kongi-Bodija, Ibadan, Oyo State |
| **Dates** | August 3 – August 27, 2026 |
| **Schedule** | Monday – Thursday, 9:00 AM – 3:00 PM daily |
| **Duration** | 4 weeks (16 sessions) |
| **Age Range** | 7 – 18 years (as of camp start date) |
| **Total Capacity** | 100 seats |
| **Email** | summercamp@traceworka.ng |
| **Phone** | +234 907 154 3344 |
| **Live Site** | [https://traceworka.ng/summercamp](https://traceworka.ng/summercamp) |

### Age Groups

| Group | Age Range | Description |
|---|---|---|
| Junior Innovators | 7 – 10 years | Foundation-level learning activities |
| Young Creators | 11 – 14 years | Intermediate skills and project work |
| Future Leaders | 15 – 18 years | Advanced tracks and entrepreneurship |

---

## Features

### Public Website

- **Hero slider** — Full-screen Swiper.js carousel with animated text, countdown timer to camp start, and live available-seats counter pulled from the database
- **Camp Highlights strip** — Quick-glance info cards (age range, dates, learning tracks, security)
- **About section** — Camp mission, vision, and programme overview
- **How to Attend** — Side-by-side Physical and Virtual attendance option cards with descriptions
- **Learning Tracks** — Four programme tracks displayed as interactive cards
- **Why Choose Us** — Reasons to register with icon cards
- **Camp Journey** — Four-week visual breakdown of what children experience each week
- **Pricing section** — Three-tier package table with Early Bird countdown and family/group discount section
- **Registration page** — Dynamic multi-child form (1–4 children per submission)
- **Get In Touch** — Contact cards for phone, email, and location
- **Sticky navbar** — Header transitions to a fixed dark-navy bar on scroll
- **Mobile menu** — Theme-native slide-in navigation overlay

### Registration System

- Supports registering **multiple children** (up to 4) in a single form submission
- Per-child fields: first/last/other name, gender, date of birth, age (auto-calculated from DOB as of camp start date), school, class/grade, home address, learning track, course selection, **mode of instruction (Physical or Virtual)**, medical conditions, allergies, emergency contact
- Shared parent/guardian fields: name, relationship, phone, alternate phone, email, address
- Package selection: Early Bird, Standard, or Premium
- Real-time family discount hint when 2+ children are added
- **Auto-generated Camp ID** per child (format: `ISC26-0001`) assigned immediately after DB insert
- **CSRF protection** on all form submissions
- Client-side and server-side validation with descriptive inline error messages
- Age validated against camp start date (August 3, 2026) — not today's date
- On success: confirmation email sent to parent via SMTP with full registration summary, camp IDs, and mode-specific next steps

### Admin Panel

- Password-protected login with bcrypt hashing and brute-force delay
- **Registrations tab** — full registration list with:
  - Search by name or email
  - Filter by package, learning track, status, and **mode of instruction**
  - One-click status dropdown (pending / confirmed / cancelled)
  - **Auto-email to parent** when status changes to Confirmed or Cancelled — with visible success/failure banner
  - Camp ID and Mode of Instruction columns
  - Pagination (25 per page)
  - CSV export (UTF-8 BOM, Excel-compatible) including Camp ID and Mode columns
- **Registration detail view** — full record display with admin notes, status control, and Camp ID
- **Messages tab** — contact form submissions with read/unread tracking
- Flash notice banner after every status-change action confirming whether the email was sent

### Contact System

- Enquiry form with name, email, phone, subject, and message
- CSRF-protected POST handler
- Saves every submission to the `contact_messages` table
- Sends notification email to camp administrators via SMTP
- Flash success/error messaging via PHP sessions

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Server Language** | PHP 8.2+ |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ |
| **CSS Framework** | Bootstrap 4 |
| **JavaScript** | jQuery 3, Swiper.js, GSAP (ScrollTrigger, SplitText) |
| **Animation** | WOW.js, Animate.css, Odometer.js |
| **Icons** | Font Awesome 6 (CDN), custom Flaticon kidscamp set |
| **Fonts** | Google Fonts — Inter, Playfair Display, Lato |
| **Email** | PHPMailer v6 (SMTP / SSL port 465) |
| **Hosting** | cPanel shared hosting (Apache/LiteSpeed + PHP) |

---

## Project Structure

```
kidscamp/
│
├── index.php                        # Homepage
├── registration.php                 # Multi-child registration form
├── .htaccess                        # URL and security rules
│
├── forms/
│   ├── register-process.php         # Registration POST handler — validates, inserts, emails
│   └── contact-process.php          # Contact POST handler — validates, saves, emails
│
├── config/
│   ├── app.php                      # Site URL, SMTP credentials, seat capacity (NOT in git)
│   ├── db.php                       # Database connection credentials (NOT in git)
│   └── mailer.php                   # PHPMailer sendMail() wrapper
│
├── vendor/                          # Composer dependencies — PHPMailer (NOT in git)
│
├── database/
│   └── schema.sql                   # Full database schema — run once in phpMyAdmin
│
├── admin/
│   ├── index.php                    # Admin login page
│   ├── config.php                   # Admin username + bcrypt hash (NOT in git)
│   ├── setup-hash.php               # One-time password hash generator (delete after use)
│   ├── dashboard.php                # Main admin panel — registrations + messages
│   ├── view.php                     # Individual registration detail view
│   ├── mail-helper.php              # Status-change email builder (confirmed / cancelled)
│   ├── export.php                   # CSV export handler
│   └── logout.php                   # Session destruction + redirect
│
└── assets/
    ├── css/
    │   ├── custom.css               # All project-specific overrides and responsive styles
    │   ├── style.css                # Theme base styles
    │   ├── header.css               # Header/navbar styles
    │   ├── footer.css               # Footer styles
    │   ├── responsive.css           # Theme breakpoint styles
    │   ├── bootstrap.css            # Bootstrap 4
    │   └── flaticon_kidscamp-icons.css
    ├── js/
    │   ├── script.js                # Theme core JavaScript
    │   ├── jquery.js
    │   ├── swiper.min.js
    │   ├── gsap.min.js
    │   └── [other vendor JS]
    └── images/
        ├── background/
        ├── icons/
        ├── main-slider/
        ├── resource/
        ├── favicon.png
        └── favicon-icon.svg
```

---

## Prerequisites

Before deploying, ensure your server has:

- **PHP 8.0 or higher** with `mysqli` extension enabled
- **MySQL 5.7+ or MariaDB 10.3+**
- **Composer** (to install PHPMailer), or upload the `vendor/` folder directly
- **SMTP email account** — credentials for an email address on your domain (e.g. cPanel email)
- **Apache or LiteSpeed** web server (standard cPanel shared hosting is sufficient)

---

## Installation & Setup

### Step 1 — Upload Files

Upload the entire project folder to your web hosting via cPanel File Manager or FTP.

- Place files inside `public_html/summercamp/` (or your chosen subdirectory)
- Ensure folder structure is preserved exactly as shown above

### Step 2 — Install PHPMailer

On the server (via SSH) or locally then upload:

```bash
composer require phpmailer/phpmailer
```

Or upload a pre-built `vendor/` folder that includes PHPMailer v6.

### Step 3 — Create the Database

1. Log in to cPanel → phpMyAdmin
2. Create a new database (e.g. `yourusername_summercamp`) via MySQL Databases
3. Create a DB user, set a strong password, and grant ALL PRIVILEGES on that database
4. Click the **SQL** tab, paste the contents of `database/schema.sql`, and click **Go**

### Step 4 — Configure Database Credentials

Create `config/db.php` on the server (copy from the template below — do not commit real credentials):

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');
```

### Step 5 — Configure App Settings & SMTP

Create `config/app.php` on the server:

```php
<?php
define('SMTP_HOST',      'mail.yourdomain.com');
define('SMTP_PORT',      465);
define('SMTP_ENCRYPTION','ssl');
define('SMTP_USER',      'summercamp@yourdomain.com');
define('SMTP_PASS',      'your_email_password');
define('SMTP_FROM',      'summercamp@yourdomain.com');
define('SMTP_FROM_NAME', 'Ibadan Summer Innovation Camp');

define('GA_MEASUREMENT_ID', '');   // Optional: Google Analytics 4 ID

define('TOTAL_SEATS', 100);

define('SITE_URL', 'https://yourdomain.com/summercamp');
```

### Step 6 — Set Up the Admin Password

1. Visit `https://yourdomain.com/summercamp/admin/setup-hash.php`
2. Enter your chosen admin password and click **Generate Hash**
3. Copy the bcrypt hash (starts with `$2y$`)
4. Create `admin/config.php` on the server:

```php
<?php
define('ADMIN_USER',      'admin');
define('ADMIN_PASS_HASH', '$2y$12$...');   // paste your full hash here
define('ADMIN_SESSION_KEY', 'isc2026_admin');
```

5. **Immediately delete** `admin/setup-hash.php` from the server

### Step 7 — Test End-to-End

- Submit a test registration and verify the parent confirmation email is received (check spam if not)
- Submit a contact enquiry and verify it appears in the Messages tab
- Change a registration status to **Confirmed** in the admin — a green banner should confirm the email was sent
- Export registrations as CSV and verify it opens correctly in Excel

---

## Admin Panel

Access the admin panel at `https://traceworka.ng/summercamp/admin/`

### Dashboard Features

#### Registrations Tab

| Feature | Description |
|---|---|
| Stat cards | Total registrations, confirmed count, package breakdown, unread message badge |
| Search | Filter by parent name, student name, or email |
| Filter by package | Early Bird, Standard, Premium |
| Filter by track | All four learning tracks |
| Filter by status | Pending, Confirmed, Cancelled |
| Filter by mode | Physical, Virtual |
| Status dropdown | One-click status change per row — triggers parent email on Confirmed/Cancelled |
| Email feedback | Green/yellow banner after status change confirms email sent or failed |
| Detail view | Click any row to open full registration record with admin notes |
| CSV export | Downloads filtered results including Camp ID and Mode columns |

#### Messages Tab

- All contact form submissions in reverse-chronological order
- Unread messages highlighted with a count badge
- Mark as Read button per message

### Admin Files Reference

| File | Purpose |
|---|---|
| `admin/index.php` | Login form |
| `admin/config.php` | Admin username and bcrypt hash — **not in git** |
| `admin/setup-hash.php` | One-time hash generator — **delete after use** |
| `admin/dashboard.php` | Main admin interface |
| `admin/view.php` | Individual registration detail and notes |
| `admin/mail-helper.php` | Builds and sends status-change emails |
| `admin/export.php` | Streams CSV download |
| `admin/logout.php` | Clears session and redirects to login |

---

## Database Schema

### `registrations` table

| Column | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED (PK) | Auto-increment primary key |
| `camp_id` | VARCHAR(20) | Auto-generated Camp ID (e.g. `ISC26-0001`) |
| `first_name` | VARCHAR(100) | Student first name |
| `last_name` | VARCHAR(100) | Student last name |
| `other_name` | VARCHAR(100) | Student middle name (optional) |
| `gender` | ENUM('Male','Female') | Student gender |
| `date_of_birth` | DATE | Student date of birth |
| `age` | TINYINT UNSIGNED | Age as of camp start date (Aug 3 2026) |
| `school` | VARCHAR(255) | Student's current school |
| `class_grade` | VARCHAR(100) | Current class or grade |
| `address` | TEXT | Student home address |
| `parent_name` | VARCHAR(255) | Parent/guardian full name |
| `relationship` | VARCHAR(50) | Relationship to student |
| `phone` | VARCHAR(50) | Primary contact phone |
| `alt_phone` | VARCHAR(50) | Alternate phone (optional) |
| `email` | VARCHAR(255) | Parent email address |
| `parent_address` | TEXT | Parent/guardian address |
| `learning_track` | VARCHAR(100) | Selected learning track |
| `courses` | TEXT | Selected courses within track |
| `mode_of_instruction` | ENUM('Physical','Virtual') | Attendance mode |
| `medical_condition` | TEXT | Known medical conditions |
| `allergies` | TEXT | Known allergies |
| `emergency_contact` | VARCHAR(255) | Emergency contact name |
| `emergency_phone` | VARCHAR(50) | Emergency contact phone |
| `emergency_relationship` | VARCHAR(50) | Relationship of emergency contact |
| `package` | VARCHAR(50) | Registration package selected |
| `number_of_children` | TINYINT UNSIGNED | Total children in submission |
| `amount_to_pay` | INT | Calculated amount (NULL if group rate) |
| `status` | ENUM('pending','confirmed','cancelled') | Admin-managed status |
| `admin_notes` | TEXT | Internal admin notes |
| `created_at` | DATETIME | Submission timestamp |

**Indexes:** `email`, `package`, `status`, `learning_track`, `mode_of_instruction`, `created_at`

---

### `contact_messages` table

| Column | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED (PK) | Auto-increment primary key |
| `name` | VARCHAR(255) | Sender's full name |
| `email` | VARCHAR(255) | Sender's email address |
| `phone` | VARCHAR(50) | Sender's phone number |
| `subject` | VARCHAR(255) | Message subject |
| `message` | TEXT | Full message body |
| `is_read` | TINYINT(1) | Read flag (0 = unread, 1 = read) |
| `created_at` | DATETIME | Submission timestamp |

**Indexes:** `is_read`, `created_at`

---

## Security

| Measure | Implementation |
|---|---|
| **CSRF protection** | Tokens generated with `bin2hex(random_bytes(32))`, stored in session, validated with `hash_equals()` on every POST |
| **Input sanitisation** | All user input passed through `htmlspecialchars()` + `strip_tags()` + `trim()` |
| **Email validation** | `filter_var($email, FILTER_VALIDATE_EMAIL)` |
| **SQL injection prevention** | All queries use MySQLi **prepared statements** with `bind_param()` — no raw interpolation |
| **Admin authentication** | bcrypt via `password_hash()` / `password_verify()` with 1-second brute-force delay |
| **Session auth** | Admin session key constant checked on every admin page load |
| **SMTP credentials** | Stored only in `config/app.php` — excluded from version control |
| **DB credentials** | Stored only in `config/db.php` — excluded from version control |
| **Admin credentials** | Stored only in `admin/config.php` — excluded from version control |
| **Character encoding** | All DB connections use `utf8mb4` |

---

## Responsive Design

| Breakpoint | Width | Behaviour |
|---|---|---|
| Desktop | ≥ 992px | Full layouts, inline navbar |
| Tablet | ≤ 991px | Collapsed nav, stacked sections |
| Mobile | ≤ 767px | Single column, stacked hero, optimised form |
| Small Mobile | ≤ 480px | Minimal padding and typography |

All custom overrides are in `assets/css/custom.css`.

---

## Learning Tracks

| Track | Description |
|---|---|
| **Technology** | Coding, programming fundamentals, computer basics |
| **Entrepreneurship** | Business ideation, pitch skills, financial literacy |
| **Vocational Skills** | Practical trades and hands-on career skills |
| **General Life Skills** | Leadership, communication, critical thinking, teamwork |

---

## Pricing Packages

| Package | Price | Notes |
|---|---|---|
| **Early Bird** | ₦45,000 | Available until 20 July 2026 |
| **Standard** | ₦55,000 | Available throughout registration period |
| **Premium** | ₦70,000 | Includes additional materials and priority support |

**Family & Group Discounts:**
- 2 children: 10% off total
- 3 children: 15% off total
- 4 children: 20% off total
- Groups of 5+ (schools/churches): contact for custom pricing

---

## Contact & Support

| Channel | Details |
|---|---|
| **Email** | summercamp@traceworka.ng |
| **Phone** | +234 907 154 3344 |
| **Address** | No 6, Hon Tunde Sarumi Close, Off Adenuga Street, Kongi-Bodija, Ibadan, Oyo State, Nigeria |
| **Facebook** | [facebook.com/traceworka](https://facebook.com/traceworka) |
| **Instagram** | [instagram.com/traceworka](https://instagram.com/traceworka) |
| **LinkedIn** | [linkedin.com/company/traceworka](https://linkedin.com/company/traceworka) |
| **Live Site** | [https://traceworka.ng/summercamp](https://traceworka.ng/summercamp) |
| **Company Website** | [traceworka.ng](https://traceworka.ng) |

---

## Licence

This project is proprietary software owned by **Traceworka Innovative Solutions Limited**.  
All rights reserved. Unauthorised copying, distribution, or use of any part of this codebase is strictly prohibited.

&copy; 2026 Traceworka Innovative Solutions Limited. All rights reserved.
