<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.ico') }}?v=2" type="image/x-icon">
    <title>Login - Manajemen Inventaris Produksi Jenang Mirah</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(145deg, #F24455 0%, #E5203A 20%, #660F24 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(145deg, #F24455 0%, #E5203A 50%, #660F24 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .login-body {
            padding: 40px;
            background: white;
        }
        
        .form-control:focus {
            border-color: hsl(355, 75%, 53%);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(145deg, #F24455 0%, #E5203A 50%, #660F24 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 9, 46, 0.4);
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        
        .form-control {
            border-left: none;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .password-toggle {
            cursor: pointer;
            background-color: #f8f9fa;
            border-left: none;
        }
    </style>
</head>
<body>
    <div class="container min-vh-100">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-sm-10 col-md-7 col-lg-5">
                <div class="login-card">
                    <div class="login-header">
                        <img src="{{ asset('img/logo.png') }}" 
                            alt="Logo" 
                            class="mb-2 d-block mx-auto"
                            style="width: 80px; height: auto;">
                        <h3 class="mb-0">Manajemen Inventaris Produksi Jenang Mirah</h3>
                        <p class="mb-0 mt-2">Silakan login untuk melanjutkan</p>
                    </div>
            
                    <div class="login-body">
                        <!-- Alert Success -->
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif
                        
                        <!-- Alert Error -->
                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif
                    
                        <!-- Login Form -->
                        <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                            @csrf
                            
                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control @error('username') is-invalid @enderror" 
                                        id="username" 
                                        name="username" 
                                        placeholder="Masukkan username"
                                        value="{{ old('username') }}"
                                        required
                                    >
                                </div>
                                @error('username')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input 
                                        type="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Masukkan password"
                                        required
                                    >
                                    <span class="input-group-text password-toggle" onclick="togglePassword()">
                                        <i class="bi bi-eye-fill" id="toggleIcon"></i>
                                    </span>
                                </div>
                                @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat Saya
                                </label>
                            </div> --}}
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Login
                            </button>
                        </form>
                        
                        <!-- Info Akun Demo -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <small class="text-muted">
                                <strong>Akun Demo:</strong><br>
                                Admin: <code>admin / 1234</code><br>
                                Produksi: <code>produksi / 12345</code><br>
                                Owner: <code>owner / 123456</code>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // AUTO FOCUS KE USERNAME - Dijalankan setelah halaman selesai load
        window.addEventListener('DOMContentLoaded', function() {
            // Tunggu sebentar untuk memastikan semua elemen sudah siap
            setTimeout(function() {
                const usernameInput = document.getElementById('username');
                if (usernameInput) {
                    usernameInput.focus();
                    // Pindahkan cursor ke akhir text jika ada value
                    usernameInput.setSelectionRange(usernameInput.value.length, usernameInput.value.length);
                }
            }, 100);
        });
        
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-fill');
                toggleIcon.classList.add('bi-eye-slash-fill');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash-fill');
                toggleIcon.classList.add('bi-eye-fill');
            }
        }
        
        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>