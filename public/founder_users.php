<?php
// File: founder_users.php - MANAJEMEN USER FOUNDER
session_start();
include 'config.php';

// Cek Login Founder
if(!isset($_SESSION['user_id']) || !in_array('founder', $_SESSION['user_roles'] ?? [])) {
    header("Location: login_unified.php");
    exit();
}

// Handle Actions
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        $user_id = intval($_POST['user_id']);
        
        if($_POST['action'] == 'toggle_status') {
            $new_status = $_POST['new_status'];
            mysqli_query($conn, "UPDATE users SET status = '$new_status' WHERE id = $user_id");
        }
        
        if($_POST['action'] == 'delete_user') {
            mysqli_query($conn, "UPDATE users SET status = 'banned' WHERE id = $user_id");
        }
        
        header("Location: founder_users.php");
        exit();
    }
}

// Pagination & Filters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$per_page = 15;
$offset = ($page - 1) * $per_page;

// Build Query
$where = "1=1";
if($role_filter != 'all') {
    $where .= " AND role = '$role_filter'";
}
if($status_filter != 'all') {
    $where .= " AND status = '$status_filter'";
}
if($search) {
    $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}

// Get Users
$users_query = mysqli_query($conn, "
    SELECT u.*, 
           p.business_name,
           a.region as agent_region
    FROM users u
    LEFT JOIN partners p ON u.id = p.user_id
    LEFT JOIN agents a ON u.id = a.user_id
    WHERE $where
    ORDER BY u.created_at DESC
    LIMIT $offset, $per_page
");

// Get Total
$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE $where");
$total_users = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_users / $per_page);

// Stats by Role
$stats = [];
$roles = ['customer', 'partner', 'agent', 'founder'];
foreach($roles as $role) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = '$role'");
    $stats[$role] = mysqli_fetch_assoc($result)['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen User - Hungerswitch</title>
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
        
        .user-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
            border-left: 4px solid transparent;
        }
        
        .user-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .user-card.customer { border-left-color: #2196F3; }
        .user-card.partner { border-left-color: var(--hs-green); }
        .user-card.agent { border-left-color: #FF9800; }
        .user-card.founder { border-left-color: var(--hs-red); }
        
        .role-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .role-customer { background: #E3F2FD; color: #1976D2; }
        .role-partner { background: #E8F5E9; color: #388E3C; }
        .role-agent { background: #FFF3E0; color: #F57C00; }
        .role-founder { background: #FFEBEE; color: #C62828; }
        
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .status-active { background: #E8F5E9; color: #2E7D32; }
        .status-inactive { background: #FFF3E0; color: #F57C00; }
        .status-banned { background: #FFEBEE; color: #C62828; }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
            border-top: 4px solid;
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-card.customer { border-top-color: #2196F3; }
        .stat-card.partner { border-top-color: var(--hs-green); }
        .stat-card.agent { border-top-color: #FF9800; }
        .stat-card.founder { border-top-color: var(--hs-red); }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0.5rem 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .search-input {
            border-radius: 25px;
            padding: 0.8rem 1.5rem;
            border: 2px solid #e0e0e0;
            transition: 0.3s;
        }
        
        .search-input:focus {
            border-color: var(--hs-green);
            box-shadow: 0 0 0 0.2rem rgba(47, 82, 51, 0.1);
        }
        
        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: 0.3s;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
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
            <li><a href="founder_verification.php"><i class="bi bi-shield-check me-2"></i>Verifikasi</a></li>
            <li><a href="founder_users.php" class="active"><i class="bi bi-people me-2"></i>Manajemen User</a></li>
            <li><hr class="border-light opacity-25 my-3"></li>
            <li><a href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
            <li><a href="logout.php" class="text-warning"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Manajemen User</h2>
                <p class="text-muted mb-0">Kelola semua pengguna platform Hungerswitch</p>
            </div>
            <div>
                <button class="btn btn-success me-2">
                    <i class="bi bi-person-plus me-1"></i>Tambah User
                </button>
                <span class="badge bg-success px-3 py-2">
                    <i class="bi bi-people me-1"></i><?php echo $total_users; ?> Total User
                </span>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-4 animate-in">
            <div class="col-md-3">
                <div class="stat-card customer">
                    <i class="bi bi-person-circle" style="font-size: 2.5rem; color: #2196F3;"></i>
                    <div class="stat-number" style="color: #2196F3;"><?php echo $stats['customer']; ?></div>
                    <div class="stat-label">Customer</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card partner">
                    <i class="bi bi-shop" style="font-size: 2.5rem; color: var(--hs-green);"></i>
                    <div class="stat-number" style="color: var(--hs-green);"><?php echo $stats['partner']; ?></div>
                    <div class="stat-label">Partner</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card agent">
                    <i class="bi bi-person-badge" style="font-size: 2.5rem; color: #FF9800;"></i>
                    <div class="stat-number" style="color: #FF9800;"><?php echo $stats['agent']; ?></div>
                    <div class="stat-label">Agent</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card founder">
                    <i class="bi bi-star-fill" style="font-size: 2.5rem; color: var(--hs-red);"></i>
                    <div class="stat-number" style="color: var(--hs-red);"><?php echo $stats['founder']; ?></div>
                    <div class="stat-label">Founder</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Cari User</label>
                        <input type="text" name="search" class="form-control search-input" 
                               placeholder="Nama, email, atau telepon..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select">
                            <option value="all" <?php echo $role_filter == 'all' ? 'selected' : ''; ?>>Semua Role</option>
                            <option value="customer" <?php echo $role_filter == 'customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="partner" <?php echo $role_filter == 'partner' ? 'selected' : ''; ?>>Partner</option>
                            <option value="agent" <?php echo $role_filter == 'agent' ? 'selected' : ''; ?>>Agent</option>
                            <option value="founder" <?php echo $role_filter == 'founder' ? 'selected' : ''; ?>>Founder</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="banned" <?php echo $status_filter == 'banned' ? 'selected' : ''; ?>>Banned</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- User List -->
        <div class="row">
            <?php if(mysqli_num_rows($users_query) > 0): ?>
                <?php while($user = mysqli_fetch_assoc($users_query)): ?>
                <div class="col-md-6 mb-3 animate-in">
                    <div class="user-card <?php echo $user['role']; ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <?php 
                                $colors = ['customer' => '#2196F3', 'partner' => '#2F5233', 'agent' => '#FF9800', 'founder' => '#D9232D'];
                                $initial = strtoupper(substr($user['name'], 0, 1));
                                ?>
                                <div class="user-avatar me-3" style="background: <?php echo $colors[$user['role']]; ?>">
                                    <?php echo $initial; ?>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($user['name']); ?></h6>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($user['email']); ?>
                                    </p>
                                    <?php if($user['phone']): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-phone me-1"></i><?php echo htmlspecialchars($user['phone']); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <?php if($user['role'] == 'partner' && $user['business_name']): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-shop me-1"></i><?php echo htmlspecialchars($user['business_name']); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <?php if($user['role'] == 'agent' && $user['agent_region']): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($user['agent_region']); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2">
                                        <span class="role-badge role-<?php echo $user['role']; ?>">
                                            <i class="bi bi-<?php 
                                                echo $user['role'] == 'customer' ? 'person' : 
                                                    ($user['role'] == 'partner' ? 'shop' : 
                                                    ($user['role'] == 'agent' ? 'person-badge' : 'star')); 
                                            ?> me-1"></i>
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                        <span class="status-badge status-<?php echo $user['status']; ?> ms-2">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>Lihat Detail</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <?php if($user['status'] == 'active'): ?>
                                    <li>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="new_status" value="inactive">
                                            <button type="submit" class="dropdown-item text-warning">
                                                <i class="bi bi-pause-circle me-2"></i>Nonaktifkan
                                            </button>
                                        </form>
                                    </li>
                                    <?php else: ?>
                                    <li>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="new_status" value="active">
                                            <button type="submit" class="dropdown-item text-success">
                                                <i class="bi bi-play-circle me-2"></i>Aktifkan
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>
                                    <li>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin ban user ini?')">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="delete_user">
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-x-circle me-2"></i>Ban User
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                Bergabung: <?php echo date('d M Y', strtotime($user['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 5rem; color: #ddd;"></i>
                        <h5 class="text-muted mt-3">Tidak ada user ditemukan</h5>
                        <p class="text-muted">Coba ubah filter pencarian Anda</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&role=<?php echo $role_filter; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>