<?php
// File: config.php

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$db   = getenv('DB_DATABASE') ?: 'hungerswitch_db';
$port = (int) (getenv('DB_PORT') ?: 3306);

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (file_exists(__DIR__ . '/security.php')) {
    require_once __DIR__ . '/security.php';
    if (class_exists('SecurityHelper') && method_exists('SecurityHelper', 'secureSessionStart')) {
        SecurityHelper::secureSessionStart();
    }
}