/**
 * Даам Тэтгэлэг - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    
    function closeMenu() {
        hamburger.classList.remove('active');
        mobileMenu.classList.remove('active');
        mobileMenu.style.maxHeight = '0';
    }
    
    function openMenu() {
        hamburger.classList.add('active');
        mobileMenu.classList.add('active');
        mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
    }
    
    if (hamburger && mobileMenu) {
        // Toggle menu
        hamburger.addEventListener('click', function() {
            if (mobileMenu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
        
        // Close menu when link clicked
        mobileMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                closeMenu();
            });
        });
        
        // Close on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 767) {
                closeMenu();
            }
        });
    }

    // Form validation for registration
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const errors = [];
            
            // Check countries
            const countries = document.querySelectorAll('input[name="countries[]"]:checked');
            if (countries.length === 0) {
                errors.push('Дор хаяж 1 улс сонгоно уу.');
            } else if (countries.length > 3) {
                errors.push('Хамгийн ихдээ 3 улс сонгох боломжтой.');
            }
            
            // Check register number
            const regNum = document.querySelector('input[name="register_number"]');
            if (regNum && regNum.value) {
                const pattern = /^[А-ЯӨҮЁ]{2}[0-9]{8}$/u;
                if (!pattern.test(regNum.value.toUpperCase())) {
                    errors.push('Регистрийн дугаар буруу байна. (2 кирилл үсэг + 8 тоо)');
                }
            }
            
            // Check phone
            const phone = document.querySelector('input[name="phone"]');
            if (phone && phone.value) {
                if (!/^[0-9]{8}$/.test(phone.value)) {
                    errors.push('Утасны дугаар 8 оронтой байх ёстой.');
                }
            }
            
            // Check email
            const email = document.querySelector('input[name="email"]');
            if (email && email.value) {
                if (!/@gmail\.com$/i.test(email.value)) {
                    errors.push('Зөвхөн @gmail.com хаяг оруулна уу.');
                }
            }
            
            // Check grade
            const grade = document.querySelector('select[name="grade"]');
            if (grade && grade.value) {
                if (!['11', '12'].includes(grade.value)) {
                    errors.push('Зөвхөн 11 эсвэл 12-р анги байна.');
                }
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Алдаа:\n\n' + errors.join('\n'));
                return false;
            }
        });
    }

    // File upload validation
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            
            // Check file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Файлын хэмжээ 5MB-ээс хэтэрч байна.');
                this.value = '';
                return;
            }
            
            // Check file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                alert('Зөвхөн JPG, PNG, PDF файл оруулна уу.');
                this.value = '';
                return;
            }
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Header scroll effect
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
            } else {
                header.style.boxShadow = 'none';
            }
        });
    }

    // Auto uppercase for register number
    const regInput = document.querySelector('input[name="register_number"]');
    if (regInput) {
        regInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Phone input - only numbers
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});

// Copy to clipboard function
function copyToClipboard(elementId) {
    const text = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        const originalText = btn.textContent;
        btn.textContent = '✓';
        btn.style.background = 'var(--success)';
        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '';
        }, 2000);
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Хуулагдлаа!');
    });
}
