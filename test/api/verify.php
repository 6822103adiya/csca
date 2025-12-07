<?php
/**
 * Тэтгэлэг Сэсэн - Email Verification API
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

try {
    $db = Database::getInstance();
    
    $registrationId = $_POST['registration_id'] ?? ($_SESSION['pending_registration_id'] ?? null);
    $code = trim($_POST['code'] ?? '');
    
    if (!$registrationId || !$code) {
        jsonResponse(['success' => false, 'message' => 'Бүртгэлийн мэдээлэл олдсонгүй'], 400);
    }
    
    // Get registration
    $stmt = $db->prepare("
        SELECT id, email, email_verification_code, email_verification_expires, status 
        FROM registrations 
        WHERE id = ?
    ");
    $stmt->execute([$registrationId]);
    $registration = $stmt->fetch();
    
    if (!$registration) {
        jsonResponse(['success' => false, 'message' => 'Бүртгэл олдсонгүй'], 404);
    }
    
    if ($registration['status'] !== 'pending_verification') {
        jsonResponse(['success' => false, 'message' => 'Бүртгэл аль хэдийн баталгаажсан'], 400);
    }
    
    // Check code expiry
    if (strtotime($registration['email_verification_expires']) < time()) {
        jsonResponse(['success' => false, 'message' => 'Баталгаажуулах кодын хугацаа дууссан'], 400);
    }
    
    // Verify code
    if ($registration['email_verification_code'] !== $code) {
        jsonResponse(['success' => false, 'message' => 'Баталгаажуулах код буруу байна'], 400);
    }
    
    // Update registration status
    $stmt = $db->prepare("
        UPDATE registrations 
        SET email_verified = 1, status = 'pending_payment' 
        WHERE id = ?
    ");
    $stmt->execute([$registrationId]);
    
    // Clear session
    unset($_SESSION['pending_registration_id']);
    
    jsonResponse([
        'success' => true,
        'message' => 'Имэйл амжилттай баталгаажлаа'
    ]);
    
} catch (PDOException $e) {
    error_log("Verification error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Системийн алдаа гарлаа'], 500);
}

