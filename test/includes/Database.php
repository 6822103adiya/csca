<?php
/**
 * Өгөгдлийн сангийн холболт (PDO)
 */

require_once __DIR__ . '/../config/config.php';

class Database {
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];
                
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                if (DEBUG_MODE) {
                    die("Өгөгдлийн сантай холбогдож чадсангүй: " . $e->getMessage());
                } else {
                    die("Өгөгдлийн сантай холбогдож чадсангүй. Дараа дахин оролдоно уу.");
                }
            }
        }
        
        return self::$instance;
    }
    
    // Clone хийхийг хориглох
    private function __clone() {}
    
    // Unserialize хийхийг хориглох
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

