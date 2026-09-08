<?php 
//session_start();
include 'config.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Untuk Mitra - Hungerswitch</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
 
        :root {
            --page-bg: #FCFCF8;
            --main-red: #D9232D;
            --main-green: #2F5233;
            --dark-text: #333;
            --light-beige: #F6F4EB;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--page-bg);
            color: var(--dark-text);
            overflow-x: hidden;
        }

        /* Navbar - Sama dengan index */
        .navbar {
            padding: 1rem 0;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .navbar-brand .brand-logo-icon {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.3);
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover .brand-logo-icon {
            transform: scale(1.05) rotate(-5deg);
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--dark-text);
            margin: 0 0.5rem;
            position: relative;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: var(--main-red);
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        /* Hero dengan Animasi */
        .mitra-hero {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            padding: 8rem 0 5rem 0;
            margin-top: 70px;
            color: var(--light-beige);
            position: relative;
            overflow: hidden;
        }

        .mitra-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .mitra-hero-content {
            position: relative;
            z-index: 1;
        }

        .mitra-hero-title {
            font-weight: 800;
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s ease;
        }

        .mitra-hero-subtitle {
            font-size: 1.2rem;
            color: rgba(246, 244, 235, 0.9);
            line-height: 1.8;
            animation: fadeInUp 0.8s ease 0.2s backwards;
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

        .mitra-hero-box {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 25px;
            padding: 3rem 2rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            animation: fadeInRight 0.8s ease 0.4s backwards;
            transition: all 0.4s ease;
        }

        .mitra-hero-box:hover {
            background: rgba(0, 0, 0, 0.3);
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .mitra-hero-box i {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Buttons dengan Animasi */
        .btn-cta {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease 0.6s backwards;
        }

        .btn-cta::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-cta:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        /* Cards dengan Animasi */
        .keuntungan-card, .cara-kerja-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            border: 2px solid transparent;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            height: 100%;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            transform: translateY(30px);
        }

        .keuntungan-card.animate, .cara-kerja-card.animate {
            animation: cardSlideUp 0.6s ease forwards;
        }

        @keyframes cardSlideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .keuntungan-card:hover, .cara-kerja-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 15px 50px rgba(47, 82, 51, 0.2);
            border-color: var(--main-green);
        }

        .keuntungan-card-icon {
            font-size: 3rem;
            color: var(--main-red);
            margin-bottom: 1.5rem;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .keuntungan-card:hover .keuntungan-card-icon {
            transform: scale(1.2) rotate(10deg);
        }

        .cara-kerja-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 20px rgba(217, 35, 45, 0.3);
            transition: all 0.3s ease;
        }

        .cara-kerja-card:hover .cara-kerja-number {
            transform: scale(1.2) rotate(360deg);
        }

        /* Stats dengan Counter Animation */
        .stat-card {
            background: linear-gradient(135deg, #ffffff, var(--light-beige));
            border-radius: 25px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            opacity: 0;
            transform: scale(0.9);
        }

        .stat-card.animate {
            animation: statPop 0.6s ease forwards;
        }

        @keyframes statPop {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 60px rgba(217, 35, 45, 0.15);
        }

        .stat-card h3 {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        /* Section Titles */
        .section-title {
            font-weight: 800;
            font-size: 2.8rem;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 3rem;
            opacity: 0;
            transform: translateY(-20px);
        }

        .section-title.animate {
            animation: titleFade 0.6s ease forwards;
        }

        @keyframes titleFade {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* CTA Section */
        .cta-mitra-section {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            padding: 6rem 0;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .cta-mitra-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
        }

        /* Scroll Animations */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.6s ease;
        }

        .scroll-reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Stagger delays untuk cards */
        .keuntungan-card:nth-child(1).animate { animation-delay: 0.1s; }
        .keuntungan-card:nth-child(2).animate { animation-delay: 0.2s; }
        .keuntungan-card:nth-child(3).animate { animation-delay: 0.3s; }
        .keuntungan-card:nth-child(4).animate { animation-delay: 0.4s; }

        .cara-kerja-card:nth-child(1).animate { animation-delay: 0.1s; }
        .cara-kerja-card:nth-child(2).animate { animation-delay: 0.2s; }
        .cara-kerja-card:nth-child(3).animate { animation-delay: 0.3s; }
        .cara-kerja-card:nth-child(4).animate { animation-delay: 0.4s; }

        .stat-card:nth-child(1).animate { animation-delay: 0.1s; }
        .stat-card:nth-child(2).animate { animation-delay: 0.2s; }
        .stat-card:nth-child(3).animate { animation-delay: 0.3s; }

        @media (max-width: 991.98px) {
            .mitra-hero-title { font-size: 2.5rem; }
            .section-title { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-logo-icon">HS</span>
                <div class="brand-text ms-2">
                    <strong>Hungerswitch</strong>
                    <div class="brand-subtext" style="font-size: 0.75rem; color: #6c757d;">Untuk Mitra</div>
                </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="marketplace.php">Marketplace</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="donasi.php">
                            <i class="bi bi-heart-fill me-1"></i> Donasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="untmitra.php">Untuk Mitra</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="agent_dashboard.php">Agen</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-3">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-2"></i>
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="bi bi-box me-2"></i>Pesanan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login_unified.php" style="background: linear-gradient(135deg, var(--main-green), #3d6b42); color: white; padding: 0.6rem 1.8rem; border-radius: 25px; font-weight: 600;">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="mitra-hero">
        <div class="container">
            <div class="row align-items-center g-5 mitra-hero-content">
                <div class="col-lg-7">
                    <h1 class="mitra-hero-title">Bergabung Sebagai Mitra Hungerswitch</h1>
                    <p class="mitra-hero-subtitle">
                        Kurangi pemborosan makanan, tingkatkan penjualan, dan buat dampak
                        sosial. Hungerswitch membantu UMKM dan restoran Anda
                        berkembang sambil membantu komunitas.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="login.php" class="btn btn-cta" style="background: var(--light-beige); color: var(--main-green);">
                            Daftar Sekarang
                        </a>
                        <a href="#" class="btn btn-cta" style="background: transparent; color: white; border: 2px solid white;">
                            Lihat Dashboard
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="mitra-hero-box text-center">
                        <div class="ratio ratio-16x9">
                        <iframe 
                        width="560" 
                        height="315" 
                        src="https://www.youtube.com/embed/7D3uAeHXe6w?si=BeJaETgeNHGSRx4F" 
                        title="YouTube video player" frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        referrerpolicy="strict-origin-when-cross-origin" 
                        allowfullscreen>
                         </iframe>
                   </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="keuntungan" class="py-5 mt-5">
            <div class="container">
                <h2 class="section-title text-center scroll-reveal">Keuntungan Menjadi Mitra</h2>
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="keuntungan-card scroll-reveal">
                            <i class="bi bi-graph-up-arrow keuntungan-card-icon"></i>
                            <h5 class="keuntungan-card-title">Tingkatkan Penjualan</h5>
                            <p class="keuntungan-card-text">Jangkau ribuan pelanggan baru yang mencari makanan berkualitas dengan harga terjangkau.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="keuntungan-card scroll-reveal">
                            <i class="bi bi-recycle keuntungan-card-icon"></i>
                            <h5 class="keuntungan-card-title">Kurangi Pemborosan</h5>
                            <p class="keuntungan-card-text">Jual makanan surplus Anda dengan cepat dan kurangi limbah hingga 80%.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="keuntungan-card scroll-reveal">
                            <i class="bi bi-people keuntungan-card-icon"></i>
                            <h5 class="keuntungan-card-title">Dampak Sosial</h5>
                            <p class="keuntungan-card-text">Bantu komunitas prasejahtera sekaligus mengembangkan bisnis Anda.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="keuntungan-card scroll-reveal">
                            <i class="bi bi-bar-chart-line keuntungan-card-icon"></i>
                            <h5 class="keuntungan-card-title">Analisis Lengkap</h5>
                            <p class="keuntungan-card-text">Periksa penjualan dan performa bisnis Anda secara real-time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="cara-kerja" class="py-5 mt-4">
            <div class="container">
                <h2 class="section-title text-center scroll-reveal">Cara Kerjanya</h2>
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="cara-kerja-card scroll-reveal">
                            <span class="cara-kerja-number">1</span>
                            <h5 class="cara-kerja-title">Daftar</h5>
                            <p class="cara-kerja-text">Isi formulir pendaftaran dan verifikasi data Anda.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="cara-kerja-card scroll-reveal">
                            <span class="cara-kerja-number">2</span>
                            <h5 class="cara-kerja-title">Posting Produk</h5>
                            <p class="cara-kerja-text">Tambahkan makanan surplus Anda ke marketplace.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="cara-kerja-card scroll-reveal">
                            <span class="cara-kerja-number">3</span>
                            <h5 class="cara-kerja-title">Terima Pesanan</h5>
                            <p class="cara-kerja-text">Kelola pesanan dan pengambilan mandiri (pick-up).</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="cara-kerja-card scroll-reveal">
                            <span class="cara-kerja-number">4</span>
                            <h5 class="cara-kerja-title">Dapatkan Hasil</h5>
                            <p class="cara-kerja-text">Terima pembayaran dan lihat laporan bisnis Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="mitra-berkembang" class="py-5 mt-5">
            <div class="container">
                <h2 class="section-title text-center scroll-reveal">Mitra Kami Berkembang</h2>
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="stat-card scroll-reveal">
                            <h3 class="counter" data-target="2000">0</h3>
                            <strong style="font-size: 1.2rem; display: block; margin-bottom: 0.5rem;">Mitra Aktif</strong>
                            <p style="color: #666;">Dari UMKM hingga restoran besar</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="stat-card scroll-reveal">
                            <h3 class="counter" data-target="50000">0</h3>
                            <strong style="font-size: 1.2rem; display: block; margin-bottom: 0.5rem;">Porsi Terjual</strong>
                            <p style="color: #666;">Makanan berkualitas terselamatkan</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="stat-card scroll-reveal">
                            <h3><span class="counter" data-target="2500000000">0</span></h3>
                            <strong style="font-size: 1.2rem; display: block; margin-bottom: 0.5rem;">Penjualan Total</strong>
                            <p style="color: #666;">Pendapatan untuk mitra kami</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <section class="cta-mitra-section text-center">
        <div class="container position-relative" style="z-index: 1;">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h2 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Siap Bergabung?</h2>
                    <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">
                        Daftar hari ini dan mulai mengurangi pemborosan sambil meningkatkan penjualan bisnis Anda.
                    </p>
                    <a href="login.php" class="btn btn-cta" style="background: white; color: var(--main-red);">
                        <i class="bi bi-rocket-takeoff me-2"></i>Mulai Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Hungerswitch</h5>
                    <p class="text-white-50">Selamatkan makanan, bantu sesama.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Navigasi</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="marketplace.php" class="nav-link p-0 text-white-50">Marketplace</a></li>
                        <li class="nav-item mb-2"><a href="donasi.php" class="nav-link p-0 text-white-50">Donasi</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Kontak</h5>
                    <p class="text-white-50">support@hungerswitch.com</p>
                </div>
            </div>
            <hr class="text-white-50">
            <p class="text-center text-white-50 mb-0">&copy; 2025 Hungerswitch</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Scroll reveal animation
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active', 'animate');
                    
                    // Counter animation
                    if (entry.target.classList.contains('stat-card')) {
                        const counter = entry.target.querySelector('.counter');
                        if (counter && counter.textContent === '0') {
                            animateCounter(counter);
                        }
                    }
                }
            });
        }, observerOptions);

        // Observe all scroll-reveal elements
        document.querySelectorAll('.scroll-reveal').forEach(el => {
            observer.observe(el);
        });

        // Counter animation
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    element.textContent = formatNumber(target);
                    clearInterval(timer);
                } else {
                    element.textContent = formatNumber(Math.floor(current));
                }
            }, 16);
        }

        function formatNumber(num) {
            if (num >= 1000000000) {
                return 'Rp ' + (num / 1000000000).toFixed(1) + 'B';
            } else if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(0) + 'K';
            }
            return num.toLocaleString('id-ID');
        }

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href !== '#!') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>