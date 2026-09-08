<?php
// File: agent_reports.php - Part 1: HTML Head & Initialization
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

// Handle Form Submission (Generate Report)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if($_POST['action'] == 'generate_report') {
        $program_id = (int)$_POST['program_id'];
        $report_type = mysqli_real_escape_string($conn, $_POST['report_type']);
        $notes = mysqli_real_escape_string($conn, $_POST['notes']);
        
        $insert = mysqli_query($conn, "INSERT INTO distribution_reports 
            (program_id, agent_id, report_type, report_date, notes, created_at) 
            VALUES 
            ($program_id, $agent_id, '$report_type', NOW(), '$notes', NOW())");
        
        if($insert) {
            $success_msg = "Laporan berhasil dibuat!";
        }
    }
}

// Statistik Laporan
$stats = [];
$stats['total_reports'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM distribution_reports WHERE agent_id = $agent_id"));
$stats['this_month'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM distribution_reports WHERE agent_id = $agent_id AND MONTH(report_date) = MONTH(NOW()) AND YEAR(report_date) = YEAR(NOW())"));
$stats['completed_programs'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM food_programs WHERE assigned_agent_id = $agent_id AND status = 'completed'"));

// Total Impact
$impact = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        SUM(quantity_distributed) as total_portions,
        COUNT(DISTINCT DATE(scheduled_date)) as distribution_days
    FROM food_programs 
    WHERE assigned_agent_id = $agent_id AND status = 'completed'
"));
$stats['total_portions'] = $impact['total_portions'] ?? 0;
$stats['distribution_days'] = $impact['distribution_days'] ?? 0;

// Filter
$filter_month = isset($_GET['month']) ? mysqli_real_escape_string($conn, $_GET['month']) : date('Y-m');
$filter_type = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : '';

$where_clause = "WHERE dr.agent_id = $agent_id";
if($filter_month) {
    $where_clause .= " AND DATE_FORMAT(dr.report_date, '%Y-%m') = '$filter_month'";
}
if($filter_type) {
    $where_clause .= " AND dr.report_type = '$filter_type'";
}

// Ambil Laporan
$reports = mysqli_query($conn, "
    SELECT dr.*, fp.program_name, fp.scheduled_date, fp.quantity_distributed, fp.distribution_location
    FROM distribution_reports dr
    JOIN food_programs fp ON dr.program_id = fp.id
    $where_clause
    ORDER BY dr.report_date DESC
");

// Ambil Program untuk Dropdown
$programs = mysqli_query($conn, "
    SELECT * FROM food_programs 
    WHERE assigned_agent_id = $agent_id 
    AND status IN ('in_progress', 'completed')
    ORDER BY scheduled_date DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Distribusi - Hungerswitch</title>
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
        
        /* Page Header Animation */
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
        
        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: slideInUp 0.6s ease;
        }
        
        /* Report Card dengan Animasi Kreatif */
        .report-card {
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
        
        .report-card::before {
            content: '';
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }
        
        .report-card:hover::before {
            left: 100%;
        }
        
        .report-card:hover {
            transform: translateX(10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            border-left-color: var(--hs-green);
        }
        
        .report-card:nth-child(odd) {
            animation-delay: 0.1s;
        }
        
        .report-card:nth-child(even) {
            animation-delay: 0.2s;
        }
        
        /* Report Type Badge */
        .report-type-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            animation: pulse 2s infinite;
        }
        
        /* Timeline Style for Reports */
        .report-timeline {
            position: relative;
            padding-left: 2rem;
        }
        
        .report-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--hs-green), #4a7c4e);
            animation: growDown 1s ease;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .timeline-dot {
            position: absolute;
            left: -2.5rem;
            top: 0.5rem;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--hs-green);
            border: 4px solid var(--hs-cream);
            box-shadow: 0 0 0 3px var(--hs-green);
            animation: dotPulse 2s infinite;
        }
        
        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            animation: scaleIn 0.7s ease;
            margin-bottom: 2rem;
        }
        
        /* Impact Metrics */
        .impact-metric {
            background: linear-gradient(135deg, var(--hs-green), #4a7c4e);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            animation: zoomIn 0.6s ease;
        }
        
        .impact-metric:hover {
            transform: scale(1.05) rotate(2deg);
            box-shadow: 0 10px 30px rgba(47,82,51,0.3);
        }
        
        .impact-number {
            font-size: 3rem;
            font-weight: 800;
            margin: 1rem 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
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
        
        /* Form Styles */
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
        
        /* Alert Animation */
        .alert {
            border-radius: 12px;
            border: none;
            animation: slideInDown 0.5s ease;
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
        
        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.5);
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
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        @keyframes dotPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 3px var(--hs-green);
            }
            50% {
                transform: scale(1.2);
                box-shadow: 0 0 0 8px rgba(47,82,51,0.3);
            }
        }
        
        @keyframes growDown {
            from {
                height: 0;
            }
            to {
                height: 100%;
            }
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
            <li><a href="agent_beneficiaries.php"><i class="bi bi-people me-2"></i>Data Penerima</a></li>
            <li><a href="agent_reports.php" class="active"><i class="bi bi-file-text me-2"></i>Laporan</a></li>
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
                    <i class="bi bi-file-text-fill me-2" style="color: var(--hs-green);"></i>
                    Laporan Distribusi
                </h2>
                <p class="text-muted mb-0">Dokumentasi dan analisis kegiatan distribusi makanan</p>
            </div>
            <button class="btn btn-action" style="background: var(--hs-red); color: white;" data-bs-toggle="modal" data-bs-target="#createReportModal">
                <i class="bi bi-file-earmark-plus me-2"></i>Buat Laporan Baru
            </button>
        </div>

        <!-- Alert Success -->
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
                            <div class="text-muted small mb-1">Total Laporan</div>
                            <div class="stat-number"><?php echo $stats['total_reports']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(47,82,51,0.1);">
                            <i class="bi bi-file-text-fill" style="font-size: 1.8rem; color: var(--hs-green);"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #007bff;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Bulan Ini</div>
                            <div class="stat-number" style="color: #007bff;"><?php echo $stats['this_month']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(0,123,255,0.1);">
                            <i class="bi bi-calendar-month" style="font-size: 1.8rem; color: #007bff;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #28a745;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Program Selesai</div>
                            <div class="stat-number" style="color: #28a745;"><?php echo $stats['completed_programs']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(40,167,69,0.1);">
                            <i class="bi bi-check-circle-fill" style="font-size: 1.8rem; color: #28a745;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #ffc107;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Hari Distribusi</div>
                            <div class="stat-number" style="color: #ffc107;"><?php echo $stats['distribution_days']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(255,193,7,0.1);">
                            <i class="bi bi-calendar-event" style="font-size: 1.8rem; color: #ffc107;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Impact Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="impact-metric">
                    <i class="bi bi-heart-fill" style="font-size: 3rem;"></i>
                    <div class="impact-number"><?php echo number_format($stats['total_portions']); ?></div>
                    <div class="fw-bold">Total Porsi Makanan Tersalurkan</div>
                    <small class="opacity-75">Sejak Anda bergabung sebagai agen</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="impact-metric" style="background: linear-gradient(135deg, var(--hs-red), #a51e26);">
                    <i class="bi bi-people-fill" style="font-size: 3rem;"></i>
                    <div class="impact-number"><?php echo number_format($stats['total_portions'] * 0.8); ?></div>
                    <div class="fw-bold">Estimasi Penerima Manfaat</div>
                    <small class="opacity-75">Individu yang terbantu melalui program Anda</small>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="bi bi-calendar-range me-1"></i>Bulan
                        </label>
                        <input type="month" class="form-control" name="month" value="<?php echo $filter_month; ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="bi bi-funnel me-1"></i>Jenis Laporan
                        </label>
                        <select class="form-select" name="type">
                            <option value="">Semua Jenis</option>
                            <option value="daily" <?php echo $filter_type == 'daily' ? 'selected' : ''; ?>>Harian</option>
                            <option value="weekly" <?php echo $filter_type == 'weekly' ? 'selected' : ''; ?>>Mingguan</option>
                            <option value="monthly" <?php echo $filter_type == 'monthly' ? 'selected' : ''; ?>>Bulanan</option>
                            <option value="program_completion" <?php echo $filter_type == 'program_completion' ? 'selected' : ''; ?>>Penyelesaian Program</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-action w-100" style="background: var(--hs-green); color: white;">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reports Timeline -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px; animation: slideInUp 0.7s ease;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Laporan
                </h5>
            </div>
            <div class="card-body">
                <?php if(mysqli_num_rows($reports) > 0): ?>
                    <div class="report-timeline">
                        <?php $index = 0; while($r = mysqli_fetch_assoc($reports)): 
                            $type_colors = [
                                'daily' => 'primary',
                                'weekly' => 'info',
                                'monthly' => 'success',
                                'program_completion' => 'warning'
                            ];
                            $type_text = [
                                'daily' => 'Laporan Harian',
                                'weekly' => 'Laporan Mingguan',
                                'monthly' => 'Laporan Bulanan',
                                'program_completion' => 'Laporan Penyelesaian'
                            ];
                            $index++;
                        ?>
                        <div class="timeline-item" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                            <div class="timeline-dot"></div>
                            <div class="report-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="report-type-badge bg-<?php echo $type_colors[$r['report_type']]; ?> text-white">
                                            <i class="bi bi-file-text me-1"></i>
                                            <?php echo $type_text[$r['report_type']]; ?>
                                        </span>
                                        <h6 class="fw-bold mt-2 mb-1"><?php echo htmlspecialchars($r['program_name']); ?></h6>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            Dibuat: <?php echo date('d M Y, H:i', strtotime($r['report_date'])); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-success mb-2">
                                            <i class="bi bi-box-seam me-1"></i>
                                            <?php echo number_format($r['quantity_distributed']); ?> porsi
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><strong>Lokasi Distribusi:</strong></small>
                                    <div><?php echo htmlspecialchars($r['distribution_location']); ?></div>
                                </div>
                                
                                <?php if($r['notes']): ?>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><strong>Catatan Agen:</strong></small>
                                    <div class="p-3" style="background: #f8f9fa; border-radius: 10px; border-left: 3px solid var(--hs-green);">
                                        <?php echo nl2br(htmlspecialchars($r['notes'])); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-action" style="background: var(--hs-green); color: white;" onclick="viewReport(<?php echo $r['id']; ?>)">
                                        <i class="bi bi-eye me-1"></i>Lihat Detail
                                    </button>
                                    <button class="btn btn-sm btn-action" style="background: #007bff; color: white;" onclick="downloadReport(<?php echo $r['id']; ?>)">
                                        <i class="bi bi-download me-1"></i>Unduh PDF
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="shareReport(<?php echo $r['id']; ?>)">
                                        <i class="bi bi-share me-1"></i>Bagikan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-x" style="font-size: 4rem; color: #ddd;"></i>
                        <p class="text-muted mt-3 fs-5">Belum ada laporan distribusi</p>
                        <button class="btn btn-action mt-2" style="background: var(--hs-red); color: white;" data-bs-toggle="modal" data-bs-target="#createReportModal">
                            <i class="bi bi-file-earmark-plus me-2"></i>Buat Laporan Pertama
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal: Create Report -->
    <div class="modal fade" id="createReportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--hs-green), #4a7c4e); color: white; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-file-earmark-plus me-2"></i>Buat Laporan Distribusi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="generate_report">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bi bi-calendar-check me-1"></i>Pilih Program *
                                </label>
                                <select class="form-select" name="program_id" required>
                                    <option value="">Pilih Program...</option>
                                    <?php mysqli_data_seek($programs, 0); while($p = mysqli_fetch_assoc($programs)): ?>
                                    <option value="<?php echo $p['id']; ?>">
                                        <?php echo htmlspecialchars($p['program_name']); ?> 
                                        - <?php echo date('d M Y', strtotime($p['scheduled_date'])); ?>
                                        (<?php echo number_format($p['quantity_distributed']); ?> porsi)
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-file-text me-1"></i>Jenis Laporan *
                                </label>
                                <select class="form-select" name="report_type" required>
                                    <option value="">Pilih Jenis...</option>
                                    <option value="daily">Laporan Harian</option>
                                    <option value="weekly">Laporan Mingguan</option>
                                    <option value="monthly">Laporan Bulanan</option>
                                    <option value="program_completion">Laporan Penyelesaian Program</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bi bi-chat-left-text me-1"></i>Catatan & Observasi
                                </label>
                                <textarea class="form-control" name="notes" rows="5" placeholder="Tulis catatan penting, observasi lapangan, kendala yang dihadapi, atau feedback dari penerima manfaat..."></textarea>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Dokumentasikan kondisi lapangan, respon penerima, dan hal-hal penting lainnya
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-action" style="background: var(--hs-green); color: white;">
                            <i class="bi bi-save me-2"></i>Simpan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // View Report Detail
        function viewReport(id) {
            alert('Melihat detail laporan ID: ' + id + '\n\nFitur ini akan menampilkan laporan lengkap dengan grafik dan statistik.');
        }

        // Download Report as PDF
        function downloadReport(id) {
            alert('Mengunduh laporan ID: ' + id + ' dalam format PDF\n\nLaporan akan segera diunduh...');
            // Implementasi download PDF
            // window.location.href = 'download_report.php?id=' + id;
        }

        // Share Report
        function shareReport(id) {
            const shareUrl = window.location.origin + '/view_report.php?id=' + id;
            if (navigator.share) {
                navigator.share({
                    title: 'Laporan Distribusi Hungerswitch',
                    text: 'Lihat laporan distribusi makanan kami',
                    url: shareUrl
                }).catch(() => {
                    copyToClipboard(shareUrl);
                });
            } else {
                copyToClipboard(shareUrl);
            }
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link laporan berhasil disalin!\n\n' + text);
            });
        }

        // Auto dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Animate numbers on load
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number, .impact-number');
            statNumbers.forEach(el => {
                const target = parseInt(el.textContent.replace(/,/g, ''));
                if(isNaN(target)) return;
                
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if(current >= target) {
                        el.textContent = target.toLocaleString('id-ID');
                        clearInterval(timer);
                    } else {
                        el.textContent = Math.floor(current).toLocaleString('id-ID');
                    }
                }, 20);
            });
        });
    </script>
</body>
</html>