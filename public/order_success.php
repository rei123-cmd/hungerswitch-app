<?php
// File: order_success.php
include 'config.php'; // Otomatis start session & load security

// 1. Validasi Akses
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: marketplace.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
$user_id  = $_SESSION['user_id'];

// 2. Ambil Data Order dari Database
$query = "SELECT * FROM orders WHERE order_id = '$order_id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    die("Pesanan tidak ditemukan atau Anda tidak memiliki akses.");
}

// 3. Decode Data JSON (Daftar makanan & harga)
$order_data = json_decode(base64_decode($order['order_data']), true);
$items = $order_data['items'];
$resto_name = $items[0]['restaurant'] ?? 'Mitra Restoran';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Hungerswitch</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #F6F4EB;
            font-family: 'Poppins', sans-serif;
            padding-bottom: 50px;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            margin-top: 30px;
            animation: slideUp 0.5s ease-out;
        }
        .header-success {
            background: linear-gradient(135deg, #2F5233, #4a7c50);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .check-icon {
            font-size: 60px;
            background: white;
            color: #2F5233;
            width: 100px;
            height: 100px;
            line-height: 100px;
            border-radius: 50%;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .pickup-box {
            background-color: #FFF7F0;
            border: 2px dashed #D9232D;
            border-radius: 12px;
            padding: 20px;
            margin: 20px;
            text-align: center;
        }
        .item-list {
            padding: 0 25px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .total-section {
            background-color: #f8f9fa;
            padding: 20px 25px;
            border-top: 1px dashed #ccc;
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="success-card">
                <div class="header-success">
                    <div class="check-icon">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <h2 class="fw-bold">Pembayaran Berhasil!</h2>
                    <p class="mb-0 opacity-75">Terima kasih telah menyelamatkan makanan.</p>
                </div>

                <div class="pickup-box">
                    <h6 class="text-danger fw-bold text-uppercase mb-2">
                        <i class="bi bi-geo-alt-fill me-1"></i> Lokasi Pengambilan
                    </h6>
                    <h3 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($resto_name); ?></h3>
                    <p class="text-muted small mb-3">Tunjukkan Kode Order ini ke kasir:</p>
                    
                    <div class="bg-white border rounded p-2 d-inline-block shadow-sm">
                        <h4 class="fw-bold mb-0 letter-spacing-2"><?php echo $order_id; ?></h4>
                    </div>
                    
                    <div class="mt-3">
                        <span class="badge bg-warning text-dark">Status: Sedang Disiapkan</span>
                    </div>
                </div>

                <div class="item-list mt-3">
                    <h6 class="fw-bold border-bottom pb-2 mb-0">Rincian Pesanan</h6>
                    
                    <?php foreach ($items as $item): ?>
                    <div class="item-row">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary rounded-pill"><?php echo $item['quantity']; ?>x</span>
                            <div>
                                <div class="fw-medium"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="text-muted small">Catatan: -</div>
                            </div>
                        </div>
                        <div class="fw-bold">
                            Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="total-section mt-3">
                    <div class="d-flex justify-content-between mb-1 text-muted small">
                        <span>Subtotal</span>
                        <span>Rp <?php echo number_format($order_data['pricing']['subtotal'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 text-muted small">
                        <span>Biaya Layanan</span>
                        <span>Rp <?php echo number_format($order_data['pricing']['service_fee'] ?? 2000, 0, ',', '.'); ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Total Bayar</span>
                        <span class="fw-bold fs-4 text-success">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="text-end text-muted small mt-1">
                        Dibayar menggunakan <i class="bi bi-wallet2"></i> Wallet
                    </div>
                </div>

                <div class="p-4 pt-2">
                    <a href="marketplace.php" class="btn btn-success w-100 py-3 fw-bold mb-2 rounded-3">
                        Belanja Makanan Lain
                    </a>
                    <a href="orders.php" class="btn btn-outline-secondary w-100 py-2 fw-bold rounded-3">
                        Lihat Riwayat Pesanan
                    </a>
                </div>

            </div>
            
            <div class="text-center mt-4 mb-5 text-muted small">
                &copy; 2025 Hungerswitch. ID Transaksi: #<?php echo rand(100000, 999999); ?>
            </div>

        </div>
    </div>
</div>

</body>
</html>