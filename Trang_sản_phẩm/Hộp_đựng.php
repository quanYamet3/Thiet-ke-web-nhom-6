<?php 
include '../ket_noi.php'; 
$sql = "SELECT * FROM products WHERE category_id = 6";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS_San_Pham.css">
    <link rel="stylesheet" href="../CSS_header.css">
    <link rel="stylesheet" href="../CSS_footer.css">
    <title>Hộp đựng - Ink Store</title>
</head>
<body>

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

<div class="container main-content" style="margin-top: 100px;">
    <aside class="sidebar">
        <h3>DANH MỤC SẢN PHẨM</h3>
        <ul class="category-list">
            <li><a href="Tất_cả_sản_phẩm.php">Tất cả sản phẩm</a></li>
            <li><a href="Giấy&sổ.php">Giấy & Sổ</a></li>
            <li><a href="Bút_viết.php">Bút viết</a></li>
            <li><a href="Kẹp&ghim.php">Kẹp & Ghim</a></li>
            <li><a href="Dụng_cụ.php">Dụng cụ</a></li>
            <li><a href="Lưu_trữ.php">Lưu trữ</a></li>
            <li><a href="Hộp_đựng.php" class="active">Hộp đựng</a></li>
            <li><a href="Khác.php">Các phụ kiện khác</a></li>
        </ul>
    </aside>

    <section class="product-area">
        <div class="breadcrumb">Trang chủ / Sản phẩm / <span>Hộp đựng</span></div>
        
        <div class="product-grid">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="product-card">
                    <a href="chi_tiet.php?id=<?php echo $row['id']; ?>" style="text-decoration:none; color:inherit;">
                        <img src="../images/<?php echo basename($row['image']); ?>" 
                             alt="<?php echo htmlspecialchars($row['name']); ?>" class="product-img">
                        <div class="product-title"><?php echo htmlspecialchars($row['name']); ?></div>
                    </a>
                    <div class="product-price"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</div>
                    
                    <div style="display:flex; gap:8px; margin-top:10px;">
                        <button class="btn-add-cart" style="flex:1;"
                            onclick="addToCart(
                                <?php echo $row['id']; ?>,
                                '<?php echo addslashes($row['name']); ?>',
                                <?php echo $row['price']; ?>,
                                '<?php echo basename($row['image']); ?>'
                            )">
                            Thêm vào giỏ
                        </button>
<button style="flex:1; background:#d8511c; color:#fff;
        border-radius:6px; font-weight:bold; font-size:14px;
        border:none; cursor:pointer; padding:10px;"
    onclick="buyNow(
        <?php echo $row['id']; ?>,
        '<?php echo addslashes($row['name']); ?>',
        <?php echo $row['price']; ?>,
        '<?php echo basename($row['image']); ?>',
        1
    )">
    Mua ngay
</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</div>

<br><br>

<footer style="background: #1e0a3c; color: #fff; width: 100%;">
    <div class="footer-grid">
        <div class="footer-brand">
            <img src="../logo.jpg" style="width:40px; margin-bottom:12px">
            <p>Chuyên cung cấp văn phòng phẩm chất lượng cao cho cá nhân và doanh nghiệp.</p>
        </div>
        <div class="footer-col">
            <h4>Cửa Hàng</h4>
            <a href="Giấy&sổ.php">Sản phẩm</a>
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
</body>
</html>