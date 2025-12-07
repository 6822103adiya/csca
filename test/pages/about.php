<?php
/**
 * Тэтгэлэг Сэсэн - Бидний тухай
 */

$pageTitle = 'Бидний тухай';
require_once __DIR__ . '/../includes/header.php';

$content = getPageContent('about');
?>

<section class="page-header">
    <div class="container">
        <h1>Бидний тухай</h1>
        <p>Тэтгэлэг Сэсэн - Гадаадад суралцах боломжийг олгоно</p>
    </div>
</section>

<section class="about-content">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <h2>Бид хэн бэ?</h2>
                <p>Тэтгэлэг Сэсэн нь Монголын залуучуудад гадаадад суралцах боломжийг олгох зорилготой байгууллага юм. Бид 2020 оноос хойш олон зуун оюутанд тэтгэлэг авахад нь туслаж ирсэн.</p>
                
                <h3>Бидний зорилго</h3>
                <p>Монголын залуучуудад дэлхийн шилдэг их сургуулиудад суралцах боломжийг олгож, тэдний ирээдүйг гэрэлтүүлэх.</p>
                
                <h3>Бидний үнэт зүйлс</h3>
                <ul class="values-list">
                    <li><i class="fas fa-check-circle"></i> Шударга байдал</li>
                    <li><i class="fas fa-check-circle"></i> Мэргэжлийн түвшин</li>
                    <li><i class="fas fa-check-circle"></i> Хариуцлага</li>
                    <li><i class="fas fa-check-circle"></i> Оюутан төвтэй үйлчилгээ</li>
                </ul>
            </div>
            <div class="about-image">
                <div class="stats-card">
                    <div class="stat">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Амжилттай оюутан</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">4</span>
                        <span class="stat-label">Улс</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Түнш их сургууль</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">5</span>
                        <span class="stat-label">Жилийн туршлага</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="team-section">
            <h2>Манай баг</h2>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">👩‍💼</div>
                    <h3>Б. Сарангэрэл</h3>
                    <p>Гүйцэтгэх захирал</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👨‍🏫</div>
                    <h3>Д. Батбаяр</h3>
                    <p>Сургалтын менежер</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👩‍💻</div>
                    <h3>Э. Оюунчимэг</h3>
                    <p>Зөвлөх</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.page-header {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: var(--text-light);
    padding: 120px 0 60px;
    text-align: center;
}

.page-header h1 {
    font-family: var(--font-display);
    font-size: clamp(2rem, 4vw, 3rem);
    margin-bottom: 1rem;
}

.page-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.about-content {
    padding: 80px 0;
}

.about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: start;
}

.about-text h2 {
    font-family: var(--font-display);
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 1.5rem;
}

.about-text h3 {
    font-size: 1.25rem;
    margin: 2rem 0 1rem;
    color: var(--text-primary);
}

.about-text p {
    color: var(--text-secondary);
    line-height: 1.8;
    margin-bottom: 1rem;
}

.values-list {
    list-style: none;
}

.values-list li {
    padding: 0.75rem 0;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.values-list i {
    color: var(--primary);
    font-size: 1.25rem;
}

.stats-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.stat {
    text-align: center;
    padding: 1.5rem;
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary);
}

.stat-label {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.team-section {
    margin-top: 80px;
    text-align: center;
}

.team-section h2 {
    font-family: var(--font-display);
    font-size: 2rem;
    margin-bottom: 3rem;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    max-width: 800px;
    margin: 0 auto;
}

.team-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
    transition: var(--transition);
}

.team-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.team-avatar {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.team-card h3 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.team-card p {
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .about-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

