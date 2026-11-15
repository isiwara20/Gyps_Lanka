<?php
// ==============================================================
// GYPS LANKA - Process Contact & Quote Inquiry
// Sends Email via SMTP (support@gypslanka.com -> info@gypslanka.com)
// ==============================================================

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/functions.php';

$isAjax = (
    isset($_POST['ajax']) && $_POST['ajax'] == '1' ||
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
    (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = sanitize($_POST['name'] ?? '');
    $phone     = sanitize($_POST['phone'] ?? '');
    $email     = sanitize($_POST['email'] ?? '');
    $service   = sanitize($_POST['service'] ?? 'General Inquiry');
    $location  = sanitize($_POST['location'] ?? 'Sri Lanka');
    $room_size = sanitize($_POST['room_size'] ?? '');
    $message   = sanitize($_POST['message'] ?? '');

    $redirect = $_SERVER['HTTP_REFERER'] ?? 'contact.php';

    // Validate required fields
    if (empty($name) || empty($phone) || empty($message)) {
        $errorMsg = 'Please fill in all required fields (Name, Phone number, and Project Details).';
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error'   => $errorMsg
            ]);
            exit;
        }
        set_flash('error', $errorMsg);
        header("Location: " . $redirect);
        exit;
    }

    $db = get_db();
    $inquiryId = null;

    if ($db) {
        try {
            $stmt = $db->prepare("
                INSERT INTO inquiries (name, phone, email, service, room_size, location, message, status)
                VALUES (:name, :phone, :email, :service, :room_size, :location, :message, 'new')
            ");
            $stmt->execute([
                ':name'      => $name,
                ':phone'     => $phone,
                ':email'     => $email,
                ':service'   => $service,
                ':room_size' => $room_size,
                ':location'  => $location,
                ':message'   => $message,
            ]);
            $inquiryId = (int)$db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Inquiry DB insert error: " . $e->getMessage());
        }
    }

    // Dispatch Email Notification via SMTP
    // From: support@gypslanka.com -> To: info@gypslanka.com
    $emailResult = send_contact_notification_email([
        'id'        => $inquiryId,
        'name'      => $name,
        'phone'     => $phone,
        'email'     => $email,
        'service'   => $service,
        'room_size' => $room_size,
        'location'  => $location,
        'message'   => $message,
    ]);

    $successMsg = "Thank you, {$name}! Your message has been received. An engineer from Gyps Lanka will reach out to you within 24 hours.";

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success'      => true,
            'title'        => 'Message Sent Successfully!',
            'message'      => $successMsg,
            'client_name'  => $name,
            'client_phone' => $phone,
            'service'      => $service,
            'location'     => $location,
            'email_sent'   => $emailResult['success']
        ]);
        exit;
    }

    // Non-AJAX fallback: save overlay payload to session and redirect
    $_SESSION['contact_success_overlay'] = [
        'name'    => $name,
        'phone'   => $phone,
        'service' => $service,
        'location'=> $location,
        'message' => $successMsg
    ];

    set_flash('success', $successMsg);
    $path = !empty($urlParts['path']) ? $urlParts['path'] : 'contact.php';
    $targetUrl = $path . (strpos($path, '?') !== false ? '&sent=1' : '?sent=1');
    header("Location: " . $targetUrl);
    exit;
} else {
    header("Location: contact.php");
    exit;
}
