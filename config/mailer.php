<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('SMTP_HOST')) {
    require_once __DIR__ . '/app.php';
}

/**
 * Send an HTML email via SMTP (PHPMailer).
 *
 * @param string $to        Recipient email
 * @param string $to_name   Recipient display name
 * @param string $subject   Email subject
 * @param string $html_body Full HTML body
 * @param string $reply_to  Optional reply-to address
 * @return bool             true on success, false on failure (error logged)
 */
function sendMail(
    string $to,
    string $to_name,
    string $subject,
    string $html_body,
    string $reply_to = ''
): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host     = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;

        if (SMTP_ENCRYPTION === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port     = SMTP_PORT;
        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to, $to_name);
        if ($reply_to) {
            $mail->addReplyTo($reply_to);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        // Plain-text fallback for email clients that don't render HTML
        $mail->AltBody = trim(strip_tags(
            str_replace(['<br>', '<br/>', '<br />', '</p>', '</li>', '</tr>'], "\n", $html_body)
        ));

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('PHPMailer error → ' . $to . ': ' . $mail->ErrorInfo);
        return false;
    }
}
