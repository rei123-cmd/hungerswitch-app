<?php
// File: agent_beneficiaries.php - Part 1: HTML Head & Initialization
session_start();
include 'config.php';

// Cek Login Agen
if(!isset($_SESSION['user_id']) || !in_array('agent', $_SESSION['user_roles'] ?? [])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil Data Agen
$q_agent = mysqli_query($conn, "SELECT * FROM agents WHERE user_id = $user_id");
$agent = mysqli_fetch_assoc($q_agent);
$agent_id = $agent['id'];

// Handle Form Submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if($_POST['action'] == 'add') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $family_size = (int)$_POST['family_size'];
        $income_level = mysqli_real_escape_string($conn, $_POST['income_level']);
        $verification_status = mysqli_real_escape_string($conn, $_POST['verification_status']);
        
        $insert = mysqli_query($conn, "INSERT INTO beneficiaries 
            (name, id_number, phone_number, address, family_size, income_level, verification_status, registered_by_agent_id, registration_date, is_active) 
            VALUES 
            ('$name', '$id_number', '$phone', '$address', $family_size, '$income_level', '$verification_status', $agent_id, NOW(), 1)");
        
        if($insert) {
            $success_msg = "Penerima manfaat berhasil ditambahkan!";
        }
    }
    
    if($_POST['action'] == 'update') {
        $beneficiary_id = (int)$_POST['beneficiary_id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $family_size = (int)$_POST['family_size'];
        $income_level = mysqli_real_escape_string($conn, $_POST['income_level']);
        $verification_status = mysqli_real_escape_string($conn, $_POST['verification_status']);
        
        $update = mysqli_query($conn, "UPDATE beneficiaries SET 
            name='$name', phone_number='$phone', address='$address', 
            family_size=$family_size, income_level='$income_level', 
            verification_status='$verification_status' 
            WHERE id=$beneficiary_id AND registered_by_agent_id=$agent_id");
        
        if($update) {
            $success_msg = "Data penerima berhasil diperbarui!";
        }
    }
    
    if($_POST['action'] == 'deactivate') {
        $beneficiary_id = (int)$_POST['beneficiary_id'];
        mysqli_query($conn, "UPDATE beneficiaries SET is_active=0 WHERE id=$beneficiary_id AND registered_by_agent_id=$agent_id");
        $success_msg = "Penerima manfaat dinonaktifkan!";
    }
}

// Statistik Penerima
$stats = [];
$stats['total'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM beneficiaries WHERE registered_by_agent_id = $agent_id"));
$stats['active'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM beneficiaries WHERE registered_by_agent_id = $agent_id AND is_active = 1"));
$stats['verified'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM beneficiaries WHERE registered_by_agent_id = $agent_id AND verification_status = 'verified'"));
$stats['pending'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM beneficiaries WHERE registered_by_agent_id = $agent_id AND verification_status = 'pending'"));

// Filter & Search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

$where_clause = "WHERE registered_by_agent_id = $agent_id";
if($search) {
    $where_clause .= " AND (name LIKE '%$search%' OR phone_number LIKE '%$search%' OR id_number LIKE '%$search%')";
}
if($filter_status) {
    $where_clause .= " AND verification_status = '$filter_status'";
}

// Ambil Data Penerima
$beneficiaries = mysqli_query($conn, "SELECT * FROM beneficiaries $where_clause ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Data Penerima Manfaat - Hungerswitch</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --hs-green: #2F5233;
            --hs-red: #D9232D;
            --hs-cream: #FCFCF8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--hs-cream);
            color: #333;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--hs-green), #1a3a1f);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            padding: 2rem 0;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            padding: 0 1.5rem;
            margin-bottom: 2rem;
            color: white;
            animation: slideInLeft 0.5s ease;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-nav a {
            display: block;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .sidebar-nav a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transition: width 0.3s ease;
            z-index: -1;
        }
        
        .sidebar-nav a:hover::before,
        .sidebar-nav a.active::before {
            width: 100%;
        }
        
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            color: white;
            border-left-color: var(--hs-red);
            transform: translateX(5px);
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            animation: fadeIn 0.5s ease;
        }
        
        /* Header Animation */
        .page-header {
            animation: slideInDown 0.6s ease;
        }
        
        /* Stats Cards dengan Animasi */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 4px solid var(--hs-green);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: scaleIn 0.5s ease;
            animation-fill-mode: both;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        .stat-card:hover::after {
            opacity: 1;
        }
        
        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--hs-green);
            animation: countUp 1s ease;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover .stat-icon {
            transform: rotate(360deg) scale(1.1);
        }
        
        /* Search & Filter Section */
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: slideInUp 0.6s ease;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            border-radius: 25px;
            padding: 0.75rem 1.5rem 0.75rem 3rem;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            border-color: var(--hs-green);
            box-shadow: 0 0 0 3px rgba(47,82,51,0.1);
            transform: translateY(-2px);
        }
        
        .search-box i {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        /* Beneficiary Card dengan Animasi Kreatif */
        .beneficiary-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: slideInRight 0.5s ease;
            animation-fill-mode: both;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .beneficiary-card::before {
            content: '';
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }
        
        .beneficiary-card:hover::before {
            left: 100%;
        }
        
        .beneficiary-card:hover {
            transform: translateX(10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            border-left-color: var(--hs-green);
        }
        
        .beneficiary-card:nth-child(odd) {
            animation-delay: 0.1s;
        }
        
        .beneficiary-card:nth-child(even) {
            animation-delay: 0.2s;
        }
        
        .avatar-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--hs-green), #4a7c4e);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(47,82,51,0.3);
        }
        
        .beneficiary-card:hover .avatar-circle {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 20px rgba(47,82,51,0.5);
        }
        
        /* Badge Styles dengan Pulse Animation */
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        /* Button Animations */
        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn-action::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.5s ease, height 0.5s ease;
            z-index: -1;
        }
        
        .btn-action:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        .btn-action:active {
            transform: translateY(-1px);
        }
        
        /* Modal Animations */
        .modal.fade .modal-dialog {
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform: scale(0.7) translateY(-100px);
        }
        
        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }
        
        /* Form Input Animations */
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--hs-green);
            box-shadow: 0 0 0 4px rgba(47,82,51,0.1);
            transform: translateY(-2px);
        }
        
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 0.5rem;
        }
        
        /* Alert Animation */
        .alert {
            border-radius: 12px;
            border: none;
            animation: slideInDown 0.5s ease;
        }
        
        /* Loading Spinner */
        .spinner-container {
            display: none;
            text-align: center;
            padding: 3rem;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
            animation: spin 0.8s linear infinite;
        }
        
        /* Keyframe Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-shield-check me-2"></i>Hungerswitch
        </div>
        
        <ul class="sidebar-nav">
            <li><a href="agent_dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li><a href="agent_programs.php"><i class="bi bi-calendar-check me-2"></i>Program Saya</a></li>
            <li><a href="agent_beneficiaries.php" class="active"><i class="bi bi-people me-2"></i>Data Penerima</a></li>
            <li><a href="agent_reports.php"><i class="bi bi-file-text me-2"></i>Laporan</a></li>
            <li><hr class="border-light opacity-25 my-3"></li>
            <li><a href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
            <li><a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">
                    <i class="bi bi-people-fill me-2" style="color: var(--hs-green);"></i>
                    Data Penerima Manfaat
                </h2>
                <p class="text-muted mb-0">Kelola dan verifikasi penerima bantuan makanan</p>
            </div>
            <button class="btn btn-action" style="background: var(--hs-red); color: white;" data-bs-toggle="modal" data-bs-target="#addBeneficiaryModal">
                <i class="bi bi-person-plus-fill me-2"></i>Tambah Penerima Baru
            </button>
        </div>

        <!-- Alert Success/Error -->
        <?php if(isset($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Total Penerima</div>
                            <div class="stat-number"><?php echo $stats['total']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(47,82,51,0.1);">
                            <i class="bi bi-people-fill" style="font-size: 1.8rem; color: var(--hs-green);"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #28a745;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Aktif</div>
                            <div class="stat-number" style="color: #28a745;"><?php echo $stats['active']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(40,167,69,0.1);">
                            <i class="bi bi-check-circle-fill" style="font-size: 1.8rem; color: #28a745;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #007bff;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Terverifikasi</div>
                            <div class="stat-number" style="color: #007bff;"><?php echo $stats['verified']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(0,123,255,0.1);">
                            <i class="bi bi-shield-check" style="font-size: 1.8rem; color: #007bff;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #ffc107;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Menunggu</div>
                            <div class="stat-number" style="color: #ffc107;"><?php echo $stats['pending']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(255,193,7,0.1);">
                            <i class="bi bi-hourglass-split" style="font-size: 1.8rem; color: #ffc107;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-search me-1"></i>Cari Penerima
                        </label>
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" name="search" placeholder="Nama, No. KTP, atau No. Telepon" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="bi bi-funnel me-1"></i>Filter Status
                        </label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                            <option value="verified" <?php echo $filter_status == 'verified' ? 'selected' : ''; ?>>Terverifikasi</option>
                            <option value="rejected" <?php echo $filter_status == 'rejected' ? 'selected' : ''; ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-action w-100" style="background: var(--hs-green); color: white;">
                            <i class="bi bi-filter me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Beneficiaries List -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px; animation: slideInUp 0.7s ease;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2"></i>Daftar Penerima Manfaat
                </h5>
            </div>
            <div class="card-body">
                <?php if(mysqli_num_rows($beneficiaries) > 0): ?>
                    <?php $index = 0; while($b = mysqli_fetch_assoc($beneficiaries)): 
                        $status_colors = [
                            'pending' => 'warning',
                            'verified' => 'success',
                            'rejected' => 'danger'
                        ];
                        $status_text = [
                            'pending' => 'Menunggu Verifikasi',
                            'verified' => 'Terverifikasi',
                            'rejected' => 'Ditolak'
                        ];
                        $index++;
                    ?>
                    <div class="beneficiary-card" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-circle">
                                    <?php echo strtoupper(substr($b['name'], 0, 1)); ?>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($b['name']); ?></h6>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span><i class="bi bi-card-text me-1"></i><?php echo htmlspecialchars($b['id_number']); ?></span>
                                    <span><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($b['phone_number']); ?></span>
                                    <span><i class="bi bi-people me-1"></i>Keluarga: <?php echo $b['family_size']; ?> orang</span>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($b['address']); ?>
                                    </small>
                                </div>
                            </div>
                            <div class="col-auto text-end">
                                <span class="badge-status bg-<?php echo $status_colors[$b['verification_status']]; ?> text-white d-inline-block mb-2">
                                    <?php echo $status_text[$b['verification_status']]; ?>
                                </span>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button class="btn btn-sm btn-action" style="background: var(--hs-green); color: white;" onclick="viewBeneficiary(<?php echo $b['id']; ?>)" title="Lihat Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-action" style="background: #007bff; color: white;" onclick="editBeneficiary(<?php echo $b['id']; ?>)" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <?php if($b['is_active']): ?>
                                    <button class="btn btn-sm btn-action" style="background: var(--hs-red); color: white;" onclick="deactivateBeneficiary(<?php echo $b['id']; ?>)" title="Nonaktifkan">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #ddd;"></i>
                        <p class="text-muted mt-3 fs-5">Belum ada data penerima manfaat</p>
                        <button class="btn btn-action mt-2" style="background: var(--hs-red); color: white;" data-bs-toggle="modal" data-bs-target="#addBeneficiaryModal">
                            <i class="bi bi-person-plus me-2"></i>Tambah Penerima Pertama
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal: Add Beneficiary -->
    <div class="modal fade" id="addBeneficiaryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--hs-green), #4a7c4e); color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i>Tambah Penerima Manfaat Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person me-1"></i>Nama Lengkap *
                                </label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-card-text me-1"></i>No. KTP *
                                </label>
                                <input type="text" class="form-control" name="id_number" required maxlength="16">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-telephone me-1"></i>No. Telepon
                                </label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-people me-1"></i>Jumlah Anggota Keluarga *
                                </label>
                                <input type="number" class="form-control" name="family_size" required min="1">
                            </div>
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bi bi-geo-alt me-1"></i>Alamat Lengkap *
                                </label>
                                <textarea class="form-control" name="address" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-cash-stack me-1"></i>Tingkat Penghasilan *
                                </label>
                                <select class="form-select" name="income_level" required>
                                    <option value="">Pilih...</option>
                                    <option value="very_low">Sangat Rendah (< Rp 500.000)</option>
                                    <option value="low">Rendah (Rp 500.000 - Rp 1.500.000)</option>
                                    <option value="medium">Menengah (> Rp 1.500.000)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-shield-check me-1"></i>Status Verifikasi *
                                </label>
                                <select class="form-select" name="verification_status" required>
                                    <option value="pending">Menunggu Verifikasi</option>
                                    <option value="verified">Terverifikasi</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-action" style="background: var(--hs-green); color: white;">
                            <i class="bi bi-save me-2"></i>Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // View Beneficiary Detail
        function viewBeneficiary(id) {
            alert('Detail penerima ID: ' + id + '\n\nFitur ini akan menampilkan riwayat penerimaan bantuan dan detail lengkap.');
        }

        // Edit Beneficiary
        function editBeneficiary(id) {
            alert('Edit penerima ID: ' + id + '\n\nModal edit akan muncul dengan data yang sudah terisi.');
        }

        // Deactivate Beneficiary
        function deactivateBeneficiary(id) {
            if(confirm('Apakah Anda yakin ingin menonaktifkan penerima ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="deactivate">
                    <input type="hidden" name="beneficiary_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>