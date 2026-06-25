<?php
session_start();
require_once('config/db.php');

$total_seats = 100;
$seats_remaining = $total_seats;
try {
    $conn = getDBConnection();
    $seats_result = $conn->query("SELECT COUNT(*) AS total FROM registrations");
    if ($seats_result) {
        $seats_row = $seats_result->fetch_assoc();
        $seats_remaining = max(0, $total_seats - (int)$seats_row['total']);
    }
    $conn->close();
} catch (Exception $e) {
    // DB unavailable — seats counter defaults to 100
}

$early_bird_deadline = new DateTime('2026-07-20 00:00:00');
$early_bird_expired  = new DateTime() >= $early_bird_deadline;

$page_title = 'Ibadan Summer Innovation Camp 2026 | Empowering Young Innovators';
$meta_description = 'Join the Ibadan Summer Innovation Camp 2026 – A 4-week hands-on learning adventure for children aged 7–18 in Technology, Entrepreneurship, Vocational Skills & Life Skills. August 3–27, 2026, Ibadan, Oyo State.';
include('includes/header.php');
include('includes/navbar.php');
?>

	<!-- Hero / Slider Section -->
	<section class="slider-one">
		<div class="slider-one_down">
			<img src="assets/images/main-slider/down.png" alt="" />
		</div>
		<div class="main-slider swiper-container">
			<div class="swiper-wrapper">

				<!-- Slide 1 -->
				<div class="swiper-slide">
					<div class="slider-one_icon-one" style="background-image:url(assets/images/main-slider/icon-1.png)"></div>
					<div class="auto-container">
						<div class="row clearfix">
							<!-- Content Column -->
							<div class="slider-one_content col-lg-6 col-md-12 col-sm-12">
								<div class="slider-one_content-inner">
									<div class="slider-one_title">&#127775; Registration Now Open for Summer 2026</div>
									<h1 class="slider-one_heading">IBADAN SUMMER INNOVATION <span>CAMP 2026</span></h1>
									<div class="slider-one_text">Empowering Young Innovators with Technology, Creativity, Entrepreneurship &amp; Future-Ready Skills.</div>
									<div class="slider-one_button d-flex align-items-center flex-wrap">
										<a href="registration.php" class="theme-btn btn-style-one">
											<span class="btn-wrap">
												<span class="text-one">Register Now <i class="flaticon-next-1"></i></span>
												<span class="text-two">Register Now <i class="flaticon-next-1"></i></span>
											</span>
										</a>
											<div class="slider-one_seats">
												Early Bird Ends: <span>19th July, 2026</span>
											</div>
									</div>
									<div class="hero-widgets-row">
										<div class="slider-one_booking">
											<div class="slider-one_booking-title"><i><img src="assets/images/main-slider/fire.png" alt="" /></i>Early Bird Offer</div>
											<div class="time-countdown clearfix" data-countdown="2026/08/03"></div>
										</div>
										<div class="hero-seats-card">
											<div class="hero-seats-card_title">Available Seats</div>
											<div class="hero-seats-card_count"><?php echo $seats_remaining; ?></div>
											<div class="hero-seats-card_sub">of <?php echo $total_seats; ?> spots</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Image Column -->
							<div class="slider-one_image-column col-lg-6 col-md-12 col-sm-12">
								<div class="slider-one_image-outer">
									<div class="slider-one_shadow" style="background-image:url(assets/images/main-slider/shadow.png)"></div>
									<div class="slider-one_icon-two" style="background-image:url(assets/images/main-slider/icon-2.png)"></div>
									<div class="slider-one_icon-three" style="background-image:url(assets/images/main-slider/icon-3.png)"></div>
									<div class="slider-one_image">
										<img src="assets/images/main-slider/image-1.png" alt="Ibadan Summer Innovation Camp 2026" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="slider-one_color-one"></div>
					<div class="slider-one_color-two"></div>
					<div class="slider-one_color-three"></div>
					<div class="slider-one_color-four"></div>
				</div>

				<!-- Slide 2 -->
				<div class="swiper-slide">
					<div class="slider-one_icon-one" style="background-image:url(assets/images/main-slider/icon-1.png)"></div>
					<div class="auto-container">
						<div class="row clearfix">
							<div class="slider-one_content col-lg-6 col-md-12 col-sm-12">
								<div class="slider-one_content-inner">
									<div class="slider-one_title">&#127775; Ages 7–18 | August 3–27, 2026</div>
									<h1 class="slider-one_heading">Where Learning Meets <span>Innovation</span></h1>
									<div class="slider-one_text">Join an exciting 4-week learning adventure designed for children and teenagers. Hands-on projects, technology training, creative workshops, and entrepreneurship lessons.</div>
									<div class="slider-one_button d-flex align-items-center flex-wrap">
										<a href="registration.php" class="theme-btn btn-style-one">
											<span class="btn-wrap">
												<span class="text-one">Register Now <i class="flaticon-next-1"></i></span>
												<span class="text-two">Register Now <i class="flaticon-next-1"></i></span>
											</span>
										</a>
										<div class="slider-one_seats">
											Schedule: <span>Mon–Thu, 9AM–3PM</span>
										</div>
									</div>
									<div class="hero-widgets-row">
										<div class="slider-one_booking">
											<div class="slider-one_booking-title"><i><img src="assets/images/main-slider/fire.png" alt="" /></i>Ibadan, Oyo State</div>
											<div class="time-countdown clearfix" data-countdown="2026/08/03"></div>
										</div>
										<div class="hero-seats-card">
											<div class="hero-seats-card_title">Available Seats</div>
											<div class="hero-seats-card_count"><?php echo $seats_remaining; ?></div>
											<div class="hero-seats-card_sub">of <?php echo $total_seats; ?> spots</div>
										</div>
									</div>
								</div>
							</div>
							<div class="slider-one_image-column col-lg-6 col-md-12 col-sm-12">
								<div class="slider-one_image-outer">
									<div class="slider-one_shadow" style="background-image:url(assets/images/main-slider/shadow.png)"></div>
									<div class="slider-one_icon-two" style="background-image:url(assets/images/main-slider/icon-2.png)"></div>
									<div class="slider-one_icon-three" style="background-image:url(assets/images/main-slider/icon-3.png)"></div>
									<div class="slider-one_image">
										<img src="assets/images/main-slider/image-2.png" alt="Kids Innovation Learning" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="slider-one_color-one"></div>
					<div class="slider-one_color-two"></div>
					<div class="slider-one_color-three"></div>
					<div class="slider-one_color-four"></div>
				</div>

				<!-- Slide 3 -->
				<div class="swiper-slide">
					<div class="slider-one_icon-one" style="background-image:url(assets/images/main-slider/icon-1.png)"></div>
					<div class="auto-container">
						<div class="row clearfix">
							<div class="slider-one_content col-lg-6 col-md-12 col-sm-12">
								<div class="slider-one_content-inner">
									<div class="slider-one_title">&#127775; Discover. Create. Innovate. Lead.</div>
									<h1 class="slider-one_heading">Give Your Child a <span>Head Start</span> for the Future</h1>
									<div class="slider-one_text">Practical skills in Technology, Entrepreneurship, Vocational Training &amp; Life Skills. Spaces are limited. Register today!</div>
									<div class="slider-one_button d-flex align-items-center flex-wrap">
										<a href="registration.php" class="theme-btn btn-style-one">
											<span class="btn-wrap">
												<span class="text-one">Register Now <i class="flaticon-next-1"></i></span>
												<span class="text-two">Register Now <i class="flaticon-next-1"></i></span>
											</span>
										</a>
										<div class="slider-one_seats">
											Location: <span>Ibadan, Oyo State</span>
										</div>
									</div>
									<div class="hero-widgets-row">
										<div class="slider-one_booking">
											<div class="slider-one_booking-title"><i><img src="assets/images/main-slider/fire.png" alt="" /></i>Limited Spaces</div>
											<div class="time-countdown clearfix" data-countdown="2026/08/03"></div>
										</div>
										<div class="hero-seats-card">
											<div class="hero-seats-card_title">Available Seats</div>
											<div class="hero-seats-card_count"><?php echo $seats_remaining; ?></div>
											<div class="hero-seats-card_sub">of <?php echo $total_seats; ?> spots</div>
										</div>
									</div>
								</div>
							</div>
							<div class="slider-one_image-column col-lg-6 col-md-12 col-sm-12">
								<div class="slider-one_image-outer">
									<div class="slider-one_shadow" style="background-image:url(assets/images/main-slider/shadow.png)"></div>
									<div class="slider-one_icon-two" style="background-image:url(assets/images/main-slider/icon-2.png)"></div>
									<div class="slider-one_icon-three" style="background-image:url(assets/images/main-slider/icon-3.png)"></div>
									<div class="slider-one_image">
										<img src="assets/images/main-slider/image-3.png" alt="Future Leaders" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="slider-one_color-one"></div>
					<div class="slider-one_color-two"></div>
					<div class="slider-one_color-three"></div>
					<div class="slider-one_color-four"></div>
				</div>

			</div>

			<!-- Slider Pagination -->
			<div class="slider-one_pagination"></div>

			<!-- Slider Arrows -->
			<div class="slider-one-arrow">
				<div class="main-slider-prev flaticon-left-arrow"></div>
				<div class="main-slider-next flaticon-next-1"></div>
			</div>
		</div>

		<!--Waves Container-->
		<div>
			<svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
			viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
			<defs>
			<path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
			</defs>
			<g class="parallaxx">
			<use xlink:href="#gentle-wave" x="18" y="0" fill="rgba(255,255,255,0.7" />
			<use xlink:href="#gentle-wave" x="100" y="3" fill="rgba(255,255,255,0.5)" />
			<use xlink:href="#gentle-wave" x="70" y="5" fill="rgba(255,255,255,0.3)" />
			<use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
			</g>
			</svg>
		</div>
		<!--Waves end-->
	</section>
	<!-- End Hero Section -->

	<!-- Hero Highlights Strip -->
	<section class="services-one">
		<div class="auto-container">
			<div class="sec-title centered">
				<div class="sec-title_title">Camp Highlights</div>
				<h2 class="sec-title_heading">Everything Your Child <br> Needs to <span>Thrive</span></h2>
			</div>
			<div class="row clearfix">

				<div class="service-block_one col-lg-3 col-md-6 col-sm-12">
					<div class="service-block_one-inner wow fadeInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
						<div class="service-block_one-stars" style="background-image:url(assets/images/icons/icon-1.png)"></div>
						<div class="service-block_one-icon"><i class="flaticon-team"></i></div>
						<h5 class="service-block_one-title"><a href="#about">Ages 7–18 Years</a></h5>
						<div class="service-block_one-text">Programs tailored for Junior Innovators, Young Creators, and Future Leaders across all age groups.</div>
					</div>
				</div>

				<div class="service-block_one col-lg-3 col-md-6 col-sm-12">
					<div class="service-block_one-inner wow fadeInLeft" data-wow-delay="150ms" data-wow-duration="1500ms">
						<div class="service-block_one-stars" style="background-image:url(assets/images/icons/icon-1.png)"></div>
						<div class="service-block_one-icon"><i class="flaticon-calendar"></i></div>
						<h5 class="service-block_one-title"><a href="#camp-journey">August 3–27, 2026</a></h5>
						<div class="service-block_one-text">A 4-week immersive programme. Monday to Thursday, 9:00 AM – 3:00 PM daily.</div>
					</div>
				</div>

				<div class="service-block_one col-lg-3 col-md-6 col-sm-12">
					<div class="service-block_one-inner wow fadeInLeft" data-wow-delay="300ms" data-wow-duration="1500ms">
						<div class="service-block_one-stars" style="background-image:url(assets/images/icons/icon-1.png)"></div>
						<div class="service-block_one-icon"><i class="flaticon-leader"></i></div>
						<h5 class="service-block_one-title"><a href="#learning-tracks">4 Learning Tracks</a></h5>
						<div class="service-block_one-text">Technology, Entrepreneurship, Vocational Skills, and General Life Skills tracks available.</div>
					</div>
				</div>

				<div class="service-block_one col-lg-3 col-md-6 col-sm-12">
					<div class="service-block_one-inner wow fadeInLeft" data-wow-delay="450ms" data-wow-duration="1500ms">
						<div class="service-block_one-stars" style="background-image:url(assets/images/icons/icon-1.png)"></div>
						<div class="service-block_one-icon"><i class="flaticon-padlock"></i></div>
						<h5 class="service-block_one-title"><a href="#about">Professional Security</a></h5>
						<div class="service-block_one-text">Trained security personnel present throughout the camp to ensure a safe, secure, and supervised environment for all participants.</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End Hero Highlights -->

	<!-- About Section -->
	<section class="about-one" id="about">
		<div class="outer-container">
			<div class="auto-container">
				<div class="about-one_icon" style="background-image:url(assets/images/icons/icon-3.png)"></div>
				<div class="row clearfix">

					<!-- Image Column -->
					<div class="about-one_image-column col-lg-6 col-md-12 col-sm-12">
						<div class="about-one_image-outer">
							<div class="about-one_experiance">
								<div class="about-one_experiance-inner">
									4 <span>Week <br> Programme</span>
								</div>
							</div>
							<div class="about-one_image">
								<div class="about-one_color-one"></div>
								<div class="about-one_color-two"></div>
								<img src="assets/images/resource/about-1.png" alt="Kids Learning at Ibadan Summer Innovation Camp" loading="lazy" />
							</div>
						</div>
					</div>

					<!-- Content Column -->
					<div class="about-one_content-column col-lg-6 col-md-12 col-sm-12">
						<div class="about-one_content-outer">
							<div class="sec-title title-anim">
								<div class="sec-title_title">About the Camp</div>
								<h2 class="sec-title_heading">Where Learning Meets <span>Innovation</span></h2>
								<div class="sec-title_text">The Ibadan Summer Innovation Camp 2026 is a transformational holiday programme created to help young people discover their talents, build confidence, and develop practical skills for the future.<br><br>Our camp combines technology, creativity, entrepreneurship, leadership, and vocational learning in a fun, engaging, and supportive environment. Whether your child dreams of becoming a software developer, entrepreneur, designer, engineer, or business leader, this camp provides the foundation to begin that journey.</div>
							</div>
							<ul class="about-one_list">
								<li><i class="flaticon-checked"></i><strong>Vision:</strong> To become the leading youth innovation and skills development programme in Southwest Nigeria.</li>
								<li><i class="flaticon-checked"></i><strong>Mission:</strong> To provide world-class, hands-on learning experiences that develop digital, vocational, and entrepreneurial skills.</li>
							</ul>
							<div class="about-one_options d-flex align-items-center flex-wrap">
								<div class="about-one_button">
									<a href="registration.php" class="theme-btn btn-style-one">
										<span class="btn-wrap">
											<span class="text-one">Register Now <i class="flaticon-next-1"></i></span>
											<span class="text-two">Register Now <i class="flaticon-next-1"></i></span>
										</span>
									</a>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
	<!-- End About Section -->

	<!-- Why Choose Us Section -->
	<section class="choose-one" id="why-choose-us">
		<div class="auto-container">
			<div class="sec-title centered" style="margin-bottom:50px;">
				<div class="sec-title_title">Why Choose Us</div>
				<h2 class="sec-title_heading">6 Reasons to Choose <br> <span>Innovation Camp</span></h2>
			</div>
			<div class="row clearfix">

				<div class="choose-one_content-column col-lg-12 col-md-12 col-sm-12">
					<div class="row clearfix">

						<div class="choose-block_one col-lg-4 col-md-6 col-sm-12">
							<div class="choose-block_one-inner">
								<div class="choose-block_one-content">
									<div class="choose-block_one-icon"><i class="flaticon-mountain"></i></div>
									<h2 class="choose-block_one-title">Future-Ready Skills</h2>
									<div class="choose-block_one-text">Learn practical skills that prepare students for tomorrow's opportunities in technology, business, and beyond.</div>
								</div>
							</div>
						</div>

						<div class="choose-block_one col-lg-4 col-md-6 col-sm-12">
							<div class="choose-block_one-inner">
								<div class="choose-block_one-content">
									<div class="choose-block_one-icon"><i class="flaticon-volume"></i></div>
									<h2 class="choose-block_one-title">Hands-On Learning</h2>
									<div class="choose-block_one-text">Build projects, create solutions, and learn through real-world experiences rather than passive instruction.</div>
								</div>
							</div>
						</div>

						<div class="choose-block_one col-lg-4 col-md-6 col-sm-12">
							<div class="choose-block_one-inner">
								<div class="choose-block_one-content">
									<div class="choose-block_one-icon"><i class="flaticon-user"></i></div>
									<h2 class="choose-block_one-title">Experienced Instructors</h2>
									<div class="choose-block_one-text">Learn from qualified professionals and industry practitioners with real-world expertise.</div>
								</div>
							</div>
						</div>

						<div class="choose-block_one col-lg-4 col-md-6 col-sm-12">
							<div class="choose-block_one-inner">
								<div class="choose-block_one-content">
									<div class="choose-block_one-icon"><i class="flaticon-happiness"></i></div>
									<h2 class="choose-block_one-title">Fun &amp; Engaging</h2>
									<div class="choose-block_one-text">A perfect balance of education, creativity, teamwork, and recreation in a fun, supportive atmosphere.</div>
								</div>
							</div>
						</div>

						<div class="choose-block_one col-lg-4 col-md-6 col-sm-12">
							<div class="choose-block_one-inner">
								<div class="choose-block_one-content">
									<div class="choose-block_one-icon"><i class="flaticon-trophy"></i></div>
									<h2 class="choose-block_one-title">Entrepreneurship Focus</h2>
									<div class="choose-block_one-text">Develop business thinking, leadership, innovation skills, and the mindset of future entrepreneurs.</div>
								</div>
							</div>
						</div>

						<div class="choose-block_one col-lg-4 col-md-6 col-sm-12">
							<div class="choose-block_one-inner">
								<div class="choose-block_one-content">
									<div class="choose-block_one-icon"><i class="flaticon-leader"></i></div>
									<h2 class="choose-block_one-title">Showcase &amp; Graduation</h2>
									<div class="choose-block_one-text">Present projects and celebrate achievements at the grand graduation event with family and mentors.</div>
								</div>
							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End Why Choose Us -->

	<!-- Learning Tracks Section -->
	<section class="program-one" id="learning-tracks">
		<div class="program-one_icon" style="background-image:url(assets/images/icons/icon-4.png)"></div>
		<div class="auto-container">
			<div class="sec-title">
				<div class="sec-title_title">Our Programmes</div>
				<h2 class="sec-title_heading">4 Exciting <span>Learning Tracks</span> <br> to Explore</h2>
			</div>
			<div class="program-one_carousel swiper-container">
				<div class="swiper-wrapper">

					<!-- Technology Track -->
					<div class="swiper-slide">
						<div class="program-block_one">
							<div class="program-block_one-inner">
								<div class="program-block_one-image">
									<img src="assets/images/resource/program-1.png" alt="Technology Track" loading="lazy" />
								</div>
								<div class="program-block_one-content">
									<div class="program-block_one-date">Track <span>1</span></div>
									<h5 class="program-block_one-title"><a href="registration.php">Technology Track</a></h5>
									<div class="program-block_one-text">Coding &amp; Programming · Robotics · Web Design · Graphic Design · UI/UX Design · Video Editing</div>
									<div class="d-flex justify-content-between align-items-center flex-wrap">
										<div class="program-block_one-experiance">Ages 7–18</div>
										<a class="program-block_one-join" href="registration.php">Join now <i class="flaticon-next-1"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Entrepreneurship Track -->
					<div class="swiper-slide">
						<div class="program-block_one">
							<div class="program-block_one-inner">
								<div class="program-block_one-image">
									<img src="assets/images/resource/program-2.png" alt="Entrepreneurship Track" loading="lazy" />
								</div>
								<div class="program-block_one-content">
									<div class="program-block_one-date">Track <span>2</span></div>
									<h5 class="program-block_one-title"><a href="registration.php">Entrepreneurship Track</a></h5>
									<div class="program-block_one-text">Branding · Digital Marketing · Sales Skills · Startup Fundamentals · Business Pitching · Financial Planning</div>
									<div class="d-flex justify-content-between align-items-center flex-wrap">
										<div class="program-block_one-experiance">Ages 11–18</div>
										<a class="program-block_one-join" href="registration.php">Join now <i class="flaticon-next-1"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Vocational Skills Track -->
					<div class="swiper-slide">
						<div class="program-block_one">
							<div class="program-block_one-inner">
								<div class="program-block_one-image">
									<img src="assets/images/resource/program-3.png" alt="Vocational Skills Track" loading="lazy" />
								</div>
								<div class="program-block_one-content">
									<div class="program-block_one-date">Track <span>3</span></div>
									<h5 class="program-block_one-title"><a href="registration.php">Vocational Skills Track</a></h5>
									<div class="program-block_one-text">Fashion Design · Baking &amp; Pastry · Bead Making · Hair Styling · Soap Production · DIY Crafts</div>
									<div class="d-flex justify-content-between align-items-center flex-wrap">
										<div class="program-block_one-experiance">Ages 7–18</div>
										<a class="program-block_one-join" href="registration.php">Join now <i class="flaticon-next-1"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- General Life Skills -->
					<div class="swiper-slide">
						<div class="program-block_one">
							<div class="program-block_one-inner">
								<div class="program-block_one-image">
									<img src="assets/images/resource/program-4.png" alt="General Life Skills" loading="lazy" />
								</div>
								<div class="program-block_one-content">
									<div class="program-block_one-date">Track <span>4</span></div>
									<h5 class="program-block_one-title"><a href="registration.php">General Life Skills</a></h5>
									<div class="program-block_one-text">Digital Literacy · AI for Kids &amp; Teens · Financial Literacy · Public Speaking · Leadership &amp; Teamwork · Career Discovery</div>
									<div class="d-flex justify-content-between align-items-center flex-wrap">
										<div class="program-block_one-experiance">Ages 7–18</div>
										<a class="program-block_one-join" href="registration.php">Join now <i class="flaticon-next-1"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

				<div class="program-one_carousel-pagination"></div>
				<div class="program-one_carousel-prev fas fa-angle-left fa-fw"></div>
				<div class="program-one_carousel-next fas fa-angle-right fa-fw"></div>
			</div>
		</div>
	</section>
	<!-- End Learning Tracks -->

	<!-- Camp Journey Section -->
	<section class="registration-one" id="camp-journey">
		<div class="registration-one_pattern" style="background-image:url(assets/images/background/pattern-1.png)"></div>
		<div class="auto-container">
			<div class="sec-title centered" style="margin-bottom:50px;">
				<div class="sec-title_title">The Camp Experience</div>
				<h2 class="sec-title_heading">Your 4-Week <span>Camp Journey</span></h2>
			</div>
			<div class="row clearfix">

				<!-- Week 1 -->
				<div class="choose-block_one col-lg-3 col-md-6 col-sm-12">
					<div class="choose-block_one-inner">
						<div class="choose-block_one-content">
							<div class="choose-block_one-icon"><i class="flaticon-happiness"></i></div>
							<h2 class="choose-block_one-title">Week 1</h2>
							<div class="choose-block_one-text"><strong>Explore &amp; Orientation</strong><br>Students meet instructors, make friends, discover their interests, and begin foundational learning.</div>
						</div>
					</div>
				</div>

				<!-- Week 2 -->
				<div class="choose-block_one col-lg-3 col-md-6 col-sm-12">
					<div class="choose-block_one-inner">
						<div class="choose-block_one-content">
							<div class="choose-block_one-icon"><i class="flaticon-leader"></i></div>
							<h2 class="choose-block_one-title">Week 2</h2>
							<div class="choose-block_one-text"><strong>Learn &amp; Practice</strong><br>Hands-on lessons, guided projects, and skill-building activities across chosen tracks.</div>
						</div>
					</div>
				</div>

				<!-- Week 3 -->
				<div class="choose-block_one col-lg-3 col-md-6 col-sm-12">
					<div class="choose-block_one-inner">
						<div class="choose-block_one-content">
							<div class="choose-block_one-icon"><i class="flaticon-mountain"></i></div>
							<h2 class="choose-block_one-title">Week 3</h2>
							<div class="choose-block_one-text"><strong>Build &amp; Create</strong><br>Students develop innovative projects and apply their knowledge to create real solutions.</div>
						</div>
					</div>
				</div>

				<!-- Week 4 -->
				<div class="choose-block_one col-lg-3 col-md-6 col-sm-12">
					<div class="choose-block_one-inner">
						<div class="choose-block_one-content">
							<div class="choose-block_one-icon"><i class="flaticon-trophy"></i></div>
							<h2 class="choose-block_one-title">Week 4</h2>
							<div class="choose-block_one-text"><strong>Showcase &amp; Graduation</strong><br>Project presentations, exhibitions, awards ceremony, and grand graduation celebration.</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End Camp Journey -->

	<!-- Fun Activities Section -->
	<section class="services-one" style="background:#f8f9ff;">
		<div class="auto-container">
			<div class="sec-title centered">
				<div class="sec-title_title">Beyond the Classroom</div>
				<h2 class="sec-title_heading">Fun Activities &amp; <span>Special Events</span></h2>
			</div>
			<div class="row clearfix">

				<?php
				$activities = [
					['icon' => 'flaticon-team',      'title' => 'Coding Competition', 'desc' => 'Friendly coding contests to challenge and showcase programming skills.'],
					['icon' => 'flaticon-volume',     'title' => 'Quiz Challenge',     'desc' => 'Knowledge-based competitions covering tech, science, and general studies.'],
					['icon' => 'flaticon-happiness',  'title' => 'Talent Show',        'desc' => 'A platform for students to showcase their creative talents and performances.'],
					['icon' => 'flaticon-trophy',     'title' => 'Chess Tournament',   'desc' => 'Strategic thinking competition to sharpen analytical and problem-solving skills.'],
					['icon' => 'flaticon-leader',     'title' => 'Debate Session',     'desc' => 'Public speaking and critical thinking through structured debate activities.'],
					['icon' => 'flaticon-mountain',   'title' => 'Career Day',         'desc' => 'Meet industry professionals and explore exciting career paths and possibilities.'],
				];
				$delays = ['0ms','150ms','300ms','0ms','150ms','300ms'];
				foreach ($activities as $i => $act): ?>
				<div class="service-block_one col-lg-4 col-md-6 col-sm-12">
					<div class="service-block_one-inner wow fadeInLeft" data-wow-delay="<?php echo $delays[$i]; ?>" data-wow-duration="1500ms">
						<div class="service-block_one-stars" style="background-image:url(assets/images/icons/icon-1.png)"></div>
						<div class="service-block_one-icon"><i class="<?php echo $act['icon']; ?>"></i></div>
						<h5 class="service-block_one-title"><a href="registration.php"><?php echo $act['title']; ?></a></h5>
						<div class="service-block_one-text"><?php echo $act['desc']; ?></div>
					</div>
				</div>
				<?php endforeach; ?>

			</div>
		</div>
	</section>
	<!-- End Fun Activities -->

	<!-- Who Can Join Section -->
	<section class="team-one" id="who-can-join">
		<div class="outer-container">
			<div class="auto-container">
				<div class="sec-title centered">
					<div class="sec-title_title">Who Can Join</div>
					<h2 class="sec-title_heading">Programmes Tailored for <span>Every Age Group</span></h2>
					<div class="sec-title_text" style="max-width:600px;margin:0 auto;">Programs and activities are tailored to suit each age group's learning style, pace, and interests.</div>
				</div>
				<div class="inner-container">
					<div class="three-items_carousel swiper-container">
						<div class="swiper-wrapper">

							<div class="swiper-slide">
								<div class="team-block_one">
									<div class="team-block_one-inner">
										<div class="team-block_one-image">
											<img src="assets/images/resource/program-5.png" alt="Junior Innovators 7-10 Years" loading="lazy" />
											<div class="team-block_one-overlay">
												<div class="team-block_one-socials" style="padding:20px;text-align:center;">
													<a href="registration.php" class="theme-btn btn-style-one" style="font-size:12px;padding:8px 16px;">
														<span class="btn-wrap"><span class="text-one">Register</span><span class="text-two">Register</span></span>
													</a>
												</div>
											</div>
										</div>
										<div class="team-block_one-content">
											<h4 class="team-block_one-heading"><a href="registration.php">Junior Innovators</a></h4>
											<div class="team-block_one-designation">Ages 7–10 Years</div>
										</div>
									</div>
								</div>
							</div>

							<div class="swiper-slide">
								<div class="team-block_one">
									<div class="team-block_one-inner">
										<div class="team-block_one-image">
											<img src="assets/images/resource/program-6.png" alt="Young Creators 11-14 Years" loading="lazy" />
											<div class="team-block_one-overlay">
												<div class="team-block_one-socials" style="padding:20px;text-align:center;">
													<a href="registration.php" class="theme-btn btn-style-one" style="font-size:12px;padding:8px 16px;">
														<span class="btn-wrap"><span class="text-one">Register</span><span class="text-two">Register</span></span>
													</a>
												</div>
											</div>
										</div>
										<div class="team-block_one-content">
											<h4 class="team-block_one-heading"><a href="registration.php">Young Creators</a></h4>
											<div class="team-block_one-designation">Ages 11–14 Years</div>
										</div>
									</div>
								</div>
							</div>

							<div class="swiper-slide">
								<div class="team-block_one">
									<div class="team-block_one-inner">
										<div class="team-block_one-image">
											<img src="assets/images/resource/program-7.png" alt="Future Leaders 15-18 Years" loading="lazy" />
											<div class="team-block_one-overlay">
												<div class="team-block_one-socials" style="padding:20px;text-align:center;">
													<a href="registration.php" class="theme-btn btn-style-one" style="font-size:12px;padding:8px 16px;">
														<span class="btn-wrap"><span class="text-one">Register</span><span class="text-two">Register</span></span>
													</a>
												</div>
											</div>
										</div>
										<div class="team-block_one-content">
											<h4 class="team-block_one-heading"><a href="registration.php">Future Leaders</a></h4>
											<div class="team-block_one-designation">Ages 15–18 Years</div>
										</div>
									</div>
								</div>
							</div>

						</div>

						<div class="team-one_arrows">
							<div class="three-items_carousel-prev fas fa-angle-left fa-fw"></div>
							<div class="three-items_carousel-next fas fa-angle-right fa-fw"></div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End Who Can Join -->

	<!-- Pricing Section -->
	<section class="registration-one" id="pricing" style="background:#f8f9ff;">
		<div class="registration-one_pattern" style="background-image:url(assets/images/background/pattern-1.png)"></div>
		<div class="auto-container">
			<div class="sec-title centered" style="margin-bottom:50px;">
				<div class="sec-title_title">Affordable Investment</div>
				<h2 class="sec-title_heading">Choose Your <span>Package</span></h2>
			</div>
			<div class="row clearfix">

				<!-- Early Bird -->
				<div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom:30px;">
					<div style="background:#fff;border-radius:20px;padding:40px 30px;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,0.08);border:2px solid transparent;transition:all 0.3s;position:relative;overflow:hidden;<?php echo $early_bird_expired ? 'opacity:.62;' : ''; ?>">
						<?php if ($early_bird_expired): ?>
						<!-- Hatch overlay -->
						<div style="position:absolute;inset:0;border-radius:20px;background:repeating-linear-gradient(-45deg,rgba(0,0,0,0.048) 0,rgba(0,0,0,0.048) 5px,transparent 5px,transparent 13px);pointer-events:none;z-index:2;"></div>
						<div style="position:absolute;top:18px;left:50%;transform:translateX(-50%);background:#e74c3c;color:#fff;font-size:11px;font-weight:800;padding:4px 16px;border-radius:20px;white-space:nowrap;letter-spacing:0.5px;text-transform:uppercase;z-index:3;">Offer Expired</div>
						<?php endif; ?>
						<div style="background:linear-gradient(135deg,#f6f0ff,#e8f4fd);border-radius:12px;padding:15px;margin-bottom:20px;<?php echo $early_bird_expired ? 'margin-top:28px;' : ''; ?>">
							<img src="assets/images/icons/price-icon.png" alt="" style="height:50px;<?php echo $early_bird_expired ? 'filter:grayscale(1);' : ''; ?>">
						</div>
						<h4 style="font-size:22px;font-weight:700;margin-bottom:5px;color:<?php echo $early_bird_expired ? '#aaa' : '#1a1a2e'; ?>;">Early Bird</h4>
						<?php if ($early_bird_expired): ?>
						<div style="background:#f5f5f5;border:1.5px solid #ddd;border-radius:8px;padding:9px 14px;margin:14px 0 4px;display:flex;align-items:center;justify-content:center;gap:8px;">
							<i class="fa-solid fa-hourglass-end" style="color:#aaa;font-size:13px;flex-shrink:0;"></i>
							<span style="color:#aaa;font-size:12.5px;font-weight:600;line-height:1.3;">Ended <strong>19th July, 2026</strong></span>
						</div>
						<?php else: ?>
						<div style="background:linear-gradient(135deg,#fff4e6,#ffe8cc);border:1.5px solid #f4821f;border-radius:8px;padding:9px 14px;margin:14px 0 4px;display:flex;align-items:center;justify-content:center;gap:8px;">
							<i class="fa-solid fa-hourglass-half" style="color:#e06800;font-size:13px;flex-shrink:0;"></i>
							<span style="color:#1a1a2e;font-size:12.5px;font-weight:600;line-height:1.3;">Offer ends: <strong style="color:#e06800;">19th July, 2026</strong></span>
						</div>
						<?php endif; ?>
						<div class="pricing-amount" style="font-size:42px;font-weight:800;color:<?php echo $early_bird_expired ? '#ccc' : '#f4821f'; ?>;margin:15px 0;<?php echo $early_bird_expired ? 'text-decoration:line-through;' : ''; ?>">&#8358;45,000</div>
						<ul style="list-style:none;padding:0;margin:20px 0;text-align:left;">
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:<?php echo $early_bird_expired ? '#ccc' : '#f4821f'; ?>;margin-right:8px;"></i> Full Camp Access</li>
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:<?php echo $early_bird_expired ? '#ccc' : '#f4821f'; ?>;margin-right:8px;"></i> Learning Materials</li>
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:<?php echo $early_bird_expired ? '#ccc' : '#f4821f'; ?>;margin-right:8px;"></i> Camp T-Shirt</li>
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:<?php echo $early_bird_expired ? '#ccc' : '#f4821f'; ?>;margin-right:8px;"></i> Certificate of Participation</li>
							<li style="padding:8px 0;"><i class="flaticon-checked" style="color:<?php echo $early_bird_expired ? '#ccc' : '#f4821f'; ?>;margin-right:8px;"></i> Project Showcase Participation</li>
						</ul>
						<?php if ($early_bird_expired): ?>
						<span style="display:block;text-align:center;padding:14px;background:#f5f5f5;border-radius:10px;color:#aaa;font-size:14px;font-weight:700;">No Longer Available</span>
						<?php else: ?>
						<a href="registration.php?package=early-bird" class="theme-btn btn-style-one" style="display:block;text-align:center;">
							<span class="btn-wrap">
								<span class="text-one">Register Now <i class="flaticon-next-1"></i></span>
								<span class="text-two">Register Now <i class="flaticon-next-1"></i></span>
							</span>
						</a>
						<?php endif; ?>
					</div>
				</div>

				<!-- Standard Package -->
				<div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom:30px;">
					<div style="background:#f4821f;border-radius:20px;padding:40px 30px;text-align:center;box-shadow:0 10px 40px rgba(244,130,31,0.3);border:2px solid #f4821f;position:relative;">
						<div style="position:absolute;top:-15px;left:50%;transform:translateX(-50%);background:#1a1a2e;color:#fff;padding:5px 20px;border-radius:20px;font-size:12px;font-weight:700;">POPULAR</div>
						<div style="background:rgba(255,255,255,0.95);border-radius:12px;padding:15px;margin-bottom:20px;">
							<img src="assets/images/icons/price-icon.png" alt="" style="height:50px;">
						</div>
						<h4 style="font-size:22px;font-weight:700;margin-bottom:5px;color:#fff;">Standard Package</h4>
						<div class="pricing-amount" style="font-size:42px;font-weight:800;color:#fff;margin:15px 0;">&#8358;55,000</div>
						<ul style="list-style:none;padding:0;margin:20px 0;text-align:left;color:#fff;">
							<li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.2);"><i class="flaticon-checked" style="color:#fff;margin-right:8px;"></i> Full Camp Access</li>
							<li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.2);"><i class="flaticon-checked" style="color:#fff;margin-right:8px;"></i> Learning Materials</li>
							<li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.2);"><i class="flaticon-checked" style="color:#fff;margin-right:8px;"></i> Camp T-Shirt</li>
							<li style="padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.2);"><i class="flaticon-checked" style="color:#fff;margin-right:8px;"></i> Certificate of Participation</li>
							<li style="padding:8px 0;"><i class="flaticon-checked" style="color:#fff;margin-right:8px;"></i> Project Showcase Participation</li>
						</ul>
						<a href="registration.php?package=standard" class="theme-btn btn-style-two" style="display:block;text-align:center;background:#fff;">
							<span class="btn-wrap">
								<span class="text-one" style="color:#002D45;font-weight:700;">Register Now <i class="flaticon-next-1" style="color:#002D45;font-size:14px;"></i></span>
								<span class="text-two" style="color:#fff;font-weight:700;">Register Now <i class="flaticon-next-1" style="color:#fff;font-size:14px;"></i></span>
							</span>
						</a>
					</div>
				</div>

				<!-- Premium Package -->
				<div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom:30px;">
					<div style="background:#fff;border-radius:20px;padding:40px 30px;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,0.08);border:2px solid transparent;transition:all 0.3s;">
						<div style="background:linear-gradient(135deg,#fff3cd,#ffeeba);border-radius:12px;padding:15px;margin-bottom:20px;">
							<img src="assets/images/icons/price-icon.png" alt="" style="height:50px;">
						</div>
						<h4 style="font-size:22px;font-weight:700;margin-bottom:5px;color:#1a1a2e;">Premium Package</h4>
						<div class="pricing-amount" style="font-size:42px;font-weight:800;color:#f4821f;margin:15px 0;">&#8358;70,000</div>
						<ul style="list-style:none;padding:0;margin:20px 0;text-align:left;">
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:#f4821f;margin-right:8px;"></i> Full Camp Access</li>
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:#f4821f;margin-right:8px;"></i> Learning Materials</li>
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:#f4821f;margin-right:8px;"></i> Camp T-Shirt</li>
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:#f4821f;margin-right:8px;"></i> Certificate of Participation</li>
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:#f4821f;margin-right:8px;"></i> Project Showcase Participation</li>
							<li style="padding:8px 0;border-bottom:1px solid #f0f0f0;"><i class="flaticon-checked" style="color:#f4821f;margin-right:8px;"></i> Premium Learning Resources</li>
							<li style="padding:8px 0;"><i class="flaticon-checked" style="color:#f4821f;margin-right:8px;"></i> Special Mentorship Session</li>
						</ul>
						<a href="registration.php?package=premium" class="theme-btn btn-style-one" style="display:block;text-align:center;">
							<span class="btn-wrap">
								<span class="text-one">Register Now <i class="flaticon-next-1"></i></span>
								<span class="text-two">Register Now <i class="flaticon-next-1"></i></span>
							</span>
						</a>
					</div>
				</div>

			</div>

			<!-- Family Discount -->
			<div class="row clearfix" style="margin-top:40px;">
				<div class="col-lg-12">
					<div class="family-discount-wrap" style="background:linear-gradient(135deg,#1a1a2e,#16213e);border-radius:20px;padding:40px;color:#fff;text-align:center;">
						<div class="sec-title centered" style="margin-bottom:30px;">
							<div class="sec-title_title" style="color:#f4821f;">Family &amp; Group Discounts</div>
							<h2 class="sec-title_heading" style="color:#fff;">Save More When You Register <span>Together</span></h2>
						</div>
						<div class="row clearfix family-discount-row">
							<div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom:20px;">
								<div class="family-discount-card" style="background:rgba(255,255,255,0.1);border-radius:15px;padding:30px;border:1px solid rgba(255,255,255,0.2);">
									<div style="font-size:36px;font-weight:800;color:#f4821f;">&#8358;100,000</div>
									<div style="font-size:18px;font-weight:600;margin:8px 0;">2 Children</div>
									<div style="background:#f4821f;color:#fff;display:inline-block;padding:4px 16px;border-radius:20px;font-size:13px;font-weight:700;">Save &#8358;10,000</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom:20px;">
								<div class="family-discount-card" style="background:rgba(244,130,31,0.2);border-radius:15px;padding:30px;border:1px solid rgba(244,130,31,0.4);">
									<div style="font-size:36px;font-weight:800;color:#f4821f;">&#8358;145,000</div>
									<div style="font-size:18px;font-weight:600;margin:8px 0;color:#fff;">3 Children</div>
									<div style="background:#f4821f;color:#fff;display:inline-block;padding:4px 16px;border-radius:20px;font-size:13px;font-weight:700;">Save &#8358;20,000</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-12 col-sm-12" style="margin-bottom:20px;">
								<div class="family-discount-card" style="background:rgba(255,255,255,0.1);border-radius:15px;padding:30px;border:1px solid rgba(255,255,255,0.2);">
									<div style="font-size:28px;font-weight:800;color:#f4821f;">Schools &amp; Groups</div>
									<div style="font-size:16px;margin:10px 0;color:#ddd;">10+ Students</div>
									<div style="font-size:14px;font-weight:600;color:#f4821f;">SCHOOL GROUP RATES AVAILABLE</div>
								</div>
							</div>
						</div>
						<div style="text-align:center;margin-top:10px;">
							<a href="contact.php" class="theme-btn btn-style-one">
								<span class="btn-wrap">
									<span class="text-one">Contact Us for Group Rates <i class="flaticon-next-1"></i></span>
									<span class="text-two">Contact Us for Group Rates <i class="flaticon-next-1"></i></span>
								</span>
							</a>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
	<!-- End Pricing Section -->

	<!-- Final CTA Section -->
	<section class="testimonial-one">
		<div class="outer-container">
			<div class="auto-container">
				<div class="testimonial-one_circle-one"></div>
				<div class="testimonial-one_circle-two"></div>
				<div class="testimonial-one_icon" style="background-image:url(assets/images/icons/icon-5.png)"></div>
				<div class="testimonial-one_icon-two" style="background-image:url(assets/images/icons/icon-6.png)"></div>
				<div class="sec-title centered light">
					<div class="sec-title_title">Limited Spaces Available</div>
					<h2 class="sec-title_heading">Give Your Child a Head Start <br> for the <span>Future</span></h2>
					<div class="sec-title_text" style="max-width:650px;margin:20px auto;font-size:17px;line-height:1.75;color:#fff;"><span style="color:#f4821f;font-weight:700;">Spaces are limited.</span> Register today and help your child discover, create, innovate, and lead.</div>
					<div style="margin-top:30px;">
						<a href="registration.php" class="theme-btn btn-style-two final-cta-btn" style="margin:0 auto;display:inline-block;">
							<span class="btn-wrap">
								<span class="text-one">Register Now <i class="flaticon-next-1"></i></span>
								<span class="text-two">Register Now <i class="flaticon-next-1"></i></span>
							</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End Final CTA -->

	<!-- Contact Quick Section -->
	<section class="registration-one" id="contact-quick">
		<div class="registration-one_pattern" style="background-image:url(assets/images/background/pattern-1.png)"></div>
		<div class="auto-container">
			<div class="row clearfix">

				<div class="registration-one_title-column col-lg-6 col-md-12 col-sm-12">
					<div class="registration-one_title-outer">
						<div class="sec-title title-anim">
							<div class="sec-title_title">Get In Touch</div>
							<h2 class="sec-title_heading">Have Questions? <br> <span>Contact Us</span> Today</h2>
							<div class="sec-title_text">We're here to help you make the right choice for your child's future. Reach out to us through any of the channels below.</div>
						</div>
						<ul class="registration-one_list">
							<li>
								<i class="icon flaticon-call"></i>
								Call Us Now<br>
								<a href="tel:+2349071543344">+234 907 154 3344</a><br>
								<a href="tel:+2348135235891">+234 813 523 5891</a>
							</li>
							<li>
								<i class="icon flaticon-arroba"></i>
								Email Us<br>
								<a href="mailto:hello@traceworka.ng">hello@traceworka.ng</a>
							</li>
							<li>
								<i class="icon flaticon-maps-and-flags"></i>
								Our Location<br>
								<a href="contact.php">No 6, Hon Tunde Sarumi Close, Off Adenuga Street, Kongi-Bodija, Ibadan</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="registration-one_form-column col-lg-6 col-md-12 col-sm-12">
					<div class="registration-one_form-outer">
						<h3 class="registration-one_title">Quick Registration</h3>
						<div class="default-form">
							<form method="post" action="forms/register-process.php" id="quick-registration-form" novalidate>
								<?php
								$token = bin2hex(random_bytes(32));
								$_SESSION['csrf_token'] = $token;
								?>
								<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
								<div class="row clearfix">
									<div class="col-lg-6 col-md-6 col-sm-12 form-group">
										<label>Child's First Name</label>
										<input type="text" name="first_name" placeholder="" required>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-12 form-group">
										<label>Child's Last Name</label>
										<input type="text" name="last_name" placeholder="" required>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-12 form-group">
										<label>Parent/Guardian Email</label>
										<input type="email" name="email" placeholder="" required>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-12 form-group">
										<label>Phone Number</label>
										<input type="tel" name="phone" placeholder="" required>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-12 form-group">
										<label>Child's Age</label>
										<select name="age_group" class="custom-select-box">
											<option value="">Select Age Group</option>
											<option value="7-10">7–10 Years (Junior Innovators)</option>
											<option value="11-14">11–14 Years (Young Creators)</option>
											<option value="15-18">15–18 Years (Future Leaders)</option>
										</select>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-12 form-group">
										<label>Package</label>
										<select name="package" class="custom-select-box">
											<option value="">Select Package</option>
											<option value="Early Bird" <?php echo $early_bird_expired ? 'disabled style="color:#bbb;"' : ''; ?>>Early Bird – ₦45,000<?php echo $early_bird_expired ? ' (Expired)' : ''; ?></option>
											<option value="Standard">Standard – ₦55,000</option>
											<option value="Premium">Premium – ₦70,000</option>
										</select>
									</div>
									<div class="col-lg-12 col-md-12 col-sm-12 form-group">
										<button type="submit" class="theme-btn btn-style-two">
											<span class="btn-wrap">
												<span class="text-one">Submit &amp; Register <i class="flaticon-next-1"></i></span>
												<span class="text-two">Submit &amp; Register <i class="flaticon-next-1"></i></span>
											</span>
										</button>
									</div>
									<div class="col-lg-12" style="margin-top:10px;">
										<p style="font-size:13px;color:#fff;opacity:0.85;">For the full registration form with all details, <a href="registration.php" style="color:#fff;font-weight:600;text-decoration:underline;">click here</a>.</p>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	<!-- End Contact Quick Section -->

<?php include('includes/footer.php'); ?>
