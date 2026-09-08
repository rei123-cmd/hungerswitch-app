<?php 
//session_start();
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - Hungerswitch</title>
    
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
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: var(--main-red);
        }

        .legal-header {
            background: linear-gradient(135deg, var(--main-red), #ff4757);
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
            border-left: 4px solid var(--main-red);
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
            color: var(--main-red);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .toc a:hover {
            color: #ff4757;
        }

        .data-type-box {
            background: linear-gradient(135deg, #fff5f5, #ffffff);
            border-left: 4px solid var(--main-red);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin: 1rem 0;
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
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-logo-icon">HS</span>
                <div class="brand-text ms-2">
                    <strong>Hungerswitch</strong>
                    <div class="brand-subtext" style="font-size: 0.75rem; color: #6c757d;">Kebijakan Privasi</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="terms_of_service.php">Syarat & Ketentuan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="privacy_policy.php">Kebijakan Privasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="cookies_policy.php">Kebijakan Cookies</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="legal-header">
        <div class="container text-center position-relative" style="z-index: 1;">
            <h1 style="font-size: 2.5rem; font-weight: 800;">🔒 Kebijakan Privasi</h1>
            <p style="font-size: 1.1rem; opacity: 0.9;">Privacy Policy - Hungerswitch</p>
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
                    <li><a href="#intro">1. Pendahuluan</a></li>
                    <li><a href="#data-collect">2. Data yang Kami Kumpulkan</a></li>
                    <li><a href="#how-use">3. Bagaimana Kami Menggunakan Data</a></li>
                    <li><a href="#data-share">4. Berbagi Data dengan Pihak Ketiga</a></li>
                    <li><a href="#security">5. Keamanan Data</a></li>
                    <li><a href="#rights">6. Hak Pengguna</a></li>
                    <li><a href="#cookies">7. Cookies dan Teknologi Tracking</a></li>
                    <li><a href="#children">8. Privasi Anak-anak</a></li>
                    <li><a href="#changes">9. Perubahan Kebijakan</a></li>
                    <li><a href="#contact">10. Hubungi Kami</a></li>
                </ul>
            </div>

            <section id="intro">
                <h2>1. Pendahuluan</h2>
                <p>Hungerswitch ("kami", "kita") menghormati privasi Anda dan berkomitmen untuk melindungi data pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda saat menggunakan Platform kami.</p>
                <p>Dengan menggunakan Platform Hungerswitch, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan ini.</p>
            </section>

            <section id="data-collect">
                <h2>2. Data yang Kami Kumpulkan</h2>
                
                <div class="data-type-box">
                    <h3 style="margin-top: 0;">2.1 Data yang Anda Berikan</h3>
                    <ul>
                        <li><strong>Informasi Akun:</strong> Nama, alamat email, nomor telepon, password (terenkripsi)</li>
                        <li><strong>Informasi Profil:</strong> Foto profil, alamat, preferensi makanan</li>
                        <li><strong>Informasi Pembayaran:</strong> Detail kartu kredit/debit, informasi rekening bank (diproses melalui payment gateway aman)</li>
                        <li><strong>Informasi Transaksi:</strong> Riwayat pembelian, pesanan, donasi</li>
                        <li><strong>Komunikasi:</strong> Email, pesan, feedback yang Anda kirimkan kepada kami</li>
                    </ul>
                </div>

                <div class="data-type-box">
                    <h3 style="margin-top: 0;">2.2 Data yang Dikumpulkan Otomatis</h3>
                    <ul>
                        <li><strong>Data Perangkat:</strong> Jenis perangkat, sistem operasi, browser</li>
                        <li><strong>Data Penggunaan:</strong> Halaman yang dikunjungi, waktu akses, fitur yang digunakan</li>
                        <li><strong>Data Lokasi:</strong> Lokasi geografis (dengan izin Anda)</li>
                        <li><strong>Cookies:</strong> Preferensi, session ID, data analytics</li>
                    </ul>
                </div>

                <div class="data-type-box">
                    <h3 style="margin-top: 0;">2.3 Data dari Pihak Ketiga</h3>
                    <ul>
                        <li>Informasi dari social media (jika Anda login via Facebook/Google)</li>
                        <li>Data verifikasi dari payment gateway</li>
                        <li>Informasi bisnis mitra dari dokumen legal</li>
                    </ul>
                </div>
            </section>

            <section id="how-use">
                <h2>3. Bagaimana Kami Menggunakan Data</h2>
                <p>Kami menggunakan data Anda untuk:</p>
                <ul>
                    <li><strong>Menyediakan Layanan:</strong> Memproses transaksi, mengelola akun, mengirim notifikasi pesanan</li>
                    <li><strong>Meningkatkan Platform:</strong> Analisis penggunaan, pengembangan fitur baru, perbaikan bug</li>
                    <li><strong>Komunikasi:</strong> Mengirim update, promosi, newsletter (dengan persetujuan Anda)</li>
                    <li><strong>Keamanan:</strong> Mendeteksi dan mencegah fraud, menjaga keamanan Platform</li>
                    <li><strong>Kepatuhan Hukum:</strong> Memenuhi kewajiban legal dan regulatory</li>
                    <li><strong>Personalisasi:</strong> Memberikan rekomendasi produk yang relevan</li>
                </ul>
            </section>

            <section id="data-share">
                <h2>4. Berbagi Data dengan Pihak Ketiga</h2>
                <p>Kami TIDAK menjual data pribadi Anda. Namun, kami dapat membagikan data Anda dengan:</p>
                
                <h3>4.1 Mitra Bisnis</h3>
                <p>Informasi pesanan dibagikan dengan mitra (restoran/UMKM) untuk memproses pesanan Anda.</p>

                <h3>4.2 Penyedia Layanan</h3>
                <ul>
                    <li><strong>Payment Gateway:</strong> Untuk memproses pembayaran (Midtrans, Xendit, dll)</li>
                    <li><strong>Cloud Hosting:</strong> Untuk penyimpanan data (AWS, Google Cloud)</li>
                    <li><strong>Email Service:</strong> Untuk mengirim notifikasi dan komunikasi</li>
                    <li><strong>Analytics:</strong> Google Analytics untuk memahami penggunaan Platform</li>
                </ul>

                <h3>4.3 Otoritas Hukum</h3>
                <p>Jika diwajibkan oleh hukum atau untuk melindungi hak kami, kami dapat membagikan informasi kepada penegak hukum atau instansi pemerintah.</p>

                <h3>4.4 Merger atau Akuisisi</h3>
                <p>Dalam hal merger, akuisisi, atau penjualan aset, data pengguna dapat ditransfer sebagai bagian dari transaksi.</p>
            </section>

            <section id="security">
                <h2>5. Keamanan Data</h2>
                <p>Kami menerapkan langkah-langkah keamanan teknis dan organisasi untuk melindungi data Anda:</p>
                <ul>
                    <li><strong>Enkripsi:</strong> Data sensitif dienkripsi saat transit (SSL/TLS) dan saat disimpan</li>
                    <li><strong>Akses Terbatas:</strong> Hanya karyawan yang berwenang yang dapat mengakses data pribadi</li>
                    <li><strong>Monitoring:</strong> Sistem monitoring 24/7 untuk mendeteksi aktivitas mencurigakan</li>
                    <li><strong>Regular Audits:</strong> Audit keamanan dan penetration testing secara berkala</li>
                    <li><strong>Secure Infrastructure:</strong> Server dengan firewall dan proteksi DDoS</li>
                </ul>
                <p class="text-muted"><em>Meskipun kami berusaha keras melindungi data Anda, tidak ada sistem yang 100% aman. Anda juga bertanggung jawab menjaga kerahasiaan password Anda.</em></p>
            </section>

            <section id="rights">
                <h2>6. Hak Pengguna</h2>
                <p>Anda memiliki hak-hak berikut terkait data pribadi Anda:</p>
                
                <h3>6.1 Hak Akses</h3>
                <p>Anda dapat meminta salinan data pribadi yang kami simpan tentang Anda.</p>

                <h3>6.2 Hak Koreksi</h3>
                <p>Anda dapat memperbarui atau memperbaiki informasi yang tidak akurat melalui pengaturan akun.</p>

                <h3>6.3 Hak Penghapusan</h3>
                <p>Anda dapat meminta penghapusan data pribadi Anda (right to be forgotten), dengan ketentuan tertentu.</p>

                <h3>6.4 Hak Pembatasan</h3>
                <p>Anda dapat meminta pembatasan pemrosesan data Anda dalam kondisi tertentu.</p>

                <h3>6.5 Hak Portabilitas</h3>
                <p>Anda dapat meminta data Anda dalam format yang dapat dibaca mesin.</p>

                <h3>6.6 Hak Menolak</h3>
                <p>Anda dapat menolak penggunaan data untuk tujuan marketing atau profiling.</p>

                <p class="mt-3"><strong>Untuk menggunakan hak-hak di atas, hubungi kami di:</strong> privacy@hungerswitch.com</p>
            </section>

            <section id="cookies">
                <h2>7. Cookies dan Teknologi Tracking</h2>
                <p>Kami menggunakan cookies dan teknologi serupa untuk:</p>
                <ul>
                    <li>Menjaga sesi login Anda</li>
                    <li>Mengingat preferensi Anda</li>
                    <li>Menganalisis penggunaan Platform</li>
                    <li>Menampilkan iklan yang relevan</li>
                </ul>
                <p>Anda dapat mengatur browser untuk menolak cookies, namun ini dapat mempengaruhi fungsionalitas Platform.</p>
                <p><a href="cookies_policy.php" style="color: var(--main-red); font-weight: 600;">Pelajari lebih lanjut tentang Kebijakan Cookies kami →</a></p>
            </section>

            <section id="children">
                <h2>8. Privasi Anak-anak</h2>
                <p>Platform kami tidak ditujukan untuk anak-anak di bawah 17 tahun. Kami tidak secara sengaja mengumpulkan data pribadi dari anak-anak. Jika Anda adalah orang tua/wali dan mengetahui anak Anda memberikan informasi kepada kami, segera hubungi kami untuk penghapusan data.</p>
            </section>

            <section id="changes">
                <h2>9. Perubahan Kebijakan</h2>
                <p>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan material akan diberitahukan melalui:</p>
                <ul>
                    <li>Email ke alamat terdaftar Anda</li>
                    <li>Notifikasi di Platform</li>
                    <li>Banner di halaman utama</li>
                </ul>
                <p>Tanggal "Terakhir diperbarui" di bagian atas akan diupdate. Penggunaan Platform setelah perubahan berarti Anda menerima kebijakan baru.</p>
            </section>

            <section id="contact">
                <h2>10. Hubungi Kami</h2>
                <p>Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini atau ingin menggunakan hak-hak Anda, hubungi kami:</p>
                
                <div class="alert alert-primary">
                    <h5><i class="bi bi-envelope me-2"></i>Data Protection Officer</h5>
                    <p class="mb-2"><strong>Email:</strong> privacy@hungerswitch.com</p>
                    <p class="mb-2"><strong>Telepon:</strong> +62 812-3456-7890</p>
                    <p class="mb-0"><strong>Alamat:</strong> Jl. BSD Raya No. 123, Tangerang Selatan, Indonesia 15310</p>
                </div>

                <p class="mt-3">Kami akan merespons permintaan Anda dalam waktu maksimal 30 hari kerja.</p>
            </section>

            <hr class="my-5">

            <div class="alert alert-success">
                <h5><i class="bi bi-shield-check me-2"></i>Komitmen Kami</h5>
                <p class="mb-0">Hungerswitch berkomitmen penuh untuk melindungi privasi Anda dan menggunakan data Anda secara bertanggung jawab. Kepercayaan Anda adalah prioritas utama kami.</p>
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