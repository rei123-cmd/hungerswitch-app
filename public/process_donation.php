<?php
// File: process_donation.php
require_once 'config.php'; 
if(session_status() === PHP_SESSION_NONE) { @session_start(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: donasi.php"); exit(); }

// Ambil Data
$user_id = $_SESSION['user_id'];
$amount  = (int)$_POST['amount'];

// 1. CARI DOMPET ADMIN (ID 1)
// Semua donasi masuk ke sini agar angkanya sama dilihat semua orang
$admin_id = 1; 
$check = mysqli_query($conn, "SELECT id, balance FROM wallets WHERE user_id = $admin_id");

if (mysqli_num_rows($check) == 0) {
    // Auto create wallet admin jika belum ada
    mysqli_query($conn, "INSERT INTO wallets (user_id, balance) VALUES ($admin_id, 0)");
    $admin_wallet = ['id' => mysqli_insert_id($conn), 'balance' => 0];
} else {
    $admin_wallet = mysqli_fetch_assoc($check);
}

mysqli_begin_transaction($conn);

try {
    // 2. TAMBAH SALDO ADMIN
    $new_balance = $admin_wallet['balance'] + $amount;
    mysqli_query($conn, "UPDATE wallets SET balance = $new_balance WHERE id = {$admin_wallet['id']}");

    // 3. GENERATE RECEIPT ID & CATAT LOG
    // Receipt ID ini nanti digunakan untuk validasi di halaman receipt.php
    $receipt_id = 'REC-' . strtoupper(uniqid()) . rand(100,999);
    
    // Deskripsi mengandung ID User untuk validasi kepemilikan struk
    $desc = "Donasi masuk dari User #$user_id"; 
    
    // Simpan Receipt ID di kolom 'reference_id'
    $stmt = mysqli_prepare($conn, "INSERT INTO wallet_transactions (wallet_id, transaction_type, amount, balance_after, description, reference_id, status, created_at) VALUES (?, 'donation_in', ?, ?, ?, ?, 'completed', NOW())");
    mysqli_stmt_bind_param($stmt, "iddss", $admin_wallet['id'], $amount, $new_balance, $desc, $receipt_id);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);
    
    // 4. REDIRECT KEMBALI KE PAGE DONASI
    // Kita kirim parameter receipt_id agar frontend bisa menampilkan tombol "Lihat Struk"
    header("Location: donasi.php?status=success&amount=$amount&receipt=$receipt_id");
    exit();

} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: donasi.php?status=error");
    exit();
}
?>