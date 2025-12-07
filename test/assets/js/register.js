/**
 * Тэтгэлэг Сэсэн - Registration Form JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initRegistrationForm();
});

function initRegistrationForm() {
    const form = document.getElementById('registrationForm');
    if (!form) return;

    // Initialize components
    initStepNavigation();
    initCountrySelection();
    initFileUploads();
    initCharacterCount();
    initGradeSelection();
    initVerification();
    initPayment();
    initValidation();
}

/**
 * Step Navigation
 */
function initStepNavigation() {
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');

    nextButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const nextStep = this.dataset.next;
            if (validateCurrentStep(getCurrentStep())) {
                goToStep(nextStep);
            }
        });
    });

    prevButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const prevStep = this.dataset.prev;
            goToStep(prevStep);
        });
    });
}

function getCurrentStep() {
    const activeStep = document.querySelector('.form-step.active');
    return activeStep ? activeStep.id.replace('step', '') : '1';
}

function goToStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(step => {
        step.classList.remove('active');
    });

    // Show target step
    const targetStep = document.getElementById('step' + stepNumber);
    if (targetStep) {
        targetStep.classList.add('active');
    }

    // Update progress
    document.querySelectorAll('.progress-steps .step').forEach(step => {
        const stepNum = step.dataset.step;
        step.classList.remove('active', 'completed');
        
        if (parseInt(stepNum) < parseInt(stepNumber)) {
            step.classList.add('completed');
        } else if (parseInt(stepNum) === parseInt(stepNumber)) {
            step.classList.add('active');
        }
    });

    // Update email display in step 3
    if (stepNumber === '3') {
        const email = document.getElementById('email').value;
        document.getElementById('verifyEmail').textContent = email;
    }

    // Update transfer note in step 4
    if (stepNumber === '4') {
        updateTransferNote();
    }

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Country Selection
 */
function initCountrySelection() {
    const checkboxes = document.querySelectorAll('.country-checkbox input');
    const countDisplay = document.getElementById('countryCount');
    const errorEl = document.getElementById('countryError');
    const maxCountries = 3;

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.country-checkbox input:checked').length;
            
            if (checkedCount > maxCountries) {
                this.checked = false;
                errorEl.textContent = 'Гурваас илүү улсыг сонгож болохгүй!';
                
                // Show tooltip animation
                this.closest('.country-box').classList.add('shake');
                setTimeout(() => {
                    this.closest('.country-box').classList.remove('shake');
                }, 500);
            } else {
                errorEl.textContent = '';
            }
            
            countDisplay.textContent = document.querySelectorAll('.country-checkbox input:checked').length;
        });
    });
}

// Add shake animation
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .shake { animation: shake 0.3s ease-in-out; }
`;
document.head.appendChild(shakeStyle);

/**
 * File Uploads
 */
function initFileUploads() {
    const uploadBoxes = document.querySelectorAll('.upload-box');

    uploadBoxes.forEach(box => {
        const input = box.querySelector('input[type="file"]');
        const preview = box.querySelector('.upload-preview');

        if (!input) return;

        input.addEventListener('change', function() {
            const file = this.files[0];
            
            if (!file) {
                box.classList.remove('has-file');
                preview.innerHTML = '';
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Файлын хэмжээ 5MB-аас хэтэрсэн байна!');
                this.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                alert('Зөвхөн JPG, PNG, PDF файл оруулна уу!');
                this.value = '';
                return;
            }

            box.classList.add('has-file');

            // Show preview
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `
                    <i class="fas fa-file-pdf" style="font-size: 3rem; color: var(--primary);"></i>
                    <div class="file-name">${file.name}</div>
                `;
            }
        });
    });

    // Update certificate requirement based on language score
    const languageScore = document.getElementById('language_score');
    const certRequired = document.getElementById('certRequired');
    const certificateInput = document.getElementById('certificate');

    if (languageScore) {
        languageScore.addEventListener('input', function() {
            if (this.value.trim()) {
                certRequired.textContent = '*';
                certRequired.classList.remove('optional');
                certRequired.classList.add('required');
                certificateInput.required = true;
            } else {
                certRequired.textContent = '(заавал биш)';
                certRequired.classList.add('optional');
                certRequired.classList.remove('required');
                certificateInput.required = false;
            }
        });
    }
}

/**
 * Character Count
 */
function initCharacterCount() {
    const textarea = document.getElementById('additional_request');
    const charCount = document.getElementById('charCount');

    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
}

/**
 * Grade Selection
 */
function initGradeSelection() {
    const gradeSelect = document.getElementById('grade');
    const gradeNotice = document.getElementById('gradeNotice');

    if (gradeSelect && gradeNotice) {
        gradeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.eligible === 'true') {
                gradeNotice.style.display = 'flex';
            } else {
                gradeNotice.style.display = 'none';
            }
        });
    }
}

/**
 * Email Verification
 */
function initVerification() {
    const sendCodeBtn = document.getElementById('sendCodeBtn');
    const codeSection = document.getElementById('codeSection');
    const codeInputs = document.querySelectorAll('.code-input');
    const verificationCode = document.getElementById('verificationCode');
    const verifyBtn = document.getElementById('verifyBtn');
    const termsCheckbox = document.getElementById('terms_accepted');
    const resendBtn = document.getElementById('resendCode');
    
    let timerInterval;

    if (sendCodeBtn) {
        sendCodeBtn.addEventListener('click', function() {
            const email = document.getElementById('email').value;
            
            if (!validateGmail(email)) {
                alert('Gmail хаяг буруу байна!');
                return;
            }

            // Simulate sending code
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Илгээж байна...';

            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check"></i> Код илгээгдлээ!';
                codeSection.style.display = 'block';
                startTimer();
                
                // For demo, show the code
                console.log('Verification code: 123456 (demo)');
            }, 1500);
        });
    }

    // Code input handling
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }

            // Update hidden field
            updateVerificationCode();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                codeInputs[index - 1].focus();
            }
        });

        // Allow paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            
            for (let i = 0; i < pastedData.length && i < codeInputs.length; i++) {
                codeInputs[i].value = pastedData[i];
            }
            updateVerificationCode();
        });
    });

    function updateVerificationCode() {
        let code = '';
        codeInputs.forEach(input => {
            code += input.value;
        });
        verificationCode.value = code;
        
        // Enable verify button if code is complete and terms accepted
        checkVerifyButton();
    }

    function checkVerifyButton() {
        const code = verificationCode.value;
        const termsAccepted = termsCheckbox.checked;
        verifyBtn.disabled = !(code.length === 6 && termsAccepted);
    }

    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', checkVerifyButton);
    }

    if (verifyBtn) {
        verifyBtn.addEventListener('click', function() {
            const code = verificationCode.value;
            
            // For demo, accept 123456
            if (code === '123456' || code.length === 6) {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Шалгаж байна...';
                
                setTimeout(() => {
                    goToStep('4');
                }, 1000);
            } else {
                alert('Баталгаажуулах код буруу байна!');
            }
        });
    }

    if (resendBtn) {
        resendBtn.addEventListener('click', function() {
            codeInputs.forEach(input => input.value = '');
            verificationCode.value = '';
            startTimer();
            alert('Код дахин илгээгдлээ!');
        });
    }

    function startTimer() {
        let timeLeft = 10 * 60; // 10 minutes
        const timerDisplay = document.getElementById('codeTimer');
        
        if (timerInterval) clearInterval(timerInterval);
        
        timerInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerDisplay.textContent = 'Хугацаа дууссан';
            }
            timeLeft--;
        }, 1000);
    }
}

/**
 * Payment Section
 */
function initPayment() {
    const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');
    const modal = document.getElementById('confirmModal');
    const modalYes = document.getElementById('modalYes');
    const modalNo = document.getElementById('modalNo');
    const copyTransferNote = document.getElementById('copyTransferNote');

    if (confirmPaymentBtn) {
        confirmPaymentBtn.addEventListener('click', function() {
            modal.classList.add('active');
        });
    }

    if (modalNo) {
        modalNo.addEventListener('click', function() {
            modal.classList.remove('active');
            alert('Гүйлгээний утга дээр овог, нэр, утасны дугаарыг заавал бичнэ үү!');
        });
    }

    if (modalYes) {
        modalYes.addEventListener('click', function() {
            modal.classList.remove('active');
            submitRegistration();
        });
    }

    if (copyTransferNote) {
        copyTransferNote.addEventListener('click', function() {
            const transferNote = document.getElementById('transferNote').textContent;
            copyToClipboard(transferNote, this);
        });
    }

    // Close modal on outside click
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    }
}

function updateTransferNote() {
    const lastName = document.getElementById('last_name').value;
    const firstName = document.getElementById('first_name').value;
    const phone = document.getElementById('phone').value;
    
    const transferNote = `${lastName} ${firstName} ${phone}`;
    document.getElementById('transferNote').textContent = transferNote;
}

function submitRegistration() {
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('confirmPaymentBtn');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Илгээж байна...';

    // Prepare form data
    const formData = new FormData(form);

    // In production, this would be an AJAX call to the server
    // For demo, simulate success
    setTimeout(() => {
        // Show success step
        document.querySelectorAll('.form-step').forEach(step => {
            step.classList.remove('active');
        });
        document.getElementById('stepSuccess').classList.add('active');

        // Update progress
        document.querySelectorAll('.progress-steps .step').forEach(step => {
            step.classList.add('completed');
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 2000);

    // Actual API call would be:
    /*
    fetch('/api/register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Алдаа гарлаа. Дахин оролдоно уу.');
    });
    */
}

/**
 * Form Validation
 */
function initValidation() {
    // Real-time validation
    const registerNumber = document.getElementById('register_number');
    const phone = document.getElementById('phone');
    const email = document.getElementById('email');

    if (registerNumber) {
        registerNumber.addEventListener('blur', function() {
            validateField(this, validateRegisterNumber, 'Регистерийн дугаар буруу форматтай — эхний 2 тэмдэг кирилл үсэг + 8 тоо байх ёстой (жишээ: АН12345678)');
        });
    }

    if (phone) {
        phone.addEventListener('blur', function() {
            validateField(this, validatePhone, 'Утасны дугаар 8 оронтой байх ёстой');
        });

        phone.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    if (email) {
        email.addEventListener('blur', function() {
            validateField(this, validateGmail, 'Зөвхөн Gmail хаяг л зөвшөөрнө');
        });
    }
}

function validateField(input, validatorFn, errorMessage) {
    const formGroup = input.closest('.form-group');
    const errorEl = formGroup.querySelector('.error-message');
    
    if (!input.value.trim()) {
        return true; // Empty is handled by required
    }

    if (!validatorFn(input.value)) {
        input.classList.add('error');
        if (errorEl) errorEl.textContent = errorMessage;
        return false;
    } else {
        input.classList.remove('error');
        if (errorEl) errorEl.textContent = '';
        return true;
    }
}

function validateCurrentStep(stepNumber) {
    let isValid = true;
    const currentStep = document.getElementById('step' + stepNumber);
    
    if (!currentStep) return true;

    // Check required fields
    const requiredInputs = currentStep.querySelectorAll('input[required], select[required], textarea[required]');
    
    requiredInputs.forEach(input => {
        const formGroup = input.closest('.form-group');
        const errorEl = formGroup ? formGroup.querySelector('.error-message') : null;
        
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            if (errorEl) {
                const label = formGroup.querySelector('label');
                const fieldName = label ? label.textContent.replace('*', '').trim() : 'Энэ талбар';
                errorEl.textContent = fieldName + ' оруулна уу';
            }
        } else {
            input.classList.remove('error');
            if (errorEl) errorEl.textContent = '';
        }
    });

    // Step-specific validation
    if (stepNumber === '1') {
        // Validate register number
        const registerNumber = document.getElementById('register_number');
        if (registerNumber.value && !validateRegisterNumber(registerNumber.value)) {
            isValid = false;
            registerNumber.classList.add('error');
            registerNumber.closest('.form-group').querySelector('.error-message').textContent = 
                'Регистерийн дугаар буруу форматтай';
        }

        // Validate phone
        const phone = document.getElementById('phone');
        if (phone.value && !validatePhone(phone.value)) {
            isValid = false;
            phone.classList.add('error');
            phone.closest('.form-group').querySelector('.error-message').textContent = 
                'Утасны дугаар 8 оронтой байх ёстой';
        }

        // Validate email
        const email = document.getElementById('email');
        if (email.value && !validateGmail(email.value)) {
            isValid = false;
            email.classList.add('error');
            email.closest('.form-group').querySelector('.error-message').textContent = 
                'Зөвхөн Gmail хаяг л зөвшөөрнө';
        }

        // Validate country selection
        const selectedCountries = document.querySelectorAll('.country-checkbox input:checked');
        if (selectedCountries.length === 0) {
            isValid = false;
            document.getElementById('countryError').textContent = 'Дор хаяж нэг улс сонгоно уу';
        }
    }

    if (stepNumber === '2') {
        // Validate ID uploads
        const idFront = document.getElementById('id_front');
        const idBack = document.getElementById('id_back');
        const idSelfie = document.getElementById('id_selfie');
        const idError = document.getElementById('idError');

        if (!idFront.files.length || !idBack.files.length || !idSelfie.files.length) {
            isValid = false;
            idError.textContent = 'Иргэний үнэмлэхний зураг бүрэн биш байна';
        } else {
            idError.textContent = '';
        }

        // Validate certificate if language score is provided
        const languageScore = document.getElementById('language_score');
        const certificate = document.getElementById('certificate');
        
        if (languageScore.value.trim() && !certificate.files.length) {
            isValid = false;
            alert('Хэлний оноотой бол сертификатын зураг оруулна уу');
        }
    }

    if (!isValid) {
        // Scroll to first error
        const firstError = currentStep.querySelector('.error, .error-message:not(:empty)');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    return isValid;
}

// Validation helper functions
function validateRegisterNumber(value) {
    const pattern = /^[А-Яа-яЁёӨөҮү]{2}\d{8}$/;
    return pattern.test(value);
}

function validatePhone(value) {
    return /^\d{8}$/.test(value);
}

function validateGmail(value) {
    return /^[a-zA-Z0-9._%+-]+@gmail\.com$/i.test(value);
}

