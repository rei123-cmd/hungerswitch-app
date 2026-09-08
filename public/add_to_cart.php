<?php
// File: add_to_cart.php - UPDATE
session_start();

// Menerima data JSON dari Javascript
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $productId = $data['id'];

    // Cek apakah produk sudah ada di keranjang
    if (isset($_SESSION['cart'][$productId])) {
        // Jika ada, tambah quantity
        $_SESSION['cart'][$productId]['quantity'] += 1;
    } else {
        // Jika belum, masukkan data baru
        $_SESSION['cart'][$productId] = [
            'id' => $data['id'],
            'name' => $data['name'],
            'price' => $data['price'], // Pastikan ini angka (integer/float)
            'originalPrice' => $data['originalPrice'] ?? $data['price'],
            'restaurant' => $data['restaurant'],
            'image' => $data['image'],
            'category' => $data['category'] ?? 'Uncategorized',
            'quantity' => 1
        ];
    }

    // Hitung total item untuk update badge
    $totalItems = 0;
    $totalAmount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalItems += $item['quantity'];
        $totalAmount += $item['price'] * $item['quantity'];
    }

    echo json_encode([
        'status' => 'success', 
        'total_items' => $totalItems,
        'total_amount' => $totalAmount,
        'message' => 'Item berhasil ditambahkan ke keranjang'
    ]);
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'No data received'
    ]);
}
?>