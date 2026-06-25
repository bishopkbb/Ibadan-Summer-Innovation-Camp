<?php
/* ============================================================
   ONE-TIME PASSWORD HASH GENERATOR
   ⚠ DELETE THIS FILE FROM YOUR SERVER AFTER USE ⚠
   ============================================================ */
$hash = '';
$msg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
    $pw   = trim($_POST['password']);
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $msg  = 'Hash generated successfully. Copy it into admin/config.php.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Password Setup — ISC 2026</title>
<style>
  body { font-family: system-ui, sans-serif; max-width: 560px; margin: 60px auto; padding: 0 20px; color: #1a1a2e; }
  h2   { color: #002D45; }
  label { display: block; font-weight: 600; margin-bottom: 6px; }
  input[type=password] { width: 100%; padding: 12px; border: 1.5px solid #ccc; border-radius: 8px; font-size: 15px; box-sizing: border-box; }
  button { margin-top: 14px; padding: 12px 28px; background: #f4821f; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; }
  pre  { background: #f0f4ff; border: 1px solid #c5d0e6; padding: 16px; border-radius: 8px; word-break: break-all; font-size: 13px; }
  .note { background: #fff3cd; border: 1px solid #ffc107; padding: 14px 18px; border-radius: 8px; font-size: 14px; margin-top: 20px; }
  .ok  { color: #155724; background: #d4edda; border: 1px solid #c3e6cb; padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; font-weight: 600; }
</style>
</head>
<body>
<h2>Admin Password Setup</h2>
<p>Enter the password you want to use for the admin panel. A secure hash will be generated — paste it into <code>admin/config.php</code>.</p>

<form method="post">
  <label for="pw">Desired Admin Password</label>
  <input type="password" id="pw" name="password" required minlength="8" placeholder="Min. 8 characters">
  <button type="submit">Generate Hash</button>
</form>

<?php if ($msg): ?>
<p class="ok">✓ <?php echo htmlspecialchars($msg); ?></p>
<label>Your hash (copy this):</label>
<pre><?php echo htmlspecialchars($hash); ?></pre>
<p>Paste this hash as <code>ADMIN_PASS_HASH</code> in <code>admin/config.php</code>.</p>
<?php endif; ?>

<div class="note">
  ⚠ <strong>Important:</strong> Delete this file (<code>admin/setup-hash.php</code>) from your server immediately after setting your password. Leaving it accessible is a security risk.
</div>
</body>
</html>
