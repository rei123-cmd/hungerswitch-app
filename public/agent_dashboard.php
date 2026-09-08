<?php
// File: agent_dashboard.php - REVISI FINAL
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

// Statistik Agen
$stats = [];
$stats['total_programs'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM food_programs WHERE assigned_agent_id = $agent_id"));
$stats['completed_programs'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM food_programs WHERE assigned_agent_id = $agent_id AND status = 'completed'"));
$stats['total_portions'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity_distributed) as total FROM food_programs WHERE assigned_agent_id = $agent_id"))['total'] ?? 0;
$stats['active_beneficiaries'] = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM beneficiaries WHERE registered_by_agent_id = $agent_id AND is_active = 1"));

// Program yang Ditugaskan
$programs = mysqli_query($conn, "
    SELECT * FROM food_programs 
    WHERE assigned_agent_id = $agent_id 
    ORDER BY 
        CASE 
            WHEN status = 'planned' THEN 1
            WHEN status = 'in_progress' THEN 2
            ELSE 3
        END,
        scheduled_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Agen - Hungerswitch</title>
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
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--hs-cream);
            color: #333;
        }
        
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
        }
        
        .sidebar-nav a {
            display: block;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--hs-red);
        }
        
        .main-content {
            margin-left: 260px;
            padding: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 4px solid var(--hs-green);
            transition: 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--hs-green);
        }
        
        .program-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        
        .program-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
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
            <li><a href="agent_dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li><a href="agent_programs.php"><i class="bi bi-calendar-check me-2"></i>Program Saya</a></li>
            <li><a href="agent_beneficiaries.php"><i class="bi bi-people me-2"></i>Data Penerima</a></li>
            <li><a href="agent_reports.php"><i class="bi bi-file-text me-2"></i>Laporan</a></li>
            <li><hr class="border-light opacity-25 my-3"></li>
            <li><a href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
            <li><a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Dashboard Agen</h2>
                <p class="text-muted mb-0">Selamat datang, <?php echo htmlspecialchars($agent['region']); ?> Region</p>
            </div>
            <a href="agent_beneficiaries.php?action=add" class="btn btn-action" style="background: var(--hs-red); color: white;">
                <i class="bi bi-person-plus me-2"></i>Tambah Penerima Baru
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Total Program</div>
                            <div class="stat-number"><?php echo $stats['total_programs']; ?></div>
                        </div>
                        <div style="width: 50px; height: 50px; background: rgba(47,82,51,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-calendar-check" style="font-size: 1.5rem; color: var(--hs-green);"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #28a745;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Selesai</div>
                            <div class="stat-number" style="color: #28a745;"><?php echo $stats['completed_programs']; ?></div>
                        </div>
                        <div style="width: 50px; height: 50px; background: rgba(40,167,69,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-check-circle" style="font-size: 1.5rem; color: #28a745;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: var(--hs-red);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Porsi Tersalurkan</div>
                            <div class="stat-number" style="color: var(--hs-red);"><?php echo number_format($stats['total_portions']); ?></div>
                        </div>
                        <div style="width: 50px; height: 50px; background: rgba(217,35,45,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-box-seam" style="font-size: 1.5rem; color: var(--hs-red);"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #ffc107;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Penerima Aktif</div>
                            <div class="stat-number" style="color: #ffc107;"><?php echo $stats['active_beneficiaries']; ?></div>
                        </div>
                        <div style="width: 50px; height: 50px; background: rgba(255,193,7,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-people" style="font-size: 1.5rem; color: #ffc107;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Terbaru -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Program yang Ditugaskan</h5>
            </div>
            <div class="card-body">
                <?php if(mysqli_num_rows($programs) > 0): ?>
                    <?php while($p = mysqli_fetch_assoc($programs)): 
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
                    ?>
                    <div class="program-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($p['program_name']); ?></h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?php echo date('d M Y', strtotime($p['scheduled_date'])); ?>
                                    <?php if($p['distribution_time']): ?>
                                        • <i class="bi bi-clock me-1"></i><?php echo date('H:i', strtotime($p['distribution_time'])); ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <span class="badge-status bg-<?php echo $status_colors[$p['status']]; ?> text-white">
                                <?php echo $status_text[$p['status']]; ?>
                            </span>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Target Porsi</small>
                                <strong><?php echo number_format($p['quantity_planned']); ?> porsi</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Tersalurkan</small>
                                <strong class="text-success"><?php echo number_format($p['quantity_distributed']); ?> porsi</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Budget</small>
                                <strong>Rp <?php echo number_format($p['budget_allocated'], 0, ',', '.'); ?></strong>
                            </div>
                        </div>
                        
                        <?php if($p['distribution_location']): ?>
                        <div class="mb-3">
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>Lokasi:</small>
                            <div><?php echo htmlspecialchars($p['distribution_location']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2">
                            <?php if($p['status'] == 'planned' || $p['status'] == 'in_progress'): ?>
                                <a href="agent_programs.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-action" style="background: var(--hs-green); color: white;">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail
                                </a>
                                <?php if($p['status'] == 'in_progress'): ?>
                                <a href="agent_programs.php?action=complete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Selesaikan Program
                                </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="agent_programs.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-text me-1"></i>Lihat Laporan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ddd;"></i>
                        <p class="text-muted mt-3">Belum ada program yang ditugaskan</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>