<?php
// File: user_donation.php
session_start();
include 'config.php';

// Cek Login
if(!isset($_SESSION['user_id'])) {
    header("Location: login_unified.php");
    exit();
}

$success_msg = "";

if(isset($_POST['donate'])) {
    $user_id = $_SESSION['user_id'];
    $amount = (int)$_POST['amount'];
    
    if($amount >= 5000) {
        mysqli_begin_transaction($conn);
        try {
            // 1. Uang Masuk ke Dompet Admin (Asumsi ID Admin = 1)
            $admin_wallet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, balance FROM wallets WHERE user_id = 1"));
            
            // Jika Admin belum punya wallet, script akan error. Pastikan SQL di langkah 1 dijalankan.
            if(!$admin_wallet) throw new Exception("Sistem Donasi sedang maintenance (Admin Wallet not found).");

            $new_balance = $admin_wallet['balance'] + $amount;
            
            // Update Saldo Admin
            mysqli_query($conn, "UPDATE wallets SET balance = $new_balance WHERE id = {$admin_wallet['id']}");

            // Catat Transaksi Masuk
            $desc = "Donasi dari User #$user_id";
            $stmt = mysqli_prepare($conn, "INSERT INTO wallet_transactions (wallet_id, transaction_type, amount, balance_after, description, status, created_at) VALUES (?, 'donation_in', ?, ?, ?, 'completed', NOW())");
            mysqli_stmt_bind_param($stmt, "idds", $admin_wallet['id'], $amount, $new_balance, $desc);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            $success_msg = "Alhamdulillah! Donasi Rp ".number_format($amount)." berhasil dikirim.";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $success_msg = "Gagal: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Salurkan Kebaikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #FCFCF8; font-family: 'Poppins', sans-serif; padding-top: 50px; }
        .donate-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 500px; margin: auto; }
        .btn-donate { background: #2F5233; color: white; border-radius: 12px; padding: 15px; font-weight: bold; width: 100%; border:none; }
        .btn-donate:hover { background: #1e3621; }
    </style>
</head>
<body>
    <div class="container">
        <?php if(!empty($success_msg)): ?>
            <script>Swal.fire('Terima Kasih', '<?php echo $success_msg; ?>', 'success');</script>
        <?php endif; ?>

        <div class="donate-card text-center">
            <i class="bi bi-heart-fill text-danger" style="font-size: 4rem;"></i>
            <h2 class="fw-bold mt-3">Mari Berbagi Makanan</h2>
            <p class="text-muted">Donasi Anda akan kami gunakan untuk membeli makanan dan dibagikan oleh Agen kami.</p>
            
            <form method="POST" class="mt-4 text-start">
                <label class="fw-bold mb-2">Nominal Donasi (Rp)</label>
                <input type="number" name="amount" class="form-control form-control-lg mb-3" placeholder="Minimal 5.000" min="5000" required>
                <button type="submit" name="donate" class="btn-donate">
                    <i class="bi bi-gift-fill me-2"></i>Kirim Donasi Sekarang
                </button>
            </form>
            <div class="mt-3 text-center">
                <a href="index.php" class="text-muted text-decoration-none">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>