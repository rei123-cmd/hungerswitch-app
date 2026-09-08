<?php
// File: cancel_order.php
//session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'];
$user_id = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {
    // 1. Cek Status Pesanan saat ini
    $query = "SELECT id, status, total_amount, payment_method FROM orders WHERE order_id = ? AND user_id = ? FOR UPDATE";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $order_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);

    if (!$order) {
        throw new Exception("Pesanan tidak ditemukan.");
    }

    // LOGIKA UTAMA: Cek apakah sudah dikonfirmasi
    if ($order['status'] !== 'pending') {
        // Jika status bukan 'pending' (misal sudah 'confirmed', 'completed', 'cancelled')
        throw new Exception("Pesanan sudah dikonfirmasi atau selesai, tidak bisa dibatalkan.");
    }

    // 2. Update status menjadi cancelled
    $update_query = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "i", $order['id']);
    mysqli_stmt_execute($update_stmt);

    // 3. REFUND (Kembalikan Saldo)
    // Cek wallet user
    $wallet_query = "SELECT id, balance FROM wallets WHERE user_id = ?";
    $wallet_stmt = mysqli_prepare($conn, $wallet_query);
    mysqli_stmt_bind_param($wallet_stmt, "i", $user_id);
    mysqli_stmt_execute($wallet_stmt);
    $wallet = mysqli_fetch_assoc(mysqli_stmt_get_result($wallet_stmt));

    if ($wallet) {
        $refund_amount = $order['total_amount'];
        $new_balance = $wallet['balance'] + $refund_amount;

        // Update saldo
        $update_wallet = "UPDATE wallets SET balance = ? WHERE id = ?";
        $uw_stmt = mysqli_prepare($conn, $update_wallet);
        mysqli_stmt_bind_param($uw_stmt, "di", $new_balance, $wallet['id']);
        mysqli_stmt_execute($uw_stmt);

        // Catat Transaksi Refund
        $log_query = "INSERT INTO wallet_transactions (wallet_id, transaction_type, amount, balance_before, balance_after, description, reference_id, status, created_at) 
                      VALUES (?, 'refund', ?, ?, ?, 'Refund Pembatalan Pesanan', ?, 'completed', NOW())";
        $log_stmt = mysqli_prepare($conn, $log_query);
        mysqli_stmt_bind_param($log_stmt, "iddds", $wallet['id'], $refund_amount, $wallet['balance'], $new_balance, $order_id);
        mysqli_stmt_execute($log_stmt);
    }

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dibatalkan. Dana telah dikembalikan ke saldo.']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>