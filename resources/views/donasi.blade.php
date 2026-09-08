<?php
// File: donasi.php - REVISI V2
// Konsep: Donasi langsung ke Rekber Hungerswitch via Payment Gateway
session_start();
include 'config.php';

// Cek apakah ada notifikasi sukses dari payment callback
$success = isset($_GET['success']) ? true : false;
$donation_number = isset($_GET['donation_number']) ? $_GET['donation_number'] : null;

// Ambil total dana donasi terkumpul
$fund_query = mysqli_query($conn, "SELECT current_balance, total_received FROM donation_fund WHERE id = 1");
$fund = mysqli_fetch_assoc($fund_query);
$current_balance = $fund['current_balance'] ?? 0;
$total_received = $fund['total_received'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Berbagi Berkah - Hungerswitch</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --hs-green: #2F5233;
            --hs-red: #D9232D;
            --hs-cream: #FCFCF8;
            --hs-light-green: #e9f5eb;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--hs-cream); 
            color: #333;
        }

        /* Hero dengan gradasi hijau */
        .hero-section {
            background: linear-gradient(135deg, rgba(47, 82, 51, 0.95), rgba(47, 82, 51, 0.8)), 
                        url('https://source.unsplash.com/1600x900/?food,charity');
            background-size: cover;
            background-position: center;
            min-height: 500px;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 50px 50px;
        }

        .hero-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.5;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stats Box Floating */
        .stats-container {
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }

        .stats-box {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            border-bottom: 4px solid var(--hs-red);
        }

        .stat-item h3 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--hs-green);
            margin: 0;
        }

        .stat-item p {
            color: #888;
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        /* Form Donasi Sticky */
        .donation-wrapper {
            position: sticky;
            top: 100px;
        }

        .donation-card {
            background: white;
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        .donation-card::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: var(--hs-light-green);
            border-radius: 50%;
            z-index: 0;
        }

        .form-content {
            position: relative;
            z-index: 1;
        }

        /* Grid Nominal */
        .nominal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .nominal-btn {
            background: white;
            border: 2px solid #eee;
            border-radius: 12px;
            padding: 12px 5px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 600;
            color: #666;
            font-size: 0.9rem;
        }

        .nominal-btn:hover,
        .nominal-btn.active {
            border-color: var(--hs-green);
            background: var(--hs-green);
            color: white;
            box-shadow: 0 5px 15px rgba(47, 82, 51, 0.3);
        }

        /* Custom Input */
        .form-control-lg {
            border: 2px solid #eee;
            border-radius: 12px;
            font-weight: 700;
            color: var(--hs-green);
        }

        .form-control-lg:focus {
            border-color: var(--hs-green);
            box-shadow: none;
        }

        /* Tombol Submit MERAH */
        .btn-donate-submit {
            background: var(--hs-red);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 15px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-donate-submit:hover {
            background: #b91d26;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(217, 35, 45, 0.4);
        }

        /* Success Alert */
        .alert-success {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.2);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .heart-beat {
            animation: heartbeat 1.5s infinite;
            color: var(--hs-red);
            display: inline-block;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            15%, 45% { transform: scale(1.3); }
        }

        /* Feature Cards */
        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: 0.3s;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(217, 35, 45, 0.15);
            border-color: var(--hs-red);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: var(--hs-light-green);
            color: var(--hs-green);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .feature-card:hover .feature-icon {
            background: var(--hs-red);
            color: white;
        }

        .section-title {
            color: var(--hs-green);
            font-weight: 800;
            letter-spacing: -1px;
        }

        .text-highlight {
            color: var(--hs-red);
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: 0.8s ease-out;
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <!-- Navbar (sama seperti yang sudah ada) -->
    <nav class="navbar fixed-top bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php" style="color: var(--hs-red);">
                <i class="bi bi-arrow-left-circle me-2"></i>Hungerswitch
            </a>
            <span class="fw-bold" style="color: var(--hs-green);">Berbagi Berkah</span>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="hero-pattern"></div>
        <div class="container hero-content text-center">
            <span class="badge bg-white px-3 py-2 rounded-pill mb-3 fw-bold" style="color: var(--hs-green);">
                <i class="bi bi-stars me-1 text-warning"></i> Program Sosial Hungerswitch
            </span>
            <h1 class="display-4 fw-bold mb-3">Ubah Recehan Jadi <span style="color: #81e69b;">Harapan</span></h1>
            <p class="lead opacity-75 mx-auto" style="max-width: 700px;">
                Donasi Anda dikelola secara transparan untuk membeli makanan layak bagi mereka yang membutuhkan.
            </p>
        </div>
    </header>

    <!-- Stats Box -->
    <div class="container stats-container fade-in-up">
        <div class="stats-box">
            <div class="row text-center">
                <div class="col-md-6 mb-3 mb-md-0 border-end">
                    <div class="stat-item">
                        <h3>Rp <?php echo number_format($current_balance, 0, ',', '.'); ?></h3>
                        <p>Dana Tersedia Saat Ini</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-item">
                        <h3>Rp <?php echo number_format($total_received, 0, ',', '.'); ?></h3>
                        <p>Total Donasi Terkumpul</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5 my-4">
        <?php if($success && $donation_number): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h4 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i>Terima Kasih!</h4>
            <p class="mb-2">Donasi Anda telah kami terima dengan nomor: <strong><?php echo htmlspecialchars($donation_number); ?></strong></p>
            <hr>
            <p class="mb-0 small">Dana akan langsung digunakan untuk program "Berbagi Berkah". Pantau dampaknya di halaman utama.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row g-5">
            <!-- Informasi Program -->
            <div class="col-lg-7">
                <div class="fade-in-up">
                    <h5 class="text-uppercase fw-bold text-secondary mb-2">Kenapa Harus Donasi?</h5>
                    <h2 class="section-title mb-4 display-6">Kebaikan Kecil, <br>Dampak <span class="text-highlight">Besar.</span></h2>
                    <p class="text-muted fs-5 mb-4">
                        Kami menghubungkan donatur dengan masyarakat prasejahtera melalui agen lokal terverifikasi. Tidak ada uang yang terbuang, tidak ada makanan yang mubazir.
                    </p>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-6 fade-in-up">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                            <h5 class="fw-bold" style="color: var(--hs-green);">100% Transparan</h5>
                            <p class="text-muted small mb-0">Dana masuk ke Rekening Bersama Hungerswitch. Setiap rupiah tercatat rapi di sistem.</p>
                        </div>
                    </div>
                    <div class="col-md-6 fade-in-up">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                            <h5 class="fw-bold" style="color: var(--hs-green);">Agen Terverifikasi</h5>
                            <p class="text-muted small mb-0">Distribusi dilakukan oleh agen lokal yang memahami kondisi lapangan dan terlatih.</p>
                        </div>
                    </div>
                    <div class="col-md-6 fade-in-up">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-heart-pulse-fill"></i></div>
                            <h5 class="fw-bold" style="color: var(--hs-green);">Makanan Sehat</h5>
                            <p class="text-muted small mb-0">Kami membeli makanan bergizi dari mitra, bukan makanan sisa tak layak konsumsi.</p>
                        </div>
                    </div>
                    <div class="col-md-6 fade-in-up">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-lightning-charge-fill"></i></div>
                            <h5 class="fw-bold" style="color: var(--hs-green);">Langsung Salur</h5>
                            <p class="text-muted small mb-0">Dana yang terkumpul langsung digunakan untuk program distribusi makanan mingguan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Donasi -->
            <div class="col-lg-5">
                <div class="donation-wrapper fade-in-up">
                    <div class="donation-card">
                        <div class="form-content">
                            <div class="text-center mb-4">
                                <i class="bi bi-heart-fill heart-beat display-4 mb-2"></i>
                                <h3 class="fw-bold" style="color: var(--hs-green);">Mulai Berbagi</h3>
                                <p class="text-muted small">Pilih nominal donasi terbaikmu</p>
                            </div>

                            <form action="process_donation_v2.php" method="POST" id="donationForm">
                                <label class="fw-bold small text-muted mb-2">PILIH NOMINAL CEPAT</label>
                                <div class="nominal-grid">
                                    <div class="nominal-btn" onclick="setAmount(10000)">10rb</div>
                                    <div class="nominal-btn" onclick="setAmount(25000)">25rb</div>
                                    <div class="nominal-btn" onclick="setAmount(50000)">50rb</div>
                                    <div class="nominal-btn active" onclick="setAmount(100000)">100rb</div>
                                    <div class="nominal-btn" onclick="setAmount(250000)">250rb</div>
                                    <div class="nominal-btn" onclick="setAmount(500000)">500rb</div>
                                </div>

                                <div class="mb-4">
                                    <label class="fw-bold small text-muted mb-2">ATAU KETIK SENDIRI</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 fw-bold text-success">Rp</span>
                                        <input type="number" name="amount" id="customAmount" class="form-control form-control-lg border-start-0" value="100000" min="5000" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="d-flex align-items-center gap-2 p-3 border rounded-3 bg-light cursor-pointer">
                                        <input type="checkbox" name="is_anonymous" class="form-check-input" style="accent-color: var(--hs-green);">
                                        <small class="text-muted">Sembunyikan nama saya (Donasi Anonim)</small>
                                    </label>
                                </div>

                                <button type="submit" class="btn-donate-submit">
                                    SALURKAN SEKARANG <i class="bi bi-arrow-right-circle ms-2"></i>
                                </button>

                                <div class="text-center mt-3">
                                    <small class="text-muted opacity-75" style="font-size: 0.75rem;">
                                        <i class="bi bi-lock-fill"></i> Pembayaran Aman via Payment Gateway Terpercaya
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center py-4 text-muted small">
        &copy; 2025 Hungerswitch. Berbagi untuk Negeri.
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set Amount
        function setAmount(val) {
            document.getElementById('customAmount').value = val;
            document.querySelectorAll('.nominal-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        // Scroll Animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));
    </script>
</body>
</html>