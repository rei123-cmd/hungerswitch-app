<?php
// File: founder_programs.php
// Admin membuat program distribusi menggunakan dana donasi
session_start();
include 'config.php';

// Cek Founder
if (!isset($_SESSION['user_id'])) {
    header("Location: login_unified.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT role FROM users WHERE id = $user_id"));

if ($user_check['role'] !== 'founder') {
    die("AKSES DITOLAK: Halaman ini hanya untuk Founder Hungerswitch.");
}

// Ambil Saldo Dana Donasi
$fund = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM donation_fund WHERE id = 1"));
$current_balance = $fund['current_balance'];

// Ambil Daftar Agen
$agents_query = mysqli_query($conn, 
    "SELECT a.id, u.name, a.region, a.total_distributions 
     FROM agents a 
     JOIN users u ON a.user_id = u.id 
     WHERE a.is_active = 1 AND a.verification_status = 'verified'
     ORDER BY u.name ASC"
);

// Proses Buat Program Baru
if (isset($_POST['create_program'])) {
    $program_name = mysqli_real_escape_string($conn, $_POST['program_name']);
    $agent_id = (int)$_POST['agent_id'];
    $budget = (int)$_POST['budget'];
    $quantity = (int)$_POST['quantity'];
    $menu = mysqli_real_escape_string($conn, $_POST['menu']);
    $scheduled_date = $_POST['scheduled_date'];
    $distribution_time = $_POST['distribution_time'];
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    // Validasi Budget
    if ($budget > $current_balance) {
        echo "<script>alert('Dana tidak cukup! Saldo: Rp " . number_format($current_balance) . "');</script>";
    } else {
        mysqli_begin_transaction($conn);
        
        try {
            // Generate Program Number
            $program_number = 'PROG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            
            // Insert Program
            $stmt = mysqli_prepare($conn,
                "INSERT INTO food_programs 
                (program_number, program_name, assigned_agent_id, budget_allocated, quantity_planned, menu_description, scheduled_date, distribution_time, distribution_location, status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'planned', ?, NOW())"
            );
            
            mysqli_stmt_bind_param($stmt, 'ssidissss',
                $program_number,
                $program_name,
                $agent_id,
                $budget,
                $quantity,
                $menu,
                $scheduled_date,
                $distribution_time,
                $location,
                $user_id
            );
            
            mysqli_stmt_execute($stmt);
            $program_id = mysqli_insert_id($conn);
            
            // Kurangi Saldo Dana Donasi
            $balance_before = $current_balance;
            $balance_after = $current_balance - $budget;
            
            mysqli_query($conn, 
                "UPDATE donation_fund 
                 SET current_balance = $balance_after, 
                     total_distributed = total_distributed + $budget
                 WHERE id = 1"
            );
            
            // Catat Transaksi
            $desc = "Alokasi dana untuk program: $program_name ($quantity porsi)";
            $stmt2 = mysqli_prepare($conn,
                "INSERT INTO donation_transactions 
                (transaction_type, reference_type, reference_id, amount, balance_before, balance_after, description, processed_by, created_at)
                VALUES ('distribution', 'food_programs', ?, ?, ?, ?, ?, ?, NOW())"
            );
            
            mysqli_stmt_bind_param($stmt2, 'idddsi',
                $program_id,
                $budget,
                $balance_before,
                $balance_after,
                $desc,
                $user_id
            );
            
            mysqli_stmt_execute($stmt2);
            
            mysqli_commit($conn);
            header("Location: founder_programs.php?success=1");
            exit();
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
}

// Ambil Daftar Program
$programs = mysqli_query($conn,
    "SELECT fp.*, u.name as agent_name, a.region
     FROM food_programs fp
     JOIN agents a ON fp.assigned_agent_id = a.id
     JOIN users u ON a.user_id = u.id
     ORDER BY fp.created_at DESC
     LIMIT 20"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Program Distribusi - Founder Hungerswitch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 80px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .stat-card {
            background: linear-gradient(135deg, #2F5233, #3d6b42);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .stat-card h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
        }
        .program-card {
            border-left: 4px solid #2F5233;
            transition: 0.3s;
        }
        .program-card:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 25px rgba(0,0,0,0.12);
        }
        .badge-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="founder_dashboard.php">
                <i class="bi bi-shield-check me-2"></i>Founder Dashboard
            </a>
            <div>
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">Website</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Berhasil!</strong> Program distribusi telah dibuat dan dana dialokasikan.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Header & Saldo -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75 text-uppercase">Dana Donasi Tersedia</small>
                            <h2>Rp <?php echo number_format($current_balance, 0, ',', '.'); ?></h2>
                            <p class="mb-0 opacity-75">Siap dialokasikan untuk program sosial</p>
                        </div>
                        <i class="bi bi-wallet2 display-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Buat Program -->
        <div class="row">
            <div class="col-lg-5 mb-4">
                <div class="card p-4">
                    <h4 class="mb-4"><i class="bi bi-plus-circle me-2"></i>Buat Program Baru</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Program</label>
                            <input type="text" name="program_name" class="form-control" placeholder="Contoh: Jumat Berkah Serpong" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Agen</label>
                            <select name="agent_id" class="form-select" required>
                                <option value="">-- Pilih Agen --</option>
                                <?php while($agent = mysqli_fetch_assoc($agents_query)): ?>
                                <option value="<?php echo $agent['id']; ?>">
                                    <?php echo $agent['name']; ?> (<?php echo $agent['region']; ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Menu Makanan</label>
                            <input type="text" name="menu" class="form-control" placeholder="Contoh: Nasi Kotak Ayam + Sayur" required>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Jumlah Porsi</label>
                                <input type="number" name="quantity" class="form-control" placeholder="100" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Budget (Rp)</label>
                                <input type="number" name="budget" class="form-control" placeholder="500000" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="date" name="scheduled_date" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Waktu</label>
                                <input type="time" name="distribution_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi Distribusi</label>
                            <input type="text" name="location" class="form-control" placeholder="Masjid Al-Hidayah, BSD" required>
                        </div>

                        <button type="submit" name="create_program" class="btn btn-success w-100 py-3 fw-bold">
                            <i class="bi bi-send-fill me-2"></i>BUAT PROGRAM & ALOKASIKAN DANA
                        </button>
                    </form>
                </div>
            </div>

            <!-- Daftar Program -->
            <div class="col-lg-7">
                <div class="card p-4">
                    <h4 class="mb-4"><i class="bi bi-list-check me-2"></i>Program Distribusi</h4>
                    
                    <?php if(mysqli_num_rows($programs) > 0): ?>
                        <?php while($prog = mysqli_fetch_assoc($programs)): ?>
                        <div class="program-card card mb-3 p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($prog['program_name']); ?></h5>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3"></i> <?php echo date('d M Y', strtotime($prog['scheduled_date'])); ?> 
                                        | <i class="bi bi-clock"></i> <?php echo date('H:i', strtotime($prog['distribution_time'])); ?>
                                    </small>
                                </div>
                                <span class="badge-status <?php 
                                    echo $prog['status'] == 'planned' ? 'bg-warning text-dark' : 
                                         ($prog['status'] == 'completed' ? 'bg-success' : 'bg-info'); 
                                ?>">
                                    <?php echo strtoupper($prog['status']); ?>
                                </span>
                            </div>

                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <small class="text-muted">Agen</small>
                                    <p class="mb-0 fw-bold"><?php echo $prog['agent_name']; ?></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Wilayah</small>
                                    <p class="mb-0 fw-bold"><?php echo $prog['region']; ?></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Budget</small>
                                    <p class="mb-0 fw-bold text-success">Rp <?php echo number_format($prog['budget_allocated']); ?></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Target Porsi</small>
                                    <p class="mb-0 fw-bold"><?php echo $prog['quantity_planned']; ?> Porsi</p>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-1 mb-3"></i>
                            <p>Belum ada program distribusi yang dibuat.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>