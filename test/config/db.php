<?php
/**
 * Database Configuration
 * Даам Тэтгэлэг
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u613238646_csca');
define('DB_USER', 'u613238646_csca');
define('DB_PASS', 'Hadesdev12.');
define('DB_CHARSET', 'utf8mb4');

// PDO холболт
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Мэдээллийн сантай холбогдож чадсангүй: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// Session эхлүүлэх
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

