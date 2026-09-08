<?php
// File: process_order.php
// Matikan tampilan error HTML agar tidak merusak data JSON
ini_set('display_errors', 0);
// Tapi tetap catat error di background
error_reporting(E_ALL);

//session_start();
include 'config.php';

header('Content-Type: application/json');

// Fungsi darurat penangkap error fatal
function shutdownHandler() {
    $error = error_get_last();
    if ($error !== NULL && $error['type'] === E_ERROR) {
        // Jika server crash, kirim pesan ini ke Javascript
        echo json_encode(['success' => false, 'message' => 'Fatal Error: ' . $error['message']]);
    }
}
register_shutdown_function('shutdownHandler');

try {
    // 1. CEK LOGIN
    if(!isset($_SESSION['user_id'])) {
        throw new Exception('Sesi habis. Silakan login ulang.');
    }

    // 2. TERIMA DATA JSON
    $json = file_get_contents('php://input');
    $orderData = json_decode($json, true);

    if(!$orderData || empty($orderData['items'])) {
        throw new Exception('Data pesanan kosong atau tidak terbaca.');
    }

    $user_id = $_SESSION['user_id'];
    $total_amount = $orderData['pricing']['total']; // Total Rupiah
    $order_id_display = 'HS-' . date('ymd') . rand(100, 999); // ID Cantik

    // 3. MULAI TRANSAKSI DATABASE
    mysqli_begin_transaction($conn);

    // A. Cek Saldo User (Tanpa Locking biar tidak macet)
    $q_wallet = mysqli_query($conn, "SELECT id, balance FROM wallets WHERE user_id = $user_id");
    $wallet = mysqli_fetch_assoc($q_wallet);

    if(!$wallet) {
        throw new Exception("Dompet (Wallet) tidak ditemukan. Hubungi admin.");
    }
    
    // VALIDASI SALDO (PENTING)
    if($wallet['balance'] < $total_amount) {
        throw new Exception("Saldo tidak cukup! Total: " . number_format($total_amount) . ", Saldo: " . number_format($wallet['balance']));
    }

    // B. Cek Stok & Kurangi
    foreach ($orderData['items'] as $item) {
        $prod_id = (int)$item['id'];
        $qty_needed = (int)$item['quantity'];

        // Ambil stok
        $q_stock = mysqli_query($conn, "SELECT stock, name FROM products WHERE id = $prod_id");
        $product = mysqli_fetch_assoc($q_stock);

        if(!$product) throw new Exception("Produk ID $prod_id hilang dari database.");
        
        if($product['stock'] < $qty_needed) {
            throw new Exception("Stok habis untuk: " . $product['name'] . " (Sisa: " . $product['stock'] . ")");
        }

        // Kurangi stok
        $new_stock = $product['stock'] - $qty_needed;
        $upd_stock = mysqli_query($conn, "UPDATE products SET stock = $new_stock WHERE id = $prod_id");
        if(!$upd_stock) throw new Exception("Gagal update stok database.");
    }

    // C. Masukkan ke Tabel Orders
    // Kita simpan JSON lengkap agar detail pesanan aman
    $json_stored = base64_encode(json_encode($orderData));
    
    // Query INSERT standar
    $query_insert = "INSERT INTO orders (
        order_id, user_id, order_data, total_amount, 
        payment_method, payment_status, status, created_at
    ) VALUES (?, ?, ?, ?, 'wallet', 'paid', 'pending', NOW())";
    
    $stmt = mysqli_prepare($conn, $query_insert);
    // s=string, i=integer, s=string, d=double
    mysqli_stmt_bind_param($stmt, "sisd", 
        $order_id_display, $user_id, $json_stored, $total_amount
    );
    
    if(!mysqli_stmt_execute($stmt)) {
        throw new Exception("Gagal Simpan Order: " . mysqli_error($conn));
    }

    // D. Potong Saldo
    $new_balance = $wallet['balance'] - $total_amount;
    $upd_wallet = mysqli_query($conn, "UPDATE wallets SET balance = $new_balance WHERE id = " . $wallet['id']);
    
    if(!$upd_wallet) throw new Exception("Gagal update saldo.");

    // E. Catat Log Transaksi
    $desc = "Pembayaran Order #" . $order_id_display;
    $query_log = "INSERT INTO wallet_transactions (
        wallet_id, transaction_type, amount, balance_before, balance_after, 
        description, reference_id, status, created_at
    ) VALUES (?, 'purchase', ?, ?, ?, ?, ?, 'completed', NOW())";

    $stmt_log = mysqli_prepare($conn, $query_log);
    mysqli_stmt_bind_param($stmt_log, "idddss", 
        $wallet['id'], $total_amount, $wallet['balance'], $new_balance, $desc, $order_id_display
    );
    mysqli_stmt_execute($stmt_log);

    // BERHASIL -> SIMPAN PERMANEN
    mysqli_commit($conn);
    
    // Kosongkan Keranjang
    unset($_SESSION['cart']);

    // Kirim Balasan Sukses
    echo json_encode([
        'success' => true,
        'order_id' => $order_id_display,
        'message' => 'Pembayaran Berhasil!'
    ]);

} catch (Exception $e) {
    // GAGAL -> BATALKAN SEMUA
    mysqli_rollback($conn);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>