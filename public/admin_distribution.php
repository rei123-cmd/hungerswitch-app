<?php
// File: admin_distribution.php
require_once 'config.php';
if(session_status() === PHP_SESSION_NONE) { @session_start(); }

// 1. CEK APAKAH ADMIN
if(!isset($_SESSION['user_id'])) { header("Location: login_unified.php"); exit(); }
$uid = $_SESSION['user_id'];
$q_role = mysqli_query($conn, "SELECT role FROM users WHERE id=$uid");
$u = mysqli_fetch_assoc($q_role);

if($u['role'] !== 'admin') {
    die("AKSES DITOLAK: Halaman ini hanya untuk Admin Hungerswitch.");
}

// 2. AMBIL SALDO ADMIN
$admin_wallet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM wallets WHERE user_id = 1"));
$balance = $admin_wallet['balance'];

// 3. AMBIL DAFTAR AGEN
$agents = mysqli_query($conn, "SELECT u.id, u.name FROM users u WHERE role='agent'");

// 4. PROSES DISTRIBUSI (BELANJA MAKANAN)
if(isset($_POST['distribute'])) {
    $agent_id = $_POST['agent_id'];
    $menu = $_POST['menu'];
    $qty = (int)$_POST['qty'];
    $cost = (int)$_POST['cost'];

    if($cost > $balance) {
        echo "<script>alert('Saldo Donasi tidak cukup!');</script>";
    } else {
        mysqli_begin_transaction($conn);
        try {
            // A. Potong Saldo Admin
            $new_bal = $balance - $cost;
            mysqli_query($conn, "UPDATE wallets SET balance = $new_bal WHERE user_id = 1");
            
            // B. Catat Pengeluaran di Wallet Transaction
            $desc = "Distribusi ke Agen #$agent_id: $menu ($qty porsi)";
            mysqli_query($conn, "INSERT INTO wallet_transactions (wallet_id, transaction_type, amount, balance_after, description, status, created_at) VALUES ({$admin_wallet['id']}, 'distribution_out', $cost, $new_bal, '$desc', 'completed', NOW())");

            // C. Buat Tugas Distribusi untuk Agen
            $stmt = mysqli_prepare($conn, "INSERT INTO donation_distributions (admin_id, agent_id, program_name, quantity, total_cost, status, created_at) VALUES (1, ?, ?, ?, ?, 'pending', NOW())");
            mysqli_stmt_bind_param($stmt, "isid", $agent_id, $menu, $qty, $cost);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            header("Location: admin_distribution.php?success=1");
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Error: ".$e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Distribusi - Hungerswitch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color: var(--hs-cream); padding-top: 80px;">
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header p-4 text-white" style="background: var(--hs-green);">
                        <h4 class="mb-0">Pusat Distribusi Makanan</h4>
                        <small>Gunakan saldo donasi untuk mengirim bantuan ke Agen</small>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="alert alert-light border-success d-flex align-items-center shadow-sm">
                            <div class="me-3 p-3 rounded-circle bg-success text-white">
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                            <div>
                                <small class="text-muted">Saldo Donasi Tersedia</small>
                                <h2 class="mb-0 text-success fw-bold">Rp <?php echo number_format($balance); ?></h2>
                            </div>
                        </div>

                        <?php if(isset($_GET['success'])): ?>
                            <div class="alert alert-success">Berhasil mendistribusikan makanan ke Agen!</div>
                        <?php endif; ?>

                        <form method="POST" class="mt-4">
                            <div class="mb-3">
                                <label class="fw-bold">Pilih Agen Tujuan</label>
                                <select name="agent_id" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih Agen --</option>
                                    <?php while($a = mysqli_fetch_assoc($agents)): ?>
                                        <option value="<?php echo $a['id']; ?>"><?php echo $a['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Menu Makanan</label>
                                <input type="text" name="menu" class="form-control" placeholder="Contoh: Nasi Kotak Ayam Bakar" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Jumlah Porsi</label>
                                    <input type="number" name="qty" class="form-control" placeholder="100" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Total Biaya (Rp)</label>
                                    <input type="number" name="cost" class="form-control" placeholder="Nominal yang dibelanjakan" required>
                                </div>
                            </div>
                            <button type="submit" name="distribute" class="btn btn-danger w-100 py-3 fw-bold mt-3" style="background: var(--hs-red);">
                                PROSES DISTRIBUSI & KIRIM NOTIFIKASI
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>