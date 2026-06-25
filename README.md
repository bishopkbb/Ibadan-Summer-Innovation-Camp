# Ibadan Summer Innovation Camp 2026

Official website and registration system for the **Ibadan Summer Innovation Camp 2026** — a 4-week transformational holiday programme for children and teenagers aged 7–18, organised by [Traceworka Innovative Solutions Limited](https://traceworka.ng), Ibadan, Oyo State, Nigeria.

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

- Public-facing marketing pages (homepage, about, learning tracks, pricing, contact)
- A multi-child online registration form with real-time seat tracking
- A contact/enquiry form
- A password-protected admin dashboard for managing registrations and messages
- Automated email confirmations sent to parents upon successful registration
- CSV export of registration data

The site is built without any framework dependency — pure PHP 8+, vanilla JavaScript, jQuery, and Bootstrap — making it straightforward to deploy on any standard cPanel shared hosting environment.

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
| **Age Range** | 7 – 18 years |
| **Total Capacity** | 100 seats |
| **Email** | hello@traceworka.ng |
| **Phone** | +234 907 154 3344 |
| **Website** | https://summercampibadan.traceworka.ng |

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
- **Camp Highlights strip** — Quick-glance info cards (age range, dates, learning tracks, professional security)
- **About section** — Camp mission, vision, and programme overview with hover-animated image
- **Learning Tracks** — Swiper-powered carousel showcasing all six programme tracks with individual cards
- **Why Choose Us / Camp Journey** — Four-week breakdown of what children experience each week
- **Camp Experience** — Detailed highlights of activities and facilities
- **Who Can Join** — Age group cards (Junior Innovators, Young Creators, Future Leaders)
- **Pricing section** — Three-tier package table with Early Bird countdown, family/group discount section
- **Registration page** — Dynamic multi-child form (register 1–5 children per submission)
- **Contact page** — Enquiry form with embedded location details
- **Security ticker** — Scrolling announcement banner in the navbar communicating professional security presence
- **Sticky navbar** — Header transitions to a fixed dark-navy bar with smooth animation on scroll
- **Slide-in mobile menu** — Theme-native mobile navigation overlay

### Registration System

- Supports registering **multiple children** (up to 5) in a single form submission
- Per-child fields: personal details, school, date of birth, gender, class/grade, home address, learning track, medical conditions, allergies, emergency contact
- Shared parent/guardian fields: name, relationship, phone, alternate phone, email, address
- Package selection: Early Bird, Standard, or Premium
- Real-time family discount hint when 2+ children are added
- **CSRF protection** on all form submissions
- Server-side validation with descriptive inline error messages
- On success: confirmation email sent to parent with full registration summary
- Seat counter on the homepage decrements in real time based on database count

### Contact System

- Enquiry form with name, email, phone, subject, and message
- CSRF-protected POST handler
- Saves every submission to the `contact_messages` database table
- Sends notification email to camp administrators
- Flash success/error messaging via PHP sessions

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Server Language** | PHP 8+ |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ |
| **CSS Framework** | Bootstrap 4 |
| **JavaScript** | jQuery 3, Swiper.js, GSAP (ScrollTrigger, SplitText, ScrollSmoother) |
| **Animation** | WOW.js, Animate.css, Odometer.js |
| **Icons** | Font Awesome 6 (CDN), custom Flaticon kidscamp set |
| **Fonts** | Google Fonts — Inter, Playfair Display, Lato |
| **Email** | PHP `mail()` with HTML formatting |
| **Hosting Target** | cPanel shared hosting (Apache/LiteSpeed + PHP) |

---

## Project Structure

```
kidscamp/
│
├── index.php                        # Homepage
├── registration.php                 # Multi-child registration form
├── contact.php                      # Contact & enquiry page
│
├── includes/
│   ├── header.php                   # <head>, meta tags, structured data, stylesheet links
│   ├── navbar.php                   # Site header, navigation, mobile menu, security ticker
│   └── footer.php                   # Site footer, social links, script tags
│
├── forms/
│   ├── register-process.php         # Registration POST handler — validates, saves, emails
│   └── contact-process.php          # Contact POST handler — validates, saves, emails
│
├── config/
│   └── db.php                       # Database connection config (credentials go here)
│
├── database/
│   └── schema.sql                   # Full database schema — run once in phpMyAdmin
│
├── admin/
│   ├── index.php                    # Login page
│   ├── config.php                   # Admin credentials (username + bcrypt hash)
│   ├── setup-hash.php               # One-time password hash generator (DELETE after use)
│   ├── dashboard.php                # Main admin panel — registrations + messages
│   ├── export.php                   # CSV export handler (UTF-8 BOM, Excel-compatible)
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
    │   ├── flaticon_kidscamp-icons.css  # Custom icon font
    │   └── [other vendor CSS]
    ├── js/
    │   ├── script.js                # Theme core JavaScript (mobile menu, sticky header, etc.)
    │   ├── form-validation.js       # Client-side form validation
    │   ├── jquery.js
    │   ├── swiper.min.js
    │   ├── gsap.min.js
    │   └── [other vendor JS]
    └── images/
        ├── background/              # Pattern and background images
        ├── icons/                   # UI icons and decorative elements
        ├── main-slider/             # Hero section images and decorations
        ├── resource/                # About and programme track images
        ├── favicon.png
        ├── favicon-icon.svg
        └── cross-out.png
```

---

## Prerequisites

Before deploying, ensure your server has:

- **PHP 8.0 or higher** with the `mysqli` extension enabled
- **MySQL 5.7+ or MariaDB 10.3+**
- **PHP `mail()` function** enabled (or configure SMTP via a mail library)
- **Apache or LiteSpeed** web server (standard cPanel shared hosting is sufficient)
- **cPanel access** for database creation and file management

---

## Installation & Setup

Follow these steps in order. All five must be completed before the site functions correctly.

### Step 1 — Upload Files

Upload the entire project folder to your web hosting via **cPanel File Manager** or **FTP** (FileZilla, WinSCP, etc.).

- Place all files inside `public_html/` for the root domain, or inside a subdirectory such as `public_html/camp/` for a subfolder installation.
- Ensure the folder structure is preserved exactly as shown above.

### Step 2 — Create the Database

1. Log in to **cPanel** → open **phpMyAdmin**
2. Click the **SQL** tab
3. Open `database/schema.sql` in a text editor, copy the entire contents, paste into the SQL tab, and click **Go**

This creates:
- The `ibadan_camp` database
- The `registrations` table
- The `contact_messages` table

> **Note:** On cPanel, the database name may be prefixed with your cPanel username (e.g. `myuser_ibadan_camp`). If so, remove the `CREATE DATABASE` and `USE` lines from the SQL, create the database manually in cPanel's **MySQL Databases** tool first, then run only the two `CREATE TABLE` statements.

### Step 3 — Create a Database User

1. In cPanel → **MySQL Databases** → **Add New User** — set a username and strong password
2. Under **Add User to Database** — select the new user and the `ibadan_camp` database
3. Grant **ALL PRIVILEGES** → click **Make Changes**

### Step 4 — Configure Database Credentials

Open `config/db.php` and update the four constants:

```php
define('DB_HOST', 'localhost');           // Almost always 'localhost' on cPanel
define('DB_USER', 'your_db_username');   // MySQL username from Step 3
define('DB_PASS', 'your_db_password');   // MySQL password from Step 3
define('DB_NAME', 'ibadan_camp');        // Database name (with prefix if applicable)
```

Save and upload the updated file to the server.

### Step 5 — Set Up the Admin Password

1. Visit `https://yourdomain.com/admin/setup-hash.php` in your browser
2. Enter your chosen admin password (minimum 8 characters) and click **Generate Hash**
3. Copy the generated bcrypt hash string (starts with `$2y$`)
4. Open `admin/config.php` and paste the hash:

```php
define('ADMIN_USER',      'admin');           // Change username if desired
define('ADMIN_PASS_HASH', '$2y$12$...');      // Paste your full hash here
```

5. Save and upload `admin/config.php` to the server
6. **Immediately delete** `admin/setup-hash.php` from the server — leaving it accessible is a security risk

### Step 6 — Verify Email Delivery

Submit a test registration and confirm:
- The parent receives a confirmation email
- The admin (`hello@traceworka.ng`) receives a notification email

If emails are not delivered, check that your host's `mail()` function is enabled, or configure an SMTP solution such as PHPMailer with your email provider's credentials.

### Step 7 — Test End-to-End

- Submit a registration via `registration.php` and verify it appears in the admin dashboard
- Submit a contact enquiry via `contact.php` and verify it appears in the Messages tab
- Change a registration status in the admin and confirm it saves
- Export registrations as CSV and verify it opens correctly in Excel

---

## Admin Panel

Access the admin panel at `/admin/` after completing setup.

### Login

| Field | Value |
|---|---|
| **URL** | `https://yourdomain.com/admin/` |
| **Username** | `admin` (or as configured in `admin/config.php`) |
| **Password** | The password you generated in setup Step 5 |

Sessions expire on browser close. Log out via the **Logout** button to destroy the session immediately.

### Dashboard Features

#### Registrations Tab

- **Stat cards** — Total registrations, confirmed count, count by package, unread message badge
- **Search** — Filter by parent name, student name, or email address
- **Filter by package** — Early Bird, Standard, Premium
- **Filter by learning track** — All six tracks
- **Filter by status** — Pending, Confirmed, Cancelled
- **Pagination** — 25 records per page
- **Status update** — One-click dropdown per row to change status (pending / confirmed / cancelled)
- **CSV Export** — Downloads a UTF-8 BOM CSV of all currently filtered results, compatible with Microsoft Excel

#### Messages Tab

- Lists all contact form submissions in reverse chronological order
- **Unread messages** are highlighted in bold with a count badge on the tab
- **Mark as Read** button per message
- Full message body displayed inline

### Admin Files Reference

| File | Purpose |
|---|---|
| `admin/index.php` | Login form — branded with camp colours |
| `admin/config.php` | Stores admin username and bcrypt password hash |
| `admin/setup-hash.php` | One-time hash generator — **delete after use** |
| `admin/dashboard.php` | Full admin interface |
| `admin/export.php` | Streams CSV download with active filters applied |
| `admin/logout.php` | Clears session and redirects to login |

---

## Database Schema

### `registrations` table

| Column | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED (PK) | Auto-increment primary key |
| `first_name` | VARCHAR(100) | Student first name |
| `last_name` | VARCHAR(100) | Student last name |
| `other_name` | VARCHAR(100) | Student middle name (optional) |
| `gender` | ENUM('Male','Female') | Student gender |
| `date_of_birth` | DATE | Student date of birth |
| `age` | TINYINT UNSIGNED | Calculated age |
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
| `medical_condition` | TEXT | Known medical conditions |
| `allergies` | TEXT | Known allergies |
| `emergency_contact` | VARCHAR(255) | Emergency contact name |
| `emergency_phone` | VARCHAR(50) | Emergency contact phone |
| `emergency_relationship` | VARCHAR(50) | Relationship of emergency contact |
| `package` | VARCHAR(50) | Registration package selected |
| `number_of_children` | TINYINT UNSIGNED | Total children in submission |
| `status` | ENUM('pending','confirmed','cancelled') | Admin-managed registration status |
| `admin_notes` | TEXT | Internal admin notes (optional) |
| `created_at` | DATETIME | Submission timestamp |

**Indexes:** `email`, `package`, `status`, `learning_track`, `created_at`

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

The following security measures are implemented throughout the application:

| Measure | Implementation |
|---|---|
| **CSRF protection** | Tokens generated with `bin2hex(random_bytes(32))`, stored in session, validated with `hash_equals()` on every POST |
| **Input sanitisation** | All user input passed through `htmlspecialchars()` + `strip_tags()` + `trim()` |
| **Email validation** | `filter_var($email, FILTER_VALIDATE_EMAIL)` with `FILTER_SANITIZE_EMAIL` |
| **SQL injection prevention** | All database queries use **prepared statements** with `bind_param()` — no raw interpolation |
| **Admin authentication** | bcrypt password hashing via `password_hash()` and `password_verify()` (PASSWORD_DEFAULT algorithm) |
| **Session-based auth** | Admin session key stored as a named constant; checked on every admin page load |
| **Access control** | Admin pages redirect to login if session key is absent |
| **setup-hash.php** | Must be deleted from the server immediately after admin password is configured |
| **Database credentials** | Stored only in `config/db.php` — never exposed in public files |
| **Character encoding** | All database connections use `utf8mb4`; charset set explicitly on every connection |

---

## Responsive Design

The site is fully responsive across all device sizes using a mobile-first approach.

| Breakpoint | Width | Behaviour |
|---|---|---|
| Desktop | ≥ 992px | Full two-column layouts, full navbar with inline links and Register Now pill |
| Tablet | ≤ 991px | Collapsed nav (slide-in mobile menu), stacked sections, adjusted typography |
| Mobile | ≤ 767px | Single column, hero image stacked above content, stacked about section, optimised form layout |
| Small Mobile | ≤ 480px | Tightest padding, smallest typography scale, minimal UI |

All custom responsive overrides are in `assets/css/custom.css` and take precedence over the theme's `responsive.css` via `!important` where necessary.

---

## Learning Tracks

| Track | Description |
|---|---|
| **Technology** | Coding, programming fundamentals, computer basics |
| **Entrepreneurship** | Business ideation, pitch skills, financial literacy |
| **Vocational Skills** | Practical trades and hands-on career skills |
| **General Life Skills** | Leadership, communication, critical thinking, teamwork |

Programme cards in the UI use custom PNG images (`assets/images/resource/program-1.png` through `program-7.png`).

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
- 4+ children: 20% off total
- Groups of 5+ (schools/churches): contact for custom pricing

The registration form dynamically displays a discount hint when multiple children are added.

---

## Contact & Support

| Channel | Details |
|---|---|
| **Email** | hello@traceworka.ng |
| **Phone** | +234 907 154 3344 |
| **Address** | No 6, Hon Tunde Sarumi Close, Off Adenuga Street, Kongi-Bodija, Ibadan, Oyo State, Nigeria |
| **Facebook** | [facebook.com/traceworka](https://facebook.com/traceworka) |
| **Instagram** | [instagram.com/traceworka](https://instagram.com/traceworka) |
| **LinkedIn** | [linkedin.com/company/traceworka](https://linkedin.com/company/traceworka) |
| **Website** | [traceworka.ng](https://traceworka.ng) |

---

## Licence

This project is proprietary software owned by **Traceworka Innovative Solutions Limited**.
All rights reserved. Unauthorised copying, distribution, or use of any part of this codebase is strictly prohibited.

&copy; 2026 Traceworka Innovative Solutions Limited. All rights reserved.
