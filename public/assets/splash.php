<?php
session_start();

if(isset($_SESSION['visited']) || isset($_COOKIE['visited'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Hungerswitch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            background: linear-gradient(135deg, #FCFCF8 0%, #f8f6f1 100%);
        }

        .splash-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .floating-icon {
            position: absolute;
            font-size: 3rem;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .floating-icon:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-icon:nth-child(2) {
            top: 20%;
            right: 15%;
            animation-delay: 1s;
        }

        .floating-icon:nth-child(3) {
            bottom: 20%;
            left: 15%;
            animation-delay: 2s;
        }

        .floating-icon:nth-child(4) {
            bottom: 15%;
            right: 10%;
            animation-delay: 1.5s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-30px) rotate(5deg);
            }
        }

        .splash-content {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 800px;
            padding: 2rem;
        }

        .logo-wrapper {
            margin-bottom: 2rem;
            animation: fadeInDown 1s ease;
        }

        .logo-icon {
            display: inline-block;
            background: linear-gradient(135deg, #D9232D, #ff4757);
            color: white;
            width: 100px;
            height: 100px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 800;
            box-shadow: 0 20px 60px rgba(217, 35, 45, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 20px 60px rgba(217, 35, 45, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 25px 70px rgba(217, 35, 45, 0.5);
            }
        }

        .welcome-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #D9232D, #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease 0.3s backwards;
        }

        .welcome-subtitle {
            font-size: 1.3rem;
            color: #555;
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease 0.5s backwards;
        }

        .quiz-section {
            background: white;
            border-radius: 30px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease 0.7s backwards;
        }

        .quiz-question {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 2rem;
        }

        .quiz-options {
            display: grid;
            gap: 1rem;
        }

        .quiz-option {
            background: linear-gradient(135deg, #F6F4EB, #ffffff);
            border: 3px solid transparent;
            border-radius: 15px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #333;
        }

        .quiz-option:hover {
            border-color: #D9232D;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(217, 35, 45, 0.2);
        }

        .quiz-option.selected {
            border-color: #2F5233;
            background: linear-gradient(135deg, #2F5233, #3d6b42);
            color: white;
        }

        .fact-section {
            background: linear-gradient(135deg, #fff7f0, #ffffff);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border-left: 5px solid #D9232D;
            animation: fadeInUp 1s ease 0.9s backwards;
        }

        .fact-icon {
            font-size: 2.5rem;
            color: #D9232D;
            margin-bottom: 1rem;
        }

        .fact-text {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.8;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 1.1s backwards;
        }

        .btn-cta {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
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

        .btn-primary-cta {
            background: linear-gradient(135deg, #2F5233, #3d6b42);
            color: white;
            box-shadow: 0 10px 30px rgba(47, 82, 51, 0.3);
        }

        .btn-primary-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(47, 82, 51, 0.4);
        }

        .btn-secondary-cta {
            background: transparent;
            color: #D9232D;
            border: 2px solid #D9232D;
        }

        .btn-secondary-cta:hover {
            background: #D9232D;
            color: white;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loading-overlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(217, 35, 45, 0.2);
            border-top-color: #D9232D;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2.5rem;
            }

            .quiz-section {
                padding: 2rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn-cta {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader"></div>
    </div>

    <div class="splash-container">
        <div class="bg-animation">
            <i class="bi bi-heart-fill floating-icon" style="color: #D9232D;"></i>
            <i class="bi bi-box-seam-fill floating-icon" style="color: #2F5233;"></i>
            <i class="bi bi-people-fill floating-icon" style="color: #D9232D;"></i>
            <i class="bi bi-globe2 floating-icon" style="color: #2F5233;"></i>
        </div>

        <div class="splash-content">
            <div class="logo-wrapper">
                <div class="logo-icon mx-auto">HS</div>
            </div>

            <h1 class="welcome-title">Hungerswitch</h1>
            <p class="welcome-subtitle">Selamatkan Makanan, Bantu Sesama, Ubah Dunia</p>

            <div class="fact-section">
                <div class="fact-icon">
                    <i class="bi bi-lightbulb-fill"></i>
                </div>
                <p class="fact-text" id="factText">
                    Tahukah Anda? <strong>1.3 miliar ton</strong> makanan terbuang setiap tahunnya di dunia, 
                    sementara <strong>828 juta orang</strong> mengalami kelaparan. 
                    Bersama Hungerswitch, kita bisa membuat perbedaan!
                </p>
            </div>

            <div class="quiz-section" id="quizSection">
                <h3 class="quiz-question" id="quizQuestion">
                    Apa yang ingin Anda lakukan hari ini?
                </h3>
                <div class="quiz-options">
                    <div class="quiz-option" onclick="selectOption(this, 'marketplace')">
                        <i class="bi bi-cart3 me-2"></i>
                        Belanja makanan berkualitas dengan harga hemat
                    </div>
                    <div class="quiz-option" onclick="selectOption(this, 'donate')">
                        <i class="bi bi-heart-fill me-2"></i>
                        Berdonasi untuk membantu sesama
                    </div>
                    <div class="quiz-option" onclick="selectOption(this, 'partner')">
                        <i class="bi bi-shop-window me-2"></i>
                        Menjadi mitra dan mengurangi food waste
                    </div>
                    <div class="quiz-option" onclick="selectOption(this, 'explore')">
                        <i class="bi bi-compass me-2"></i>
                        Jelajahi semua fitur Hungerswitch
                    </div>
                </div>
            </div>

            <div class="cta-buttons">
                <button class="btn-cta btn-primary-cta" onclick="startJourney()">
                    <i class="bi bi-arrow-right me-2"></i>Mulai Perjalanan
                </button>
                <button class="btn-cta btn-secondary-cta" onclick="skipIntro()">
                    Lewati
                </button>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        });

        const facts = [
            'Tahukah Anda? <strong>1.3 miliar ton</strong> makanan terbuang setiap tahunnya di dunia, sementara <strong>828 juta orang</strong> mengalami kelaparan. Bersama Hungerswitch, kita bisa membuat perbedaan!',
            'Setiap tahun, <strong>17% dari total produksi makanan global</strong> terbuang percuma. Dengan Hungerswitch, makanan surplus bisa sampai ke tangan yang tepat!',
            '<strong>25% dari air</strong> yang digunakan untuk pertanian terbuang karena food waste. Mari selamatkan makanan dan planet kita!',
            'Di Indonesia, <strong>23-48 juta ton</strong> makanan terbuang setiap tahun. Hungerswitch hadir untuk mengubah statistik ini!'
        ];

        let currentFact = 0;
        setInterval(() => {
            currentFact = (currentFact + 1) % facts.length;
            document.getElementById('factText').innerHTML = facts[currentFact];
        }, 8000);

        let selectedOption = null;

        function selectOption(element, value) {
            document.querySelectorAll('.quiz-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            element.classList.add('selected');
            selectedOption = value;
        }

        function startJourney() {
            fetch('set_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'visited=true&preference=' + (selectedOption || 'explore')
            }).then(() => {
                if (selectedOption === 'marketplace') {
                    window.location.href = 'marketplace.php';
                } else if (selectedOption === 'donate') {
                    window.location.href = 'donasi.php';
                } else if (selectedOption === 'partner') {
                    window.location.href = 'untmitra.php';
                } else {
                    window.location.href = 'index.php';
                }
            });
        }

        function skipIntro() {
            fetch('set_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'visited=true&preference=explore'
            }).then(() => {
                window.location.href = 'index.php';
            });
        }
    </script>
</body>
</html>