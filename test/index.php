<?php
$pageTitle = 'Нүүр';
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1><?= getSetting('home_title', 'Ирээдүйгээ Бүтээ') ?></h1>
                <p><?= getSetting('home_description') ?></p>
                <div class="hero-buttons">
                    <a href="/register.php" class="btn btn-primary btn-lg">Бүртгүүлэх</a>
                    <a href="/scholarship.php" class="btn btn-secondary btn-lg">Дэлгэрэнгүй</a>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-graphic">🎓</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Яагаад биднийг сонгох вэ?</h2>
            <p>Бид танд гадаадад суралцах хамгийн найдвартай замыг санал болгож байна</p>
        </div>
        <div class="cards-grid">
            <div class="card">
                <div class="card-icon">🌍</div>
                <h3>Олон улсын хамтрагчид</h3>
                <p>Хятад, Солонгос, Герман, Орос зэрэг улсын 100+ их сургуультай хамтран ажилладаг</p>
            </div>
            <div class="card">
                <div class="card-icon">💰</div>
                <h3>Бүрэн тэтгэлэг</h3>
                <p>Сургалтын төлбөр, байр, хоолны зардлыг бүрэн хариуцсан тэтгэлгүүд</p>
            </div>
            <div class="card">
                <div class="card-icon">📚</div>
                <h3>Хэлний бэлтгэл</h3>
                <p>Тухайн улсын хэлний сургалтыг тэтгэлэгт хамруулсан хөтөлбөрүүд</p>
            </div>
            <div class="card">
                <div class="card-icon">🤝</div>
                <h3>Бүрэн дэмжлэг</h3>
                <p>Бүртгэлээс эхлээд суралцаж дуустал бүрэн зөвлөгөө, дэмжлэг үзүүлнэ</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-card);">
    <div class="container">
        <div class="section-header">
            <h2>Тэтгэлгийн улсууд</h2>
            <p>Дараах улсуудад суралцах боломжтой</p>
        </div>
        <div class="cards-grid">
            <?php 
            $homeCountries = getActiveCountries();
            foreach ($homeCountries as $country): 
            ?>
            <div class="card country-card">
                <div class="country-flag"><?= $country['flag'] ?></div>
                <h3><?= clean($country['name']) ?></h3>
                <p><?= clean($country['description']) ?></p>
                <a href="/scholarship.php#<?= $country['code'] ?>" class="btn btn-outline btn-sm mt-2">Дэлгэрэнгүй</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Хэрхэн бүртгүүлэх вэ?</h2>
            <p>Энгийн 4 алхамаар бүртгүүлээрэй</p>
        </div>
        <div class="cards-grid">
            <div class="card text-center">
                <div class="card-icon" style="margin: 0 auto 1.5rem;">1️⃣</div>
                <h3>Бүртгүүлэх</h3>
                <p>Онлайн формоо бөглөж, шаардлагатай баримт бичгүүдийг хавсаргана</p>
            </div>
            <div class="card text-center">
                <div class="card-icon" style="margin: 0 auto 1.5rem;">2️⃣</div>
                <h3>Төлбөр төлөх</h3>
                <p>Бүртгэлийн хураамжаа банкны дансаар шилжүүлнэ</p>
            </div>
            <div class="card text-center">
                <div class="card-icon" style="margin: 0 auto 1.5rem;">3️⃣</div>
                <h3>Баталгаажуулалт</h3>
                <p>Админ таны бүртгэлийг 24 цагийн дотор шалгаж баталгаажуулна</p>
            </div>
            <div class="card text-center">
                <div class="card-icon" style="margin: 0 auto 1.5rem;">4️⃣</div>
                <h3>Эхлүүлэх</h3>
                <p>Тэтгэлэгт хөтөлбөрт хамрагдах дараагийн алхмуудыг авна</p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="/register.php" class="btn btn-primary btn-lg">Одоо бүртгүүлэх</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
