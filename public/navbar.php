<?php
// File: navbar.php - NAVBAR UNIVERSAL
// Deteksi otomatis role dan tampilkan menu sesuai

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Deteksi role saat ini
$current_role = $_SESSION['current_role'] ?? null;
$user_roles = $_SESSION['user_roles'] ?? [];
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';

// Menu berdasarkan role
$menus = [
    'customer' => [
        ['label' => 'Beranda', 'url' => 'index.php', 'icon' => 'house'],
        ['label' => 'Marketplace', 'url' => 'marketplace.php', 'icon' => 'shop'],
        ['label' => 'Donasi', 'url' => 'donasi.php', 'icon' => 'heart-fill'],
        ['label' => 'Untuk Mitra', 'url' => 'untmitra.php', 'icon' => 'briefcase'],
        ['label' => 'Agen', 'url' => 'agen.php', 'icon' => 'shield-check']
    ],
    'partner' => [
        ['label' => 'Dashboard Mitra', 'url' => 'mitra_dashboard.php', 'icon' => 'speedometer2'],
        ['label' => 'Produk Saya', 'url' => 'mitra_products.php', 'icon' => 'box-seam'],
        ['label' => 'Pesanan', 'url' => 'mitra_orders.php', 'icon' => 'cart-check'],
        ['label' => 'Keuangan', 'url' => 'mitra_finance.php', 'icon' => 'wallet2']
    ],
    'agent' => [
        ['label' => 'Dashboard Agen', 'url' => 'agent_dashboard.php', 'icon' => 'speedometer2'],
        ['label' => 'Program Saya', 'url' => 'agent_programs.php', 'icon' => 'calendar-check'],
        ['label' => 'Data Penerima', 'url' => 'agent_beneficiaries.php', 'icon' => 'people'],
        ['label' => 'Laporan', 'url' => 'agent_reports.php', 'icon' => 'file-text']
    ],
    'founder' => [
        ['label' => 'Dashboard Founder', 'url' => 'founder_dashboard.php', 'icon' => 'speedometer2'],
        ['label' => 'Program Distribusi', 'url' => 'founder_programs.php', 'icon' => 'calendar-check'],
        ['label' => 'Keuangan', 'url' => 'founder_finance.php', 'icon' => 'wallet2'],
        ['label' => 'Verifikasi', 'url' => 'founder_verification.php', 'icon' => 'shield-check']
    ]
];

$active_menu = $menus[$current_role] ?? $menus['customer'];
?>

<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <span class="brand-logo-icon" style="background: linear-gradient(135deg, #D9232D, #ff4757); color: white; padding: 10px 12px; border-radius: 12px; font-weight: 700; font-size: 1.1rem;">HS</span>
            <div class="ms-2">
                <strong>Hungerswitch</strong>
                <div style="font-size: 0.75rem; color: #6c757d;">
                    <?php 
                        $role_labels = [
                            'customer' => 'Selamatkan Makanan',
                            'partner' => 'Mitra Dashboard',
                            'agent' => 'Agen Dashboard',
                            'founder' => 'Founder Dashboard'
                        ];
                        echo $role_labels[$current_role] ?? 'Selamatkan Makanan';
                    ?>
                </div>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php foreach($active_menu as $menu): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $menu['url']; ?>">
                        <i class="bi bi-<?php echo $menu['icon']; ?> me-1"></i>
                        <?php echo $menu['label']; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <ul class="navbar-nav ms-3 align-items-center">
                <?php if($is_logged_in): ?>
                    
                    <!-- Role Switcher (jika user punya > 1 role) -->
                    <?php if(count($user_roles) > 1): ?>
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-gear me-1"></i>
                            <?php 
                                $role_names = [
                                    'customer' => 'Customer',
                                    'partner' => 'Mitra',
                                    'agent' => 'Agen',
                                    'founder' => 'Founder'
                                ];
                                echo $role_names[$current_role] ?? 'Role';
                            ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach($user_roles as $role): ?>
                            <li>
                                <a class="dropdown-item <?php echo $role == $current_role ? 'active' : ''; ?>" 
                                   href="switch_role.php?to=<?php echo $role; ?>">
                                    <i class="bi bi-<?php echo ['customer' => 'person', 'partner' => 'shop', 'agent' => 'shield-check', 'founder' => 'stars'][$role]; ?> me-2"></i>
                                    <?php echo $role_names[$role]; ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Keranjang (hanya untuk customer) -->
                    <?php if($current_role == 'customer'): ?>
                    <li class="nav-item me-3">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#cartModal">
                            <div class="position-relative">
                                <i class="bi bi-cart3 fs-4"></i>
                                <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadge" style="display: none;">0</span>
                            </div>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            <?php echo htmlspecialchars($user_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profil</a></li>
                            
                            <?php if($current_role == 'customer'): ?>
                            <li><a class="dropdown-item" href="orders.php"><i class="bi bi-box me-2"></i>Pesanan</a></li>
                            <?php endif; ?>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn" href="login_unified.php" style="background: linear-gradient(135deg, #2F5233, #3d6b42); color: white; padding: 0.6rem 1.8rem; border-radius: 25px; font-weight: 600;">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar-nav .nav-link {
    font-weight: 500;
    color: #333;
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
    background: #D9232D;
    transition: width 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #D9232D;
}

.navbar-nav .nav-link:hover::after {
    width: 80%;
}

.navbar-nav .nav-link.active {
    color: #D9232D;
}

.navbar-nav .nav-link.active::after {
    width: 80%;
}
</style>