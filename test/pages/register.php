<?php
/**
 * Тэтгэлэг Сэсэн - Бүртгэл
 */

$pageTitle = 'Бүртгүүлэх';
$pageScripts = ['register.js'];
require_once __DIR__ . '/../includes/header.php';

$bankAccount = getSetting('bank_account_number') ?? '5000123456';
$bankName = getSetting('bank_name') ?? 'Хаан Банк';
$accountName = getSetting('bank_account_name') ?? 'Тэтгэлэг Сэсэн ХХК';
$registrationFee = getSetting('registration_fee') ?? '50000';
?>

<section class="page-header">
    <div class="container">
        <h1>Бүртгүүлэх</h1>
        <p>Тэтгэлэгт хөтөлбөрт бүртгүүлэх маягт</p>
    </div>
</section>

<section class="register-content">
    <div class="container">
        
        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <span>Мэдээлэл</span>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <span>Баримт</span>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <span>Баталгаажуулалт</span>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <span>Төлбөр</span>
            </div>
        </div>
        
        <form id="registrationForm" class="registration-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <!-- Step 1: Personal Information -->
            <div class="form-step active" id="step1">
                <h2><i class="fas fa-user"></i> Хувийн мэдээлэл</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="last_name">Овог <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" required 
                               placeholder="Жишээ: Батболд">
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="first_name">Нэр <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" required
                               placeholder="Жишээ: Дорж">
                        <div class="error-message"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="register_number">Регистерийн дугаар <span class="required">*</span></label>
                    <input type="text" id="register_number" name="register_number" required
                           placeholder="Жишээ: АН12345678" maxlength="10"
                           pattern="[А-Яа-яЁёӨөҮү]{2}[0-9]{8}">
                    <div class="field-hint">2 кирилл үсэг + 8 тоо (жишээ: АН12345678)</div>
                    <div class="error-message"></div>
                </div>
                
                <div class="form-group">
                    <label for="home_address">Гэрийн хаяг <span class="required">*</span></label>
                    <textarea id="home_address" name="home_address" required rows="2"
                              placeholder="Дүүрэг, хороо, гудамж, байр, тоот"></textarea>
                    <div class="error-message"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="school">Сургууль <span class="required">*</span></label>
                        <input type="text" id="school" name="school" required
                               placeholder="Сургуулийн нэр">
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="grade">Анги <span class="required">*</span></label>
                        <select id="grade" name="grade" required>
                            <option value="">-- Сонгоно уу --</option>
                            <?php foreach (GRADES as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $value >= 11 ? 'data-eligible="true"' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="grade-notice" id="gradeNotice" style="display: none;">
                            <i class="fas fa-check-circle"></i> 11-12-р ангийнхан бүртгүүлэх боломжтой
                        </div>
                        <div class="error-message"></div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Утасны дугаар <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required
                               placeholder="99001122" maxlength="8" pattern="[0-9]{8}">
                        <div class="field-hint">8 оронтой тоо</div>
                        <div class="error-message"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Gmail хаяг <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required
                               placeholder="example@gmail.com">
                        <div class="field-hint">Зөвхөн Gmail хаяг</div>
                        <div class="error-message"></div>
                    </div>
                </div>
                
                <!-- Country Selection -->
                <div class="form-group">
                    <label>Сонгох улс(ууд) <span class="required">*</span></label>
                    <p class="field-description">1-3 улсыг сонгож болно. 3-аас илүү улс сонгож болохгүй.</p>
                    
                    <div class="country-selection" id="countrySelection">
                        <?php foreach (COUNTRIES as $code => $country): ?>
                        <label class="country-checkbox">
                            <input type="checkbox" name="countries[]" value="<?= $code ?>">
                            <span class="country-box">
                                <span class="country-flag"><?= $country['flag'] ?></span>
                                <span class="country-name"><?= $country['name'] ?></span>
                                <span class="check-icon"><i class="fas fa-check"></i></span>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="selected-count">Сонгосон: <span id="countryCount">0</span>/3</div>
                    <div class="error-message" id="countryError"></div>
                </div>
                
                <!-- Language Score -->
                <div class="form-group">
                    <label for="language_score">Хэлний оноо <span class="optional">(заавал биш)</span></label>
                    <input type="text" id="language_score" name="language_score"
                           placeholder="Жишээ: HSK 4, TOPIK 3, IELTS 6.5">
                    <div class="field-hint">Хэрэв хэлний шалгалтын оноотой бол бичнэ үү</div>
                </div>
                
                <!-- Additional Request -->
                <div class="form-group">
                    <label for="additional_request">Нэмэлт хүсэлт <span class="optional">(заавал биш)</span></label>
                    <textarea id="additional_request" name="additional_request" rows="3"
                              maxlength="300" placeholder="Нэмэлт мэдээлэл, хүсэлт..."></textarea>
                    <div class="char-count"><span id="charCount">0</span>/300</div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-primary btn-lg next-step" data-next="2">
                        Үргэлжлүүлэх <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Documents -->
            <div class="form-step" id="step2">
                <h2><i class="fas fa-file-upload"></i> Баримт бичгүүд</h2>
                
                <!-- ID Card Upload -->
                <div class="upload-section">
                    <h3>Иргэний үнэмлэхний зураг <span class="required">*</span></h3>
                    <p class="upload-description">
                        Бүх зураг тод, гэрэлтүүлэг сайтай, засваргүй байх ёстой. 
                        Формат: JPG, PNG. Хэмжээ: 5MB хүртэл.
                    </p>
                    
                    <div class="upload-grid">
                        <div class="upload-box" id="uploadIdFront">
                            <input type="file" name="id_front" id="id_front" accept="image/jpeg,image/png" required>
                            <div class="upload-placeholder">
                                <i class="fas fa-id-card"></i>
                                <span>Урд тал</span>
                                <small>Дарж зураг оруулна уу</small>
                            </div>
                            <div class="upload-preview"></div>
                        </div>
                        
                        <div class="upload-box" id="uploadIdBack">
                            <input type="file" name="id_back" id="id_back" accept="image/jpeg,image/png" required>
                            <div class="upload-placeholder">
                                <i class="fas fa-id-card"></i>
                                <span>Ард тал</span>
                                <small>Дарж зураг оруулна уу</small>
                            </div>
                            <div class="upload-preview"></div>
                        </div>
                        
                        <div class="upload-box" id="uploadIdSelfie">
                            <input type="file" name="id_selfie" id="id_selfie" accept="image/jpeg,image/png" required>
                            <div class="upload-placeholder">
                                <i class="fas fa-user-check"></i>
                                <span>Selfie + ID</span>
                                <small>Үнэмлэхтэй хамт</small>
                            </div>
                            <div class="upload-preview"></div>
                        </div>
                    </div>
                    <div class="error-message" id="idError"></div>
                </div>
                
                <!-- Certificate Upload (conditional) -->
                <div class="upload-section" id="certificateSection">
                    <h3>Хэлний сертификат <span id="certRequired" class="optional">(заавал биш)</span></h3>
                    <p class="upload-description">
                        Хэрэв хэлний оноотой бол сертификатын зураг эсвэл скан оруулна уу.
                        Формат: JPG, PNG, PDF. Хэмжээ: 5MB хүртэл.
                    </p>
                    
                    <div class="upload-box large" id="uploadCertificate">
                        <input type="file" name="certificate" id="certificate" accept="image/jpeg,image/png,application/pdf">
                        <div class="upload-placeholder">
                            <i class="fas fa-certificate"></i>
                            <span>Сертификат</span>
                            <small>Дарж файл оруулна уу</small>
                        </div>
                        <div class="upload-preview"></div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline btn-lg prev-step" data-prev="1">
                        <i class="fas fa-arrow-left"></i> Буцах
                    </button>
                    <button type="button" class="btn btn-primary btn-lg next-step" data-next="3">
                        Үргэлжлүүлэх <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 3: Verification -->
            <div class="form-step" id="step3">
                <h2><i class="fas fa-envelope"></i> Gmail баталгаажуулалт</h2>
                
                <div class="verification-section">
                    <div class="verification-info">
                        <i class="fas fa-paper-plane"></i>
                        <p>Бид таны <strong id="verifyEmail"></strong> хаяг руу 6 оронтой баталгаажуулах код илгээх болно.</p>
                    </div>
                    
                    <button type="button" class="btn btn-primary btn-lg" id="sendCodeBtn">
                        <i class="fas fa-envelope"></i> Код илгээх
                    </button>
                    
                    <div class="verification-code-section" id="codeSection" style="display: none;">
                        <label>Баталгаажуулах код</label>
                        <div class="code-inputs">
                            <input type="text" maxlength="1" class="code-input" data-index="0">
                            <input type="text" maxlength="1" class="code-input" data-index="1">
                            <input type="text" maxlength="1" class="code-input" data-index="2">
                            <input type="text" maxlength="1" class="code-input" data-index="3">
                            <input type="text" maxlength="1" class="code-input" data-index="4">
                            <input type="text" maxlength="1" class="code-input" data-index="5">
                        </div>
                        <input type="hidden" name="verification_code" id="verificationCode">
                        <p class="code-timer">Код <span id="codeTimer">10:00</span> минут хүчинтэй</p>
                        <button type="button" class="btn btn-link" id="resendCode">Дахин илгээх</button>
                    </div>
                </div>
                
                <!-- Terms -->
                <div class="terms-section">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms_accepted" id="terms_accepted" required>
                        <span class="checkmark"></span>
                        <span>Би <a href="<?= SITE_URL ?>/pages/terms.php" target="_blank">үйлчилгээний нөхцөл</a> болон 
                        <a href="<?= SITE_URL ?>/pages/privacy.php" target="_blank">нууцлалын бодлого</a>-г уншиж, 
                        зөвшөөрч байна. Мөн оруулсан мэдээлэл үнэн зөв гэдгийг баталж байна. <span class="required">*</span></span>
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline btn-lg prev-step" data-prev="2">
                        <i class="fas fa-arrow-left"></i> Буцах
                    </button>
                    <button type="button" class="btn btn-primary btn-lg" id="verifyBtn" disabled>
                        Баталгаажуулах <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 4: Payment -->
            <div class="form-step" id="step4">
                <h2><i class="fas fa-credit-card"></i> Төлбөр төлөх</h2>
                
                <div class="payment-info">
                    <div class="payment-notice">
                        <i class="fas fa-info-circle"></i>
                        <p>Та төлбөрийг доорх дансанд шилжүүлсний дараа админ 24 цагийн дотор бүртгэлийг баталгаажуулна. 
                        <strong>Гүйлгээний утга дээр овог, нэр, утасны дугаарыг заавал бичнэ үү.</strong></p>
                    </div>
                    
                    <div class="payment-details">
                        <div class="payment-item">
                            <label>Банкны нэр:</label>
                            <span><?= escape($bankName) ?></span>
                        </div>
                        <div class="payment-item">
                            <label>Дансны дугаар:</label>
                            <span id="accountNumber"><?= escape($bankAccount) ?></span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('<?= escape($bankAccount) ?>', this)">
                                <i class="fas fa-copy"></i> Хуулах
                            </button>
                        </div>
                        <div class="payment-item">
                            <label>Данс эзэмшигч:</label>
                            <span id="accountName"><?= escape($accountName) ?></span>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('<?= escape($accountName) ?>', this)">
                                <i class="fas fa-copy"></i> Хуулах
                            </button>
                        </div>
                        <div class="payment-item highlight">
                            <label>Төлбөрийн дүн:</label>
                            <span class="amount"><?= number_format((int)$registrationFee) ?>₮</span>
                        </div>
                        <div class="payment-item">
                            <label>Гүйлгээний утга:</label>
                            <span id="transferNote" class="transfer-note"></span>
                            <button type="button" class="copy-btn" id="copyTransferNote">
                                <i class="fas fa-copy"></i> Хуулах
                            </button>
                        </div>
                    </div>
                    
                    <!-- Payment Receipt Upload (optional) -->
                    <div class="upload-section">
                        <h3>Гүйлгээний баримт <span class="optional">(заавал биш)</span></h3>
                        <p class="upload-description">Банкны гүйлгээний баримтын зургийг оруулж болно.</p>
                        
                        <div class="upload-box large" id="uploadReceipt">
                            <input type="file" name="payment_receipt" id="payment_receipt" accept="image/jpeg,image/png">
                            <div class="upload-placeholder">
                                <i class="fas fa-receipt"></i>
                                <span>Гүйлгээний баримт</span>
                                <small>Дарж зураг оруулна уу</small>
                            </div>
                            <div class="upload-preview"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline btn-lg prev-step" data-prev="3">
                        <i class="fas fa-arrow-left"></i> Буцах
                    </button>
                    <button type="button" class="btn btn-primary btn-lg" id="confirmPaymentBtn">
                        <i class="fas fa-check"></i> Би төлбөр хийлээ
                    </button>
                </div>
            </div>
            
            <!-- Success Step -->
            <div class="form-step" id="stepSuccess">
                <div class="success-section">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>Баярлалаа!</h2>
                    <p>Таны бүртгэл амжилттай илгээгдлээ.</p>
                    <p class="success-note">
                        Төлбөр илгээсэн тохиолдолд админ 24 цагийн дотор таны бүртгэлийг баталгаажуулна. 
                        Гүйлгээний утгад овог, нэр, утасны дугаарыг бичсэн эсэхээ шалгана уу.
                    </p>
                    <p>Бид таны Gmail рүү мэдэгдэл илгээх болно.</p>
                    <a href="<?= SITE_URL ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-home"></i> Нүүр хуудас руу
                    </a>
                </div>
            </div>
            
        </form>
    </div>
</section>

<!-- Confirm Payment Dialog -->
<div class="modal" id="confirmModal">
    <div class="modal-content">
        <h3><i class="fas fa-question-circle"></i> Баталгаажуулах</h3>
        <p>Гүйлгээний утга дээр овог, нэр, утасны дугаарыг бичсэн үү?</p>
        <div class="modal-actions">
            <button type="button" class="btn btn-outline" id="modalNo">Үгүй</button>
            <button type="button" class="btn btn-primary" id="modalYes">Тийм</button>
        </div>
    </div>
</div>

<style>
.register-content {
    padding: 40px 0 80px;
}

/* Progress Steps */
.progress-steps {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    opacity: 0.5;
    transition: var(--transition);
}

.step.active,
.step.completed {
    opacity: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    border: 2px solid var(--border-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    background: var(--bg-secondary);
    transition: var(--transition);
}

.step.active .step-number {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--text-light);
}

.step.completed .step-number {
    background: #28a745;
    border-color: #28a745;
    color: var(--text-light);
}

.step span {
    font-size: 0.85rem;
    color: var(--text-secondary);
}

/* Form */
.registration-form {
    max-width: 800px;
    margin: 0 auto;
}

.form-step {
    display: none;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
    animation: fadeIn 0.3s ease;
}

.form-step.active {
    display: block;
}

.form-step h2 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-family: var(--font-display);
    font-size: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.form-step h2 i {
    color: var(--primary);
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

.optional {
    color: var(--text-muted);
    font-weight: 400;
    font-size: 0.9rem;
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

.form-group input.error,
.form-group select.error,
.form-group textarea.error {
    border-color: #dc3545;
}

.field-hint {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

.field-description {
    color: var(--text-secondary);
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.error-message {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 0.25rem;
    min-height: 1.2rem;
}

.grade-notice {
    color: #28a745;
    font-size: 0.9rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.char-count {
    text-align: right;
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

/* Country Selection */
.country-selection {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
}

.country-checkbox {
    cursor: pointer;
}

.country-checkbox input {
    display: none;
}

.country-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.25rem;
    background: var(--bg-primary);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    transition: var(--transition);
    position: relative;
}

.country-box:hover {
    border-color: var(--primary);
}

.country-checkbox input:checked + .country-box {
    border-color: var(--primary);
    background: rgba(255, 165, 0, 0.1);
}

.country-flag {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.country-name {
    font-weight: 500;
    color: var(--text-primary);
}

.check-icon {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 24px;
    height: 24px;
    background: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: scale(0);
    transition: var(--transition);
}

.check-icon i {
    color: var(--text-light);
    font-size: 0.75rem;
}

.country-checkbox input:checked + .country-box .check-icon {
    opacity: 1;
    transform: scale(1);
}

.selected-count {
    margin-top: 1rem;
    font-weight: 500;
    color: var(--text-secondary);
}

/* Upload Section */
.upload-section {
    margin: 2rem 0;
    padding: 1.5rem;
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.upload-section h3 {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.upload-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.upload-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.upload-box {
    position: relative;
    border: 2px dashed var(--border-color);
    border-radius: var(--radius-lg);
    padding: 2rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-box:hover {
    border-color: var(--primary);
    background: rgba(255, 165, 0, 0.05);
}

.upload-box input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.upload-placeholder i {
    font-size: 2rem;
    color: var(--primary);
}

.upload-placeholder span {
    font-weight: 500;
    color: var(--text-primary);
}

.upload-placeholder small {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.upload-box.large {
    max-width: 300px;
}

.upload-box.has-file {
    border-style: solid;
    border-color: var(--primary);
}

.upload-preview {
    display: none;
}

.upload-box.has-file .upload-placeholder {
    display: none;
}

.upload-box.has-file .upload-preview {
    display: block;
}

.upload-preview img {
    max-width: 100%;
    max-height: 120px;
    border-radius: var(--radius);
}

.upload-preview .file-name {
    font-size: 0.85rem;
    color: var(--text-secondary);
    word-break: break-all;
}

/* Verification Section */
.verification-section {
    text-align: center;
    padding: 2rem;
}

.verification-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.verification-info i {
    font-size: 3rem;
    color: var(--primary);
}

.verification-info p {
    color: var(--text-secondary);
    max-width: 400px;
}

.verification-code-section {
    margin-top: 2rem;
}

.code-inputs {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
    margin: 1rem 0;
}

.code-input {
    width: 50px;
    height: 60px;
    text-align: center;
    font-size: 1.5rem;
    font-weight: 600;
    border: 2px solid var(--border-color);
    border-radius: var(--radius);
    background: var(--bg-primary);
    color: var(--text-primary);
}

.code-input:focus {
    border-color: var(--primary);
    outline: none;
}

.code-timer {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.btn-link {
    background: none;
    border: none;
    color: var(--primary);
    cursor: pointer;
    text-decoration: underline;
}

/* Terms Section */
.terms-section {
    margin: 2rem 0;
    padding: 1.5rem;
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    cursor: pointer;
}

.checkbox-label input {
    display: none;
}

.checkmark {
    width: 24px;
    height: 24px;
    min-width: 24px;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.checkbox-label input:checked + .checkmark {
    background: var(--primary);
    border-color: var(--primary);
}

.checkbox-label input:checked + .checkmark::after {
    content: '✓';
    color: var(--text-light);
    font-weight: bold;
}

.checkbox-label span:last-child {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.6;
}

.checkbox-label a {
    color: var(--primary);
}

/* Payment Section */
.payment-info {
    padding: 1rem;
}

.payment-notice {
    display: flex;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(255, 165, 0, 0.1);
    border: 2px solid var(--primary);
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
}

.payment-notice i {
    font-size: 1.5rem;
    color: var(--primary);
}

.payment-notice p {
    color: var(--text-secondary);
    line-height: 1.6;
}

.payment-details {
    background: var(--bg-primary);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.payment-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
    gap: 1rem;
    flex-wrap: wrap;
}

.payment-item:last-child {
    border-bottom: none;
}

.payment-item label {
    min-width: 150px;
    font-weight: 500;
    color: var(--text-secondary);
}

.payment-item span {
    flex: 1;
    font-weight: 600;
    color: var(--text-primary);
}

.payment-item.highlight {
    background: rgba(255, 165, 0, 0.1);
    margin: 0 -1.5rem;
    padding: 1rem 1.5rem;
    border-radius: var(--radius);
}

.payment-item .amount {
    font-size: 1.5rem;
    color: var(--primary);
}

.transfer-note {
    font-family: monospace;
}

.copy-btn {
    padding: 0.5rem 1rem;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    cursor: pointer;
    font-size: 0.85rem;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.copy-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.copy-btn.copied {
    background: #28a745;
    color: var(--text-light);
    border-color: #28a745;
}

/* Success Section */
.success-section {
    text-align: center;
    padding: 3rem;
}

.success-icon {
    font-size: 5rem;
    color: #28a745;
    margin-bottom: 1.5rem;
    animation: scaleIn 0.5s ease;
}

.success-section h2 {
    justify-content: center;
    border: none;
}

.success-section p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.success-note {
    background: rgba(255, 165, 0, 0.1);
    padding: 1rem;
    border-radius: var(--radius);
    margin: 1.5rem 0;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.form-actions .btn {
    min-width: 180px;
}

/* Modal */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: var(--bg-primary);
    border-radius: var(--radius-xl);
    padding: 2rem;
    max-width: 400px;
    text-align: center;
}

.modal-content h3 {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.modal-content h3 i {
    color: var(--primary);
}

.modal-content p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scaleIn {
    from { transform: scale(0); }
    to { transform: scale(1); }
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .upload-grid {
        grid-template-columns: 1fr;
    }
    
    .progress-steps {
        gap: 1rem;
    }
    
    .step span {
        display: none;
    }
    
    .code-inputs {
        gap: 0.5rem;
    }
    
    .code-input {
        width: 40px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
    
    .payment-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .payment-item label {
        min-width: auto;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

