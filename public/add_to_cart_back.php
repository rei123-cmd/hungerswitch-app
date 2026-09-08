<?php
// File: add_to_cart.php
require_once 'config.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Terima data JSON dari frontend
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit();
}

// Inisialisasi cart di session jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productId = $data['id'];

// Cek apakah produk sudah ada di cart
if (isset($_SESSION['cart'][$productId])) {
    $_SESSION['cart'][$productId]['quantity'] += 1;
} else {
    // Tambah produk baru ke cart
    $_SESSION['cart'][$productId] = [
        'id' => $data['id'],
        'name' => $data['name'],
        'price' => (float)$data['price'],
        'originalPrice' => (float)($data['originalPrice'] ?? $data['price']),
        'restaurant' => $data['restaurant'],
        'image' => $data['image'],
        'category' => $data['category'] ?? 'Uncategorized',
        'quantity' => 1
    ];
}

// Hitung total
$totalItems = 0;
$totalAmount = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalItems += $item['quantity'];
    $totalAmount += $item['price'] * $item['quantity'];
}

// Enkripsi dan simpan ke cookie juga (backup)
$cartData = json_encode($_SESSION['cart']);
SecurityHelper::setSecureCookie('hs_cart_backup', $cartData, 7);

echo json_encode([
    'status' => 'success',
    'total_items' => $totalItems,
    'total_amount' => $totalAmount,
    'message' => 'Item berhasil ditambahkan'
]);
?>