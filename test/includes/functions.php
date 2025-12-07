<?php
/**
 * Тэтгэлэг Сэсэн - Туслах функцүүд
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

/**
 * CSRF токен үүсгэх
 */
function generateCSRFToken(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * CSRF токен шалгах
 */
function validateCSRFToken(string $token): bool {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * XSS-ээс хамгаалах
 */
function escape(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Регистерийн дугаар шалгах (2 кирилл үсэг + 8 тоо)
 */
function validateRegisterNumber(string $number): bool {
    // Кирилл үсэг + 8 тоо
    $pattern = '/^[А-Яа-яЁёӨөҮү]{2}\d{8}$/u';
    return preg_match($pattern, $number) === 1;
}

/**
 * Утасны дугаар шалгах (8 оронтой)
 */
function validatePhone(string $phone): bool {
    return preg_match('/^\d{8}$/', $phone) === 1;
}

/**
 * Gmail хаяг шалгах
 */
function validateGmail(string $email): bool {
    $pattern = '/^[a-zA-Z0-9._%+-]+@gmail\.com$/i';
    return preg_match($pattern, $email) === 1;
}

/**
 * Файлын төрөл шалгах
 */
function validateFileType(array $file, array $allowedTypes): bool {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    // MIME type шалгах
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return false;
    }
    
    // Extension шалгах
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return false;
    }
    
    return true;
}

/**
 * Файлын хэмжээ шалгах
 */
function validateFileSize(array $file): bool {
    return isset($file['size']) && $file['size'] <= MAX_FILE_SIZE;
}

/**
 * Файл upload хийх
 */
function uploadFile(array $file, string $directory): ?string {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return null;
    }
    
    $uploadDir = UPLOAD_PATH . $directory . '/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $directory . '/' . $filename;
    }
    
    return null;
}

/**
 * Санамсаргүй баталгаажуулах код үүсгэх
 */
function generateVerificationCode(): string {
    return str_pad(random_int(0, 999999), VERIFICATION_CODE_LENGTH, '0', STR_PAD_LEFT);
}

/**
 * Имэйл илгээх (SMTP)
 */
function sendEmail(string $to, string $subject, string $body): bool {
    // Энэ хэсэгт PHPMailer эсвэл native mail() ашиглана
    // Одоохондоо queue-д нэмнэ
    
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO email_queue (to_email, subject, body) VALUES (?, ?, ?)");
        $stmt->execute([$to, $subject, $body]);
        return true;
    } catch (PDOException $e) {
        error_log("Email queue error: " . $e->getMessage());
        return false;
    }
}

/**
 * Баталгаажуулах имэйл илгээх
 */
function sendVerificationEmail(string $email, string $code): bool {
    $subject = "Тэтгэлэг Сэсэн - Баталгаажуулах код";
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .code { font-size: 32px; font-weight: bold; color: #FFA500; letter-spacing: 5px; }
            .footer { margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Тэтгэлэг Сэсэн</h2>
            <p>Таны баталгаажуулах код:</p>
            <p class='code'>{$code}</p>
            <p>Энэ код " . VERIFICATION_CODE_EXPIRY . " минут хүчинтэй.</p>
            <div class='footer'>
                <p>Хэрэв та бүртгүүлээгүй бол энэ имэйлийг үл тоомсорлоно уу.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

/**
 * Баталгаажуулсан имэйл илгээх
 */
function sendApprovalEmail(string $email, string $name): bool {
    $subject = "Тэтгэлэг Сэсэн - Бүртгэл баталгаажлаа";
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .success { color: #28a745; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Тэтгэлэг Сэсэн</h2>
            <p>Сайн байна уу, <strong>{$name}</strong>!</p>
            <p class='success'>Таны бүртгэлийг амжилттай баталгаажууллаа!</p>
            <p>Бидэнтэй хамтарсанд баярлалаа. Удахгүй тантай холбогдох болно.</p>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

/**
 * Татгалзсан имэйл илгээх
 */
function sendRejectionEmail(string $email, string $name, string $reason): bool {
    $subject = "Тэтгэлэг Сэсэн - Бүртгэлийн мэдэгдэл";
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .reason { background: #f8f9fa; padding: 15px; border-left: 4px solid #dc3545; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Тэтгэлэг Сэсэн</h2>
            <p>Сайн байна уу, <strong>{$name}</strong>!</p>
            <p>Уучлаарай, таны бүртгэл дараах шалтгааны улмаас баталгаажаагүй:</p>
            <div class='reason'>{$reason}</div>
            <p>Асуулт байвал бидэнтэй холбогдоно уу.</p>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

/**
 * Тохиргоо авах
 */
function getSetting(string $key): ?string {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Сайтын агуулга авах
 */
function getPageContent(string $slug): ?array {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM site_content WHERE page_slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * JSON хариу илгээх
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Redirect хийх
 */
function redirect(string $url): void {
    header("Location: " . $url);
    exit;
}

/**
 * Огноог Монгол хэл рүү хөрвүүлэх
 */
function formatDateMongolian(string $date): string {
    $months = [
        1 => '1-р сар', 2 => '2-р сар', 3 => '3-р сар', 4 => '4-р сар',
        5 => '5-р сар', 6 => '6-р сар', 7 => '7-р сар', 8 => '8-р сар',
        9 => '9-р сар', 10 => '10-р сар', 11 => '11-р сар', 12 => '12-р сар'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "{$year} оны {$month}-ын {$day}";
}

/**
 * Лог бичих
 */
function logActivity(?int $adminId, string $action, string $description = ''): void {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (admin_id, action, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $adminId,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (PDOException $e) {
        error_log("Log activity error: " . $e->getMessage());
    }
}

/**
 * Flash message тохируулах
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Flash message авах
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

