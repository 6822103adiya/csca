<?php
/**
 * Тэтгэлэг Сэсэн - Холбоо барих
 */

$pageTitle = 'Холбоо барих';
require_once __DIR__ . '/../includes/header.php';

$contactPhone = getSetting('contact_phone') ?? '99001122';
$contactEmail = getSetting('contact_email') ?? 'info@tetgeleg.mn';
?>

<section class="page-header">
    <div class="container">
        <h1>Холбоо барих</h1>
        <p>Асуулт байвал бидэнтэй холбогдоорой</p>
    </div>
</section>

<section class="contact-content">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info-section">
                <h2>Холбогдох мэдээлэл</h2>
                <p>Бид таны асуултад хариулахад бэлэн байна. Доорх сувгуудаар бидэнтэй холбогдоорой.</p>
                
                <div class="contact-cards">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>Утас</h3>
                        <p><a href="tel:+976<?= $contactPhone ?>"><?= $contactPhone ?></a></p>
                        <span>Даваа - Баасан: 09:00 - 18:00</span>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>И-мэйл</h3>
                        <p><a href="mailto:<?= $contactEmail ?>"><?= $contactEmail ?></a></p>
                        <span>24 цагийн дотор хариулна</span>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Хаяг</h3>
                        <p>Улаанбаатар хот</p>
                        <span>Сүхбаатар дүүрэг</span>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fab fa-facebook-f"></i>
                        </div>
                        <h3>Facebook</h3>
                        <p><a href="#">Тэтгэлэг Сэсэн</a></p>
                        <span>Мессенжер хүлээн авна</span>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-section">
                <h2>Мессеж илгээх</h2>
                <form id="contactForm" class="contact-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Нэр <span class="required">*</span></label>
                            <input type="text" id="name" name="name" required placeholder="Таны нэр">
                        </div>
                        <div class="form-group">
                            <label for="email">И-мэйл <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required placeholder="example@gmail.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Утасны дугаар</label>
                        <input type="tel" id="phone" name="phone" placeholder="99001122">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Сэдэв <span class="required">*</span></label>
                        <select id="subject" name="subject" required>
                            <option value="">-- Сонгоно уу --</option>
                            <option value="registration">Бүртгэлийн асуулт</option>
                            <option value="scholarship">Тэтгэлэгийн мэдээлэл</option>
                            <option value="payment">Төлбөрийн асуулт</option>
                            <option value="document">Баримт бичгийн асуулт</option>
                            <option value="other">Бусад</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Мессеж <span class="required">*</span></label>
                        <textarea id="message" name="message" rows="5" required placeholder="Таны мессеж..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Илгээх
                    </button>
                </form>
            </div>
        </div>
        
        <!-- FAQ Quick Links -->
        <div class="contact-faq">
            <h3>Түгээмэл асуултууд</h3>
            <div class="faq-quick">
                <a href="<?= SITE_URL ?>/#faq" class="faq-link">
                    <i class="fas fa-question-circle"></i>
                    Хэрхэн бүртгүүлэх вэ?
                </a>
                <a href="<?= SITE_URL ?>/#faq" class="faq-link">
                    <i class="fas fa-question-circle"></i>
                    Төлбөр хэд вэ?
                </a>
                <a href="<?= SITE_URL ?>/#faq" class="faq-link">
                    <i class="fas fa-question-circle"></i>
                    Ямар баримт шаардлагатай?
                </a>
                <a href="<?= SITE_URL ?>/pages/programs.php" class="faq-link">
                    <i class="fas fa-question-circle"></i>
                    Хөтөлбөрийн мэдээлэл
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.contact-content {
    padding: 60px 0;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
}

.contact-info-section h2,
.contact-form-section h2 {
    font-family: var(--font-display);
    font-size: 1.75rem;
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.contact-info-section > p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

.contact-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.contact-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    text-align: center;
    transition: var(--transition);
}

.contact-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
}

.contact-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.contact-icon i {
    font-size: 1.5rem;
    color: var(--text-light);
}

.contact-card h3 {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.contact-card p {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.contact-card p a {
    color: var(--primary);
}

.contact-card span {
    font-size: 0.85rem;
    color: var(--text-muted);
}

/* Contact Form */
.contact-form {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-primary);
}

.required {
    color: #dc3545;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius);
    background: var(--bg-primary);
    color: var(--text-primary);
    font-size: 1rem;
    transition: var(--transition);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

/* FAQ Quick */
.contact-faq {
    margin-top: 60px;
    text-align: center;
}

.contact-faq h3 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
}

.faq-quick {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
}

.faq-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    color: var(--text-secondary);
    transition: var(--transition);
}

.faq-link:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.faq-link i {
    color: var(--primary);
}

@media (max-width: 992px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .contact-cards {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Simple validation
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value.trim();
    
    if (!name || !email || !subject || !message) {
        alert('Бүх шаардлагатай талбаруудыг бөглөнө үү.');
        return;
    }
    
    // Show success message (in production, this would be an AJAX call)
    alert('Таны мессеж амжилттай илгээгдлээ! Бид удахгүй хариу өгөх болно.');
    this.reset();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

