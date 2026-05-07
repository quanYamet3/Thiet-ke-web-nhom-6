<?php 
include 'config.php'; // Đảm bảo file này chứa kết nối $conn

// 1. Lấy ID sản phẩm từ URL và kiểm tra tính hợp lệ
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Truy vấn dữ liệu sản phẩm từ backend
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Nếu không tìm thấy sản phẩm, quay về trang chính
if (!$product) {
    header("Location: sanpham.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $product['name'] ?> - InkCorner</title>
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <style>
        .detail-wrapper { max-width: 1200px; margin: 100px auto 50px; padding: 0 20px; }
        .product-main { display: flex; gap: 50px; margin-bottom: 40px; }
        .product-image { flex: 1; text-align: center; }
        .product-image img { max-width: 100%; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        .product-info { flex: 1.2; }
        .p-name { font-size: 32px; color: #5c3290; font-weight: bold; margin-bottom: 10px; line-height: 1.2; }
        .p-price { font-size: 36px; color: #d8511c; font-weight: 800; margin-bottom: 25px; }
        
        .info-block { margin-bottom: 25px; }
        .info-label { font-weight: 700; color: #1a1a2e; text-transform: uppercase; font-size: 14px; margin-bottom: 8px; display: block; border-left: 4px solid #5c3290; padding-left: 10px; }
        .info-text { color: #64748b; line-height: 1.7; font-size: 15px; text-align: justify; }
        
        .p-status { margin-bottom: 20px; font-size: 14px; }
        .status-tag { padding: 4px 12px; border-radius: 20px; font-weight: bold; background: #f0fff4; color: #1a7a3c; }
        .stock-num { color: #888; margin-left: 10px; }

        .btn-buy { 
            background: linear-gradient(135deg, #5c3290, #d8511c); color: #fff; 
            border: none; padding: 18px; border-radius: 12px; 
            width: 100%; font-size: 18px; font-weight: 700; cursor: pointer;
            box-shadow: 0 4px 15px rgba(216, 81, 28, 0.3); transition: 0.3s;
        }
        .btn-buy:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(216, 81, 28, 0.4); }

        .extra-specs { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 50px; padding-top: 30px; border-top: 2px solid #eee; }
        .spec-card { background: #fdfbff; padding: 25px; border-radius: 15px; border: 1px solid #f3e8ff; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="detail-wrapper">
        <div class="product-main">
            <div class="product-image">
                <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

            <div class="product-info">
                <h1 class="p-name"><?= htmlspecialchars($product['name']) ?></h1>
                <div class="p-price"><?= number_format($product['price'], 0, ',', '.') ?>đ</div>

                <div class="p-status">
                    <span class="status-tag">✓ Đang kinh doanh</span>
                    <span class="stock-num">Kho còn: <strong><?= $product['stock'] ?></strong> sản phẩm</span>
                </div>

                <div class="info-block">
                    <span class="info-label">Mô tả tổng quan</span>
                    <div class="info-text"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
                </div>

                <?php if ($product['features']): ?>
                <div class="info-block">
                    <span class="info-label">Đặc điểm nổi bật</span>
                    <div class="info-text"><?= nl2br(htmlspecialchars($product['features'])) ?></div>
                </div>
                <?php endif; ?>

                <button class="btn-buy" onclick="addToCart(<?= $product['id'] ?>)">
                    MUA NGAY - GIAO TẬN NƠI
                </button>
            </div>
        </div>

        <div class="extra-specs">
            <div class="spec-card">
                <span class="info-label">Ứng dụng thực tế</span>
                <div class="info-text"><?= nl2br(htmlspecialchars($product['application'])) ?></div>
            </div>
            <div class="spec-card">
                <span class="info-label">Hướng dẫn bảo quản</span>
                <div class="info-text"><?= nl2br(htmlspecialchars($product['storage'])) ?></div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>