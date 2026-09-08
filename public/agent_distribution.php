<?php
// File: agent_distribution.php
session_start();
include 'config.php';

// Cek Login Agen
if(!isset($_SESSION['user_id'])) {
    header("Location: login_unified.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// PROSES KONFIRMASI (Agen sudah membagikan makanan)
if(isset($_GET['confirm_id'])) {
    $dist_id = $_GET['confirm_id'];
    mysqli_query($conn, "UPDATE donation_distributions SET status = 'distributed', distributed_at = NOW() WHERE id = $dist_id AND agent_id = $user_id");
    header("Location: agent_distribution.php");
    exit();
}

// Ambil Data Misi
$missions = mysqli_query($conn, "SELECT * FROM donation_distributions WHERE agent_id = $user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tugas Distribusi Agen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-person-badge-fill me-2"></i>Misi Kebaikan Saya</h3>
            <a href="agen.php" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
        </div>

        <?php while($m = mysqli_fetch_assoc($missions)): ?>
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-primary mb-1"><?php echo htmlspecialchars($m['program_name']); ?></h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-clock me-1"></i> Diterima: <?php echo date('d M Y', strtotime($m['created_at'])); ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold mb-0"><?php echo $m['quantity']; ?></h3>
                            <small>Porsi Makanan</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Status: 
                            <?php if($m['status'] == 'pending'): ?>
                                <span class="badge bg-warning text-dark">BELUM DIBAGIKAN</span>
                            <?php else: ?>
                                <span class="badge bg-success">SELESAI DIBAGIKAN</span>
                                <small class="text-muted ms-2">(<?php echo date('d M H:i', strtotime($m['distributed_at'])); ?>)</small>
                            <?php endif; ?>
                        </div>

                        <?php if($m['status'] == 'pending'): ?>
                            <a href="?confirm_id=<?php echo $m['id']; ?>" class="btn btn-success fw-bold" onclick="return confirm('Apakah Anda yakin sudah membagikan makanan ini ke masyarakat?')">
                                <i class="bi bi-check-circle-fill me-2"></i>Lapor Sudah Dibagikan
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Misi Selesai</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($missions) == 0): ?>
            <div class="alert alert-info text-center">Belum ada tugas distribusi dari Admin.</div>
        <?php endif; ?>
    </div>
</body>
</html>