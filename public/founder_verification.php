<?php
// File: founder_verification.php - VERIFIKASI FOUNDER
session_start();
include 'config.php';

// Cek Login Founder
if(!isset($_SESSION['user_id']) || !in_array('founder', $_SESSION['user_roles'] ?? [])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Verification Actions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        $action = $_POST['action'];
        $type = $_POST['type']; // partner, agent, beneficiary
        $id = intval($_POST['id']);
        $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
        
        if($type == 'partner') {
            if($action == 'approve') {
                mysqli_query($conn, "UPDATE partners SET verification_status = 'verified' WHERE id = $id");
            } elseif($action == 'reject') {
                mysqli_query($conn, "UPDATE partners SET verification_status = 'rejected' WHERE id = $id");
            }
        } elseif($type == 'agent') {
            if($action == 'approve') {
                mysqli_query($conn, "UPDATE agents SET verification_status = 'verified', verified_at = NOW(), verified_by = $user_id WHERE id = $id");
            } elseif($action == 'reject') {
                mysqli_query($conn, "UPDATE agents SET verification_status = 'rejected' WHERE id = $id");
            }
        } elseif($type == 'beneficiary') {
            if($action == 'approve') {
                mysqli_query($conn, "UPDATE beneficiaries SET verification_status = 'verified', verification_notes = '$notes' WHERE id = $id");
            } elseif($action == 'reject') {
                mysqli_query($conn, "UPDATE beneficiaries SET verification_status = 'rejected', verification_notes = '$notes' WHERE id = $id");
            }
        }
        
        header("Location: founder_verification.php");
        exit();
    }
}

// Get Pending Verifications
$pending_partners = mysqli_query($conn, "
    SELECT p.*, u.name, u.email, u.phone, u.created_at
    FROM partners p
    JOIN users u ON p.user_id = u.id
    WHERE p.verification_status = 'pending'
    ORDER BY p.created_at DESC
");

$pending_agents = mysqli_query($conn, "
    SELECT a.*, u.name, u.email, u.phone, u.created_at
    FROM agents a
    JOIN users u ON a.user_id = u.id
    WHERE a.verification_status = 'pending'
    ORDER BY a.created_at DESC
");

$pending_beneficiaries = mysqli_query($conn, "
    SELECT b.*, a.region, u.name as agent_name
    FROM beneficiaries b
    JOIN agents a ON b.registered_by_agent_id = a.id
    JOIN users u ON a.user_id = u.id
    WHERE b.verification_status = 'pending'
    ORDER BY b.created_at DESC
");

// Stats
$stats = [
    'pending_partners' => mysqli_num_rows($pending_partners),
    'pending_agents' => mysqli_num_rows($pending_agents),
    'pending_beneficiaries' => mysqli_num_rows($pending_beneficiaries),
    'verified_partners' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM partners WHERE verification_status = 'verified'"))['total'],
    'verified_agents' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM agents WHERE verification_status = 'verified'"))['total'],
    'verified_beneficiaries' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM beneficiaries WHERE verification_status = 'verified'"))['total']
];

// Reset pointer untuk looping ulang
mysqli_data_seek($pending_partners, 0);
mysqli_data_seek($pending_agents, 0);
mysqli_data_seek($pending_beneficiaries, 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Verifikasi - Hungerswitch</title>
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
        
        .verification-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
            border-left: 5px solid;
        }
        
        .verification-card.partner { border-left-color: var(--hs-green); }
        .verification-card.agent { border-left-color: #FF9800; }
        .verification-card.beneficiary { border-left-color: #2196F3; }
        
        .verification-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--hs-green), var(--hs-red));
        }
        
        .stat-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 25px rgba(0,0,0,0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0.5rem 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .pending-badge {
            background: linear-gradient(135deg, #FFA726, #FB8C00);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }
        
        .action-btn {
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
            border: none;
        }
        
        .action-btn.approve {
            background: linear-gradient(135deg, var(--hs-green), #3d6b42);
            color: white;
        }
        
        .action-btn.approve:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(47, 82, 51, 0.3);
        }
        
        .action-btn.reject {
            background: linear-gradient(135deg, var(--hs-red), #b91d26);
            color: white;
        }
        
        .action-btn.reject:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.3);
        }
        
        .nav-tabs {
            border: none;
            gap: 1rem;
        }
        
        .nav-tabs .nav-link {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #666;
            background: white;
            transition: 0.3s;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--hs-green);
            background: #f5f5f5;
        }
        
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--hs-green), #3d6b42);
            color: white;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
        
        .info-value {
            color: #333;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-in {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        .verification-modal .modal-content {
            border-radius: 20px;
            border: none;
        }
        
        .verification-modal .modal-header {
            background: linear-gradient(135deg, var(--hs-green), var(--hs-red));
            color: white;
            border-radius: 20px 20px 0 0;
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
            <li><a href="founder_dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li><a href="founder_programs.php"><i class="bi bi-calendar-check me-2"></i>Program Distribusi</a></li>
            <li><a href="founder_finance.php"><i class="bi bi-wallet2 me-2"></i>Keuangan</a></li>
            <li><a href="founder_verification.php" class="active"><i class="bi bi-shield-check me-2"></i>Verifikasi</a></li>
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
                <h2 class="fw-bold mb-1">Verifikasi & Persetujuan</h2>
                <p class="text-muted mb-0">Verifikasi partner, agent, dan penerima manfaat</p>
            </div>
            <div>
                <span class="badge bg-warning px-3 py-2 me-2">
                    <i class="bi bi-clock-history me-1"></i>
                    <?php echo $stats['pending_partners'] + $stats['pending_agents'] + $stats['pending_beneficiaries']; ?> Menunggu
                </span>
                <span class="badge bg-success px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i>
                    <?php echo $stats['verified_partners'] + $stats['verified_agents'] + $stats['verified_beneficiaries']; ?> Terverifikasi
                </span>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row g-4 mb-4 animate-in">
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="bi bi-shop-window" style="font-size: 2.5rem; color: var(--hs-green);"></i>
                    <div class="stat-number" style="color: var(--hs-green);">
                        <?php echo $stats['pending_partners']; ?>
                    </div>
                    <div class="stat-label">Partner Pending</div>
                    <div class="mt-2">
                        <small class="text-muted"><?php echo $stats['verified_partners']; ?> Terverifikasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="bi bi-person-badge-fill" style="font-size: 2.5rem; color: #FF9800;"></i>
                    <div class="stat-number" style="color: #FF9800;">
                        <?php echo $stats['pending_agents']; ?>
                    </div>
                    <div class="stat-label">Agent Pending</div>
                    <div class="mt-2">
                        <small class="text-muted"><?php echo $stats['verified_agents']; ?> Terverifikasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="bi bi-people-fill" style="font-size: 2.5rem; color: #2196F3;"></i>
                    <div class="stat-number" style="color: #2196F3;">
                        <?php echo $stats['pending_beneficiaries']; ?>
                    </div>
                    <div class="stat-label">Penerima Manfaat Pending</div>
                    <div class="mt-2">
                        <small class="text-muted"><?php echo $stats['verified_beneficiaries']; ?> Terverifikasi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" id="verificationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="partners-tab" data-bs-toggle="tab" data-bs-target="#partners" type="button">
                    <i class="bi bi-shop me-2"></i>Partner
                    <?php if($stats['pending_partners'] > 0): ?>
                        <span class="badge bg-danger ms-2"><?php echo $stats['pending_partners']; ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="agents-tab" data-bs-toggle="tab" data-bs-target="#agents" type="button">
                    <i class="bi bi-person-badge me-2"></i>Agent
                    <?php if($stats['pending_agents'] > 0): ?>
                        <span class="badge bg-danger ms-2"><?php echo $stats['pending_agents']; ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="beneficiaries-tab" data-bs-toggle="tab" data-bs-target="#beneficiaries" type="button">
                    <i class="bi bi-people me-2"></i>Penerima Manfaat
                    <?php if($stats['pending_beneficiaries'] > 0): ?>
                        <span class="badge bg-danger ms-2"><?php echo $stats['pending_beneficiaries']; ?></span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="verificationTabsContent">
            
            <!-- PARTNERS TAB -->
            <div class="tab-pane fade show active" id="partners" role="tabpanel">
                <?php if(mysqli_num_rows($pending_partners) > 0): ?>
                    <div class="row g-4">
                        <?php while($partner = mysqli_fetch_assoc($pending_partners)): ?>
                        <div class="col-md-6 animate-in">
                            <div class="verification-card partner">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($partner['business_name']); ?></h5>
                                        <p class="text-muted mb-0"><?php echo ucfirst($partner['business_type']); ?></p>
                                    </div>
                                    <span class="pending-badge">
                                        <i class="bi bi-clock-history me-1"></i>Pending
                                    </span>
                                </div>
                                
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-person me-2"></i>Nama Pemilik</span>
                                    <span class="info-value"><?php echo htmlspecialchars($partner['name']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-envelope me-2"></i>Email</span>
                                    <span class="info-value"><?php echo htmlspecialchars($partner['email']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-phone me-2"></i>Telepon</span>
                                    <span class="info-value"><?php echo htmlspecialchars($partner['phone'] ?? '-'); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-geo-alt me-2"></i>Alamat</span>
                                    <span class="info-value"><?php echo htmlspecialchars($partner['business_address']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-calendar me-2"></i>Tanggal Daftar</span>
                                    <span class="info-value"><?php echo date('d M Y', strtotime($partner['created_at'])); ?></span>
                                </div>
                                
                                <div class="mt-4 pt-3 border-top d-flex gap-2">
                                    <button class="action-btn approve flex-fill" onclick="verifyItem('partner', <?php echo $partner['id']; ?>, 'approve')">
                                        <i class="bi bi-check-circle me-2"></i>Setujui
                                    </button>
                                    <button class="action-btn reject flex-fill" onclick="verifyItem('partner', <?php echo $partner['id']; ?>, 'reject')">
                                        <i class="bi bi-x-circle me-2"></i>Tolak
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <h5 class="text-muted">Tidak Ada Partner Pending</h5>
                        <p class="text-muted">Semua partner telah terverifikasi</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- AGENTS TAB -->
            <div class="tab-pane fade" id="agents" role="tabpanel">
                <?php if(mysqli_num_rows($pending_agents) > 0): ?>
                    <div class="row g-4">
                        <?php while($agent = mysqli_fetch_assoc($pending_agents)): ?>
                        <div class="col-md-6 animate-in">
                            <div class="verification-card agent">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($agent['name']); ?></h5>
                                        <p class="text-muted mb-0">Agent - <?php echo htmlspecialchars($agent['region']); ?></p>
                                    </div>
                                    <span class="pending-badge">
                                        <i class="bi bi-clock-history me-1"></i>Pending
                                    </span>
                                </div>
                                
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-envelope me-2"></i>Email</span>
                                    <span class="info-value"><?php echo htmlspecialchars($agent['email']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-phone me-2"></i>Telepon</span>
                                    <span class="info-value"><?php echo htmlspecialchars($agent['phone'] ?? '-'); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-geo-alt me-2"></i>Wilayah</span>
                                    <span class="info-value"><?php echo htmlspecialchars($agent['region']); ?></span>
                                </div>
                                <?php if($agent['coverage_area']): ?>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-map me-2"></i>Area Cakupan</span>
                                    <span class="info-value"><?php echo htmlspecialchars($agent['coverage_area']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-calendar me-2"></i>Tanggal Daftar</span>
                                    <span class="info-value"><?php echo date('d M Y', strtotime($agent['created_at'])); ?></span>
                                </div>
                                
                                <div class="mt-4 pt-3 border-top d-flex gap-2">
                                    <button class="action-btn approve flex-fill" onclick="verifyItem('agent', <?php echo $agent['id']; ?>, 'approve')">
                                        <i class="bi bi-check-circle me-2"></i>Setujui
                                    </button>
                                    <button class="action-btn reject flex-fill" onclick="verifyItem('agent', <?php echo $agent['id']; ?>, 'reject')">
                                        <i class="bi bi-x-circle me-2"></i>Tolak
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <h5 class="text-muted">Tidak Ada Agent Pending</h5>
                        <p class="text-muted">Semua agent telah terverifikasi</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- BENEFICIARIES TAB -->
            <div class="tab-pane fade" id="beneficiaries" role="tabpanel">
                <?php if(mysqli_num_rows($pending_beneficiaries) > 0): ?>
                    <div class="row g-4">
                        <?php while($beneficiary = mysqli_fetch_assoc($pending_beneficiaries)): ?>
                        <div class="col-md-6 animate-in">
                            <div class="verification-card beneficiary">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($beneficiary['full_name']); ?></h5>
                                        <p class="text-muted mb-0">Penerima Manfaat - <?php echo ucfirst(str_replace('_', ' ', $beneficiary['economic_status'])); ?></p>
                                    </div>
                                    <span class="pending-badge">
                                        <i class="bi bi-clock-history me-1"></i>Pending
                                    </span>
                                </div>
                                
                                <?php if($beneficiary['id_number']): ?>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-card-text me-2"></i>NIK/KTP</span>
                                    <span class="info-value"><?php echo htmlspecialchars($beneficiary['id_number']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($beneficiary['phone']): ?>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-phone me-2"></i>Telepon</span>
                                    <span class="info-value"><?php echo htmlspecialchars($beneficiary['phone']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-geo-alt me-2"></i>Alamat</span>
                                    <span class="info-value"><?php echo htmlspecialchars($beneficiary['address']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-people me-2"></i>Anggota Keluarga</span>
                                    <span class="info-value"><?php echo $beneficiary['family_members']; ?> orang</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-person-check me-2"></i>Didaftarkan oleh</span>
                                    <span class="info-value"><?php echo htmlspecialchars($beneficiary['agent_name']); ?> (<?php echo htmlspecialchars($beneficiary['region']); ?>)</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="bi bi-calendar me-2"></i>Tanggal Daftar</span>
                                    <span class="info-value"><?php echo date('d M Y', strtotime($beneficiary['created_at'])); ?></span>
                                </div>
                                
                                <div class="mt-4 pt-3 border-top d-flex gap-2">
                                    <button class="action-btn approve flex-fill" onclick="verifyItem('beneficiary', <?php echo $beneficiary['id']; ?>, 'approve')">
                                        <i class="bi bi-check-circle me-2"></i>Setujui
                                    </button>
                                    <button class="action-btn reject flex-fill" onclick="verifyItem('beneficiary', <?php echo $beneficiary['id']; ?>, 'reject')">
                                        <i class="bi bi-x-circle me-2"></i>Tolak
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <h5 class="text-muted">Tidak Ada Penerima Manfaat Pending</h5>
                        <p class="text-muted">Semua penerima manfaat telah terverifikasi</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div class="modal fade verification-modal" id="verificationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verificationModalTitle">Konfirmasi Verifikasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="verificationForm">
                    <div class="modal-body">
                        <input type="hidden" name="type" id="verificationType">
                        <input type="hidden" name="id" id="verificationId">
                        <input type="hidden" name="action" id="verificationAction">
                        
                        <div class="text-center mb-4" id="verificationIcon"></div>
                        <p class="text-center" id="verificationMessage"></p>
                        
                        <div class="mb-3" id="notesSection" style="display: none;">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn" id="verificationSubmitBtn">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verifyItem(type, id, action) {
            const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
            const form = document.getElementById('verificationForm');
            const title = document.getElementById('verificationModalTitle');
            const icon = document.getElementById('verificationIcon');
            const message = document.getElementById('verificationMessage');
            const submitBtn = document.getElementById('verificationSubmitBtn');
            
            document.getElementById('verificationType').value = type;
            document.getElementById('verificationId').value = id;
            document.getElementById('verificationAction').value = action;
            
            const typeName = type === 'partner' ? 'Partner' : (type === 'agent' ? 'Agent' : 'Penerima Manfaat');
            
            if(action === 'approve') {
                title.textContent = 'Setujui Verifikasi ' + typeName;
                icon.innerHTML = '<i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>';
                message.textContent = 'Apakah Anda yakin ingin menyetujui verifikasi ' + typeName.toLowerCase() + ' ini?';
                submitBtn.className = 'btn btn-success';
                submitBtn.textContent = 'Ya, Setujui';
            } else {
                title.textContent = 'Tolak Verifikasi ' + typeName;
                icon.innerHTML = '<i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>';
                message.textContent = 'Apakah Anda yakin ingin menolak verifikasi ' + typeName.toLowerCase() + ' ini?';
                submitBtn.className = 'btn btn-danger';
                submitBtn.textContent = 'Ya, Tolak';
            }
            
            modal.show();
        }
    </script>
</body>
</html>