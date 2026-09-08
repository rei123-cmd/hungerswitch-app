<?php
require_once 'config.php';

// Cek login
if(!isset($_SESSION['user_id'])) {
    header("Location: login_unified.php");
    exit();
}

// Cek CSRF token
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_POST['csrf_token']) || !SecurityHelper::verifyCSRFToken($_POST['csrf_token'])) {
        die("CSRF Token invalid!");
    }
}

// Cek keranjang
if(empty($_SESSION['cart'])) {
    header("Location: marketplace.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user dan wallet
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$wallet_query = "SELECT * FROM wallets WHERE user_id = ?";
$wallet_stmt = mysqli_prepare($conn, $wallet_query);
mysqli_stmt_bind_param($wallet_stmt, "i", $user_id);
mysqli_stmt_execute($wallet_stmt);
$wallet_result = mysqli_stmt_get_result($wallet_stmt);
$wallet = mysqli_fetch_assoc($wallet_result);
$wallet_balance = $wallet ? $wallet['balance'] : 0;

// Hitung total
$subtotal = 0;
foreach($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$serviceFee = 2000;
$total = $subtotal + $serviceFee;

// Cek saldo cukup atau tidak
$saldo_cukup = ($wallet_balance >= $total);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran - Hungerswitch</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #FCFCF8 0%, #f8f6f1 100%);
            padding-top: 100px;
            padding-bottom: 50px;
        }
        
        .payment-card {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .total-amount {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #D9232D, #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .wallet-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 1.5rem;
            border-radius: 15px;
            margin: 2rem 0;
        }
        
        .btn-pay {
            background: linear-gradient(135deg, #2F5233, #3d6b42);
            color: white;
            border: none;
            padding: 1.25rem;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-pay:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(47, 82, 51, 0.3);
        }
        
        .btn-topup {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
        }
        
        .btn-topup:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 152, 0, 0.3);
        }
    </style>
</head>
<body>

    <nav class="navbar fixed-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="cart_modal.php">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
            <span class="fw-bold">Konfirmasi Pembayaran</span>
        </div>
    </nav>

    <div class="container">
        <div class="payment-card">
            <div class="text-center mb-4">
                <i class="bi bi-cash-coin" style="font-size: 4rem; color: #2F5233;"></i>
                <h2 class="fw-bold mt-3">Konfirmasi Pembayaran</h2>
                <p class="text-muted">Periksa detail pesanan Anda</p>
            </div>
            
            <div class="text-center mb-4">
                <p class="text-muted mb-2">Total Pembayaran</p>
                <div class="total-amount">Rp <?php echo number_format($total, 0, ',', '.'); ?></div>
            </div>
            
            <div class="wallet-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Saldo Dompet Anda</h6>
                        <p class="text-muted small mb-0">Hungerswitch Wallet</p>
                    </div>
                    <div class="text-end">
                        <h5 class="fw-bold mb-0 <?php echo $saldo_cukup ? 'text-success' : 'text-danger'; ?>">
                            Rp <?php echo number_format($wallet_balance, 0, ',', '.'); ?>
                        </h5>
                    </div>
                </div>
            </div>
            
            <?php if(!$saldo_cukup): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Saldo tidak cukup!</strong><br>
                    Anda membutuhkan Rp <?php echo number_format($total - $wallet_balance, 0, ',', '.'); ?> lagi.
                </div>
                
                <a href="wallet.php" class="btn btn-pay btn-topup">
                    <i class="bi bi-wallet2 me-2"></i>Top Up Saldo
                </a>
            <?php else: ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    Saldo Anda cukup untuk pembayaran ini
                </div>
                
                <div class="border-top pt-3 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Biaya Layanan</span>
                        <span>Rp <?php echo number_format($serviceFee, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                </div>
                
                <form action="process_payment.php" method="POST" id="paymentForm">
                    <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::generateCSRFToken(); ?>">
                    
                    <button type="submit" class="btn btn-pay" id="btnPay">
                        <i class="bi bi-lock-fill me-2"></i>Bayar Sekarang
                    </button>
                </form>
            <?php endif; ?>
            
            <p class="text-center text-muted small mt-3 mb-0">
                <i class="bi bi-shield-check me-1"></i>
                Transaksi Anda aman dan terenkripsi
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnPay');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        });
    </script>
</body>
</html>