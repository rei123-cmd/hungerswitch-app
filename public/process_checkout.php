<?php
// File: process_checkout.php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header("Location: marketplace.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Hitung Total
$items = [];
$total_item_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_item_price += ($item['price'] * $item['quantity']);
    $items[] = $item;
}
$service_fee = 2000;
$grand_total = $total_item_price + $service_fee;

// 2. Susun Data JSON (Hanya Item, Tanpa Alamat)
$order_json = [
    'items' => $items,
    'pricing' => ['subtotal' => $total_item_price, 'service' => $service_fee, 'total' => $grand_total],
    'type' => 'pickup' // Penanda ambil sendiri
];

$json_data = base64_encode(json_encode($order_json));
$order_id  = 'HS-' . date('ymdHis') . rand(100, 999);

// 3. Simpan ke Database
$query = "INSERT INTO orders (order_id, user_id, order_data, total_amount, payment_status, status, created_at) 
          VALUES (?, ?, ?, ?, 'unpaid', 'pending', NOW())";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sisd", $order_id, $user_id, $json_data, $grand_total);

if (mysqli_stmt_execute($stmt)) {
    // === KUNCI: KERANJANG DIHAPUS DISINI ===
    unset($_SESSION['cart']);
    SecurityHelper::setSecureCookie('hs_cart_backup', '', -1); // Hapus cookie juga
    
    // Redirect ke Halaman Pembayaran
    header("Location: payment.php?order_id=" . $order_id);
    exit();
} else {
    die("Gagal membuat pesanan.");
}
?>