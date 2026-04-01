<?php
require_once __DIR__ . '/util.php';
require_once __DIR__ . '/order_service.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function ah_send_order_confirmation(int $orderId, int $userId): bool
{
    $conn = connect_db();

    // Get order
    $stmt = $conn->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) {
        return false;
    }

    // Get user email and name
    $stmt = $conn->prepare(
        'SELECT u.email, p.fname, p.lname
         FROM phpauth_users u
         LEFT JOIN user_profiles p ON p.user_id = u.id
         WHERE u.id = ? LIMIT 1'
    );
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user || empty($user['email'])) {
        return false;
    }

    $toEmail  = $user['email'];
    $fname    = !empty($user['fname']) ? $user['fname'] : 'there';
    $items    = ah_checkout_get_order_items($orderId);
    $shipping = ah_checkout_decode_shipping_snapshot($order['shipping_snapshot_json'] ?? null);
    $currency = strtoupper($order['currency'] ?? 'USD');

    // Build items table rows
    $itemRows = '';
    foreach ($items as $item) {
        $itemRows .= '
            <tr>
                <td style="padding: 10px 8px; border-bottom: 1px solid #f0ebe5;">' . htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') . '</td>
                <td style="padding: 10px 8px; border-bottom: 1px solid #f0ebe5; text-align: center;">' . (int) $item['quantity'] . '</td>
                <td style="padding: 10px 8px; border-bottom: 1px solid #f0ebe5; text-align: right;">$' . number_format((float) $item['unit_price'], 2) . '</td>
                <td style="padding: 10px 8px; border-bottom: 1px solid #f0ebe5; text-align: right;">$' . number_format((float) $item['line_total'], 2) . '</td>
            </tr>';
    }

    // Build shipping address block
    $shippingLines = array_filter([
        $shipping['full_name'] ?? '',
        $shipping['street'] ?? '',
        trim(($shipping['city'] ?? '') . ' ' . ($shipping['state'] ?? '') . ' ' . ($shipping['zip'] ?? '')),
    ]);
    $shippingHtml = implode('<br>', array_map(fn($l) => htmlspecialchars($l, ENT_QUOTES, 'UTF-8'), $shippingLines));

    $mailFromName = (string) ah_env('MAIL_FROM_NAME', 'Aroma Haven');
    $mailHost = (string) ah_env('MAIL_HOST', '');
    $mailUser = (string) ah_env('MAIL_USER', '');
    $mailPass = (string) ah_env('MAIL_PASS', '');
    $mailPort = (int) ah_env('MAIL_PORT', '587');
    $mailEncryption = strtolower((string) ah_env('MAIL_ENCRYPTION', 'tls'));
    $mailAuthRaw = strtolower((string) ah_env('MAIL_SMTP_AUTH', 'true'));
    $mailAuth = !in_array($mailAuthRaw, ['0', 'false', 'no', 'off'], true);

    if ($mailHost === '' || ($mailAuth && $mailUser === '')) {
        ah_log_error('order_mailer_config_missing', null, ['order_id' => $orderId]);
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host     = $mailHost;
        $mail->SMTPAuth = $mailAuth;
        $mail->Username = $mailUser;
        $mail->Password = $mailPass;
        $mail->Port     = $mailPort > 0 ? $mailPort : 587;

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
        $mail->Subject = 'Your Aroma Haven Order #' . $orderId . ' is confirmed!';
        $mail->Body    = '
        <div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 32px; color: #2c2c2c;">
            <h2 style="margin-bottom: 4px; color: #3b2a1a;">Order Confirmed</h2>
            <p style="color: #888; margin-top: 0;">Order #' . $orderId . '</p>

            <p>Hi ' . htmlspecialchars($fname, ENT_QUOTES, 'UTF-8') . ',</p>
            <p>Thank you for your order! We\'re roasting your beans fresh and will ship within 1-2 working days.</p>

            <table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
                <thead>
                    <tr style="background: #f5f0eb;">
                        <th style="padding: 10px 8px; text-align: left;">Product</th>
                        <th style="padding: 10px 8px; text-align: center;">Qty</th>
                        <th style="padding: 10px 8px; text-align: right;">Unit Price</th>
                        <th style="padding: 10px 8px; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>' . $itemRows . '</tbody>
            </table>

            <table style="width: 100%; max-width: 300px; margin-left: auto; margin-bottom: 24px;">
                <tr>
                    <td style="padding: 4px 0; color: #666;">Subtotal</td>
                    <td style="padding: 4px 0; text-align: right;">$' . number_format((float) $order['subtotal'], 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666;">Shipping</td>
                    <td style="padding: 4px 0; text-align: right;">$' . number_format((float) $order['shipping'], 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #666;">Tax</td>
                    <td style="padding: 4px 0; text-align: right;">$' . number_format((float) $order['tax'], 2) . '</td>
                </tr>
                <tr style="font-weight: bold; font-size: 16px; border-top: 2px solid #3b2a1a;">
                    <td style="padding: 8px 0;">Total</td>
                    <td style="padding: 8px 0; text-align: right;">' . $currency . ' $' . number_format((float) $order['total'], 2) . '</td>
                </tr>
            </table>

            ' . (!empty($shippingHtml) ? '
            <div style="background: #f5f0eb; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <p style="margin: 0 0 8px; font-weight: bold;">Shipping to</p>
                <p style="margin: 0; color: #555; line-height: 1.6;">' . $shippingHtml . '</p>
            </div>' : '') . '

            <p style="color: #999; font-size: 13px;">If you have any questions, reply to this email or visit our contact page.</p>
            <p style="color: #999; font-size: 13px;">— The Aroma Haven Team</p>
        </div>';

        $mail->AltBody = 'Hi ' . $fname . ', your Aroma Haven order #' . $orderId . ' is confirmed! Total: ' . $currency . ' $' . number_format((float) $order['total'], 2) . '.';
        $mail->send();
        return true;
    } catch (Exception $e) {
        ah_log_error('order_mailer_send_failed', $e, [
            'order_id' => $orderId,
            'user_id'  => $userId,
        ]);
        return false;
    }
}