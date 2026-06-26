<?php
session_start();
$page_title = 'Contact Us – Ibadan Summer Innovation Camp 2026';
$meta_description = 'Get in touch with the Ibadan Summer Innovation Camp 2026 team. Located at Traceworka Innovative Solutions, Kongi-Bodija, Ibadan. Call +234 907 154 3344.';

// Flash messages
$success_msg = '';
$error_msg   = '';
if (!empty($_SESSION['contact_success'])) {
    $success_msg = $_SESSION['contact_success'];
    unset($_SESSION['contact_success']);
}
if (!empty($_SESSION['contact_error'])) {
    $error_msg = $_SESSION['contact_error'];
    unset($_SESSION['contact_error']);
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
			<h2>Contact Us</h2>
			<ul class="bread-crumb clearfix">
				<li><a href="index.php"><i class="flaticon-home"></i> Home</a></li>
				<li>Contact Us</li>
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

	<!-- Contact Info Cards -->
	<section class="contact-info">
		<div class="auto-container">
			<div class="row clearfix">

				<div class="info-block_two col-lg-3 col-md-6 col-sm-12">
					<div class="info-block_two-inner">
						<div class="info-block_two-icon"><i class="flaticon-phone-call"></i></div>
						<h4>Phone Numbers</h4>
						<a href="tel:+2349071543344">+234 907 154 3344</a><br>
						<a href="tel:+2348135235891">+234 813 523 5891</a>
					</div>
				</div>

				<div class="info-block_two col-lg-3 col-md-6 col-sm-12">
					<div class="info-block_two-inner">
						<div class="info-block_two-icon"><i class="flaticon-pin"></i></div>
						<h4>Our Location</h4>
						<div class="text">No 6, Hon Tunde Sarumi Close,<br> Off Adenuga Street, Kongi-Bodija,<br> Ibadan, Oyo State</div>
					</div>
				</div>

				<div class="info-block_two col-lg-3 col-md-6 col-sm-12">
					<div class="info-block_two-inner">
						<div class="info-block_two-icon"><i class="flaticon-mail"></i></div>
						<h4>Email Address</h4>
						<a href="mailto:hello@traceworka.ng">hello@traceworka.ng</a>
					</div>
				</div>

				<div class="info-block_two col-lg-3 col-md-6 col-sm-12">
					<div class="info-block_two-inner">
						<div class="info-block_two-icon"><i class="flaticon-calendar"></i></div>
						<h4>Camp Schedule</h4>
						<div class="text">August 3–27, 2026<br>Monday – Thursday<br>9:00 AM – 3:00 PM</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End Contact Info Cards -->

	<!-- Contact Form + Map -->
	<section class="contact-form-section">
		<div class="auto-container">
			<div class="row clearfix">

				<!-- Contact Form Column -->
				<div class="form-column col-lg-6 col-md-12 col-sm-12">
					<div class="inner-column">
						<h3>Send Us a Message</h3>
						<div class="text">We'll respond within 24 hours. Fields marked <span style="color:#e74c3c;">*</span> are required.</div>

						<?php if ($success_msg): ?>
						<div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:15px;border-radius:8px;margin:15px 0;font-weight:600;">
							<i class="fa-solid fa-circle-check" style="color:#f4821f;margin-right:8px;"></i>
							<?php echo htmlspecialchars($success_msg); ?>
						</div>
						<?php endif; ?>

						<?php if ($error_msg): ?>
						<div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:15px;border-radius:8px;margin:15px 0;">
							<i class="fa-solid fa-circle-xmark" style="color:#e74c3c;margin-right:8px;"></i>
							<?php echo htmlspecialchars($error_msg); ?>
						</div>
						<?php endif; ?>

						<div class="default-form contact-form">
							<form method="post" action="forms/contact-process.php" id="contact-form" novalidate>
								<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
								<input type="text" name="website" tabindex="-1" autocomplete="off" style="display:none!important;position:absolute;left:-9999px;" aria-hidden="true" value="">
								<div class="row clearfix">
									<div class="form-group col-lg-6 col-md-6 col-sm-6">
										<input type="text" name="name" placeholder="Your Full Name *" maxlength="255" required>
									</div>
									<div class="form-group col-lg-6 col-md-6 col-sm-6">
										<input type="email" name="email" placeholder="Email Address *" maxlength="255" required>
									</div>
									<div class="form-group col-lg-6 col-md-6 col-sm-6">
										<input type="tel" name="phone" placeholder="Phone Number *" maxlength="50" required>
									</div>
									<div class="form-group col-lg-6 col-md-6 col-sm-6">
										<select name="subject" class="custom-select-box">
											<option value="">Select Subject</option>
											<option value="Registration Enquiry">Registration Enquiry</option>
											<option value="Pricing & Packages">Pricing &amp; Packages</option>
											<option value="School Group Rates">School Group Rates</option>
											<option value="Learning Tracks">Learning Tracks</option>
											<option value="Camp Schedule">Camp Schedule</option>
											<option value="General Enquiry">General Enquiry</option>
										</select>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 form-group">
										<textarea name="message" placeholder="Your message..." rows="5" maxlength="2000"></textarea>
									</div>
									<div class="form-group col-lg-12 col-md-12 col-sm-12">
										<button type="submit" class="template-btn btn-style-one">
											<span class="btn-wrap">
												<span class="text-one">Send Message <i class="flaticon-next-1"></i></span>
												<span class="text-two">Send Message <i class="flaticon-next-1"></i></span>
											</span>
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

				<!-- Map Column -->
				<div class="map-column col-lg-6 col-md-12 col-sm-12">
					<div class="inner-column">
						<div class="map-one_map">
							<iframe
								id="gmap_canvas"
								src="https://maps.google.com/maps?q=Kongi-Bodija+Ibadan+Oyo+State+Nigeria&t=&z=15&ie=UTF8&iwloc=&output=embed"
								width="100%" height="450" style="border:0;border-radius:12px;" allowfullscreen="" loading="lazy"
								referrerpolicy="no-referrer-when-downgrade" title="Ibadan Summer Innovation Camp Location">
							</iframe>
						</div>
						<!-- Organisation Details -->
						<div style="background:#f8f9ff;border-radius:16px;padding:30px;margin-top:25px;border-left:4px solid var(--main-color);">
							<h5 style="font-size:17px;font-weight:700;color:#1a1a2e;margin-bottom:18px;letter-spacing:0.2px;">
								<i class="fa-solid fa-building" style="color:var(--main-color);margin-right:8px;"></i>Organisation Details
							</h5>
							<div style="font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:14px;">
								Traceworka Innovative Solutions Limited
							</div>
							<ul style="list-style:none;padding:0;margin:0 0 20px;">
								<li style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;">
									<i class="fa-solid fa-location-dot" style="color:var(--main-color);font-size:14px;margin-top:3px;flex-shrink:0;"></i>
									<span style="font-size:14px;color:#444;line-height:1.65;">No 6, Hon Tunde Sarumi Close, Off Adenuga Street, Kongi-Bodija, Ibadan, Oyo State, Nigeria</span>
								</li>
								<li style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
									<i class="fa-solid fa-phone" style="color:var(--main-color);font-size:14px;flex-shrink:0;"></i>
									<a href="tel:+2349071543344" style="font-size:14px;color:#444;text-decoration:none;font-weight:500;">+234 907 154 3344</a>
								</li>
								<li style="display:flex;align-items:center;gap:10px;">
									<i class="fa-solid fa-envelope" style="color:var(--main-color);font-size:14px;flex-shrink:0;"></i>
									<a href="mailto:hello@traceworka.ng" style="font-size:14px;color:#444;text-decoration:none;font-weight:500;">hello@traceworka.ng</a>
								</li>
							</ul>
							<div style="border-top:1px solid #e2e5ed;padding-top:18px;">
								<div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#999;margin-bottom:12px;">Follow Us</div>
								<div style="display:flex;gap:10px;">
									<a href="https://facebook.com/traceworka" target="_blank" rel="noopener noreferrer" style="width:40px;height:40px;background:#002D45;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:background 0.3s;" onmouseover="this.style.background='#f4821f'" onmouseout="this.style.background='#002D45'">
										<i class="fa-brands fa-facebook-f" style="font-size:14px;"></i>
									</a>
									<a href="https://instagram.com/traceworka" target="_blank" rel="noopener noreferrer" style="width:40px;height:40px;background:#002D45;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:background 0.3s;" onmouseover="this.style.background='#f4821f'" onmouseout="this.style.background='#002D45'">
										<i class="fa-brands fa-instagram" style="font-size:14px;"></i>
									</a>
									<a href="https://linkedin.com/company/traceworka" target="_blank" rel="noopener noreferrer" style="width:40px;height:40px;background:#002D45;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:background 0.3s;" onmouseover="this.style.background='#f4821f'" onmouseout="this.style.background='#002D45'">
										<i class="fa-brands fa-linkedin-in" style="font-size:14px;"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End Contact Form + Map -->

	<!-- CTA Banner -->
	<section class="registration-one">
		<div class="registration-one_pattern" style="background-image:url(assets/images/background/pattern-1.png)"></div>
		<div class="auto-container">
			<div class="row clearfix">
				<div class="col-lg-8 col-md-12" style="margin:auto;">
					<div class="sec-title centered title-anim">
						<div class="sec-title_title">Ready to Join?</div>
						<h2 class="sec-title_heading">Register Your Child for <span>Innovation Camp 2026</span></h2>
						<div class="sec-title_text">Spaces are limited. Secure your child's place today before registration closes.</div>
					</div>
					<div style="text-align:center;margin-top:30px;">
						<a href="registration.php" class="theme-btn btn-style-one" style="margin-right:15px;margin-bottom:15px;">
							<span class="btn-wrap">
								<span class="text-one">Register Now <i class="flaticon-next-1"></i></span>
								<span class="text-two">Register Now <i class="flaticon-next-1"></i></span>
							</span>
						</a>
						<a href="tel:+2349071543344" class="theme-btn btn-style-two" style="margin-bottom:15px;">
							<span class="btn-wrap">
								<span class="text-one">Call Us Now <i class="flaticon-call"></i></span>
								<span class="text-two">Call Us Now <i class="flaticon-call"></i></span>
							</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End CTA Banner -->

<?php include('includes/footer.php'); ?>
