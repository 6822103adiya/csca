<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();

$pdo = getDB();

// Устгах үйлдэл
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];
    
    // Файлуудыг устгах
    $stmt = $pdo->prepare("SELECT id_front, id_back, id_selfie, language_certificate FROM registrations WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if ($user) {
        $uploadDir = __DIR__ . '/../uploads/';
        if ($user['id_front'] && file_exists($uploadDir . $user['id_front'])) {
            unlink($uploadDir . $user['id_front']);
        }
        if ($user['id_back'] && file_exists($uploadDir . $user['id_back'])) {
            unlink($uploadDir . $user['id_back']);
        }
        if ($user['id_selfie'] && file_exists($uploadDir . $user['id_selfie'])) {
            unlink($uploadDir . $user['id_selfie']);
        }
        if ($user['language_certificate'] && file_exists($uploadDir . $user['language_certificate'])) {
            unlink($uploadDir . $user['language_certificate']);
        }
        
        // Database-ээс устгах
        $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = ?");
        $stmt->execute([$id]);
        
        setFlash('success', 'Хэрэглэгч амжилттай устгагдлаа.');
    }
    
    header('Location: /admin/approved.php');
    exit;
}

// Улсаар шүүх
$countryFilter = $_GET['country'] ?? '';
$where = "WHERE status = 'approved'";
$params = [];

if ($countryFilter) {
    $where .= " AND JSON_CONTAINS(countries, ?)";
    $params[] = '"' . $countryFilter . '"';
}

$stmt = $pdo->prepare("SELECT * FROM registrations $where ORDER BY created_at DESC");
$stmt->execute($params);
$registrations = $stmt->fetchAll();

$currentPage = 'approved';
include 'includes/header.php';

$flash = getFlash();
?>

<div class="admin-header">
    <h1>✅ Баталгаажсан бүртгэлүүд</h1>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <select class="form-select" style="width: auto;" onchange="location.href='/admin/approved.php?country=' + this.value">
            <option value="">Бүх улс</option>
            <option value="china" <?= $countryFilter === 'china' ? 'selected' : '' ?>>🇨🇳 Хятад</option>
            <option value="korea" <?= $countryFilter === 'korea' ? 'selected' : '' ?>>🇰🇷 Солонгос</option>
            <option value="germany" <?= $countryFilter === 'germany' ? 'selected' : '' ?>>🇩🇪 Герман</option>
            <option value="russia" <?= $countryFilter === 'russia' ? 'selected' : '' ?>>🇷🇺 Орос</option>
        </select>
    </div>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>">
    <?= $flash['type'] === 'success' ? '✅' : '❌' ?> <?= $flash['message'] ?>
</div>
<?php endif; ?>

<?php if (empty($registrations)): ?>
<div class="card text-center" style="padding: 4rem;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
    <h3>Баталгаажсан бүртгэл байхгүй</h3>
</div>
<?php else: ?>
<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Огноо</th>
                <th>Нэр</th>
                <th>Регистр</th>
                <th>Улс</th>
                <th>Сургууль</th>
                <th>Утас</th>
                <th>Gmail</th>
                <th>Үйлдэл</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registrations as $row): ?>
            <tr>
                <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                <td><?= clean($row['last_name'] . ' ' . $row['first_name']) ?></td>
                <td><?= clean($row['register_number']) ?></td>
                <td>
                    <?php 
                    $countries = json_decode($row['countries'], true);
                    foreach ($countries as $c) {
                        echo getCountryName($c) . '<br>';
                    }
                    ?>
                </td>
                <td><?= clean($row['school']) ?> (<?= $row['grade'] ?>-р анги)</td>
                <td><?= clean($row['phone']) ?></td>
                <td><?= clean($row['email']) ?></td>
                <td>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <a href="/admin/view.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Харах</a>
                        <a href="mailto:<?= $row['email'] ?>" class="btn btn-primary btn-sm">📧</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Энэ хэрэглэгчийг устгах уу? Энэ үйлдлийг буцаах боломжгүй!')">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-secondary btn-sm" style="background: var(--error);">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div style="margin-top: 1rem; color: var(--text-secondary);">
    Нийт: <?= count($registrations) ?> бүртгэл
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
