<?php
require_once __DIR__ . '/util.php';
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function otp_generate_and_send(int $userId, string $toEmail): bool
{
    $conn = connect_db();
    $conn->prepare("UPDATE otp_codes SET used = 1 WHERE user_id = ? AND used = 0")
         ->execute([$userId]);
    $code      = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $stmt = $conn->prepare(
        "INSERT INTO otp_codes (user_id, code, expires_at) VALUES (?, ?, ?)"
    );
    $stmt->execute([$userId, $code, $expiresAt]);

    $mailHost = (string) ah_env('MAIL_HOST', '');
    $mailUser = (string) ah_env('MAIL_USER', '');
    $mailPass = (string) ah_env('MAIL_PASS', '');
    $mailPort = (int) ah_env('MAIL_PORT', '587');
    $mailFromName = (string) ah_env('MAIL_FROM_NAME', 'Aroma Haven');
    $mailEncryption = strtolower((string) ah_env('MAIL_ENCRYPTION', 'tls'));
    $mailAuthRaw = strtolower((string) ah_env('MAIL_SMTP_AUTH', 'true'));
    $mailAuth = !in_array($mailAuthRaw, ['0', 'false', 'no', 'off'], true);

    if ($mailHost === '' || ($mailAuth && $mailUser === '')) {
        ah_log_error('otp_mail_config_missing', null, [
            'mail_host_present' => $mailHost !== '',
            'mail_user_present' => $mailUser !== '',
            'smtp_auth' => $mailAuth,
        ]);
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $mailHost;
        $mail->SMTPAuth = $mailAuth;
        $mail->Username = $mailUser;
        $mail->Password = $mailPass;
        $mail->Port = $mailPort > 0 ? $mailPort : 587;

        if (in_array($mailEncryption, ['ssl', 'smtps'], true)) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif (in_array($mailEncryption, ['tls', 'starttls'], true)) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        $mail->setFrom($mailUser !== '' ? $mailUser : 'no-reply@localhost', $mailFromName);
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Your Aroma Haven verification code';
        $mail->Body    = '
            <div style="font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 32px;">
                <h2 style="margin-bottom: 8px;">Your verification code</h2>
                <p style="color: #666; margin-bottom: 24px;">Enter this code to complete your sign-in. It expires in 10 minutes.</p>
                <div style="font-size: 36px; font-weight: bold; letter-spacing: 8px; text-align: center; padding: 24px; background: #f5f0eb; border-radius: 8px; margin-bottom: 24px;">
                    ' . $code . '
                </div>
                <p style="color: #999; font-size: 13px;">If you did not request this, you can safely ignore this email.</p>
            </div>
        ';
        $mail->AltBody = 'Your Aroma Haven verification code is: ' . $code . '. It expires in 10 minutes.';
        $mail->send();
        return true;
    } catch (Exception $e) {
        ah_log_error('otp_mail_send_failed', $e, [
            'user_id' => $userId,
            'recipient' => $toEmail,
        ]);
        return false;
    }
}
function otp_verify(int $userId, string $code): bool
{
    $conn = connect_db();
    $stmt = $conn->prepare(
        "SELECT id FROM otp_codes
         WHERE user_id = ? AND code = ? AND used = 0 AND expires_at > NOW()
         LIMIT 1"
    );
    $stmt->execute([$userId, $code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return false;
    }
    $conn->prepare("UPDATE otp_codes SET used = 1 WHERE id = ?")
         ->execute([$row['id']]);
    return true;
}
