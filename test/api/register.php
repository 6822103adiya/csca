<?php
/**
 * Тэтгэлэг Сэсэн - Registration API
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
}

try {
    $db = Database::getInstance();
    
    // Validate required fields
    $errors = [];
    
    // Personal info
    $lastName = trim($_POST['last_name'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $registerNumber = trim($_POST['register_number'] ?? '');
    $homeAddress = trim($_POST['home_address'] ?? '');
    $school = trim($_POST['school'] ?? '');
    $grade = (int)($_POST['grade'] ?? 0);
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $languageScore = trim($_POST['language_score'] ?? '');
    $additionalRequest = trim($_POST['additional_request'] ?? '');
    $countries = $_POST['countries'] ?? [];
    
    // Validation
    if (empty($lastName)) {
        $errors['last_name'] = 'Овог оруулна уу';
    }
    
    if (empty($firstName)) {
        $errors['first_name'] = 'Нэр оруулна уу';
    }
    
    if (!validateRegisterNumber($registerNumber)) {
        $errors['register_number'] = 'Регистерийн дугаар буруу форматтай — эхний 2 тэмдэг кирилл үсэг + 8 тоо байх ёстой';
    }
    
    if (empty($homeAddress)) {
        $errors['home_address'] = 'Гэрийн хаяг оруулна уу';
    }
    
    if (empty($school)) {
        $errors['school'] = 'Сургууль оруулна уу';
    }
    
    if ($grade < 1 || $grade > 12) {
        $errors['grade'] = 'Анги сонгоно уу';
    }
    
    if (!validatePhone($phone)) {
        $errors['phone'] = 'Утасны дугаар 8 оронтой байх ёстой';
    }
    
    if (!validateGmail($email)) {
        $errors['email'] = 'Зөвхөн Gmail хаяг л зөвшөөрнө';
    }
    
    if (empty($countries) || !is_array($countries)) {
        $errors['countries'] = 'Дор хаяж нэг улс сонгоно уу';
    } elseif (count($countries) > 3) {
        $errors['countries'] = 'Гурваас илүү улс сонгож болохгүй';
    }
    
    // Check if email or register number already exists
    $stmt = $db->prepare("SELECT id FROM registrations WHERE email = ? OR register_number = ?");
    $stmt->execute([$email, $registerNumber]);
    if ($stmt->fetch()) {
        $errors['email'] = 'Энэ имэйл эсвэл регистерийн дугаар аль хэдийн бүртгэгдсэн байна';
    }
    
    // File validation
    $allowedImageTypes = ALLOWED_IMAGE_TYPES;
    $allowedDocTypes = ALLOWED_DOCUMENT_TYPES;
    
    // ID Front
    if (!isset($_FILES['id_front']) || $_FILES['id_front']['error'] !== UPLOAD_ERR_OK) {
        $errors['id_front'] = 'Иргэний үнэмлэхний урд талын зураг оруулна уу';
    } elseif (!validateFileType($_FILES['id_front'], $allowedImageTypes) || !validateFileSize($_FILES['id_front'])) {
        $errors['id_front'] = 'Зургийн формат буруу эсвэл хэмжээ 5MB-аас хэтэрсэн';
    }
    
    // ID Back
    if (!isset($_FILES['id_back']) || $_FILES['id_back']['error'] !== UPLOAD_ERR_OK) {
        $errors['id_back'] = 'Иргэний үнэмлэхний ард талын зураг оруулна уу';
    } elseif (!validateFileType($_FILES['id_back'], $allowedImageTypes) || !validateFileSize($_FILES['id_back'])) {
        $errors['id_back'] = 'Зургийн формат буруу эсвэл хэмжээ 5MB-аас хэтэрсэн';
    }
    
    // ID Selfie
    if (!isset($_FILES['id_selfie']) || $_FILES['id_selfie']['error'] !== UPLOAD_ERR_OK) {
        $errors['id_selfie'] = 'Иргэний үнэмлэхтэй selfie зураг оруулна уу';
    } elseif (!validateFileType($_FILES['id_selfie'], $allowedImageTypes) || !validateFileSize($_FILES['id_selfie'])) {
        $errors['id_selfie'] = 'Зургийн формат буруу эсвэл хэмжээ 5MB-аас хэтэрсэн';
    }
    
    // Certificate (required if language score is provided)
    $certificatePath = null;
    if (!empty($languageScore)) {
        if (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] !== UPLOAD_ERR_OK) {
            $errors['certificate'] = 'Хэлний оноотой бол сертификат оруулна уу';
        } elseif (!validateFileType($_FILES['certificate'], $allowedDocTypes) || !validateFileSize($_FILES['certificate'])) {
            $errors['certificate'] = 'Файлын формат буруу эсвэл хэмжээ 5MB-аас хэтэрсэн';
        }
    }
    
    // Terms
    if (!isset($_POST['terms_accepted']) || $_POST['terms_accepted'] !== 'on') {
        $errors['terms'] = 'Үйлчилгээний нөхцлийг зөвшөөрнө үү';
    }
    
    // Return errors if any
    if (!empty($errors)) {
        jsonResponse([
            'success' => false,
            'message' => 'Бүртгэлийн мэдээлэл дутуу байна',
            'errors' => $errors
        ], 400);
    }
    
    // Upload files
    $idFrontPath = uploadFile($_FILES['id_front'], 'id');
    $idBackPath = uploadFile($_FILES['id_back'], 'id');
    $idSelfiePath = uploadFile($_FILES['id_selfie'], 'id');
    
    if (!empty($languageScore) && isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
        $certificatePath = uploadFile($_FILES['certificate'], 'certificates');
    }
    
    // Generate verification code
    $verificationCode = generateVerificationCode();
    $verificationExpires = date('Y-m-d H:i:s', strtotime('+' . VERIFICATION_CODE_EXPIRY . ' minutes'));
    
    // Insert registration
    $stmt = $db->prepare("
        INSERT INTO registrations (
            last_name, first_name, register_number, home_address, school, grade,
            phone, email, language_score, selected_countries, additional_request,
            id_front, id_back, id_selfie, certificate_file,
            email_verification_code, email_verification_expires, terms_accepted,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'pending_verification')
    ");
    
    $stmt->execute([
        $lastName,
        $firstName,
        $registerNumber,
        $homeAddress,
        $school,
        $grade,
        $phone,
        $email,
        $languageScore ?: null,
        json_encode($countries),
        $additionalRequest ?: null,
        $idFrontPath,
        $idBackPath,
        $idSelfiePath,
        $certificatePath,
        $verificationCode,
        $verificationExpires
    ]);
    
    $registrationId = $db->lastInsertId();
    
    // Send verification email
    sendVerificationEmail($email, $verificationCode);
    
    // Store registration ID in session for verification step
    $_SESSION['pending_registration_id'] = $registrationId;
    
    jsonResponse([
        'success' => true,
        'message' => 'Бүртгэл амжилттай. Баталгаажуулах код Gmail рүү илгээгдлээ.',
        'registration_id' => $registrationId
    ]);
    
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Системийн алдаа гарлаа. Дахин оролдоно уу.'
    ], 500);
}

