<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS_San_Pham.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <link rel="stylesheet" href="CSS_AI.css">
</head>
<body>
    <?php include 'header.php'; ?>
<br>
<br>
<br>
    <div class="container main-content">
        
        <aside class="sidebar">
            <h3>DANH MỤC SẢN PHẨM</h3>
            <ul class="category-list">
                <li><a href="sanpham.php">Tất cả sản phẩm</a></li>
                <li><a href="Giấy&sổ.php">Giấy & Sổ</a></li>
                <li><a href="Bút_viết.php">Bút viết</a></li>
                <li><a href="Kẹp&Ghim.php">Kẹp & Ghim</a></li>
                <li><a href="Dụng_cụ.php">Dụng cụ</a></li>
                <li><a href="Lưu_trữ.php" class="active">Lưu trữ</a></li>
                <li><a href="Hộp_đựng.php">Hộp đựng</a></li>
                <li><a href="Khác.php">Các phụ kiện khác</a></li>
            </ul>
        </aside>

        <section class="product-area">
            <div class="breadcrumb">
                Trang chủ / Sản phẩm / <span>Lưu trữ</span>
            </div>

            <div class="product-grid">
                <div class="product-card">
                    <img src="#" alt="Bìa hồ sơ A4 - Thiên  - 180gsm - VP031" class="product-img">
                    <div class="product-title">Bìa hồ sơ A4 - Thiên  - 180gsm - VP031</div>
                    <div class="product-price">26.000đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="Bìa còng 2 lỗ - King Jim - 250gsm - VP032" class="product-img">
                    <div class="product-title">Bìa còng 2 lỗ - King Jim - 250gsm - VP032</div>
                    <div class="product-price">43.500đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="Bìa còng 3 lỗ - Deli - 250gsm - VP033" class="product-img">
                    <div class="product-title">Bìa còng 3 lỗ - Deli - 250gsm - VP033</div>
                    <div class="product-price">45.000đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="File nhựa - Plus - VP034" class="product-img">
                    <div class="product-title">File nhựa - Plus - VP034</div>
                    <div class="product-price">17.000đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="Túi đựng tài liệu - Thiên Long - VP035" class="product-img">
                    <div class="product-title">Túi đựng tài liệu - Thiên Long - VP035</div>
                    <div class="product-price">17.500đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="Hộp đựng hồ sơ - Deli - VP036" class="product-img">
                    <div class="product-title">Hộp đựng hồ sơ - Deli - VP036</div>
                    <div class="product-price">90.000đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
            </div>
        </section>

    </div>
    <br>
    <br>
    <?php include 'footer.php'; ?>
    <?php include 'chat_main.php'; ?>
</body>
</html>