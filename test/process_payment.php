<?php
/**
 * Payment confirmation handler
 */
require_once 'config/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registrationId = (int)($_POST['registration_id'] ?? 0);
    
    if ($registrationId > 0) {
        $pdo = getDB();
        $stmt = $pdo->prepare("UPDATE registrations SET payment_status = 'paid', payment_date = NOW() WHERE id = ?");
        $stmt->execute([$registrationId]);
    }
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Төлбөр баталгаажлаа - Даам Тэтгэлэг</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div class="card text-center" style="max-width: 500px;">
            <div style="font-size: 5rem; margin-bottom: 1rem;">✅</div>
            <h1 style="color: var(--primary);">Баярлалаа!</h1>
            <p style="font-size: 1.1rem; margin-bottom: 2rem;">
                Таны бүртгэл болон гүйлгээний мэдээлэл хүлээн авлаа.<br>
                Админ 24 цагийн дотор таны бүртгэлийг шалгаж баталгаажуулна.
            </p>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">
                Баталгаажуулалтын мэдээллийг Gmail хаяг руу тань илгээх болно.
            </p>
            <a href="/" class="btn btn-primary">Нүүр хуудас руу буцах</a>
        </div>
    </div>
</body>
</html>

