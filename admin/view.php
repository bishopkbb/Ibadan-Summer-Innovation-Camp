<?php
session_start();
require_once('config.php');
require_once('../config/db.php');
require_once('../config/app.php');

$_mailer_available = file_exists(__DIR__ . '/../vendor/autoload.php');
if ($_mailer_available) {
    require_once('../config/mailer.php');
} else {
    function sendMail(): bool { return false; }
}
require_once('mail-helper.php');

if (empty($_SESSION[ADMIN_SESSION_KEY])) {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

$conn = getDBConnection();

/* Fetch registration first — needed in both GET and POST paths */
$stmt = $conn->prepare('SELECT * FROM registrations WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$r) {
    $conn->close();
    header('Location: dashboard.php');
    exit;
}

/* Handle admin notes / status update */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_notes'])) {
    $notes      = htmlspecialchars(strip_tags(trim($_POST['admin_notes'])), ENT_QUOTES, 'UTF-8');
    $new_status = in_array($_POST['status'] ?? '', ['pending','confirmed','cancelled']) ? $_POST['status'] : 'pending';
    $prev_status = $r['status'];

    $upd = $conn->prepare('UPDATE registrations SET admin_notes = ?, status = ? WHERE id = ?');
    $upd->bind_param('ssi', $notes, $new_status, $id);
    $upd->execute();
    $upd->close();

    if ($prev_status !== $new_status && in_array($new_status, ['confirmed','cancelled'])) {
        $email_sent = sendRegistrationStatusEmail($r, $new_status);
        if ($email_sent) {
            $_SESSION['admin_notice'] = ['type' => 'success', 'msg' => "Status set to <strong>" . ucfirst($new_status) . "</strong> and confirmation email sent to <strong>" . htmlspecialchars($r['email'] ?? '') . "</strong>."];
        } else {
            $_SESSION['admin_notice'] = ['type' => 'warning', 'msg' => "Status set to <strong>" . ucfirst($new_status) . "</strong> but the email to <strong>" . htmlspecialchars($r['email'] ?? '') . "</strong> could not be sent. Check the server error log."];
        }
    }

    $conn->close();
    header('Location: view.php?id=' . $id . '&saved=1');
    exit;
}

$conn->close();

$saved = !empty($_GET['saved']);

function row(string $label, string $value, string $extra = ''): void {
    if ($value === '' || $value === null) $value = '<span style="color:#bbb;">—</span>';
    echo '<tr>
        <td style="padding:10px 18px;font-size:13px;color:#888;white-space:nowrap;vertical-align:top;width:180px;border-bottom:1px solid #f4f5f8;">' . $label . '</td>
        <td style="padding:10px 18px;font-size:14px;color:#1a1a2e;border-bottom:1px solid #f4f5f8;' . $extra . '">' . $value . '</td>
    </tr>';
}

$statusColors = [
    'pending'   => ['#fff3cd','#856404'],
    'confirmed' => ['#d4edda','#155724'],
    'cancelled' => ['#f8d7da','#721c24'],
];
[$sbg, $scol] = $statusColors[$r['status']] ?? ['#eee','#333'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Registration #<?php echo $r['id']; ?> — ISC 2026 Admin</title>
<link rel="icon" href="../assets/images/favicon.png" type="image/png">
<link rel="icon" href="../assets/images/favicon-icon.svg" type="image/svg+xml">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',system-ui,sans-serif;background:#f0f2f7;color:#1a1a2e;min-height:100vh;}
.topbar{background:#002D45;color:#fff;display:flex;align-items:center;justify-content:space-between;padding:0 28px;height:62px;position:sticky;top:0;z-index:100;box-shadow:0 2px 12px rgba(0,0,0,0.18);}
.topbar a{color:rgba(255,255,255,0.75);text-decoration:none;font-size:14px;}
.topbar a:hover{color:#f4821f;}
.main{max-width:900px;margin:0 auto;padding:28px 24px;}
.card{background:#fff;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:24px;}
.card-header{padding:16px 24px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;gap:12px;}
.card-header h3{font-size:15px;font-weight:700;color:#002D45;}
.section-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;}
table{width:100%;border-collapse:collapse;}
.btn{padding:9px 20px;border-radius:7px;font-size:14px;font-weight:700;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-navy{background:#002D45;color:#fff;}
.btn-navy:hover{background:#013857;}
.btn-orange{background:#f4821f;color:#fff;}
.btn-orange:hover{background:#e07318;}
.btn-sm{padding:5px 14px;font-size:12px;}
.alert-success{background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:14px 20px;border-radius:10px;margin-bottom:20px;font-weight:600;font-size:14px;}
@media(max-width:600px){.main{padding:16px 12px;}.topbar{padding:0 14px;}}
</style>
</head>
<body>

<header class="topbar">
    <div style="display:flex;align-items:center;gap:16px;">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
    <div style="font-size:14px;font-weight:700;color:#fff;">Registration #<?php echo $r['id']; ?></div>
    <a href="logout.php">Sign Out</a>
</header>

<main class="main">

<?php if ($saved): ?>
<div class="alert-success">✓ Changes saved successfully.</div>
<?php endif; ?>
<?php if (!empty($_SESSION['admin_notice'])): $notice = $_SESSION['admin_notice']; unset($_SESSION['admin_notice']); ?>
<div style="margin-bottom:16px;padding:14px 20px;border-radius:10px;font-size:14px;font-weight:600;
    <?php echo $notice['type'] === 'success' ? 'background:#d4edda;border:1px solid #b7dfc6;color:#155724;' : 'background:#fff3cd;border:1px solid #ffeeba;color:#856404;'; ?>">
    <?php echo $notice['msg']; ?>
</div>
<?php endif; ?>

<!-- Summary Bar -->
<div style="background:#002D45;border-radius:14px;padding:20px 28px;margin-bottom:24px;display:flex;flex-wrap:wrap;gap:20px;align-items:center;justify-content:space-between;">
    <div>
        <div style="color:rgba(255,255,255,0.6);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;">Registration #<?php echo $r['id']; ?></div>
        <div style="color:#fff;font-size:20px;font-weight:800;margin-top:4px;">
            <?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?>
        </div>
        <div style="color:rgba(255,255,255,0.65);font-size:13px;margin-top:2px;">
            Submitted <?php echo date('d M Y, H:i', strtotime($r['created_at'])); ?>
        </div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
        <span style="background:<?php echo $sbg; ?>;color:<?php echo $scol; ?>;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;text-transform:capitalize;"><?php echo $r['status']; ?></span>
        <span style="background:rgba(244,130,31,0.2);color:#f4821f;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;"><?php echo htmlspecialchars($r['package']); ?></span>
        <?php if (!empty($r['amount_to_pay'])): ?>
        <span style="background:rgba(255,255,255,0.15);color:#fff;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;">&#8358;<?php echo number_format((int)$r['amount_to_pay']); ?></span>
        <?php else: ?>
        <span style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.6);padding:6px 16px;border-radius:20px;font-size:13px;">Group rate</span>
        <?php endif; ?>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

<!-- Student Info -->
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:#fff5ed;color:#f4821f;">&#128100;</div>
        <h3>Student Information</h3>
    </div>
    <table>
        <?php
        row('Full Name', htmlspecialchars($r['first_name'] . ' ' . $r['last_name'] . ($r['other_name'] ? ' ' . $r['other_name'] : '')));
        row('Gender', htmlspecialchars($r['gender']));
        row('Date of Birth', htmlspecialchars($r['date_of_birth']) . ' (Age ' . $r['age'] . ')');
        row('School', htmlspecialchars($r['school']));
        row('Class / Grade', htmlspecialchars($r['class_grade']));
        row('Address', nl2br(htmlspecialchars($r['address'])));
        ?>
    </table>
</div>

<!-- Parent Info -->
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:#eef2ff;color:#002D45;">&#128106;</div>
        <h3>Parent / Guardian</h3>
    </div>
    <table>
        <?php
        row('Name', htmlspecialchars($r['parent_name']));
        row('Relationship', htmlspecialchars($r['relationship']));
        row('Phone', '<a href="tel:' . htmlspecialchars($r['phone']) . '" style="color:#f4821f;">' . htmlspecialchars($r['phone']) . '</a>');
        row('Alt Phone', $r['alt_phone'] ? '<a href="tel:' . htmlspecialchars($r['alt_phone']) . '" style="color:#f4821f;">' . htmlspecialchars($r['alt_phone']) . '</a>' : '');
        row('Email', '<a href="mailto:' . htmlspecialchars($r['email']) . '" style="color:#f4821f;">' . htmlspecialchars($r['email']) . '</a>');
        row('Address', nl2br(htmlspecialchars($r['parent_address'])));
        ?>
    </table>
</div>

</div>

<!-- Camp Participation -->
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:#fff5ed;color:#f4821f;">&#127891;</div>
        <h3>Camp Participation</h3>
    </div>
    <table>
        <?php
        row('Camp ID', '<span style="font-size:18px;font-weight:900;color:#002D45;letter-spacing:3px;">' . htmlspecialchars($r['camp_id'] ?? 'Not yet assigned') . '</span>');
        row('Learning Track', '<strong>' . htmlspecialchars($r['learning_track']) . '</strong>');
        row('Courses Selected', htmlspecialchars($r['courses']));
        $mode_display = ($r['mode_of_instruction'] ?? 'Physical') === 'Virtual' ? '&#128187; Virtual' : '&#127979; Physical';
        row('Mode of Instruction', '<strong>' . $mode_display . '</strong>');
        row('Package', '<strong style="color:#f4821f;">' . htmlspecialchars($r['package']) . '</strong>');
        row('No. of Children (family)', (string)$r['number_of_children']);
        row('Amount Due', !empty($r['amount_to_pay']) ? '<strong>&#8358;' . number_format((int)$r['amount_to_pay']) . '</strong>' : 'Group rate');
        ?>
    </table>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

<!-- Medical Info -->
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:#fff0f0;color:#e74c3c;">&#128138;</div>
        <h3>Medical Information</h3>
    </div>
    <table>
        <?php
        row('Medical Condition', nl2br(htmlspecialchars($r['medical_condition'] ?: 'None reported')));
        row('Allergies', nl2br(htmlspecialchars($r['allergies'] ?: 'None reported')));
        ?>
    </table>
</div>

<!-- Emergency Contact -->
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:#f0fff4;color:#27ae60;">&#128222;</div>
        <h3>Emergency Contact</h3>
    </div>
    <table>
        <?php
        row('Name', htmlspecialchars($r['emergency_contact']));
        row('Phone', '<a href="tel:' . htmlspecialchars($r['emergency_phone']) . '" style="color:#f4821f;">' . htmlspecialchars($r['emergency_phone']) . '</a>');
        row('Relationship', htmlspecialchars($r['emergency_relationship'] ?: '—'));
        ?>
    </table>
</div>

</div>

<!-- Admin Controls -->
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:#f8f9ff;color:#002D45;">&#9881;</div>
        <h3>Admin Controls</h3>
    </div>
    <form method="post" style="padding:24px;">
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#888;margin-bottom:8px;">Payment Status</label>
                <select name="status" style="width:100%;height:42px;padding:0 12px;border:1.5px solid #dde1ea;border-radius:8px;font-size:14px;font-weight:600;color:#1a1a2e;background:#f8f9ff;">
                    <?php foreach (['pending','confirmed','cancelled'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $r['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#888;margin-bottom:8px;">Admin Notes</label>
                <textarea name="admin_notes" rows="3" style="width:100%;padding:10px 14px;border:1.5px solid #dde1ea;border-radius:8px;font-size:14px;color:#1a1a2e;background:#f8f9ff;resize:vertical;"><?php echo htmlspecialchars($r['admin_notes'] ?? ''); ?></textarea>
            </div>
        </div>
        <p style="margin-top:14px;font-size:12px;color:#888;">&#9993; Changing status to <strong>Confirmed</strong> or <strong>Cancelled</strong> will automatically email the parent.</p>
        <div style="margin-top:10px;display:flex;gap:12px;">
            <button type="submit" class="btn btn-orange">Save Changes</button>
            <a href="dashboard.php" class="btn btn-navy">← Back to Dashboard</a>
        </div>
    </form>
</div>

</main>
</body>
</html>
