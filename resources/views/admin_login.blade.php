<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
    <title>Admin Login - Jeongol</title>
    

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .login-box {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 420px;
            text-align: center;
            position: relative;
        }

        .admin-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.4);
        }

        .login-title {
            color: #2c3e50;
            margin-bottom: 2rem;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input.valid {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1) !important;
        }

        input.invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: left;
            background-color: #fdf2f2;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border-left: 4px solid #e74c3c;
        }

        .success-message {
            color: #27ae60;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
            background-color: #f0f9f4;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border-left: 4px solid #27ae60;
        }

        .login-box img {
            max-width: 100px;
            height: auto;
            margin-bottom: 1rem;
            border-radius: 50%;
            border: 4px solid #f8f9fa;
        }

        .btn {
            width: 100%;
            padding: 0.875rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 0.75rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn:disabled {
            background: #6c757d !important;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn:disabled:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        .btn-link {
            background: none;
            color: #667eea;
            text-decoration: none;
            border: none;
            padding: 0.5rem 0;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .back-link {
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            margin-top: 1rem;
        }

        .hidden {
            display: none;
        }

        .divider {
            margin: 1.5rem 0;
            text-align: center;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .password-requirements {
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }

        .requirement {
            color: #6c757d;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
        }

        .requirement.valid {
            color: #28a745;
        }

        .requirement.invalid {
            color: #dc3545;
        }

        .requirement.valid::before {
            content: "✓ ";
            font-weight: bold;
        }

        .requirement.invalid::before {
            content: "✗ ";
            font-weight: bold;
        }

        .requirement:not(.valid):not(.invalid)::before {
            content: "• ";
        }

        @media (max-width: 500px) {
            .login-box {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .admin-badge {
                font-size: 0.7rem;
                padding: 0.4rem 0.8rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <!-- Admin Login Form (with logo) -->
        <div class="login-box" id="loginForm">
            <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo">
            <h2 class="login-title">Admin Login</h2>

            @if(session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('status'))
                <div class="success-message">{{ session('status') }}</div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <button type="button" class="btn-link" onclick="adminAuth.showForgotPassword()">
                Forgot Password?
            </button>
        </div>

        <!-- Forgot Password Form (no logo) -->
        <div id="forgotPasswordForm" class="login-box hidden">
            <h2 class="login-title">Reset Password</h2>

            <div class="success-message" id="resetSuccess" style="display: none;"></div>
            <div class="error-message" id="resetError" style="display: none;"></div>

            <form action="{{ route('admin.password.email') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="reset_email">Enter your recovery email address</label>
                    <input type="email" id="reset_email" name="email" required>
                </div>

                <button type="submit" class="btn btn-primary">Send Code</button>
            </form>

            <a href="#" class="back-link" onclick="adminAuth.showLogin()">← Back to Login</a>
        </div>

        <!-- Code Verification Form (no logo) -->
        <div id="codeVerificationForm" class="login-box hidden">
            <h2 class="login-title">Enter Verification Code</h2>

            <div class="success-message" id="codeSuccess" style="display: none;"></div>
            <div class="error-message" id="codeError" style="display: none;"></div>

            <form id="verifyCodeForm">
                @csrf
                <input type="hidden" id="verify_email" name="email">

                <div class="form-group">
                    <label for="reset_code">Verification Code</label>
                    <input type="text" id="reset_code" name="code" required maxlength="6"
                        style="text-align: center; font-size: 1.2em; letter-spacing: 2px;">
                </div>

                <button type="submit" class="btn btn-primary">Verify Code</button>
            </form>

            <p style="text-align: center; margin-top: 15px; font-size: 0.9rem; color: #6c757d;">
                Code expires in <span id="countdown">5:00</span>
            </p>

            <div style="text-align: center; margin-top: 10px;">
                <a href="#" class="back-link" onclick="adminAuth.showForgotPassword()">← Back</a>
            </div>
        </div>

        <!-- New Password Form -->
        <div id="newPasswordForm" class="login-box hidden">
            <h2 class="login-title">Set New Password</h2>

            <div class="success-message" id="passwordSuccess" style="display: none;"></div>
            <div class="error-message" id="passwordError" style="display: none;"></div>

            <form id="newPasswordFormElement">
                @csrf
                <input type="hidden" id="final_email" name="email">
                <input type="hidden" id="final_token" name="token">

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="password" required>

                    <div class="password-requirements">
                        <div id="length-req" class="requirement">At least 8 characters</div>
                        <div id="uppercase-req" class="requirement">One uppercase letter</div>
                        <div id="lowercase-req" class="requirement">One lowercase letter</div>
                        <div id="number-req" class="requirement">One number</div>
                        <div id="special-req" class="requirement">One special character (!@#$%^&*)</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="password_confirmation" required>

                </div>

                <button type="submit" class="btn btn-primary" id="resetPasswordBtn" disabled>Reset Password</button>
            </form>

            <a href="#" class="back-link" onclick="adminAuth.showLogin()">← Back to Login</a>
        </div>
    </div>

    <script>
        class AdminAuth {
            constructor() {
                this.currentState = 'login';
                this.passwordChecker = null;
                this.countdownTimer = null;
                this.init();
            }

            init() {
                this.addCSRFToken();
                this.initializeFormState();
                this.setupEventListeners();
                this.passwordChecker = new PasswordChecker();
            }

            // State Management
            getCurrentState() {
                const hashParam = window.location.hash.substring(1);
                if (hashParam) return hashParam;

                try {
                    return sessionStorage.getItem('currentForm') || 'login';
                } catch (e) {
                    return 'login';
                }
            }

            setState(state) {
                this.currentState = state;
                if (state === 'login') {
                    window.location.hash = '';
                } else {
                    window.location.hash = state;
                }

                try {
                    sessionStorage.setItem('currentForm', state);
                } catch (e) { }
            }

            saveFormData(formId, data) {
                try {
                    sessionStorage.setItem(`formData_${formId}`, JSON.stringify(data));
                } catch (e) { }
            }

            getFormData(formId) {
                try {
                    const data = sessionStorage.getItem(`formData_${formId}`);
                    return data ? JSON.parse(data) : {};
                } catch (e) {
                    return {};
                }
            }

            clearFormData(formId) {
                try {
                    sessionStorage.removeItem(`formData_${formId}`);
                } catch (e) { }
            }

            hideAllForms() {
                document.getElementById('loginForm').classList.add('hidden');
                document.getElementById('forgotPasswordForm').classList.add('hidden');
                document.getElementById('codeVerificationForm').classList.add('hidden');
                document.getElementById('newPasswordForm').classList.add('hidden');
            }

            showLogin() {
                this.hideAllForms();
                document.getElementById('loginForm').classList.remove('hidden');
                this.setState('login');
                this.clearFormData('forgot-password');
                this.clearFormData('code-verification');
            }

            showForgotPassword() {
                this.hideAllForms();
                document.getElementById('forgotPasswordForm').classList.remove('hidden');
                this.setState('forgot-password');
            }

            showCodeVerification() {
                this.hideAllForms();
                document.getElementById('codeVerificationForm').classList.remove('hidden');
                this.setState('code-verification');
            }

            showNewPasswordForm() {
                this.hideAllForms();
                document.getElementById('newPasswordForm').classList.remove('hidden');
                this.setState('new-password');
            }

            initializeFormState() {
                const currentState = this.getCurrentState();

                switch (currentState) {
                    case 'forgot-password':
                        this.showForgotPassword();
                        const forgotData = this.getFormData('forgot-password');
                        if (forgotData.email) {
                            document.getElementById('reset_email').value = forgotData.email;
                        }
                        break;
                    case 'code-verification':
                        this.showCodeVerification();
                        const codeData = this.getFormData('code-verification');
                        if (codeData.email) {
                            document.getElementById('verify_email').value = codeData.email;
                            if (codeData.countdownStarted) {
                                this.startCountdown();
                            }
                        }
                        break;
                    case 'new-password':
                        this.showNewPasswordForm();
                        const passwordData = this.getFormData('new-password');
                        if (passwordData.email) {
                            document.getElementById('final_email').value = passwordData.email;
                        }
                        if (passwordData.token) {
                            document.getElementById('final_token').value = passwordData.token;
                        }
                        break;
                    default:
                        this.showLogin();
                }
            }

            addCSRFToken() {
                if (!document.querySelector('meta[name="csrf-token"]')) {
                    const csrfMeta = document.createElement('meta');
                    csrfMeta.name = 'csrf-token';
                    csrfMeta.content = '{{ csrf_token() }}';
                    document.head.appendChild(csrfMeta);
                }
            }

            startCountdown() {
                let timeLeft = 5 * 60; 
                const countdownElement = document.getElementById('countdown');
                if (!countdownElement) return;

                this.countdownTimer = setInterval(() => {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                    if (timeLeft <= 0) {
                        clearInterval(this.countdownTimer);
                        countdownElement.textContent = 'Expired';
                        this.clearFormData('code-verification');
                    }
                    timeLeft--;
                }, 1000);
            }

            setupEventListeners() {
                const forgotForm = document.querySelector('#forgotPasswordForm form');
                if (forgotForm) {
                    forgotForm.addEventListener('submit', (e) => this.handleForgotPassword(e));
                }

                const codeForm = document.getElementById('verifyCodeForm');
                if (codeForm) {
                    codeForm.addEventListener('submit', (e) => this.handleCodeVerification(e));
                }

                const newPasswordForm = document.getElementById('newPasswordFormElement');
                if (newPasswordForm) {
                    newPasswordForm.addEventListener('submit', (e) => this.handleNewPassword(e));
                }

                window.addEventListener('popstate', () => this.initializeFormState());
            }

            async handleForgotPassword(e) {
                e.preventDefault();
                const email = document.getElementById('reset_email').value;
                const formData = new FormData(e.target);

                this.saveFormData('forgot-password', { email: email });
                document.getElementById('resetSuccess').style.display = 'none';
                document.getElementById('resetError').style.display = 'none';

                try {
                    const response = await fetch('{{ route("admin.password.email") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('verify_email').value = email;
                        this.saveFormData('code-verification', { email: email, countdownStarted: true });
                        this.showCodeVerification();
                        this.startCountdown();
                    } else {
                        document.getElementById('resetError').textContent = data.message;
                        document.getElementById('resetError').style.display = 'block';
                    }
                } catch (error) {
                    document.getElementById('resetError').textContent = 'Network error. Please try again.';
                    document.getElementById('resetError').style.display = 'block';
                }
            }

            async handleCodeVerification(e) {
                e.preventDefault();
                const formData = new FormData(e.target);
                const email = document.getElementById('verify_email').value;

                document.getElementById('codeSuccess').style.display = 'none';
                document.getElementById('codeError').style.display = 'none';

                try {
                    const response = await fetch('{{ route("admin.password.verify") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('final_email').value = email;
                        document.getElementById('final_token').value = data.reset_token;

                        this.saveFormData('new-password', {
                            email: email,
                            token: data.reset_token
                        });

                        this.showNewPasswordForm();
                    } else {
                        document.getElementById('codeError').textContent = data.message;
                        document.getElementById('codeError').style.display = 'block';
                    }
                } catch (error) {
                    document.getElementById('codeError').textContent = 'Network error. Please try again.';
                    document.getElementById('codeError').style.display = 'block';
                }
            }

            async handleNewPassword(e) {
                e.preventDefault();

                if (!this.passwordChecker.validate()) {
                    document.getElementById('passwordError').textContent = 'Please ensure all password requirements are met.';
                    document.getElementById('passwordError').style.display = 'block';
                    return;
                }

                const resetButton = document.getElementById('resetPasswordBtn');
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('email', document.getElementById('final_email').value);
                formData.append('token', document.getElementById('final_token').value);
                formData.append('password', document.getElementById('new_password').value);
                formData.append('password_confirmation', document.getElementById('confirm_password').value);

                document.getElementById('passwordSuccess').style.display = 'none';
                document.getElementById('passwordError').style.display = 'none';

                resetButton.disabled = true;
                resetButton.textContent = 'Resetting...';

                try {
                    const response = await fetch('{{ route("admin.password.update") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('passwordSuccess').textContent = 'Password reset successfully!';
                        document.getElementById('passwordSuccess').style.display = 'block';

                        sessionStorage.removeItem('currentForm');
                        this.clearFormData('new-password');
                        this.clearFormData('code-verification');
                        this.clearFormData('forgot-password');

                        history.replaceState(null, null, window.location.pathname);

                        setTimeout(() => {
                            window.location.href = '/login/admin';
                        }, 2000);
                    } else {
                        let errorMessage = 'Password reset failed. Please try again.';
                        if (data.errors && data.errors.password) {
                            errorMessage = data.errors.password[0];
                        } else if (data.message) {
                            errorMessage = data.message;
                        }
                        document.getElementById('passwordError').textContent = errorMessage;
                        document.getElementById('passwordError').style.display = 'block';
                    }
                } catch (error) {
                    document.getElementById('passwordError').textContent = 'Network error. Please try again.';
                    document.getElementById('passwordError').style.display = 'block';
                } finally {
                    resetButton.disabled = false;
                    resetButton.textContent = 'Reset Password';
                    this.passwordChecker.validate();
                }
            }


        }

        class PasswordChecker {
            constructor() {
                this.passwordInput = document.getElementById('new_password');
                this.confirmInput = document.getElementById('confirm_password');
                this.submitButton = document.getElementById('resetPasswordBtn');
                this.init();
            }

            init() {
                if (this.passwordInput && this.confirmInput) {
                    this.passwordInput.addEventListener('input', () => this.validate());
                    this.confirmInput.addEventListener('input', () => this.validate());
                }
            }

            validate() {
                const password = this.passwordInput.value;
                const confirm = this.confirmInput.value;

                const checks = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[!@#$%^&*(),.?":{}|<>]/.test(password),
                    match: password === confirm && password.length > 0
                };

                this.updateCheck('length-req', checks.length);
                this.updateCheck('uppercase-req', checks.uppercase);
                this.updateCheck('lowercase-req', checks.lowercase);
                this.updateCheck('number-req', checks.number);
                this.updateCheck('special-req', checks.special);
                this.updateCheck('password-match', checks.match);

                this.updateInputStyle(this.passwordInput, Object.values(checks).slice(0, 5).every(c => c));
                this.updateInputStyle(this.confirmInput, checks.match);

                const allValid = Object.values(checks).every(c => c);
                this.submitButton.disabled = !allValid;

                return allValid;
            }

            updateCheck(id, isValid) {
                const element = document.getElementById(id);
                if (!element) return;

                element.classList.remove('valid', 'invalid');
                if (isValid) {
                    element.classList.add('valid');
                } else if (this.passwordInput.value.length > 0 || this.confirmInput.value.length > 0) {
                    element.classList.add('invalid');
                }
            }

            updateInputStyle(input, isValid) {
                input.classList.remove('valid', 'invalid');
                if (input.value.length > 0) {
                    input.classList.add(isValid ? 'valid' : 'invalid');
                }
            }

            reset() {
                const elements = ['length-req', 'uppercase-req', 'lowercase-req', 'number-req', 'special-req', 'password-match'];
                elements.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.classList.remove('valid', 'invalid');
                    }
                });

                this.passwordInput.classList.remove('valid', 'invalid');
                this.confirmInput.classList.remove('valid', 'invalid');
                this.submitButton.disabled = true;
            }
        }

        let adminAuth;
        document.addEventListener('DOMContentLoaded', function () {
            adminAuth = new AdminAuth();
        });

    </script>

</body>

</html>