<?php 
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || !in_array('partner', $_SESSION['user_roles'])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch partner data
$query = "SELECT p.*, u.name, u.email, u.phone 
          FROM partners p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$partner = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(!$partner) {
    die("Data mitra tidak ditemukan!");
}

// Fetch partner products
$products_query = "SELECT * FROM products WHERE partner_id = ? ORDER BY created_at DESC LIMIT 5";
$products_stmt = mysqli_prepare($conn, $products_query);
mysqli_stmt_bind_param($products_stmt, "i", $user_id);
mysqli_stmt_execute($products_stmt);
$products_result = mysqli_stmt_get_result($products_stmt);
$total_products = mysqli_num_rows($products_result);

// Calculate stats
$stats_query = "SELECT 
                (SELECT COUNT(*) FROM products WHERE partner_id = ?) as total_products,
                (SELECT COUNT(DISTINCT o.id) FROM orders o 
                 JOIN order_items oi ON o.id = oi.order_id 
                 JOIN products p ON oi.product_id = p.id 
                 WHERE p.partner_id = ? AND o.status = 'completed') as total_sold,
                (SELECT SUM(oi.quantity * oi.price) FROM orders o 
                 JOIN order_items oi ON o.id = oi.order_id 
                 JOIN products p ON oi.product_id = p.id 
                 WHERE p.partner_id = ? AND o.status = 'completed') as total_revenue";
$stats_stmt = mysqli_prepare($conn, $stats_query);
mysqli_stmt_bind_param($stats_stmt, "iii", $user_id, $user_id, $user_id);
mysqli_stmt_execute($stats_stmt);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($stats_stmt));

$total_products_all = $stats['total_products'] ?? 0;
$total_sold = $stats['total_sold'] ?? 0;
$total_revenue = $stats['total_revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mitra - <?php echo htmlspecialchars($partner['business_name']); ?></title>
    
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
            --info-box-bg: #FFF7F0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--page-bg); 
            color: var(--dark-text); 
        }

        /* Navbar */
        .navbar { 
            background-color: white !important; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            padding: 1rem 0; 
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

        /* Dashboard Layout */
        .dashboard-container { 
            display: flex; 
            min-height: calc(100vh - 76px); 
            margin-top: 76px; 
        }

        /* Sidebar */
        .sidebar { 
            width: 280px; 
            background-color: white; 
            border-right: 1px solid #e0e0e0; 
            padding: 2rem 0; 
            position: fixed; 
            height: calc(100vh - 76px); 
            overflow-y: auto; 
        }
        .partner-profile { 
            padding: 0 1.5rem 1.5rem; 
            border-bottom: 1px solid #e0e0e0; 
            margin-bottom: 1.5rem; 
            text-align: center; 
        }
        .partner-avatar { 
            width: 80px; 
            height: 80px; 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            font-size: 2rem; 
            font-weight: 700; 
            margin: 0 auto 1rem auto;
            box-shadow: 0 8px 20px rgba(217, 35, 45, 0.3);
        }
        .partner-name { 
            font-weight: 600; 
            font-size: 1.1rem; 
            margin-bottom: 0.25rem; 
        }
        .partner-type { 
            font-size: 0.9rem; 
            color: #6c757d; 
        }
        .verification-badge { 
            display: inline-block; 
            padding: 0.25rem 0.75rem; 
            border-radius: 12px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            margin-top: 0.5rem; 
        }
        .badge-verified { 
            background: rgba(47, 82, 51, 0.15); 
            color: var(--main-green); 
        }
        .badge-pending { 
            background: rgba(217, 35, 45, 0.15); 
            color: var(--main-red); 
        }

        /* Navigation */
        .nav-menu { 
            list-style: none; 
            padding: 0; 
            margin: 0; 
        }
        .nav-link-custom { 
            display: flex; 
            align-items: center; 
            padding: 0.875rem 1.5rem; 
            color: var(--dark-text); 
            text-decoration: none; 
            font-weight: 500; 
            transition: all 0.3s; 
            border-left: 3px solid transparent; 
        }
        .nav-link-custom:hover { 
            background-color: rgba(217, 35, 45, 0.05); 
            color: var(--main-red); 
            border-left-color: var(--main-red); 
        }
        .nav-link-custom.active { 
            background-color: var(--info-box-bg); 
            color: var(--main-red); 
            border-left-color: var(--main-red); 
        }
        .nav-link-custom i { 
            width: 24px; 
            margin-right: 0.875rem; 
            font-size: 1.1rem; 
        }

        /* Main Content */
        .main-content { 
            flex: 1; 
            margin-left: 280px; 
            padding: 2rem; 
        }

        /* Header */
        .welcome-header { 
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white; 
            padding: 2rem; 
            border-radius: 16px; 
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(47, 82, 51, 0.3);
        }
        .welcome-header h1 { 
            font-size: 2rem; 
            font-weight: 700; 
            margin-bottom: 0.5rem; 
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
            padding: 1.5rem; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid rgba(217, 35, 45, 0.05);
        }
        .stat-card:hover { 
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(217, 35, 45, 0.15);
        }
        .stat-icon { 
            width: 50px; 
            height: 50px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.5rem; 
            margin-bottom: 1rem; 
        }
        .stat-icon.red { 
            background: rgba(217, 35, 45, 0.1); 
            color: var(--main-red); 
        }
        .stat-icon.green { 
            background: rgba(47, 82, 51, 0.1); 
            color: var(--main-green); 
        }
        .stat-icon.blue { 
            background: rgba(33, 150, 243, 0.1); 
            color: #2196F3; 
        }
        .stat-value { 
            font-size: 2rem; 
            font-weight: 700; 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-label { 
            font-size: 0.9rem; 
            color: #666; 
        }

        /* Content Cards */
        .content-card { 
            background: white; 
            border-radius: 12px; 
            padding: 1.5rem; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            margin-bottom: 1.5rem;
            border: 2px solid rgba(217, 35, 45, 0.05);
        }
        .card-header-custom { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 1.5rem; 
            padding-bottom: 1rem; 
            border-bottom: 2px solid #f0f0f0; 
        }
        .card-title { 
            font-size: 1.25rem; 
            font-weight: 600; 
            margin: 0; 
        }

        .btn-action { 
            padding: 0.5rem 1.25rem; 
            border-radius: 8px; 
            font-weight: 600; 
            transition: all 0.3s; 
            border: none; 
            cursor: pointer; 
        }
        .btn-primary-custom { 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.3);
        }
        .btn-primary-custom:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 35, 45, 0.4);
        }

        /* Product Table */
        .product-row { 
            display: grid; 
            grid-template-columns: 80px 2fr 1fr 1fr 1fr 150px; 
            gap: 1rem; 
            padding: 1rem; 
            border: 1px solid #f0f0f0; 
            border-radius: 8px; 
            margin-bottom: 0.5rem; 
            align-items: center; 
            transition: all 0.3s;
        }
        .product-row:hover { 
            background-color: var(--info-box-bg);
            border-color: rgba(217, 35, 45, 0.2);
        }
        .product-image { 
            width: 60px; 
            height: 60px; 
            border-radius: 8px; 
            object-fit: cover; 
        }
        .product-name { 
            font-weight: 600; 
        }
        .product-status { 
            padding: 0.25rem 0.75rem; 
            border-radius: 12px; 
            font-size: 0.85rem; 
            font-weight: 600; 
            display: inline-block; 
        }
        .status-available { 
            background: rgba(47, 82, 51, 0.15); 
            color: var(--main-green); 
        }
        .status-soldout { 
            background: rgba(217, 35, 45, 0.15); 
            color: var(--main-red); 
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { 
                width: 100%; 
                position: static; 
                height: auto; 
            }
            .main-content { 
                margin-left: 0; 
            }
            .dashboard-container { 
                flex-direction: column; 
            }
            .product-row { 
                grid-template-columns: 1fr; 
            }
        }
    </style>
</head>
<body>

    <nav class="navbar fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-logo-icon">HS</span>
                <div class="brand-text ms-2">
                    <strong>Hungerswitch</strong>
                    <div class="brand-subtext" style="font-size: 0.75rem; color: #6c757d;">Dashboard Mitra</div>
                </div>
            </a>
            <div class="d-flex align-items-center gap-3">
                <?php if(count($_SESSION['user_roles']) > 1): ?>
                    <a href="select_role.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left-right me-1"></i>Ganti Role
                    </a>
                <?php endif; ?>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-house-door me-1"></i>Ke Beranda
                </a>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="partner-profile">
                <div class="partner-avatar">
                    <?php echo strtoupper(substr($partner['business_name'], 0, 1)); ?>
                </div>
                <div class="partner-name"><?php echo htmlspecialchars($partner['business_name']); ?></div>
                <div class="partner-type">
                    <i class="bi bi-shop"></i> <?php echo ucfirst($partner['business_type']); ?>
                </div>
                <span class="verification-badge badge-<?php echo $partner['verification_status']; ?>">
                    <?php echo $partner['verification_status'] == 'verified' ? '✓ Terverifikasi' : 'Pending Verifikasi'; ?>
                </span>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="mitra_dashboard.php" class="nav-link-custom active">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="mitra_products.php" class="nav-link-custom">
                        <i class="bi bi-box-seam"></i>
                        <span>Produk Saya</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="mitra_orders.php" class="nav-link-custom">
                        <i class="bi bi-receipt"></i>
                        <span>Pesanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="mitra_finance.php" class="nav-link-custom">
                        <i class="bi bi-wallet2"></i>
                        <span>Keuangan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link-custom" onclick="alert('Fitur profil akan segera hadir!'); return false;">
                        <i class="bi bi-shop"></i>
                        <span>Profil Bisnis</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link-custom text-danger">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="welcome-header">
                <h1>Dashboard <?php echo htmlspecialchars($partner['business_name']); ?></h1>
                <p>Kelola produk dan penjualan Anda dengan mudah</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon red">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-label">Total Produk</div>
                    <div class="stat-value"><?php echo $total_products_all; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-label">Produk Terjual</div>
                    <div class="stat-value"><?php echo $total_sold; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-value">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header-custom">
                    <h3 class="card-title">Produk Terbaru</h3>
                    <a href="mitra_products.php" class="btn-action btn-primary-custom">
                        <i class="bi bi-box-seam me-2"></i>Kelola Produk
                    </a>
                </div>

                <?php if($total_products > 0): ?>
                    <?php while($product = mysqli_fetch_assoc($products_result)): ?>
                        <div class="product-row">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="product-image" 
                                 alt="Product" 
                                 onerror="this.src='https://via.placeholder.com/60'">
                            <div>
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($product['category']); ?></small>
                            </div>
                            <div>Rp <?php echo number_format($product['discounted_price'], 0, ',', '.'); ?></div>
                            <div>Stok: <?php echo $product['stock']; ?></div>
                            <div>
                                <span class="product-status status-<?php echo $product['stock'] > 0 ? 'available' : 'soldout'; ?>">
                                    <?php echo $product['stock'] > 0 ? 'Tersedia' : 'Habis'; ?>
                                </span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary" onclick="alert('Fitur edit akan segera hadir!')">Edit</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <div class="text-center mt-3">
                        <a href="mitra_products.php" class="btn btn-outline-primary">
                            Lihat Semua Produk <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                        <p>Belum ada produk. Mulai tambahkan produk Anda!</p>
                        <button class="btn-action btn-primary-custom mt-2" onclick="alert('Fitur tambah produk akan segera hadir!')">
                            <i class="bi bi-plus-lg me-2"></i>Tambah Produk
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="content-card">
                        <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>Pesanan Terbaru</h5>
                        <p class="text-muted">Lihat semua pesanan masuk</p>
                        <a href="mitra_orders.php" class="btn btn-outline-success w-100">
                            <i class="bi bi-arrow-right me-2"></i>Kelola Pesanan
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="content-card">
                        <h5 class="mb-3"><i class="bi bi-graph-up me-2"></i>Laporan Keuangan</h5>
                        <p class="text-muted">Pantau pendapatan bisnis Anda</p>
                        <a href="mitra_finance.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-arrow-right me-2"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>