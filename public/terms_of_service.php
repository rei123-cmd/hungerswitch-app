<?php 
//session_start();
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat dan Ketentuan - Hungerswitch</title>
    
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
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
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
            color: var(--main-green);
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
            border-left: 4px solid var(--main-green);
        }

        .toc {
            background: var(--light-beige);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .toc ul {
            list-style: none;
            padding-left: 0;
        }

        .toc li {
            padding: 0.5rem 0;
        }

        .toc a {
            color: var(--main-green);
            text-decoration: none;
            font-weight: 500;
        }

        .toc a:hover {
            color: var(--main-red);
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
                    <div class="brand-subtext" style="font-size: 0.75rem; color: #6c757d;">Syarat dan Ketentuan</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" href="terms_of_service.php">Syarat & Ketentuan</a></li>
                    <li class="nav-item"><a class="nav-link" href="privacy_policy.php">Kebijakan Privasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="cookies_policy.php">Kebijakan Cookies</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="legal-header">
        <div class="container text-center position-relative" style="z-index: 1;">
            <h1 style="font-size: 2.5rem; font-weight: 800;">Syarat dan Ketentuan</h1>
            <p style="font-size: 1.1rem; opacity: 0.9;">Terms of Service - Hungerswitch</p>
        </div>
    </div>

    <div class="container">
        <div class="legal-content">
            <div class="last-updated">
                <i class="bi bi-calendar-check me-2"></i>
                <strong>Terakhir diperbarui:</strong> 20 November 2025
            </div>

            <div class="toc">
                <h4><i class="bi bi-list-ul me-2"></i>Daftar Isi</h4>
                <ul>
                    <li><a href="#pendahuluan">1. Pendahuluan</a></li>
                    <li><a href="#definisi">2. Definisi</a></li>
                    <li><a href="#akun">3. Akun Pengguna</a></li>
                    <li><a href="#layanan">4. Layanan Hungerswitch</a></li>
                    <li><a href="#transaksi">5. Transaksi dan Pembayaran</a></li>
                    <li><a href="#mitra">6. Ketentuan untuk Mitra</a></li>
                    <li><a href="#larangan">7. Larangan Penggunaan</a></li>
                    <li><a href="#hki">8. Hak Kekayaan Intelektual</a></li>
                    <li><a href="#tanggung-jawab">9. Tanggung Jawab</a></li>
                    <li><a href="#perubahan">10. Perubahan Ketentuan</a></li>
                </ul>
            </div>

            <section id="pendahuluan">
                <h2>1. Pendahuluan</h2>
                <p>Selamat datang di Hungerswitch! Syarat dan Ketentuan ini mengatur penggunaan platform Hungerswitch, termasuk website, aplikasi mobile, dan layanan terkait (selanjutnya disebut "Platform").</p>
                <p>Dengan mengakses atau menggunakan Platform kami, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui untuk terikat dengan Syarat dan Ketentuan ini.</p>
            </section>

            <section id="definisi">
                <h2>2. Definisi</h2>
                <ul>
                    <li><strong>Hungerswitch:</strong> Platform digital yang menghubungkan makanan surplus dari mitra dengan konsumen dan komunitas yang membutuhkan.</li>
                    <li><strong>Pengguna:</strong> Setiap individu atau entitas yang mengakses atau menggunakan Platform.</li>
                    <li><strong>Mitra:</strong> UMKM, restoran, atau bisnis kuliner yang menjual produk di Platform.</li>
                    <li><strong>Agen:</strong> Individu atau organisasi yang mendistribusikan makanan ke komunitas prasejahtera.</li>
                    <li><strong>Produk:</strong> Makanan surplus yang dijual di Platform.</li>
                </ul>
            </section>

            <section id="akun">
                <h2>3. Akun Pengguna</h2>
                <h3>3.1 Pendaftaran</h3>
                <ul>
                    <li>Anda harus berusia minimal 17 tahun untuk membuat akun.</li>
                    <li>Informasi yang Anda berikan harus akurat dan lengkap.</li>
                    <li>Anda bertanggung jawab menjaga kerahasiaan akun dan password Anda.</li>
                    <li>Anda tidak diperbolehkan membuat lebih dari satu akun.</li>
                </ul>

                <h3>3.2 Keamanan Akun</h3>
                <p>Anda bertanggung jawab penuh atas semua aktivitas yang terjadi di akun Anda. Segera hubungi kami jika Anda mengetahui adanya penggunaan tidak sah atas akun Anda.</p>
            </section>

            <section id="layanan">
                <h2>4. Layanan Hungerswitch</h2>
                <h3>4.1 Marketplace Surplus</h3>
                <ul>
                    <li>Platform memfasilitasi pembelian makanan surplus dengan diskon.</li>
                    <li>Produk dijual dalam kondisi "as is" sesuai deskripsi mitra.</li>
                    <li>Pengambilan dilakukan langsung di lokasi mitra (pick-up).</li>
                </ul>

                <h3>4.2 Program Donasi</h3>
                <ul>
                    <li>Donasi digunakan untuk membeli dan mendistribusikan makanan ke komunitas yang membutuhkan.</li>
                    <li>Kami menyediakan laporan transparansi untuk setiap donasi.</li>
                </ul>
            </section>

            <section id="transaksi">
                <h2>5. Transaksi dan Pembayaran</h2>
                <h3>5.1 Harga dan Pembayaran</h3>
                <ul>
                    <li>Harga produk ditentukan oleh mitra dan dapat berubah sewaktu-waktu.</li>
                    <li>Pembayaran dilakukan melalui metode yang tersedia di Platform.</li>
                    <li>Biaya layanan sebesar 5% ditambahkan pada setiap transaksi.</li>
                </ul>

                <h3>5.2 Pembatalan dan Refund</h3>
                <ul>
                    <li>Pembatalan hanya dapat dilakukan maksimal 1 jam sebelum waktu pengambilan.</li>
                    <li>Refund akan diproses ke dompet digital dalam 1x24 jam.</li>
                    <li>Produk yang sudah diambil tidak dapat dikembalikan.</li>
                </ul>
            </section>

            <section id="mitra">
                <h2>6. Ketentuan untuk Mitra</h2>
                <h3>6.1 Kewajiban Mitra</h3>
                <ul>
                    <li>Menyediakan informasi produk yang akurat dan jujur.</li>
                    <li>Memastikan kualitas dan keamanan makanan yang dijual.</li>
                    <li>Menyiapkan produk sesuai waktu pengambilan yang dijadwalkan.</li>
                    <li>Mematuhi standar kesehatan dan kebersihan yang berlaku.</li>
                </ul>

                <h3>6.2 Komisi</h3>
                <p>Hungerswitch mengenakan komisi sebesar 15% dari setiap transaksi yang berhasil.</p>
            </section>

            <section id="larangan">
                <h2>7. Larangan Penggunaan</h2>
                <p>Pengguna dilarang untuk:</p>
                <ul>
                    <li>Menggunakan Platform untuk tujuan ilegal atau melanggar hukum.</li>
                    <li>Menjual atau membeli makanan yang kadaluarsa atau tidak layak konsumsi.</li>
                    <li>Melakukan penipuan atau memberikan informasi palsu.</li>
                    <li>Mengganggu atau merusak sistem keamanan Platform.</li>
                    <li>Menyalahgunakan program donasi untuk kepentingan pribadi.</li>
                </ul>
            </section>

            <section id="hki">
                <h2>8. Hak Kekayaan Intelektual</h2>
                <p>Semua konten di Platform, termasuk logo, desain, teks, dan gambar, dilindungi oleh hak cipta dan merek dagang. Anda tidak diperbolehkan menggunakan, menyalin, atau mendistribusikan konten kami tanpa izin tertulis.</p>
            </section>

            <section id="tanggung-jawab">
                <h2>9. Tanggung Jawab</h2>
                <h3>9.1 Batasan Tanggung Jawab</h3>
                <p>Hungerswitch bertindak sebagai platform perantara. Kami tidak bertanggung jawab atas:</p>
                <ul>
                    <li>Kualitas, keamanan, atau kelayakan produk yang dijual mitra.</li>
                    <li>Perselisihan antara pengguna dan mitra.</li>
                    <li>Kerugian akibat keterlambatan atau pembatalan sepihak oleh mitra.</li>
                </ul>

                <h3>9.2 Ganti Rugi</h3>
                <p>Anda setuju untuk mengganti rugi dan membebaskan Hungerswitch dari segala klaim, kerugian, atau biaya yang timbul akibat pelanggaran Anda terhadap Syarat dan Ketentuan ini.</p>
            </section>

            <section id="perubahan">
                <h2>10. Perubahan Ketentuan</h2>
                <p>Kami berhak mengubah Syarat dan Ketentuan ini sewaktu-waktu. Perubahan akan diberitahukan melalui Platform atau email. Penggunaan Platform setelah perubahan berlaku menandakan persetujuan Anda terhadap ketentuan baru.</p>
            </section>

            <hr class="my-5">

            <div class="alert alert-info">
                <h5><i class="bi bi-info-circle me-2"></i>Butuh Bantuan?</h5>
                <p class="mb-0">Jika Anda memiliki pertanyaan tentang Syarat dan Ketentuan ini, silakan hubungi kami di <strong>legal@hungerswitch.com</strong> atau <strong>+62 812-3456-7890</strong></p>
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
</body>
</html>