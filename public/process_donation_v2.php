<?php
// File: process_donation_v2.php - PROSES DONASI KE REKBER HUNGERSWITCH
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: donasi.php");
    exit();
}

// Ambil data donasi
$amount = floatval($_POST['amount']);
$is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Validasi
if ($amount < 5000) {
    $_SESSION['error'] = "Minimal donasi adalah Rp 5.000";
    header("Location: donasi.php");
    exit();
}

// Generate Donation Number
$donation_number = 'DON-' . date('Ymd') . '-' . strtoupper(uniqid());

// Insert ke user_donations
$payment_method = 'QRIS / Transfer'; // Nanti bisa disesuaikan dengan payment gateway
$status = 'pending';

$query = "INSERT INTO user_donations (user_id, donation_number, amount, payment_method, status, created_at) 
          VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "isdss", $user_id, $donation_number, $amount, $payment_method, $status);

if (mysqli_stmt_execute($stmt)) {
    $donation_id = mysqli_insert_id($conn);
    
    // ===========================================
    // SIMULASI: Auto-approve donasi (untuk development)
    // Di production, ini harus menunggu payment gateway callback
    // ===========================================
    
    // 1. Update status donasi menjadi paid
    $update_query = "UPDATE user_donations SET status = 'paid', paid_at = NOW() WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "i", $donation_id);
    mysqli_stmt_execute($update_stmt);
    
    // 2. Update donation_fund
    $fund_query = "SELECT current_balance FROM donation_fund WHERE id = 1";
    $fund_result = mysqli_query($conn, $fund_query);
    $fund = mysqli_fetch_assoc($fund_result);
    $balance_before = $fund['current_balance'];
    $balance_after = $balance_before + $amount;
    
    $update_fund = "UPDATE donation_fund 
                    SET current_balance = current_balance + ?, 
                        total_received = total_received + ?,
                        last_updated_at = NOW() 
                    WHERE id = 1";
    $fund_stmt = mysqli_prepare($conn, $update_fund);
    mysqli_stmt_bind_param($fund_stmt, "dd", $amount, $amount);
    mysqli_stmt_execute($fund_stmt);
    
    // 3. Log transaction
    $donor_name = $is_anonymous ? 'Hamba Allah' : ($_SESSION['user_name'] ?? 'Anonymous');
    $log_query = "INSERT INTO donation_transactions 
                  (transaction_type, reference_type, reference_id, amount, balance_before, balance_after, description, created_at) 
                  VALUES ('income', 'donations', ?, ?, ?, ?, ?, NOW())";
    $log_stmt = mysqli_prepare($conn, $log_query);
    $description = "Donasi dari " . $donor_name;
    mysqli_stmt_bind_param($log_stmt, "iddds", $donation_id, $amount, $balance_before, $balance_after, $description);
    mysqli_stmt_execute($log_stmt);
    
    // ===========================================
    // END SIMULASI
    // ===========================================
    
    // Redirect dengan success message
    header("Location: donasi.php?success=1&donation_number=" . $donation_number);
    exit();
    
} else {
    $_SESSION['error'] = "Gagal memproses donasi. Silakan coba lagi.";
    header("Location: donasi.php");
    exit();
}
?>