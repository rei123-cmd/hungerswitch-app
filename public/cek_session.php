<?php
// File: cek_session.php
include 'config.php'; // Ini akan otomatis start session

echo "<h3>Status Session:</h3>";
echo "Session ID: " . session_id() . "<br><br>";

echo "<h3>Isi Data Session (Server Side):</h3>";
echo "<pre>";
print_r($_SESSION); // Menampilkan semua data yang disimpan di session
echo "</pre>";

echo "<h3>Isi Cookie (Browser Side):</h3>";
echo "<pre>";
print_r($_COOKIE); // Menampilkan cookie yang dikirim browser
echo "</pre>";

// Tes Dekripsi Cookie Keranjang
if(isset($_COOKIE['hs_cart_backup'])) {
    echo "<h3>Tes Dekripsi Cookie Keranjang:</h3>";
    $dekripsi = SecurityHelper::decrypt($_COOKIE['hs_cart_backup']);
    echo "Isi Asli: " . htmlspecialchars($dekripsi);
}
?>