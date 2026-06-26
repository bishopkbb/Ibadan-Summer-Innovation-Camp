<?php
session_start();
require_once('config/db.php');
require_once('config/app.php');

$page_title = 'Registration – Ibadan Summer Innovation Camp 2026';
$meta_description = 'Register for the Ibadan Summer Innovation Camp 2026. Fill in your student and parent details, select a learning track, and choose your package. Ages 7–18.';

// Live seat count from DB
$seats_remaining = TOTAL_SEATS;
try {
    $conn = getDBConnection();
    $sr = $conn->query("SELECT COUNT(*) AS total FROM registrations WHERE status != 'cancelled'");
    if ($sr) $seats_remaining = max(0, TOTAL_SEATS - (int)$sr->fetch_assoc()['total']);
    $conn->close();
} catch (Exception $e) {}

// Pre-fill package from URL parameter
$selected_package = '';
if (!empty($_GET['package'])) {
    $allowed = ['early-bird' => 'Early Bird', 'standard' => 'Standard', 'premium' => 'Premium'];
    $key = strtolower(trim($_GET['package']));
    $selected_package = $allowed[$key] ?? '';
}

// Early Bird expires at midnight ending July 19, 2026
$early_bird_deadline = new DateTime('2026-07-20 00:00:00');
$early_bird_expired  = new DateTime() >= $early_bird_deadline;
if ($early_bird_expired && $selected_package === 'Early Bird') {
    $selected_package = '';
}

// Form repopulation after validation error
$reg_old = null;
if (!empty($_SESSION['reg_old'])) {
    $reg_old = $_SESSION['reg_old'];
    unset($_SESSION['reg_old']);
    // Override package from saved data
    if (!empty($reg_old['package']) && !($early_bird_expired && $reg_old['package'] === 'Early Bird')) {
        $selected_package = $reg_old['package'];
    }
}

function old_val(string $key, string $default = ''): string {
    global $reg_old;
    if (!$reg_old || !isset($reg_old[$key]) || $reg_old[$key] === '') return htmlspecialchars($default);
    return htmlspecialchars((string)$reg_old[$key]);
}
function old_sel(string $key, string $val): string {
    global $reg_old;
    return (!empty($reg_old[$key]) && $reg_old[$key] === $val) ? 'selected' : '';
}

// Flash messages from redirect
$success_msg = '';
$error_msg   = '';
if (!empty($_SESSION['reg_success'])) {
    $success_msg = $_SESSION['reg_success'];
    unset($_SESSION['reg_success']);
}
if (!empty($_SESSION['reg_error'])) {
    $error_msg = $_SESSION['reg_error'];
    unset($_SESSION['reg_error']);
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

include('includes/header.php');
include('includes/navbar.php');
?>

	<!-- Page Title -->
	<section class="page-title">
		<div class="color-one"></div>
		<div class="color-two"></div>
		<div class="color-three"></div>
		<div class="color-four"></div>
		<div class="auto-container">
			<h2>Camp Registration</h2>
			<ul class="bread-crumb clearfix">
				<li><a href="index.php"><i class="flaticon-home"></i> Home</a></li>
				<li>Registration</li>
			</ul>
		</div>
		<div>
			<svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
			viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
			<defs><path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" /></defs>
			<g class="parallaxx">
			<use xlink:href="#gentle-wave" x="148" y="0" fill="rgba(255,255,255,0.7" />
			<use xlink:href="#gentle-wave" x="100" y="3" fill="rgba(255,255,255,0.5)" />
			<use xlink:href="#gentle-wave" x="70" y="5" fill="rgba(255,255,255,0.3)" />
			<use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
			</g></svg>
		</div>
	</section>
	<!-- End Page Title -->

	<!-- Registration Form Section -->
	<section class="register-one" style="padding:80px 0;">
		<div class="auto-container">
			<div class="inner-container" style="max-width:900px;margin:0 auto;">

				<h3 style="margin-bottom:8px;">Ibadan Summer Innovation Camp 2026</h3>
				<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:35px;">
					<div class="text" style="margin:0;">Complete all sections below. Fields marked <span style="color:#e74c3c;font-weight:700;">*</span> are required. <a href="contact.php">Contact us</a> if you have any questions.</div>
					<div style="display:inline-flex;align-items:center;gap:8px;background:<?php echo $seats_remaining <= 10 ? '#fff5f5' : '#f0fff4'; ?>;border:1.5px solid <?php echo $seats_remaining <= 10 ? '#e74c3c' : '#2ecc71'; ?>;border-radius:30px;padding:7px 16px;font-size:13px;font-weight:700;color:<?php echo $seats_remaining <= 10 ? '#c0392b' : '#1a7a4a'; ?>;white-space:nowrap;">
						<i class="fa-solid fa-chair" style="font-size:12px;"></i>
						<?php echo $seats_remaining; ?> spot<?php echo $seats_remaining !== 1 ? 's' : ''; ?> remaining
					</div>
				</div>

				<?php if ($success_msg): ?>
				<div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:20px;border-radius:10px;margin-bottom:25px;font-weight:600;">
					<i class="fa-solid fa-circle-check" style="color:#f4821f;margin-right:8px;"></i>
					<?php echo htmlspecialchars($success_msg); ?>
				</div>
				<?php endif; ?>

				<?php if ($error_msg): ?>
				<div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:20px;border-radius:10px;margin-bottom:25px;">
					<i class="fa-solid fa-circle-xmark" style="color:#e74c3c;margin-right:8px;"></i>
					<?php echo htmlspecialchars($error_msg); ?>
				</div>
				<?php endif; ?>

				<div class="register-form">
					<form method="post" action="forms/register-process.php" id="registration-form" novalidate>
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
						<input type="text" name="website" tabindex="-1" autocomplete="off" style="display:none!important;position:absolute;left:-9999px;" aria-hidden="true" value="">
						<input type="hidden" name="form_type" value="full_registration">
						<input type="hidden" name="number_of_children" id="number_of_children" value="1">

						<!-- ===== HOW MANY CHILDREN? ===== -->
						<div class="reg-children-card" style="background:linear-gradient(135deg,#002D45,#01415b);border-radius:16px;padding:30px 32px;margin-bottom:35px;color:#fff;">
							<div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;">
								<div style="width:50px;height:50px;background:rgba(244,130,31,0.22);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
									<i class="fa-solid fa-users" style="color:#f4821f;font-size:20px;"></i>
								</div>
								<div>
									<div style="font-size:11px;opacity:0.6;text-transform:uppercase;letter-spacing:1.3px;margin-bottom:3px;">Step 1 of 4</div>
									<div style="font-size:19px;font-weight:700;letter-spacing:0.1px;">How Many Children Are You Registering?</div>
								</div>
							</div>
							<p style="font-size:14px;opacity:0.7;margin-bottom:22px;line-height:1.65;max-width:560px;">Select the number of children you are enrolling. A separate details form will appear for each child below.</p>
							<div style="display:flex;gap:10px;flex-wrap:wrap;">
								<button type="button" onclick="setChildCount(1)" id="cc-1" class="cc-btn active">1 Child</button>
								<button type="button" onclick="setChildCount(2)" id="cc-2" class="cc-btn">2 Children</button>
								<button type="button" onclick="setChildCount(3)" id="cc-3" class="cc-btn">3 Children</button>
								<button type="button" onclick="setChildCount(4)" id="cc-4" class="cc-btn">4+ Children</button>
							</div>
							<div id="family-discount-hint" style="display:none;align-items:center;gap:10px;margin-top:18px;padding:13px 18px;background:rgba(244,130,31,0.15);border-radius:10px;font-size:14px;font-weight:600;border:1px solid rgba(244,130,31,0.3);line-height:1.5;"></div>
						</div>

						<!-- Dynamic child sections (JS populates one group per child) -->
						<div id="child-sections-container"></div>

						<!-- ===== PARENT / GUARDIAN INFORMATION ===== -->
						<div class="reg-section-card" style="background:#f8f9ff;border-radius:15px;padding:30px;margin-bottom:30px;border-left:4px solid #002D45;">
							<h4 style="margin-bottom:25px;color:#1a1a2e;font-size:20px;"><i class="flaticon-team" style="color:#002D45;margin-right:10px;"></i> Parent / Guardian Information</h4>
							<div class="row clearfix">

								<div class="col-lg-6 col-md-6 col-sm-12 form-group">
									<label>Parent / Guardian Full Name <span style="color:#e74c3c;">*</span></label>
									<input type="text" name="parent_name" maxlength="255" required value="<?php echo old_val('parent_name'); ?>">
								</div>

								<div class="col-lg-6 col-md-6 col-sm-12 form-group">
									<label>Relationship to Student <span style="color:#e74c3c;">*</span></label>
									<select name="relationship" class="custom-select-box" required>
										<option value="">Select Relationship</option>
										<option value="Father" <?php echo old_sel('relationship','Father'); ?>>Father</option>
										<option value="Mother" <?php echo old_sel('relationship','Mother'); ?>>Mother</option>
										<option value="Guardian" <?php echo old_sel('relationship','Guardian'); ?>>Guardian</option>
										<option value="Other" <?php echo old_sel('relationship','Other'); ?>>Other</option>
									</select>
								</div>

								<div class="col-lg-6 col-md-6 col-sm-12 form-group">
									<label>Phone Number <span style="color:#e74c3c;">*</span></label>
									<input type="tel" name="phone" maxlength="50" placeholder="+234..." required value="<?php echo old_val('phone'); ?>">
								</div>

								<div class="col-lg-6 col-md-6 col-sm-12 form-group">
									<label>Alternative Phone Number</label>
									<input type="tel" name="alt_phone" maxlength="50" placeholder="+234..." value="<?php echo old_val('alt_phone'); ?>">
								</div>

								<div class="col-lg-12 col-md-12 col-sm-12 form-group">
									<label>Email Address <span style="color:#e74c3c;">*</span></label>
									<input type="email" name="email" maxlength="255" required value="<?php echo old_val('email'); ?>">
								</div>

								<div class="col-lg-12 col-md-12 col-sm-12 form-group">
									<label>Parent / Guardian Residential Address <span style="color:#e74c3c;">*</span></label>
									<textarea name="parent_address" rows="3" required><?php echo old_val('parent_address'); ?></textarea>
								</div>

							</div>
						</div>

						<!-- ===== PACKAGE SELECTION ===== -->
						<div class="reg-section-card" style="background:#f8f9ff;border-radius:15px;padding:30px;margin-bottom:30px;border-left:4px solid #9b59b6;">
							<h4 style="margin-bottom:6px;color:#1a1a2e;font-size:20px;"><i class="flaticon-trophy" style="color:#9b59b6;margin-right:10px;"></i> Package Selection</h4>
							<div id="pkg-section-label" style="font-size:13px;color:#888;font-weight:500;margin-bottom:22px;"></div>
							<div class="row clearfix">

								<div class="col-lg-4 col-md-4 col-sm-12 form-group<?php echo $early_bird_expired ? ' pkg-expired-wrap' : ''; ?>">
									<label style="cursor:<?php echo $early_bird_expired ? 'not-allowed' : 'pointer'; ?>;display:block;">
										<div style="border:2px solid #e0e0e0;border-radius:12px;padding:20px;text-align:center;transition:all 0.3s;" id="pkg-early-bird"<?php echo $early_bird_expired ? ' class="pkg-expired-card"' : ''; ?>>
											<?php if ($early_bird_expired): ?>
											<span class="pkg-expired-badge">Offer Expired</span><br>
											<?php endif; ?>
											<input type="radio" name="package" value="Early Bird"
												<?php echo ($selected_package == 'Early Bird') ? 'checked' : ''; ?>
												<?php echo $early_bird_expired ? 'disabled' : 'onchange="highlightPackage(\'early-bird\')"'; ?>
												style="margin-bottom:10px;">
											<div style="font-weight:700;font-size:16px;color:<?php echo $early_bird_expired ? '#aaa' : '#1a1a2e'; ?>;margin-bottom:5px;">Early Bird</div>
											<div id="pkg-price-early-bird" style="font-size:26px;font-weight:800;color:<?php echo $early_bird_expired ? '#bbb' : '#f4821f'; ?>;<?php echo $early_bird_expired ? 'text-decoration:line-through;' : ''; ?>">&#8358;45,000</div>
											<div id="pkg-desc-early-bird" style="font-size:13px;color:<?php echo $early_bird_expired ? '#bbb' : '#444'; ?>;font-weight:500;margin-top:6px;line-height:1.5;">Full Access + Materials + T-Shirt + Certificate</div>
										</div>
									</label>
								</div>

								<div class="col-lg-4 col-md-4 col-sm-12 form-group">
									<label style="cursor:pointer;display:block;">
										<div style="border:2px solid #e0e0e0;border-radius:12px;padding:20px;text-align:center;transition:all 0.3s;" id="pkg-standard">
											<input type="radio" name="package" value="Standard" <?php echo ($selected_package == 'Standard') ? 'checked' : ''; ?> onchange="highlightPackage('standard')" style="margin-bottom:10px;">
											<div style="font-weight:700;font-size:16px;color:#1a1a2e;margin-bottom:5px;">Standard</div>
											<div id="pkg-price-standard" style="font-size:26px;font-weight:800;color:#f4821f;">&#8358;55,000</div>
											<div id="pkg-desc-standard" style="font-size:13px;color:#444;font-weight:500;margin-top:6px;line-height:1.5;">Everything in Early Bird + Project Showcase</div>
										</div>
									</label>
								</div>

								<div class="col-lg-4 col-md-4 col-sm-12 form-group">
									<label style="cursor:pointer;display:block;">
										<div style="border:2px solid #e0e0e0;border-radius:12px;padding:20px;text-align:center;transition:all 0.3s;" id="pkg-premium">
											<input type="radio" name="package" value="Premium" <?php echo ($selected_package == 'Premium') ? 'checked' : ''; ?> onchange="highlightPackage('premium')" style="margin-bottom:10px;">
											<div style="font-weight:700;font-size:16px;color:#1a1a2e;margin-bottom:5px;">Premium</div>
											<div id="pkg-price-premium" style="font-size:26px;font-weight:800;color:#f4821f;">&#8358;70,000</div>
											<div id="pkg-desc-premium" style="font-size:13px;color:#444;font-weight:500;margin-top:6px;line-height:1.5;">Everything in Standard + Resources + Mentorship</div>
										</div>
									</label>
								</div>

							</div>
						</div>

						<!-- ===== CONSENT & AGREEMENT ===== -->
						<div class="reg-section-card" style="background:#fff9e6;border-radius:15px;padding:30px;margin-bottom:30px;border-left:4px solid #f39c12;">
							<h4 style="margin-bottom:20px;color:#1a1a2e;font-size:20px;"><i class="flaticon-checked" style="color:#f39c12;margin-right:10px;"></i> Consent &amp; Agreement</h4>
							<div style="font-size:15px;line-height:1.8;color:#333;">

								<div style="margin-bottom:12px;">
									<label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;">
										<input type="checkbox" name="consent_participate" value="1" required style="margin-top:3px;flex-shrink:0;">
										<span>I consent to my child's participation in the Ibadan Summer Innovation Camp 2026 (August 3–27, 2026) and all related activities. <span style="color:#e74c3c;">*</span></span>
									</label>
								</div>

								<div style="margin-bottom:12px;">
									<label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;">
										<input type="checkbox" name="consent_photo" value="1" style="margin-top:3px;flex-shrink:0;">
										<span>I consent to photographs and videos of my child being taken during the camp for promotional and documentation purposes.</span>
									</label>
								</div>

								<div style="margin-bottom:12px;">
									<label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;">
										<input type="checkbox" name="consent_medical" value="1" required style="margin-top:3px;flex-shrink:0;">
										<span>I confirm that the medical information provided is accurate and I authorize camp staff to seek emergency medical treatment if necessary. <span style="color:#e74c3c;">*</span></span>
									</label>
								</div>

								<div style="margin-bottom:12px;">
									<label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;">
										<input type="checkbox" name="consent_rules" value="1" required style="margin-top:3px;flex-shrink:0;">
										<span>I agree to the camp rules and regulations and confirm that all information provided in this form is accurate. <span style="color:#e74c3c;">*</span></span>
									</label>
								</div>

								<div style="margin-bottom:12px;">
									<label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;">
										<input type="checkbox" name="consent_payment" value="1" required style="margin-top:3px;flex-shrink:0;">
										<span>I understand that registration fees must be paid to confirm my child's place, and that fees are non-refundable after confirmation. <span style="color:#e74c3c;">*</span></span>
									</label>
								</div>

							</div>
						</div>

						<!-- Submit Button -->
						<div class="form-group" style="text-align:center;">
							<button type="submit" class="theme-btn btn-style-two" style="min-width:250px;">
								<span class="btn-wrap">
									<span class="text-one">Submit Registration <i class="flaticon-next-1"></i></span>
									<span class="text-two">Submit Registration <i class="flaticon-next-1"></i></span>
								</span>
							</button>
							<p style="font-size:15px;color:#444;margin-top:18px;line-height:1.65;">After submission, we will contact you within 24 hours with payment details to confirm your child's space.</p>
						</div>

					</form>
				</div>

			</div>
		</div>
	</section>
	<!-- End Registration Form -->

<style>
/* Track select — hide native arrow, overlay chevron */
.track-select-wrap { position: relative; display: block; }
.track-select-wrap .custom-select-box {
	-webkit-appearance: none !important;
	-moz-appearance: none !important;
	appearance: none !important;
	background-image: none !important;
	padding-right: 44px !important;
}
.track-chevron {
	position: absolute;
	right: 15px;
	top: 50%;
	transform: translateY(-50%);
	color: #f4821f;
	font-size: 13px;
	pointer-events: none;
	z-index: 1;
}

/* Custom courses dropdown */
.courses-dd { position: relative; }
.courses-dd-trigger {
	display: flex;
	align-items: center;
	justify-content: space-between;
	background: #ffffff;
	border: 1.5px solid #dde1ea;
	border-radius: 8px;
	padding: 0 18px;
	height: 52px;
	cursor: pointer;
	font-size: 15px;
	color: #b0b7c3;
	transition: border-color 0.25s ease, box-shadow 0.25s ease;
	user-select: none;
	-webkit-user-select: none;
}
.courses-dd-trigger.has-value { color: #1a1a2e; }
.courses-dd-trigger:hover { border-color: #f4821f; }
.courses-dd-trigger.open {
	border-color: #f4821f;
	box-shadow: 0 0 0 3px rgba(244,130,31,0.12);
}
.courses-dd-trigger.disabled { cursor: not-allowed; background: #f5f5f5; }
.dd-chevron { color: #f4821f; font-size: 13px; flex-shrink: 0; transition: transform 0.25s ease; }
.courses-dd-trigger.open .dd-chevron { transform: rotate(180deg); }

.courses-dd-menu {
	display: none;
	position: absolute;
	top: calc(100% + 5px);
	left: 0; right: 0;
	background: #ffffff;
	border: 1.5px solid #e0e4ee;
	border-radius: 10px;
	padding: 6px 0;
	z-index: 300;
	box-shadow: 0 10px 30px rgba(0,0,0,0.11);
	max-height: 240px;
	overflow-y: auto;
}
.courses-dd-menu.open { display: block; }
.courses-dd-menu label {
	display: flex !important;
	align-items: center !important;
	gap: 11px !important;
	padding: 10px 18px !important;
	margin: 0 !important;
	cursor: pointer !important;
	font-size: 14px !important;
	color: #1a1a2e !important;
	font-weight: 500 !important;
	opacity: 1 !important;
	transition: background 0.15s !important;
	line-height: 1.4 !important;
}
.courses-dd-menu label:hover { background: #fff5ed !important; }
.courses-dd-menu input[type="checkbox"] {
	width: 16px; height: 16px;
	flex-shrink: 0;
	accent-color: #f4821f;
	cursor: pointer;
	margin: 0 !important;
}

.cc-btn {
	padding: 12px 24px;
	border-radius: 50px;
	font-size: 14px;
	font-weight: 700;
	border: 2px solid rgba(255,255,255,0.22);
	background: rgba(255,255,255,0.10);
	color: #fff;
	cursor: pointer;
	transition: all 0.25s ease;
	letter-spacing: 0.3px;
	outline: none;
}
.cc-btn:hover {
	background: rgba(244,130,31,0.28);
	border-color: rgba(244,130,31,0.55);
}
.cc-btn.active {
	background: #f4821f;
	border-color: #f4821f;
	color: #fff;
	box-shadow: 0 4px 16px rgba(244,130,31,0.45);
}
@keyframes childSlideIn {
	from { opacity: 0; transform: translateY(24px); }
	to   { opacity: 1; transform: translateY(0); }
}

/* Expired package card */
.pkg-expired-wrap { cursor: not-allowed !important; }
.pkg-expired-wrap label { pointer-events: none; }
.pkg-expired-card {
	opacity: 0.62;
	border-color: #ccc !important;
	background: #f5f5f5 !important;
	position: relative;
	overflow: hidden;
}
.pkg-expired-card::after {
	content: '';
	position: absolute;
	inset: 0;
	border-radius: 10px;
	background: repeating-linear-gradient(
		-45deg,
		rgba(0,0,0,0.055) 0px,
		rgba(0,0,0,0.055) 5px,
		transparent 5px,
		transparent 13px
	);
	pointer-events: none;
}
.pkg-expired-badge {
	display: inline-block;
	background: #e74c3c;
	color: #fff;
	font-size: 10.5px;
	font-weight: 700;
	padding: 3px 11px;
	border-radius: 20px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 8px;
}

/* ── Registration page responsive ── */
@media (max-width: 767px) {
	/* Courses dropdown: prevent overflow beyond viewport */
	.courses-dd-menu {
		max-height: 200px;
		left: 0;
		right: 0;
		min-width: 0;
	}
	/* Package cards: equal gap on single-column stack */
	#pkg-early-bird,
	#pkg-standard,
	#pkg-premium { margin-bottom: 12px; }
	/* Form field font: match reduced size */
	.register-form .form-group input[type="text"],
	.register-form .form-group input[type="email"],
	.register-form .form-group input[type="tel"],
	.register-form .form-group input[type="date"],
	.register-form .form-group select,
	.register-form .form-group textarea { font-size: 14px; height: 48px; }
	/* Step counter text inside children card */
	.reg-children-card [style*="font-size:19px"],
	.reg-children-card [style*="font-size: 19px"] { font-size: 15px !important; }
}

@media (max-width: 480px) {
	/* Very small screens: tighter track select */
	.track-select-wrap .custom-select-box { font-size: 13px; }
	.courses-dd-trigger { font-size: 13px; height: 46px; }
	/* Form fields: compact height */
	.register-form .form-group input[type="text"],
	.register-form .form-group input[type="email"],
	.register-form .form-group input[type="tel"],
	.register-form .form-group input[type="date"],
	.register-form .form-group select,
	.register-form .form-group textarea { font-size: 13px; height: 46px; padding: 8px 14px; }
	/* Consent checkboxes: tighter */
	.reg-section-card label { gap: 10px !important; }
}
</style>

<script>
/* Package pricing */
var earlyBirdExpired = <?php echo $early_bird_expired ? 'true' : 'false'; ?>;
const PKG_BASE = { 'early-bird': 45000, 'standard': 55000, 'premium': 70000 };
const PKG_BASE_DESC = {
	'early-bird': 'Full Access + Materials + T-Shirt + Certificate',
	'standard':   'Everything in Early Bird + Project Showcase',
	'premium':    'Everything in Standard + Resources + Mentorship'
};

function naira(n) {
	return '&#8358;' + n.toLocaleString('en-NG');
}

function updatePackagePrices(n) {
	// Exclude early-bird from dynamic pricing when it has expired
	var pkgs = earlyBirdExpired ? ['standard', 'premium'] : ['early-bird', 'standard', 'premium'];
	var labelEl = document.getElementById('pkg-section-label');

	pkgs.forEach(function(pkg) {
		var base     = PKG_BASE[pkg];
		var priceEl  = document.getElementById('pkg-price-' + pkg);
		var descEl   = document.getElementById('pkg-desc-'  + pkg);
		if (!priceEl || !descEl) return;

		if (n === 1) {
			priceEl.innerHTML = naira(base);
			descEl.innerHTML  = PKG_BASE_DESC[pkg];
		} else if (n === 2) {
			var child2 = Math.round(base * 0.90);
			var total  = base + child2;
			priceEl.innerHTML = naira(total) + '<span style="font-size:14px;font-weight:600;"> total</span>';
			descEl.innerHTML  = naira(base) + ' + ' + naira(child2) +
				' <span style="display:inline-block;background:#e8f8f0;color:#27ae60;font-size:11px;font-weight:700;padding:1px 7px;border-radius:20px;margin-top:4px;">10% off 2nd child</span>';
		} else if (n === 3) {
			var c2    = Math.round(base * 0.90);
			var c3    = Math.round(base * 0.85);
			var tot3  = base + c2 + c3;
			priceEl.innerHTML = naira(tot3) + '<span style="font-size:14px;font-weight:600;"> total</span>';
			descEl.innerHTML  = naira(base) + ' + ' + naira(c2) + ' + ' + naira(c3) +
				' <span style="display:inline-block;background:#e8f8f0;color:#27ae60;font-size:11px;font-weight:700;padding:1px 7px;border-radius:20px;margin-top:4px;">10% & 15% off</span>';
		} else {
			priceEl.innerHTML = '<span style="font-size:18px;font-weight:700;">Group Rate</span>';
			descEl.innerHTML  = naira(base) + '/child &nbsp;&middot;&nbsp; Final price via contact<br>' +
				'<a href="mailto:hello@traceworka.ng" style="font-size:12px;color:#f4821f;font-weight:600;">hello@traceworka.ng</a>';
		}
	});

	if (labelEl) {
		if (n === 1) {
			labelEl.textContent = '';
		} else if (n <= 3) {
			labelEl.textContent = 'Total price shown below covers all ' + n + ' children with family discount applied.';
		} else {
			labelEl.textContent = 'Select a package type — our team will confirm group pricing within 24 hours.';
		}
	}
}

/* Courses keyed by track → age tier */
const coursesDataByAge = {
	'Technology': {
		junior: ['Coding & Programming','Robotics','Graphic Design','Video Editing'],
		mid:    ['Coding & Programming','Robotics','Web Design','Graphic Design','UI/UX Design','Video Editing'],
		senior: ['Coding & Programming','Robotics','Web Design','Graphic Design','UI/UX Design','Video Editing']
	},
	'Entrepreneurship': {
		junior: ['Startup Fundamentals','Branding','Sales Skills'],
		mid:    ['Branding','Digital Marketing','Sales Skills','Startup Fundamentals','Business Pitching'],
		senior: ['Branding','Digital Marketing','Sales Skills','Startup Fundamentals','Business Pitching','Financial Planning']
	},
	'Vocational Skills': {
		junior: ['Fashion Design','Baking & Pastry','Bead Making','DIY Crafts'],
		mid:    ['Fashion Design','Baking & Pastry','Bead Making','Hair Styling','Soap Production','DIY Crafts'],
		senior: ['Fashion Design','Baking & Pastry','Bead Making','Hair Styling','Soap Production','DIY Crafts']
	}
};

const TIER_LABELS = {
	junior: 'Foundation · Ages 7–10',
	mid:    'Intermediate · Ages 11–14',
	senior: 'Advanced · Ages 15–18'
};

function getAgeTier(age) {
	if (age >= 7  && age <= 10) return 'junior';
	if (age >= 11 && age <= 14) return 'mid';
	return 'senior';
}

let currentChildCount = 1;

function childSectionHTML(i, showDivider) {
	const num = i + 1;
	const ordinals = ['First','Second','Third','Fourth'];
	const ordinal  = ordinals[i] || ('Child ' + num);

	let ageOpts = '<option value="">Select Age</option>';
	for (let a = 7; a <= 18; a++) {
		ageOpts += '<option value="' + a + '">' + a + ' years</option>';
	}

	const divider = showDivider
		? '<div style="border:none;border-top:2px dashed #dde1ea;margin:10px 0 40px;"></div>'
		: '<div style="margin-bottom:10px;"></div>';

	const delay = (i * 0.08).toFixed(2);

	return (
		'<div class="child-group" id="child-group-' + i + '" style="animation:childSlideIn 0.45s ease ' + delay + 's both;">' +

		/* ---- child header ---- */
		'<div class="reg-child-header" style="display:flex;align-items:center;gap:18px;margin-bottom:26px;padding:20px 26px;background:linear-gradient(135deg,#002D45,#01415b);border-radius:14px;color:#fff;">' +
			'<div style="width:52px;height:52px;border-radius:50%;background:#f4821f;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;flex-shrink:0;box-shadow:0 4px 14px rgba(244,130,31,0.42);">' + num + '</div>' +
			'<div>' +
				'<div style="font-size:11px;opacity:0.6;text-transform:uppercase;letter-spacing:1.3px;margin-bottom:3px;">' + ordinal + ' Child</div>' +
				'<div style="font-size:18px;font-weight:700;">Student Registration</div>' +
			'</div>' +
		'</div>' +

		/* ---- student info ---- */
		'<div class="reg-section-card" style="background:#f8f9ff;border-radius:15px;padding:30px;margin-bottom:20px;border-left:4px solid #f4821f;">' +
			'<h4 style="margin-bottom:25px;color:#1a1a2e;font-size:18px;font-weight:700;"><i class="flaticon-user" style="color:#f4821f;margin-right:10px;"></i> Student Information</h4>' +
			'<div class="row clearfix">' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>First Name <span style="color:#e74c3c;">*</span></label><input type="text" name="first_name[' + i + ']" maxlength="100" required></div>' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>Last Name <span style="color:#e74c3c;">*</span></label><input type="text" name="last_name[' + i + ']" maxlength="100" required></div>' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>Other Name</label><input type="text" name="other_name[' + i + ']" maxlength="100"></div>' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>Gender <span style="color:#e74c3c;">*</span></label>' +
					'<select name="gender[' + i + ']" class="custom-select-box" required>' +
						'<option value="">Select Gender</option><option value="Male">Male</option><option value="Female">Female</option>' +
					'</select></div>' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>Date of Birth <span style="color:#e74c3c;">*</span></label><input type="date" name="date_of_birth[' + i + ']" min="2008-01-01" max="2019-12-31" required onchange="autoFillAge(' + i + ')"></div>' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>Age <span style="color:#e74c3c;">*</span></label>' +
					'<select name="age[' + i + ']" id="age_select_' + i + '" class="custom-select-box" required onchange="updateCourses(' + i + ')">' + ageOpts + '</select></div>' +
				'<div class="col-lg-6 col-md-6 col-sm-12 form-group"><label>Current School <span style="color:#e74c3c;">*</span></label><input type="text" name="school[' + i + ']" maxlength="255" required></div>' +
				'<div class="col-lg-6 col-md-6 col-sm-12 form-group"><label>Current Class / Grade <span style="color:#e74c3c;">*</span></label><input type="text" name="class_grade[' + i + ']" maxlength="100" placeholder="e.g. JSS 2, Primary 5" required></div>' +
				'<div class="col-lg-12 col-md-12 col-sm-12 form-group"><label>Student\'s Home Address <span style="color:#e74c3c;">*</span></label><textarea name="address[' + i + ']" rows="3" required></textarea></div>' +
			'</div>' +
		'</div>' +

		/* ---- camp participation ---- */
		'<div class="reg-section-card" style="background:#f8f9ff;border-radius:15px;padding:30px;margin-bottom:20px;border-left:4px solid #f4821f;">' +
			'<h4 style="margin-bottom:25px;color:#1a1a2e;font-size:18px;font-weight:700;"><i class="flaticon-leader" style="color:#f4821f;margin-right:10px;"></i> Camp Participation Details</h4>' +
			'<div class="row clearfix">' +
				'<div class="col-lg-6 col-md-12 col-sm-12 form-group"><label>Primary Learning Track <span style="color:#e74c3c;">*</span></label>' +
					'<div class="track-select-wrap">' +
						'<select name="learning_track[' + i + ']" id="learning_track_' + i + '" class="custom-select-box" required onchange="updateCourses(' + i + ')">' +
							'<option value="">Select Learning Track</option>' +
							'<option value="Technology">Technology Track</option>' +
							'<option value="Entrepreneurship">Entrepreneurship Track</option>' +
							'<option value="Vocational Skills">Vocational Skills Track</option>' +
						'</select>' +
						'<i class="fa-solid fa-chevron-down track-chevron"></i>' +
					'</div></div>' +
				'<div class="col-lg-6 col-md-12 col-sm-12 form-group">' +
					'<label>Select Course(s) <span style="color:#e74c3c;">*</span> <span style="font-size:12px;color:#888;font-weight:500;">(Select all that apply)</span></label>' +
					'<div class="courses-dd" id="courses-dd_' + i + '">' +
						'<div class="courses-dd-trigger disabled" id="courses-trigger_' + i + '" onclick="toggleCoursesDropdown(' + i + ')">' +
							'<span id="courses-label_' + i + '">Select a Learning Track first</span>' +
							'<i class="fa-solid fa-chevron-down dd-chevron"></i>' +
						'</div>' +
						'<div class="courses-dd-menu" id="courses-menu_' + i + '"></div>' +
					'</div>' +
					'<input type="hidden" name="courses[' + i + ']" id="courses_hidden_' + i + '">' +
				'</div>' +
			'</div>' +
		'</div>' +

		/* ---- medical info ---- */
		'<div class="reg-section-card" style="background:#f8f9ff;border-radius:15px;padding:30px;margin-bottom:20px;border-left:4px solid #e74c3c;">' +
			'<h4 style="margin-bottom:25px;color:#1a1a2e;font-size:18px;font-weight:700;"><i class="flaticon-happiness" style="color:#e74c3c;margin-right:10px;"></i> Medical Information</h4>' +
			'<div class="row clearfix">' +
				'<div class="col-lg-6 col-md-12 col-sm-12 form-group"><label>Medical Condition (if any)</label><textarea name="medical_condition[' + i + ']" rows="3" placeholder="Describe any known medical conditions, or write \'None\'"></textarea></div>' +
				'<div class="col-lg-6 col-md-12 col-sm-12 form-group"><label>Allergies (if any)</label><textarea name="allergies[' + i + ']" rows="3" placeholder="List any allergies (food, medication, environmental), or write \'None\'"></textarea></div>' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>Emergency Contact Name <span style="color:#e74c3c;">*</span></label><input type="text" name="emergency_contact[' + i + ']" maxlength="255" required></div>' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>Emergency Contact Phone <span style="color:#e74c3c;">*</span></label><input type="tel" name="emergency_phone[' + i + ']" maxlength="50" placeholder="+234..." required></div>' +
				'<div class="col-lg-4 col-md-6 col-sm-12 form-group"><label>Relationship to Student <span style="color:#e74c3c;">*</span></label>' +
					'<select name="emergency_relationship[' + i + ']" class="custom-select-box" required>' +
						'<option value="">Select Relationship</option>' +
						'<option value="Father">Father</option>' +
						'<option value="Mother">Mother</option>' +
						'<option value="Sibling">Sibling</option>' +
						'<option value="Relative">Relative</option>' +
						'<option value="Family Friend">Family Friend</option>' +
						'<option value="Other">Other</option>' +
					'</select></div>' +
			'</div>' +
		'</div>' +

		divider +
		'</div>'
	);
}

function setChildCount(n) {
	currentChildCount = n;
	document.getElementById('number_of_children').value = n;

	/* update button states */
	for (var i = 1; i <= 4; i++) {
		var btn = document.getElementById('cc-' + i);
		if (btn) btn.className = 'cc-btn' + (i === n ? ' active' : '');
	}

	/* family discount hint */
	var hint = document.getElementById('family-discount-hint');
	if (hint) {
		if (n === 1) {
			hint.style.display = 'none';
		} else {
			hint.style.display = 'flex';
			var msgs = {
				2: '<i class="fa-solid fa-tags" style="color:#f4821f;font-size:16px;flex-shrink:0;"></i><span><strong>Family Discount applies!</strong> Get 10% off your second child\'s registration fee.</span>',
				3: '<i class="fa-solid fa-tags" style="color:#f4821f;font-size:16px;flex-shrink:0;"></i><span><strong>Family Discount applies!</strong> 10% off 2nd child &amp; 15% off 3rd child.</span>',
				4: '<i class="fa-solid fa-tags" style="color:#f4821f;font-size:16px;flex-shrink:0;"></i><span><strong>Group Discount available!</strong> Contact us at <strong>hello@traceworka.ng</strong> for special group rates.</span>'
			};
			hint.innerHTML = msgs[n] || msgs[4];
		}
	}

	/* update package prices */
	updatePackagePrices(n);

	/* render child sections */
	var container = document.getElementById('child-sections-container');
	var html = '';
	for (var j = 0; j < n; j++) {
		html += childSectionHTML(j, j < n - 1);
	}
	container.innerHTML = html;

	/* clear any previous validation banner */
	var prev = document.getElementById('children-error-banner');
	if (prev) prev.remove();
}

function updateCourses(i) {
	var trackEl   = document.getElementById('learning_track_' + i);
	var triggerEl = document.getElementById('courses-trigger_' + i);
	var menuEl    = document.getElementById('courses-menu_' + i);
	var labelEl   = document.getElementById('courses-label_' + i);
	var hiddenEl  = document.getElementById('courses_hidden_' + i);
	if (!trackEl || !triggerEl || !menuEl) return;

	/* close and reset */
	menuEl.classList.remove('open');
	triggerEl.classList.remove('open', 'has-value');
	if (hiddenEl) hiddenEl.value = '';

	var track = trackEl.value;

	if (!track || !coursesDataByAge[track]) {
		menuEl.innerHTML = '';
		triggerEl.classList.add('disabled');
		if (labelEl) labelEl.textContent = 'Select a Learning Track first';
		return;
	}

	/* resolve age → tier */
	var ageEl   = document.getElementById('age_select_' + i);
	var age     = ageEl ? parseInt(ageEl.value) : 0;
	var hasAge  = age >= 7 && age <= 18;
	var tier    = hasAge ? getAgeTier(age) : 'senior';
	var courses = coursesDataByAge[track][tier];

	triggerEl.classList.remove('disabled');
	if (labelEl) labelEl.textContent = 'Select courses…';

	/* tier label header inside the dropdown */
	var tierHtml = hasAge
		? '<div style="padding:7px 18px 8px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.9px;color:#f4821f;background:#fff5ed;border-bottom:1px solid #ffe4cc;pointer-events:none;">' + TIER_LABELS[tier] + '</div>'
		: '';

	var html = tierHtml;
	courses.forEach(function(c) {
		html += '<label><input type="checkbox" value="' + c + '" onchange="syncCourses(' + i + ')">' + c + '</label>';
	});
	menuEl.innerHTML = html;
}

function autoFillAge(i) {
	var dobEl = document.querySelector('[name="date_of_birth[' + i + ']"]');
	var ageEl = document.getElementById('age_select_' + i);
	if (!dobEl || !ageEl || !dobEl.value) return;

	/* calculate completed years */
	var dob   = new Date(dobEl.value + 'T00:00:00');
	var today = new Date();
	var age   = today.getFullYear() - dob.getFullYear();
	var dm    = today.getMonth() - dob.getMonth();
	if (dm < 0 || (dm === 0 && today.getDate() < dob.getDate())) age--;

	if (age >= 7 && age <= 18) {
		ageEl.value = age;
		/* flash the field green briefly to confirm auto-fill */
		ageEl.style.transition = 'border-color 0.2s';
		ageEl.style.borderColor = '#2ecc71';
		setTimeout(function() { ageEl.style.borderColor = ''; }, 1200);
		/* reload courses for the new age tier */
		updateCourses(i);
	} else {
		ageEl.value = '';
	}
}

function toggleCoursesDropdown(i) {
	var triggerEl = document.getElementById('courses-trigger_' + i);
	var menuEl    = document.getElementById('courses-menu_'   + i);
	if (!triggerEl || !menuEl || triggerEl.classList.contains('disabled')) return;

	var isOpen = menuEl.classList.contains('open');

	/* close all open dropdowns first */
	document.querySelectorAll('.courses-dd-menu.open').forEach(function(m) { m.classList.remove('open'); });
	document.querySelectorAll('.courses-dd-trigger.open').forEach(function(t) { t.classList.remove('open'); });

	if (!isOpen) {
		menuEl.classList.add('open');
		triggerEl.classList.add('open');
	}
}

function syncCourses(i) {
	var menuEl    = document.getElementById('courses-menu_'    + i);
	var labelEl   = document.getElementById('courses-label_'   + i);
	var hiddenEl  = document.getElementById('courses_hidden_'  + i);
	var triggerEl = document.getElementById('courses-trigger_' + i);
	if (!menuEl || !hiddenEl) return;

	var checks = menuEl.querySelectorAll('input[type="checkbox"]:checked');
	var vals   = Array.prototype.map.call(checks, function(c) { return c.value; });
	hiddenEl.value = vals.join(',');

	if (labelEl) {
		if (vals.length === 0) {
			labelEl.textContent = 'Select courses…';
			if (triggerEl) triggerEl.classList.remove('has-value');
		} else if (vals.length === 1) {
			labelEl.textContent = vals[0];
			if (triggerEl) triggerEl.classList.add('has-value');
		} else {
			labelEl.textContent = vals.length + ' courses selected';
			if (triggerEl) triggerEl.classList.add('has-value');
		}
	}
}

function highlightPackage(pkg) {
	if (earlyBirdExpired && pkg === 'early-bird') return;
	['early-bird','standard','premium'].forEach(function(p) {
		if (earlyBirdExpired && p === 'early-bird') return; // leave expired card as-is
		document.getElementById('pkg-' + p).style.borderColor = '#e0e0e0';
		document.getElementById('pkg-' + p).style.background  = '#fff';
	});
	document.getElementById('pkg-' + pkg).style.borderColor = '#f4821f';
	document.getElementById('pkg-' + pkg).style.background  = '#fff8f0';
}

<?php if ($reg_old): ?>
var REG_OLD = <?php
    $old_children = [];
    $fn_arr = (array)($reg_old['first_name'] ?? []);
    for ($ci = 0; $ci < count($fn_arr); $ci++) {
        $ga = function(string $k) use ($reg_old, $ci) {
            $v = $reg_old[$k] ?? [];
            return is_array($v) ? ($v[$ci] ?? '') : (string)$v;
        };
        $old_children[] = [
            'first_name'             => $ga('first_name'),
            'last_name'              => $ga('last_name'),
            'other_name'             => $ga('other_name'),
            'gender'                 => $ga('gender'),
            'date_of_birth'          => $ga('date_of_birth'),
            'age'                    => $ga('age'),
            'school'                 => $ga('school'),
            'class_grade'            => $ga('class_grade'),
            'address'                => $ga('address'),
            'learning_track'         => $ga('learning_track'),
            'courses'                => $ga('courses'),
            'medical_condition'      => $ga('medical_condition'),
            'allergies'              => $ga('allergies'),
            'emergency_contact'      => $ga('emergency_contact'),
            'emergency_phone'        => $ga('emergency_phone'),
            'emergency_relationship' => $ga('emergency_relationship'),
        ];
    }
    echo json_encode(['num_children' => count($fn_arr), 'children' => $old_children]);
?>;

function repopulateFromOld(oldData) {
    if (!oldData || !oldData.children || !oldData.children.length) return;
    setChildCount(oldData.num_children);
    setTimeout(function() {
        oldData.children.forEach(function(c, i) {
            function sf(name, val) {
                var el = document.querySelector('[name="' + name + '[' + i + ']"]');
                if (el && val) el.value = val;
            }
            sf('first_name',             c.first_name);
            sf('last_name',              c.last_name);
            sf('other_name',             c.other_name);
            sf('date_of_birth',          c.date_of_birth);
            sf('school',                 c.school);
            sf('class_grade',            c.class_grade);
            sf('address',                c.address);
            sf('medical_condition',      c.medical_condition);
            sf('allergies',              c.allergies);
            sf('emergency_contact',      c.emergency_contact);
            sf('emergency_phone',        c.emergency_phone);
            sf('gender',                 c.gender);
            sf('age',                    c.age);
            sf('emergency_relationship', c.emergency_relationship);

            if (c.learning_track) {
                var trackEl = document.getElementById('learning_track_' + i);
                if (trackEl) {
                    trackEl.value = c.learning_track;
                    if (c.age) updateCourses(i);
                    if (c.courses) {
                        setTimeout(function(idx, csv) {
                            var saved = csv.split(',').map(function(s){ return s.trim(); });
                            var menu  = document.getElementById('courses-menu_' + idx);
                            if (menu) {
                                menu.querySelectorAll('input[type="checkbox"]').forEach(function(cb) {
                                    if (saved.indexOf(cb.value) !== -1) cb.checked = true;
                                });
                                syncCourses(idx);
                            }
                        }, 80, i, c.courses);
                    }
                }
            }
        });
    }, 60);
}
<?php endif; ?>

document.addEventListener('DOMContentLoaded', function() {
	/* Auto-highlight pre-selected package */
	var checked = document.querySelector('input[name="package"]:checked');
	if (checked) {
		var map = {'Early Bird':'early-bird','Standard':'standard','Premium':'premium'};
		highlightPackage(map[checked.value] || 'early-bird');
	}

	/* Close courses dropdowns when clicking outside */
	document.addEventListener('click', function(e) {
		if (!e.target.closest('.courses-dd')) {
			document.querySelectorAll('.courses-dd-menu.open').forEach(function(m) { m.classList.remove('open'); });
			document.querySelectorAll('.courses-dd-trigger.open').forEach(function(t) { t.classList.remove('open'); });
		}
	});

	/* Render initial single-child form — or restore from saved data */
	<?php if ($reg_old): ?>
	repopulateFromOld(REG_OLD);
	<?php else: ?>
	setChildCount(1);
	<?php endif; ?>

	/* Pre-submit child validation — runs in capture phase before form-validation.js */
	document.getElementById('registration-form').addEventListener('submit', function(e) {
		var n    = currentChildCount;
		var errs = [];

		for (var i = 0; i < n; i++) {
			var childLabel = n === 1 ? 'Student' : 'Child ' + (i + 1);

			/* required text / date inputs */
			var textFields = [
				['first_name['   + i + ']', 'First Name'],
				['last_name['    + i + ']', 'Last Name'],
				['date_of_birth['+ i + ']', 'Date of Birth'],
				['school['       + i + ']', 'School'],
				['class_grade['  + i + ']', 'Class / Grade'],
				['address['      + i + ']', 'Home Address'],
				['emergency_contact[' + i + ']', 'Emergency Contact Name'],
				['emergency_phone['   + i + ']', 'Emergency Contact Phone'],
			];
			textFields.forEach(function(pair) {
				var el = document.querySelector('[name="' + pair[0] + '"]');
				if (el && !el.value.trim()) {
					errs.push(childLabel + ': ' + pair[1] + ' is required.');
					el.style.borderColor = '#e74c3c';
					el.style.boxShadow   = '0 0 0 3px rgba(231,76,60,0.10)';
				}
			});

			/* required selects */
			var selectFields = [
				['gender['                 + i + ']', 'Gender'],
				['age['                    + i + ']', 'Age'],
				['learning_track['         + i + ']', 'Learning Track'],
				['emergency_relationship[' + i + ']', 'Emergency Contact Relationship'],
			];
			selectFields.forEach(function(pair) {
				var el = document.querySelector('[name="' + pair[0] + '"]');
				if (el && !el.value) {
					errs.push(childLabel + ': ' + pair[1] + ' is required.');
					el.style.borderColor = '#e74c3c';
				}
			});

			/* courses */
			var coursesEl = document.getElementById('courses_hidden_' + i);
			if (coursesEl && !coursesEl.value) {
				errs.push(childLabel + ': Please select at least one course.');
				var trigEl = document.getElementById('courses-trigger_' + i);
				if (trigEl) { trigEl.style.borderColor = '#e74c3c'; trigEl.style.boxShadow = '0 0 0 3px rgba(231,76,60,0.10)'; }
			}
		}

		if (errs.length > 0) {
			e.preventDefault();
			e.stopImmediatePropagation();

			var banner = document.getElementById('children-error-banner');
			if (!banner) {
				banner = document.createElement('div');
				banner.id        = 'children-error-banner';
				banner.className = 'v-banner';
				var container = document.getElementById('child-sections-container');
				container.parentNode.insertBefore(banner, container);
			}
			var shown = errs.slice(0, 6).map(function(msg){ return '<li>' + msg + '</li>'; }).join('');
			var extra = errs.length > 6 ? '<li>…and ' + (errs.length - 6) + ' more field(s) to complete.</li>' : '';
			banner.innerHTML = '<strong>Please complete the following before submitting:</strong><ul style="margin:8px 0 0;padding-left:20px;">' + shown + extra + '</ul>';
			banner.scrollIntoView({behavior:'smooth', block:'center'});
		}
	}, true);
});
</script>

<?php include('includes/footer.php'); ?>
