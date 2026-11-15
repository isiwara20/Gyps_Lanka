<?php
// ==============================================================
// GYPS LANKA - Native SMTP Mailer Engine
// ==============================================================

require_once __DIR__ . '/db.php';

if (!class_exists('GypsSMTPMailer')) {
class GypsSMTPMailer {
    private $host;
    private $port;
    private $encryption;
    private $username;
    private $password;
    private $timeout;
    private $socket = null;
    private $lastError = '';
    private $debugLog = [];

    public function __construct($config = []) {
        $this->host       = $config['host']       ?? get_setting('smtp_host', 'mail.gypslanka.com');
        $this->port       = (int)($config['port'] ?? get_setting('smtp_port', '465'));
        $this->encryption = strtolower($config['encryption'] ?? get_setting('smtp_encryption', 'ssl'));
        $this->username   = $config['username']   ?? get_setting('smtp_username', 'support@gypslanka.com');
        $this->password   = $config['password']   ?? get_setting('smtp_password', '');
        $this->timeout    = (int)($config['timeout'] ?? 10);
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function getDebugLog() {
        return $this->debugLog;
    }

    private function log($message) {
        $this->debugLog[] = $message;
        $logFile = __DIR__ . '/../assets/logs/mail.log';
        @file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
    }

    private function readResponse($expectedCode = null) {
        $response = '';
        while ($line = @fgets($this->socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        $this->log("SERVER: " . trim($response));

        if ($expectedCode !== null) {
            $actualCode = (int)substr($response, 0, 3);
            if ($actualCode !== (int)$expectedCode) {
                $this->lastError = "Expected {$expectedCode}, but received: " . trim($response);
                $this->log("ERROR: " . $this->lastError);
                return false;
            }
        }
        return $response;
    }

    private function sendCommand($command, $expectedCode = null) {
        $logCommand = (strpos($command, 'AUTH') !== false || strlen($command) > 40 && base64_decode($command, true)) 
            ? '***[CREDENTIAL]***' 
            : $command;
        $this->log("CLIENT: " . trim($logCommand));

        @fputs($this->socket, $command . "\r\n");
        return $this->readResponse($expectedCode);
    }

    public function send($to, $subject, $htmlBody, $fromEmail = null, $fromName = null, $replyToEmail = null, $replyToName = null) {
        $fromEmail = $fromEmail ?: get_setting('smtp_from_email', 'support@gypslanka.com');
        $fromName  = $fromName  ?: get_setting('smtp_from_name', 'Gyps Lanka Support');

        $this->log("--- Starting SMTP session to send to: {$to} from: {$fromEmail} ---");

        // Determine remote socket address
        $remoteAddress = $this->host . ':' . $this->port;
        if ($this->encryption === 'ssl') {
            $remoteAddress = 'ssl://' . $remoteAddress;
        }

        $context = stream_context_create([
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ]);

        $errno = 0;
        $errstr = '';
        $this->socket = @stream_socket_client(
            $remoteAddress,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$this->socket) {
            $this->lastError = "Could not connect to SMTP host {$remoteAddress}: {$errstr} ({$errno})";
            $this->log("ERROR: " . $this->lastError);
            return false;
        }

        stream_set_timeout($this->socket, $this->timeout);

        // 1. Initial greeting (220)
        if (!$this->readResponse(220)) {
            @fclose($this->socket);
            return false;
        }

        // 2. EHLO
        $clientHost = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
        if (!$this->sendCommand("EHLO {$clientHost}", 250)) {
            if (!$this->sendCommand("HELO {$clientHost}", 250)) {
                @fclose($this->socket);
                return false;
            }
        }

        // 3. STARTTLS if required
        if ($this->encryption === 'tls') {
            if (!$this->sendCommand("STARTTLS", 220)) {
                @fclose($this->socket);
                return false;
            }
            if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->lastError = "Failed to establish TLS encryption with SMTP server.";
                $this->log("ERROR: " . $this->lastError);
                @fclose($this->socket);
                return false;
            }
            // Send EHLO again after TLS handshake
            if (!$this->sendCommand("EHLO {$clientHost}", 250)) {
                @fclose($this->socket);
                return false;
            }
        }

        // 4. Authenticate if username is provided
        if (!empty($this->username)) {
            if (!$this->sendCommand("AUTH LOGIN", 334)) {
                @fclose($this->socket);
                return false;
            }
            if (!$this->sendCommand(base64_encode($this->username), 334)) {
                @fclose($this->socket);
                return false;
            }
            if (!$this->sendCommand(base64_encode($this->password), 235)) {
                @fclose($this->socket);
                return false;
            }
        }

        // 5. Envelope sender & recipient
        if (!$this->sendCommand("MAIL FROM:<{$fromEmail}>", 250)) {
            @fclose($this->socket);
            return false;
        }

        if (!$this->sendCommand("RCPT TO:<{$to}>", 250)) {
            @fclose($this->socket);
            return false;
        }

        // 6. DATA command
        if (!$this->sendCommand("DATA", 354)) {
            @fclose($this->socket);
            return false;
        }

        // 7. Compose Email Headers & Payload
        $headers   = [];
        $headers[] = "Date: " . date('r');
        $headers[] = "To: <{$to}>";
        $headers[] = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>";
        if ($replyToEmail) {
            $replyName = $replyToName ?: $replyToEmail;
            $headers[] = "Reply-To: =?UTF-8?B?" . base64_encode($replyName) . "?= <{$replyToEmail}>";
        }
        $headers[] = "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: 8bit";
        $headers[] = "X-Mailer: GypsLanka-SMTP-Engine/2.0";

        $messageData = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.";

        if (!$this->sendCommand($messageData, 250)) {
            @fclose($this->socket);
            return false;
        }

        // 8. QUIT
        $this->sendCommand("QUIT", 221);
        @fclose($this->socket);

        $this->log("SUCCESS: Email sent successfully to {$to} with subject '{$subject}'");
        return true;
    }
}
}

if (!function_exists('send_contact_notification_email')) {
/**
 * Send Contact Form / Quote Request Notification via SMTP to info@gypslanka.com
 */
function send_contact_notification_email($data) {
    $toEmail   = get_setting('contact_recipient_email', 'info@gypslanka.com');
    $fromEmail = get_setting('smtp_from_email', 'support@gypslanka.com');
    $fromName  = get_setting('smtp_from_name', 'Gyps Lanka Support');

    $name      = htmlspecialchars($data['name'] ?? 'Client');
    $phone     = htmlspecialchars($data['phone'] ?? 'N/A');
    $email     = htmlspecialchars($data['email'] ?? 'Not provided');
    $service   = htmlspecialchars($data['service'] ?? 'General Inquiry');
    $location  = htmlspecialchars($data['location'] ?? 'Sri Lanka');
    $roomSize  = htmlspecialchars($data['room_size'] ?? 'Not specified');
    $message   = nl2br(htmlspecialchars($data['message'] ?? ''));
    $submitted = date('Y-m-d H:i:s');
    $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

    $subject   = "New Inquiry from {$name} - {$service} ({$location})";

    $htmlBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: "Segoe UI", Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 25px; color: #1e293b; }
            .email-container { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
            .email-header { background: #0b1728; padding: 30px 25px; text-align: center; border-bottom: 3px solid #d4af37; }
            .email-header h1 { margin: 0; color: #ffffff; font-size: 24px; letter-spacing: 1px; }
            .email-header h1 span { color: #d4af37; }
            .email-header p { margin: 6px 0 0; color: #94a3b8; font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px; }
            .badge-notice { display: inline-block; background: #fef3c7; color: #92400e; font-weight: 700; font-size: 12px; padding: 6px 14px; border-radius: 50px; margin: 20px 0 10px; border: 1px solid #fde68a; }
            .email-body { padding: 30px 28px; }
            .info-table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 25px; }
            .info-table td { padding: 11px 12px; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
            .info-table td.label { font-weight: 700; color: #64748b; width: 35%; background: #f8fafc; }
            .info-table td.value { font-weight: 600; color: #0f172a; }
            .message-box { background: #f8fafc; border-left: 4px solid #d4af37; padding: 18px; border-radius: 0 8px 8px 0; margin-bottom: 25px; font-size: 14px; line-height: 1.6; color: #334155; }
            .action-bar { text-align: center; margin: 25px 0 10px; }
            .action-btn { display: inline-block; background: #d4af37; color: #0b1728; font-weight: 800; font-size: 14px; padding: 12px 28px; text-decoration: none; border-radius: 6px; box-shadow: 0 4px 14px rgba(212,175,55,0.3); }
            .btn-wa { display: inline-block; background: #25d366; color: #ffffff; font-weight: 700; font-size: 13px; padding: 10px 20px; text-decoration: none; border-radius: 6px; margin-left: 8px; }
            .email-footer { background: #f8fafc; padding: 18px 25px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                <h1>GYPS<span>LANKA</span></h1>
                <p>Ceiling &amp; Interior Solutions - Sri Lanka</p>
            </div>
            <div class="email-body">
                <div style="text-align: center;">
                    <span class="badge-notice">&#9889; New Website Contact Inquiry Received</span>
                    <h2 style="margin: 10px 0 5px; color: #0b1728; font-size: 20px;">' . $name . ' submitted a message</h2>
                    <p style="color: #64748b; font-size: 13px; margin: 0 0 20px;">Please follow up with the client within 24 hours.</p>
                </div>

                <table class="info-table">
                    <tr>
                        <td class="label">Full Name:</td>
                        <td class="value">' . $name . '</td>
                    </tr>
                    <tr>
                        <td class="label">Phone / Mobile:</td>
                        <td class="value">
                            <a href="tel:' . $cleanPhone . '" style="color:#0b1728; text-decoration:none; font-weight:700;">' . $phone . '</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Email Address:</td>
                        <td class="value">
                            <a href="mailto:' . $email . '" style="color:#2563eb; text-decoration:none;">' . $email . '</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Service Requested:</td>
                        <td class="value"><span style="color:#b45309; font-weight:700;">' . $service . '</span></td>
                    </tr>
                    <tr>
                        <td class="label">Location / City:</td>
                        <td class="value">' . $location . '</td>
                    </tr>
                    <tr>
                        <td class="label">Floor / Room Size:</td>
                        <td class="value">' . $roomSize . '</td>
                    </tr>
                    <tr>
                        <td class="label">Date &amp; Time:</td>
                        <td class="value">' . $submitted . '</td>
                    </tr>
                </table>

                <div style="font-weight: 700; color: #0b1728; font-size: 14px; margin-bottom: 8px;">Client Message &amp; Project Requirements:</div>
                <div class="message-box">
                    ' . $message . '
                </div>

                <div class="action-bar">
                    <a href="tel:' . $cleanPhone . '" class="action-btn">&#128222; Call Client Now</a>
                    <a href="https://wa.me/' . preg_replace('/[^0-9]/', '', $cleanPhone) . '" class="btn-wa">&#128172; Chat on WhatsApp</a>
                </div>
            </div>
            <div class="email-footer">
                This inquiry was received from the <strong>Gyps Lanka</strong> website contact form.<br>
                Recipient: ' . $toEmail . ' | Sender: ' . $fromEmail . '
            </div>
        </div>
    </body>
    </html>
    ';

    $replyTo = (!empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) ? $data['email'] : null;

    $mailer = new GypsSMTPMailer();
    $sent = $mailer->send($toEmail, $subject, $htmlBody, $fromEmail, $fromName, $replyTo, $name);

    if (!$sent) {
        // Fallback: Attempt PHP native mail() if SMTP fails
        @mail(
            $toEmail,
            $subject,
            $htmlBody,
            "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: {$fromName} <{$fromEmail}>\r\n" . ($replyTo ? "Reply-To: {$replyTo}\r\n" : "")
        );
    }

    return [
        'success' => $sent,
        'to'      => $toEmail,
        'from'    => $fromEmail,
        'error'   => $mailer->getLastError()
    ];
}
}

if (!function_exists('send_test_smtp_email')) {
/**
 * Send Test Email from Admin Dashboard to test SMTP setup
 */
function send_test_smtp_email($testRecipient = 'info@gypslanka.com') {
    $fromEmail = get_setting('smtp_from_email', 'support@gypslanka.com');
    $fromName  = get_setting('smtp_from_name', 'Gyps Lanka Support');
    $subject   = "Gyps Lanka SMTP Test Email - " . date('Y-m-d H:i:s');

    $html = '
    <div style="font-family: Arial, sans-serif; padding: 25px; background:#f8fafc;">
        <div style="max-width: 550px; margin: 0 auto; background:#fff; padding:30px; border-radius:12px; border:1px solid #e2e8f0;">
            <h2 style="color:#0b1728; margin-top:0;">&#9989; Gyps Lanka SMTP Connection Verified</h2>
            <p style="color:#475569; font-size:14px; line-height:1.6;">
                Congratulations! Your SMTP email engine has connected successfully and is sending emails from 
                <strong>' . htmlspecialchars($fromEmail) . '</strong> to <strong>' . htmlspecialchars($testRecipient) . '</strong>.
            </p>
            <div style="background:#f1f5f9; padding:15px; border-radius:8px; font-size:13px; color:#334155;">
                <strong>SMTP Host:</strong> ' . htmlspecialchars(get_setting('smtp_host', 'mail.gypslanka.com')) . '<br>
                <strong>SMTP Port:</strong> ' . htmlspecialchars(get_setting('smtp_port', '465')) . '<br>
                <strong>Encryption:</strong> ' . htmlspecialchars(get_setting('smtp_encryption', 'ssl')) . '<br>
                <strong>Tested At:</strong> ' . date('Y-m-d H:i:s') . '
            </div>
        </div>
    </div>';

    $mailer = new GypsSMTPMailer();
    $ok = $mailer->send($testRecipient, $subject, $html, $fromEmail, $fromName);

    return [
        'success'  => $ok,
        'error'    => $mailer->getLastError(),
        'debugLog' => $mailer->getDebugLog()
    ];
}
}
