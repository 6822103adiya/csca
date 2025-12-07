<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();

$pdo = getDB();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM registrations WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: /admin/pending.php');
    exit;
}

// Approve/Reject үйлдэл
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE registrations SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Email илгээх
        if (sendApprovalEmail($user)) {
            setFlash('success', 'Бүртгэл баталгаажлаа! Email илгээгдлээ.');
        } else {
            setFlash('success', 'Бүртгэл баталгаажлаа! (Email илгээхэд алдаа гарлаа)');
        }
    } elseif ($action === 'reject') {
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
    
    header('Location: /admin/view.php?id=' . $id);
    exit;
}

$currentPage = '';
include 'includes/header.php';

$flash = getFlash();
?>

<div class="admin-header">
    <div>
        <a href="javascript:history.back()" style="color: var(--text-secondary);">← Буцах</a>
        <h1 style="margin-top: 0.5rem;"><?= clean($user['last_name'] . ' ' . $user['first_name']) ?></h1>
    </div>
    <div>
        <?php if ($user['status'] === 'pending'): ?>
        <span class="badge badge-pending" style="font-size: 1rem; padding: 0.5rem 1rem;">Хүлээгдэж буй</span>
        <?php elseif ($user['status'] === 'approved'): ?>
        <span class="badge badge-approved" style="font-size: 1rem; padding: 0.5rem 1rem;">Баталгаажсан</span>
        <?php else: ?>
        <span class="badge badge-rejected" style="font-size: 1rem; padding: 0.5rem 1rem;">Татгалзсан</span>
        <?php endif; ?>
    </div>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>">
    <?= $flash['message'] ?>
</div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div>
        <!-- Хувийн мэдээлэл -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="color: var(--primary); margin-bottom: 1.5rem;">👤 Хувийн мэдээлэл</h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem; font-size: 0.875rem;">Овог, Нэр</p>
                    <p style="margin: 0; font-weight: 600;"><?= clean($user['last_name'] . ' ' . $user['first_name']) ?></p>
                </div>
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem; font-size: 0.875rem;">Регистр</p>
                    <p style="margin: 0; font-weight: 600;"><?= clean($user['register_number']) ?></p>
                </div>
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem; font-size: 0.875rem;">Сургууль</p>
                    <p style="margin: 0;"><?= clean($user['school']) ?></p>
                </div>
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem; font-size: 0.875rem;">Анги</p>
                    <p style="margin: 0;"><?= $user['grade'] ?>-р анги</p>
                </div>
                <div style="grid-column: span 2;">
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem; font-size: 0.875rem;">Хаяг</p>
                    <p style="margin: 0;"><?= clean($user['address']) ?></p>
                </div>
            </div>
        </div>

        <!-- Холбоо барих -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="color: var(--primary); margin-bottom: 1.5rem;">📱 Холбоо барих</h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem; font-size: 0.875rem;">Утас</p>
                    <p style="margin: 0; font-weight: 600;"><?= clean($user['phone']) ?></p>
                </div>
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.25rem; font-size: 0.875rem;">Gmail</p>
                    <p style="margin: 0;"><?= clean($user['email']) ?> 
                        <?= $user['email_verified'] ? '<span style="color: var(--success);">✓</span>' : '' ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Хэлний оноо -->
        <?php if ($user['language_score']): ?>
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="color: var(--primary); margin-bottom: 1.5rem;">📝 Хэлний оноо</h3>
            <p style="font-weight: 600;"><?= clean($user['language_score']) ?></p>
            <?php if ($user['language_certificate']): ?>
            <a href="/uploads/<?= $user['language_certificate'] ?>" target="_blank" class="btn btn-secondary btn-sm">
                📄 Сертификат харах
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Иргэний үнэмлэх -->
        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 1.5rem;">🪪 Иргэний үнэмлэх</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.875rem;">Урд тал</p>
                    <a href="/uploads/<?= $user['id_front'] ?>" target="_blank">
                        <img src="/uploads/<?= $user['id_front'] ?>" style="width: 100%; border-radius: 8px; border: 1px solid var(--border);">
                    </a>
                </div>
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.875rem;">Ар тал</p>
                    <a href="/uploads/<?= $user['id_back'] ?>" target="_blank">
                        <img src="/uploads/<?= $user['id_back'] ?>" style="width: 100%; border-radius: 8px; border: 1px solid var(--border);">
                    </a>
                </div>
                <div>
                    <p style="color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.875rem;">Selfie</p>
                    <a href="/uploads/<?= $user['id_selfie'] ?>" target="_blank">
                        <img src="/uploads/<?= $user['id_selfie'] ?>" style="width: 100%; border-radius: 8px; border: 1px solid var(--border);">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div>
        <!-- Улсын сонголт -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">🌍 Сонгосон улсууд</h3>
            <?php 
            $countries = json_decode($user['countries'], true);
            $flags = ['china' => '🇨🇳', 'korea' => '🇰🇷', 'germany' => '🇩🇪', 'russia' => '🇷🇺'];
            foreach ($countries as $c): 
            ?>
            <div style="padding: 0.75rem; background: var(--bg-input); border-radius: 8px; margin-bottom: 0.5rem;">
                <?= $flags[$c] ?? '' ?> <?= getCountryName($c) ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Огноо -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">📅 Бүртгэлийн огноо</h3>
            <p style="margin: 0;"><?= date('Y оны m сарын d, H:i', strtotime($user['created_at'])) ?></p>
        </div>

        <!-- Үйлдлүүд -->
        <?php if ($user['status'] === 'pending'): ?>
        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">⚡ Үйлдлүүд</h3>
            <form method="POST" style="margin-bottom: 1rem;">
                <input type="hidden" name="action" value="approve">
                <button type="submit" class="btn btn-primary btn-block" onclick="return confirm('Баталгаажуулах уу?')">
                    ✅ Баталгаажуулах
                </button>
            </form>
            <form method="POST">
                <input type="hidden" name="action" value="reject">
                <div class="form-group">
                    <textarea class="form-textarea" name="reason" placeholder="Татгалзах шалтгаан..." style="min-height: 80px;"></textarea>
                </div>
                <button type="submit" class="btn btn-secondary btn-block" style="background: var(--error);" onclick="return confirm('Татгалзах уу?')">
                    ❌ Татгалзах
                </button>
            </form>
        </div>
        <?php elseif ($user['status'] === 'rejected' && $user['reject_reason']): ?>
        <div class="card">
            <h3 style="color: var(--error); margin-bottom: 1rem;">❌ Татгалзсан шалтгаан</h3>
            <p style="margin: 0;"><?= clean($user['reject_reason']) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

