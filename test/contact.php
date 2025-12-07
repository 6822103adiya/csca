<?php
$pageTitle = 'Холбоо барих';
require_once 'includes/header.php';
?>

<section class="section" style="padding-top: calc(var(--header-height) + 4rem);">
    <div class="container">
        <div class="section-header">
            <h1><?= getSetting('contact_title', 'Холбоо барих') ?></h1>
            <p>Бидэнтэй холбогдох аргууд</p>
        </div>

        <div class="contact-grid" style="max-width: 1000px; margin: 0 auto;">
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <div class="contact-text">
                        <h4>Утас</h4>
                        <p><?= getSetting('phone', '+976 9999-9999') ?></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">📧</div>
                    <div class="contact-text">
                        <h4>Gmail</h4>
                        <p><?= getSetting('email', 'info@daam.mn') ?></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">📍</div>
                    <div class="contact-text">
                        <h4>Байршил</h4>
                        <p><?= getSetting('address', 'Улаанбаатар хот') ?></p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">🕐</div>
                    <div class="contact-text">
                        <h4>Ажлын цаг</h4>
                        <p><?= getSetting('contact_hours', 'Даваа - Баасан: 09:00 - 18:00') ?></p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Мессеж илгээх</h3>
                <form id="contactForm">
                    <div class="form-group">
                        <label class="form-label">Нэр <span class="required">*</span></label>
                        <input type="text" class="form-input" name="name" required placeholder="Таны нэр">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gmail <span class="required">*</span></label>
                        <input type="email" class="form-input" name="email" required placeholder="example@gmail.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Сэдэв</label>
                        <input type="text" class="form-input" name="subject" placeholder="Мессежийн сэдэв">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Мессеж <span class="required">*</span></label>
                        <textarea class="form-textarea" name="message" required placeholder="Таны мессеж..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Илгээх</button>
                </form>
            </div>
        </div>

        <!-- Map Placeholder -->
        <div class="card" style="margin-top: 3rem; padding: 0; overflow: hidden;">
            <div style="background: var(--bg-input); height: 300px; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                <div class="text-center">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🗺️</div>
                    <p>Google Maps энд харагдана</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

