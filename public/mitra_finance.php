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

// Calculate financial stats
$finance_query = "SELECT 
                  COUNT(DISTINCT o.id) as total_transactions,
                  SUM(oi.quantity * oi.price) as total_revenue,
                  AVG(oi.quantity * oi.price) as avg_transaction
                  FROM orders o 
                  JOIN order_items oi ON o.id = oi.order_id 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE p.partner_id = ? AND o.status = 'completed'";
$finance_stmt = mysqli_prepare($conn, $finance_query);
mysqli_stmt_bind_param($finance_stmt, "i", $user_id);
mysqli_stmt_execute($finance_stmt);
$finance = mysqli_fetch_assoc(mysqli_stmt_get_result($finance_stmt));

$total_revenue = $finance['total_revenue'] ?? 0;
$total_transactions = $finance['total_transactions'] ?? 0;
$avg_transaction = $finance['avg_transaction'] ?? 0;

// Get monthly revenue (last 6 months)
$monthly_query = "SELECT 
                  DATE_FORMAT(o.created_at, '%Y-%m') as month,
                  SUM(oi.quantity * oi.price) as revenue
                  FROM orders o 
                  JOIN order_items oi ON o.id = oi.order_id 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE p.partner_id = ? AND o.status = 'completed'
                  AND o.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                  GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
                  ORDER BY month DESC";
$monthly_stmt = mysqli_prepare($conn, $monthly_query);
mysqli_stmt_bind_param($monthly_stmt, "i", $user_id);
mysqli_stmt_execute($monthly_stmt);
$monthly_data = mysqli_stmt_get_result($monthly_stmt);

// Recent transactions
$transactions_query = "SELECT o.*, u.name as customer_name,
                       SUM(oi.quantity * oi.price) as amount
                       FROM orders o 
                       JOIN order_items oi ON o.id = oi.order_id 
                       JOIN products p ON oi.product_id = p.id 
                       JOIN users u ON o.user_id = u.id
                       WHERE p.partner_id = ? AND o.status = 'completed'
                       GROUP BY o.id
                       ORDER BY o.created_at DESC
                       LIMIT 10";
$transactions_stmt = mysqli_prepare($conn, $transactions_query);
mysqli_stmt_bind_param($transactions_stmt, "i", $user_id);
mysqli_stmt_execute($transactions_stmt);
$transactions = mysqli_stmt_get_result($transactions_stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - Dashboard Mitra</title>
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
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white; 
            padding: 2rem; 
            border-radius: 16px; 
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(47, 82, 51, 0.3);
        }
        .page-header h1 { 
            font-size: 2rem; 
            font-weight: 700; 
            margin-bottom: 0.5rem; 
        }
        
        .stats-row { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 1.5rem; 
            margin-bottom: 2rem; 
        }
        .stat-box { 
            background: white; 
            padding: 2rem; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            transition: transform 0.3s;
            border: 2px solid rgba(217, 35, 45, 0.05);
        }
        .stat-box:hover { 
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(217, 35, 45, 0.15);
        }
        .stat-icon { 
            width: 60px; 
            height: 60px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.8rem; 
            margin-bottom: 1rem; 
        }
        .icon-red { 
            background: rgba(217, 35, 45, 0.1); 
            color: var(--main-red); 
        }
        .icon-green { 
            background: rgba(47, 82, 51, 0.1); 
            color: var(--main-green); 
        }
        .icon-blue { 
            background: rgba(33, 150, 243, 0.1); 
            color: #2196F3; 
        }
        .stat-value { 
            font-size: 2rem; 
            font-weight: 700; 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem; 
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
        
        .month-card { 
            background: linear-gradient(135deg, var(--info-box-bg), white); 
            padding: 1rem; 
            border-radius: 8px; 
            border-left: 4px solid var(--main-red); 
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }
        .month-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.1);
        }
        .month-name { 
            font-weight: 600; 
            color: var(--dark-text); 
        }
        .month-revenue { 
            font-size: 1.3rem; 
            font-weight: 700; 
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .transaction-row { 
            display: grid; 
            grid-template-columns: 80px 2fr 1fr 1fr 120px; 
            gap: 1rem; 
            padding: 1rem; 
            border: 1px solid #e0e0e0; 
            border-radius: 8px; 
            margin-bottom: 0.5rem; 
            align-items: center;
            transition: all 0.3s;
        }
        .transaction-row:hover { 
            background: var(--info-box-bg);
            border-color: rgba(217, 35, 45, 0.2);
        }
        
        .btn-export { 
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white; 
            border: none; 
            padding: 0.5rem 1.5rem; 
            border-radius: 8px; 
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(47, 82, 51, 0.3);
            transition: all 0.3s;
        }
        .btn-export:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(47, 82, 51, 0.4);
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
            <h1><i class="bi bi-wallet2 me-2"></i>Laporan Keuangan</h1>
            <p>Pantau pendapatan dan transaksi bisnis Anda</p>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-icon icon-red">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-icon icon-green">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value"><?php echo $total_transactions; ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-icon icon-blue">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stat-label">Rata-rata Transaksi</div>
                <div class="stat-value">Rp <?php echo number_format($avg_transaction, 0, ',', '.'); ?></div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header-custom">
                <h3 class="mb-0">Pendapatan Bulanan (6 Bulan Terakhir)</h3>
                <button class="btn-export" onclick="alert('Fitur export akan segera hadir!')">
                    <i class="bi bi-download me-2"></i>Export Laporan
                </button>
            </div>

            <?php if(mysqli_num_rows($monthly_data) > 0): ?>
                <?php while($month = mysqli_fetch_assoc($monthly_data)): ?>
                    <div class="month-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="month-name">
                                    <?php 
                                    $date = DateTime::createFromFormat('Y-m', $month['month']);
                                    echo $date->format('F Y'); 
                                    ?>
                                </div>
                            </div>
                            <div class="month-revenue">
                                Rp <?php echo number_format($month['revenue'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-graph-up" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-2">Belum ada data pendapatan</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="content-card">
            <h3 class="mb-4">Riwayat Transaksi Terbaru</h3>

            <?php if(mysqli_num_rows($transactions) > 0): ?>
                <div style="overflow-x: auto;">
                    <?php while($trans = mysqli_fetch_assoc($transactions)): ?>
                        <div class="transaction-row">
                            <div class="text-center">
                                <div class="fw-bold">#<?php echo str_pad($trans['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            </div>
                            <div>
                                <div class="fw-semibold"><?php echo htmlspecialchars($trans['customer_name']); ?></div>
                                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($trans['created_at'])); ?></small>
                            </div>
                            <div>
                                <span class="badge bg-success">Completed</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="color: var(--main-red)">Rp <?php echo number_format($trans['amount'], 0, ',', '.'); ?></div>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="alert('Detail transaksi')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-receipt" style="font-size: 4rem; opacity: 0.3;"></i>
                    <p class="mt-3">Belum ada transaksi</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="content-card">
            <h4 class="mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Pembayaran</h4>
            <div class="alert" style="background: var(--info-box-bg); border-left: 4px solid var(--main-red);">
                <p class="mb-2"><strong>💡 Catatan Penting:</strong></p>
                <ul class="mb-0">
                    <li>Pembayaran akan diproses setiap akhir minggu</li>
                    <li>Dana akan ditransfer ke rekening terdaftar</li>
                    <li>Pastikan data rekening Anda sudah benar</li>
                    <li>Laporan detail dapat diunduh dalam format PDF/Excel</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>