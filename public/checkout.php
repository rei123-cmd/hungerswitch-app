<?php 
// File: checkout.php
//session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])) { header("Location: login_unified.php"); exit(); }
if(empty($_SESSION['cart'])) { header("Location: marketplace.php"); exit(); }

$uid = $_SESSION['user_id'];
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $uid"));

// Hitung Total
$subtotal = 0;
foreach($_SESSION['cart'] as $item) { $subtotal += ($item['price'] * $item['quantity']); }
$grand_total = $subtotal + 2000 + 10000;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 800px;">
        <h2 class="fw-bold mb-4">Checkout</h2>
        
        <form action="process_checkout.php" method="POST">
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Alamat Pengiriman</h5>
                    <textarea name="address" class="form-control mb-2" rows="3" required><?php echo htmlspecialchars($u['address'] ?? ''); ?></textarea>
                    <input type="text" name="notes" class="form-control" placeholder="Catatan (Opsional)">
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Ringkasan Pesanan</h5>
                    <?php foreach($_SESSION['cart'] as $item): ?>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span><?php echo $item['name']; ?> (x<?php echo $item['quantity']; ?>)</span>
                        <span>Rp <?php echo number_format($item['price'] * $item['quantity']); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between mt-3 fw-bold fs-5">
                        <span>Total Bayar</span>
                        <span class="text-danger">Rp <?php echo number_format($grand_total); ?></span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5">
                LANJUT KE PEMBAYARAN
            </button>
        </form>
    </div>
</body>
</html>