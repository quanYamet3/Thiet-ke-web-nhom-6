<?php 
include '../ket_noi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
if (!$row) { die("Sản phẩm không tồn tại!"); }

// Tạo rating ngẫu nhiên nhưng cố định theo id sản phẩm (4.0 - 5.0)
srand($row['id'] * 7);
$rating = round((rand(40, 50) / 10), 1);
$review_count = rand(50, 200);
srand(); // reset seed
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS_header.css">
    <link rel="stylesheet" href="../CSS_footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title><?php echo htmlspecialchars($row['name']); ?> - Ink Store</title>
    <style>
        /* ══════ FIX HEADER - Copy từ CSS_header.css ══════ */
.cart-badge {
    background: #d8511c !important;
    color: #fff !important;
    border-radius: 50%;
    min-width: 18px;
    height: 18px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
}
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background: #f5f4f9;
            color: #1a1a2e;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .page-wrapper { flex: 1; padding-top: 80px; }

        /* ══════════ PRODUCT HERO ══════════ */
        .product-hero {
            display: flex;
            gap: 48px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 32px;
        }

        /* LEFT - Images */
        .product-gallery {
            flex: 0 0 480px;
            position: sticky;
            top: 100px;
            align-self: flex-start;
        }

        .main-image {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #ece9f5;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 40px rgba(92,50,144,0.08);
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 20px;
            transition: transform 0.4s ease;
        }

        .main-image:hover img { transform: scale(1.04); }

        .thumb-row {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }

        .thumb {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            border: 2px solid #ece9f5;
            background: #fff;
            overflow: hidden;
            cursor: pointer;
            transition: border-color 0.2s;
        }

        .thumb.active { border-color: #5c3290; }
        .thumb img { width: 100%; height: 100%; object-fit: contain; padding: 6px; }

        /* RIGHT - Info */
        .product-info { flex: 1; min-width: 0; }

        .product-name {
            font-size: 28px;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1.3;
            margin-bottom: 14px;
        }

        /* Rating */
        .rating-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            cursor: pointer;
        }

        .stars { display: flex; gap: 2px; }

        .star {
            color: #f59e0b;
            font-size: 18px;
        }

        .star.half { position: relative; }

        .rating-score {
            font-weight: 700;
            font-size: 15px;
            color: #f59e0b;
        }

        .rating-count {
            font-size: 14px;
            color: #7c6fa0;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .rating-count:hover { color: #5c3290; }

        /* Price */
        .price-box { margin-bottom: 20px; }

        .product-price {
            font-size: 36px;
            font-weight: 800;
            color: #d8511c;
            letter-spacing: -0.5px;
        }

        /* Trust badges */
        .trust-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f0ebfa;
            border: 1px solid #ddd5f0;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            color: #5c3290;
        }

        .badge svg { width: 14px; height: 14px; }

        /* Stock */
        .stock-box {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            font-size: 15px;
            font-weight: 500;
        }

        .stock-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
        }

        .stock-green { color: #16a34a; }
        .stock-green .stock-dot { background: #16a34a; box-shadow: 0 0 0 3px #dcfce7; }
        .stock-orange { color: #ea580c; }
        .stock-orange .stock-dot { background: #ea580c; box-shadow: 0 0 0 3px #ffedd5; }
        .stock-red { color: #dc2626; }
        .stock-red .stock-dot { background: #dc2626; box-shadow: 0 0 0 3px #fee2e2; }

        /* Quantity */
        .qty-section {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .qty-label { font-size: 15px; font-weight: 600; color: #444; }

        .qty-control {
            display: flex;
            align-items: center;
            border: 2px solid #ece9f5;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .qty-btn {
            width: 40px; height: 40px;
            border: none;
            background: none;
            font-size: 20px;
            cursor: pointer;
            color: #5c3290;
            font-weight: 700;
            transition: background 0.15s;
        }

        .qty-btn:hover { background: #f0ebfa; }
        .qty-btn:disabled { color: #ccc; cursor: not-allowed; }

        .qty-num {
            width: 50px;
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            border: none;
            outline: none;
            color: #1a1a2e;
        }

        .subtotal {
            font-size: 14px;
            color: #7c6fa0;
        }

        .subtotal span {
            font-weight: 700;
            color: #d8511c;
        }

        /* CTA buttons */
        .cta-row {
            display: flex;
            gap: 12px;
        }

        .btn-cart-main {
            flex: 1;
            padding: 16px 20px;
            background: #fff;
            color: #5c3290;
            border: 2px solid #5c3290;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        .btn-cart-main:hover {
            background: #5c3290;
            color: #fff;
        }

        .btn-buy-main {
            flex: 1;
            padding: 16px 20px;
            background: linear-gradient(135deg, #d8511c, #e8671c);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(216,81,28,0.3);
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        .btn-buy-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(216,81,28,0.4);
        }

        /* ══════════ DETAIL TABS ══════════ */
        .detail-section {
            max-width: 1200px;
            margin: 0 auto 40px;
            padding: 0 32px;
        }

        .detail-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #ece9f5;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .detail-card-header {
            padding: 20px 28px;
            border-bottom: 1px solid #f0ebfa;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-card-header h2 {
            font-size: 18px;
            font-weight: 800;
            color: #1a1a2e;
        }

        .detail-card-header .icon {
            width: 32px; height: 32px;
            background: #f0ebfa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .detail-card-body {
            padding: 24px 28px;
            line-height: 1.8;
            color: #444;
            font-size: 15px;
        }

        .features-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .features-list li {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            background: #faf9ff;
            border-radius: 12px;
            border-left: 3px solid #5c3290;
        }

        .feature-num {
            font-weight: 800;
            color: #5c3290;
            font-size: 16px;
            min-width: 24px;
        }

        /* ══════════ REVIEWS ══════════ */
        .reviews-section {
            max-width: 1200px;
            margin: 0 auto 60px;
            padding: 0 32px;
        }

        #reviews { scroll-margin-top: 100px; }

        .reviews-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .reviews-header h2 {
            font-size: 22px;
            font-weight: 800;
        }

        .reviews-summary {
            display: flex;
            gap: 32px;
            align-items: center;
            background: #fff;
            border-radius: 20px;
            border: 1px solid #ece9f5;
            padding: 28px 32px;
            margin-bottom: 24px;
        }

        .big-rating {
            text-align: center;
            min-width: 120px;
        }

        .big-num {
            font-size: 56px;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1;
        }

        .big-stars { font-size: 22px; color: #f59e0b; margin: 6px 0; }

        .big-count { font-size: 13px; color: #888; }

        .rating-bars { flex: 1; }

        .bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .bar-label { min-width: 36px; color: #666; font-weight: 600; }

        .bar-track {
            flex: 1;
            height: 8px;
            background: #f0ebfa;
            border-radius: 4px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #f59e0b, #f59e0b);
            border-radius: 4px;
        }

        .bar-count { min-width: 28px; color: #888; text-align: right; }

        /* Review cards */
        .review-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .review-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #ece9f5;
            padding: 20px 24px;
        }

        .review-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .reviewer-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            color: #fff;
        }

        .reviewer-name { font-weight: 700; font-size: 15px; }
        .review-date { font-size: 12px; color: #999; margin-left: auto; }
        .review-stars { font-size: 14px; color: #f59e0b; margin-bottom: 8px; }
        .review-text { font-size: 14px; color: #555; line-height: 1.7; }

        /* Footer */
        footer {
            background: #1e0a3c;
            color: #fff;
            width: 100%;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 40px;
            padding: 50px 60px 40px;
        }

        .footer-brand p, .footer-col a {
            color: #cbb8e8;
            font-size: 14px;
            line-height: 1.8;
            text-decoration: none;
            display: block;
        }

        .footer-col a:hover { color: #fff; }
        .footer-col h4, .footer-brand h4 {
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .footer-bottom {
            border-top: 1px solid #3a2060;
            padding: 18px 60px;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #9b80cc;
        }

        @media (max-width: 900px) {
            .product-hero { flex-direction: column; }
            .product-gallery { position: static; flex: none; }
            .footer-grid { grid-template-columns: 1fr 1fr; padding: 40px 24px 30px; }
            .footer-bottom { padding: 16px 24px; }
        }
    </style>
</head>
<body>

<!-- ══════════ HEADER ══════════ -->
<header>
    <nav>
        <a href="#" class="nav-logo"><img src="../logo.jpg" class="logo-img"></a>
        <div class="nav-links">
            <a href="../Trang_chủ.php">Trang Chủ</a>
            <a href="Giấy&sổ.php">Sản Phẩm</a>
            <a href="../Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới Thiệu</a>
            <a href="../Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
            <a href="../Liên_hệ.html">Liên Hệ</a>
        </div>
        <div class="nav-right">
            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" placeholder="Tìm kiếm..." id="searchInput">
            </div>
            <button class="btn-account">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Tài khoản
            </button>
            <button class="btn-cart" onclick="toggleCart()">
                CART
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <span class="cart-badge" id="cartBadge">0</span>
            </button>
        </div>
    </nav>
    <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-header">
        <h3>Giỏ Hàng</h3>
        <button class="cart-close" onclick="toggleCart()">×</button>
    </div>
    <div class="cart-body" id="cartBody"></div>
    <div id="cartFooter"></div>
</div>
</header>

<!-- ══════════ PRODUCT HERO ══════════ -->
<div class="page-wrapper">
<div class="product-hero">

    <!-- LEFT: Ảnh -->
    <div class="product-gallery">
        <div class="main-image">
            <img src="../images/<?php echo basename($row['image']); ?>"
                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                 id="mainImg">
        </div>
        <div class="thumb-row">
            <div class="thumb active">
                <img src="../images/<?php echo basename($row['image']); ?>">
            </div>
        </div>
    </div>

    <!-- RIGHT: Thông tin -->
    <div class="product-info">

        <!-- Tên -->
        <h1 class="product-name"><?php echo htmlspecialchars($row['name']); ?></h1>

        <!-- Rating -->
        <div class="rating-row" onclick="document.getElementById('reviews').scrollIntoView({behavior:'smooth'})">
            <div class="stars">
                <?php
                $full = floor($rating);
                $half = ($rating - $full) >= 0.5;
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $full) echo '<span class="star">★</span>';
                    elseif ($half && $i == $full + 1) echo '<span class="star">⯨</span>';
                    else echo '<span class="star" style="color:#e0d5f0;">★</span>';
                }
                ?>
            </div>
            <span class="rating-score"><?php echo $rating; ?></span>
            <span class="rating-count"><?php echo $review_count; ?> đánh giá</span>
        </div>

        <!-- Giá -->
        <div class="price-box">
            <div class="product-price"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</div>
        </div>

        <!-- Trust badges -->
        <div class="trust-badges">
            <span class="badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                100% Chính hãng
            </span>
            <span class="badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
                    <rect x="9" y="11" width="14" height="10" rx="2"/>
                    <circle cx="12" cy="16" r="1"/>
                </svg>
                Vận chuyển nhanh
            </span>
            <span class="badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/>
                </svg>
                Đổi trả 7 ngày
            </span>
        </div>

        <!-- Tình trạng kho -->
        <?php
        $stock = (int)$row['stock'];
        if ($stock === 0) {
            $stockClass = 'stock-red';
            $stockText  = 'Hết hàng';
        } elseif ($stock <= 10) {
            $stockClass = 'stock-orange';
            $stockText  = "Sắp hết hàng (còn $stock sản phẩm)";
        } else {
            $stockClass = 'stock-green';
            $stockText  = "Còn hàng ($stock sản phẩm)";
        }
        ?>
        <div class="stock-box <?php echo $stockClass; ?>">
            <span class="stock-dot"></span>
            <span><?php echo $stockText; ?></span>
        </div>

        <!-- Số lượng -->
        <div class="qty-section">
            <span class="qty-label">Số lượng:</span>
            <div class="qty-control">
                <button class="qty-btn" id="btnMinus" onclick="changeQty(-1)">−</button>
                <input class="qty-num" type="number" id="qtyInput" value="1" min="1" max="<?php echo $stock; ?>">
                <button class="qty-btn" id="btnPlus" onclick="changeQty(1)">+</button>
            </div>
            <span class="subtotal">Tạm tính: <span id="subtotalPrice"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</span></span>
        </div>

    
        
        <!-- Buttons -->
        <?php if ($stock > 0): ?>
        <div class="cta-row">
            <button class="btn-cart-main"  onclick="buyNow(prodId, prodName, price, prodImg, getQty())"> 
                🛒 Thêm vào giỏ hàng
            </button>
            <button class="btn-buy-main" onclick="buyNowQty()">
                ⚡ Mua ngay
            </button>
        </div>
        <?php else: ?>
        <div style="padding:16px;background:#fee2e2;border-radius:12px;color:#dc2626;font-weight:600;text-align:center;">
            Sản phẩm tạm thời hết hàng
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- ══════════ CHI TIẾT SẢN PHẨM ══════════ -->
<div class="detail-section">

    <!-- Mô tả -->
    <div class="detail-card">
        <div class="detail-card-header">
            <div class="icon">📋</div>
            <h2>Mô tả sản phẩm</h2>
        </div>
        <div class="detail-card-body">
            <?php echo nl2br(htmlspecialchars($row['description'])); ?>
        </div>
    </div>

    <!-- Đặc điểm nổi bật -->
    <?php if (!empty($row['features'])): ?>
    <div class="detail-card">
        <div class="detail-card-header">
            <div class="icon">✨</div>
            <h2>Đặc điểm nổi bật</h2>
        </div>
        <div class="detail-card-body">
            <?php
            $features = explode("\n", trim($row['features']));
            echo '<ul class="features-list">';
            foreach ($features as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                // Tách số thứ tự khỏi nội dung
                if (preg_match('/^(\d+)\.\s*(.+)/', $line, $m)) {
                    echo '<li><span class="feature-num">' . $m[1] . '.</span><span>' . htmlspecialchars($m[2]) . '</span></li>';
                } else {
                    echo '<li><span>' . htmlspecialchars($line) . '</span></li>';
                }
            }
            echo '</ul>';
            ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Ứng dụng -->
    <?php if (!empty($row['application'])): ?>
    <div class="detail-card">
        <div class="detail-card-header">
            <div class="icon">🎯</div>
            <h2>Ứng dụng & Đối tượng sử dụng</h2>
        </div>
        <div class="detail-card-body">
            <?php echo nl2br(htmlspecialchars($row['application'])); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bảo quản -->
    <?php if (!empty($row['storage'])): ?>
    <div class="detail-card">
        <div class="detail-card-header">
            <div class="icon">📦</div>
            <h2>Hướng dẫn bảo quản</h2>
        </div>
        <div class="detail-card-body">
            <?php echo nl2br(htmlspecialchars($row['storage'])); ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ══════════ ĐÁNH GIÁ ══════════ -->
<div class="reviews-section" id="reviews">
    <div class="reviews-header">
        <span style="font-size:24px;">⭐</span>
        <h2>Đánh giá của khách hàng</h2>
    </div>

    <!-- Tổng quan rating -->
    <div class="reviews-summary">
        <div class="big-rating">
            <div class="big-num"><?php echo $rating; ?></div>
            <div class="big-stars">
                <?php for ($i = 1; $i <= 5; $i++) echo ($i <= round($rating)) ? '★' : '☆'; ?>
            </div>
            <div class="big-count"><?php echo $review_count; ?> đánh giá</div>
        </div>

        <div class="rating-bars">
            <?php
            // Phân phối rating giả lập theo id
            srand($row['id'] * 13);
            $dist = [5 => rand(60,80), 4 => rand(10,25), 3 => rand(3,10), 2 => rand(1,5), 1 => rand(0,3)];
            $total_dist = array_sum($dist);
            foreach ($dist as $star => $pct):
                $width = round($pct / $total_dist * 100);
            ?>
            <div class="bar-row">
                <span class="bar-label"><?php echo $star; ?> ★</span>
                <div class="bar-track">
                    <div class="bar-fill" style="width:<?php echo $width; ?>%"></div>
                </div>
                <span class="bar-count"><?php echo round($review_count * $pct / $total_dist); ?></span>
            </div>
            <?php endforeach; srand(); ?>
        </div>
    </div>

<!-- Danh sách review -->
<?php
$reviewers = [
    ['name'=>'Nguyễn Minh Tuấn','color'=>'#5c3290','stars'=>5,'text'=>'Sản phẩm chất lượng rất tốt, đúng như mô tả. Giao hàng nhanh, đóng gói cẩn thận. Sẽ mua lại lần sau!'],
    ['name'=>'Trần Thị Lan','color'=>'#0891b2','stars'=>5,'text'=>'Mình đã dùng nhiều lần rồi, lần nào cũng hài lòng. Chất lượng ổn định, giá cả hợp lý. Rất đáng mua!'],
    ['name'=>'Lê Văn Hùng','color'=>'#16a34a','stars'=>4,'text'=>'Sản phẩm khá tốt, dùng bình thường. Giao hàng đúng hẹn. Trừ 1 sao vì bao bì hơi nhăn một chút.'],
    ['name'=>'Phạm Thu Hà','color'=>'#d8511c','stars'=>5,'text'=>'Tuyệt vời! Chính xác như hình, dùng rất thích. Shop tư vấn nhiệt tình, giao hàng siêu tốc.'],
    ['name'=>'Hoàng Đức Anh','color'=>'#7c3aed','stars'=>5,'text'=>'Mua về dùng thử thấy rất ưng ý. Chất lượng vượt mong đợi so với giá tiền. Highly recommend!'],
    ['name'=>'Nguyễn Thu Trang','color'=>'#be185d','stars'=>5,'text'=>'Sản phẩm đẹp, chắc chắn. Giao hàng cực nhanh, đóng gói kỹ càng. Rất hài lòng!'],
    ['name'=>'Vũ Minh Khoa','color'=>'#0369a1','stars'=>4,'text'=>'Dùng được, chất lượng ổn so với giá. Sẽ cân nhắc mua lại.'],
    ['name'=>'Đặng Hải Yến','color'=>'#15803d','stars'=>5,'text'=>'Quá ưng ý! Đúng như mô tả, dùng rất thích. Sẽ giới thiệu cho bạn bè.'],
    ['name'=>'Bùi Thanh Long','color'=>'#b45309','stars'=>5,'text'=>'Hàng đẹp, giá tốt. Giao hàng nhanh hơn dự kiến. 5 sao không tiếc!'],
    ['name'=>'Lý Thị Hồng','color'=>'#7c3aed','stars'=>4,'text'=>'Sản phẩm ổn, dùng tốt. Bao bì đơn giản nhưng hàng bên trong được bảo vệ tốt.'],
    ['name'=>'Trương Văn Nam','color'=>'#0891b2','stars'=>5,'text'=>'Chất lượng vượt mong đợi! Mua lần đầu nhưng chắc chắn sẽ quay lại.'],
    ['name'=>'Phan Thị Mai','color'=>'#16a34a','stars'=>5,'text'=>'Sản phẩm xịn lắm, dùng rất thích. Cảm ơn shop đã tư vấn nhiệt tình!'],
];

// Sinh thêm review giả để đủ $review_count
srand($row['id'] * 31);
$names  = ['Minh Anh','Hùng Dũng','Thu Hương','Quang Huy','Bảo Châu','Ngọc Linh','Tuấn Kiệt','Mỹ Duyên'];
$colors = ['#5c3290','#0891b2','#16a34a','#d8511c','#7c3aed','#be185d','#0369a1','#b45309'];
$texts  = [
    'Sản phẩm rất tốt, đúng mô tả. Sẽ ủng hộ shop dài dài!',
    'Hàng đẹp, ship nhanh. Mình rất hài lòng.',
    'Chất lượng ổn định, giá cả phải chăng. Recommend!',
    'Dùng tốt, đóng gói cẩn thận. 5 sao cho shop.',
    'Sản phẩm vượt kỳ vọng, sẽ mua thêm lần sau.',
];
while (count($reviewers) < $review_count) {
    $n = $names[array_rand($names)];
    $reviewers[] = [
        'name'  => 'Khách hàng ' . $n,
        'color' => $colors[array_rand($colors)],
        'stars' => rand(4,5),
        'text'  => $texts[array_rand($texts)],
    ];
}
srand();

// Tạo ngày giả
$base = strtotime('2026-05-12');
foreach ($reviewers as $i => &$r) {
    $r['date'] = date('d/m/Y', $base - $i * rand(1,3) * 86400);
}
unset($r);

// Encode sang JSON để JS dùng
$reviewsJson = json_encode($reviewers, JSON_UNESCAPED_UNICODE);
?>

<div class="review-list" id="reviewList"></div>

<!-- Pagination -->
<div class="pagination" id="reviewPagination" style="
    display:flex; gap:8px; justify-content:center;
    margin-top:28px; flex-wrap:wrap;
"></div>


<script>
const allReviews   = <?php echo $reviewsJson; ?>;
const perPage      = 10;
let   currentPage  = 1;
const totalPages   = Math.ceil(allReviews.length / perPage);

function renderReviews(page) {
    currentPage = page;
    const start = (page - 1) * perPage;
    const slice = allReviews.slice(start, start + perPage);

    let html = '';
    slice.forEach(r => {
        let stars = '';
        for (let s = 1; s <= 5; s++) stars += s <= r.stars ? '★' : '☆';
        html += `
        <div class="review-card">
            <div class="review-top">
                <div class="reviewer-avatar" style="background:${r.color}">
                    ${[...r.name][0]}
                </div>
                <div>
                    <div class="reviewer-name">${r.name}</div>
                    <div class="review-stars">${stars}</div>
                </div>
                <span class="review-date">${r.date}</span>
            </div>
            <div class="review-text">${r.text}</div>
        </div>`;
    });
    document.getElementById('reviewList').innerHTML = html;
    renderPagination();

    // Scroll về đầu section review
    document.getElementById('reviews').scrollIntoView({behavior:'smooth', block:'start'});
}

function renderPagination() {
    const pg = document.getElementById('reviewPagination');
    let html  = '';

    const btnStyle = (active) => `
        style="padding:8px 14px; border-radius:8px; border:1.5px solid #ece9f5;
        background:${active ? '#5c3290' : '#fff'}; color:${active ? '#fff' : '#5c3290'};
        font-weight:700; cursor:pointer; font-family:inherit; font-size:14px;"
    `;

    // Prev
    html += `<button ${btnStyle(false)} ${currentPage===1?'disabled':''} onclick="renderReviews(${currentPage-1})">‹</button>`;

    // Pages
    for (let p = 1; p <= totalPages; p++) {
        if (totalPages > 7 && p > 3 && p < totalPages - 1 && Math.abs(p - currentPage) > 1) {
            if (p === 4 || p === totalPages - 2) html += `<span style="padding:8px 4px;color:#aaa">…</span>`;
            continue;
        }
        html += `<button ${btnStyle(p===currentPage)} onclick="renderReviews(${p})">${p}</button>`;
    }

    // Next
    html += `<button ${btnStyle(false)} ${currentPage===totalPages?'disabled':''} onclick="renderReviews(${currentPage+1})">›</button>`;

    pg.innerHTML = html;
}

/// Init
renderReviews(1);
</script>

</div> <!-- đóng .reviews-section -->
</div> <!-- đóng .page-wrapper -->

<!-- ══════════ FOOTER ══════════ -->
<footer style="background: #1e0a3c; color: #fff; width: 100%;">
    <div class="footer-grid">
        <div class="footer-brand">
            <img src="../logo.jpg" style="width:40px; margin-bottom:12px">
            <p>Chuyên cung cấp văn phòng phẩm chất lượng cao cho cá nhân và doanh nghiệp. Chất lượng là cam kết của chúng tôi.</p>
        </div>
        <div class="footer-col">
            <h4>Cửa Hàng</h4>
            <a href="Giấy&sổ.html">Sản phẩm</a>
            <a href="../Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới thiệu</a>
            <a href="../Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
            <a href="../Liên_hệ.html">Liên hệ</a>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ</h4>
            <a href="#">Chính sách đổi trả</a>
            <a href="#">Hướng dẫn mua hàng</a>
            <a href="#">Phương thức thanh toán</a>
            <a href="#">Chính sách bảo mật</a>
        </div>
        <div class="footer-col">
            <h4>Liên Hệ</h4>
            <a href="#">0913200206</a>
            <a href="#">inkcorner.contact@gmail.com</a>
            <a href="#">79, Hồ Tùng Mậu, Hà Nội</a>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© 2026 iNK Store. Tất cả các quyền được bảo lưu.</span>
        <span>Thiết kế bởi INK Team</span>
    </div>
</footer>

<script src="../js_gh.js"></script>
<script>
    const price    = <?php echo $row['price']; ?>;
    const maxStock = <?php echo $row['stock']; ?>;
    const prodId   = <?php echo $row['id']; ?>;
    const prodName = '<?php echo addslashes($row['name']); ?>';
    const prodImg  = '<?php echo basename($row['image']); ?>';

    function getQty() {
        return parseInt(document.getElementById('qtyInput').value) || 1;
    }

    function changeQty(delta) {
        let q = getQty() + delta;
        if (q < 1) q = 1;
        if (q > maxStock) q = maxStock;
        document.getElementById('qtyInput').value = q;
        document.getElementById('btnMinus').disabled = (q <= 1);
        document.getElementById('btnPlus').disabled  = (q >= maxStock);
        updateSubtotal();
    }

    function updateSubtotal() {
        const total = getQty() * price;
        document.getElementById('subtotalPrice').textContent =
            total.toLocaleString('vi-VN') + 'đ';
    }


function addToCartQty() {
    const qty = getQty();
    
    // Tái sử dụng hàm addToCart từ js_gh.js, thêm qty lần
    // Nhưng addToCart chỉ +1 mỗi lần, nên ta gọi trực tiếp với qty
    let cart = getCart();
    const existing = cart.find(item => item.id === prodId);
    
    if (existing) {
        existing.qty += qty;
    } else {
        cart.push({ id: prodId, name: prodName, price: price, image: prodImg, qty: qty });
    }
    
    saveCart(cart);
    updateCartUI();           // ✅ Đúng tên hàm trong js_gh.js
    showToast(prodName + ' đã được thêm vào giỏ!'); // ✅ Dùng toast có sẵn
    toggleCart();             // ✅ Mở drawer
}

function buyNowQty() {
    const qty = getQty();
    
    // Dùng buy_now_cart riêng để không ảnh hưởng giỏ hàng chính
    const tempCart = [{ 
        id: prodId, name: prodName, price: price, image: prodImg, qty: qty 
    }];
    localStorage.setItem('buy_now_cart', JSON.stringify(tempCart));
    window.location.href = '../Thanh_toán.php';
}

// Init - cập nhật badge khi trang load
document.getElementById('btnMinus').disabled = true;
document.getElementById('btnPlus').disabled  = (maxStock <= 1);
updateCartUI(); // ✅ Hiển thị đúng badge ngay khi load trang

document.getElementById('qtyInput').addEventListener('input', function() {
    let q = parseInt(this.value) || 1;
    if (q < 1) q = 1;
    if (q > maxStock) q = maxStock;
    this.value = q;
    document.getElementById('btnMinus').disabled = (q <= 1);
    document.getElementById('btnPlus').disabled  = (q >= maxStock);
    updateSubtotal();
});

document.getElementById('qtyInput').addEventListener('blur', function() {
    if (!this.value || parseInt(this.value) < 1) this.value = 1;
    updateSubtotal();
});

function addToCartQty() {
    const qty = getQty();
    let cart = getCart();
    const existing = cart.find(item => item.id === prodId);

    if (existing) {
        existing.qty += qty;
    } else {
        cart.push({ id: prodId, name: prodName, price: price, image: prodImg, qty: qty });
    }

    saveCart(cart);
    updateCartUI();
    showToast('✓ ' + prodName + ' đã được thêm vào giỏ!');
    // ❌ Bỏ toggleCart() — không mở drawer tự động
}

</script>
</body>
</html>