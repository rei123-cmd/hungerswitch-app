<?php
// File: create_order.php
//session_start();
include 'config.php';
header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) throw new Exception("Silakan login terlebih dahulu.");

    // Ambil Data JSON dari Checkout
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || empty($data['items'])) throw new Exception("Keranjang kosong.");

    $user_id = $_SESSION['user_id'];
    $total_amount = $data['pricing']['total'];
    // Buat Order ID Unik (String)
    $order_id = 'HS-' . date('Ymd') . rand(1000, 9999);
    
    // Simpan data keranjang lengkap ke database (sebagai backup)
    $order_data_json = base64_encode(json_encode($data));

    // INSERT ke Database dengan status 'unpaid'
    $query = "INSERT INTO orders (order_id, user_id, order_data, total_amount, payment_status, status, created_at) 
              VALUES (?, ?, ?, ?, 'unpaid', 'pending', NOW())";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sisd", $order_id, $user_id, $order_data_json, $total_amount);
    
    if (mysqli_stmt_execute($stmt)) {
        // Sukses simpan, kembalikan Order ID ke browser
        echo json_encode(['success' => true, 'order_id' => $order_id]);
    } else {
        throw new Exception("Gagal menyimpan pesanan ke database.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>