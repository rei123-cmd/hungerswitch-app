<?php
// File: select_role.php - REVISI FINAL
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_roles'])) {
    header("Location: login_unified.php");
    exit();
}

// Handle role selection
if(isset($_POST['select_role'])) {
    $selected_role = $_POST['role'];
    
    // Verify role is valid
    if(in_array($selected_role, $_SESSION['user_roles'])) {
        $_SESSION['current_role'] = $selected_role;
        
        // Redirect based on role
        switch($selected_role) {
            case 'customer':
                header("Location: index.php");
                break;
            case 'agent':
                header("Location: agen.php");
                break;
            case 'partner':
                header("Location: mitra_dashboard.php");
                break;
            case 'founder':
                header("Location: founder_dashboard.php");
                break;
            default:
                header("Location: index.php");
        }
        exit();
    }
}

$user_roles = $_SESSION['user_roles'];
$user_name = $_SESSION['user_name'];

// Role information
$role_info = [
    'customer' => [
        'title' => 'Customer',
        'desc' => 'Belanja makanan surplus dengan harga terjangkau',
        'icon' => 'person-circle',
        'color' => '#2196F3'
    ],
    'partner' => [
        'title' => 'Mitra',
        'desc' => 'Kelola bisnis dan jual makanan surplus',
        'icon' => 'shop',
        'color' => '#FF9800'
    ],
    'agent' => [
        'title' => 'Agen',
        'desc' => 'Distribusikan makanan ke komunitas yang membutuhkan',
        'icon' => 'shield-check',
        'color' => '#2F5233'
    ],
    'founder' => [
        'title' => 'Founder',
        'desc' => 'Kelola platform dan pantau semua operasional',
        'icon' => 'stars',
        'color' => '#D9232D'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - Hungerswitch</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

        :root {
            --page-bg: #FCFCF8;
            --main-red: #D9232D;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--page-bg) 0%, #f8f6f1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .role-selector-container {
            max-width: 1200px;
            width: 100%;
        }

        .welcome-box {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .welcome-box h1 {
            font-weight: 800;
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .role-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .role-card {
            background: white;
            border-radius: 25px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            border: 3px solid transparent;
        }

        .role-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .role-card.customer:hover { border-color: #2196F3; }
        .role-card.partner:hover { border-color: #FF9800; }
        .role-card.agent:hover { border-color: #2F5233; }
        .role-card.founder:hover { border-color: #D9232D; }

        .role-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: white;
        }

        .role-icon.customer { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .role-icon.partner { background: linear-gradient(135deg, #FF9800, #F57C00); }
        .role-icon.agent { background: linear-gradient(135deg, #2F5233, #1a2e1d); }
        .role-icon.founder { background: linear-gradient(135deg, #D9232D, #ff4757); }

        .role-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .role-desc {
            color: #666;
            margin-bottom: 2rem;
        }

        .btn-select-role {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-select-role.customer { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .btn-select-role.partner { background: linear-gradient(135deg, #FF9800, #F57C00); }
        .btn-select-role.agent { background: linear-gradient(135deg, #2F5233, #1a2e1d); }
        .btn-select-role.founder { background: linear-gradient(135deg, #D9232D, #ff4757); }

        .btn-select-role:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

    <div class="container role-selector-container">
        <div class="welcome-box">
            <h1>Selamat Datang, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
            <p class="text-muted mb-0">Pilih role untuk melanjutkan</p>
        </div>

        <div class="role-cards">
            <?php foreach($user_roles as $role): ?>
                <?php if(isset($role_info[$role])): ?>
                <div class="role-card <?php echo $role; ?>">
                    <div class="role-icon <?php echo $role; ?>">
                        <i class="bi bi-<?php echo $role_info[$role]['icon']; ?>"></i>
                    </div>
                    <h3 class="role-title"><?php echo $role_info[$role]['title']; ?></h3>
                    <p class="role-desc"><?php echo $role_info[$role]['desc']; ?></p>
                    <form method="POST">
                        <input type="hidden" name="role" value="<?php echo $role; ?>">
                        <button type="submit" name="select_role" class="btn btn-select-role <?php echo $role; ?>">
                            Masuk sebagai <?php echo $role_info[$role]['title']; ?>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="logout.php" class="text-decoration-none text-muted">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>