<?php
// File: agent_programs.php - FINAL VERSION WITH ANIMATIONS
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || !in_array('agent', $_SESSION['user_roles'] ?? [])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$q_agent = mysqli_query($conn, "SELECT * FROM agents WHERE user_id = $user_id");
$agent = mysqli_fetch_assoc($q_agent);
$agent_id = $agent['id'];

$success = $error = '';

// PROSES MULAI PROGRAM
if(isset($_GET['action']) && $_GET['action'] == 'start' && isset($_GET['id'])) {
    $program_id = intval($_GET['id']);
    mysqli_query($conn, "UPDATE food_programs SET status = 'in_progress' WHERE id = $program_id AND assigned_agent_id = $agent_id");
    $success = "Program berhasil dimulai!";
}

// Filter
$filter_status = $_GET['filter'] ?? 'all';
$search_query = $_GET['search'] ?? '';

// Query Programs
$where = "assigned_agent_id = $agent_id";
if($filter_status != 'all') {
    $where .= " AND status = '$filter_status'";
}
if($search_query) {
    $where .= " AND (program_name LIKE '%$search_query%' OR distribution_location LIKE '%$search_query%')";
}

$programs = mysqli_query($conn, "
    SELECT * FROM food_programs 
    WHERE $where
    ORDER BY 
        CASE 
            WHEN status = 'planned' THEN 1
            WHEN status = 'in_progress' THEN 2
            ELSE 3
        END,
        scheduled_date DESC
");

// Stats
$stats = [];
$stats['total'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM food_programs WHERE assigned_agent_id = $agent_id"));
$stats['in_progress'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM food_programs WHERE assigned_agent_id = $agent_id AND status = 'in_progress'"));
$stats['completed'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM food_programs WHERE assigned_agent_id = $agent_id AND status = 'completed' AND MONTH(completed_at) = MONTH(NOW())"));
$stats['portions'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity_distributed) as total FROM food_programs WHERE assigned_agent_id = $agent_id"))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Program Saya - Hungerswitch</title>
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
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--hs-cream);
            color: #333;
        }
        
        /* Sidebar */
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
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--hs-red);
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
        }
        
        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 4px solid var(--hs-green);
            transition: all 0.3s;
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        .stat-card:nth-child(1) { animation-delay: 0s; }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.2s; }
        .stat-card:nth-child(4) { animation-delay: 0.3s; }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--hs-green);
        }
        
        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            animation: fadeIn 0.6s ease-out;
        }
        
        .filter-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            transition: all 0.3s;
            background: #f0f0f0;
            color: #666;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--hs-green);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(47,82,51,0.3);
        }
        
        /* Program Cards */
        .program-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
            position: relative;
            overflow: hidden;
        }
        
        .program-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--hs-green), var(--hs-red));
        }
        
        .program-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .progress-bar-custom {
            height: 8px;
            border-radius: 10px;
            background: #e0e0e0;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 1s ease-out;
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            .main-content {
                margin-left: 0;
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
            <li><a href="agent_dashboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
            <li><a href="agent_programs.php" class="active"><i class="bi bi-calendar-check"></i>Program Saya</a></li>
            <li><a href="agent_beneficiaries.php"><i class="bi bi-people"></i>Data Penerima</a></li>
            <li><a href="agent_reports.php"><i class="bi bi-file-text"></i>Laporan</a></li>
            <li><hr class="border-light opacity-25 my-3"></li>
            <li><a href="profile.php"><i class="bi bi-person"></i>Profil</a></li>
            <li><a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4" style="animation: fadeIn 0.6s ease-out;">
            <div>
                <h2 class="fw-bold mb-1" style="color: var(--hs-green);">Program Saya</h2>
                <p class="text-muted mb-0">Kelola dan pantau program distribusi makanan Anda</p>
            </div>
            <a href="agent_beneficiaries.php?action=add" class="btn text-white" style="background: var(--hs-red); border-radius: 12px; padding: 0.75rem 1.5rem; font-weight: 600;">
                <i class="bi bi-person-plus me-2"></i>Tambah Penerima Baru
            </a>
        </div>

        <?php if($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px;">
            <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Total Program</div>
                            <div class="stat-number"><?php echo $stats['total']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(47,82,51,0.1); color: var(--hs-green);">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #0d6efd;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Sedang Berjalan</div>
                            <div class="stat-number" style="color: #0d6efd;"><?php echo $stats['in_progress']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(13,110,253,0.1); color: #0d6efd;">
                            <i class="bi bi-play-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #28a745;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Selesai Bulan Ini</div>
                            <div class="stat-number" style="color: #28a745;"><?php echo $stats['completed']; ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(40,167,69,0.1); color: #28a745;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: var(--hs-red);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Total Porsi</div>
                            <div class="stat-number" style="color: var(--hs-red);"><?php echo number_format($stats['portions']); ?></div>
                        </div>
                        <div class="stat-icon" style="background: rgba(217,35,45,0.1); color: var(--hs-red);">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="filter-section">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <button type="submit" name="filter" value="all" class="filter-btn <?php echo $filter_status == 'all' ? 'active' : ''; ?>">
                        Semua Program
                    </button>
                </div>
                <div class="col-auto">
                    <button type="submit" name="filter" value="planned" class="filter-btn <?php echo $filter_status == 'planned' ? 'active' : ''; ?>">
                        Direncanakan
                    </button>
                </div>
                <div class="col-auto">
                    <button type="submit" name="filter" value="in_progress" class="filter-btn <?php echo $filter_status == 'in_progress' ? 'active' : ''; ?>">
                        Berjalan
                    </button>
                </div>
                <div class="col-auto">
                    <button type="submit" name="filter" value="completed" class="filter-btn <?php echo $filter_status == 'completed' ? 'active' : ''; ?>">
                        Selesai
                    </button>
                </div>
                <div class="col-auto ms-auto">
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius: 12px 0 0 12px; background: white;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Cari program atau lokasi..." 
                               value="<?php echo htmlspecialchars($search_query); ?>"
                               style="border-radius: 0 12px 12px 0; border-left: none;">
                    </div>
                </div>
            </form>
        </div>

        <!-- Programs Grid -->
        <div class="row g-4">
            <?php 
            $idx = 0;
            while($p = mysqli_fetch_assoc($programs)): 
                $status_colors = [
                    'planned' => 'warning',
                    'in_progress' => 'primary',
                    'completed' => 'success',
                    'cancelled' => 'danger'
                ];
                $status_text = [
                    'planned' => 'Direncanakan',
                    'in_progress' => 'Sedang Berjalan',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan'
                ];
                $progress = $p['quantity_planned'] > 0 ? ($p['quantity_distributed'] / $p['quantity_planned']) * 100 : 0;
            ?>
            <div class="col-md-6">
                <div class="program-card" style="animation-delay: <?php echo $idx * 0.1; ?>s;">
                    <!-- Progress Bar -->
                    <div class="progress-bar-custom">
                        <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge-status bg-<?php echo $status_colors[$p['status']]; ?> text-white">
                                    <i class="bi bi-<?php echo $p['status'] == 'completed' ? 'check-circle' : ($p['status'] == 'in_progress' ? 'play-circle' : 'calendar'); ?>"></i>
                                    <?php echo $status_text[$p['status']]; ?>
                                </span>
                                <small class="text-muted" style="font-family: monospace;"><?php echo $p['program_number']; ?></small>
                            </div>
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($p['program_name']); ?></h5>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                            <i class="bi bi-calendar" style="color: var(--hs-green);"></i>
                            <span><?php echo date('d M Y', strtotime($p['scheduled_date'])); ?></span>
                            <?php if($p['distribution_time']): ?>
                                <i class="bi bi-clock ms-2" style="color: var(--hs-green);"></i>
                                <span><?php echo date('H:i', strtotime($p['distribution_time'])); ?> WIB</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-start gap-2 text-muted">
                            <i class="bi bi-geo-alt" style="color: var(--hs-red);"></i>
                            <span><?php echo htmlspecialchars($p['distribution_location'] ?? 'Belum ditentukan'); ?></span>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="p-3" style="background: #f8f9fa; border-radius: 12px;">
                                <small class="text-muted">Target Porsi</small>
                                <h4 class="mb-0 fw-bold"><?php echo number_format($p['quantity_planned']); ?></h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3" style="background: #d4edda; border-radius: 12px;">
                                <small class="text-success">Tersalurkan</small>
                                <h4 class="mb-0 fw-bold text-success"><?php echo number_format($p['quantity_distributed']); ?></h4>
                            </div>
                        </div>
                    </div>

                    <!-- Menu -->
                    <?php if($p['menu_description']): ?>
                    <div class="mb-3 p-3" style="background: #fff3cd; border-radius: 12px; border-left: 4px solid #ffc107;">
                        <small class="text-warning fw-bold">Menu:</small>
                        <div class="text-dark"><?php echo htmlspecialchars($p['menu_description']); ?></div>
                    </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <?php if($p['status'] == 'planned'): ?>
                            <a href="?action=start&id=<?php echo $p['id']; ?>" class="btn btn-success flex-fill fw-bold" style="border-radius: 12px;">
                                <i class="bi bi-play-circle me-2"></i>Mulai Program
                            </a>
                        <?php elseif($p['status'] == 'in_progress'): ?>
                            <a href="agent_program_detail.php?id=<?php echo $p['id']; ?>" class="btn flex-fill fw-bold text-white" style="background: var(--hs-green); border-radius: 12px;">
                                <i class="bi bi-arrow-right-circle me-2"></i>Lanjutkan Distribusi
                            </a>
                        <?php else: ?>
                            <a href="agent_reports.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-secondary flex-fill fw-bold" style="border-radius: 12px;">
                                <i class="bi bi-file-text me-2"></i>Lihat Laporan
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php 
            $idx++;
            endwhile; 
            ?>

            <?php if(mysqli_num_rows($programs) == 0): ?>
            <div class="col-12">
                <div class="text-center py-5" style="animation: fadeIn 0.6s ease-out;">
                    <i class="bi bi-calendar-x" style="font-size: 4rem; color: #ddd;"></i>
                    <p class="text-muted mt-3 fs-5">Tidak ada program yang sesuai dengan pencarian Anda</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>