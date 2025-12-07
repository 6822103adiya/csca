<?php
$pageTitle = 'Тэтгэлэгийн мэдээлэл';
require_once 'includes/header.php';

// Идэвхтэй улсуудыг авах
$countries = getActiveCountries();
?>

<section class="section" style="padding-top: calc(var(--header-height) + 4rem);">
    <div class="container">
        <div class="section-header">
            <h1>Тэтгэлэгийн мэдээлэл</h1>
            <p>Дараах улсуудад суралцах боломжтой тэтгэлгийн хөтөлбөрүүд</p>
        </div>

        <?php if (empty($countries)): ?>
        <div class="card text-center" style="padding: 4rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🌍</div>
            <h3>Одоогоор улс бүртгэгдээгүй байна</h3>
        </div>
        <?php else: ?>
        
        <?php foreach ($countries as $country): ?>
        <div id="<?= $country['code'] ?>" class="card" style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.5rem;">
                <div style="font-size: 4rem;"><?= $country['flag'] ?></div>
                <div style="flex: 1;">
                    <h2 style="color: var(--primary); margin-bottom: 1rem;"><?= clean($country['name']) ?></h2>
                    <p style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 1.5rem;">
                        <?= clean($country['description']) ?>
                    </p>
                    <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <?php if ($country['tuition']): ?>
                        <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
                            <h4 style="color: var(--primary); margin-bottom: 0.5rem;">💵 Тэтгэлэг</h4>
                            <p style="margin: 0; font-size: 0.9rem;"><?= clean($country['tuition']) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($country['accommodation']): ?>
                        <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
                            <h4 style="color: var(--primary); margin-bottom: 0.5rem;">🏠 Байр</h4>
                            <p style="margin: 0; font-size: 0.9rem;"><?= clean($country['accommodation']) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($country['language']): ?>
                        <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
                            <h4 style="color: var(--primary); margin-bottom: 0.5rem;">📖 Хэл</h4>
                            <p style="margin: 0; font-size: 0.9rem;"><?= clean($country['language']) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($country['duration']): ?>
                        <div style="background: var(--bg-input); padding: 1rem; border-radius: 10px;">
                            <h4 style="color: var(--primary); margin-bottom: 0.5rem;">⏱️ Хугацаа</h4>
                            <p style="margin: 0; font-size: 0.9rem;"><?= clean($country['duration']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="/register.php" class="btn btn-primary btn-lg">Одоо бүртгүүлэх</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
