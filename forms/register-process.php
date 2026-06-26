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

// Honeypot: bots fill hidden fields, humans don't
if (!empty($_POST['website'])) {
    redirect('registration.php', 'reg_error', 'Submission blocked.');
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
    $em_name  = sanitize(postArr('emergency_contact',      $i));
    $em_phone = sanitize(postArr('emergency_phone',        $i));
    $em_rel   = sanitize(postArr('emergency_relationship', $i));

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

    $children[] = compact('fn','ln','on','gender','dob','age','school','grade','addr','track','courses_str','med_cond','allergies','em_name','em_phone','em_rel');
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
   Calculate total amount payable
   Mirrors the JS logic in registration.php exactly:
     1 child  → full base price
     2 children → base + round(base × 0.90)   [10% off 2nd]
     3 children → base + round(base × 0.90) + round(base × 0.85) [10% & 15% off]
     4+ children → group rate (no fixed total)
   =================================================== */
$pkg_prices = ['Early Bird' => 45000, 'Standard' => 55000, 'Premium' => 70000];
$base_price = $pkg_prices[$package] ?? 0;

$amount_rows   = [];   // per-child breakdown for email
$total_amount  = 0;
$is_group_rate = false;

if ($num_children === 1) {
    $amount_rows[]  = ['label' => $children[0]['fn'] . ' ' . $children[0]['ln'], 'amount' => $base_price, 'note' => ''];
    $total_amount   = $base_price;
} elseif ($num_children === 2) {
    $c2_price       = (int) round($base_price * 0.90);
    $amount_rows[]  = ['label' => $children[0]['fn'] . ' ' . $children[0]['ln'], 'amount' => $base_price,  'note' => ''];
    $amount_rows[]  = ['label' => $children[1]['fn'] . ' ' . $children[1]['ln'], 'amount' => $c2_price,    'note' => '10% family discount'];
    $total_amount   = $base_price + $c2_price;
} elseif ($num_children === 3) {
    $c2_price       = (int) round($base_price * 0.90);
    $c3_price       = (int) round($base_price * 0.85);
    $amount_rows[]  = ['label' => $children[0]['fn'] . ' ' . $children[0]['ln'], 'amount' => $base_price,  'note' => ''];
    $amount_rows[]  = ['label' => $children[1]['fn'] . ' ' . $children[1]['ln'], 'amount' => $c2_price,    'note' => '10% family discount'];
    $amount_rows[]  = ['label' => $children[2]['fn'] . ' ' . $children[2]['ln'], 'amount' => $c3_price,    'note' => '15% family discount'];
    $total_amount   = $base_price + $c2_price + $c3_price;
} else {
    $is_group_rate  = true;
    foreach ($children as $c) {
        $amount_rows[] = ['label' => $c['fn'] . ' ' . $c['ln'], 'amount' => $base_price, 'note' => ''];
    }
}

function fmt_naira(int $amount): string {
    return '&#8358;' . number_format($amount);
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
        emergency_contact, emergency_phone, emergency_relationship,
        package, number_of_children, amount_to_pay,
        created_at
    ) VALUES (
        ?, ?, ?, ?, ?, ?,
        ?, ?, ?,
        ?, ?, ?, ?, ?, ?,
        ?, ?,
        ?, ?,
        ?, ?, ?,
        ?, ?, ?,
        NOW()
    )
");

if (!$stmt) {
    error_log('Prepare failed: ' . $conn->error);
    redirect('registration.php', 'reg_error', 'A server error occurred. Please try again or contact us directly.');
}

$db_amount = $is_group_rate ? null : $total_amount;

foreach ($children as $child) {
    $stmt->bind_param(
        'sssssisssssssssssssssssii',
        $child['fn'], $child['ln'], $child['on'], $child['gender'], $child['dob'], $child['age'],
        $child['school'], $child['grade'], $child['addr'],
        $parent_name, $relationship, $phone, $alt_phone, $email, $parent_address,
        $child['track'], $child['courses_str'],
        $child['med_cond'], $child['allergies'],
        $child['em_name'], $child['em_phone'], $child['em_rel'],
        $package, $num_children, $db_amount
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
   Confirmation email — HTML
   =================================================== */
$child_names = implode(' & ', array_map(fn($c) => $c['fn'] . ' ' . $c['ln'], $children));

/* Build the registered children summary rows */
$child_rows_html = '';
foreach ($children as $idx => $c) {
    $label = $num_children > 1 ? ($idx + 1) . '. ' : '';
    $child_rows_html .= '
        <tr>
            <td style="padding:10px 14px;border-bottom:1px solid #f0f0f0;color:#1a1a2e;font-size:15px;">
                ' . htmlspecialchars($label . $c['fn'] . ' ' . $c['ln']) . '
            </td>
            <td style="padding:10px 14px;border-bottom:1px solid #f0f0f0;color:#555;font-size:15px;">
                ' . htmlspecialchars($c['track']) . '
            </td>
        </tr>';
}

$subject = 'Registration Received – Ibadan Summer Innovation Camp 2026';

$body = '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

      <!-- Header -->
      <tr>
        <td style="background:#002D45;padding:32px 36px;text-align:center;">
          <p style="margin:0 0 6px;color:#f4821f;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">Ibadan Summer Innovation Camp</p>
          <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:800;line-height:1.3;">Registration Received!</h1>
          <p style="margin:10px 0 0;color:rgba(255,255,255,0.75);font-size:14px;">August 3 – 27, 2026 &nbsp;|&nbsp; Ibadan, Oyo State</p>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:36px 36px 10px;">
          <p style="margin:0 0 18px;color:#1a1a2e;font-size:16px;line-height:1.65;">Dear <strong>' . htmlspecialchars($parent_name) . '</strong>,</p>
          <p style="margin:0 0 18px;color:#444;font-size:15px;line-height:1.7;">
            Thank you for registering your child' . ($num_children > 1 ? 'ren' : '') . ' for the <strong>Ibadan Summer Innovation Camp 2026!</strong>
          </p>
          <p style="margin:0 0 24px;color:#444;font-size:15px;line-height:1.7;">
            We are excited to welcome ' . ($num_children > 1 ? 'them' : 'your child') . ' to an enriching and fun-filled learning experience focused on innovation, technology, creativity, entrepreneurship, and personal development.
          </p>

          <!-- Registered Children -->
          <p style="margin:0 0 10px;color:#1a1a2e;font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">
            ' . ($num_children > 1 ? 'Children Registered' : 'Child Registered') . '
          </p>
          <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e8eaf0;border-radius:8px;overflow:hidden;margin-bottom:28px;">
            <tr style="background:#f8f9ff;">
              <th style="padding:10px 14px;text-align:left;font-size:13px;color:#555;font-weight:700;">Name</th>
              <th style="padding:10px 14px;text-align:left;font-size:13px;color:#555;font-weight:700;">Learning Track</th>
            </tr>
            ' . $child_rows_html . '
            <tr style="background:#f8f9ff;">
              <td colspan="2" style="padding:10px 14px;font-size:13px;color:#555;">
                Package: <strong style="color:#f4821f;">' . htmlspecialchars($package) . '</strong>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- Amount Summary -->
      <tr>
        <td style="padding:0 36px 28px;">
          <p style="margin:0 0 10px;color:#1a1a2e;font-size:14px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Amount to Pay</p>
          <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e8eaf0;border-radius:8px;overflow:hidden;">
            <tr style="background:#f8f9ff;">
              <th style="padding:10px 14px;text-align:left;font-size:13px;color:#555;font-weight:700;">Child</th>
              <th style="padding:10px 14px;text-align:right;font-size:13px;color:#555;font-weight:700;">Amount</th>
            </tr>
            ' . (function() use ($amount_rows, $is_group_rate, $base_price) {
                $rows = '';
                foreach ($amount_rows as $r) {
                    $note = $r['note']
                        ? ' <span style="font-size:11px;color:#27ae60;font-weight:700;background:#e8f8f0;padding:1px 6px;border-radius:10px;">' . htmlspecialchars($r['note']) . '</span>'
                        : '';
                    $amt  = $is_group_rate
                        ? '<span style="font-size:12px;color:#888;">See note below</span>'
                        : fmt_naira($r['amount']);
                    $rows .= '<tr>
                        <td style="padding:10px 14px;border-top:1px solid #f0f0f0;font-size:14px;color:#1a1a2e;">'
                            . htmlspecialchars($r['label']) . $note .
                        '</td>
                        <td style="padding:10px 14px;border-top:1px solid #f0f0f0;font-size:14px;color:#1a1a2e;text-align:right;font-weight:600;">'
                            . $amt .
                        '</td>
                    </tr>';
                }
                return $rows;
            })() . '
            ' . (!$is_group_rate ? '
            <tr style="background:#002D45;">
              <td style="padding:12px 14px;font-size:15px;color:#fff;font-weight:800;">Total Amount Due</td>
              <td style="padding:12px 14px;font-size:18px;color:#f4821f;font-weight:900;text-align:right;">' . fmt_naira($total_amount) . '</td>
            </tr>' : '
            <tr style="background:#fff8f0;">
              <td colspan="2" style="padding:12px 14px;font-size:13px;color:#b7600a;font-weight:600;">
                &#9432; Group rate applies for ' . $num_children . ' children. Our team will confirm your total within 24 hours.
              </td>
            </tr>') . '
          </table>
        </td>
      </tr>

      <!-- Payment Section -->
      <tr>
        <td style="padding:0 36px 28px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff8f0;border:2px solid #f4821f;border-radius:10px;overflow:hidden;">
            <tr>
              <td style="background:#f4821f;padding:14px 20px;">
                <p style="margin:0;color:#fff;font-size:15px;font-weight:800;letter-spacing:0.3px;">&#128179; Payment Details</p>
              </td>
            </tr>
            <tr>
              <td style="padding:20px;">
                <p style="margin:0 0 14px;color:#444;font-size:15px;line-height:1.65;">
                  To complete your registration, kindly proceed with payment using the account details below:
                </p>
                <table cellpadding="0" cellspacing="0" style="width:100%;">
                  <tr>
                    <td style="padding:7px 0;font-size:14px;color:#888;width:140px;">Account Name</td>
                    <td style="padding:7px 0;font-size:15px;color:#1a1a2e;font-weight:700;">Traceworka Innovative Solutions Limited</td>
                  </tr>
                  <tr>
                    <td style="padding:7px 0;font-size:14px;color:#888;">Bank Name</td>
                    <td style="padding:7px 0;font-size:15px;color:#1a1a2e;font-weight:700;">GTBank</td>
                  </tr>
                  <tr>
                    <td style="padding:7px 0;font-size:14px;color:#888;">Account Number</td>
                    <td style="padding:7px 0;font-size:22px;color:#f4821f;font-weight:800;letter-spacing:2px;">0745519031</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- WhatsApp Instruction -->
      <tr>
        <td style="padding:0 36px 28px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fff4;border:1.5px solid #2ecc71;border-radius:10px;">
            <tr>
              <td style="padding:18px 20px;">
                <p style="margin:0 0 10px;color:#1a1a2e;font-size:15px;font-weight:700;">&#128241; After Making Payment</p>
                <p style="margin:0 0 10px;color:#444;font-size:15px;line-height:1.7;">
                  Please send the payment receipt or screenshot as evidence of payment via <strong>WhatsApp</strong> to:
                </p>
                <p style="margin:0 0 12px;text-align:center;">
                  <a href="https://wa.me/2349071543344" style="font-size:26px;font-weight:900;color:#1a1a2e;letter-spacing:2px;text-decoration:none;">09071543344</a>
                </p>
                <p style="margin:0;color:#555;font-size:14px;line-height:1.65;">
                  Please ensure that <strong>' . ($num_children > 1 ? 'the children\'s names are' : 'the child\'s name is') . '</strong> included when sending the payment confirmation to help us verify your registration promptly.
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- Important Notice -->
      <tr>
        <td style="padding:0 36px 28px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff5f5;border:1.5px solid #e74c3c;border-radius:10px;">
            <tr>
              <td style="padding:16px 20px;">
                <p style="margin:0;color:#c0392b;font-size:14px;line-height:1.65;">
                  <strong>&#9888;&#65039; Important:</strong> Registration will only be confirmed after payment has been received and verified.
                </p>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- Closing -->
      <tr>
        <td style="padding:0 36px 36px;">
          <p style="margin:0 0 14px;color:#444;font-size:15px;line-height:1.7;">
            If you have any questions or require assistance, please feel free to contact us at
            <a href="mailto:hello@traceworka.ng" style="color:#f4821f;text-decoration:none;font-weight:600;">hello@traceworka.ng</a>
            or call <a href="tel:+2349071543344" style="color:#f4821f;text-decoration:none;font-weight:600;">+234 907 154 3344</a>.
          </p>
          <p style="margin:0 0 24px;color:#444;font-size:15px;line-height:1.7;">
            We look forward to welcoming ' . ($num_children > 1 ? 'your children' : 'your child') . ' to an unforgettable summer of learning, innovation, and fun!
          </p>
          <p style="margin:0;color:#444;font-size:15px;">Warm regards,</p>
          <p style="margin:4px 0 0;color:#1a1a2e;font-size:16px;font-weight:800;">Ibadan Summer Innovation Camp Team</p>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#002D45;padding:20px 36px;text-align:center;">
          <p style="margin:0 0 6px;color:rgba(255,255,255,0.6);font-size:12px;">
            Traceworka Innovative Solutions Limited &nbsp;|&nbsp; Kongi-Bodija, Ibadan, Oyo State
          </p>
          <p style="margin:0;color:rgba(255,255,255,0.4);font-size:11px;">
            &copy; 2026 Ibadan Summer Innovation Camp. All rights reserved.
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>';

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: Ibadan Summer Innovation Camp <hello@traceworka.ng>\r\n";
$headers .= "Reply-To: hello@traceworka.ng\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();
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
$_SESSION['reg_success_name']     = $parent_name;
$_SESSION['reg_success_email']    = $email;
$_SESSION['reg_success_children'] = $num_children;
$_SESSION['reg_success_child1']   = $children[0]['fn'];

header('Location: ../thank-you.php');
exit;
