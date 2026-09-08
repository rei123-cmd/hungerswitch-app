<?php 
//session_start();
include 'config.php';

$error = '';
$success = '';

// Proses Login CUSTOMER ONLY
if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Query hanya untuk customer dan partner (bukan agent)
    $query = "SELECT * FROM users WHERE email = ? AND role IN ('customer', 'partner')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect ke profile untuk semua user
            header("Location: profile.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan atau Anda adalah agen. Silakan gunakan portal agen untuk login.";
    }
}

// Proses Sign Up
if(isset($_POST['signup'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    
    // Validasi input
    if(empty($name) || empty($email) || empty($password) || empty($phone)) {
        $error = "Semua field harus diisi!";
    } else if(strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah email sudah terdaftar
        $check = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $query = "INSERT INTO users (name, email, password, phone, role, status, created_at) VALUES (?, ?, ?, ?, 'customer', 'active', NOW())";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $phone);
            
            if(mysqli_stmt_execute($stmt)) {
                $success = "Registrasi berhasil! Silakan login.";
                // Auto switch to login tab
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(function() {
                            document.getElementById('signin-tab').click();
                        }, 100);
                    });
                </script>";
            } else {
                $error = "Terjadi kesalahan: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hungerswitch</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

        :root {
            --page-bg: #FCFCF8;
            --main-red: #D9232D;
            --main-green: #2F5233;
            --dark-text: #333;
            --light-beige: #F6F4EB;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--page-bg) 0%, #f8f6f1 100%);
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(217, 35, 45, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(47, 82, 51, 0.05) 0%, transparent 50%);
            z-index: 0;
            animation: bgFloat 10s ease-in-out infinite;
        }

        @keyframes bgFloat {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 30px); }
        }

        .navbar {
            padding: 1rem 0;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .navbar-brand .brand-logo-icon {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.3);
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover .brand-logo-icon {
            transform: scale(1.05) rotate(-5deg);
        }

        .login-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 1rem 3rem;
            position: relative;
            z-index: 1;
        }

        .login-container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            animation: containerSlideUp 0.8s ease;
        }

        @keyframes containerSlideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: moveGrid 15s linear infinite;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        .login-header h2 {
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            margin: 0;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .login-body {
            padding: 3rem 2.5rem;
        }

        .nav-tabs {
            border: none;
            margin-bottom: 2rem;
            justify-content: center;
            gap: 1rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 1rem 2.5rem;
            border-radius: 15px;
            background: var(--light-beige);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-tabs .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(217, 35, 45, 0.1), transparent);
            transition: left 0.5s;
        }

        .nav-tabs .nav-link:hover::before {
            left: 100%;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(217, 35, 45, 0.3);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--main-red);
            box-shadow: 0 0 0 4px rgba(217, 35, 45, 0.1);
            transform: translateY(-2px);
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--main-red);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            border: none;
            color: white;
            font-weight: 700;
            padding: 1rem;
            border-radius: 12px;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-submit:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(217, 35, 45, 0.4);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .alert {
            border-radius: 12px;
            border: none;
            animation: alertSlide 0.5s ease;
        }

        @keyframes alertSlide {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee, #fdd);
            color: var(--main-red);
        }

        .alert-success {
            background: linear-gradient(135deg, #efe, #dfd);
            color: var(--main-green);
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #dee2e6, transparent);
        }

        .divider span {
            background: white;
            padding: 0 1.5rem;
            position: relative;
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .btn-social {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            background: white;
        }

        .btn-social:hover {
            border-color: var(--main-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .form-check-input:checked {
            background-color: var(--main-red);
            border-color: var(--main-red);
        }

        .agent-login-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #f0f0f0;
        }

        .agent-login-link a {
            color: var(--main-green);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: 2px solid var(--main-green);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .agent-login-link a:hover {
            background: var(--main-green);
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .login-body {
                padding: 2rem 1.5rem;
            }
            .nav-tabs .nav-link {
                padding: 0.75rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        .tab-pane {
            animation: tabFade 0.4s ease;
        }

        @keyframes tabFade {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-logo-icon">HS</span>
                <div class="brand-text ms-2">
                    <strong>Hungerswitch</strong>
                    <div style="font-size: 0.75rem; color: #6c757d;">Selamatkan Makanan</div>
                </div>
            </a>
        </div>
    </nav>

    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="login-container">
                        <div class="login-header">
                            <h2>Selamat Datang di Hungerswitch</h2>
                            <p>Selamatkan makanan, bantu sesama</p>
                        </div>
                        
                        <div class="login-body">
                            <?php if($error): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($success): ?>
                                <div class="alert alert-success" role="alert">
                                    <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                                </div>
                            <?php endif; ?>

                            <ul class="nav nav-tabs" id="authTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="signin-tab" data-bs-toggle="tab" 
                                            data-bs-target="#signin" type="button">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="signup-tab" data-bs-toggle="tab" 
                                            data-bs-target="#signup" type="button">
                                        <i class="bi bi-person-plus me-2"></i>Sign Up
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="authTabContent">
                                <!-- Sign In Tab -->
                                <div class="tab-pane fade show active" id="signin" role="tabpanel">
                                    <form method="POST" action="">
                                        <div class="mb-3">
                                            <label for="login-email" class="form-label">
                                                <i class="bi bi-envelope"></i>Email
                                            </label>
                                            <input type="email" class="form-control" id="login-email" 
                                                   name="email" required placeholder="nama@email.com">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="login-password" class="form-label">
                                                <i class="bi bi-lock"></i>Password
                                            </label>
                                            <div class="password-wrapper">
                                                <input type="password" class="form-control" id="login-password" 
                                                       name="password" required placeholder="Masukkan password">
                                                <i class="bi bi-eye password-toggle" onclick="togglePassword('login-password', this)"></i>
                                            </div>
                                        </div>

                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="remember">
                                            <label class="form-check-label" for="remember">
                                                Ingat saya
                                            </label>
                                        </div>

                                        <button type="submit" name="login" class="btn btn-submit">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                                        </button>

                                        <div class="text-center mt-3">
                                            <a href="#" class="text-decoration-none" style="color: var(--main-red); font-weight: 600;">
                                                Lupa password?
                                            </a>
                                        </div>
                                    </form>
                                </div>

                                <!-- Sign Up Tab -->
                                <div class="tab-pane fade" id="signup" role="tabpanel">
                                    <form method="POST" action="">
                                        <div class="mb-3">
                                            <label for="signup-name" class="form-label">
                                                <i class="bi bi-person"></i>Nama Lengkap
                                            </label>
                                            <input type="text" class="form-control" id="signup-name" 
                                                   name="name" required placeholder="Nama lengkap Anda">
                                        </div>

                                        <div class="mb-3">
                                            <label for="signup-email" class="form-label">
                                                <i class="bi bi-envelope"></i>Email
                                            </label>
                                            <input type="email" class="form-control" id="signup-email" 
                                                   name="email" required placeholder="nama@email.com">
                                        </div>

                                        <div class="mb-3">
                                            <label for="signup-phone" class="form-label">
                                                <i class="bi bi-phone"></i>Nomor Telepon
                                            </label>
                                            <input type="tel" class="form-control" id="signup-phone" 
                                                   name="phone" required placeholder="08xxxxxxxxxx" pattern="[0-9]{10,13}">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="signup-password" class="form-label">
                                                <i class="bi bi-lock"></i>Password
                                            </label>
                                            <div class="password-wrapper">
                                                <input type="password" class="form-control" id="signup-password" 
                                                       name="password" required placeholder="Minimal 6 karakter" minlength="6">
                                                <i class="bi bi-eye password-toggle" onclick="togglePassword('signup-password', this)"></i>
                                            </div>
                                        </div>

                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="terms" required>
                                            <label class="form-check-label" for="terms">
                                                Saya setuju dengan <a href="#" style="color: var(--main-red); font-weight: 600;">syarat dan ketentuan</a>
                                            </label>
                                        </div>

                                        <button type="submit" name="signup" class="btn btn-submit">
                                            <i class="bi bi-person-plus me-2"></i>Daftar
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="divider">
                                <span>atau lanjutkan dengan</span>
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <button class="btn btn-social w-100">
                                        <i class="bi bi-google me-2"></i>Google
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-social w-100">
                                        <i class="bi bi-facebook me-2"></i>Facebook
                                    </button>
                                </div>
                            </div>

                            <div class="agent-login-link">
                                <p class="text-muted mb-3">Apakah Anda seorang agen?</p>
                                <a href="agen.php">
                                    <i class="bi bi-shield-check"></i>
                                    Login sebagai Agen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, icon) {
            const passwordInput = document.getElementById(inputId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>