<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();

$pdo = getDB();
$registrations = $pdo->query("SELECT * FROM registrations WHERE status = 'rejected' ORDER BY created_at DESC")->fetchAll();

$currentPage = 'rejected';
include 'includes/header.php';
?>

<div class="admin-header">
    <h1>❌ Татгалзсан бүртгэлүүд</h1>
</div>

<?php if (empty($registrations)): ?>
<div class="card text-center" style="padding: 4rem;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
    <h3>Татгалзсан бүртгэл байхгүй</h3>
</div>
<?php else: ?>
<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Огноо</th>
                <th>Нэр</th>
                <th>Утас</th>
                <th>Gmail</th>
                <th>Шалтгаан</th>
                <th>Үйлдэл</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registrations as $row): ?>
            <tr>
                <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                <td><?= clean($row['last_name'] . ' ' . $row['first_name']) ?></td>
                <td><?= clean($row['phone']) ?></td>
                <td><?= clean($row['email']) ?></td>
                <td><?= clean($row['reject_reason'] ?: '-') ?></td>
                <td>
                    <a href="/admin/view.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Харах</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

