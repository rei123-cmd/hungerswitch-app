<?php 
//session_start();
include 'config.php';

// Check if user is admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login_unified.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch admin data
$admin_query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $admin_query);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
$admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Dashboard Statistics
$stats = [];

// Total Users
$users_query = "SELECT COUNT(*) as total FROM users WHERE role != 'admin'";
$stats['total_users'] = mysqli_fetch_assoc(mysqli_query($conn, $users_query))['total'];

// Total Orders
$orders_query = "SELECT COUNT(*) as total, SUM(total_amount) as revenue FROM orders";
$orders_data = mysqli_fetch_assoc(mysqli_query($conn, $orders_query));
$stats['total_orders'] = $orders_data['total'];
$stats['total_revenue'] = $orders_data['revenue'] ?? 0;

// Total Products
$products_query = "SELECT COUNT(*) as total FROM products";
$stats['total_products'] = mysqli_fetch_assoc(mysqli_query($conn, $products_query))['total'];

// Pending Orders
$pending_query = "SELECT COUNT(*) as total FROM orders WHERE status = 'pending'";
$stats['pending_orders'] = mysqli_fetch_assoc(mysqli_query($conn, $pending_query))['total'];

// Total Partners
$partners_query = "SELECT COUNT(*) as total FROM partners";
$stats['total_partners'] = mysqli_fetch_assoc(mysqli_query($conn, $partners_query))['total'];

// Total Agents
$agents_query = "SELECT COUNT(*) as total FROM agents";
$stats['total_agents'] = mysqli_fetch_assoc(mysqli_query($conn, $agents_query))['total'];

// Recent Orders
$recent_orders_query = "SELECT o.*, u.name as customer_name FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC LIMIT 10";
$recent_orders = mysqli_query($conn, $recent_orders_query);

// Recent Users
$recent_users_query = "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC LIMIT 10";
$recent_users = mysqli_query($conn, $recent_users_query);

// Top Products
$top_products_query = "SELECT p.*, COUNT(oi.id) as order_count 
                       FROM products p 
                       LEFT JOIN order_items oi ON p.id = oi.product_id 
                       GROUP BY p.id 
                       ORDER BY order_count DESC 
                       LIMIT 5";
$top_products = mysqli_query($conn, $top_products_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hungerswitch</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

        :root {
            --admin-primary: #6366f1;
            --admin-secondary: #8b5cf6;
            --admin-success: #10b981;
            --admin-danger: #ef4444;
            --admin-warning: #f59e0b;
            --admin-info: #3b82f6;
            --admin-dark: #1e293b;
            --admin-light: #f8fafc;
            --sidebar-width: 280px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--admin-light);
            color: var(--admin-dark);
        }

        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
            padding: 2rem 0;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(99, 102, 241, 0.2);
        }

        .admin-logo {
            text-align: center;
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 2rem;
        }

        .admin-logo-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
            backdrop-filter: blur(10px);
        }

        .admin-logo h3 {
            color: white;
            font-weight: 800;
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .admin-logo p {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            margin: 0;
        }

        .admin-menu {
            list-style: none;
            padding: 0;
        }

        .admin-menu-item {
            margin-bottom: 0.25rem;
        }

        .admin-menu-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .admin-menu-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }

        .admin-menu-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: white;
        }

        .admin-menu-icon {
            width: 24px;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        /* Main Content */
        .admin-main {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
        }

        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-welcome h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--admin-dark);
            margin-bottom: 0.25rem;
        }

        .admin-welcome p {
            color: #64748b;
            margin: 0;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: var(--card-color, var(--admin-primary));
            opacity: 0.1;
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--card-color, var(--admin-primary));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--admin-dark);
            line-height: 1;
        }

        .stat-change {
            margin-top: 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .stat-change.positive {
            color: var(--admin-success);
        }

        .stat-change.negative {
            color: var(--admin-danger);
        }

        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--admin-dark);
            margin: 0;
        }

        /* Table */
        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .admin-table thead th {
            font-weight: 600;
            color: #64748b;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 1rem;
            text-align: left;
        }

        .admin-table tbody tr {
            background: #f8fafc;
            transition: all 0.3s ease;
        }

        .admin-table tbody tr:hover {
            background: #f1f5f9;
            transform: scale(1.01);
        }

        .admin-table tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
        }

        .admin-table tbody tr td:first-child {
            border-radius: 12px 0 0 12px;
        }

        .admin-table tbody tr td:last-child {
            border-radius: 0 12px 12px 0;
        }

        /* Badges */
        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-pending { background: rgba(245, 158, 11, 0.15); color: var(--admin-warning); }
        .badge-completed { background: rgba(16, 185, 129, 0.15); color: var(--admin-success); }
        .badge-cancelled { background: rgba(239, 68, 68, 0.15); color: var(--admin-danger); }
        .badge-active { background: rgba(16, 185, 129, 0.15); color: var(--admin-success); }
        .badge-inactive { background: rgba(100, 116, 139, 0.15); color: #64748b; }

        /* Buttons */
        .btn-admin {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-admin-primary {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            color: white;
        }

        .btn-admin-success { background: var(--admin-success); color: white; }
        .btn-admin-danger { background: var(--admin-danger); color: white; }
        .btn-admin-warning { background: var(--admin-warning); color: white; }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        /* Charts */
        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Page Content */
        .page-section {
            display: none;
        }

        .page-section.active {
            display: block;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            border-color: var(--admin-primary);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.2);
        }

        .quick-action-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        /* User Avatar in Table */
        .user-avatar-small {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            margin-right: 0.75rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-header {
                flex-direction: column;
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-logo">
            <div class="admin-logo-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <h3>Admin Panel</h3>
            <p>Hungerswitch</p>
        </div>

        <ul class="admin-menu">
            <li class="admin-menu-item">
                <a href="#" class="admin-menu-link active" data-page="dashboard">
                    <i class="bi bi-speedometer2 admin-menu-icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="#" class="admin-menu-link" data-page="users">
                    <i class="bi bi-people admin-menu-icon"></i>
                    <span>Kelola Users</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="#" class="admin-menu-link" data-page="products">
                    <i class="bi bi-box-seam admin-menu-icon"></i>
                    <span>Kelola Produk</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="#" class="admin-menu-link" data-page="orders">
                    <i class="bi bi-receipt admin-menu-icon"></i>
                    <span>Kelola Orders</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="#" class="admin-menu-link" data-page="partners">
                    <i class="bi bi-shop admin-menu-icon"></i>
                    <span>Kelola Mitra</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="#" class="admin-menu-link" data-page="agents">
                    <i class="bi bi-shield-check admin-menu-icon"></i>
                    <span>Kelola Agen</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="#" class="admin-menu-link" data-page="reports">
                    <i class="bi bi-graph-up admin-menu-icon"></i>
                    <span>Laporan</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="#" class="admin-menu-link" data-page="settings">
                    <i class="bi bi-gear admin-menu-icon"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="index.php" class="admin-menu-link">
                    <i class="bi bi-house admin-menu-icon"></i>
                    <span>Ke Website</span>
                </a>
            </li>
            <li class="admin-menu-item">
                <a href="logout.php" class="admin-menu-link" style="color: rgba(239, 68, 68, 0.8);">
                    <i class="bi bi-box-arrow-right admin-menu-icon"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Header -->
        <div class="admin-header">
            <div class="admin-welcome">
                <h1>Selamat Datang, <?php echo htmlspecialchars(explode(' ', $admin['name'])[0]); ?>! 👋</h1>
                <p>Kelola seluruh sistem Hungerswitch dari sini</p>
            </div>
            <div class="admin-profile">
                <div>
                    <div style="font-weight: 600; text-align: right;"><?php echo htmlspecialchars($admin['name']); ?></div>
                    <div style="font-size: 0.85rem; color: #64748b; text-align: right;">Administrator</div>
                </div>
                <div class="admin-avatar">
                    <?php echo strtoupper(substr($admin['name'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <!-- Dashboard Page -->
        <div class="page-section active" id="page-dashboard">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card" style="--card-color: #6366f1;">
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                    <div class="stat-change positive">
                        <i class="bi bi-arrow-up"></i> +12% dari bulan lalu
                    </div>
                </div>

                <div class="stat-card" style="--card-color: #10b981;">
                    <div class="stat-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                    <div class="stat-change positive">
                        <i class="bi bi-arrow-up"></i> +8% dari bulan lalu
                    </div>
                </div>

                <div class="stat-card" style="--card-color: #f59e0b;">
                    <div class="stat-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-label">Total Products</div>
                    <div class="stat-value"><?php echo number_format($stats['total_products']); ?></div>
                    <div class="stat-change positive">
                        <i class="bi bi-arrow-up"></i> +5% dari bulan lalu
                    </div>
                </div>

                <div class="stat-card" style="--card-color: #8b5cf6;">
                    <div class="stat-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value" style="font-size: 1.8rem;">Rp <?php echo number_format($stats['total_revenue']/1000000, 1); ?>M</div>
                    <div class="stat-change positive">
                        <i class="bi bi-arrow-up"></i> +15% dari bulan lalu
                    </div>
                </div>

                <div class="stat-card" style="--card-color: #ef4444;">
                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-label">Pending Orders</div>
                    <div class="stat-value"><?php echo number_format($stats['pending_orders']); ?></div>
                    <div class="stat-change negative">
                        <i class="bi bi-arrow-down"></i> Butuh perhatian
                    </div>
                </div>

                <div class="stat-card" style="--card-color: #3b82f6;">
                    <div class="stat-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div class="stat-label">Active Partners</div>
                    <div class="stat-value"><?php echo number_format($stats['total_partners']); ?></div>
                    <div class="stat-change positive">
                        <i class="bi bi-arrow-up"></i> +3 mitra baru
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue Overview</h3>
                            <select class="form-select" style="width: auto;">
                                <option>Last 7 Days</option>
                                <option>Last 30 Days</option>
                                <option>Last 3 Months</option>
                            </select>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Order Status</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Recent Orders</h3>
                    <a href="#" class="btn-admin btn-admin-primary btn-sm" onclick="switchPage('orders')">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($recent_orders) > 0): ?>
                                <?php while($order = mysqli_fetch_assoc($recent_orders)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-small">
                                                <?php echo strtoupper(substr($order['customer_name'], 0, 1)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($order['customer_name']); ?>
                                        </div>
                                    </td>
                                    <td><strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong></td>
                                    <td>
                                        <span class="badge-custom badge-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <button class="btn-admin btn-admin-primary btn-sm" onclick="alert('View order details')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada orders</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Users Management Page -->
        <div class="page-section" id="page-users">
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Users</h3>
                    <button class="btn-admin btn-admin-primary" onclick="alert('Tambah user baru')">
                        <i class="bi bi-plus-lg"></i> Tambah User
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php mysqli_data_seek($recent_users, 0); ?>
                            <?php while($user = mysqli_fetch_assoc($recent_users)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-small">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <?php echo htmlspecialchars($user['name']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="badge-custom badge-active"><?php echo ucfirst($user['role']); ?></span></td>
                                <td>
                                    <span class="badge-custom badge-<?php echo $user['status'] == 'active' ? 'active' : 'inactive'; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="btn-admin btn-admin-warning btn-sm" onclick="alert('Edit user')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-admin btn-admin-danger btn-sm" onclick="alert('Delete user')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Products Management Page -->
        <div class="page-section" id="page-products">
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Produk</h3>
                    <button class="btn-admin btn-admin-primary" onclick="alert('Tambah produk baru')">
                        <i class="bi bi-plus-lg"></i> Tambah Produk
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Partner</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $products_query = "SELECT p.*, u.name as partner_name FROM products p 
                                              JOIN users u ON p.partner_id = u.id 
                                              ORDER BY p.created_at DESC LIMIT 20";
                            $products_result = mysqli_query($conn, $products_query);
                            while($product = mysqli_fetch_assoc($products_result)): 
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                             style="width: 50px; height: 50px; border-radius: 10px; margin-right: 1rem; object-fit: cover;"
                                             onerror="this.src='https://via.placeholder.com/50'">
                                        <div>
                                            <div style="font-weight: 600;"><?php echo htmlspecialchars($product['name']); ?></div>
                                            <div style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($product['category']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($product['partner_name']); ?></td>
                                <td><strong>Rp <?php echo number_format($product['discounted_price'], 0, ',', '.'); ?></strong></td>
                                <td><?php echo $product['stock']; ?> pcs</td>
                                <td>
                                    <span class="badge-custom badge-<?php echo $product['status'] == 'available' ? 'active' : 'inactive'; ?>">
                                        <?php echo ucfirst($product['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-admin btn-admin-warning btn-sm" onclick="alert('Edit produk')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn-admin btn-admin-danger btn-sm" onclick="alert('Hapus produk')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Orders Management Page -->
        <div class="page-section" id="page-orders">
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Orders</h3>
                    <div class="d-flex gap-2">
                        <select class="form-select" style="width: auto;">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Confirmed</option>
                            <option>Completed</option>
                            <option>Cancelled</option>
                        </select>
                        <button class="btn-admin btn-admin-primary" onclick="alert('Export orders')">
                            <i class="bi bi-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $all_orders_query = "SELECT o.*, u.name as customer_name FROM orders o 
                                                JOIN users u ON o.user_id = u.id 
                                                ORDER BY o.created_at DESC";
                            $all_orders = mysqli_query($conn, $all_orders_query);
                            while($order = mysqli_fetch_assoc($all_orders)): 
                            $order_data = json_decode(base64_decode($order['order_data']), true);
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-small">
                                            <?php echo strtoupper(substr($order['customer_name'], 0, 1)); ?>
                                        </div>
                                        <?php echo htmlspecialchars($order['customer_name']); ?>
                                    </div>
                                </td>
                                <td><?php echo count($order_data['items']); ?> items</td>
                                <td><strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong></td>
                                <td><?php echo ucfirst($order['payment_method']); ?></td>
                                <td>
                                    <span class="badge-custom badge-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <button class="btn-admin btn-admin-primary btn-sm" onclick="viewOrderDetail('<?php echo $order['order_id']; ?>')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if($order['status'] == 'pending'): ?>
                                    <button class="btn-admin btn-admin-success btn-sm" onclick="confirmOrder('<?php echo $order['order_id']; ?>')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Partners Management Page -->
        <div class="page-section" id="page-partners">
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Mitra</h3>
                    <button class="btn-admin btn-admin-primary" onclick="alert('Tambah mitra')">
                        <i class="bi bi-plus-lg"></i> Tambah Mitra
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Products</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $partners_list_query = "SELECT p.*, u.name as owner_name, 
                                                   (SELECT COUNT(*) FROM products WHERE partner_id = u.id) as product_count
                                                   FROM partners p 
                                                   JOIN users u ON p.user_id = u.id";
                            $partners_list = mysqli_query($conn, $partners_list_query);
                            while($partner = mysqli_fetch_assoc($partners_list)): 
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($partner['business_name']); ?></div>
                                    <div style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($partner['business_address']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($partner['owner_name']); ?></td>
                                <td><span class="badge-custom badge-active"><?php echo ucfirst($partner['business_type']); ?></span></td>
                                <td><?php echo $partner['product_count']; ?> products</td>
                                <td>
                                    <span class="badge-custom badge-<?php echo $partner['verification_status'] == 'verified' ? 'completed' : 'pending'; ?>">
                                        <?php echo ucfirst($partner['verification_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-admin btn-admin-primary btn-sm" onclick="alert('View partner')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if($partner['verification_status'] == 'pending'): ?>
                                    <button class="btn-admin btn-admin-success btn-sm" onclick="verifyPartner(<?php echo $partner['user_id']; ?>)">
                                        <i class="bi bi-check-lg"></i> Verify
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Agents Management Page -->
        <div class="page-section" id="page-agents">
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Agen</h3>
                    <button class="btn-admin btn-admin-primary" onclick="alert('Tambah agen')">
                        <i class="bi bi-plus-lg"></i> Tambah Agen
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Region</th>
                                <th>Communities</th>
                                <th>Distributions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $agents_list_query = "SELECT a.*, u.name, u.email, u.status 
                                                 FROM agents a 
                                                 JOIN users u ON a.user_id = u.id";
                            $agents_list = mysqli_query($conn, $agents_list_query);
                            while($agent = mysqli_fetch_assoc($agents_list)): 
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-small">
                                            <?php echo strtoupper(substr($agent['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600;"><?php echo htmlspecialchars($agent['name']); ?></div>
                                            <div style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($agent['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($agent['region']); ?></td>
                                <td><?php echo $agent['active_communities'] ?? 0; ?> communities</td>
                                <td>-</td>
                                <td>
                                    <span class="badge-custom badge-<?php echo $agent['status'] == 'active' ? 'active' : 'inactive'; ?>">
                                        <?php echo ucfirst($agent['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-admin btn-admin-primary btn-sm" onclick="alert('View agent')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn-admin btn-admin-warning btn-sm" onclick="alert('Edit agent')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reports Page -->
        <div class="page-section" id="page-reports">
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Laporan & Analitik</h3>
                    <button class="btn-admin btn-admin-primary" onclick="alert('Generate report')">
                        <i class="bi bi-file-earmark-text"></i> Generate Report
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h4 class="mb-3">Top Products</h4>
                        <div class="chart-container">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h4 class="mb-3">Sales by Category</h4>
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <div class="quick-action-card" onclick="alert('Download monthly report')">
                        <div class="quick-action-icon">📊</div>
                        <h5>Monthly Report</h5>
                        <p>Download laporan bulanan</p>
                    </div>
                    <div class="quick-action-card" onclick="alert('Download financial report')">
                        <div class="quick-action-icon">💰</div>
                        <h5>Financial Report</h5>
                        <p>Laporan keuangan</p>
                    </div>
                    <div class="quick-action-card" onclick="alert('Download user report')">
                        <div class="quick-action-icon">👥</div>
                        <h5>User Analytics</h5>
                        <p>Analisis pengguna</p>
                    </div>
                    <div class="quick-action-card" onclick="alert('Download partner report')">
                        <div class="quick-action-icon">🏪</div>
                        <h5>Partner Report</h5>
                        <p>Laporan mitra</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Page -->
        <div class="page-section" id="page-settings">
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Pengaturan Sistem</h3>
                </div>
                
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Website</label>
                            <input type="text" class="form-control" value="Hungerswitch">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Support</label>
                            <input type="email" class="form-control" value="support@hungerswitch.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Service Fee (%)</label>
                            <input type="number" class="form-control" value="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Min. Top-up Amount</label>
                            <input type="number" class="form-control" value="10000">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">About Us</label>
                            <textarea class="form-control" rows="4">Hungerswitch adalah platform untuk mengurangi food waste...</textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn-admin btn-admin-success">
                                <i class="bi bi-save"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        // Navigation
        document.querySelectorAll('.admin-menu-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const pageName = this.getAttribute('data-page');
                if(pageName) {
                    e.preventDefault();
                    switchPage(pageName);
                }
            });
        });

        function switchPage(pageName) {
            // Update active menu
            document.querySelectorAll('.admin-menu-link').forEach(l => l.classList.remove('active'));
            document.querySelector(`[data-page="${pageName}"]`).classList.add('active');
            
            // Show page
            document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
            document.getElementById(`page-${pageName}`).classList.add('active');
        }

        // Charts
        const ctx1 = document.getElementById('revenueChart');
        if(ctx1) {
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Revenue',
                        data: [12000000, 15000000, 18000000, 22000000, 25000000, 28000000, 32000000],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        }

        const ctx2 = document.getElementById('orderStatusChart');
        if(ctx2) {
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Pending', 'Cancelled'],
                    datasets: [{
                        data: [<?php 
                            $completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='completed'"))['c'];
                            $pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='pending'"))['c'];
                            $cancelled = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE status='cancelled'"))['c'];
                            echo "$completed, $pending, $cancelled";
                        ?>],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // Functions
        function viewOrderDetail(orderId) {
            window.location.href = `order_success.php?order_id=${orderId}`;
        }

        function confirmOrder(orderId) {
            if(confirm('Konfirmasi order ini?')) {
                alert('Order dikonfirmasi! (Implementasi backend diperlukan)');
            }
        }

        function verifyPartner(userId) {
            if(confirm('Verifikasi mitra ini?')) {
                alert('Mitra diverifikasi! (Implementasi backend diperlukan)');
            }
        }

        // Mobile sidebar toggle
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('show');
        }
    </script>
</body>
</html>