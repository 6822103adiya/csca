<?php
$pageTitle = 'Бүртгүүлэх';
require_once 'includes/header.php';

$errors = [];
$success = false;

// Форм илгээгдсэн бол
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDB();
    
    // Улсын сонголт
    $countries = isset($_POST['countries']) ? $_POST['countries'] : [];
    if (empty($countries)) {
        $errors['countries'] = 'Дор хаяж 1 улс сонгоно уу.';
    } elseif (count($countries) > 3) {
        $errors['countries'] = 'Хамгийн ихдээ 3 улс сонгох боломжтой.';
    }
    
    // Овог
    $lastName = trim($_POST['last_name'] ?? '');
    if (empty($lastName)) {
        $errors['last_name'] = 'Овог оруулна уу.';
    }
    
    // Нэр
    $firstName = trim($_POST['first_name'] ?? '');
    if (empty($firstName)) {
        $errors['first_name'] = 'Нэр оруулна уу.';
    }
    
    // Регистрийн дугаар
    $registerNumber = mb_strtoupper(trim($_POST['register_number'] ?? ''), 'UTF-8');
    if (empty($registerNumber)) {
        $errors['register_number'] = 'Регистрийн дугаар оруулна уу.';
    } elseif (!validateRegisterNumber($registerNumber)) {
        $errors['register_number'] = 'Регистрийн дугаар буруу байна. (Жишээ: АБ12345678)';
    } else {
        // Давхардал шалгах
        $checkStmt = $pdo->prepare("SELECT id FROM registrations WHERE register_number = ?");
        $checkStmt->execute([$registerNumber]);
        if ($checkStmt->fetch()) {
            $errors['register_number'] = 'Энэ регистрийн дугаар бүртгэгдсэн байна.';
        }
    }
    
    // Хаяг
    $address = trim($_POST['address'] ?? '');
    if (empty($address)) {
        $errors['address'] = 'Гэрийн хаяг оруулна уу.';
    }
    
    // Сургууль
    $school = trim($_POST['school'] ?? '');
    if (empty($school)) {
        $errors['school'] = 'Сургуулийн нэр оруулна уу.';
    }
    
    // Анги
    $grade = (int)($_POST['grade'] ?? 0);
    if (!validateGrade($grade)) {
        $errors['grade'] = 'Зөвхөн 11 эсвэл 12-р анги байна.';
    }
    
    // Хэлний оноо (optional) - сертификат ч заавал биш
    $languageScore = trim($_POST['language_score'] ?? '');
    $languageCertificate = null;
    
    // Сертификат байвал upload хийнэ, байхгүй ч болно
    if (isset($_FILES['language_certificate']) && $_FILES['language_certificate']['error'] === 0) {
        $upload = uploadFile($_FILES['language_certificate']);
        if ($upload['success']) {
            $languageCertificate = $upload['filename'];
        } else {
            $errors['language_certificate'] = $upload['error'];
        }
    }
    
    // Иргэний үнэмлэх - Урд
    if (!isset($_FILES['id_front']) || $_FILES['id_front']['error'] !== 0) {
        $errors['id_front'] = 'Иргэний үнэмлэхний урд талын зураг оруулна уу.';
    } else {
        $upload = uploadFile($_FILES['id_front'], ['jpg', 'jpeg', 'png']);
        if ($upload['success']) {
            $idFront = $upload['filename'];
        } else {
            $errors['id_front'] = $upload['error'];
        }
    }
    
    // Иргэний үнэмлэх - Ард
    if (!isset($_FILES['id_back']) || $_FILES['id_back']['error'] !== 0) {
        $errors['id_back'] = 'Иргэний үнэмлэхний ар талын зураг оруулна уу.';
    } else {
        $upload = uploadFile($_FILES['id_back'], ['jpg', 'jpeg', 'png']);
        if ($upload['success']) {
            $idBack = $upload['filename'];
        } else {
            $errors['id_back'] = $upload['error'];
        }
    }
    
    // Selfie
    if (!isset($_FILES['id_selfie']) || $_FILES['id_selfie']['error'] !== 0) {
        $errors['id_selfie'] = 'Иргэний үнэмлэх барьсан selfie зураг оруулна уу.';
    } else {
        $upload = uploadFile($_FILES['id_selfie'], ['jpg', 'jpeg', 'png']);
        if ($upload['success']) {
            $idSelfie = $upload['filename'];
        } else {
            $errors['id_selfie'] = $upload['error'];
        }
    }
    
    // Утасны дугаар
    $phone = trim($_POST['phone'] ?? '');
    if (!validatePhone($phone)) {
        $errors['phone'] = 'Утасны дугаар 8 оронтой байх ёстой.';
    } else {
        // Давхардал шалгах
        $checkStmt = $pdo->prepare("SELECT id FROM registrations WHERE phone = ?");
        $checkStmt->execute([$phone]);
        if ($checkStmt->fetch()) {
            $errors['phone'] = 'Энэ утасны дугаар бүртгэгдсэн байна.';
        }
    }
    
    // Gmail
    $email = trim($_POST['email'] ?? '');
    if (!validateGmail($email)) {
        $errors['email'] = 'Зөвхөн @gmail.com хаяг оруулна уу.';
    } else {
        // Давхардал шалгах
        $checkStmt = $pdo->prepare("SELECT id FROM registrations WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $errors['email'] = 'Энэ Gmail хаяг бүртгэгдсэн байна.';
        }
    }
    
    // Алдаа байхгүй бол хадгалах
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO registrations 
                (countries, last_name, first_name, register_number, address, school, grade, 
                language_score, language_certificate, id_front, id_back, id_selfie, phone, email, email_verified) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            
            $stmt->execute([
                json_encode($countries),
                $lastName,
                $firstName,
                $registerNumber,
                $address,
                $school,
                $grade,
                $languageScore,
                $languageCertificate,
                $idFront ?? '',
                $idBack ?? '',
                $idSelfie ?? '',
                $phone,
                $email
            ]);
            
            $success = true;
            $registrationId = $pdo->lastInsertId();
            
        } catch (PDOException $e) {
            $errors['general'] = 'Бүртгэл хадгалахад алдаа гарлаа. Дахин оролдоно уу.';
        }
    }
}
?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="container">
        <div class="section-header">
            <h1>Тэтгэлэгт бүртгүүлэх</h1>
            <p>Бүх талбарыг үнэн зөв бөглөнө үү</p>
        </div>

        <?php if ($success): ?>
        <div class="register-form">
            <div class="alert alert-success" style="margin-bottom: 2rem;">
                ✅ Таны бүртгэл амжилттай хадгалагдлаа!
            </div>
            
            <div class="payment-section">
                <div class="payment-info">
                    <h3>💳 Төлбөрийн мэдээлэл</h3>
                    <p style="margin-top: 1rem;">
                        Та төлбөрийг доорх дансанд шилжүүлсний дараа админ 24 цагийн дотор бүртгэлийг баталгаажуулна.
                    </p>
                    <p style="color: var(--warning);">
                        ⚠️ Гүйлгээний утга дээр <strong>овог, нэр, утасны дугаар</strong> бичнэ үү.
                    </p>
                </div>
                
                <div class="bank-details">
                    <div class="bank-detail-row">
                        <span class="bank-detail-label">Банкны нэр:</span>
                        <span class="bank-detail-value"><?= getSetting('bank_name', 'Хаан банк') ?></span>
                    </div>
                    <div class="bank-detail-row">
                        <span class="bank-detail-label">Дансны дугаар:</span>
                        <span class="bank-detail-value">
                            <span id="bankAccount"><?= getSetting('bank_account', '5000123456') ?></span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('bankAccount')">Copy</button>
                        </span>
                    </div>
                    <div class="bank-detail-row">
                        <span class="bank-detail-label">Данс эзэмшигч:</span>
                        <span class="bank-detail-value"><?= getSetting('account_holder', 'Даам ХХК') ?></span>
                    </div>
                    <div class="bank-detail-row">
                        <span class="bank-detail-label">Төлбөрийн дүн:</span>
                        <span class="bank-detail-value" style="color: var(--primary); font-size: 1.25rem;">
                            <?= number_format((int)getSetting('registration_fee', '50000')) ?>₮
                        </span>
                    </div>
                </div>
                
                <form action="process_payment.php" method="POST" style="text-align: center; margin-top: 1.5rem;">
                    <input type="hidden" name="registration_id" value="<?= $registrationId ?>">
                    <button type="submit" class="btn btn-primary btn-lg">✅ Гүйлгээ хийсэн</button>
                </form>
            </div>
        </div>
        <?php else: ?>

        <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error">
            ❌ <?= $errors['general'] ?>
        </div>
        <?php endif; ?>

        <form class="register-form" method="POST" enctype="multipart/form-data" id="registerForm">
            
            <!-- Улсын сонголт -->
            <div class="form-section">
                <h3 class="form-section-title">🌍 Улсын сонголт</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1rem;">1-3 улс сонгоно уу (4-өөс дээш сонгох боломжгүй)</p>
                
                <div class="checkbox-group" id="countrySelect">
                    <?php 
                    $availableCountries = getActiveCountries();
                    foreach ($availableCountries as $country): 
                    ?>
                    <label class="checkbox-item" data-country="<?= $country['code'] ?>">
                        <input type="checkbox" name="countries[]" value="<?= $country['code'] ?>">
                        <span class="checkbox-custom"></span>
                        <span><?= $country['flag'] ?> <?= clean($country['name']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($errors['countries'])): ?>
                <div class="form-error">❌ <?= $errors['countries'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Хувийн мэдээлэл -->
            <div class="form-section">
                <h3 class="form-section-title">👤 Хувийн мэдээлэл</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Овог <span class="required">*</span></label>
                        <input type="text" class="form-input" name="last_name" required 
                               value="<?= clean($_POST['last_name'] ?? '') ?>" placeholder="Овог">
                        <?php if (!empty($errors['last_name'])): ?>
                        <div class="form-error">❌ <?= $errors['last_name'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Нэр <span class="required">*</span></label>
                        <input type="text" class="form-input" name="first_name" required 
                               value="<?= clean($_POST['first_name'] ?? '') ?>" placeholder="Нэр">
                        <?php if (!empty($errors['first_name'])): ?>
                        <div class="form-error">❌ <?= $errors['first_name'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Регистрийн дугаар <span class="required">*</span></label>
                        <input type="text" class="form-input" name="register_number" required 
                               value="<?= clean($_POST['register_number'] ?? '') ?>" 
                               placeholder="АБ12345678" maxlength="10"
                               style="text-transform: uppercase;">
                        <small style="color: var(--text-muted);">2 кирилл үсэг + 8 тоо (Жишээ: АН12345678)</small>
                        <?php if (!empty($errors['register_number'])): ?>
                        <div class="form-error">❌ <?= $errors['register_number'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Анги <span class="required">*</span></label>
                        <select class="form-select" name="grade" required>
                            <option value="">Анги сонгох</option>
                            <option value="11" <?= ($_POST['grade'] ?? '') == '11' ? 'selected' : '' ?>>11-р анги</option>
                            <option value="12" <?= ($_POST['grade'] ?? '') == '12' ? 'selected' : '' ?>>12-р анги</option>
                        </select>
                        <?php if (!empty($errors['grade'])): ?>
                        <div class="form-error">❌ <?= $errors['grade'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Гэрийн хаяг <span class="required">*</span></label>
                    <textarea class="form-textarea" name="address" required 
                              placeholder="Хот/Аймаг, Дүүрэг/Сум, Хороо/Баг, Байр/Гудамж..."><?= clean($_POST['address'] ?? '') ?></textarea>
                    <?php if (!empty($errors['address'])): ?>
                    <div class="form-error">❌ <?= $errors['address'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Сургууль <span class="required">*</span></label>
                    <input type="text" class="form-input" name="school" required 
                           value="<?= clean($_POST['school'] ?? '') ?>" placeholder="Сургуулийн нэр">
                    <?php if (!empty($errors['school'])): ?>
                    <div class="form-error">❌ <?= $errors['school'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Хэлний оноо -->
            <div class="form-section">
                <h3 class="form-section-title">📝 Хэлний оноо (заавал биш)</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Хэлний оноо</label>
                        <input type="text" class="form-input" name="language_score" 
                               value="<?= clean($_POST['language_score'] ?? '') ?>" 
                               placeholder="Жишээ: HSK4, TOPIK3, B2...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Сертификатын зураг (заавал биш)</label>
                        <div class="file-upload">
                            <input type="file" name="language_certificate" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="file-upload-icon">📄</div>
                            <div class="file-upload-text">
                                <span>Файл сонгох</span> эсвэл энд чирнэ үү
                                <br><small>JPG, PNG, PDF (5MB хүртэл)</small>
                            </div>
                        </div>
                        <?php if (!empty($errors['language_certificate'])): ?>
                        <div class="form-error">❌ <?= $errors['language_certificate'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Иргэний үнэмлэх -->
            <div class="form-section">
                <h3 class="form-section-title">🪪 Иргэний үнэмлэхний мэдээлэл</h3>
                
                <div class="id-cards-grid">
                    <div class="form-group">
                        <label class="form-label">Урд тал <span class="required">*</span></label>
                        <div class="file-upload">
                            <input type="file" name="id_front" accept=".jpg,.jpeg,.png" required>
                            <div class="file-upload-icon">📸</div>
                            <div class="file-upload-text">
                                <span>Урд зураг</span>
                            </div>
                        </div>
                        <?php if (!empty($errors['id_front'])): ?>
                        <div class="form-error">❌ <?= $errors['id_front'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ар тал <span class="required">*</span></label>
                        <div class="file-upload">
                            <input type="file" name="id_back" accept=".jpg,.jpeg,.png" required>
                            <div class="file-upload-icon">📸</div>
                            <div class="file-upload-text">
                                <span>Ар зураг</span>
                            </div>
                        </div>
                        <?php if (!empty($errors['id_back'])): ?>
                        <div class="form-error">❌ <?= $errors['id_back'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Selfie <span class="required">*</span></label>
                        <div class="file-upload">
                            <input type="file" name="id_selfie" accept=".jpg,.jpeg,.png" required>
                            <div class="file-upload-icon">🤳</div>
                            <div class="file-upload-text">
                                <span>Үнэмлэх барьсан</span>
                            </div>
                        </div>
                        <?php if (!empty($errors['id_selfie'])): ?>
                        <div class="form-error">❌ <?= $errors['id_selfie'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Холбоо барих -->
            <div class="form-section">
                <h3 class="form-section-title">📱 Холбоо барих</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Утасны дугаар <span class="required">*</span></label>
                        <input type="tel" class="form-input" name="phone" required 
                               value="<?= clean($_POST['phone'] ?? '') ?>" 
                               placeholder="99999999" maxlength="8" pattern="[0-9]{8}">
                        <small style="color: var(--text-muted);">8 оронтой тоо</small>
                        <?php if (!empty($errors['phone'])): ?>
                        <div class="form-error">❌ <?= $errors['phone'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gmail хаяг <span class="required">*</span></label>
                        <input type="email" class="form-input" name="email" required
                               value="<?= clean($_POST['email'] ?? '') ?>" 
                               placeholder="example@gmail.com">
                        <small style="color: var(--text-muted);">Зөвхөн @gmail.com</small>
                        <?php if (!empty($errors['email'])): ?>
                        <div class="form-error">❌ <?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Бүртгүүлэх</button>
        </form>
        <?php endif; ?>
    </div>
</section>

<script>
// Улсын сонголт - checkbox handler
document.querySelectorAll('#countrySelect .checkbox-item').forEach(item => {
    const checkbox = item.querySelector('input[type="checkbox"]');
    
    checkbox.addEventListener('change', function() {
        const selected = document.querySelectorAll('#countrySelect input:checked').length;
        
        if (selected > 3) {
            this.checked = false;
            alert('Хамгийн ихдээ 3 улс сонгох боломжтой!');
            return;
        }
        
        item.classList.toggle('selected', this.checked);
    });
    
    // Initial state
    if (checkbox.checked) {
        item.classList.add('selected');
    }
});

// File upload preview
document.querySelectorAll('.file-upload input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        const fileName = this.files[0]?.name;
        const textEl = this.closest('.file-upload').querySelector('.file-upload-text');
        if (fileName) {
            textEl.innerHTML = `<span style="color: var(--success);">✓ ${fileName}</span>`;
        }
    });
});

// Copy to clipboard
function copyToClipboard(elementId) {
    const text = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('Хуулагдлаа!');
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
