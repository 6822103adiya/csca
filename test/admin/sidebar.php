<?php
/**
 * Admin Sidebar
 */

// Get pending count for badge
$pendingCount = 0;
try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'pending_approval'");
    $pendingCount = $stmt->fetch()['count'];
} catch (PDOException $e) {}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <span class="logo-icon">🎓</span>
        <h2>Тэтгэлэг Сэсэн</h2>
    </div>
    
    <nav class="sidebar-menu">
        <span class="menu-label">Үндсэн</span>
        <a href="dashboard.php" class="menu-item <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            Dashboard
        </a>
        
        <span class="menu-label">Бүртгэлүүд</span>
        <a href="registrations.php" class="menu-item <?= $currentPage === 'registrations.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            Бүх бүртгэлүүд
            <?php if ($pendingCount > 0): ?>
            <span class="badge"><?= $pendingCount ?></span>
            <?php endif; ?>
        </a>
        <a href="registrations.php?status=pending_approval" class="menu-item">
            <i class="fas fa-clock"></i>
            Хүлээгдэж буй
        </a>
        <a href="registrations.php?status=approved" class="menu-item">
            <i class="fas fa-check-circle"></i>
            Баталгаажсан
        </a>
        <a href="registrations.php?status=rejected" class="menu-item">
            <i class="fas fa-times-circle"></i>
            Татгалзсан
        </a>
        
        <span class="menu-label">Төлбөр</span>
        <a href="payments.php" class="menu-item <?= $currentPage === 'payments.php' ? 'active' : '' ?>">
            <i class="fas fa-credit-card"></i>
            Төлбөрийн шалгалт
        </a>
        
        <span class="menu-label">Агуулга</span>
        <a href="content.php" class="menu-item <?= $currentPage === 'content.php' ? 'active' : '' ?>">
            <i class="fas fa-edit"></i>
            Сайтын агуулга
        </a>
        <a href="settings.php" class="menu-item <?= $currentPage === 'settings.php' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            Тохиргоо
        </a>
        
        <span class="menu-label">Хэрэгслүүд</span>
        <a href="export.php" class="menu-item <?= $currentPage === 'export.php' ? 'active' : '' ?>">
            <i class="fas fa-download"></i>
            CSV татах
        </a>
        <a href="email.php" class="menu-item <?= $currentPage === 'email.php' ? 'active' : '' ?>">
            <i class="fas fa-envelope"></i>
            Имэйл илгээх
        </a>
        <a href="logs.php" class="menu-item <?= $currentPage === 'logs.php' ? 'active' : '' ?>">
            <i class="fas fa-history"></i>
            Лог
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)) ?>
            </div>
            <div class="user-details">
                <strong><?= escape($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'Admin') ?></strong>
                <span><?= escape($_SESSION['admin_role'] ?? 'admin') ?></span>
            </div>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Гарах
        </a>
    </div>
</aside>

