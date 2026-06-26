<?php
session_start();
require_once('../config/db.php');
require_once('../config/mailer.php');

function sanitize(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

function redirect(string $page, string $key, string $msg): never {
    $_SESSION[$key] = $msg;
    header('Location: ../' . $page);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contact.php', 'contact_error', 'Invalid request.');
}

if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    redirect('contact.php', 'contact_error', 'Security token mismatch. Please refresh and try again.');
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if (!empty($_POST['website'])) {
    redirect('contact.php', 'contact_error', 'Submission blocked.');
}

$name    = sanitize($_POST['name'] ?? '');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone   = sanitize($_POST['phone'] ?? '');
$subject = sanitize($_POST['subject'] ?? 'General Enquiry');
$message = sanitize($_POST['message'] ?? '');

$errors = [];
if (strlen($name) < 2) $errors[] = 'Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
if (!preg_match('/^[+]?[\d\s\-()]{7,20}$/', $phone)) $errors[] = 'A valid phone number is required.';

if (!empty($errors)) {
    redirect('contact.php', 'contact_error', implode(' | ', $errors));
}

// Save to database
$conn = getDBConnection();
$ins  = $conn->prepare(
    'INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)'
);
if ($ins) {
    $ins->bind_param('sssss', $name, $email, $phone, $subject, $message);
    $ins->execute();
    $ins->close();
}
$conn->close();

// Send email notification
$mail_subject = "[ISC2026 Contact] {$subject} — {$name}";
$mail_body = '<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0;">
  <tr><td align="center">
    <table width="580" cellpadding="0" cellspacing="0" style="max-width:580px;width:100%;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
      <tr><td style="background:#002D45;padding:24px 32px;">
        <p style="margin:0 0 4px;color:#f4821f;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">Contact Form</p>
        <h1 style="margin:0;color:#fff;font-size:19px;font-weight:800;">New Message from Website</h1>
        <p style="margin:6px 0 0;color:rgba(255,255,255,0.6);font-size:13px;">' . date('d M Y, H:i') . '</p>
      </td></tr>
      <tr><td style="padding:28px 32px 0;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e8eaf0;border-radius:8px;overflow:hidden;">
          <tr><td style="padding:10px 18px;font-size:13px;color:#888;width:90px;border-bottom:1px solid #f4f5f8;">Name</td>
              <td style="padding:10px 18px;font-size:14px;color:#1a1a2e;font-weight:700;border-bottom:1px solid #f4f5f8;">' . htmlspecialchars($name) . '</td></tr>
          <tr><td style="padding:10px 18px;font-size:13px;color:#888;border-bottom:1px solid #f4f5f8;">Email</td>
              <td style="padding:10px 18px;font-size:14px;border-bottom:1px solid #f4f5f8;"><a href="mailto:' . htmlspecialchars($email) . '" style="color:#f4821f;">' . htmlspecialchars($email) . '</a></td></tr>
          <tr><td style="padding:10px 18px;font-size:13px;color:#888;border-bottom:1px solid #f4f5f8;">Phone</td>
              <td style="padding:10px 18px;font-size:14px;color:#1a1a2e;border-bottom:1px solid #f4f5f8;">' . htmlspecialchars($phone) . '</td></tr>
          <tr><td style="padding:10px 18px;font-size:13px;color:#888;">Subject</td>
              <td style="padding:10px 18px;font-size:14px;color:#1a1a2e;font-weight:600;">' . htmlspecialchars($subject) . '</td></tr>
        </table>
      </td></tr>
      <tr><td style="padding:20px 32px 0;">
        <p style="margin:0 0 8px;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#002D45;">Message</p>
        <div style="background:#f8f9ff;border-radius:8px;padding:18px;font-size:14px;color:#333;line-height:1.75;white-space:pre-wrap;">' . htmlspecialchars($message) . '</div>
      </td></tr>
      <tr><td style="padding:24px 32px;text-align:center;">
        <a href="mailto:' . htmlspecialchars($email) . '" style="display:inline-block;background:#f4821f;color:#fff;text-decoration:none;padding:12px 26px;border-radius:8px;font-size:14px;font-weight:700;">Reply to ' . htmlspecialchars($name) . ' →</a>
      </td></tr>
      <tr><td style="background:#002D45;padding:16px 32px;text-align:center;">
        <p style="margin:0;color:rgba(255,255,255,0.45);font-size:11px;">ISC 2026 · Traceworka Innovative Solutions Limited</p>
      </td></tr>
    </table>
  </td></tr>
</table>
</body></html>';
sendMail('hello@traceworka.ng', 'ISC Admin', $mail_subject, $mail_body, $email);

redirect('contact.php', 'contact_success',
    "Thank you, {$name}! Your message has been sent. We'll get back to you within 24 hours."
);
