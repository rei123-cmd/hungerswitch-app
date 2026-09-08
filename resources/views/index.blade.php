

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hungerswitch - Selamatkan Makanan, Bantu Sesama</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

        :root {
            --page-bg: #FCFCF8;
            --main-red: #D9232D;
            --main-green: #2F5233;
            --dark-text: #333;
            --info-box-bg: #FFF7F0;
            --light-beige: #F6F4EB;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--page-bg);
            color: var(--dark-text);
            overflow-x: hidden;
        }

        /* Navbar Enhanced */
        .navbar {
            padding: 1rem 0;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
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

        .navbar-brand .brand-text { line-height: 1.2; }
        .navbar-brand .brand-subtext { font-size: 0.75rem; color: #6c757d; }

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

        .navbar-nav .nav-link:hover {
            color: var(--main-red);
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        .btn-login-nav {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white !important;
            padding: 0.6rem 1.8rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(47, 82, 51, 0.2);
        }

        .btn-login-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(47, 82, 51, 0.3);
        }

        /* Hero Section - Modern 2025 */
        .hero-section {
            padding-top: 140px;
            padding-bottom: 100px;
            min-height: 95vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #FCFCF8 0%, #f8f6f1 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: radial-gradient(circle, rgba(217, 35, 45, 0.05) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease;
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

        .hero-subtitle {
            font-size: 1.25rem;
            color: #555;
            margin-bottom: 2.5rem;
            line-height: 1.8;
            animation: fadeInUp 0.8s ease 0.2s backwards;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.4s backwards;
        }

        .btn-hero {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-hero::before {
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

        .btn-hero:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-green-hero {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white;
            box-shadow: 0 8px 25px rgba(47, 82, 51, 0.3);
        }

        .btn-green-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(47, 82, 51, 0.4);
        }

        .btn-outline-red-hero {
            background: transparent;
            color: var(--main-red);
            border: 2px solid var(--main-red);
        }

        .btn-outline-red-hero:hover {
            background: var(--main-red);
            color: white;
            transform: translateY(-3px);
        }

        /* Hero Image Box - Interactive */
        .hero-image-box {
            position: relative;
            animation: fadeInRight 0.8s ease 0.6s backwards;
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

        .info-box {
            background: linear-gradient(135deg, #ffffff, var(--info-box-bg));
            border-radius: 30px;
            padding: 4rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 2px solid rgba(217, 35, 45, 0.1);
        }

        .info-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.15);
        }

        .info-box-icon {
            color: var(--main-red);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Floating Elements */
        .floating-element {
            position: absolute;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .float-1 {
            top: 20%;
            left: 5%;
            font-size: 3rem;
            opacity: 0.15;
            animation-delay: 0s;
        }

        .float-2 {
            top: 60%;
            left: 10%;
            font-size: 2rem;
            opacity: 0.1;
            animation-delay: 1s;
        }

        .float-3 {
            top: 40%;
            right: 15%;
            font-size: 2.5rem;
            opacity: 0.12;
            animation-delay: 2s;
        }

        /* Stats Section - Animated Counters */
        .stats-section {
            padding: 5rem 0;
            background: white;
            position: relative;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff, var(--light-beige));
            border-radius: 25px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(217, 35, 45, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(217, 35, 45, 0.1), transparent);
            transition: left 0.5s;
        }

        .stat-card:hover::before {
            left: 100%;
        }

        .stat-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 60px rgba(217, 35, 45, 0.15);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.15rem;
            color: #666;
            font-weight: 500;
        }

        /* How It Works - Card Design */
        .how-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #FCFCF8 0%, #ffffff 100%);
        }

        .section-title {
            font-weight: 800;
            font-size: 2.8rem;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 4rem;
        }

        .how-card {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .how-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--main-green), var(--main-red));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .how-card:hover::before {
            transform: scaleX(1);
        }

        .how-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border-color: var(--main-red);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .icon-box.bg-green {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white;
        }

        .icon-box.bg-red {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
        }

        .how-card h3 {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .how-card p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .card-link {
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: gap 0.3s ease;
        }

        .card-link:hover {
            gap: 1rem;
        }

        .card-link.link-green { color: var(--main-green); }
        .card-link.link-red { color: var(--main-red); }

        /* Testimonials Section - NEW */
        .testimonials-section {
            padding: 6rem 0;
            background: white;
            position: relative;
        }

        .testimonial-card {
            background: linear-gradient(135deg, #ffffff, var(--light-beige));
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid rgba(217, 35, 45, 0.05);
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(217, 35, 45, 0.15);
        }

        .testimonial-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 20px rgba(217, 35, 45, 0.3);
        }

        .testimonial-text {
            font-size: 1.05rem;
            color: #555;
            line-height: 1.8;
            margin-bottom: 1.5rem;
            font-style: italic;
        }

        .testimonial-author {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark-text);
            margin-bottom: 0.25rem;
        }

        .testimonial-role {
            color: #888;
            font-size: 0.9rem;
        }

        .rating {
            color: #ffc107;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        /* Partners Section - NEW */
        .partners-section {
            padding: 4rem 0;
            background: var(--light-beige);
        }

        .partner-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            align-items: center;
        }

        .partner-logo {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            font-weight: 700;
            color: #888;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .partner-logo:hover {
            transform: scale(1.05);
            border-color: var(--main-red);
            color: var(--main-red);
        }

        /* FAQ Section - NEW */
        .faq-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #FCFCF8 0%, #ffffff 100%);
        }

        .faq-item {
            background: white;
            border-radius: 15px;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            box-shadow: 0 8px 25px rgba(217, 35, 45, 0.1);
        }

        .faq-question {
            padding: 1.5rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            color: var(--main-red);
            background: rgba(217, 35, 45, 0.02);
        }

        .faq-icon {
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 2rem;
            color: #666;
            line-height: 1.8;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 0 2rem 1.5rem 2rem;
        }

        /* CTA Section - Enhanced */
        .cta-section {
            background: linear-gradient(135deg, var(--main-green), #243f27);
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
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

        .cta-title {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3rem;
        }

        /* Footer - Modern */
        footer {
            background: #1a1a1a;
            color: #ddd;
            padding: 4rem 0 2rem;
        }

        footer h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        footer a {
            color: #aaa;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--main-red);
        }

        footer .social-links a {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        footer .social-links a:hover {
            background: var(--main-red);
            color: white;
            transform: translateY(-5px);
        }

        /* Scroll to Top Button - Enhanced */
        .scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(217, 35, 45, 0.3);
            z-index: 1000;
        }

        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(217, 35, 45, 0.4);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .hero-title { font-size: 2.5rem; }
            .hero-subtitle { font-size: 1.1rem; }
            .stat-number { font-size: 2.5rem; }
            .section-title { font-size: 2rem; }
            .cta-title { font-size: 2rem; }
            .hero-buttons { justify-content: center; }
            .hero-section { text-align: center; padding-top: 120px; }
        }

        /* Loading Animation */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--page-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loader-icon {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(217, 35, 45, 0.2);
            border-top-color: var(--main-red);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
        to { transform: rotate(360deg); }
}

    </style>

    <script src="cookies_consent.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-icon"></div>
    </div>
        @include('navbar')

    <!-- Navbar - TANPA KERANJANG -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-logo-icon">HS</span>
                <div class="brand-text ms-2">
                    <strong>Hungerswitch</strong>
                    <div class="brand-subtext">Selamatkan Makanan</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="marketplace.php">Marketplace</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="donasi.php">
                            <i class="bi bi-heart-fill me-1"></i> Donasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="untmitra.php">Untuk Mitra</a>
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
                                <li><a class="dropdown-item" href="my_orders.php"><i class="bi bi-box me-2"></i>Pesanan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn-login-nav" href="login_unified.php">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <!-- Floating Elements -->
        <i class="bi bi-heart-fill floating-element float-1"></i>
        <i class="bi bi-box-seam-fill floating-element float-2"></i>
        <i class="bi bi-people-fill floating-element float-3"></i>

        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h1 class="hero-title">Selamatkan Makanan, Bantu Sesama</h1>
                    <p class="hero-subtitle">
                        Platform inovatif yang menghubungkan makanan surplus berkualitas dari UMKM dan restoran dengan mereka yang membutuhkan. Belanja hemat atau donasi untuk membuat dampak nyata!
                    </p>
                    <div class="hero-buttons">
                        <a href="marketplace.php" class="btn btn-hero btn-green-hero">
                            Jelajahi Marketplace <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="donasi.php" class="btn btn-hero btn-outline-red-hero">
                            <i class="bi bi-heart-fill me-2"></i> Mulai Donasi
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 hero-image-box">
                    <div class="info-box text-center">
                        <i class="bi bi-box-seam-fill display-1 info-box-icon"></i>
                        <h3 class="mt-4 fw-bold">Makanan Berkualitas</h3>
                        <p class="fs-5 text-muted mb-0">Harga Terjangkau, Dampak Luar Biasa</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section with Animated Counters -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number" data-target="50000">0</div>
                        <div class="stat-label">Porsi Terselamatkan</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number" data-target="2000">0</div>
                        <div class="stat-label">Mitra Aktif</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number" data-target="10000">0</div>
                        <div class="stat-label">Orang Terbantu</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number" data-target="25">0</div>
                        <div class="stat-label">Ton CO₂ Dikurangi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Bagaimana Hungerswitch Bekerja</h2>
                <p class="section-subtitle">Dua cara mudah untuk membuat dampak positif</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="how-card">
                        <div class="icon-box bg-green">
                            <i class="bi bi-cart3"></i>
                        </div>
                        <h3>Pasar Surplus</h3>
                        <p>
                            Beli makanan berkualitas premium dari UMKM dan restoran terpercaya dengan diskon hingga 70%. Setiap pembelian Anda tidak hanya menghemat uang, tetapi juga menyelamatkan makanan dari pemborosan dan mendukung bisnis lokal.
                        </p>
                        <a href="marketplace.php" class="card-link link-green">
                            Belanja Sekarang <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="how-card">
                        <div class="icon-box bg-red">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <h3>Berbagi Berkah</h3>
                        <p>
                            Donasi untuk membeli makanan surplus yang tidak terjual dan distribusikan langsung ke komunitas yang membutuhkan. Sistem transparan kami memastikan setiap rupiah donasi Anda membuat dampak nyata bagi mereka yang kurang beruntung.
                        </p>
                        <a href="donasi.php" class="card-link link-red">
                            Donasi Sekarang <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Apa Kata Mereka</h2>
                <p class="section-subtitle">Testimoni dari pengguna dan mitra kami</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="testimonial-avatar">AS</div>
                        <p class="testimonial-text">
                            "Hungerswitch benar-benar mengubah cara kami mengurangi food waste. Makanan surplus kami sekarang sampai ke tangan yang tepat, dan penjualan meningkat 40%!"
                        </p>
                        <div class="testimonial-author">Ahmad Suryadi</div>
                        <div class="testimonial-role">Owner, Bakery Artisan</div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="testimonial-avatar">SL</div>
                        <p class="testimonial-text">
                            "Saya bisa belanja makanan sehat berkualitas dengan harga terjangkau. Plus, saya merasa berkontribusi mengurangi pemborosan makanan. Win-win solution!"
                        </p>
                        <div class="testimonial-author">Siti Laila</div>
                        <div class="testimonial-role">Pelanggan Setia</div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="testimonial-avatar">RH</div>
                        <p class="testimonial-text">
                            "Sebagai agen distribusi, Hungerswitch memudahkan saya menyalurkan bantuan makanan ke komunitas yang membutuhkan dengan sistem yang sangat efisien."
                        </p>
                        <div class="testimonial-author">Rudi Hartono</div>
                        <div class="testimonial-role">Agen Tangerang</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Dipercaya Oleh</h2>
                <p class="section-subtitle">Mitra dan organisasi yang bekerja sama dengan kami</p>
            </div>

            <div class="partner-grid">
                <div class="partner-logo">
                    <i class="bi bi-shop-window fs-1 d-block mb-2"></i>
                    <div>500+ UMKM</div>
                </div>
                <div class="partner-logo">
                    <i class="bi bi-building fs-1 d-block mb-2"></i>
                    <div>200+ Restoran</div>
                </div>
                <div class="partner-logo">
                    <i class="bi bi-cup-hot fs-1 d-block mb-2"></i>
                    <div>150+ Cafe</div>
                </div>
                <div class="partner-logo">
                    <i class="bi bi-house-heart fs-1 d-block mb-2"></i>
                    <div>80+ LSM</div>
                </div>
                <div class="partner-logo">
                    <i class="bi bi-hospital fs-1 d-block mb-2"></i>
                    <div>45+ Panti</div>
                </div>
                <div class="partner-logo">
                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                    <div>120+ Komunitas</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Pertanyaan Umum</h2>
                <p class="section-subtitle">Jawaban untuk pertanyaan yang sering ditanyakan</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Apa itu Hungerswitch?</span>
                            <i class="bi bi-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            Hungerswitch adalah platform digital yang menghubungkan makanan surplus berkualitas dari UMKM dan restoran dengan konsumen yang mencari harga terjangkau, sekaligus membantu mendistribusikan makanan ke komunitas yang membutuhkan melalui sistem donasi.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Bagaimana cara berbelanja di marketplace?</span>
                            <i class="bi bi-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            Cukup daftar atau login, pilih produk yang Anda inginkan dari daftar marketplace, tambahkan ke keranjang, dan lakukan pembayaran. Anda bisa mengambil pesanan langsung di lokasi mitra (pick-up) sesuai jadwal yang ditentukan.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Apakah makanan surplus masih layak konsumsi?</span>
                            <i class="bi bi-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            Ya, sangat layak! Makanan surplus adalah makanan berkualitas tinggi yang mendekati tanggal kedaluwarsa atau tidak terjual pada hari itu. Semua mitra kami telah terverifikasi dan mengikuti standar keamanan pangan yang ketat.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Kemana donasi saya disalurkan?</span>
                            <i class="bi bi-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            Donasi Anda digunakan untuk membeli makanan surplus dan didistribusikan langsung ke komunitas prasejahtera yang terdaftar, seperti panti asuhan, rumah singgah, dan kelompok buruh harian. Kami menyediakan laporan transparan untuk setiap donasi.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Bagaimana cara menjadi mitra Hungerswitch?</span>
                            <i class="bi bi-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            Kunjungi halaman "Untuk Mitra", isi formulir pendaftaran, dan tim kami akan memverifikasi bisnis Anda. Setelah disetujui, Anda bisa langsung mulai posting produk surplus dan mengurangi pemborosan makanan sambil meningkatkan pendapatan.
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFAQ(this)">
                            <span>Apa keuntungan menjadi agen distribusi?</span>
                            <i class="bi bi-chevron-down faq-icon"></i>
                        </div>
                        <div class="faq-answer">
                            Sebagai agen, Anda berperan penting dalam mendistribusikan makanan ke komunitas lokal, mendapatkan akses ke dashboard manajemen yang lengkap, mendapat dukungan operasional dari tim kami, dan menjadi bagian dari gerakan sosial yang berdampak nyata.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section text-center text-white">
        <div class="container position-relative">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h2 class="cta-title">Bergabunglah dengan Gerakan Kami</h2>
                    <p class="cta-subtitle">
                        Bersama kita bisa mengatasi pemborosan makanan dan kelaparan. Setiap langkah kecil membuat perbedaan besar. Mulai dari sekarang!
                    </p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="marketplace.php" class="btn btn-hero" style="background: white; color: var(--main-green);">
                            Jelajahi Marketplace
                        </a>
                        <a href="donasi.php" class="btn btn-hero" style="background: transparent; color: white; border: 2px solid white;">
                            Mulai Donasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
   <footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>Hungerswitch</h5>
                <p class="text-white-50">Selamatkan makanan, bantu sesama. Platform untuk mengurangi limbah makanan dan membantu komunitas.</p>
                <div class="social-links d-flex gap-2 mt-3">
                    <a href="#"><i class="bi bi-twitter"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h5>Navigasi</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="marketplace.php" class="nav-link p-0">Marketplace</a></li>
                    <li class="nav-item mb-2"><a href="donasi.php" class="nav-link p-0">Donasi</a></li>
                    <li class="nav-item mb-2"><a href="untmitra.php" class="nav-link p-0">Untuk Mitra</a></li>
                  <li class="nav-item mb-2"><a href="agent_dashboard.php" class="nav-link p-0">Agen</a>
</li>
                </ul>
            </div>
            <div class="col-md-2 mb-4">
                <h5>Legal</h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="terms_of_service.php" class="nav-link p-0">Syarat & Ketentuan</a></li>
                    <li class="nav-item mb-2"><a href="privacy_policy.php" class="nav-link p-0">Kebijakan Privasi</a></li>
                    <li class="nav-item mb-2"><a href="cookies_policy.php" class="nav-link p-0">Kebijakan Cookies</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Kontak</h5>
                <p class="text-white-50 mb-2">
                    <i class="bi bi-envelope me-2"></i>support@hungerswitch.com
                </p>
                <p class="text-white-50 mb-2">
                    <i class="bi bi-phone me-2"></i>+62 812-3456-7890
                </p>
                <p class="text-white-50">
                    <i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia
                </p>
            </div>
        </div>
        <hr class="text-white-50 my-4">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-white-50 mb-0">&copy; 2025 Hungerswitch. Semua Hak Dilindungi.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="privacy_policy.php" class="me-3">Privacy Policy</a>
                <a href="terms_of_service.php" class="me-3">Terms of Service</a>
                <a href="cookies_policy.php">Cookies</a>
            </div>
        </div>
    </div>
</footer>

    <!-- Scroll to Top Button -->
    <a href="#" id="scrollTopBtn" class="scroll-to-top">
        <i class="bi bi-arrow-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Page Loader
        window.addEventListener('load', function() {
            document.getElementById('pageLoader').classList.add('hidden');
        });

        // Navbar Scroll Effect
        const navbar = document.getElementById('mainNav');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Animated Counter
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    element.textContent = target.toLocaleString() + '+';
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
        }

        // Intersection Observer for Counters
        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.stat-number');
                    counters.forEach(counter => {
                        if (counter.textContent === '0') {
                            animateCounter(counter);
                        }
                    });
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // FAQ Toggle
        function toggleFAQ(element) {
            const faqItem = element.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Open clicked FAQ if it wasn't active
            if (!isActive) {
                faqItem.classList.add('active');
            }
        }

        // Scroll to Top Button
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        });

        scrollTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Smooth Scroll for Anchor Links
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

        // Add scroll animation for elements
        const animateOnScroll = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        // Apply animation to cards
        document.querySelectorAll('.how-card, .testimonial-card, .stat-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            animateOnScroll.observe(el);
        });
    </script>
</body>
</html>