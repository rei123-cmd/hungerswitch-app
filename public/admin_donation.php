<?php
// File: admin_donation.php
session_start();
include 'config.php';

// Pastikan yang akses adalah ADMIN
// (Sesuaikan logika ini dengan cara Anda menyimpan role admin)
if(!isset($_SESSION['user_id'])) { // Tambahkan pengecekan role='admin' jika perlu
    header("Location: login_unified.php");
    exit();
}

// 1. Ambil Saldo Donasi (Wallet Admin)
$admin_wallet = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM wallets WHERE user_id = 1"));
$current_balance = $admin_wallet['balance'] ?? 0;

// 2. Ambil Daftar Agen
$agents = mysqli_query($conn, "SELECT u.id, u.name, a.region FROM users u JOIN agents a ON u.id = a.user_id WHERE u.role='agent'");

// PROSES DISTRIBUSI
if(isset($_POST['distribute'])) {
    $agent_id = $_POST['agent_id'];
    $program_name = mysqli_real_escape_string($conn, $_POST['program_name']);
    $qty = (int)$_POST['quantity'];
    $cost = (int)$_POST['cost']; // Berapa uang donasi yang dipakai untuk beli makanan ini

    if($cost > $current_balance) {
        echo "<script>alert('Saldo Donasi tidak cukup!');</script>";
    } else {
        mysqli_begin_transaction($conn);
        try {
            // A. Potong Saldo Donasi
            $sisa = $current_balance - $cost;
            mysqli_query($conn, "UPDATE wallets SET balance = $sisa WHERE id = {$admin_wallet['id']}");

            // B. Catat Pengeluaran
            $desc = "Distribusi Program: $program_name ($qty Porsi)";
            mysqli_query($conn, "INSERT INTO wallet_transactions (wallet_id, transaction_type, amount, balance_after, description, status, created_at) VALUES ({$admin_wallet['id']}, 'donation_use', $cost, $sisa, '$desc', 'completed', NOW())");

            // C. Buat Tugas untuk Agen
            $stmt = mysqli_prepare($conn, "INSERT INTO donation_distributions (admin_id, agent_id, program_name, quantity, total_cost, status, created_at) VALUES (1, ?, ?, ?, ?, 'pending', NOW())");
            mysqli_stmt_bind_param($stmt, "isid", $agent_id, $program_name, $qty, $cost);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            header("Location: admin_donation.php?success=1");
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Donasi (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4">Admin Donation Manager</h2>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-success text-white p-4 text-center shadow">
                    <h4>Total Uang Donasi</h4>
                    <h1 class="fw-bold">Rp <?php echo number_format($current_balance); ?></h1>
                    <p>Siap digunakan untuk membeli makanan</p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow p-4">
                    <h5><i class="bi bi-box-seam me-2"></i>Buat Program Distribusi</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Nama Program Makanan</label>
                            <input type="text" name="program_name" class="form-control" placeholder="Contoh: Nasi Padang Jumat Berkah" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Pilih Agen Penerima</label>
                                <select name="agent_id" class="form-select" required>
                                    <option value="">-- Pilih Agen --</option>
                                    <?php while($a = mysqli_fetch_assoc($agents)): ?>
                                        <option value="<?php echo $a['id']; ?>">
                                            <?php echo $a['name']; ?> (<?php echo $a['region']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Jumlah Porsi</label>
                                <input type="number" name="quantity" class="form-control" placeholder="100" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Biaya (Ambil dari Saldo)</label>
                                <input type="number" name="cost" class="form-control" placeholder="Rp" required>
                            </div>
                        </div>
                        <button type="submit" name="distribute" class="btn btn-primary w-100 fw-bold">
                            Beli & Kirim ke Agen
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card mt-4 p-3 shadow-sm">
            <h5>Riwayat Distribusi</h5>
            <table class="table">
                <thead><tr><th>Tanggal</th><th>Program</th><th>Agen</th><th>Porsi</th><th>Biaya</th><th>Status</th></tr></thead>
                <tbody>
                    <?php 
                    $history = mysqli_query($conn, "SELECT d.*, u.name as agent_name FROM donation_distributions d JOIN users u ON d.agent_id = u.id ORDER BY d.created_at DESC");
                    while($h = mysqli_fetch_assoc($history)): 
                    ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($h['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($h['program_name']); ?></td>
                        <td><?php echo htmlspecialchars($h['agent_name']); ?></td>
                        <td><?php echo $h['quantity']; ?> Porsi</td>
                        <td>Rp <?php echo number_format($h['total_cost']); ?></td>
                        <td>
                            <?php if($h['status']=='pending'): ?>
                                <span class="badge bg-warning text-dark">Dikirim ke Agen</span>
                            <?php else: ?>
                                <span class="badge bg-success">Sudah Dibagikan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>