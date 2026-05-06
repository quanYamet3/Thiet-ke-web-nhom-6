<?php 
include '../ket_noi.php'; 
// Lấy sản phẩm thuộc danh mục Giấy & Sổ (category_id = 1)
$sql = "SELECT * FROM products WHERE category_id = 1";
$result = mysqli_query($conn, $sql);
?>
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
    <div class="container main-content" style="margin-top: 100px;">
        <aside class="sidebar">
            <h3>DANH MỤC SẢN PHẨM</h3>
            <ul class="category-list">
<li><a href="Tất_cả_sản_phẩm.php">Tất cả sản phẩm</a></li>
                <li><a href="Giấy&sổ.php" class="active">Giấy & Sổ</a></li>
                <li><a href="Bút_viết.php">Bút viết</a></li>
                <li><a href="Kẹp&Ghim.php">Kẹp & Ghim</a></li>
                <li><a href="Dụng_cụ.php">Dụng cụ</a></li>
                <li><a href="Lưu_trữ.php">Lưu trữ</a></li>
                <li><a href="Hộp_đựng.php">Hộp đựng</a></li>
                <li><a href="Khác.php">Các phụ kiện khác</a></li>
            </ul>
        </aside>

        <section class="product-area">
            <div class="breadcrumb">Trang chủ / Sản phẩm / <span>Giấy & Sổ</span></div>
            <div class="product-grid">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="product-card">
                        <a href="chi_tiet.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                            <img src="../images/<?php echo basename($row['image']); ?>" alt="sp" class="product-img">
                            <div class="product-title"><?php echo $row['name']; ?></div>
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
    <br>
    <br>
    <?php include 'footer.php'; ?>
    <?php include 'chat_main.php'; ?>
    <script src="../js_gh.js"></script>
</body>
</html>