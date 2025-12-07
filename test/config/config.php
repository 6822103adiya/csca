<?php
/**
 * Тэтгэлэг Сэсэн - Тохиргооны файл
 */

// Алдааг харуулах (production-д false болгоно)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Өгөгдлийн сангийн тохиргоо
define('DB_HOST', 'localhost');
define('DB_NAME', 'u613238646_csca');
define('DB_USER', 'u613238646_csca');
define('DB_PASS', 'Hadesdev12.');
define('DB_CHARSET', 'utf8mb4');

// Сайтын тохиргоо (динамик DOMAIN + SUBDIR)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($scriptDir === '/' || $scriptDir === '\\') {
    $scriptDir = '';
}
define('SITE_NAME', 'Тэтгэлэг Сэсэн');
define('SITE_URL', $scheme . '://' . $host . $scriptDir);
define('SITE_EMAIL', '6822103@gmail.com');

// Файлын тохиргоо
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('ALLOWED_DOCUMENT_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Имэйл тохиргоо (SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '6822103@gmail.com');
define('SMTP_PASSWORD', 'ysqm hsmy zijy wzii');
define('SMTP_FROM_NAME', 'Admin');
define('SMTP_FROM_EMAIL', '6822103@gmail.com');

// Баталгаажуулах код
define('VERIFICATION_CODE_LENGTH', 6);
define('VERIFICATION_CODE_EXPIRY', 10); // минут

// Session тохиргоо
define('SESSION_LIFETIME', 3600); // 1 цаг

// CSRF токен нэр
define('CSRF_TOKEN_NAME', 'csrf_token');

// Улсуудын жагсаалт
define('COUNTRIES', [
    'china' => ['name' => 'Хятад', 'flag' => '🇨🇳'],
    'korea' => ['name' => 'Солонгос', 'flag' => '🇰🇷'],
    'germany' => ['name' => 'Герман', 'flag' => '🇩🇪'],
    'russia' => ['name' => 'Орос', 'flag' => '🇷🇺']
]);

// Ангиудын жагсаалт
define('GRADES', [
    1 => '1-р анги',
    2 => '2-р анги',
    3 => '3-р анги',
    4 => '4-р анги',
    5 => '5-р анги',
    6 => '6-р анги',
    7 => '7-р анги',
    8 => '8-р анги',
    9 => '9-р анги',
    10 => '10-р анги',
    11 => '11-р анги',
    12 => '12-р анги'
]);

// Хамгийн их сонгож болох улсын тоо
define('MAX_COUNTRIES', 3);

// Timezone
date_default_timezone_set('Asia/Ulaanbaatar');

// Session эхлүүлэх
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

