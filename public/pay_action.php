<?php
// File: pay_action.php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Akses Ditolak.");

$user_id  = $_SESSION['user_id'];
$order_id = $_POST['order_id'];

// 1. Ambil Order
$q_order = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = '$order_id' AND user_id = '$user_id'");
$order   = mysqli_fetch_assoc($q_order);

if (!$order) die("Pesanan tidak valid.");

// 2. UPDATE STATUS ORDER (Tanpa Cek Saldo)
// Kita anggap pembayaran sukses via Gateway Luar / Tunai
mysqli_query($conn, "UPDATE orders SET payment_status = 'paid', status = 'process', paid_at = NOW() WHERE id = {$order['id']}");

// 3. Redirect ke Sukses
header("Location: order_success.php?order_id=" . $order_id);
exit();
?>