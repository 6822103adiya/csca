# Даам Тэтгэлэг - Тэтгэлэгт бүртгүүлэх систем

## 🎓 Тухай

Гадаад улсад суралцах тэтгэлэгт хөтөлбөрт бүртгүүлэх веб систем.

## 🛠 Технологи

- PHP 7.4+
- MySQL 5.7+
- HTML5 / CSS3 / JavaScript
- Dark Mode UI
- Orange (#FFA500) theme

## 📦 Суулгах заавар

### 1. Database үүсгэх

```sql
-- config/database.sql файлыг MySQL дээр ажиллуулна
mysql -u root -p < config/database.sql
```

### 2. Database тохиргоо

`config/db.php` файлд өөрийн MySQL мэдээллийг оруулна:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'daam_scholarship');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### 3. Uploads folder

`uploads` хавтас бичих эрхтэй байх ёстой:

```bash
chmod 755 uploads
```

### 4. Web server

XAMPP, WAMP, Laragon эсвэл PHP built-in server ашиглана:

```bash
php -S localhost:8000
```

## 🔐 Админ нэвтрэх

- URL: `/admin/login.php`
- Нэвтрэх нэр: `admin`
- Нууц үг: `admin123`

## 📁 Файлын бүтэц

```
├── admin/                  # Админ панель
│   ├── includes/          # Header, Footer
│   ├── index.php          # Dashboard
│   ├── login.php          # Нэвтрэх
│   ├── pending.php        # Хүлээгдэж буй
│   ├── approved.php       # Баталгаажсан
│   ├── rejected.php       # Татгалзсан
│   ├── view.php           # Дэлгэрэнгүй
│   ├── settings.php       # Тохиргоо
│   └── logout.php         # Гарах
├── api/                    # API endpoints
│   └── send_code.php      # Email код илгээх
├── assets/
│   ├── css/style.css      # Styles
│   └── js/main.js         # JavaScript
├── config/
│   ├── database.sql       # SQL schema
│   ├── db.php             # DB холболт
│   └── functions.php      # Helper functions
├── includes/
│   ├── header.php         # Header template
│   └── footer.php         # Footer template
├── uploads/               # Uploaded files
├── index.php              # Нүүр хуудас
├── about.php              # Бидний тухай
├── contact.php            # Холбоо барих
├── scholarship.php        # Тэтгэлэгийн мэдээлэл
├── register.php           # Бүртгэлийн форм
└── process_payment.php    # Төлбөр баталгаажуулалт
```

## ✅ Онцлогууд

- ✅ Dark mode UI
- ✅ Mobile responsive
- ✅ Multi-country selection (1-3 улс)
- ✅ Регистрийн дугаар validation (2 кирилл + 8 тоо)
- ✅ 8 оронтой утасны дугаар
- ✅ @gmail.com хаяг validation
- ✅ Зураг upload (ID урд, ард, selfie)
- ✅ Төлбөрийн заавар
- ✅ Admin approve/reject
- ✅ Улсаар шүүх
- ✅ Динамик контент засварлах

## 📞 Холбоо барих

Асуулт байвал холбогдоно уу.

---

© 2025 Даам Тэтгэлэг

