<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #667eea 100%);
            min-height: 100vh;
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .login-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 25px;
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .form-control {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.5);
        }
        .school-logo {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .credentials-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
        }
        .credentials-box h6 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .credential-item {
            font-size: 0.75rem;
            color: #6c757d;
            padding: 3px 0;
        }
        .credential-item strong {
            color: #495057;
        }
        .toggle-password {
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s;
        }
        .toggle-password:hover {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="school-logo">
                                <i class="bi bi-mortarboard-fill text-white fs-1"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">School ERP</h3>
                            <p class="text-muted mb-0">Welcome back! Please sign in to your account</p>
                        </div>
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="Enter your email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-lock me-2"></i>Password
                                </label>
                                <div class="position-relative">
                                    <input type="password" class="form-control @error('password') is-invalid @else @enderror" 
                                           id="password" name="password" placeholder="Enter your password" required>
                                    <span class="position-absolute end-0 top-50 translate-middle-y me-3 toggle-password" 
                                          onclick="togglePassword()" style="border: none; background: none; cursor: pointer;">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label text-muted" for="remember">
                                        Remember me
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
                            
                            <div class="text-center">
                                <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #667eea;">
                                    Forgot Password?
                                </a>
                            </div>
                        </form>
                        
                        <!-- Login Credentials Box -->
                        <div class="credentials-box">
                            <h6><i class="bi bi-info-circle me-2"></i>Demo Login Credentials</h6>
                            <div class="row">
                                <div class="col-6">
                                    <div class="credential-item"><strong>Admin:</strong> admin@schoolerp.com</div>
                                    <div class="credential-item"><strong>Principal:</strong> principal@schoolerp.com</div>
                                    <div class="credential-item"><strong>Teacher:</strong> teacher@schoolerp.com</div>
                                </div>
                                <div class="col-6">
                                    <div class="credential-item"><strong>Accountant:</strong> accountant@schoolerp.com</div>
                                    <div class="credential-item"><strong>Librarian:</strong> librarian@schoolerp.com</div>
                                    <div class="credential-item"><strong>Student:</strong> student@schoolerp.com</div>
                                </div>
                            </div>
                            <div class="credential-item mt-2 text-center" style="color: #667eea; font-weight: 600;">
                                <i class="bi bi-key me-1"></i>Password: password
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-white-50 small mb-0">
                        © 2025 School ERP System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
