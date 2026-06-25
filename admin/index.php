<?php
session_start();
require_once('config.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (ADMIN_PASS_HASH === '') {
        $error = 'Admin password not configured yet. Run admin/setup-hash.php first.';
    } elseif ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS_HASH)) {
        $_SESSION[ADMIN_SESSION_KEY] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
        // Small delay to slow brute-force attempts
        sleep(1);
    }
}

if (!empty($_SESSION[ADMIN_SESSION_KEY])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login — ISC 2026</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: linear-gradient(135deg, #002D45 0%, #01415b 60%, #001e30 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.login-card {
    background: #fff;
    border-radius: 20px;
    padding: 48px 44px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.28);
}
.logo-area { text-align: center; margin-bottom: 32px; }
.logo-area img { max-height: 80px; width: auto; }
.logo-area h2 {
    font-size: 17px;
    font-weight: 700;
    color: #002D45;
    margin-top: 14px;
    letter-spacing: 0.2px;
}
.logo-area p {
    font-size: 13px;
    color: #888;
    margin-top: 4px;
}
.form-group { margin-bottom: 20px; }
.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 7px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}
.form-group input {
    display: block;
    width: 100%;
    height: 50px;
    padding: 0 16px;
    font-size: 15px;
    color: #1a1a2e;
    background: #f8f9ff;
    border: 1.5px solid #dde1ea;
    border-radius: 9px;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.form-group input:focus {
    border-color: #f4821f;
    box-shadow: 0 0 0 3px rgba(244,130,31,.12);
    background: #fff;
}
.btn-login {
    display: block;
    width: 100%;
    height: 50px;
    background: #f4821f;
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    border: none;
    border-radius: 9px;
    cursor: pointer;
    letter-spacing: 0.3px;
    transition: background .2s, transform .15s;
    margin-top: 8px;
}
.btn-login:hover { background: #e07318; transform: translateY(-1px); }
.error {
    background: #fff0f0;
    border: 1px solid #fcc;
    color: #c0392b;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 22px;
}
.back-link {
    display: block;
    text-align: center;
    margin-top: 22px;
    font-size: 13px;
    color: #888;
    text-decoration: none;
}
.back-link:hover { color: #f4821f; }
</style>
</head>
<body>
<div class="login-card">
    <div class="logo-area">
        <img src="../assets/Summer Camp Logos/ISC Logo.svg" alt="ISC 2026">
        <h2>Admin Panel</h2>
        <p>Ibadan Summer Innovation Camp 2026</p>
    </div>

    <?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autocomplete="username"
                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn-login">Sign In</button>
    </form>
    <a href="../index.php" class="back-link">← Back to camp website</a>
</div>
</body>
</html>
