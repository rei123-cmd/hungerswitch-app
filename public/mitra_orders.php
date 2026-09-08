<?php 
session_start();
include 'config.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user orders
$orders_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$orders_stmt = mysqli_prepare($conn, $orders_query);
mysqli_stmt_bind_param($orders_stmt, "i", $user_id);
mysqli_stmt_execute($orders_stmt);
$orders = mysqli_stmt_get_result($orders_stmt);

// Count orders by status
$stats_query = "SELECT 
                SUM(CASE WHEN payment_status = 'unpaid' THEN 1 ELSE 0 END) as unpaid,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                COUNT(*) as total
                FROM orders 
                WHERE user_id = ?";
$stats_stmt = mysqli_prepare($conn, $stats_query);
mysqli_stmt_bind_param($stats_stmt, "i", $user_id);
mysqli_stmt_execute($stats_stmt);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($stats_stmt));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Hungerswitch</title>
    
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--page-bg);
            color: var(--dark-text);
            padding-top: 80px;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
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
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover .brand-logo-icon {
            transform: scale(1.05) rotate(-5deg);
        }

        .btn-back {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(47, 82, 51, 0.3);
            color: white;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            padding: 3rem 0 2rem;
            margin-bottom: 3rem;
            border-radius: 0 0 30px 30px;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.95;
            position: relative;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff, var(--light-beige));
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(217, 35, 45, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 50px rgba(217, 35, 45, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
        }

        .stat-icon.red {
            background: linear-gradient(135deg, rgba(217, 35, 45, 0.15), rgba(255, 71, 87, 0.15));
            color: var(--main-red);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, rgba(47, 82, 51, 0.15), rgba(61, 107, 66, 0.15));
            color: var(--main-green);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, rgba(255, 152, 0, 0.15), rgba(255, 193, 7, 0.15));
            color: #FF9800;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.95rem;
            color: #666;
            font-weight: 500;
        }

        /* Order Cards */
        .order-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--main-red), #ff4757);
            transition: width 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(217, 35, 45, 0.15);
            border-color: rgba(217, 35, 45, 0.2);
        }

        .order-card:hover::before {
            width: 8px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-beige);
        }

        .order-id {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--dark-text);
        }

        .order-date {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .order-status {
            padding: 0.5rem 1.25rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-unpaid {
            background: linear-gradient(135deg, rgba(255, 152, 0, 0.15), rgba(255, 193, 7, 0.15));
            color: #FF9800;
            border: 2px solid rgba(255, 152, 0, 0.3);
        }

        .status-pending {
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(30, 136, 229, 0.15));
            color: #2196F3;
            border: 2px solid rgba(33, 150, 243, 0.3);
        }

        .status-completed {
            background: linear-gradient(135deg, rgba(47, 82, 51, 0.15), rgba(61, 107, 66, 0.15));
            color: var(--main-green);
            border: 2px solid rgba(47, 82, 51, 0.3);
        }

        .order-items {
            background: linear-gradient(135deg, var(--light-beige), #ffffff);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }

        .item-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .item-row:last-child {
            margin-bottom: 0;
        }

        .item-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--main-red);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.25rem;
        }

        .item-count {
            font-size: 0.85rem;
            color: #888;
        }

        .order-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(217, 35, 45, 0.05), rgba(255, 71, 87, 0.05));
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .total-label {
            font-weight: 600;
            color: #666;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .order-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-order {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-pay {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.3);
            flex: 1;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 35, 45, 0.4);
            color: white;
        }

        .btn-detail {
            background: transparent;
            color: var(--main-green);
            border: 2px solid var(--main-green);
        }

        .btn-detail:hover {
            background: var(--main-green);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .empty-icon {
            font-size: 6rem;
            color: #ddd;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 1rem;
        }

        .empty-text {
            color: #888;
            margin-bottom: 2rem;
        }

        .btn-shop {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 8px 25px rgba(47, 82, 51, 0.3);
            transition: all 0.3s ease;
        }

        .btn-shop:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(47, 82, 51, 0.4);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title { font-size: 2rem; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .order-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-logo-icon">HS</span>
                <div class="ms-2">
                    <strong>Hungerswitch</strong>
                    <div style="font-size: 0.75rem; color: #6c757d;">Selamatkan Makanan</div>
                </div>
            </a>
            <div class="d-flex gap-2">
                <a href="marketplace.php" class="btn-back">
                    <i class="bi bi-arrow-left me-2"></i>Belanja Lagi
                </a>
                <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-house-door"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><i class="bi bi-bag-check-fill me-3"></i>Pesanan Saya</h1>
            <p class="page-subtitle">Pantau status pesanan Anda</p>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
                <div class="stat-label">Total Pesanan</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div class="stat-value"><?php echo $stats['unpaid'] ?? 0; ?></div>
                <div class="stat-label">Belum Dibayar</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(30, 136, 229, 0.15)); color: #2196F3;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-value"><?php echo $stats['pending'] ?? 0; ?></div>
                <div class="stat-label">Sedang Diproses</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $stats['completed'] ?? 0; ?></div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>

        <!-- Orders List -->
        <?php if(mysqli_num_rows($orders) > 0): ?>
            <?php while($order = mysqli_fetch_assoc($orders)): 
                // Decode order data
                $items_data = json_decode(base64_decode($order['order_data']), true);
                $items = $items_data['items'] ?? [];
                $item_count = count($items);
                $first_item = $items[0] ?? ['name' => 'Paket Makanan'];
                
                // Determine status
                $status_class = 'pending';
                $status_text = 'Sedang Diproses';
                $show_pay = false;

                if ($order['payment_status'] == 'unpaid') {
                    $status_class = 'unpaid';
                    $status_text = 'Menunggu Pembayaran';
                    $show_pay = true;
                } elseif ($order['status'] == 'pending') {
                    $status_class = 'pending';
                    $status_text = 'Sedang Diproses';
                } elseif ($order['status'] == 'completed') {
                    $status_class = 'completed';
                    $status_text = 'Selesai';
                }
            ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
                        <div class="order-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?>
                        </div>
                    </div>
                    <span class="order-status status-<?php echo $status_class; ?>">
                        <?php echo $status_text; ?>
                    </span>
                </div>

                <div class="order-items">
                    <div class="item-row">
                        <div class="item-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="item-details">
                            <div class="item-name"><?php echo htmlspecialchars($first_item['name']); ?></div>
                            <div class="item-count">
                                <?php 
                                if($item_count > 1) {
                                    echo "+ " . ($item_count - 1) . " produk lainnya";
                                } else {
                                    echo "1 produk";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-total">
                    <span class="total-label">Total Pembayaran</span>
                    <span class="total-amount">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                </div>

                <div class="order-actions">
                    <?php if($show_pay): ?>
                        <a href="payment.php?order_id=<?php echo $order['order_id']; ?>" class="btn-order btn-pay">
                            <i class="bi bi-credit-card-fill"></i>
                            <span>Bayar Sekarang</span>
                        </a>
                    <?php else: ?>
                        <a href="order_success.php?order_id=<?php echo $order['order_id']; ?>" class="btn-order btn-detail">
                            <i class="bi bi-eye-fill"></i>
                            <span>Lihat Detail</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-bag-x empty-icon"></i>
                <h3 class="empty-title">Belum Ada Pesanan</h3>
                <p class="empty-text">Ayo mulai belanja dan selamatkan makanan surplus berkualitas!</p>
                <a href="marketplace.php" class="btn-shop">
                    <i class="bi bi-cart3 me-2"></i>Mulai Belanja
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>