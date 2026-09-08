<?php
// File: founder_dashboard.php - DASHBOARD FOUNDER
session_start();
include 'config.php';

// Cek Login Founder
if(!isset($_SESSION['user_id']) || !in_array('founder', $_SESSION['user_roles'] ?? [])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil Statistik Umum
$stats = [];

// Total Users by Role
$q_customers = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$stats['customers'] = mysqli_fetch_assoc($q_customers)['total'];

$q_partners = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'partner'");
$stats['partners'] = mysqli_fetch_assoc($q_partners)['total'];

$q_agents = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'agent'");
$stats['agents'] = mysqli_fetch_assoc($q_agents)['total'];

// Dana Donasi
$q_donation = mysqli_query($conn, "SELECT current_balance, total_received, total_distributed FROM donation_fund WHERE id = 1");
$donation_fund = mysqli_fetch_assoc($q_donation);
$stats['donation_balance'] = $donation_fund['current_balance'] ?? 0;
$stats['donation_received'] = $donation_fund['total_received'] ?? 0;
$stats['donation_distributed'] = $donation_fund['total_distributed'] ?? 0;

// Total Orders Marketplace
$q_orders = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(total_amount) as revenue FROM orders WHERE order_source = 'marketplace'");
$orders_data = mysqli_fetch_assoc($q_orders);
$stats['marketplace_orders'] = $orders_data['total'] ?? 0;
$stats['marketplace_revenue'] = $orders_data['revenue'] ?? 0;

// Total Program Distribusi
$q_programs = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(quantity_distributed) as portions FROM food_programs");
$programs_data = mysqli_fetch_assoc($q_programs);
$stats['total_programs'] = $programs_data['total'] ?? 0;
$stats['total_portions'] = $programs_data['portions'] ?? 0;

// Total Penerima Manfaat Aktif
$q_beneficiaries = mysqli_query($conn, "SELECT COUNT(*) as total FROM beneficiaries WHERE is_active = 1");
$stats['active_beneficiaries'] = mysqli_fetch_assoc($q_beneficiaries)['total'];

// Recent Activities
$recent_donations = mysqli_query($conn, "
    SELECT ud.*, u.name as donor_name 
    FROM user_donations ud 
    LEFT JOIN users u ON ud.user_id = u.id 
    ORDER BY ud.created_at DESC 
    LIMIT 5
");

$recent_programs = mysqli_query($conn, "
    SELECT fp.*, a.region 
    FROM food_programs fp 
    JOIN agents a ON fp.assigned_agent_id = a.id 
    ORDER BY fp.created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Founder Dashboard - Hungerswitch</title>
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
            background: linear-gradient(180deg, var(--hs-red), #b91d26);
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
            border-left-color: white;
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
            transition: 0.3s;
            border-left: 4px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-card.green { border-left-color: var(--hs-green); }
        .stat-card.red { border-left-color: var(--hs-red); }
        .stat-card.blue { border-left-color: #2196F3; }
        .stat-card.orange { border-left-color: #FF9800; }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--hs-red);
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .activity-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        
        .activity-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-stars me-2"></i>Hungerswitch
        </div>
        
        <ul class="sidebar-nav">
            <li><a href="founder_dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li><a href="founder_programs.php"><i class="bi bi-calendar-check me-2"></i>Program Distribusi</a></li>
            <li><a href="founder_finance.php"><i class="bi bi-wallet2 me-2"></i>Keuangan</a></li>
            <li><a href="founder_verification.php"><i class="bi bi-shield-check me-2"></i>Verifikasi</a></li>
            <li><a href="founder_users.php"><i class="bi bi-people me-2"></i>Manajemen User</a></li>
            <li><hr class="border-light opacity-25 my-3"></li>
            <li><a href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
            <li><a href="logout.php" class="text-warning"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Dashboard Founder</h2>
                <p class="text-muted mb-0">Monitoring seluruh operasional platform</p>
            </div>
            <div>
                <span class="badge bg-success px-3 py-2">
                    <i class="bi bi-clock me-1"></i><?php echo date('d M Y, H:i'); ?>
                </span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card blue">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-number" style="color: #2196F3;">
                                <?php echo $stats['customers'] + $stats['partners'] + $stats['agents']; ?>
                            </div>
                            <small class="text-muted">
                                <?php echo $stats['customers']; ?> Customer • 
                                <?php echo $stats['partners']; ?> Partner • 
                                <?php echo $stats['agents']; ?> Agent
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; background: rgba(33,150,243,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-people" style="font-size: 1.5rem; color: #2196F3;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card green">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label">Dana Donasi</div>
                            <div class="stat-number" style="color: var(--hs-green);">
                                Rp <?php echo number_format($stats['donation_balance'], 0, ',', '.'); ?>
                            </div>
                            <small class="text-muted">
                                Total: Rp <?php echo number_format($stats['donation_received'], 0, ',', '.'); ?>
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; background: rgba(47,82,51,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-cash-stack" style="font-size: 1.5rem; color: var(--hs-green);"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card red">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label">Marketplace</div>
                            <div class="stat-number"><?php echo $stats['marketplace_orders']; ?></div>
                            <small class="text-muted">
                                Revenue: Rp <?php echo number_format($stats['marketplace_revenue'], 0, ',', '.'); ?>
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; background: rgba(217,35,45,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-shop" style="font-size: 1.5rem; color: var(--hs-red);"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card orange">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-label">Porsi Tersalurkan</div>
                            <div class="stat-number" style="color: #FF9800;">
                                <?php echo number_format($stats['total_portions']); ?>
                            </div>
                            <small class="text-muted">
                                <?php echo $stats['total_programs']; ?> Program Aktif
                            </small>
                        </div>
                        <div style="width: 50px; height: 50px; background: rgba(255,152,0,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-box-seam" style="font-size: 1.5rem; color: #FF9800;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">Donasi Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php if(mysqli_num_rows($recent_donations) > 0): ?>
                            <?php while($donation = mysqli_fetch_assoc($recent_donations)): ?>
                            <div class="activity-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <?php echo htmlspecialchars($donation['donor_name'] ?? 'Hamba Allah'); ?>
                                        </h6>
                                        <p class="text-muted small mb-1">
                                            <?php echo $donation['donation_number']; ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?php echo date('d M Y, H:i', strtotime($donation['created_at'])); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success" style="font-size: 1.2rem;">
                                            Rp <?php echo number_format($donation['amount'], 0, ',', '.'); ?>
                                        </div>
                                        <span class="badge-status bg-<?php echo $donation['status'] == 'paid' ? 'success' : 'warning'; ?> text-white">
                                            <?php echo $donation['status'] == 'paid' ? 'Paid' : 'Pending'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
                                <p class="text-muted mt-2">Belum ada donasi</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">Program Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php if(mysqli_num_rows($recent_programs) > 0): ?>
                            <?php while($program = mysqli_fetch_assoc($recent_programs)): ?>
                            <div class="activity-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <?php echo htmlspecialchars($program['program_name']); ?>
                                        </h6>
                                        <p class="text-muted small mb-1">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <?php echo htmlspecialchars($program['region']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            <?php echo date('d M Y', strtotime($program['scheduled_date'])); ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold" style="font-size: 1.2rem; color: var(--hs-green);">
                                            <?php echo $program['quantity_planned']; ?> porsi
                                        </div>
                                        <span class="badge-status bg-<?php 
                                            echo $program['status'] == 'completed' ? 'success' : 
                                                ($program['status'] == 'in_progress' ? 'primary' : 'warning'); 
                                        ?> text-white">
                                            <?php 
                                                $status_labels = [
                                                    'planned' => 'Planned',
                                                    'in_progress' => 'Progress',
                                                    'completed' => 'Done',
                                                    'cancelled' => 'Cancelled'
                                                ];
                                                echo $status_labels[$program['status']];
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ddd;"></i>
                                <p class="text-muted mt-2">Belum ada program</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>