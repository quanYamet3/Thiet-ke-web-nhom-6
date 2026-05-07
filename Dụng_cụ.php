<?php include 'connect.php'; ?>
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
            <?php
                // Lấy tên file hiện tại trên thanh địa chỉ URL (có xử lý giải mã tiếng Việt và ký tự đặc biệt)
                $current_page = urldecode(basename($_SERVER['PHP_SELF']));
            ?>
            <ul class="category-list">
                <li><a href="sanpham.php" class="<?= ($current_page == 'sanpham.php') ? 'active' : '' ?>">Tất cả sản phẩm</a></li>
                <li><a href="Giấy&sổ.php" class="<?= ($current_page == 'Giấy&sổ.php') ? 'active' : '' ?>">Giấy & Sổ</a></li>
                <li><a href="Bút_viết.php" class="<?= ($current_page == 'Bút_viết.php') ? 'active' : '' ?>">Bút viết</a></li>
                <li><a href="Kẹp&Ghim.php" class="<?= ($current_page == 'Kẹp&Ghim.php') ? 'active' : '' ?>">Kẹp & Ghim</a></li>
                <li><a href="Dụng_cụ.php" class="<?= ($current_page == 'Dụng_cụ.php') ? 'active' : '' ?>">Dụng cụ</a></li>
                <li><a href="Lưu_trữ.php" class="<?= ($current_page == 'Lưu_trữ.php') ? 'active' : '' ?>">Lưu trữ</a></li>
                <li><a href="Hộp_đựng.php" class="<?= ($current_page == 'Hộp_đựng.php') ? 'active' : '' ?>">Hộp đựng</a></li>
                <li><a href="Khác.php" class="<?= ($current_page == 'Khác.php') ? 'active' : '' ?>">Các phụ kiện khác</a></li>
            </ul>
        </aside>

        <section class="product-area">
            <div class="breadcrumb">
                Trang chủ / Sản phẩm / <span>Tất cả sản phẩm</span>
            </div>
            
            <div class="product-grid">
                <?php
                // Truy vấn lấy TẤT CẢ sản phẩm từ CSDL
                $sql = "SELECT * FROM products WHERE category_id = 4";
                $result = $conn->query($sql);

                // Kiểm tra xem có sản phẩm nào không
                if ($result && $result->num_rows > 0) {
                    // Lặp qua từng sản phẩm và in ra HTML
                    while($row = $result->fetch_assoc()) {
                        // Định dạng lại giá tiền
                        $gia = number_format($row["price"], 0, ',', '.');
                        
                        echo '<div class="product-card">';
                        // Đường dẫn ảnh lấy từ cột 'image' trong CSDL
                        echo '    <img src="images/' . $row["image"] . '" alt="' . $row["name"] . '" class="product-img">';
                        echo '    <div class="product-title">' . $row["name"] . '</div>';
                        echo '    <div class="product-price">' . $gia . 'đ</div>';
                        
                        // Nút thêm vào giỏ đã được gắn động ID của sản phẩm
                        echo '    <button class="btn-add-cart" onclick="addToCart(' . $row["id"] . ')">Thêm vào giỏ</button>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>Hiện chưa có sản phẩm nào trong cửa hàng.</p>";
                }
                ?>
            </div>
        </section>

    </div>
    <br>
    <br>
    <?php include 'footer.php'; ?>
    <?php include 'chat_main.php'; ?>
</body>
</html>