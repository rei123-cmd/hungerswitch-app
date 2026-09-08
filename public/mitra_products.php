<?php 
session_start();
include 'config.php';

if(!isset($_SESSION['user_id']) || !in_array('partner', $_SESSION['user_roles'])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch partner data
$query = "SELECT p.*, u.name FROM partners p JOIN users u ON p.user_id = u.id WHERE p.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$partner = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Fetch products
$products_query = "SELECT * FROM products WHERE partner_id = ? ORDER BY created_at DESC";
$products_stmt = mysqli_prepare($conn, $products_query);
mysqli_stmt_bind_param($products_stmt, "i", $user_id);
mysqli_stmt_execute($products_stmt);
$products = mysqli_stmt_get_result($products_stmt);
$total_products = mysqli_num_rows($products);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Saya - Dashboard Mitra</title>
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
            background: var(--page-bg); 
            padding-top: 80px; 
        }
        
        .navbar { 
            background: white; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            padding: 1rem 0; 
        }
        .brand-logo-icon { 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white; 
            padding: 10px 12px; 
            border-radius: 12px; 
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.3);
        }
        
        .page-header { 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white; 
            padding: 2rem; 
            border-radius: 16px; 
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(217, 35, 45, 0.3);
        }
        .page-header h1 { 
            font-size: 2rem; 
            font-weight: 700; 
            margin-bottom: 0.5rem; 
        }
        
        .stats-row { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 1rem; 
            margin-bottom: 2rem; 
        }
        .stat-box { 
            background: white; 
            padding: 1.5rem; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 2px solid rgba(217, 35, 45, 0.05);
            transition: all 0.3s;
        }
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(217, 35, 45, 0.15);
        }
        .stat-value { 
            font-size: 2rem; 
            font-weight: 700; 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-label { 
            color: #666; 
            font-size: 0.9rem; 
        }
        
        .content-card { 
            background: white; 
            border-radius: 12px; 
            padding: 1.5rem; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
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
        
        .product-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 1.5rem; 
        }
        .product-card { 
            background: white; 
            border: 2px solid rgba(217, 35, 45, 0.1); 
            border-radius: 12px; 
            overflow: hidden; 
            transition: all 0.3s; 
        }
        .product-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 25px rgba(217, 35, 45, 0.2);
            border-color: var(--main-red);
        }
        .product-img { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
        }
        .product-body { 
            padding: 1rem; 
        }
        .product-name { 
            font-weight: 600; 
            margin-bottom: 0.5rem; 
        }
        .product-price { 
            font-size: 1.3rem; 
            font-weight: 700; 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .product-status { 
            padding: 0.25rem 0.75rem; 
            border-radius: 12px; 
            font-size: 0.85rem; 
            font-weight: 600; 
        }
        .status-available { 
            background: rgba(47, 82, 51, 0.15); 
            color: var(--main-green); 
        }
        .status-soldout { 
            background: rgba(217, 35, 45, 0.15); 
            color: var(--main-red); 
        }
        
        .btn-red { 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white; 
            border: none; 
            padding: 0.5rem 1.5rem; 
            border-radius: 8px; 
            font-weight: 600; 
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.3);
        }
        .btn-red:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 35, 45, 0.4);
        }
    </style>
</head>
<body>
    <nav class="navbar fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="mitra_dashboard.php">
                <span class="brand-logo-icon">HS</span>
                <div class="ms-2">
                    <strong>Hungerswitch</strong>
                    <div style="font-size: 0.75rem; color: #6c757d;">Dashboard Mitra</div>
                </div>
            </a>
            <div class="d-flex gap-2">
                <a href="mitra_dashboard.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-house-door"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1><i class="bi bi-box-seam me-2"></i>Produk Saya</h1>
            <p>Kelola semua produk surplus Anda</p>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-value"><?php echo $total_products; ?></div>
                <div class="stat-label">Total Produk</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">
                    <?php 
                    $available = 0;
                    mysqli_data_seek($products, 0);
                    while($p = mysqli_fetch_assoc($products)) {
                        if($p['stock'] > 0) $available++;
                    }
                    echo $available;
                    ?>
                </div>
                <div class="stat-label">Tersedia</div>
            </div>
            <div class="stat-box">
                <div class="stat-value"><?php echo $total_products - $available; ?></div>
                <div class="stat-label">Habis Stok</div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header-custom">
                <h3 class="mb-0">Semua Produk</h3>
                <button class="btn-red" onclick="alert('Fitur tambah produk akan segera hadir!')">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Produk
                </button>
            </div>

            <?php if($total_products > 0): ?>
                <div class="product-grid">
                    <?php mysqli_data_seek($products, 0); ?>
                    <?php while($product = mysqli_fetch_assoc($products)): ?>
                        <div class="product-card">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="product-img" 
                                 alt="Product"
                                 onerror="this.src='https://via.placeholder.com/280x200?text=No+Image'">
                            <div class="product-body">
                                <div class="text-muted small mb-1"><?php echo htmlspecialchars($product['category']); ?></div>
                                <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="product-price">Rp <?php echo number_format($product['discounted_price'], 0, ',', '.'); ?></div>
                                    <span class="product-status status-<?php echo $product['stock'] > 0 ? 'available' : 'soldout'; ?>">
                                        <?php echo $product['stock'] > 0 ? 'Tersedia' : 'Habis'; ?>
                                    </span>
                                </div>
                                <div class="text-muted small mb-2">
                                    <i class="bi bi-box me-1"></i>Stok: <?php echo $product['stock']; ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="alert('Edit produk')">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="if(confirm('Hapus produk?')) alert('Hapus')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 4rem; opacity: 0.3;"></i>
                    <p class="mt-3">Belum ada produk</p>
                    <button class="btn-red mt-2" onclick="alert('Tambah produk')">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Produk Pertama
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>