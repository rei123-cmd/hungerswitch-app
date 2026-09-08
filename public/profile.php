<?php 
//session_start();
include 'config.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Fetch user's orders count
$orders_query = "SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?";
$orders_stmt = mysqli_prepare($conn, $orders_query);
mysqli_stmt_bind_param($orders_stmt, "i", $user_id);
mysqli_stmt_execute($orders_stmt);
$orders_data = mysqli_fetch_assoc(mysqli_stmt_get_result($orders_stmt));
$total_orders = $orders_data['total_orders'] ?? 0;

$success = '';
$error = '';

if(isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    
    $update_query = "UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sssi", $name, $phone, $address, $user_id);
    
    if(mysqli_stmt_execute($update_stmt)) {
        $success = "Profil berhasil diperbarui!";
        $_SESSION['user_name'] = $name;
        // Refresh user data
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    } else {
        $error = "Gagal memperbarui profil.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Hungerswitch</title>
    
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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--page-bg);
            color: var(--dark-text);
            padding-top: 76px;
        }

        /* Navbar */
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
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            padding: 4rem 0 8rem 0;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: moveGrid 15s linear infinite;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        .profile-header-content {
            position: relative;
            z-index: 1;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin-top: -5rem;
            position: relative;
            z-index: 10;
            padding: 2.5rem;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto -75px auto;
            z-index: 15;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            border: 8px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            font-weight: 800;
            box-shadow: 0 10px 40px rgba(217, 35, 45, 0.3);
            transition: all 0.3s ease;
        }

        .profile-avatar:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 15px 50px rgba(217, 35, 45, 0.4);
        }

        .profile-info {
            text-align: center;
            padding-top: 5rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .profile-name {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .profile-email {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .profile-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, var(--light-beige), #ffffff);
            border-radius: 25px;
            font-weight: 600;
            color: var(--main-green);
            border: 2px solid var(--main-green);
        }

        /* Stats */
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-box {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--light-beige), #ffffff);
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            font-size: 1.8rem;
            color: var(--main-green);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--main-red);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.95rem;
        }

        /* Tab Navigation */
        .profile-tabs {
            margin-top: 3rem;
        }

        .nav-tabs {
            border: none;
            gap: 1rem;
        }

        .nav-tabs .nav-link {
            border: none;
            padding: 1rem 2rem;
            border-radius: 15px;
            font-weight: 600;
            color: #666;
            background: var(--light-beige);
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            background: #e6e2d9;
            color: var(--dark-text);
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white;
            box-shadow: 0 8px 20px rgba(47, 82, 51, 0.3);
        }

        .tab-content {
            margin-top: 2rem;
        }

        /* Form Styles */
        .form-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: var(--light-beige);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--main-green);
            font-size: 1.2rem;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--main-green);
            box-shadow: 0 0 0 4px rgba(47, 82, 51, 0.1);
        }

        .btn-update {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(47, 82, 51, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            color: var(--dark-text);
            border: 2px solid #e9ecef;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            border-color: var(--main-green);
            color: var(--main-green);
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
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

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: var(--main-green);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: var(--main-red);
        }

        @media (max-width: 768px) {
            .profile-name {
                font-size: 1.5rem;
            }
            
            .profile-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-logo-icon">HS</span>
                <div class="brand-text ms-2">
                    <strong>Hungerswitch</strong>
                    <div class="brand-subtext" style="font-size: 0.75rem; color: #6c757d;">Profil</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i><?php echo htmlspecialchars($user['name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item active" href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="profile-header">
        <div class="container profile-header-content">
            <h1 class="text-white text-center" style="font-size: 2.5rem; font-weight: 800;">Profil Saya</h1>
        </div>
    </div>

    <div class="container mb-5">
        <div class="profile-avatar-container">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-info">
                <h2 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h2>
                <div class="profile-email">
                    <i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?>
                </div>
                <span class="profile-badge">
                    <i class="bi bi-person-check me-2"></i><?php echo ucfirst($user['role']); ?>
                </span>
            </div>

            <div class="profile-stats">
                <div class="stat-box">
                    <div class="stat-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-value"><?php echo $total_orders; ?></div>
                    <div class="stat-label">Total Pesanan</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <div class="stat-value">0</div>
                    <div class="stat-label">Donasi</div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div class="stat-value">0</div>
                    <div class="stat-label">Poin Reward</div>
                </div>
            </div>

            <div class="profile-tabs">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                            <i class="bi bi-person-lines-fill me-2"></i>Informasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">
                            <i class="bi bi-shield-lock me-2"></i>Keamanan
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <?php if($success): ?>
                        <div class="alert alert-success mt-3">
                            <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger mt-3">
                            <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Info Tab -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <div class="form-section">
                            <h3 class="form-section-title">
                                <div class="section-icon"><i class="bi bi-person-fill"></i></div>
                                Informasi Pribadi
                            </h3>
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor Telepon</label>
                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Bergabung</label>
                                        <input type="text" class="form-control" value="<?php echo date('d F Y', strtotime($user['created_at'])); ?>" disabled>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alamat (Opsional)</label>
                                        <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="mt-4 d-flex gap-3">
                                    <button type="submit" name="update_profile" class="btn btn-update">
                                        <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                                    </button>
                                    <a href="index.php" class="btn btn-secondary-custom">
                                        <i class="bi bi-x-circle me-2"></i>Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="form-section">
                            <h3 class="form-section-title">
                                <div class="section-icon"><i class="bi bi-key-fill"></i></div>
                                Ubah Password
                            </h3>
                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Password Lama</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" name="new_password" minlength="6" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" name="change_password" class="btn btn-update">
                                        <i class="bi bi-shield-check me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
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