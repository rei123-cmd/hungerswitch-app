<?php
// File: login_agen.php
// JANGAN sertakan session_start() atau config.php di sini, 
// karena file ini akan di-include oleh agen.php yang sudah memilikinya.

$error = '';
$success = '';

// Proses Login Agen
if(isset($_POST['login_agent'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE email = ? AND role = 'agent'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['password'])) {
            // Login sukses
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Reload halaman untuk masuk ke dashboard
            header("Location: agen.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email agen tidak ditemukan!";
    }
}

// Proses Sign Up Agen
if(isset($_POST['signup_agent'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $region = mysqli_real_escape_string($conn, trim($_POST['region']));
    
    // Validasi
    if(empty($name) || empty($email) || empty($password) || empty($phone) || empty($region)) {
        $error = "Semua field harus diisi!";
    } elseif(strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek email duplikat
        $check = "SELECT * FROM users WHERE email = ?";
        $stmt_check = mysqli_prepare($conn, $check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        if(mysqli_stmt_get_result($stmt_check)->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Gunakan Transaksi Database
            mysqli_begin_transaction($conn);
            try {
                // 1. Insert ke tabel 'users'
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query_user = "INSERT INTO users (name, email, password, phone, role, status, created_at) 
                               VALUES (?, ?, ?, ?, 'agent', 'active', NOW())";
                $stmt_user = mysqli_prepare($conn, $query_user);
                mysqli_stmt_bind_param($stmt_user, "ssss", $name, $email, $hashed_password, $phone);
                
                if(!mysqli_stmt_execute($stmt_user)) {
                    throw new Exception("Gagal mendaftarkan user.");
                }
                
                $new_user_id = mysqli_insert_id($conn);
                
                // 2. Insert ke tabel 'agents'
                $query_agent = "INSERT INTO agents (user_id, region, created_at) VALUES (?, ?, NOW())";
                $stmt_agent = mysqli_prepare($conn, $query_agent);
                mysqli_stmt_bind_param($stmt_agent, "is", $new_user_id, $region);
                
                if(!mysqli_stmt_execute($stmt_agent)) {
                    throw new Exception("Gagal mendaftarkan agen.");
                }
                
                // Jika semua sukses
                mysqli_commit($conn);
                $success = "Registrasi agen berhasil! Silakan login.";
                
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = $e->getMessage();
            }
        }
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --page-bg: #FCFCF8;
        --main-red: #D9232D;
        --main-green: #2F5233;
        --dark-text: #333;
        --light-beige: #F6F4EB;
    }
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, var(--page-bg) 0%, #f8f6f1 100%);
        color: var(--dark-text);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }
    .login-container {
        background: white;
        border-radius: 30px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        max-width: 900px;
        width: 100%;
    }
    .login-header {
        background: linear-gradient(135deg, var(--main-green), #3d6b42);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
    }
    .login-header h2 { font-weight: 800; }
    .login-body { padding: 3rem 2.5rem; }
    .nav-tabs { border: none; margin-bottom: 2rem; justify-content: center; gap: 1rem; }
    .nav-tabs .nav-link {
        border: none; color: #6c757d; font-weight: 600;
        padding: 1rem 2.5rem; border-radius: 15px;
        background: var(--light-beige); transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, var(--main-green), #3d6b42);
        color: white; transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(47, 82, 51, 0.3);
    }
    .form-label { font-weight: 600; }
    .form-control, .form-select {
        border: 2px solid #e9ecef; border-radius: 12px;
        padding: 0.875rem 1rem; font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--main-green);
        box-shadow: 0 0 0 4px rgba(47, 82, 51, 0.1);
    }
    .btn-submit {
        background: linear-gradient(135deg, var(--main-green), #3d6b42);
        border: none; color: white; font-weight: 700;
        padding: 1rem; border-radius: 12px; width: 100%;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(47, 82, 51, 0.4);
    }
    .alert-danger { background: #fee; color: var(--main-red); }
    .alert-success { background: #efe; color: var(--main-green); }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="login-container">
                <div class="login-header">
                    <h2>Portal Agen Hungerswitch</h2>
                    <p>Bergabunglah sebagai agen untuk mendistribusikan makanan dan membuat dampak.</p>
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
                            <button class="nav-link active" id="signin-tab" data-bs-toggle="tab" data-bs-target="#signin" type="button">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup" type="button">
                                <i class="bi bi-person-plus me-2"></i>Sign Up
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="authTabContent">
                        <div class="tab-pane fade show active" id="signin" role="tabpanel">
                            <form method="POST" action="agen.php">
                                <div class="mb-3">
                                    <label for="login-email" class="form-label">Email Agen</label>
                                    <input type="email" class="form-control" id="login-email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="login-password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="login-password" name="password" required>
                                </div>
                                <button type="submit" name="login_agent" class="btn btn-submit">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                                </button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="signup" role="tabpanel">
                            <form method="POST" action="agen.php">
                                <div class="mb-3">
                                    <label for="signup-name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="signup-name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signup-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="signup-email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signup-phone" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" id="signup-phone" name="phone" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signup-region" class="form-label">Wilayah (Contoh: Tangerang Selatan)</label>
                                    <input type="text" class="form-control" id="signup-region" name="region" required>
                                </div>
                                <div class="mb-3">
                                    <label for="signup-password" class="form-label">Password (Min. 6 karakter)</label>
                                    <input type="password" class="form-control" id="signup-password" name="password" required minlength="6">
                                </div>
                                <button type="submit" name="signup_agent" class="btn btn-submit">
                                    <i class="bi bi-person-plus me-2"></i>Daftar Sebagai Agen
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>