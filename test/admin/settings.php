<?php
require_once __DIR__ . '/../config/functions.php';
requireAdmin();

$pdo = getDB();
$message = '';
$messageType = '';

// POST хүсэлт
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'save_settings';
    
    // Тохиргоо хадгалах
    if ($action === 'save_settings') {
        $settings = [
            'site_name', 'site_description', 'phone', 'email', 'address',
            'bank_account', 'bank_name', 'account_holder', 'registration_fee',
            'home_title', 'home_description',
            'about_title', 'about_content',
            'contact_title', 'contact_hours'
        ];
        
        foreach ($settings as $key) {
            if (isset($_POST[$key])) {
                saveSetting($key, trim($_POST[$key]));
            }
        }
        
        $message = 'Тохиргоо амжилттай хадгалагдлаа!';
        $messageType = 'success';
    }
    
    // Улс нэмэх
    elseif ($action === 'add_country') {
        $code = strtolower(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $flag = trim($_POST['flag'] ?? '🏳️');
        $description = trim($_POST['description'] ?? '');
        $tuition = trim($_POST['tuition'] ?? '');
        $accommodation = trim($_POST['accommodation'] ?? '');
        $language = trim($_POST['language'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        
        if (!empty($code) && !empty($name)) {
            $countries = getCountriesData();
            
            // Давхардал шалгах
            $exists = false;
            foreach ($countries as $c) {
                if ($c['code'] === $code) {
                    $exists = true;
                    break;
                }
            }
            
            if ($exists) {
                $message = 'Энэ кодтой улс бүртгэгдсэн байна!';
                $messageType = 'error';
            } else {
                $countries[] = [
                    'code' => $code,
                    'name' => $name,
                    'flag' => $flag,
                    'description' => $description,
                    'tuition' => $tuition,
                    'accommodation' => $accommodation,
                    'language' => $language,
                    'duration' => $duration,
                    'is_active' => 1
                ];
                saveCountriesData($countries);
                $message = 'Улс амжилттай нэмэгдлээ!';
                $messageType = 'success';
            }
        } else {
            $message = 'Код болон нэр заавал оруулна уу!';
            $messageType = 'error';
        }
    }
    
    // Улс засах
    elseif ($action === 'edit_country') {
        $editCode = $_POST['edit_code'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $flag = trim($_POST['flag'] ?? '🏳️');
        $description = trim($_POST['description'] ?? '');
        $tuition = trim($_POST['tuition'] ?? '');
        $accommodation = trim($_POST['accommodation'] ?? '');
        $language = trim($_POST['language'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (!empty($editCode) && !empty($name)) {
            $countries = getCountriesData();
            
            foreach ($countries as &$c) {
                if ($c['code'] === $editCode) {
                    $c['name'] = $name;
                    $c['flag'] = $flag;
                    $c['description'] = $description;
                    $c['tuition'] = $tuition;
                    $c['accommodation'] = $accommodation;
                    $c['language'] = $language;
                    $c['duration'] = $duration;
                    $c['is_active'] = $isActive;
                    break;
                }
            }
            
            saveCountriesData($countries);
            $message = 'Улс амжилттай засагдлаа!';
            $messageType = 'success';
        }
    }
    
    // Улс устгах
    elseif ($action === 'delete_country') {
        $deleteCode = $_POST['delete_code'] ?? '';
        
        if (!empty($deleteCode)) {
            $countries = getCountriesData();
            $countries = array_filter($countries, function($c) use ($deleteCode) {
                return $c['code'] !== $deleteCode;
            });
            $countries = array_values($countries); // Re-index
            saveCountriesData($countries);
            $message = 'Улс амжилттай устгагдлаа!';
            $messageType = 'success';
        }
    }
}

// Улсуудыг авах
$countries = getAllCountries();

$currentPage = 'settings';
include 'includes/header.php';
?>

<div class="admin-header">
    <h1>⚙️ Тохиргоо</h1>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?>">
    <?= $messageType === 'success' ? '✅' : '❌' ?> <?= $message ?>
</div>
<?php endif; ?>

<!-- Улсуудын удирдлага -->
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="color: var(--primary); margin: 0;">🌍 Улсуудын удирдлага</h3>
        <button type="button" class="btn btn-primary btn-sm" onclick="showAddCountryModal()">+ Улс нэмэх</button>
    </div>
    
    <?php if (empty($countries)): ?>
    <p style="color: var(--text-muted); text-align: center; padding: 2rem;">Улс бүртгэгдээгүй байна.</p>
    <?php else: ?>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Туг</th>
                    <th>Код</th>
                    <th>Нэр</th>
                    <th>Тайлбар</th>
                    <th>Статус</th>
                    <th>Үйлдэл</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($countries as $country): ?>
                <tr>
                    <td style="font-size: 1.5rem;"><?= $country['flag'] ?? '🏳️' ?></td>
                    <td><code><?= clean($country['code']) ?></code></td>
                    <td><strong><?= clean($country['name']) ?></strong></td>
                    <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?= clean($country['description'] ?? '') ?>
                    </td>
                    <td>
                        <?php if (isset($country['is_active']) && $country['is_active']): ?>
                        <span class="badge badge-approved">Идэвхтэй</span>
                        <?php else: ?>
                        <span class="badge badge-rejected">Идэвхгүй</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-secondary btn-sm" onclick='editCountry(<?= json_encode($country, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>✏️</button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Энэ улсыг устгах уу?')">
                            <input type="hidden" name="action" value="delete_country">
                            <input type="hidden" name="delete_code" value="<?= $country['code'] ?>">
                            <button type="submit" class="btn btn-secondary btn-sm" style="background: var(--error);">🗑️</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<form method="POST">
    <input type="hidden" name="action" value="save_settings">
    
    <!-- Ерөнхий тохиргоо -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem;">🏢 Ерөнхий мэдээлэл</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Сайтын нэр</label>
                <input type="text" class="form-input" name="site_name" value="<?= clean(getSetting('site_name')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Тайлбар</label>
                <input type="text" class="form-input" name="site_description" value="<?= clean(getSetting('site_description')) ?>">
            </div>
        </div>
    </div>

    <!-- Холбоо барих -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem;">📞 Холбоо барих</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Утас</label>
                <input type="text" class="form-input" name="phone" value="<?= clean(getSetting('phone')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" name="email" value="<?= clean(getSetting('email')) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Хаяг</label>
                <input type="text" class="form-input" name="address" value="<?= clean(getSetting('address')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Ажлын цаг</label>
                <input type="text" class="form-input" name="contact_hours" value="<?= clean(getSetting('contact_hours')) ?>">
            </div>
        </div>
    </div>

    <!-- Банкны мэдээлэл -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem;">💳 Төлбөрийн мэдээлэл</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Банкны нэр</label>
                <input type="text" class="form-input" name="bank_name" value="<?= clean(getSetting('bank_name')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Дансны дугаар</label>
                <input type="text" class="form-input" name="bank_account" value="<?= clean(getSetting('bank_account')) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Данс эзэмшигч</label>
                <input type="text" class="form-input" name="account_holder" value="<?= clean(getSetting('account_holder')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Бүртгэлийн хураамж (₮)</label>
                <input type="number" class="form-input" name="registration_fee" value="<?= clean(getSetting('registration_fee')) ?>">
            </div>
        </div>
    </div>

    <!-- Нүүр хуудас -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem;">🏠 Нүүр хуудас</h3>
        <div class="form-group">
            <label class="form-label">Гарчиг</label>
            <input type="text" class="form-input" name="home_title" value="<?= clean(getSetting('home_title')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Тайлбар</label>
            <textarea class="form-textarea" name="home_description"><?= clean(getSetting('home_description')) ?></textarea>
        </div>
    </div>

    <!-- Бидний тухай -->
    <div class="card" style="margin-bottom: 2rem;">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem;">📖 Бидний тухай</h3>
        <div class="form-group">
            <label class="form-label">Гарчиг</label>
            <input type="text" class="form-input" name="about_title" value="<?= clean(getSetting('about_title')) ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Агуулга</label>
            <textarea class="form-textarea" name="about_content" style="min-height: 150px;"><?= clean(getSetting('about_content')) ?></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg">💾 Тохиргоо хадгалах</button>
</form>

<!-- Улс нэмэх Modal -->
<div class="modal-overlay" id="addCountryModal">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <h3>🌍 Шинэ улс нэмэх</h3>
            <button class="modal-close" onclick="closeAddCountryModal()">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_country">
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Код <span class="required">*</span></label>
                    <input type="text" class="form-input" name="code" required placeholder="japan">
                    <small style="color: var(--text-muted);">Англиар, жижиг үсгээр</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Туг</label>
                    <input type="text" class="form-input" name="flag" placeholder="🇯🇵" maxlength="10">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Нэр <span class="required">*</span></label>
                <input type="text" class="form-input" name="name" required placeholder="Япон">
            </div>
            
            <div class="form-group">
                <label class="form-label">Тайлбар</label>
                <textarea class="form-textarea" name="description" placeholder="Улсын талаарх мэдээлэл..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">💵 Тэтгэлэг</label>
                    <input type="text" class="form-input" name="tuition" placeholder="Сургалтын төлбөр 100%">
                </div>
                <div class="form-group">
                    <label class="form-label">🏠 Байр</label>
                    <input type="text" class="form-input" name="accommodation" placeholder="Дотуур байр">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">📖 Хэл</label>
                    <input type="text" class="form-input" name="language" placeholder="Хэлний бэлтгэл">
                </div>
                <div class="form-group">
                    <label class="form-label">⏱️ Хугацаа</label>
                    <input type="text" class="form-input" name="duration" placeholder="4 жил">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Нэмэх</button>
        </form>
    </div>
</div>

<!-- Улс засах Modal -->
<div class="modal-overlay" id="editCountryModal">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <h3>✏️ Улс засах</h3>
            <button class="modal-close" onclick="closeEditCountryModal()">&times;</button>
        </div>
        <form method="POST" id="editCountryForm">
            <input type="hidden" name="action" value="edit_country">
            <input type="hidden" name="edit_code" id="edit_code">
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Код</label>
                    <input type="text" class="form-input" id="edit_code_display" disabled style="background: var(--bg-dark);">
                </div>
                <div class="form-group">
                    <label class="form-label">Туг</label>
                    <input type="text" class="form-input" name="flag" id="edit_flag" maxlength="10">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Нэр <span class="required">*</span></label>
                <input type="text" class="form-input" name="name" id="edit_name" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Тайлбар</label>
                <textarea class="form-textarea" name="description" id="edit_description"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">💵 Тэтгэлэг</label>
                    <input type="text" class="form-input" name="tuition" id="edit_tuition">
                </div>
                <div class="form-group">
                    <label class="form-label">🏠 Байр</label>
                    <input type="text" class="form-input" name="accommodation" id="edit_accommodation">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">📖 Хэл</label>
                    <input type="text" class="form-input" name="language" id="edit_language">
                </div>
                <div class="form-group">
                    <label class="form-label">⏱️ Хугацаа</label>
                    <input type="text" class="form-input" name="duration" id="edit_duration">
                </div>
            </div>
            
            <div class="form-group">
                <label class="checkbox-item" style="width: fit-content;">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1">
                    <span class="checkbox-custom"></span>
                    <span>Идэвхтэй</span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Хадгалах</button>
        </form>
    </div>
</div>

<script>
function showAddCountryModal() {
    document.getElementById('addCountryModal').classList.add('active');
}

function closeAddCountryModal() {
    document.getElementById('addCountryModal').classList.remove('active');
}

function editCountry(country) {
    document.getElementById('edit_code').value = country.code;
    document.getElementById('edit_code_display').value = country.code;
    document.getElementById('edit_flag').value = country.flag || '🏳️';
    document.getElementById('edit_name').value = country.name || '';
    document.getElementById('edit_description').value = country.description || '';
    document.getElementById('edit_tuition').value = country.tuition || '';
    document.getElementById('edit_accommodation').value = country.accommodation || '';
    document.getElementById('edit_language').value = country.language || '';
    document.getElementById('edit_duration').value = country.duration || '';
    document.getElementById('edit_is_active').checked = country.is_active == 1;
    
    document.getElementById('editCountryModal').classList.add('active');
}

function closeEditCountryModal() {
    document.getElementById('editCountryModal').classList.remove('active');
}

// Modal гаднаас дарахад хаах
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
