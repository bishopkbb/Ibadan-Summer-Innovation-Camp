<?php
session_start();
require_once('config.php');
require_once('../config/db.php');

if (empty($_SESSION[ADMIN_SESSION_KEY])) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

/* ── Filters (same as dashboard) ── */
$pkg    = $_GET['package'] ?? '';
$track  = $_GET['track']   ?? '';
$status = $_GET['status']  ?? '';
$search = trim($_GET['search'] ?? '');

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

$sql  = "SELECT * FROM registrations $whereSQL ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();

/* ── Stream CSV ── */
$filename = 'isc2026-registrations-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');

$out = fopen('php://output', 'w');
// UTF-8 BOM for Excel compatibility
fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($out, [
    'ID', 'Camp ID', 'First Name', 'Last Name', 'Other Name', 'Gender', 'Date of Birth', 'Age',
    'School', 'Class/Grade', 'Student Address',
    'Parent/Guardian', 'Relationship', 'Phone', 'Alt Phone', 'Email', 'Parent Address',
    'Learning Track', 'Courses', 'Mode of Instruction',
    'Medical Condition', 'Allergies', 'Emergency Contact', 'Emergency Phone', 'Emergency Relationship',
    'Package', 'No. of Children', 'Amount Due (NGN)', 'Status', 'Date Registered',
]);

while ($row = $result->fetch_assoc()) {
    fputcsv($out, [
        $row['id'],
        $row['camp_id'] ?? '',
        $row['first_name'],
        $row['last_name'],
        $row['other_name'] ?? '',
        $row['gender'],
        $row['date_of_birth'],
        $row['age'],
        $row['school'],
        $row['class_grade'],
        $row['address'],
        $row['parent_name'],
        $row['relationship'],
        $row['phone'],
        $row['alt_phone'] ?? '',
        $row['email'],
        $row['parent_address'],
        $row['learning_track'],
        $row['courses'],
        $row['mode_of_instruction'] ?? 'Physical',
        $row['medical_condition'] ?? '',
        $row['allergies'] ?? '',
        $row['emergency_contact'],
        $row['emergency_phone'],
        $row['emergency_relationship'] ?? '',
        $row['package'],
        $row['number_of_children'],
        $row['amount_to_pay'] ? (int)$row['amount_to_pay'] : 'Group rate',
        $row['status'],
        $row['created_at'],
    ]);
}

fclose($out);
exit;
