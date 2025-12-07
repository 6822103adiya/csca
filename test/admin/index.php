<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();

$pdo = getDB();

// Статистик
$stats = [
    'total' => $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn(),
    'pending' => $pdo->query("SELECT COUNT(*) FROM registrations WHERE status = 'pending'")->fetchColumn(),
    'approved' => $pdo->query("SELECT COUNT(*) FROM registrations WHERE status = 'approved'")->fetchColumn(),
    'rejected' => $pdo->query("SELECT COUNT(*) FROM registrations WHERE status = 'rejected'")->fetchColumn(),
];

// Сүүлийн бүртгэлүүд
$recent = $pdo->query("SELECT * FROM registrations ORDER BY created_at DESC LIMIT 5")->fetchAll();

$currentPage = 'dashboard';
include 'includes/header.php';
?>

<div class="admin-header">
    <h1>Dashboard</h1>
    <p style="color: var(--text-secondary);">Сайн байна уу, <?= $_SESSION['admin_username'] ?>!</p>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <h4>Нийт бүртгэл</h4>
        <div class="stat-value"><?= $stats['total'] ?></div>
    </div>
    <div class="stat-card">
        <h4>Хүлээгдэж буй</h4>
        <div class="stat-value" style="color: var(--warning);"><?= $stats['pending'] ?></div>
    </div>
    <div class="stat-card">
        <h4>Баталгаажсан</h4>
        <div class="stat-value" style="color: var(--success);"><?= $stats['approved'] ?></div>
    </div>
    <div class="stat-card">
        <h4>Татгалзсан</h4>
        <div class="stat-value" style="color: var(--error);"><?= $stats['rejected'] ?></div>
    </div>
</div>

<!-- Recent Registrations -->
<div class="card" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0;">Сүүлийн бүртгэлүүд</h3>
        <a href="/admin/pending.php" class="btn btn-secondary btn-sm">Бүгдийг харах</a>
    </div>
    
    <?php if (empty($recent)): ?>
    <p style="color: var(--text-muted); text-align: center; padding: 2rem;">Бүртгэл байхгүй байна.</p>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Огноо</th>
                <th>Нэр</th>
                <th>Улс</th>
                <th>Утас</th>
                <th>Статус</th>
                <th>Үйлдэл</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent as $row): ?>
            <tr>
                <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                <td><?= clean($row['last_name'] . ' ' . $row['first_name']) ?></td>
                <td>
                    <?php 
                    $countries = json_decode($row['countries'], true);
                    foreach ($countries as $c) {
                        echo getCountryName($c) . ' ';
                    }
                    ?>
                </td>
                <td><?= clean($row['phone']) ?></td>
                <td>
                    <?php if ($row['status'] === 'pending'): ?>
                    <span class="badge badge-pending">Хүлээгдэж буй</span>
                    <?php elseif ($row['status'] === 'approved'): ?>
                    <span class="badge badge-approved">Баталгаажсан</span>
                    <?php else: ?>
                    <span class="badge badge-rejected">Татгалзсан</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/admin/view.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Харах</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
