<?php 
// File: marketplace.php - REVISI TANPA WALLET
session_start();
include 'config.php';

// Fetch Products
$query = "SELECT p.*, u.name as partner_name, c.name as category_name 
          FROM products p 
          JOIN users u ON p.partner_id = u.id 
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.status = 'available' AND p.stock > 0 
          ORDER BY p.created_at DESC";
$products = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - Hungerswitch</title>
    
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
            padding-top: 76px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar */
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

        .navbar-nav .nav-link:hover::after { width: 80%; }
        .navbar-nav .nav-link.active { color: var(--main-red); }
        .navbar-nav .nav-link.active::after { width: 80%; }

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

        /* Marketplace Header */
        .marketplace-header {
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            padding: 3rem 0 2rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .marketplace-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 150%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: headerPulse 8s ease-in-out infinite;
        }

        @keyframes headerPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .marketplace-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            animation: fadeInDown 0.6s ease;
        }

        .marketplace-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            animation: fadeInDown 0.6s ease 0.2s backwards;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            animation: slideUp 0.6s ease 0.3s backwards;
            transition: all 0.3s ease;
        }

        .filter-section:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.12); }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--main-green);
            box-shadow: 0 0 0 4px rgba(47, 82, 51, 0.1);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            opacity: 0;
            animation: cardFadeIn 0.6s ease forwards;
        }

        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.2s; }
        .product-card:nth-child(3) { animation-delay: 0.3s; }
        .product-card:nth-child(4) { animation-delay: 0.4s; }
        .product-card:nth-child(5) { animation-delay: 0.5s; }
        .product-card:nth-child(6) { animation-delay: 0.6s; }
        .product-card:nth-child(n+7) { animation-delay: 0.7s; }

        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(30px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .product-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 12px 40px rgba(47, 82, 51, 0.2);
        }

        .product-image-container {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
            background: #f0f0f0;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image { transform: scale(1.1); }

        .discount-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 4px 15px rgba(217, 35, 45, 0.4);
            animation: badgeBounce 2s ease-in-out infinite;
        }

        @keyframes badgeBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .product-body { padding: 1.5rem; }

        .product-category {
            color: #999;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
            min-height: 60px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-restaurant {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, var(--light-beige), #ffffff);
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .product-info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-pricing {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--main-red), #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .product-original-price {
            font-size: 1rem;
            color: #999;
            text-decoration: line-through;
        }

        .btn-add-cart {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--main-green), #3d6b42);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(47, 82, 51, 0.3);
        }

        .cart-icon-wrapper {
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .cart-icon-wrapper:hover { transform: scale(1.1); }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--main-red);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            animation: badgePop 0.3s ease;
        }

        .cart-badge.show { display: flex; }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            animation: fadeIn 0.6s ease;
        }

        .empty-icon {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Footer */
        footer {
            background-color: #1a1a1a;
            color: white;
            padding: 60px 0 20px;
            margin-top: auto;
        }
        .footer-title { font-weight: 700; margin-bottom: 20px; color: white; }
        .footer-link { color: #aaa; text-decoration: none; display: block; margin-bottom: 10px; transition: 0.3s; }
        .footer-link:hover { color: var(--main-red); padding-left: 5px; }

        @media (max-width: 768px) {
            .product-grid { grid-template-columns: 1fr; }
            .marketplace-title { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="marketplace-header">
        <div class="container position-relative">
            <h1 class="marketplace-title">Marketplace Surplus</h1>
            <p class="marketplace-subtitle">Temukan makanan berkualitas dengan harga terjangkau</p>
        </div>
    </div>

    <div class="container">
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari makanan..." id="searchInput">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">Semua Kategori</option>
                        <option value="Bakery">Bakery</option>
                        <option value="Ready to Eat">Ready to Eat</option>
                        <option value="Healthy Food">Healthy Food</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Snacks">Snacks</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="sortFilter">
                        <option value="newest">Terbaru</option>
                        <option value="price_low">Harga Terendah</option>
                        <option value="price_high">Harga Tertinggi</option>
                        <option value="discount">Diskon Terbesar</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="product-grid" id="productGrid">
            <?php if(mysqli_num_rows($products) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($products)): 
                    $discount = round((($product['original_price'] - $product['discounted_price']) / $product['original_price']) * 100);
                    
                    // Calculate time remaining
                    $expiry = strtotime($product['expiry_time']);
                    $now = time();
                    $hours_left = max(0, round(($expiry - $now) / 3600));
                ?>
                <div class="product-card" 
                     data-category="<?php echo htmlspecialchars($product['category_name'] ?? $product['category']); ?>" 
                     data-price="<?php echo $product['discounted_price']; ?>" 
                     data-discount="<?php echo $discount; ?>">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="product-image"
                             onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
                        <div class="discount-badge">-<?php echo $discount; ?>%</div>
                    </div>
                    
                    <div class="product-body">
                        <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="product-restaurant">
                            <i class="bi bi-shop"></i>
                            <?php echo htmlspecialchars($product['partner_name']); ?>
                        </div>
                        
                        <div class="product-info">
                            <div class="product-info-item">
                                <i class="bi bi-clock text-warning"></i>
                                <span><?php echo $hours_left; ?> jam</span>
                            </div>
                            <div class="product-info-item">
                                <i class="bi bi-box-seam text-success"></i>
                                <span><?php echo $product['stock']; ?> stok</span>
                            </div>
                        </div>
                        
                        <div class="product-pricing">
                            <div>
                                <div class="product-price">Rp <?php echo number_format($product['discounted_price'], 0, ',', '.'); ?></div>
                                <div class="product-original-price">Rp <?php echo number_format($product['original_price'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        
                        <button class="btn-add-cart" onclick='addToCart(<?php echo json_encode([
                            "id" => "product_" . $product["id"],
                            "name" => $product["name"],
                            "price" => $product["discounted_price"],
                            "originalPrice" => $product["original_price"],
                            "restaurant" => $product["partner_name"],
                            "image" => $product["image_url"],
                            "category" => $product["category_name"] ?? "Uncategorized"
                        ]); ?>)'>
                            <i class="bi bi-cart-plus"></i>
                            <span>Tambah ke Keranjang</span>
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-icon"></i>
                        <h3>Belum Ada Produk</h3>
                        <p class="text-muted">Silakan cek kembali nanti atau hubungi kami untuk informasi lebih lanjut.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h4 class="footer-title text-danger">Hungerswitch</h4>
                    <p class="text-secondary">Platform penyelamat makanan pertama di Indonesia.</p>
                </div>
                <div class="col-lg-2 col-6">
                    <h5 class="footer-title">Menu</h5>
                    <a href="index.php" class="footer-link">Beranda</a>
                    <a href="marketplace.php" class="footer-link">Belanja</a>
                    <a href="donasi.php" class="footer-link">Donasi</a>
                </div>
                <div class="col-lg-2 col-6">
                    <h5 class="footer-title">Bantuan</h5>
                    <a href="#" class="footer-link">FAQ</a>
                    <a href="#" class="footer-link">Kontak</a>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="text-center text-secondary">
                <small>&copy; 2025 Hungerswitch. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="height: 80vh;">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Keranjang Saya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="cart_modal.php" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
        });

        // Search functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if(searchInput){
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(filterProducts, 300);
            });
        }

        const catFilter = document.getElementById('categoryFilter');
        if(catFilter) catFilter.addEventListener('change', filterProducts);
        
        const sortFilter = document.getElementById('sortFilter');
        if(sortFilter) sortFilter.addEventListener('change', sortProducts);

        function filterProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value;
            const cards = document.querySelectorAll('.product-card');

            cards.forEach(card => {
                const name = card.querySelector('.product-name').textContent.toLowerCase();
                const cardCategory = card.dataset.category;
                
                const matchesSearch = name.includes(searchTerm);
                const matchesCategory = !category || cardCategory === category;

                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                    card.style.animation = 'cardFadeIn 0.4s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function sortProducts() {
            const sortBy = document.getElementById('sortFilter').value;
            const grid = document.getElementById('productGrid');
            const cards = Array.from(document.querySelectorAll('.product-card'));

            cards.sort((a, b) => {
                switch(sortBy) {
                    case 'price_low':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price_high':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'discount':
                        return parseFloat(b.dataset.discount) - parseFloat(a.dataset.discount);
                    default:
                        return 0;
                }
            });

            cards.forEach((card, index) => {
                card.style.animation = 'none';
                setTimeout(() => {
                    card.style.animation = `cardFadeIn 0.4s ease ${index * 0.1}s forwards`;
                }, 10);
                grid.appendChild(card);
            });
        }

        function addToCart(productData) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(productData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateCartBadge(data.total_items);
                    
                    // Reload iframe jika modal sedang terbuka
                    const iframe = document.querySelector('#cartModal iframe');
                    if(iframe) iframe.contentWindow.location.reload();

                    alert('Berhasil menambahkan ' + productData.name + ' ke keranjang!');
                } else {
                    console.error('Gagal menambahkan ke keranjang');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        function updateCartBadge(total) {
            const badge = document.getElementById('cartBadge');
            if (badge) {
                badge.innerText = total;
                badge.classList.add('show');
                badge.style.display = total > 0 ? 'flex' : 'none';
            }
        }

        // Cek Keranjang saat Loading
        document.addEventListener("DOMContentLoaded", function() {
            fetch('add_to_cart.php', { method: 'POST', body: JSON.stringify({}) })
            .then(res => res.json())
            .then(data => {
                if(data.total_items) updateCartBadge(data.total_items);
            });
        });
    </script>
</body>
</html>