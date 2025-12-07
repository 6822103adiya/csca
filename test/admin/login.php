<?php
require_once __DIR__ . '/../config/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: /admin/index.php');
            exit;
        } else {
            $error = 'Нэвтрэх нэр эсвэл нууц үг буруу байна.';
        }
    } else {
        $error = 'Бүх талбарыг бөглөнө үү.';
    }
}

if (isAdminLoggedIn()) {
    header('Location: /admin/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ нэвтрэх - Даам Тэтгэлэг</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: 
                radial-gradient(ellipse at 30% 20%, var(--primary-glow) 0%, transparent 50%),
                var(--bg-dark);
        }
        .login-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .login-logo h1 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-box">
            <div class="login-logo">
                <div class="icon">🔐</div>
                <h1>Админ панель</h1>
                <p style="color: var(--text-secondary);">Нэвтрэх</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                ❌ <?= $error ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Нэвтрэх нэр</label>
                    <input type="text" class="form-input" name="username" required 
                           placeholder="admin" value="<?= clean($_POST['username'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Нууц үг</label>
                    <input type="password" class="form-input" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">Нэвтрэх</button>
            </form>
            
            <p style="text-align: center; margin-top: 2rem; color: var(--text-muted);">
                <a href="/">← Нүүр хуудас руу буцах</a>
            </p>
        </div>
    </div>
</body>
</html>

