<?php
// File: config.php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "hungerswitch_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 1. Panggil Security Helper (Pastikan file security.php ada di folder yang sama)
require_once __DIR__ . '/security.php';

// 2. Jalankan Session Aman (Otomatis jalan di semua halaman yang include config.php)
SecurityHelper::secureSessionStart();
?>