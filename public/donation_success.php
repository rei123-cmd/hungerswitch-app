<?php
// File: donation_success.php
session_start();
if (!isset($_GET['amount'])) header("Location: index.php");
$amount = $_GET['amount'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Donasi Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #198754, #20c997); 
            height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Poppins', sans-serif; overflow: hidden;
        }
        .success-box {
            background: white; padding: 50px 40px; border-radius: 30px; text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            animation: popUp 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            max-width: 400px; width: 90%;
        }
        @keyframes popUp { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .check-circle {
            width: 100px; height: 100px; background: #e8f5e9; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); } 70% { box-shadow: 0 0 0 20px rgba(25, 135, 84, 0); } 100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); } }

        .check-icon { font-size: 3rem; color: #198754; animation: checkMark 0.5s ease-in-out 0.4s both; }
        @keyframes checkMark { from { transform: scale(0); } to { transform: scale(1); } }
    </style>
</head>
<body>
    <div class="success-box">
        <div class="check-circle">
            <i class="bi bi-check-lg check-icon"></i>
        </div>
        <h2 class="fw-bold text-dark">Terima Kasih!</h2>
        <p class="text-muted">Donasi Anda telah kami terima.</p>
        
        <div class="py-3 border-top border-bottom my-4">
            <small class="text-uppercase text-muted fw-bold">Total Donasi</small>
            <h1 class="text-success fw-bold m-0">Rp <?php echo number_format($amount); ?></h1>
        </div>

        <a href="index.php" class="btn btn-dark w-100 py-3 rounded-pill fw-bold">Kembali ke Beranda</a>
    </div>
</body>
</html>