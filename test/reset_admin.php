<?php
/**
 * Admin нууц үг шинэчлэх скрипт
 * Нэг удаа ажиллуулаад устгана уу!
 */

require_once 'config/config.php';

echo "<html><head><meta charset='UTF-8'><title>Admin Reset</title></head><body style='font-family: Arial; padding: 40px; background: #1a1a1a; color: #fff;'>";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p style='color: #22c55e;'>✅ Database холболт амжилттай!</p>";
    
    // admins хүснэгт байгаа эсэхийг шалгах
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'admins'")->rowCount();
    
    if ($tableCheck == 0) {
        // Хүснэгт үүсгэх
        $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "<p style='color: #ffa500;'>📋 admins хүснэгт үүсгэгдлээ.</p>";
    }
    
    // Шинэ мэдээлэл
    $newUsername = 'daamworld';
    $newPassword = 'Daamworldllc123$';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Хуучин admin-уудыг устгах
    $pdo->exec("DELETE FROM admins");
    
    // Шинэ admin үүсгэх
    $stmt = $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$newUsername, $hashedPassword, 'admin@daamworld.com']);
    
    echo "<h1 style='color: #22c55e;'>✅ Амжилттай!</h1>";
    echo "<p>Admin хэрэглэгч шинэчлэгдлээ.</p>";
    
    echo "<hr style='border-color: #333; margin: 20px 0;'>";
    echo "<h2 style='color: #ffa500;'>🔐 Шинэ нэвтрэх мэдээлэл:</h2>";
    echo "<div style='background: #2a2a2a; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<p><strong>Нэвтрэх нэр:</strong> <code style='background: #333; padding: 5px 10px; border-radius: 5px;'>daamworld</code></p>";
    echo "<p><strong>Нууц үг:</strong> <code style='background: #333; padding: 5px 10px; border-radius: 5px;'>Daamworldllc123$</code></p>";
    echo "</div>";
    echo "<p style='color: #ef4444;'><strong>⚠️ Анхааруулга:</strong> Энэ файлыг одоо ЗААВАЛ устгана уу! (reset_admin.php)</p>";
    echo "<br>";
    echo "<a href='/admin/login.php' style='background: #ffa500; color: #000; padding: 15px 30px; text-decoration: none; border-radius: 10px; font-weight: bold;'>👉 Админ панель руу очих</a>";
    
} catch (PDOException $e) {
    echo "<h1 style='color: #ef4444;'>❌ Алдаа!</h1>";
    echo "<p style='color: #ef4444;'>" . $e->getMessage() . "</p>";
}

echo "</body></html>";
