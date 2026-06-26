<?php
session_start();

// Guard: only accessible after a successful registration
if (empty($_SESSION['reg_success_name'])) {
    header('Location: registration.php');
    exit;
}

$parent_name   = htmlspecialchars($_SESSION['reg_success_name']);
$email         = htmlspecialchars($_SESSION['reg_success_email']);
$num_children  = (int) $_SESSION['reg_success_children'];
$child1_name   = htmlspecialchars($_SESSION['reg_success_child1']);

// Clear session vars so the page can't be refreshed to view again
unset(
    $_SESSION['reg_success_name'],
    $_SESSION['reg_success_email'],
    $_SESSION['reg_success_children'],
    $_SESSION['reg_success_child1']
);

$page_title = 'Registration Received – Ibadan Summer Innovation Camp 2026';
$meta_description = 'Thank you for registering for the Ibadan Summer Innovation Camp 2026. Check your email for payment details.';
include('includes/header.php');
include('includes/navbar.php');
?>

<!-- Page Title -->
<section class="page-title">
    <div class="color-one"></div>
    <div class="auto-container">
        <h1>Registration Received!</h1>
        <ul class="bread-crumb clearfix">
            <li><a href="index.php">Home</a></li>
            <li>Thank You</li>
        </ul>
    </div>
</section>

<!-- Thank You Content -->
<section style="padding:70px 0;background:#f8f9ff;">
    <div class="auto-container">
        <div style="max-width:680px;margin:0 auto;text-align:center;">

            <!-- Success Icon -->
            <div style="width:90px;height:90px;border-radius:50%;background:#e8f8f0;display:flex;align-items:center;justify-content:center;margin:0 auto 28px;">
                <i class="fa-solid fa-circle-check" style="font-size:48px;color:#27ae60;"></i>
            </div>

            <h2 style="font-size:34px;color:#1a1a2e;margin-bottom:14px;font-weight:800;">
                Thank You, <?php echo $parent_name; ?>!
            </h2>
            <p style="font-size:17px;color:#555;line-height:1.75;margin-bottom:32px;">
                Your registration for <?php echo $num_children === 1 ? '<strong>' . $child1_name . '</strong> has' : '<strong>' . $num_children . ' children</strong> has'; ?>
                been received successfully. A confirmation email with your <strong>payment details</strong> has been sent to
                <strong><?php echo $email; ?></strong>.
            </p>

            <!-- Next Steps -->
            <div style="background:#fff;border-radius:16px;padding:36px;box-shadow:0 4px 20px rgba(0,0,0,0.07);text-align:left;margin-bottom:36px;">
                <h4 style="color:#1a1a2e;font-size:18px;font-weight:800;margin-bottom:22px;border-bottom:2px solid #f0f2f7;padding-bottom:12px;">
                    What Happens Next?
                </h4>
                <div style="display:flex;flex-direction:column;gap:18px;">
                    <div style="display:flex;align-items:flex-start;gap:16px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#f4821f;color:#fff;font-weight:800;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">1</div>
                        <div>
                            <strong style="color:#1a1a2e;display:block;margin-bottom:4px;">Check Your Email</strong>
                            <span style="color:#666;font-size:15px;line-height:1.6;">Open the confirmation email sent to <strong><?php echo $email; ?></strong>. It contains the full payment details and bank account number.</span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:16px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#f4821f;color:#fff;font-weight:800;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">2</div>
                        <div>
                            <strong style="color:#1a1a2e;display:block;margin-bottom:4px;">Make Payment — GTBank</strong>
                            <span style="color:#666;font-size:15px;line-height:1.6;">Transfer the amount due to <strong>Traceworka Innovative Solutions Limited</strong>, GTBank account <strong style="font-size:16px;color:#002D45;letter-spacing:1px;">0745519031</strong>.</span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:16px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#f4821f;color:#fff;font-weight:800;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">3</div>
                        <div>
                            <strong style="color:#1a1a2e;display:block;margin-bottom:4px;">Send Payment Proof via WhatsApp</strong>
                            <span style="color:#666;font-size:15px;line-height:1.6;">Screenshot or photo of your payment receipt to
                                <a href="https://wa.me/2349071543344" style="color:#f4821f;font-weight:700;text-decoration:none;">+234 907 154 3344</a>.
                                Include <?php echo $num_children === 1 ? "your child's name" : "all children's names"; ?> in the message.
                            </span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:16px;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#27ae60;color:#fff;font-weight:800;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid fa-check" style="font-size:14px;"></i>
                        </div>
                        <div>
                            <strong style="color:#1a1a2e;display:block;margin-bottom:4px;">Place Confirmed!</strong>
                            <span style="color:#666;font-size:15px;line-height:1.6;">Once payment is verified, we will confirm your registration and send camp details. See you in August!</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Notice -->
            <div style="background:#fff5f5;border:1.5px solid #e74c3c;border-radius:10px;padding:16px 20px;margin-bottom:36px;text-align:left;">
                <p style="margin:0;color:#c0392b;font-size:14px;line-height:1.65;">
                    <strong>Important:</strong> Your place is only confirmed after payment has been received and verified.
                    Early registration is encouraged as seats are limited.
                </p>
            </div>

            <!-- CTA Buttons -->
            <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
                <a href="index.php" class="theme-btn btn-style-one">
                    <span class="btn-wrap">
                        <span class="text-one">Back to Home</span>
                        <span class="text-two">Back to Home</span>
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
    </div>
</section>

<?php include('includes/footer.php'); ?>
