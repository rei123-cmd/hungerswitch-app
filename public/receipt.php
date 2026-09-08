<?php
// File: receipt.php
require_once 'config.php';
if(session_status() === PHP_SESSION_NONE) { @session_start(); }

if(!isset($_SESSION['user_id'])) { header("Location: login_unified.php"); exit(); }
if(!isset($_GET['id'])) { header("Location: index.php"); exit(); }

$receipt_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Cek Role dari Session (Pastikan diset di login)
$role = $_SESSION['user_roles'][0] ?? 'customer'; 
// Atau ambil lagi dari DB biar aman
$u_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT role FROM users WHERE id=$user_id"));
if($u_check) $role = $u_check['role'];

// Cari Transaksi berdasarkan Receipt ID (reference_id)
$q = "SELECT * FROM wallet_transactions WHERE reference_id = '$receipt_id' AND transaction_type = 'donation_in'";
$res = mysqli_query($conn, $q);
$trx = mysqli_fetch_assoc($res);

if(!$trx) die("Struk tidak ditemukan.");

// VALIDASI PRIVASI:
// 1. Apakah Admin?
// 2. Apakah User ID yang login ada di deskripsi transaksi? (Format desc: "Donasi dari User #ID")
$is_admin = ($role === 'admin');
$is_owner = (strpos($trx['description'], "User #$user_id") !== false);

if (!$is_admin && !$is_owner) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h1 style='color:red;'>AKSES DITOLAK</h1>
            <p>Maaf, struk donasi ini bersifat rahasia antara Donatur dan Hungerswitch.</p>
            <a href='index.php'>Kembali ke Beranda</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Struk Donasi #<?php echo $receipt_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #e0e0e0; padding: 50px 0; font-family: 'Courier New', Courier, monospace; }
        .receipt {
            max-width: 400px; margin: auto; background: white; padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background-image: radial-gradient(#eee 1px, transparent 1px);
            background-size: 10px 10px;
            position: relative; border-top: 5px solid #2F5233;
        }
        .receipt::after {
            content: ''; position: absolute; bottom: -10px; left: 0; width: 100%; height: 20px;
            background: linear-gradient(45deg, transparent 50%, white 50%), linear-gradient(-45deg, transparent 50%, white 50%);
            background-size: 20px 20px; background-repeat: repeat-x;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-uppercase" style="color: #2F5233;">Hungerswitch</h4>
            <p class="small mb-0">Official Donation Receipt</p>
            <p class="small text-muted"><?php echo date('d F Y H:i', strtotime($trx['created_at'])); ?></p>
        </div>
        <hr style="border-top: 2px dashed #ccc;">
        
        <div class="d-flex justify-content-between mb-2">
            <span>Tipe</span>
            <span class="fw-bold">Donasi Masuk</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>ID Struk</span>
            <span class="small"><?php echo $receipt_id; ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Donatur</span>
            <span><?php echo $is_admin ? 'User #'.$user_id : 'Anda'; ?></span>
        </div>
        
        <hr style="border-top: 2px dashed #ccc;">
        
        <div class="d-flex justify-content-between align-items-center py-3">
            <span class="fw-bold fs-5">TOTAL</span>
            <span class="fw-bold fs-3" style="color: #D9232D;">Rp <?php echo number_format($trx['amount'],0,',','.'); ?></span>
        </div>

        <hr style="border-top: 2px dashed #ccc;">
        
        <div class="text-center mt-4">
            <p class="small text-muted mb-1">Dana telah masuk ke Wallet Hungerswitch.</p>
            <p class="small text-muted">Terima kasih atas kebaikan Anda.</p>
            <a href="donasi.php" class="btn btn-sm btn-dark mt-3 px-4 rounded-pill">Kembali</a>
        </div>
    </div>
</body>
</html>