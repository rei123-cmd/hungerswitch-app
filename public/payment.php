<?php
// File: payment.php
include 'config.php';

if (!isset($_GET['order_id'])) header("Location: index.php");
$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Ambil data order
$q = mysqli_query($conn, "SELECT * FROM orders WHERE order_id='$order_id' AND user_id='$user_id'");
$order = mysqli_fetch_assoc($q);

if (!$order) die("Order tidak ditemukan.");
if ($order['payment_status'] == 'paid') header("Location: order_success.php?order_id=$order_id");

$total = $order['total_amount'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { background: #F6F4EB; font-family: 'Poppins', sans-serif; padding-top: 50px; }
        .pay-card { background: white; max-width: 450px; margin: auto; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .total-display { font-size: 2.5rem; font-weight: 700; color: #2F5233; margin: 10px 0; }
        .method-box { border: 2px solid #eee; border-radius: 12px; padding: 15px; cursor: pointer; transition: 0.3s; margin-bottom: 10px; }
        .method-box:hover, .method-box.active { border-color: #2F5233; background: #f0fdf4; }
    </style>
</head>
<body>
    <div class="container">
        <div class="pay-card text-center">
            <h4 class="fw-bold">Total Pembayaran</h4>
            <div class="total-display">Rp <?php echo number_format($total); ?></div>
            <p class="text-muted mb-4">Order ID: #<?php echo $order_id; ?></p>

            <div class="text-start mb-3">
                <label class="fw-bold mb-2">Pilih Metode Pembayaran:</label>
                <div class="method-box active" onclick="selectMethod(this)">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-qr-code-scan fs-3 text-success"></i>
                        <div>
                            <h6 class="m-0 fw-bold">QRIS / E-Wallet</h6>
                            <small class="text-muted">Scan & Bayar Instan</small>
                        </div>
                    </div>
                </div>
                
                <div class="method-box" onclick="selectMethod(this)">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-cash-coin fs-3 text-warning"></i>
                        <div>
                            <h6 class="m-0 fw-bold">Tunai di Resto</h6>
                            <small class="text-muted">Bayar saat ambil makanan</small>
                        </div>
                    </div>
                </div>
            </div>

            <form action="pay_action.php" method="POST" id="payForm">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <button type="button" onclick="confirmPay()" class="btn btn-success w-100 py-3 fw-bold rounded-3">
                    BAYAR SEKARANG
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function selectMethod(el) {
            document.querySelectorAll('.method-box').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
        }

        function confirmPay() {
            Swal.fire({
                title: 'Konfirmasi Bayar?',
                text: "Pastikan nominal sudah sesuai!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2F5233',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Bayar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan Loading
                    Swal.fire({
                        title: 'Memproses...',
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: () => { Swal.showLoading() }
                    }).then(() => {
                        document.getElementById('payForm').submit();
                    });
                }
            })
        }
    </script>
</body>
</html>