<?php
/**
 * Тэтгэлэг Сэсэн - Admin Dashboard
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

// Get statistics
$db = Database::getInstance();

$stats = [];

// Total registrations
$stmt = $db->query("SELECT COUNT(*) as count FROM registrations");
$stats['total'] = $stmt->fetch()['count'];

// Pending approval
$stmt = $db->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'pending_approval'");
$stats['pending'] = $stmt->fetch()['count'];

// Approved
$stmt = $db->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'approved'");
$stats['approved'] = $stmt->fetch()['count'];

// Rejected
$stmt = $db->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'rejected'");
$stats['rejected'] = $stmt->fetch()['count'];

// Pending payment
$stmt = $db->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'pending_payment'");
$stats['pending_payment'] = $stmt->fetch()['count'];

// Recent registrations
$stmt = $db->query("
    SELECT * FROM registrations 
    ORDER BY created_at DESC 
    LIMIT 10
");
$recentRegistrations = $stmt->fetchAll();

$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="mn" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?> | Админ - Тэтгэлэг Сэсэн</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Mongolian:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <div class="admin-main">
            <header class="admin-header">
                <button class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Dashboard</h1>
                <div class="header-actions">
                    <button class="theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-moon"></i>
                        <i class="fas fa-sun"></i>
                    </button>
                </div>
            </header>
            
            <div class="admin-content">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['total'] ?></h3>
                            <p>Нийт бүртгэл</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['pending'] ?></h3>
                            <p>Хүлээгдэж буй</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['approved'] ?></h3>
                            <p>Баталгаажсан</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon rejected">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= $stats['rejected'] ?></h3>
                            <p>Татгалзсан</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Registrations -->
                <div class="data-card">
                    <div class="data-card-header">
                        <h2><i class="fas fa-list"></i> Сүүлийн бүртгэлүүд</h2>
                        <a href="registrations.php" class="btn btn-outline btn-sm">
                            Бүгдийг харах <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Овог, Нэр</th>
                                    <th>Утас</th>
                                    <th>Имэйл</th>
                                    <th>Улс</th>
                                    <th>Статус</th>
                                    <th>Огноо</th>
                                    <th>Үйлдэл</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentRegistrations)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2rem;">
                                        Бүртгэл олдсонгүй
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($recentRegistrations as $reg): ?>
                                    <tr>
                                        <td>#<?= $reg['id'] ?></td>
                                        <td><?= escape($reg['last_name'] . ' ' . $reg['first_name']) ?></td>
                                        <td><?= escape($reg['phone']) ?></td>
                                        <td><?= escape($reg['email']) ?></td>
                                        <td>
                                            <?php 
                                            $countries = json_decode($reg['selected_countries'], true);
                                            foreach ($countries as $code) {
                                                echo COUNTRIES[$code]['flag'] ?? '';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($reg['status']) {
                                                case 'pending_verification':
                                                    $statusClass = 'status-pending';
                                                    $statusText = 'Баталгаажуулалт хүлээгдэж байна';
                                                    break;
                                                case 'pending_payment':
                                                    $statusClass = 'status-payment';
                                                    $statusText = 'Төлбөр хүлээгдэж байна';
                                                    break;
                                                case 'pending_approval':
                                                    $statusClass = 'status-pending';
                                                    $statusText = 'Хянагдаж байна';
                                                    break;
                                                case 'approved':
                                                    $statusClass = 'status-approved';
                                                    $statusText = 'Баталгаажсан';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'status-rejected';
                                                    $statusText = 'Татгалзсан';
                                                    break;
                                            }
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                        <td><?= date('Y-m-d', strtotime($reg['created_at'])) ?></td>
                                        <td>
                                            <div class="action-btns">
                                                <button class="action-btn view" onclick="viewRegistration(<?= $reg['id'] ?>)" title="Дэлгэрэнгүй">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($reg['status'] === 'pending_approval'): ?>
                                                <button class="action-btn approve" onclick="approveRegistration(<?= $reg['id'] ?>)" title="Баталгаажуулах">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="action-btn reject" onclick="rejectRegistration(<?= $reg['id'] ?>)" title="Татгалзах">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detail Modal -->
    <div class="detail-modal" id="detailModal">
        <div class="detail-content">
            <div class="detail-header">
                <h2><i class="fas fa-user"></i> Бүртгэлийн дэлгэрэнгүй</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="detail-body" id="detailBody">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="detail-actions" id="detailActions">
                <!-- Actions loaded via AJAX -->
            </div>
        </div>
    </div>
    
    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
        }
        
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const newTheme = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
        
        function viewRegistration(id) {
            document.getElementById('detailModal').classList.add('active');
            document.getElementById('detailBody').innerHTML = '<p style="text-align:center;padding:2rem;">Уншиж байна...</p>';
            
            fetch('ajax/get_registration.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('detailBody').innerHTML = data.html;
                        document.getElementById('detailActions').innerHTML = data.actions;
                    } else {
                        alert(data.message);
                    }
                });
        }
        
        function closeModal() {
            document.getElementById('detailModal').classList.remove('active');
        }
        
        function approveRegistration(id) {
            if (confirm('Энэ бүртгэлийг баталгаажуулах уу?')) {
                fetch('ajax/approve.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                });
            }
        }
        
        function rejectRegistration(id) {
            const reason = prompt('Татгалзах шалтгаан оруулна уу:');
            if (reason) {
                fetch('ajax/reject.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + id + '&reason=' + encodeURIComponent(reason)
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                });
            }
        }
        
        // Close modal on outside click
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>

