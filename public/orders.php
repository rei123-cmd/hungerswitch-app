<?php 
//session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login_unified.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Ambil Data Pesanan
$q = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Pesanan Saya</h3>
            <a href="marketplace.php" class="btn btn-outline-primary">Belanja Lagi</a>
        </div>

        <?php if(mysqli_num_rows($q) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($q)): 
                // Coba decode JSON data produk
                $items_data = json_decode(base64_decode($row['order_data']), true);
                $item_name = isset($items_data['items'][0]['name']) ? $items_data['items'][0]['name'] : 'Paket Makanan';
                $count = isset($items_data['items']) ? count($items_data['items']) : 1;
                $more = $count > 1 ? "+ " . ($count-1) . " lainnya" : "";
                
                // Tentukan Status
                $status_text = $row['status'];
                $badge = "secondary";
                $show_pay = false;

                if ($row['payment_status'] == 'unpaid') {
                    $status_text = "Menunggu Pembayaran";
                    $badge = "warning text-dark";
                    $show_pay = true;
                } elseif ($row['status'] == 'pending') {
                    $status_text = "Sedang Diproses";
                    $badge = "info text-white";
                } elseif ($row['status'] == 'completed') {
                    $status_text = "Selesai";
                    $badge = "success";
                }
            ?>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold">Order #<?php echo $row['order_id']; ?></h5>
                        <span class="badge bg-<?php echo $badge; ?>"><?php echo $status_text; ?></span>
                    </div>
                    <p class="text-muted mb-1">
                        <?php echo date('d M Y H:i', strtotime($row['created_at'])); ?>
                    </p>
                    <div class="d-flex align-items-center bg-light p-2 rounded mb-3">
                        <i class="bi bi-bag-check fs-3 me-3 text-secondary"></i>
                        <div>
                            <strong><?php echo htmlspecialchars($item_name); ?> <?php echo $more; ?></strong>
                            <div class="small text-muted">Total: Rp <?php echo number_format($row['total_amount'], 0, ',', '.'); ?></div>
                        </div>
                    </div>

                    <?php if ($show_pay): ?>
                        <a href="payment.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-danger fw-bold w-100">
                            Bayar Sekarang
                        </a>
                    <?php else: ?>
                        <a href="order_success.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-outline-secondary w-100">
                            Lihat Detail
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">Belum ada pesanan.</div>
        <?php endif; ?>
    </div>
</body>
</html>