<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS_San_Pham.css">
    <link rel="stylesheet" href="../CSS_header.css">
    <link rel="stylesheet" href="../CSS_footer.css">
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
                <li><a href="Lưu_trữ.php">Lưu trữ</a></li>
                <li><a href="Hộp_đựng.php" class="active">Hộp đựng</a></li>
                <li><a href="Khác.php">Các phụ kiện khác</a></li>
            </ul>
        </aside>

        <section class="product-area">
            <div class="breadcrumb">
                Trang chủ / Sản phẩm / <span>Hộp đựng</span>
            </div>

            <div class="product-grid">
                <div class="product-card">
                    <img src="#" alt="Hộp đựng bút đa năng-Deli-VP37" class="product-img">
                    <div class="product-title">Hộp đựng bút đa năng-Deli-VP37</div>
                    <div class="product-price">?đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="Hộp đựng bút xoay 360 độ-Deli-VP38" class="product-img">
                    <div class="product-title">Hộp đựng bút xoay 360 độ-Deli-VP38</div>
                    <div class="product-price">?đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="Khay đựng giấy - Plus - VP039" class="product-img">
                    <div class="product-title">Khay đựng giấy - Plus - VP039</div>
                    <div class="product-price">65.000đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="Hộp bút - Deli - VP040" class="product-img">
                    <div class="product-title">Hộp bút - Deli - VP040</div>
                    <div class="product-price">43.500đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
                <div class="product-card">
                    <img src="#" alt="Giá để tài liệu 3 tầng  - King Jim - VP041" class="product-img">
                    <div class="product-title">Giá để tài liệu 3 tầng  - King Jim - VP041</div>
                    <div class="product-price">120.000đ</div>
                    <button class="btn-add-cart">Thêm vào giỏ</button>
                </div>
            </div>
        </section>

    </div>
    <br>
    <br>
    <?php include 'footer.php'; ?>
</body>
</html>