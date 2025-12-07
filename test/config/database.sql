-- Даам Тэтгэлэг - Database Schema
-- MySQL Database

CREATE DATABASE IF NOT EXISTS u613238646_csca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE u613238646_csca;

-- Админ хэрэглэгчид
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Анхны админ (password: admin123)
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@daam.mn');

-- Бүртгүүлэгчдийн мэдээлэл
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Улсын сонголт (JSON array)
    countries JSON NOT NULL,
    
    -- Хувийн мэдээлэл
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    register_number VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    school VARCHAR(200) NOT NULL,
    grade TINYINT NOT NULL,
    
    -- Хэлний оноо
    language_score VARCHAR(50) DEFAULT NULL,
    language_certificate VARCHAR(255) DEFAULT NULL,
    
    -- Иргэний үнэмлэх
    id_front VARCHAR(255) NOT NULL,
    id_back VARCHAR(255) NOT NULL,
    id_selfie VARCHAR(255) NOT NULL,
    
    -- Холбоо барих
    phone VARCHAR(8) NOT NULL,
    email VARCHAR(100) NOT NULL,
    email_verified TINYINT(1) DEFAULT 0,
    verification_code VARCHAR(6) DEFAULT NULL,
    
    -- Төлбөр
    payment_status ENUM('pending', 'paid', 'confirmed') DEFAULT 'pending',
    payment_date DATETIME DEFAULT NULL,
    
    -- Статус
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    reject_reason TEXT DEFAULT NULL,
    
    -- Огноо
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Сайтын тохиргоо (динамик контент)
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Анхны тохиргоонууд
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'Даам Тэтгэлэг'),
('site_description', 'Гадаад улсад суралцах тэтгэлэгт хөтөлбөр'),
('phone', '+976 9999-9999'),
('email', 'info@daam.mn'),
('address', 'Улаанбаатар хот, Сүхбаатар дүүрэг'),
('bank_account', '5000123456'),
('bank_name', 'Хаан банк'),
('account_holder', 'Даам ХХК'),
('registration_fee', '50000'),

-- Нүүр хуудасны контент
('home_title', 'Ирээдүйгээ Бүтээ'),
('home_subtitle', 'Дэлхийн шилдэг их сургуулиудад суралцах боломжоо бүү алд'),
('home_description', 'Бид таны гадаадад суралцах хүсэл мөрөөдлийг биелүүлэхэд туслах болно. Манай тэтгэлэгт хөтөлбөрт хамрагдаж, ирээдүйгээ өөрөө бүтээ.'),

-- Бидний тухай
('about_title', 'Бидний тухай'),
('about_content', 'Даам Тэтгэлэг нь 2020 оноос хойш Монгол оюутнуудад гадаадад суралцах боломжийг олгож ирсэн. Бид Хятад, Солонгос, Герман, Орос зэрэг улсуудын шилдэг их сургуулиудтай хамтран ажилладаг.'),

-- Холбоо барих
('contact_title', 'Холбоо барих'),
('contact_hours', 'Даваа - Баасан: 09:00 - 18:00'),

-- Улсуудын мэдээлэл
('country_china', 'Хятад улсад жил бүр 500+ оюутан элсдэг. Бүрэн тэтгэлэгтэй хөтөлбөрүүд, хэлний сургалт багтсан.'),
('country_korea', 'Солонгос улсын шилдэг их сургуулиуд. K-pop, технологи, бизнесийн чиглэлээр суралцах боломж.'),
('country_germany', 'Герман улсад үнэ төлбөргүй дээд боловсрол. Инженер, анагаах ухааны чиглэлүүд.'),
('country_russia', 'Орос улсын түүхэн их сургуулиуд. Анагаах ухаан, инженерчлэл, урлагийн чиглэлүүд.');

-- Улсуудын хүснэгт
CREATE TABLE IF NOT EXISTS countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    flag VARCHAR(10) DEFAULT '🏳️',
    description TEXT,
    tuition VARCHAR(100) DEFAULT '',
    accommodation VARCHAR(100) DEFAULT '',
    language VARCHAR(100) DEFAULT '',
    duration VARCHAR(50) DEFAULT '',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Анхны улсууд
INSERT INTO countries (code, name, flag, description, tuition, accommodation, language, duration, sort_order) VALUES
('china', 'Хятад', '🇨🇳', 'Хятад улсад жил бүр 500+ оюутан элсдэг. Бүрэн тэтгэлэгтэй хөтөлбөрүүд, хэлний сургалт багтсан.', 'Сургалтын төлбөр 100%', 'Дотуур байр үнэгүй', '1 жил хэлний бэлтгэл', '4-6 жил', 1),
('korea', 'Солонгос', '🇰🇷', 'Солонгос улсын шилдэг их сургуулиуд. K-pop, технологи, бизнесийн чиглэлээр суралцах боломж.', 'Сургалтын төлбөр 50-100%', 'Сар бүр тэтгэмж', 'TOPIK бэлтгэл', '4 жил', 2),
('germany', 'Герман', '🇩🇪', 'Герман улсад үнэ төлбөргүй дээд боловсрол. Инженер, анагаах ухааны чиглэлүүд.', 'Үнэ төлбөргүй', 'Эрүүл мэндийн даатгал', 'Герман хэлний бэлтгэл', '3-5 жил', 3),
('russia', 'Орос', '🇷🇺', 'Орос улсын түүхэн их сургуулиуд. Анагаах ухаан, инженерчлэл, урлагийн чиглэлүүд.', 'Засгийн газрын тэтгэлэг', 'Дотуур байр багтсан', 'Орос хэлний бэлтгэл', '4-6 жил', 4);

-- Email verification codes
CREATE TABLE IF NOT EXISTS email_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index-үүд
CREATE INDEX idx_registrations_status ON registrations(status);
CREATE INDEX idx_registrations_email ON registrations(email);
CREATE INDEX idx_registrations_phone ON registrations(phone);
