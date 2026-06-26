<?php
http_response_code(404);
$page_title = '404 – Page Not Found | Ibadan Summer Innovation Camp 2026';
$meta_description = 'The page you are looking for could not be found. Return to the Ibadan Summer Innovation Camp 2026 homepage.';
include('includes/header.php');
include('includes/navbar.php');
?>

<!-- Page Title -->
<section class="page-title">
    <div class="color-one"></div>
    <div class="auto-container">
        <h1>Page Not Found</h1>
        <ul class="bread-crumb clearfix">
            <li><a href="index.php">Home</a></li>
            <li>404</li>
        </ul>
    </div>
</section>

<!-- 404 Content -->
<section style="padding:80px 0;text-align:center;background:#f8f9ff;">
    <div class="auto-container">
        <div style="font-size:120px;font-weight:900;color:#f4821f;line-height:1;margin-bottom:20px;">404</div>
        <h2 style="font-size:32px;color:#1a1a2e;margin-bottom:16px;">Oops! Page Not Found</h2>
        <p style="font-size:17px;color:#666;max-width:520px;margin:0 auto 36px;line-height:1.7;">
            The page you are looking for may have been moved, deleted, or never existed.
            Let's get you back on track.
        </p>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="index.php" class="theme-btn btn-style-one">
                <span class="btn-wrap">
                    <span class="text-one">Back to Home</span>
                    <span class="text-two">Back to Home</span>
                </span>
            </a>
            <a href="registration.php" class="theme-btn btn-style-two">
                <span class="btn-wrap">
                    <span class="text-one">Register Now</span>
                    <span class="text-two">Register Now</span>
                </span>
            </a>
            <a href="contact.php" class="theme-btn btn-style-two">
                <span class="btn-wrap">
                    <span class="text-one">Contact Us</span>
                    <span class="text-two">Contact Us</span>
                </span>
            </a>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>
