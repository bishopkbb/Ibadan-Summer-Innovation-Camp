<?php
session_start();
require_once('../config/db.php');

/* ===================================================
   Helpers
   =================================================== */
function sanitize(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

function redirect(string $page, string $key, string $msg): never {
    $_SESSION[$key] = $msg;
    header('Location: ../' . $page);
    exit;
}

/* ===================================================
   Method & CSRF guards
   =================================================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('registration.php', 'reg_error', 'Invalid request method.');
}

if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    redirect('registration.php', 'reg_error', 'Security token mismatch. Please refresh and try again.');
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

/* ===================================================
   Shared (per-family) fields
   =================================================== */
$parent_name    = sanitize($_POST['parent_name']    ?? '');
$relationship   = sanitize($_POST['relationship']   ?? '');
$phone          = sanitize($_POST['phone']          ?? '');
$alt_phone      = sanitize($_POST['alt_phone']      ?? '');
$email          = filter_var(trim($_POST['email']   ?? ''), FILTER_SANITIZE_EMAIL);
$parent_address = sanitize($_POST['parent_address'] ?? '');
$package        = sanitize($_POST['package']        ?? '');

/* ===================================================
   Resolve child count from actual POST arrays
   =================================================== */
$raw_first_names = $_POST['first_name'] ?? [];
if (!is_array($raw_first_names)) {
    $raw_first_names = [$raw_first_names];
}
$num_children = max(1, min(4, count($raw_first_names)));

/* ===================================================
   Allowed values
   =================================================== */
$allowed_tracks = ['Technology','Entrepreneurship','Vocational Skills'];
$track_courses  = [
    'Technology'        => ['Coding & Programming','Robotics','Web Design','Graphic Design','UI/UX Design','Video Editing'],
    'Entrepreneurship'  => ['Branding','Digital Marketing','Sales Skills','Startup Fundamentals','Business Pitching','Financial Planning'],
    'Vocational Skills' => ['Fashion Design','Baking & Pastry','Bead Making','Hair Styling','Soap Production','DIY Crafts'],
];
/* Note: front-end filters courses by age tier (junior/mid/senior).
   Back-end validates against the full track list so all tier subsets pass. */

/* ===================================================
   Collect & validate each child
   =================================================== */
$errors   = [];
$children = [];

function postArr(string $key, int $i): string {
    $val = $_POST[$key] ?? '';
    return is_array($val) ? ($val[$i] ?? '') : (string)$val;
}

for ($i = 0; $i < $num_children; $i++) {
    $lbl = $num_children === 1 ? 'Student' : 'Child ' . ($i + 1);

    $fn       = sanitize(postArr('first_name',    $i));
    $ln       = sanitize(postArr('last_name',     $i));
    $on       = sanitize(postArr('other_name',    $i));
    $gender   = sanitize(postArr('gender',        $i));
    $dob_raw  = sanitize(postArr('date_of_birth', $i));
    $age      = (int)   postArr('age',            $i);
    $school   = sanitize(postArr('school',        $i));
    $grade    = sanitize(postArr('class_grade',   $i));
    $addr     = sanitize(postArr('address',       $i));
    $track    = sanitize(postArr('learning_track',$i));
    $craw     = sanitize(postArr('courses',       $i));
    $med_cond = sanitize(postArr('medical_condition', $i));
    $allergies= sanitize(postArr('allergies',     $i));
    $em_name  = sanitize(postArr('emergency_contact', $i));
    $em_phone = sanitize(postArr('emergency_phone',   $i));

    /* student validation */
    if (strlen($fn) < 2) $errors[] = "$lbl: First name is required.";
    if (strlen($ln) < 2) $errors[] = "$lbl: Last name is required.";
    if (!in_array($gender, ['Male','Female'])) $errors[] = "$lbl: Please select a valid gender.";

    $dob = null;
    if (!empty($dob_raw)) {
        $dobObj = DateTime::createFromFormat('Y-m-d', $dob_raw);
        if (!$dobObj) {
            $errors[] = "$lbl: Invalid date of birth.";
        } else {
            $dob = $dobObj->format('Y-m-d');
            $calc_age = (new DateTime())->diff($dobObj)->y;
            if ($calc_age < 7 || $calc_age > 18) {
                $errors[] = "$lbl: Age must be between 7 and 18 years.";
            }
        }
    } else {
        $errors[] = "$lbl: Date of birth is required.";
    }

    if ($age < 7 || $age > 18)  $errors[] = "$lbl: Please select a valid age (7–18).";
    if (strlen($school) < 3)    $errors[] = "$lbl: School name is required.";
    if (strlen($grade) < 1)     $errors[] = "$lbl: Class / grade is required.";
    if (strlen($addr) < 5)      $errors[] = "$lbl: Home address is required.";

    /* track & courses */
    if (!in_array($track, $allowed_tracks)) {
        $errors[] = "$lbl: Please select a valid learning track.";
    }
    $selected_courses = [];
    if (!empty($craw)) {
        $valid = $track_courses[$track] ?? [];
        foreach (array_map('trim', explode(',', $craw)) as $c) {
            if (in_array($c, $valid)) $selected_courses[] = $c;
        }
    }
    if (empty($selected_courses)) $errors[] = "$lbl: Please select at least one course.";
    $courses_str = implode(', ', $selected_courses);

    /* emergency contact */
    if (strlen($em_name) < 3) $errors[] = "$lbl: Emergency contact name is required.";
    if (!preg_match('/^[+]?[\d\s\-()]{7,20}$/', $em_phone)) {
        $errors[] = "$lbl: Please enter a valid emergency contact phone.";
    }

    $children[] = compact('fn','ln','on','gender','dob','age','school','grade','addr','track','courses_str','med_cond','allergies','em_name','em_phone');
}

/* ===================================================
   Validate shared parent info
   =================================================== */
if (strlen($parent_name) < 3)   $errors[] = 'Parent/guardian name is required.';
if (empty($relationship))        $errors[] = 'Relationship to student is required.';
if (!preg_match('/^[+]?[\d\s\-()]{7,20}$/', $phone)) $errors[] = 'Please enter a valid phone number.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))        $errors[] = 'Please enter a valid email address.';
if (strlen($parent_address) < 5) $errors[] = 'Parent/guardian address is required.';

if (!in_array($package, ['Early Bird','Standard','Premium'])) {
    $errors[] = 'Please select a valid package.';
} elseif ($package === 'Early Bird' && new DateTime() >= new DateTime('2026-07-20 00:00:00')) {
    $errors[] = 'The Early Bird offer has expired. Please select the Standard or Premium package.';
}

if (empty($_POST['consent_participate'])) $errors[] = 'You must consent to your child\'s participation.';
if (empty($_POST['consent_medical']))     $errors[] = 'You must confirm the medical consent.';
if (empty($_POST['consent_rules']))       $errors[] = 'You must agree to the camp rules.';
if (empty($_POST['consent_payment']))     $errors[] = 'You must acknowledge the payment terms.';

/* ===================================================
   Return errors
   =================================================== */
if (!empty($errors)) {
    redirect('registration.php', 'reg_error', implode(' | ', $errors));
}

/* ===================================================
   Insert one row per child
   =================================================== */
$conn = getDBConnection();

$stmt = $conn->prepare("
    INSERT INTO registrations (
        first_name, last_name, other_name, gender, date_of_birth, age,
        school, class_grade, address,
        parent_name, relationship, phone, alt_phone, email, parent_address,
        learning_track, courses,
        medical_condition, allergies,
        emergency_contact, emergency_phone,
        package, number_of_children,
        created_at
    ) VALUES (
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?,
        ?, ?,
        ?, ?,
        ?, ?,
        NOW()
    )
");

if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    redirect('registration.php', 'reg_error', 'A server error occurred. Please try again or contact us directly.');
}

foreach ($children as $child) {
    $stmt->bind_param(
        'sssssississsssssssssssi',
        $child['fn'], $child['ln'], $child['on'], $child['gender'], $child['dob'], $child['age'],
        $child['school'], $child['grade'], $child['addr'],
        $parent_name, $relationship, $phone, $alt_phone, $email, $parent_address,
        $child['track'], $child['courses_str'],
        $child['med_cond'], $child['allergies'],
        $child['em_name'], $child['em_phone'],
        $package, $num_children
    );

    if (!$stmt->execute()) {
        error_log('Execute failed: ' . $stmt->error);
        $stmt->close();
        $conn->close();
        redirect('registration.php', 'reg_error', 'Registration could not be saved. Please try again or contact us at hello@traceworka.ng.');
    }
}

$stmt->close();
$conn->close();

/* ===================================================
   Confirmation email
   =================================================== */
$child_names = implode(' & ', array_map(fn($c) => $c['fn'] . ' ' . $c['ln'], $children));
$child_list  = implode("\n", array_map(
    fn($c, $idx) => '  ' . ($idx + 1) . '. ' . $c['fn'] . ' ' . $c['ln'] . ' — ' . $c['track'],
    $children, array_keys($children)
));

$subject = 'Registration Received – Ibadan Summer Innovation Camp 2026';
$body    = "Dear {$parent_name},\n\n";
$body   .= "Thank you for registering " . ($num_children > 1 ? "your children" : "{$children[0]['fn']} {$children[0]['ln']}") . " for the Ibadan Summer Innovation Camp 2026.\n\n";
$body   .= "Children Registered:\n{$child_list}\n";
$body   .= "Package: {$package}\n\n";
$body   .= "Our team will contact you within 24 hours with payment details to confirm " . ($num_children > 1 ? "their places" : "your child's place") . ".\n\n";
$body   .= "Camp Dates: August 3–27, 2026 | Monday–Thursday | 9:00 AM – 3:00 PM\n";
$body   .= "Venue: Traceworka Innovative Solutions Limited, Kongi-Bodija, Ibadan\n\n";
$body   .= "For enquiries: hello@traceworka.ng | +234 907 154 3344\n\n";
$body   .= "See you at camp!\n-- Ibadan Summer Innovation Camp Team";
$headers = "From: hello@traceworka.ng\r\nReply-To: hello@traceworka.ng\r\nX-Mailer: PHP/" . phpversion();
@mail($email, $subject, $body, $headers);

/* ===================================================
   Admin notification email → hello@traceworka.ng
   =================================================== */
$admin_subject = "[ISC2026] New Registration – {$child_names} – {$package}";
$admin_body    = "NEW CAMP REGISTRATION\n" . str_repeat('=', 52) . "\n\n";
$admin_body   .= "Package:    {$package}\n";
$admin_body   .= "Children:   {$num_children}\n";
$admin_body   .= "Submitted:  " . date('d M Y, H:i') . "\n\n";
$admin_body   .= "PARENT / GUARDIAN\n" . str_repeat('-', 30) . "\n";
$admin_body   .= "Name:         {$parent_name}\n";
$admin_body   .= "Relationship: {$relationship}\n";
$admin_body   .= "Phone:        {$phone}\n";
$admin_body   .= "Alt Phone:    " . ($alt_phone ?: '—') . "\n";
$admin_body   .= "Email:        {$email}\n";
$admin_body   .= "Address:      {$parent_address}\n\n";

foreach ($children as $idx => $c) {
    $lbl = $num_children === 1 ? 'STUDENT' : 'CHILD ' . ($idx + 1);
    $admin_body .= "{$lbl}\n" . str_repeat('-', 30) . "\n";
    $admin_body .= "Name:           {$c['fn']} {$c['ln']}" . ($c['on'] ? " {$c['on']}" : '') . "\n";
    $admin_body .= "Gender:         {$c['gender']}\n";
    $admin_body .= "DOB / Age:      {$c['dob']} ({$c['age']} yrs)\n";
    $admin_body .= "School:         {$c['school']}\n";
    $admin_body .= "Class/Grade:    {$c['grade']}\n";
    $admin_body .= "Track:          {$c['track']}\n";
    $admin_body .= "Courses:        {$c['courses_str']}\n";
    $admin_body .= "Medical:        " . ($c['med_cond'] ?: 'None') . "\n";
    $admin_body .= "Allergies:      " . ($c['allergies'] ?: 'None') . "\n";
    $admin_body .= "Emerg. Contact: {$c['em_name']} — {$c['em_phone']}\n\n";
}

$admin_headers = "From: hello@traceworka.ng\r\nReply-To: {$email}\r\nX-Mailer: PHP/" . phpversion();
@mail('hello@traceworka.ng', $admin_subject, $admin_body, $admin_headers);

/* ===================================================
   Success redirect
   =================================================== */
$success_text = $num_children === 1
    ? "Registration submitted! Thank you, {$parent_name}. We will contact you at {$email} within 24 hours with payment details to confirm {$children[0]['fn']}'s space."
    : "Registration submitted! Thank you, {$parent_name}. We have received details for {$child_names}. We will contact you at {$email} within 24 hours with payment information.";

redirect('registration.php', 'reg_success', $success_text);
