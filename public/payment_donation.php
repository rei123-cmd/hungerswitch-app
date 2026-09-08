<?php
// File: payment_donation.php
session_start();
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['amount'])) { header("Location: donasi.php"); exit(); }
$amount = (int)$_POST['amount'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #F6F4EB; font-family: 'Poppins', sans-serif; padding-top: 50px; }
        .slide-up { animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .pay-card {
            background: white; max-width: 480px; margin: auto; padding: 30px; border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.06);
        }
        .amount-display {
            background: #e8f5e9; color: #198754; padding: 20px; border-radius: 15px;
            font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 20px;
        }
        .method-item {
            border: 2px solid #f1f1f1; border-radius: 12px; padding: 15px; cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative; overflow: hidden;
        }
        .method-item:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .method-item.active { border-color: #198754; background: #f0fff4; }
        .method-item.active::after {
            content: '✔'; position: absolute; top: 10px; right: 15px;
            color: #198754; font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container slide-up">
        <div class="pay-card text-center">
            <h5 class="fw-bold text-muted mb-3">Konfirmasi Donasi</h5>
            <div class="amount-display">Rp <?php echo number_format($amount); ?></div>

            <p class="text-start fw-bold mb-2 small text-uppercase text-muted">Metode Pembayaran</p>
            
            <div class="method-item active mb-2" onclick="selectMe(this)">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-qr-code-scan fs-3 text-dark"></i>
                    <div class="text-start">
                        <h6 class="m-0 fw-bold">QRIS</h6>
                        <small class="text-muted">Gopay, OVO, Dana, ShopeePay</small>
                    </div>
                </div>
            </div>

            <div class="method-item mb-4" onclick="selectMe(this)">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-bank fs-3 text-dark"></i>
                    <div class="text-start">
                        <h6 class="m-0 fw-bold">Virtual Account</h6>
                        <small class="text-muted">BCA, Mandiri, BNI, BRI</small>
                    </div>
                </div>
            </div>

            <form action="process_donation.php" method="POST" id="payForm">
                <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                <button type="button" onclick="confirmPayment()" class="btn btn-dark w-100 py-3 rounded-4 fw-bold">
                    Bayar Sekarang <i class="bi bi-lightning-fill text-warning ms-1"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        function selectMe(el) {
            document.querySelectorAll('.method-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
        }

        function confirmPayment() {
            Swal.fire({
                title: 'Sudah Yakin?',
                text: "Anda akan mendonasikan Rp <?php echo number_format($amount); ?>",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                confirmButtonText: 'Ya, Lanjut Bayar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    let timerInterval;
                    Swal.fire({
                        title: 'Memproses Pembayaran...',
                        html: 'Mohon tunggu sejenak.',
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: () => { Swal.showLoading(); },
                        willClose: () => { document.getElementById('payForm').submit(); }
                    });
                }
            })
        }
    </script>
</body>
</html>