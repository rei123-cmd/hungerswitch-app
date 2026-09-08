<?php
// File: founder_finance.php - KEUANGAN FOUNDER
session_start();
include 'config.php';

// Cek Login Founder
if(!isset($_SESSION['user_id']) || !in_array('founder', $_SESSION['user_roles'] ?? [])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil Data Keuangan
$donation_fund = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM donation_fund WHERE id = 1"));

// Recent Transactions
$recent_transactions = mysqli_query($conn, "
    SELECT dt.*, u.name as processed_by_name 
    FROM donation_transactions dt 
    LEFT JOIN users u ON dt.processed_by = u.id 
    ORDER BY dt.created_at DESC 
    LIMIT 20
");

// Monthly Stats
$monthly_stats = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        transaction_type,
        SUM(amount) as total
    FROM donation_transactions
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m'), transaction_type
    ORDER BY month DESC
");

// Marketplace Revenue
$marketplace_revenue = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(paid_at, '%Y-%m') as month,
        COUNT(*) as total_orders,
        SUM(total_amount) as revenue
    FROM orders
    WHERE payment_status = 'paid' AND order_source = 'marketplace'
    AND paid_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
    ORDER BY month DESC
");

// Top Donors
$top_donors = mysqli_query($conn, "
    SELECT u.name, SUM(ud.amount) as total_donated, COUNT(*) as donation_count
    FROM user_donations ud
    LEFT JOIN users u ON ud.user_id = u.id
    WHERE ud.status = 'paid'
    GROUP BY ud.user_id
    ORDER BY total_donated DESC
    LIMIT 5
");

// Donation by Month
$donation_by_month = [];
$result = mysqli_query($conn, "
    SELECT DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(amount) as total
    FROM user_donations
    WHERE status = 'paid' AND paid_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
    ORDER BY month ASC
");
while($row = mysqli_fetch_assoc($result)) {
    $donation_by_month[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Keuangan - Hungerswitch</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
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
        
        .finance-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .finance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--hs-green), var(--hs-red));
        }
        
        .finance-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .balance-display {
            background: linear-gradient(135deg, var(--hs-green), #3d6b42);
            border-radius: 20px;
            padding: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(47, 82, 51, 0.3);
        }
        
        .balance-display::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .balance-amount {
            font-size: 3rem;
            font-weight: 800;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.2);
        }
        
        .transaction-item {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
            transition: 0.3s;
            cursor: pointer;
        }
        
        .transaction-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .transaction-item.income {
            border-left-color: var(--hs-green);
            background: linear-gradient(to right, rgba(47, 82, 51, 0.05), white);
        }
        
        .transaction-item.distribution {
            border-left-color: var(--hs-red);
            background: linear-gradient(to right, rgba(217, 35, 45, 0.05), white);
        }
        
        .transaction-item.operational {
            border-left-color: #FF9800;
            background: linear-gradient(to right, rgba(255, 152, 0, 0.05), white);
        }
        
        .chart-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            height: 400px;
        }
        
        .donor-badge {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .stat-mini {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            transition: 0.3s;
        }
        
        .stat-mini:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        
        .stat-mini-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--hs-green);
        }
        
        .stat-mini-label {
            color: #666;
            font-size: 0.85rem;
            margin-top: 0.5rem;
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
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
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
            <li><a href="founder_finance.php" class="active"><i class="bi bi-wallet2 me-2"></i>Keuangan</a></li>
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
                <h2 class="fw-bold mb-1">Manajemen Keuangan</h2>
                <p class="text-muted mb-0">Monitor dana donasi dan transaksi finansial</p>
            </div>
            <div>
                <button class="btn btn-success me-2">
                    <i class="bi bi-download me-1"></i>Export Laporan
                </button>
                <span class="badge bg-success px-3 py-2">
                    <i class="bi bi-clock me-1"></i><?php echo date('d M Y, H:i'); ?>
                </span>
            </div>
        </div>

        <!-- Balance Overview -->
        <div class="row g-4 mb-4 animate-in">
            <div class="col-md-6">
                <div class="balance-display">
                    <div class="position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="mb-2 opacity-75">Saldo Dana Donasi</p>
                                <div class="balance-amount">
                                    Rp <?php echo number_format($donation_fund['current_balance'], 0, ',', '.'); ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <i class="bi bi-cash-stack" style="font-size: 3rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <small class="opacity-75">Total Diterima</small>
                                <div class="h5 mb-0">Rp <?php echo number_format($donation_fund['total_received'], 0, ',', '.'); ?></div>
                            </div>
                            <div class="col-6">
                                <small class="opacity-75">Total Tersalurkan</small>
                                <div class="h5 mb-0">Rp <?php echo number_format($donation_fund['total_distributed'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-mini">
                            <i class="bi bi-arrow-down-circle text-success" style="font-size: 2rem;"></i>
                            <div class="stat-mini-value text-success">
                                <?php 
                                $total_income = mysqli_fetch_assoc(mysqli_query($conn, 
                                    "SELECT SUM(amount) as total FROM donation_transactions WHERE transaction_type = 'income'"
                                ))['total'] ?? 0;
                                echo number_format($total_income, 0, ',', '.');
                                ?>
                            </div>
                            <div class="stat-mini-label">Total Pemasukan</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-mini">
                            <i class="bi bi-arrow-up-circle text-danger" style="font-size: 2rem;"></i>
                            <div class="stat-mini-value text-danger">
                                <?php 
                                $total_distribution = mysqli_fetch_assoc(mysqli_query($conn, 
                                    "SELECT SUM(amount) as total FROM donation_transactions WHERE transaction_type = 'distribution'"
                                ))['total'] ?? 0;
                                echo number_format($total_distribution, 0, ',', '.');
                                ?>
                            </div>
                            <div class="stat-mini-label">Total Distribusi</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-mini">
                            <i class="bi bi-graph-up-arrow text-primary" style="font-size: 2rem;"></i>
                            <div class="stat-mini-value text-primary">
                                <?php 
                                $marketplace_total = mysqli_fetch_assoc(mysqli_query($conn, 
                                    "SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid' AND order_source = 'marketplace'"
                                ))['total'] ?? 0;
                                echo number_format($marketplace_total, 0, ',', '.');
                                ?>
                            </div>
                            <div class="stat-mini-label">Revenue Marketplace</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-mini">
                            <i class="bi bi-people text-warning" style="font-size: 2rem;"></i>
                            <div class="stat-mini-value text-warning">
                                <?php 
                                $total_donors = mysqli_fetch_assoc(mysqli_query($conn, 
                                    "SELECT COUNT(DISTINCT user_id) as total FROM user_donations WHERE status = 'paid'"
                                ))['total'] ?? 0;
                                echo $total_donors;
                                ?>
                            </div>
                            <div class="stat-mini-label">Total Donatur</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="chart-container">
                    <h5 class="fw-bold mb-3">Tren Donasi 6 Bulan Terakhir</h5>
                    <canvas id="donationChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="finance-card" style="height: 400px; overflow-y: auto;">
                    <h5 class="fw-bold mb-3">Top Donatur</h5>
                    <?php if(mysqli_num_rows($top_donors) > 0): ?>
                        <?php $rank = 1; while($donor = mysqli_fetch_assoc($top_donors)): ?>
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--hs-green), var(--hs-red)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800;">
                                        <?php echo $rank++; ?>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($donor['name'] ?? 'Hamba Allah'); ?></h6>
                                    <small class="text-muted"><?php echo $donor['donation_count']; ?> donasi</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">
                                    Rp <?php echo number_format($donor['total_donated'], 0, ',', '.'); ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
                            <p class="text-muted mt-2">Belum ada donatur</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="finance-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Transaksi Terbaru</h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary active">Semua</button>
                    <button class="btn btn-sm btn-outline-success">Pemasukan</button>
                    <button class="btn btn-sm btn-outline-danger">Distribusi</button>
                    <button class="btn btn-sm btn-outline-warning">Operasional</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($recent_transactions) > 0): ?>
                            <?php while($trans = mysqli_fetch_assoc($recent_transactions)): ?>
                            <tr>
                                <td>
                                    <small><?php echo date('d M Y, H:i', strtotime($trans['created_at'])); ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $type_badge = [
                                        'income' => ['class' => 'success', 'icon' => 'arrow-down-circle', 'label' => 'Pemasukan'],
                                        'distribution' => ['class' => 'danger', 'icon' => 'arrow-up-circle', 'label' => 'Distribusi'],
                                        'operational' => ['class' => 'warning', 'icon' => 'gear', 'label' => 'Operasional']
                                    ];
                                    $badge = $type_badge[$trans['transaction_type']];
                                    ?>
                                    <span class="badge bg-<?php echo $badge['class']; ?>">
                                        <i class="bi bi-<?php echo $badge['icon']; ?> me-1"></i>
                                        <?php echo $badge['label']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($trans['description'] ?? '-'); ?>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-<?php echo $trans['transaction_type'] == 'income' ? 'success' : 'danger'; ?>">
                                        <?php echo $trans['transaction_type'] == 'income' ? '+' : '-'; ?>
                                        Rp <?php echo number_format($trans['amount'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <small class="text-muted">
                                        Rp <?php echo number_format($trans['balance_after'], 0, ',', '.'); ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
                                    <p class="text-muted mt-2">Belum ada transaksi</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Donation Chart
        const ctx = document.getElementById('donationChart').getContext('2d');
        const donationData = <?php echo json_encode($donation_by_month); ?>;
        
        const labels = donationData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
        });
        
        const data = donationData.map(item => parseFloat(item.total));
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Donasi',
                    data: data,
                    borderColor: '#2F5233',
                    backgroundColor: 'rgba(47, 82, 51, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#2F5233',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000) + 'K';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>