<?php
$pageTitle = 'Бидний тухай';
require_once 'includes/header.php';
?>

<section class="section" style="padding-top: calc(var(--header-height) + 4rem);">
    <div class="container">
        <div class="section-header">
            <h1><?= getSetting('about_title', 'Бидний тухай') ?></h1>
            <p>Гадаадад суралцах хүсэл мөрөөдлийг тань биелүүлэхийн төлөө</p>
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            <div class="card" style="margin-bottom: 2rem;">
                <h3>Бидний түүх</h3>
                <p style="font-size: 1.1rem; line-height: 1.8;">
                    <?= getSetting('about_content') ?>
                </p>
            </div>

            <div class="cards-grid" style="grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                <div class="card text-center">
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">500+</div>
                    <p style="margin: 0; color: var(--text-secondary);">Амжилттай оюутан</p>
                </div>
                <div class="card text-center">
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">4</div>
                    <p style="margin: 0; color: var(--text-secondary);">Улс</p>
                </div>
                <div class="card text-center">
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary);">100+</div>
                    <p style="margin: 0; color: var(--text-secondary);">Хамтрагч сургуулиуд</p>
                </div>
            </div>

            <div class="card" style="margin-top: 2rem;">
                <h3>Бидний зорилго</h3>
                <p style="font-size: 1.1rem; line-height: 1.8;">
                    Монгол залуучуудад олон улсын түвшний боловсрол эзэмших боломжийг олгож, 
                    тэдний ирээдүйг гэрэлтүүлэхэд туслах нь бидний гол зорилго юм.
                </p>
                <p style="font-size: 1.1rem; line-height: 1.8;">
                    Бид зөвхөн тэтгэлэг олгоод зогсохгүй, оюутнуудад гадаадад амжилттай суралцаж, 
                    ирээдүйн мэргэжилтэн болоход нь бүх талын дэмжлэг үзүүлдэг.
                </p>
            </div>

            <div class="card" style="margin-top: 2rem;">
                <h3>Бидний үнэт зүйлс</h3>
                <div class="cards-grid" style="margin-top: 1.5rem;">
                    <div>
                        <h4 style="color: var(--primary);">🎯 Чанар</h4>
                        <p>Зөвхөн итгэмжлэгдсэн сургуулиудтай хамтарна</p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary);">💎 Шударга</h4>
                        <p>Ил тод, шударга үйл ажиллагаа явуулна</p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary);">🤝 Дэмжлэг</h4>
                        <p>Оюутнуудыг эхнээс нь дуустал дагалдана</p>
                    </div>
                    <div>
                        <h4 style="color: var(--primary);">🌟 Амжилт</h4>
                        <p>Оюутнуудын амжилт бидний амжилт</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

