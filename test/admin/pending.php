<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();

$pdo = getDB();

// Approve/Reject үйлдэл
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];
    
    // Хэрэглэгчийн мэдээлэл авах
    $userStmt = $pdo->prepare("SELECT * FROM registrations WHERE id = ?");
    $userStmt->execute([$id]);
    $user = $userStmt->fetch();
    
    if ($action === 'approve' && $user) {
        $stmt = $pdo->prepare("UPDATE registrations SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Email илгээх
        if (sendApprovalEmail($user)) {
            setFlash('success', 'Бүртгэл баталгаажлаа! Email илгээгдлээ.');
        } else {
            setFlash('success', 'Бүртгэл баталгаажлаа! (Email илгээхэд алдаа гарлаа)');
        }
    } elseif ($action === 'reject' && $user) {
        $reason = trim($_POST['reason'] ?? '');
        $stmt = $pdo->prepare("UPDATE registrations SET status = 'rejected', reject_reason = ? WHERE id = ?");
        $stmt->execute([$reason, $id]);
        
        // Email илгээх
        if (sendRejectionEmail($user, $reason)) {
            setFlash('success', 'Бүртгэл татгалзлаа! Email илгээгдлээ.');
        } else {
            setFlash('success', 'Бүртгэл татгалзлаа! (Email илгээхэд алдаа гарлаа)');
        }
    }
    
    header('Location: /admin/pending.php');
    exit;
}

// Хүлээгдэж буй бүртгэлүүд
$registrations = $pdo->query("SELECT * FROM registrations WHERE status = 'pending' ORDER BY created_at DESC")->fetchAll();

$currentPage = 'pending';
include 'includes/header.php';

$flash = getFlash();
?>

<div class="admin-header">
    <h1>⏳ Хүлээгдэж буй бүртгэлүүд</h1>
    <p style="color: var(--text-secondary);">Баталгаажуулах эсвэл татгалзах</p>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>">
    <?= $flash['message'] ?>
</div>
<?php endif; ?>

<?php if (empty($registrations)): ?>
<div class="card text-center" style="padding: 4rem;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
    <h3>Хүлээгдэж буй бүртгэл байхгүй</h3>
    <p style="color: var(--text-secondary);">Шинэ бүртгэл ирэхэд энд харагдана.</p>
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
                <th>Утас</th>
                <th>Gmail</th>
                <th>Үйлдэл</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registrations as $row): ?>
            <tr>
                <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
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
                <td><?= clean($row['phone']) ?></td>
                <td><?= clean($row['email']) ?></td>
                <td>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/admin/view.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Харах</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Баталгаажуулах уу?')">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-primary btn-sm" style="background: var(--success);">✓</button>
                        </form>
                        <button type="button" class="btn btn-secondary btn-sm" style="background: var(--error);" 
                                onclick="showRejectModal(<?= $row['id'] ?>)">✕</button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Reject Modal -->
<div class="modal-overlay" id="rejectModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Татгалзах шалтгаан</h3>
            <button class="modal-close" onclick="closeRejectModal()">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="id" id="rejectId">
            <input type="hidden" name="action" value="reject">
            <div class="form-group">
                <label class="form-label">Шалтгаан (заавал биш)</label>
                <textarea class="form-textarea" name="reason" placeholder="Татгалзах шалтгаанаа бичнэ үү..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="background: var(--error);">Татгалзах</button>
        </form>
    </div>
</div>

<script>
function showRejectModal(id) {
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectModal').classList.add('active');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
}
</script>

<?php include 'includes/footer.php'; ?>

