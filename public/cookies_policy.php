<?php 
//session_start();
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Cookies - Hungerswitch</title>
    
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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--page-bg);
            color: var(--dark-text);
            padding-top: 76px;
        }

        .navbar {
            padding: 1rem 0;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .navbar-brand .brand-logo-icon {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.3);
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

        .navbar-nav .nav-link:hover {
            color: var(--main-red);
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        .navbar-nav .nav-link.active {
            color: var(--main-red);
        }

        .legal-header {
            background: linear-gradient(135deg, #FF6B35, #ff4757);
            padding: 4rem 0 3rem 0;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .legal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: moveGrid 15s linear infinite;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }

        .legal-content {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            margin-top: -2rem;
            margin-bottom: 3rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
        }

        .legal-content h2 {
            color: var(--main-red);
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .legal-content h3 {
            color: #ff4757;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .legal-content p, .legal-content li {
            line-height: 1.8;
            color: #555;
        }

        .legal-content ul {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .last-updated {
            background: var(--light-beige);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border-left: 4px solid #ff4757;
        }

        .cookie-type-card {
            background: linear-gradient(135deg, #fff5f5, #ffffff);
            border-left: 4px solid #ff4757;
            padding: 1.5rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            transition: all 0.3s ease;
        }

        .cookie-type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.15);
        }

        .cookie-type-card h3 {
            margin-top: 0;
        }

        .cookie-table {
            width: 100%;
            margin: 1rem 0;
            border-collapse: collapse;
        }

        .cookie-table th {
            background: var(--light-beige);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .cookie-table td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .cookie-table tr:hover {
            background: #fafafa;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff5e6, #ffffff);
            border-left: 4px solid #ff4757;
            border: 1px solid rgba(255, 107, 53, 0.2);
        }

        .alert-info {
            background: linear-gradient(135deg, #e8f4fd, #ffffff);
            border-left: 4px solid var(--main-red);
            border: 1px solid rgba(217, 35, 45, 0.2);
        }

        .alert-primary {
            background: linear-gradient(135deg, #fff5f5, #ffffff);
            border-left: 4px solid var(--main-red);
            border: 1px solid rgba(217, 35, 45, 0.2);
        }

        .alert-success {
            background: linear-gradient(135deg, #f0fdf4, #ffffff);
            border-left: 4px solid var(--main-green);
            border: 1px solid rgba(47, 82, 51, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(217, 35, 45, 0.3);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-logo-icon">HS</span>
                <div class="brand-text ms-2">
                    <strong>Hungerswitch</strong>
                    <div class="brand-subtext" style="font-size: 0.75rem; color: #6c757d;">Kebijakan Cookies</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="terms_of_service.php">Syarat & Ketentuan</a></li>
                    <li class="nav-item"><a class="nav-link" href="privacy_policy.php">Kebijakan Privasi</a></li>
                    <li class="nav-item"><a class="nav-link active" href="cookies_policy.php">Kebijakan Cookies</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="legal-header">
        <div class="container text-center position-relative" style="z-index: 1;">
            <h1 style="font-size: 2.5rem; font-weight: 800;">🍪 Kebijakan Cookies</h1>
            <p style="font-size: 1.1rem; opacity: 0.9;">Cookies Policy - Hungerswitch</p>
        </div>
    </div>

    <div class="container">
        <div class="legal-content">
            <div class="last-updated">
                <i class="bi bi-calendar-check me-2"></i>
                <strong>Terakhir diperbarui:</strong> 20 November 2025
            </div>

            <section id="intro">
                <h2>Apa itu Cookies?</h2>
                <p>Cookies adalah file teks kecil yang disimpan di perangkat Anda (komputer, tablet, atau smartphone) saat Anda mengunjungi website. Cookies membantu website mengingat informasi tentang kunjungan Anda, seperti preferensi bahasa dan pengaturan lainnya.</p>
                <p>Hungerswitch menggunakan cookies dan teknologi serupa untuk meningkatkan pengalaman Anda, menjaga keamanan Platform, dan menyediakan layanan yang lebih personal.</p>
            </section>

            <section id="types">
                <h2>Jenis Cookies yang Kami Gunakan</h2>

                <div class="cookie-type-card">
                    <h3><i class="bi bi-shield-check me-2"></i>1. Cookies yang Diperlukan (Essential Cookies)</h3>
                    <p>Cookies ini sangat penting untuk fungsi dasar website dan tidak dapat dinonaktifkan.</p>
                    <table class="cookie-table">
                        <thead>
                            <tr>
                                <th>Nama Cookie</th>
                                <th>Tujuan</th>
                                <th>Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>PHPSESSID</code></td>
                                <td>Menjaga sesi login Anda</td>
                                <td>Sesi browser</td>
                            </tr>
                            <tr>
                                <td><code>hs_cart</code></td>
                                <td>Menyimpan item di keranjang belanja</td>
                                <td>7 hari</td>
                            </tr>
                            <tr>
                                <td><code>csrf_token</code></td>
                                <td>Keamanan dan perlindungan CSRF</td>
                                <td>Sesi browser</td>
                            </tr>
                            <tr>
                                <td><code>cookie_consent</code></td>
                                <td>Menyimpan preferensi cookie Anda</td>
                                <td>1 tahun</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="cookie-type-card">
                    <h3><i class="bi bi-graph-up me-2"></i>2. Cookies Analitik (Analytics Cookies)</h3>
                    <p>Membantu kami memahami bagaimana pengguna berinteraksi dengan Platform.</p>
                    <table class="cookie-table">
                        <thead>
                            <tr>
                                <th>Nama Cookie</th>
                                <th>Tujuan</th>
                                <th>Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>_ga</code></td>
                                <td>Google Analytics - mengidentifikasi pengguna unik</td>
                                <td>2 tahun</td>
                            </tr>
                            <tr>
                                <td><code>_gid</code></td>
                                <td>Google Analytics - mengidentifikasi pengguna unik</td>
                                <td>24 jam</td>
                            </tr>
                            <tr>
                                <td><code>_gat</code></td>
                                <td>Google Analytics - throttle request rate</td>
                                <td>1 menit</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="cookie-type-card">
                    <h3><i class="bi bi-palette me-2"></i>3. Cookies Fungsional (Functional Cookies)</h3>
                    <p>Menyimpan preferensi Anda untuk pengalaman yang lebih personal.</p>
                    <table class="cookie-table">
                        <thead>
                            <tr>
                                <th>Nama Cookie</th>
                                <th>Tujuan</th>
                                <th>Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>user_preferences</code></td>
                                <td>Menyimpan preferensi bahasa, mata uang, dll</td>
                                <td>30 hari</td>
                            </tr>
                            <tr>
                                <td><code>filter_settings</code></td>
                                <td>Menyimpan filter marketplace yang Anda pilih</td>
                                <td>7 hari</td>
                            </tr>
                            <tr>
                                <td><code>location_data</code></td>
                                <td>Menyimpan lokasi untuk rekomendasi mitra terdekat</td>
                                <td>30 hari</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="cookie-type-card">
                    <h3><i class="bi bi-bullseye me-2"></i>4. Cookies Marketing (Marketing Cookies)</h3>
                    <p>Digunakan untuk menampilkan iklan yang relevan dengan minat Anda.</p>
                    <table class="cookie-table">
                        <thead>
                            <tr>
                                <th>Nama Cookie</th>
                                <th>Tujuan</th>
                                <th>Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>_fbp</code></td>
                                <td>Facebook Pixel - tracking dan retargeting</td>
                                <td>90 hari</td>
                            </tr>
                            <tr>
                                <td><code>ads_preferences</code></td>
                                <td>Menyimpan preferensi iklan</td>
                                <td>1 tahun</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="manage">
                <h2>Cara Mengelola Cookies</h2>
                
                <h3>Melalui Browser</h3>
                <p>Anda dapat mengontrol dan menghapus cookies melalui pengaturan browser:</p>
                <ul>
                    <li><strong>Google Chrome:</strong> Settings > Privacy and security > Cookies and other site data</li>
                    <li><strong>Mozilla Firefox:</strong> Options > Privacy & Security > Cookies and Site Data</li>
                    <li><strong>Safari:</strong> Preferences > Privacy > Manage Website Data</li>
                    <li><strong>Microsoft Edge:</strong> Settings > Cookies and site permissions</li>
                </ul>

                <h3>Melalui Platform Kami</h3>
                <div class="alert alert-warning">
                    <i class="bi bi-gear me-2"></i>
                    <strong>Pengaturan Cookie</strong>
                    <p class="mb-2 mt-2">Anda dapat mengatur preferensi cookies Anda di sini:</p>
                    <button class="btn btn-primary" onclick="openCookieSettings()">
                        <i class="bi bi-sliders me-2"></i>Kelola Preferensi Cookie
                    </button>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Perhatian:</strong> Menonaktifkan cookies tertentu dapat mempengaruhi fungsionalitas Platform. Cookies yang diperlukan (essential cookies) tidak dapat dinonaktifkan.
                </div>
            </section>

            <section id="third-party">
                <h2>Cookies Pihak Ketiga</h2>
                <p>Kami menggunakan layanan pihak ketiga yang juga dapat menempatkan cookies di perangkat Anda:</p>
                
                <h3>Google Analytics</h3>
                <p>Untuk menganalisis penggunaan website. <a href="https://policies.google.com/privacy" target="_blank" style="color: var(--main-red); font-weight: 600;">Privacy Policy Google</a></p>

                <h3>Facebook Pixel</h3>
                <p>Untuk iklan dan retargeting. <a href="https://www.facebook.com/privacy/explanation" target="_blank" style="color: var(--main-red); font-weight: 600;">Privacy Policy Facebook</a></p>

                <h3>Payment Gateways</h3>
                <p>Midtrans, Xendit untuk memproses pembayaran secara aman.</p>
            </section>

            <section id="do-not-track">
                <h2>Do Not Track (DNT)</h2>
                <p>Kami menghormati sinyal Do Not Track (DNT) dari browser Anda. Jika browser Anda mengirim sinyal DNT, kami tidak akan melacak aktivitas Anda untuk tujuan advertising.</p>
            </section>

            <section id="updates">
                <h2>Pembaruan Kebijakan</h2>
                <p>Kami dapat memperbarui Kebijakan Cookies ini dari waktu ke waktu. Perubahan akan diberitahukan melalui banner di website atau email. Tanggal "Terakhir diperbarui" akan selalu diupdate.</p>
            </section>

            <section id="contact">
                <h2>Hubungi Kami</h2>
                <p>Jika Anda memiliki pertanyaan tentang penggunaan cookies kami:</p>
                <div class="alert alert-primary">
                    <p class="mb-2"><strong>Email:</strong> privacy@hungerswitch.com</p>
                    <p class="mb-0"><strong>Telepon:</strong> +62 812-3456-7890</p>
                </div>
            </section>

            <hr class="my-5">

            <div class="alert alert-success">
                <h5><i class="bi bi-cookie me-2"></i>Transparansi Cookies</h5>
                <p class="mb-0">Kami berkomitmen untuk transparan tentang bagaimana kami menggunakan cookies. Anda selalu memiliki kontrol penuh atas cookies non-esensial yang kami gunakan.</p>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Hungerswitch. All Rights Reserved.</p>
            <div class="mt-2">
                <a href="terms_of_service.php" class="text-white-50 me-3">Terms of Service</a>
                <a href="privacy_policy.php" class="text-white-50 me-3">Privacy Policy</a>
                <a href="cookies_policy.php" class="text-white-50">Cookies Policy</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openCookieSettings() {
            alert('Fitur pengaturan cookie akan segera hadir! Untuk sementara, gunakan pengaturan browser Anda.');
        }
    </script>
</body>
</html>