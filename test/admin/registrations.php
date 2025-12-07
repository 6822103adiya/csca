<?php
/**
 * Тэтгэлэг Сэсэн - Registrations List
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

$db = Database::getInstance();

// Filters
$status = $_GET['status'] ?? '';
$country = $_GET['country'] ?? '';
$grade = $_GET['grade'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$where = [];
$params = [];

if ($status) {
    $where[] = "status = ?";
    $params[] = $status;
}

if ($country) {
    $where[] = "JSON_CONTAINS(selected_countries, ?)";
    $params[] = json_encode($country);
}

if ($grade) {
    $where[] = "grade = ?";
    $params[] = $grade;
}

if ($search) {
    $where[] = "(last_name LIKE ? OR first_name LIKE ? OR phone LIKE ? OR email LIKE ? OR register_number LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$countStmt = $db->prepare("SELECT COUNT(*) as count FROM registrations $whereClause");
$countStmt->execute($params);
$totalCount = $countStmt->fetch()['count'];
$totalPages = ceil($totalCount / $perPage);

// Get registrations
$query = "SELECT * FROM registrations $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$registrations = $stmt->fetchAll();

$pageTitle = 'Бүртгэлүүд';
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
                <h1>Бүртгэлүүд</h1>
                <div class="header-actions">
                    <a href="export.php?<?= http_build_query($_GET) ?>" class="btn btn-outline btn-sm">
                        <i class="fas fa-download"></i> CSV татах
                    </a>
                    <button class="theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-moon"></i>
                        <i class="fas fa-sun"></i>
                    </button>
                </div>
            </header>
            
            <div class="admin-content">
                <div class="data-card">
                    <div class="data-card-header">
                        <h2><i class="fas fa-list"></i> Бүртгэлийн жагсаалт (<?= $totalCount ?>)</h2>
                        
                        <form class="filters" method="GET">
                            <input type="text" name="search" placeholder="Хайх..." value="<?= escape($search) ?>">
                            
                            <select name="status">
                                <option value="">Бүх статус</option>
                                <option value="pending_verification" <?= $status === 'pending_verification' ? 'selected' : '' ?>>Баталгаажуулалт хүлээгдэж байна</option>
                                <option value="pending_payment" <?= $status === 'pending_payment' ? 'selected' : '' ?>>Төлбөр хүлээгдэж байна</option>
                                <option value="pending_approval" <?= $status === 'pending_approval' ? 'selected' : '' ?>>Хянагдаж байна</option>
                                <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Баталгаажсан</option>
                                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Татгалзсан</option>
                            </select>
                            
                            <select name="country">
                                <option value="">Бүх улс</option>
                                <?php foreach (COUNTRIES as $code => $c): ?>
                                <option value="<?= $code ?>" <?= $country === $code ? 'selected' : '' ?>><?= $c['flag'] ?> <?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <select name="grade">
                                <option value="">Бүх анги</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>" <?= $grade == $i ? 'selected' : '' ?>><?= $i ?>-р анги</option>
                                <?php endfor; ?>
                            </select>
                            
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Хайх
                            </button>
                            
                            <?php if ($status || $country || $grade || $search): ?>
                            <a href="registrations.php" class="btn btn-outline btn-sm">
                                <i class="fas fa-times"></i> Цэвэрлэх
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Овог, Нэр</th>
                                    <th>Рег. дугаар</th>
                                    <th>Утас</th>
                                    <th>Имэйл</th>
                                    <th>Анги</th>
                                    <th>Улс</th>
                                    <th>Статус</th>
                                    <th>Огноо</th>
                                    <th>Үйлдэл</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($registrations)): ?>
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 2rem;">
                                        Бүртгэл олдсонгүй
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($registrations as $reg): ?>
                                    <tr>
                                        <td>#<?= $reg['id'] ?></td>
                                        <td><?= escape($reg['last_name'] . ' ' . $reg['first_name']) ?></td>
                                        <td><?= escape($reg['register_number']) ?></td>
                                        <td><?= escape($reg['phone']) ?></td>
                                        <td><?= escape($reg['email']) ?></td>
                                        <td><?= $reg['grade'] ?>-р анги</td>
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
                                                    $statusText = 'Баталгаажуулалт';
                                                    break;
                                                case 'pending_payment':
                                                    $statusClass = 'status-payment';
                                                    $statusText = 'Төлбөр';
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
                    
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="pagination-btn <?= $i === $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="pagination-btn">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
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
            </div>
            <div class="detail-actions" id="detailActions">
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
        
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>

