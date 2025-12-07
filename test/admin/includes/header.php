<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - Даам Тэтгэлэг</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-logo">
                <a href="/admin/" class="logo" style="font-size: 1.25rem;">
                    🎓 Даам <span>Тэтгэлэг</span>
                </a>
            </div>
            <ul class="admin-nav">
                <li>
                    <a href="/admin/index.php" class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                        📊 Dashboard
                    </a>
                </li>
                <li>
                    <a href="/admin/pending.php" class="<?= ($currentPage ?? '') === 'pending' ? 'active' : '' ?>">
                        ⏳ Хүлээгдэж буй
                    </a>
                </li>
                <li>
                    <a href="/admin/approved.php" class="<?= ($currentPage ?? '') === 'approved' ? 'active' : '' ?>">
                        ✅ Баталгаажсан
                    </a>
                </li>
                <li>
                    <a href="/admin/rejected.php" class="<?= ($currentPage ?? '') === 'rejected' ? 'active' : '' ?>">
                        ❌ Татгалзсан
                    </a>
                </li>
                <li>
                    <a href="/admin/settings.php" class="<?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
                        ⚙️ Тохиргоо
                    </a>
                </li>
                <li style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                    <a href="/admin/logout.php">
                        🚪 Гарах
                    </a>
                </li>
            </ul>
        </aside>
        <main class="admin-main">

