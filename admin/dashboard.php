<?php
session_start();
require_once('config.php');
require_once('../config/db.php');

/* ── Auth guard ── */
if (empty($_SESSION[ADMIN_SESSION_KEY])) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

/* ============================================================
   Handle quick actions (status update, mark-read)
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['action'])) {

        if ($_POST['action'] === 'update_status' && !empty($_POST['reg_id'])) {
            $id     = (int) $_POST['reg_id'];
            $status = in_array($_POST['new_status'] ?? '', ['pending','confirmed','cancelled'])
                      ? $_POST['new_status'] : 'pending';
            $stmt = $conn->prepare('UPDATE registrations SET status = ? WHERE id = ?');
            $stmt->bind_param('si', $status, $id);
            $stmt->execute();
            $stmt->close();
        }

        if ($_POST['action'] === 'mark_read' && !empty($_POST['msg_id'])) {
            $id   = (int) $_POST['msg_id'];
            $stmt = $conn->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: dashboard.php?' . http_build_query(array_filter([
        'tab'     => $_GET['tab']     ?? 'registrations',
        'package' => $_GET['package'] ?? '',
        'track'   => $_GET['track']   ?? '',
        'status'  => $_GET['status']  ?? '',
        'search'  => $_GET['search']  ?? '',
        'page'    => $_GET['page']    ?? '',
    ])));
    exit;
}

/* ============================================================
   Filters & Pagination
   ============================================================ */
$tab     = in_array($_GET['tab'] ?? '', ['registrations','messages']) ? $_GET['tab'] : 'registrations';
$pkg     = $_GET['package'] ?? '';
$track   = $_GET['track']   ?? '';
$status  = $_GET['status']  ?? '';
$search  = trim($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset  = ($page - 1) * $perPage;

/* ── Stats ── */
$stats = [];
$r = $conn->query("SELECT package, COUNT(*) AS cnt FROM registrations GROUP BY package");
while ($row = $r->fetch_assoc()) $stats[$row['package']] = (int)$row['cnt'];
$totalReg     = array_sum($stats);
$totalPending  = (int)$conn->query("SELECT COUNT(*) FROM registrations WHERE status='pending'")->fetch_row()[0];
$totalConfirmed= (int)$conn->query("SELECT COUNT(*) FROM registrations WHERE status='confirmed'")->fetch_row()[0];
$unreadMsgs    = (int)$conn->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetch_row()[0];

/* ── Registrations query ── */
$where  = [];
$params = [];
$types  = '';

if ($pkg)    { $where[] = 'package = ?';        $params[] = $pkg;    $types .= 's'; }
if ($track)  { $where[] = 'learning_track = ?'; $params[] = $track;  $types .= 's'; }
if ($status) { $where[] = 'status = ?';         $params[] = $status; $types .= 's'; }
if ($search) {
    $like = '%' . $search . '%';
    $where[]  = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= 'ssss';
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* total matching */
$countSQL = "SELECT COUNT(*) FROM registrations $whereSQL";
if ($params) {
    $cs = $conn->prepare($countSQL);
    $cs->bind_param($types, ...$params);
    $cs->execute();
    $totalFiltered = (int)$cs->get_result()->fetch_row()[0];
    $cs->close();
} else {
    $totalFiltered = (int)$conn->query($countSQL)->fetch_row()[0];
}
$totalPages = max(1, (int)ceil($totalFiltered / $perPage));

/* registrations rows */
$regSQL = "SELECT * FROM registrations $whereSQL ORDER BY created_at DESC LIMIT ? OFFSET ?";
$regParams = array_merge($params, [$perPage, $offset]);
$regTypes  = $types . 'ii';
$rs = $conn->prepare($regSQL);
$rs->bind_param($regTypes, ...$regParams);
$rs->execute();
$registrations = $rs->get_result()->fetch_all(MYSQLI_ASSOC);
$rs->close();

/* ── Contact messages query ── */
$msgs = $conn->query("SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);

/* ── Helper: build pagination URL ── */
function pageURL(int $p): string {
    $q = $_GET;
    $q['page'] = $p;
    return '?' . http_build_query($q);
}

/* ── Status badge ── */
function statusBadge(string $s): string {
    $map = [
        'pending'   => ['#fff3cd','#856404'],
        'confirmed' => ['#d4edda','#155724'],
        'cancelled' => ['#f8d7da','#721c24'],
    ];
    [$bg, $color] = $map[$s] ?? ['#eee','#333'];
    return "<span style='background:$bg;color:$color;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;text-transform:capitalize;'>$s</span>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo ADMIN_TITLE; ?></title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f0f2f7; color: #1a1a2e; min-height: 100vh; }

/* ── Top bar ── */
.topbar {
    background: #002D45;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    height: 62px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 12px rgba(0,0,0,0.18);
}
.topbar-brand { display: flex; align-items: center; gap: 14px; }
.topbar-brand img { height: 40px; }
.topbar-brand span { font-size: 16px; font-weight: 700; }
.topbar-right { display: flex; align-items: center; gap: 18px; font-size: 14px; }
.topbar-right a { color: rgba(255,255,255,0.75); text-decoration: none; }
.topbar-right a:hover { color: #f4821f; }
.badge-red { background: #e74c3c; color: #fff; border-radius: 50px; font-size: 11px; font-weight: 700; padding: 1px 7px; margin-left: 4px; }

/* ── Main layout ── */
.main { max-width: 1300px; margin: 0 auto; padding: 28px 24px; }

/* ── Stat cards ── */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 22px 20px;
    border-left: 4px solid #f4821f;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}
.stat-card.blue  { border-color: #002D45; }
.stat-card.green { border-color: #2ecc71; }
.stat-card.red   { border-color: #e74c3c; }
.stat-card.purple{ border-color: #9b59b6; }
.stat-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: #888; margin-bottom: 8px; }
.stat-value { font-size: 32px; font-weight: 800; color: #1a1a2e; line-height: 1; }
.stat-sub   { font-size: 12px; color: #999; margin-top: 5px; }

/* ── Tabs ── */
.tabs { display: flex; gap: 6px; margin-bottom: 22px; }
.tab-btn {
    padding: 9px 22px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    border: 2px solid transparent;
    background: #fff;
    color: #555;
    text-decoration: none;
    transition: all .2s;
}
.tab-btn.active, .tab-btn:hover { background: #f4821f; color: #fff; border-color: #f4821f; }

/* ── Filter bar ── */
.filter-bar {
    background: #fff;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 18px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-end;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.filter-bar form { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; width: 100%; }
.fb-group { display: flex; flex-direction: column; gap: 5px; }
.fb-group label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #888; }
.fb-group select,
.fb-group input[type=text] {
    height: 38px;
    padding: 0 12px;
    border: 1.5px solid #dde1ea;
    border-radius: 7px;
    font-size: 14px;
    color: #1a1a2e;
    background: #f8f9ff;
    outline: none;
    min-width: 140px;
}
.fb-group select:focus,
.fb-group input:focus { border-color: #f4821f; background: #fff; }
.btn { padding: 9px 20px; border-radius: 7px; font-size: 14px; font-weight: 700; border: none; cursor: pointer; transition: .2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-orange { background: #f4821f; color: #fff; }
.btn-orange:hover { background: #e07318; }
.btn-navy  { background: #002D45; color: #fff; }
.btn-navy:hover { background: #013857; }
.btn-sm { padding: 5px 14px; font-size: 12px; }
.btn-green  { background: #2ecc71; color: #fff; }
.btn-red    { background: #e74c3c; color: #fff; }
.btn-yellow { background: #f39c12; color: #fff; }

/* ── Card ── */
.card { background: #fff; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 24px; }
.card-header { padding: 18px 24px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; }
.card-header h3 { font-size: 16px; font-weight: 700; color: #002D45; }

/* ── Table ── */
.tbl-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
thead th {
    background: #f8f9ff;
    padding: 12px 14px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #888;
    white-space: nowrap;
    border-bottom: 1.5px solid #eef0f5;
}
tbody td { padding: 13px 14px; border-bottom: 1px solid #f4f5f8; vertical-align: middle; }
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover td { background: #fafbff; }
.text-muted { color: #999; font-size: 12px; }
.nowrap { white-space: nowrap; }

/* ── Pagination ── */
.pagination { display: flex; gap: 6px; align-items: center; justify-content: center; padding: 20px 24px; border-top: 1px solid #f0f0f0; flex-wrap: wrap; }
.pg-btn { padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 600; text-decoration: none; color: #555; background: #f0f2f7; border: 1px solid #dde1ea; }
.pg-btn.active { background: #f4821f; color: #fff; border-color: #f4821f; }
.pg-btn:hover:not(.active) { background: #e0e4ef; }
.pg-info { font-size: 13px; color: #888; }

/* ── Unread row ── */
.unread-row td { font-weight: 700 !important; background: #fffaf5 !important; }

/* ── Status select in table ── */
.status-form select { height: 30px; padding: 0 8px; border: 1px solid #dde1ea; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; background: #f8f9ff; }
.status-form button { height: 30px; padding: 0 12px; font-size: 12px; }

/* ── Responsive: tablet ── */
@media (max-width: 900px) {
    .stats-grid { grid-template-columns: repeat(3, 1fr); }
}

/* ── Responsive: mobile ── */
@media (max-width: 600px) {
    .topbar { padding: 0 14px; height: 54px; }
    .topbar-brand span { font-size: 13px; }
    .topbar-right { gap: 10px; font-size: 12px; }
    .main { padding: 16px 14px; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .filter-bar { padding: 14px 14px; }
    .filter-bar form { flex-direction: column; }
    .fb-group select,
    .fb-group input[type=text] { min-width: 0; width: 100%; }
    .card-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .tabs { flex-wrap: wrap; }
    .tab-btn { flex: 1; text-align: center; padding: 8px 14px; }
}

/* ── Responsive: small mobile ── */
@media (max-width: 400px) {
    .topbar-brand span { display: none; }
    .topbar { padding: 0 10px; }
    .topbar-brand img { height: 34px; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .stat-value { font-size: 26px; }
    .topbar-right a:first-child { display: none; }
}
</style>
</head>
<body>

<!-- Top Bar -->
<header class="topbar">
    <div class="topbar-brand">
        <img src="../assets/Summer Camp Logos/ISC Logo.svg" alt="ISC">
        <span>ISC 2026 Admin</span>
    </div>
    <div class="topbar-right">
        <a href="../index.php" target="_blank">View Website</a>
        <a href="export.php?<?php echo http_build_query(array_filter(['package'=>$pkg,'track'=>$track,'status'=>$status,'search'=>$search])); ?>" class="btn btn-orange btn-sm">Export CSV</a>
        <a href="logout.php">Sign Out</a>
    </div>
</header>

<main class="main">

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-label">Total Registrations</div>
            <div class="stat-value"><?php echo $totalReg; ?></div>
            <div class="stat-sub"><?php echo $totalPending; ?> pending</div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Confirmed</div>
            <div class="stat-value"><?php echo $totalConfirmed; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Early Bird</div>
            <div class="stat-value"><?php echo $stats['Early Bird'] ?? 0; ?></div>
            <div class="stat-sub">₦45,000/child</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Standard</div>
            <div class="stat-value"><?php echo $stats['Standard'] ?? 0; ?></div>
            <div class="stat-sub">₦55,000/child</div>
        </div>
        <div class="stat-card red">
            <div class="stat-label">Premium</div>
            <div class="stat-value"><?php echo $stats['Premium'] ?? 0; ?></div>
            <div class="stat-sub">₦70,000/child</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Unread Messages</div>
            <div class="stat-value"><?php echo $unreadMsgs; ?></div>
            <div class="stat-sub">Contact enquiries</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <a href="?tab=registrations" class="tab-btn <?php echo $tab === 'registrations' ? 'active' : ''; ?>">
            Registrations (<?php echo $totalReg; ?>)
        </a>
        <a href="?tab=messages" class="tab-btn <?php echo $tab === 'messages' ? 'active' : ''; ?>">
            Messages <?php if ($unreadMsgs): ?><span class="badge-red"><?php echo $unreadMsgs; ?></span><?php endif; ?>
        </a>
    </div>

    <?php if ($tab === 'registrations'): ?>
    <!-- ======================================================
         REGISTRATIONS TAB
    ====================================================== -->

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="get">
            <input type="hidden" name="tab" value="registrations">

            <div class="fb-group">
                <label>Search</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name, email or phone…">
            </div>
            <div class="fb-group">
                <label>Package</label>
                <select name="package">
                    <option value="">All Packages</option>
                    <?php foreach (['Early Bird','Standard','Premium'] as $p): ?>
                    <option value="<?php echo $p; ?>" <?php echo $pkg === $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="fb-group">
                <label>Track</label>
                <select name="track">
                    <option value="">All Tracks</option>
                    <?php foreach (['Technology','Entrepreneurship','Vocational Skills'] as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo $track === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="fb-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Statuses</option>
                    <?php foreach (['pending','confirmed','cancelled'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $status === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="fb-group" style="justify-content:flex-end;">
                <button type="submit" class="btn btn-orange">Filter</button>
                <a href="?tab=registrations" class="btn btn-navy" style="margin-top:0;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Registrations Table -->
    <div class="card">
        <div class="card-header">
            <h3>Registrations
                <?php if ($totalFiltered !== $totalReg): ?>
                <span style="font-size:13px;color:#888;font-weight:500;"> — <?php echo $totalFiltered; ?> matching filter</span>
                <?php endif; ?>
            </h3>
            <a href="export.php?<?php echo http_build_query(array_filter(['package'=>$pkg,'track'=>$track,'status'=>$status,'search'=>$search])); ?>"
               class="btn btn-navy btn-sm">Export CSV</a>
        </div>
        <div class="tbl-wrap">
            <?php if (empty($registrations)): ?>
            <p style="padding:30px 24px;color:#999;text-align:center;">No registrations found.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Age</th>
                        <th>Track</th>
                        <th>Package</th>
                        <th>Amount Due</th>
                        <th>Parent</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Submitted</th>
                        <th></th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($registrations as $r): ?>
                <tr>
                    <td class="text-muted nowrap"><?php echo $r['id']; ?></td>
                    <td class="nowrap">
                        <strong><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></strong>
                        <div class="text-muted"><?php echo htmlspecialchars($r['gender']); ?> · <?php echo htmlspecialchars($r['school']); ?></div>
                    </td>
                    <td class="nowrap"><?php echo $r['age']; ?> yrs</td>
                    <td class="nowrap">
                        <?php echo htmlspecialchars($r['learning_track']); ?>
                        <div class="text-muted" style="max-width:160px;white-space:normal;"><?php echo htmlspecialchars($r['courses']); ?></div>
                    </td>
                    <td class="nowrap">
                        <?php
                        $pkgColors = ['Early Bird'=>'#f4821f','Standard'=>'#002D45','Premium'=>'#9b59b6'];
                        $pc = $pkgColors[$r['package']] ?? '#555';
                        ?>
                        <span style="color:<?php echo $pc; ?>;font-weight:700;"><?php echo htmlspecialchars($r['package']); ?></span>
                    </td>
                    <td class="nowrap">
                        <?php if (!empty($r['amount_to_pay'])): ?>
                        <strong style="color:#002D45;">&#8358;<?php echo number_format((int)$r['amount_to_pay']); ?></strong>
                        <?php else: ?>
                        <span style="color:#999;font-size:12px;">Group rate</span>
                        <?php endif; ?>
                    </td>
                    <td class="nowrap"><?php echo htmlspecialchars($r['parent_name']); ?></td>
                    <td class="nowrap"><a href="tel:<?php echo htmlspecialchars($r['phone']); ?>" style="color:#f4821f;text-decoration:none;"><?php echo htmlspecialchars($r['phone']); ?></a></td>
                    <td class="nowrap"><a href="mailto:<?php echo htmlspecialchars($r['email']); ?>" style="color:#f4821f;text-decoration:none;"><?php echo htmlspecialchars($r['email']); ?></a></td>
                    <td class="nowrap text-muted"><?php echo date('d M Y', strtotime($r['created_at'])); ?><br><?php echo date('H:i', strtotime($r['created_at'])); ?></td>
                    <td class="nowrap">
                        <form method="post" class="status-form" style="display:flex;gap:6px;align-items:center;">
                            <input type="hidden" name="action"  value="update_status">
                            <input type="hidden" name="reg_id" value="<?php echo $r['id']; ?>">
                            <?php foreach ($_GET as $k => $v): if ($k !== 'page'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
                            <?php endif; endforeach; ?>
                            <select name="new_status" onchange="this.form.submit()">
                                <?php foreach (['pending','confirmed','cancelled'] as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $r['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td class="nowrap">
                        <a href="view.php?id=<?php echo $r['id']; ?>" style="display:inline-block;background:#002D45;color:#fff;text-decoration:none;padding:4px 12px;border-radius:6px;font-size:12px;font-weight:700;">View →</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <span class="pg-info">Showing <?php echo ($offset + 1); ?>–<?php echo min($offset + $perPage, $totalFiltered); ?> of <?php echo $totalFiltered; ?></span>
            <?php if ($page > 1): ?>
            <a href="<?php echo pageURL($page - 1); ?>" class="pg-btn">‹ Prev</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end   = min($totalPages, $page + 2);
            for ($p = $start; $p <= $end; $p++):
            ?>
            <a href="<?php echo pageURL($p); ?>" class="pg-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="<?php echo pageURL($page + 1); ?>" class="pg-btn">Next ›</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- ======================================================
         MESSAGES TAB
    ====================================================== -->
    <div class="card">
        <div class="card-header">
            <h3>Contact Messages</h3>
            <?php if ($unreadMsgs): ?>
            <span style="background:#f4821f;color:#fff;padding:4px 14px;border-radius:20px;font-size:13px;font-weight:700;"><?php echo $unreadMsgs; ?> unread</span>
            <?php endif; ?>
        </div>
        <div class="tbl-wrap">
            <?php if (empty($msgs)): ?>
            <p style="padding:30px 24px;color:#999;text-align:center;">No messages yet.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($msgs as $m): ?>
                <tr <?php echo !$m['is_read'] ? 'class="unread-row"' : ''; ?>>
                    <td class="text-muted"><?php echo $m['id']; ?></td>
                    <td class="nowrap"><?php echo htmlspecialchars($m['name']); ?></td>
                    <td class="nowrap"><a href="mailto:<?php echo htmlspecialchars($m['email']); ?>" style="color:#f4821f;text-decoration:none;"><?php echo htmlspecialchars($m['email']); ?></a></td>
                    <td class="nowrap"><?php echo htmlspecialchars($m['phone']); ?></td>
                    <td class="nowrap"><?php echo htmlspecialchars($m['subject']); ?></td>
                    <td style="max-width:280px;white-space:pre-wrap;word-break:break-word;"><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
                    <td class="nowrap text-muted"><?php echo date('d M Y H:i', strtotime($m['created_at'])); ?></td>
                    <td class="nowrap">
                        <?php if (!$m['is_read']): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="mark_read">
                            <input type="hidden" name="msg_id" value="<?php echo $m['id']; ?>">
                            <input type="hidden" name="tab" value="messages">
                            <button type="submit" class="btn btn-green btn-sm">Mark Read</button>
                        </form>
                        <?php else: ?>
                        <span class="text-muted">Read</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</main>
</body>
</html>
