<?php
/**
 * Email verification code sender API
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

if (!validateGmail($email)) {
    echo json_encode(['success' => false, 'error' => 'Зөвхөн @gmail.com хаяг оруулна уу.']);
    exit;
}

$code = generateVerificationCode();

if (sendVerificationEmail($email, $code)) {
    echo json_encode(['success' => true, 'message' => 'Код илгээгдлээ.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Код илгээхэд алдаа гарлаа.']);
}

