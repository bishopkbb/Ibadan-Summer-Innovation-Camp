<?php
session_start();
require_once('../config/db.php');

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
$to      = 'hello@traceworka.ng';
$subj_hdr = "[ISC2026 Contact] {$subject} – {$name}";
$body    = "New contact message received\n" . str_repeat('=', 40) . "\n\n";
$body   .= "Name:    {$name}\n";
$body   .= "Email:   {$email}\n";
$body   .= "Phone:   {$phone}\n";
$body   .= "Subject: {$subject}\n\n";
$body   .= "Message:\n{$message}\n\n";
$body   .= "Received: " . date('d M Y, H:i') . "\n";
$headers = "From: hello@traceworka.ng\r\nReply-To: {$email}\r\nX-Mailer: PHP/" . phpversion();
@mail($to, $subj_hdr, $body, $headers);

redirect('contact.php', 'contact_success',
    "Thank you, {$name}! Your message has been sent. We'll get back to you within 24 hours."
);
