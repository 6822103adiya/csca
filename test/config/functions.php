<?php
/**
 * Helper Functions
 * Даам Тэтгэлэг
 */

require_once __DIR__ . '/db.php';

// Сайтын тохиргоо авах
function getSetting($key, $default = '') {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

// Тохиргоо хадгалах
function saveSetting($key, $value) {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE setting_value = ?");
    return $stmt->execute([$key, $value, $value]);
}

// Регистрийн дугаар шалгах (2 кирилл үсэг + 8 тоо)
function validateRegisterNumber($number) {
    // Кирилл үсгүүд
    $pattern = '/^[А-ЯӨҮЁ]{2}[0-9]{8}$/u';
    return preg_match($pattern, mb_strtoupper($number, 'UTF-8'));
}

// Утасны дугаар шалгах (8 оронтой)
function validatePhone($phone) {
    return preg_match('/^[0-9]{8}$/', $phone);
}

// Gmail шалгах
function validateGmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return preg_match('/@gmail\.com$/i', $email);
}

// Анги шалгах (11 эсвэл 12)
function validateGrade($grade) {
    return in_array((int)$grade, [11, 12]);
}

// Файл upload хийх
function uploadFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
    $uploadDir = __DIR__ . '/../uploads/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Файлын төрөл шалгах
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'error' => 'Зөвхөн JPG, PNG, PDF файл оруулна уу.'];
    }
    
    // Файлын хэмжээ шалгах (5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Файлын хэмжээ 5MB-ээс хэтэрч байна.'];
    }
    
    // Шинэ нэр үүсгэх
    $newName = uniqid() . '_' . time() . '.' . $ext;
    $destination = $uploadDir . $newName;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $newName];
    }
    
    return ['success' => false, 'error' => 'Файл хадгалахад алдаа гарлаа.'];
}

// Баталгаажуулах код үүсгэх
function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Email илгээх (PHPMailer эсвэл mail() функц)
function sendVerificationEmail($email, $code) {
    $subject = "Даам Тэтгэлэг - Баталгаажуулах код";
    $message = "Таны баталгаажуулах код: $code\n\nЭнэ код 10 минутын дотор хүчинтэй.";
    $headers = "From: noreply@daam.mn\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Код хадгалах
    $pdo = getDB();
    $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $stmt = $pdo->prepare("INSERT INTO email_verifications (email, code, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $code, $expires]);
    
    // Email илгээх (production дээр PHPMailer ашиглана)
    // return mail($email, $subject, $message, $headers);
    return true; // Development mode
}

// Код шалгах
function verifyEmailCode($email, $code) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM email_verifications 
                          WHERE email = ? AND code = ? AND expires_at > NOW() AND used = 0 
                          ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email, $code]);
    $result = $stmt->fetch();
    
    if ($result) {
        // Код ашигласан гэж тэмдэглэх
        $update = $pdo->prepare("UPDATE email_verifications SET used = 1 WHERE id = ?");
        $update->execute([$result['id']]);
        return true;
    }
    
    return false;
}

// XSS халдлагаас хамгаалах
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Админ нэвтэрсэн эсэх
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Админ шалгах
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

// Flash message
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Улсуудыг JSON-оор хадгалах/авах
function getCountriesData() {
    $data = getSetting('countries_data', '');
    if (empty($data)) {
        // Анхны утга
        return [
            ['code' => 'china', 'name' => 'Хятад', 'flag' => '🇨🇳', 'description' => 'Хятад улсад жил бүр 500+ оюутан элсдэг.', 'tuition' => 'Сургалтын төлбөр 100%', 'accommodation' => 'Дотуур байр үнэгүй', 'language' => '1 жил хэлний бэлтгэл', 'duration' => '4-6 жил', 'is_active' => 1],
            ['code' => 'korea', 'name' => 'Солонгос', 'flag' => '🇰🇷', 'description' => 'Солонгос улсын шилдэг их сургуулиуд.', 'tuition' => 'Сургалтын төлбөр 50-100%', 'accommodation' => 'Сар бүр тэтгэмж', 'language' => 'TOPIK бэлтгэл', 'duration' => '4 жил', 'is_active' => 1],
            ['code' => 'germany', 'name' => 'Герман', 'flag' => '🇩🇪', 'description' => 'Герман улсад үнэ төлбөргүй дээд боловсрол.', 'tuition' => 'Үнэ төлбөргүй', 'accommodation' => 'Эрүүл мэндийн даатгал', 'language' => 'Герман хэлний бэлтгэл', 'duration' => '3-5 жил', 'is_active' => 1],
            ['code' => 'russia', 'name' => 'Орос', 'flag' => '🇷🇺', 'description' => 'Орос улсын түүхэн их сургуулиуд.', 'tuition' => 'Засгийн газрын тэтгэлэг', 'accommodation' => 'Дотуур байр багтсан', 'language' => 'Орос хэлний бэлтгэл', 'duration' => '4-6 жил', 'is_active' => 1],
        ];
    }
    return json_decode($data, true) ?: [];
}

function saveCountriesData($countries) {
    return saveSetting('countries_data', json_encode($countries, JSON_UNESCAPED_UNICODE));
}

// Улсын нэр авах
function getCountryName($code) {
    $countries = getCountriesData();
    foreach ($countries as $c) {
        if ($c['code'] === $code) {
            return $c['name'];
        }
    }
    return $code;
}

// Идэвхтэй улсуудыг авах
function getActiveCountries() {
    $countries = getCountriesData();
    return array_filter($countries, function($c) {
        return isset($c['is_active']) && $c['is_active'] == 1;
    });
}

// Бүх улсуудыг авах
function getAllCountries() {
    return getCountriesData();
}

// Email илгээх функц (PHPMailer ашиглана)
function sendEmail($to, $subject, $body) {
    // PHPMailer ашиглах бол
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
            $mail->Password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $mail->CharSet = 'UTF-8';
            
            $mail->setFrom(defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : SMTP_USERNAME, defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Даам Тэтгэлэг');
            $mail->addAddress($to);
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email илгээхэд алдаа: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    // PHP mail() функц ашиглах (fallback)
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Даам Тэтгэлэг <noreply@daam.mn>\r\n";
    
    return @mail($to, $subject, $body, $headers);
}

// Баталгаажуулсан email илгээх
function sendApprovalEmail($user) {
    $countries = json_decode($user['countries'], true);
    $countryNames = array_map('getCountryName', $countries);
    
    $subject = "✅ Таны бүртгэл баталгаажлаа - Даам Тэтгэлэг";
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
        <div style='background: linear-gradient(135deg, #FFA500, #CC8400); padding: 30px; border-radius: 15px 15px 0 0; text-align: center;'>
            <h1 style='color: #fff; margin: 0;'>🎓 Даам Тэтгэлэг</h1>
        </div>
        <div style='background: #1a1a1a; color: #fff; padding: 30px; border-radius: 0 0 15px 15px;'>
            <h2 style='color: #22c55e;'>✅ Баталгаажлаа!</h2>
            <p>Сайн байна уу, <strong>{$user['first_name']}</strong>!</p>
            <p>Таны тэтгэлэгт бүртгүүлэх хүсэлт амжилттай баталгаажлаа.</p>
            
            <div style='background: #2a2a2a; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                <h3 style='color: #FFA500; margin-top: 0;'>📋 Таны мэдээлэл:</h3>
                <p><strong>Нэр:</strong> {$user['last_name']} {$user['first_name']}</p>
                <p><strong>Сонгосон улс:</strong> " . implode(', ', $countryNames) . "</p>
                <p><strong>Регистр:</strong> {$user['register_number']}</p>
            </div>
            
            <p>Бид тантай удахгүй холбогдох болно. Асуулт байвал бидэнтэй холбогдоорой.</p>
            
            <p style='color: #888; font-size: 14px; margin-top: 30px;'>
                Хүндэтгэсэн,<br>
                <strong style='color: #FFA500;'>Даам Тэтгэлэг</strong> баг
            </p>
        </div>
    </div>
    ";
    
    return sendEmail($user['email'], $subject, $body);
}

// Татгалзсан email илгээх
function sendRejectionEmail($user, $reason = '') {
    $subject = "❌ Таны бүртгэлийн хүсэлт - Даам Тэтгэлэг";
    
    $reasonText = $reason ? "<p><strong>Шалтгаан:</strong> {$reason}</p>" : "";
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
        <div style='background: linear-gradient(135deg, #FFA500, #CC8400); padding: 30px; border-radius: 15px 15px 0 0; text-align: center;'>
            <h1 style='color: #fff; margin: 0;'>🎓 Даам Тэтгэлэг</h1>
        </div>
        <div style='background: #1a1a1a; color: #fff; padding: 30px; border-radius: 0 0 15px 15px;'>
            <h2 style='color: #ef4444;'>Уучлаарай</h2>
            <p>Сайн байна у|, <strong>{$user['first_name']}</strong>!</p>
            <p>Таны тэтгэлэгт бүртгүүлэх хүсэлт энэ удаад хүлээн авагдсангүй.</p>
            
            {$reasonText}
            
            <div style='background: #2a2a2a; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                <p>Та дахин бүртгүүлэх боломжтой. Асуулт байвал бидэнтэй холбогдоорой.</p>
            </div>
            
            <p style='color: #888; font-size: 14px; margin-top: 30px;'>
                Хүндэтгэсэн,<br>
                <strong style='color: #FFA500;'>Даам Тэтгэлэг</strong> баг
            </p>
        </div>
    </div>
    ";
    
    return sendEmail($user['email'], $subject, $body);
}

