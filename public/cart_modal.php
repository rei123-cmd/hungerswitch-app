<?php
// File: cart_modal.php
require_once 'config.php'; // Session & Security otomatis jalan

// === LOGIC HAPUS & UPDATE TETAP SAMA ===
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
    SecurityHelper::setSecureCookie('hs_cart_backup', json_encode($_SESSION['cart']), 7);
    header("Location: cart_modal.php"); exit;
}
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id'])) {
    $id = $_GET['id']; $mode = $_GET['mode'];
    if (isset($_SESSION['cart'][$id])) {
        if ($mode == 'plus') $_SESSION['cart'][$id]['quantity']++;
        elseif ($mode == 'minus') {
            $_SESSION['cart'][$id]['quantity']--;
            if ($_SESSION['cart'][$id]['quantity'] < 1) unset($_SESSION['cart'][$id]);
        }
    }
    SecurityHelper::setSecureCookie('hs_cart_backup', json_encode($_SESSION['cart']), 7);
    header("Location: cart_modal.php"); exit;
}

// Hitung Total
$grandTotal = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) $grandTotal += ($item['price'] * $item['quantity']);
}
$serviceFee = 2000;
$finalTotal = $grandTotal + $serviceFee;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #FCFCF8; padding-top: 20px; }
        .cart-item { background: white; border-radius: 12px; padding: 10px; border: 1px solid #eee; margin-bottom: 10px; }
        .btn-checkout { background: #2F5233; color: white; width: 100%; border-radius: 12px; padding: 12px; font-weight: bold; border:none; transition: 0.3s; }
        .btn-checkout:hover { background: #1e3621; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container pb-5">
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <h5 class="mt-3">Keranjang Kosong</h5>
                <a href="marketplace.php" class="btn btn-outline-success mt-2">Mulai Jajan</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <div class="cart-item d-flex align-items-center gap-3">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" style="width:70px; height:70px; object-fit:cover; border-radius:8px;">
                            <div class="flex-grow-1">
                                <h6 class="m-0 fw-bold"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-muted"><i class="bi bi-shop me-1"></i><?php echo htmlspecialchars($item['restaurant']); ?></small>
                                <div class="text-danger fw-bold">Rp <?php echo number_format($item['price']); ?></div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="?action=update&mode=minus&id=<?php echo $id; ?>" class="btn btn-sm btn-light border">-</a>
                                <span class="fw-bold"><?php echo $item['quantity']; ?></span>
                                <a href="?action=update&mode=plus&id=<?php echo $id; ?>" class="btn btn-sm btn-light border">+</a>
                                <a href="?action=delete&id=<?php echo $id; ?>" class="text-danger ms-2"><i class="bi bi-trash"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 rounded-4">
                        <h5 class="fw-bold">Ringkasan</h5>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Subtotal</span><span>Rp <?php echo number_format($grandTotal); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Biaya Layanan</span><span>Rp <?php echo number_format($serviceFee); ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4 fs-5 fw-bold">
                            <span>Total</span><span class="text-success">Rp <?php echo number_format($finalTotal); ?></span>
                        </div>
                        
                        <form action="process_checkout.php" method="POST">
                            <div class="alert alert-warning py-2 small">
                                <i class="bi bi-info-circle me-1"></i> Metode: <b>Ambil di Resto</b>
                            </div>
                            <button type="submit" class="btn-checkout">
                                Bayar Sekarang <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>