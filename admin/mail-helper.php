<?php
/**
 * Admin status-change email helper.
 * Included by dashboard.php and view.php.
 * Requires sendMail() to already be loaded (from config/mailer.php or a stub).
 */

function sendRegistrationStatusEmail(array $reg, string $new_status): bool
{
    if (!function_exists('sendMail')) return false;

    $parent  = $reg['parent_name']     ?? '';
    $child   = trim(($reg['first_name'] ?? '') . ' ' . ($reg['last_name'] ?? ''));
    $email   = $reg['email']            ?? '';
    $track   = $reg['learning_track']   ?? '';
    $courses = $reg['courses']          ?? '';
    $mode    = $reg['mode_of_instruction'] ?? 'Physical';
    $camp_id = $reg['camp_id']          ?? 'TBA';
    $package = $reg['package']          ?? '';

    if (!$email) {
        error_log('ISC Admin email: no email address on record id=' . ($reg['id'] ?? '?'));
        return false;
    }

    if ($new_status === 'confirmed') {
        $subject = 'Your Admission is Confirmed – ISC 2026 | ' . $child;
        $body    = _isc_build_confirmed_email($parent, $child, $track, $courses, $mode, $camp_id, $package);
    } elseif ($new_status === 'cancelled') {
        $subject = 'Registration Update – ISC 2026 | ' . $child;
        $body    = _isc_build_cancelled_email($parent, $child, $track);
    } else {
        return false;
    }

    return sendMail($email, $parent, $subject, $body);
}

function _isc_build_confirmed_email(
    string $parent, string $child, string $track,
    string $courses, string $mode, string $camp_id, string $package
): string {
    $mode_label   = $mode === 'Virtual' ? '&#128187; Virtual'    : '&#127979; Physical';
    $next_steps   = $mode === 'Virtual'
        ? '<p style="margin:0;color:#444;font-size:15px;line-height:1.7;">Since you opted for <strong>Virtual</strong> attendance, your login credentials and platform link will be sent to this email address <strong>3 days before camp starts</strong>. Please keep an eye on your inbox.</p>'
        : '<p style="margin:0;color:#444;font-size:15px;line-height:1.7;">Since you opted for <strong>Physical</strong> attendance, please ensure <strong>' . htmlspecialchars($child) . '</strong> arrives at our Innovation Hub in <strong>Kongi-Bodija, Ibadan, Oyo State</strong> by <strong>8:30 AM on August 3, 2026</strong>.</p>';

    return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

  <tr><td style="background:#002D45;padding:32px 36px;text-align:center;">
    <p style="margin:0 0 6px;color:#f4821f;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">Ibadan Summer Innovation Camp 2026</p>
    <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:800;line-height:1.3;">&#127881; Admission Confirmed!</h1>
    <p style="margin:10px 0 0;color:rgba(255,255,255,0.75);font-size:14px;">August 3 &#8211; 27, 2026 &nbsp;|&nbsp; Ibadan, Oyo State</p>
  </td></tr>

  <tr><td style="padding:36px 36px 28px;">
    <p style="margin:0 0 18px;color:#1a1a2e;font-size:16px;line-height:1.65;">Dear <strong>' . htmlspecialchars($parent) . '</strong>,</p>
    <p style="margin:0 0 20px;color:#444;font-size:15px;line-height:1.7;">
      We are delighted to confirm that <strong>' . htmlspecialchars($child) . '&#39;s</strong> registration for the
      <strong>Ibadan Summer Innovation Camp 2026</strong> has been officially confirmed!
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e8eaf0;border-radius:10px;overflow:hidden;margin-bottom:28px;">
      <tr style="background:#002D45;">
        <td colspan="2" style="padding:12px 20px;color:#f4821f;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:0.8px;">Admission Details</td>
      </tr>
      <tr>
        <td style="padding:12px 18px;font-size:13px;color:#888;width:160px;border-bottom:1px solid #f4f5f8;font-weight:600;">Camp ID</td>
        <td style="padding:12px 18px;font-size:20px;color:#002D45;border-bottom:1px solid #f4f5f8;font-weight:900;letter-spacing:3px;">' . htmlspecialchars($camp_id) . '</td>
      </tr>
      <tr>
        <td style="padding:12px 18px;font-size:13px;color:#888;border-bottom:1px solid #f4f5f8;font-weight:600;">Student</td>
        <td style="padding:12px 18px;font-size:15px;color:#1a1a2e;border-bottom:1px solid #f4f5f8;font-weight:700;">' . htmlspecialchars($child) . '</td>
      </tr>
      <tr>
        <td style="padding:12px 18px;font-size:13px;color:#888;border-bottom:1px solid #f4f5f8;font-weight:600;">Learning Track</td>
        <td style="padding:12px 18px;font-size:15px;color:#1a1a2e;border-bottom:1px solid #f4f5f8;">' . htmlspecialchars($track) . '</td>
      </tr>
      <tr>
        <td style="padding:12px 18px;font-size:13px;color:#888;border-bottom:1px solid #f4f5f8;font-weight:600;">Course(s)</td>
        <td style="padding:12px 18px;font-size:15px;color:#1a1a2e;border-bottom:1px solid #f4f5f8;">' . htmlspecialchars($courses) . '</td>
      </tr>
      <tr>
        <td style="padding:12px 18px;font-size:13px;color:#888;border-bottom:1px solid #f4f5f8;font-weight:600;">Mode</td>
        <td style="padding:12px 18px;font-size:15px;color:#1a1a2e;border-bottom:1px solid #f4f5f8;">' . $mode_label . '</td>
      </tr>
      <tr>
        <td style="padding:12px 18px;font-size:13px;color:#888;font-weight:600;">Package</td>
        <td style="padding:12px 18px;font-size:15px;color:#f4821f;font-weight:700;">' . htmlspecialchars($package) . '</td>
      </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fff4;border:1.5px solid #2ecc71;border-radius:10px;margin-bottom:28px;">
      <tr><td style="padding:20px 24px;">
        <p style="margin:0 0 10px;color:#1a1a2e;font-size:15px;font-weight:700;">&#128204; What Happens Next</p>
        ' . $next_steps . '
      </td></tr>
    </table>

    <p style="margin:0 0 14px;color:#444;font-size:15px;line-height:1.7;">
      If you have any questions, contact us at
      <a href="mailto:summercamp@traceworka.ng" style="color:#f4821f;font-weight:600;">summercamp@traceworka.ng</a>
      or call <a href="tel:+2349071543344" style="color:#f4821f;font-weight:600;">+234 907 154 3344</a>.
    </p>
    <p style="margin:0 0 24px;color:#444;font-size:15px;line-height:1.7;">We look forward to an incredible summer with <strong>' . htmlspecialchars($child) . '</strong>!</p>
    <p style="margin:0;color:#444;font-size:15px;">Warm regards,</p>
    <p style="margin:4px 0 0;color:#1a1a2e;font-size:16px;font-weight:800;">Ibadan Summer Innovation Camp Team</p>
  </td></tr>

  <tr><td style="background:#002D45;padding:20px 36px;text-align:center;">
    <p style="margin:0 0 6px;color:rgba(255,255,255,0.6);font-size:12px;">Traceworka Innovative Solutions Limited &nbsp;|&nbsp; Kongi-Bodija, Ibadan, Oyo State</p>
    <p style="margin:0;color:rgba(255,255,255,0.4);font-size:11px;">&copy; 2026 Ibadan Summer Innovation Camp. All rights reserved.</p>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>';
}

function _isc_build_cancelled_email(string $parent, string $child, string $track): string
{
    return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

  <tr><td style="background:#002D45;padding:32px 36px;text-align:center;">
    <p style="margin:0 0 6px;color:#f4821f;font-size:12px;font-weight:700;letter-spacing:2px;text-transform:uppercase;">Ibadan Summer Innovation Camp 2026</p>
    <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:800;line-height:1.3;">Registration Update</h1>
  </td></tr>

  <tr><td style="padding:36px 36px 28px;">
    <p style="margin:0 0 18px;color:#1a1a2e;font-size:16px;line-height:1.65;">Dear <strong>' . htmlspecialchars($parent) . '</strong>,</p>
    <p style="margin:0 0 20px;color:#444;font-size:15px;line-height:1.7;">
      We regret to inform you that <strong>' . htmlspecialchars($child) . '&#39;s</strong> registration for the
      <strong>Ibadan Summer Innovation Camp 2026</strong> (' . htmlspecialchars($track) . ' Track) has been cancelled.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff5f5;border:1.5px solid #e74c3c;border-radius:10px;margin-bottom:28px;">
      <tr><td style="padding:18px 22px;">
        <p style="margin:0;color:#c0392b;font-size:14px;line-height:1.7;">
          If you believe this is an error, or would like to discuss your registration, please do not hesitate to contact our team — we are happy to assist you.
        </p>
      </td></tr>
    </table>

    <p style="margin:0 0 14px;color:#444;font-size:15px;line-height:1.7;">
      Reach us at
      <a href="mailto:summercamp@traceworka.ng" style="color:#f4821f;font-weight:600;">summercamp@traceworka.ng</a>
      or call <a href="tel:+2349071543344" style="color:#f4821f;font-weight:600;">+234 907 154 3344</a>.
    </p>
    <p style="margin:0;color:#444;font-size:15px;">Warm regards,</p>
    <p style="margin:4px 0 0;color:#1a1a2e;font-size:16px;font-weight:800;">Ibadan Summer Innovation Camp Team</p>
  </td></tr>

  <tr><td style="background:#002D45;padding:20px 36px;text-align:center;">
    <p style="margin:0 0 6px;color:rgba(255,255,255,0.6);font-size:12px;">Traceworka Innovative Solutions Limited &nbsp;|&nbsp; Kongi-Bodija, Ibadan, Oyo State</p>
    <p style="margin:0;color:rgba(255,255,255,0.4);font-size:11px;">&copy; 2026 Ibadan Summer Innovation Camp. All rights reserved.</p>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>';
}
