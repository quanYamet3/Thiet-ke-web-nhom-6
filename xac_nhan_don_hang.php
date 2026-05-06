<?php
include 'ket_noi.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if (!$order_id) { header('Location: index.php'); exit; }

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM orders WHERE id = $order_id";
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);
if (!$order) { die('Đơn hàng không tồn tại!'); }

// Lấy danh sách sản phẩm trong đơn
$sql_items = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = mysqli_query($conn, $sql_items);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - Ink Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background: #f5f4f9;
            margin: 0;
        }

        .success-wrapper {
            max-width: 700px;
            margin: 100px auto 60px;
            padding: 0 20px;
        }

        /* Hộp thành công */
        .success-box {
            background: #fff;
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            border: 1px solid #ece9f5;
            box-shadow: 0 8px 40px rgba(92,50,144,0.08);
            margin-bottom: 24px;
        }

        .success-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            animation: pop 0.5s ease;
        }

        @keyframes pop {
            0%   { transform: scale(0); opacity: 0; }
            70%  { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .success-title {
            font-size: 26px;
            font-weight: 800;
            color: #16a34a;
            margin-bottom: 8px;
        }

        .success-sub {
            font-size: 15px;
            color: #666;
            margin-bottom: 24px;
        }

        .order-code {
            display: inline-block;
            background: #f0ebfa;
            color: #5c3290;
            font-weight: 800;
            font-size: 18px;
            padding: 10px 28px;
            border-radius: 12px;
            letter-spacing: 1px;
        }

        /* Thông tin đơn hàng */
        .order-detail-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #ece9f5;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f0ebfa;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }

        .card-body { padding: 20px 24px; }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
        }

        .info-row:last-child { border: none; }
        .info-row span:first-child { color: #888; }
        .info-row strong { color: #1a1a2e; text-align: right; max-width: 60%; }

        /* Sản phẩm */
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
            gap: 12px;
        }

        .item-row:last-child { border: none; }

        .item-name { color: #1a1a2e; font-weight: 500; flex: 1; }
        .item-qty  { color: #888; min-width: 60px; text-align: center; }
        .item-price{ color: #d8511c; font-weight: 700; min-width: 80px; text-align: right; }

        /* Tổng tiền */
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 16px 24px;
            background: #f5f0ff;
            border-top: 2px solid #ece9f5;
        }

        .total-row span { font-size: 16px; font-weight: 700; }
        .total-row .total-price { font-size: 22px; font-weight: 800; color: #d8511c; }

        /* Trạng thái */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff8e1;
            color: #d97706;
            border: 1px solid #fde68a;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }

        .btn-home {
            flex: 1;
            padding: 14px;
            background: linear-gradient(135deg, #5c3290, #7c3aed);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-home:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(92,50,144,0.3); }

        .btn-continue {
            flex: 1;
            padding: 14px;
            background: #fff;
            color: #5c3290;
            border: 2px solid #5c3290;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-continue:hover { background: #f0ebfa; }
    </style>
</head>
<body>

<header>
    <nav>
        <a href="#" class="nav-logo"><img src="logo.jpg" class="logo-img"></a>
        <div class="nav-links">
            <a href="Trang_chủ.php">Trang Chủ</a>
            <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản Phẩm</a>
            <a href="Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới Thiệu</a>
            <a href="Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
            <a href="Liên_hệ.html">Liên Hệ</a>
        </div>
        <div class="nav-right">
            <button class="btn-account">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Tài khoản
            </button>
        </div>
    </nav>
</header>

<div class="success-wrapper">

    <!-- Hộp thành công -->
    <div class="success-box">
        <div class="success-icon">✓</div>
        <div class="success-title">Đặt hàng thành công!</div>
        <div class="success-sub">Cảm ơn bạn đã mua hàng tại Ink Store. Chúng tôi sẽ liên hệ xác nhận sớm nhất.</div>
        <div class="order-code">#DH<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></div>
    </div>

    <!-- Thông tin giao hàng -->
    <div class="order-detail-card">
        <div class="card-header">
            <span>📦</span>
            <h3>Thông tin giao hàng</h3>
        </div>
        <div class="card-body">
            <div class="info-row">
                <span>Người nhận</span>
                <strong><?php echo htmlspecialchars($order['ho_ten']); ?></strong>
            </div>
            <div class="info-row">
                <span>Số điện thoại</span>
                <strong><?php echo htmlspecialchars($order['sdt']); ?></strong>
            </div>
            <?php if ($order['email']): ?>
            <div class="info-row">
                <span>Email</span>
                <strong><?php echo htmlspecialchars($order['email']); ?></strong>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <span>Địa chỉ</span>
                <strong><?php echo htmlspecialchars($order['dia_chi']); ?></strong>
            </div>
            <div class="info-row">
                <span>Vận chuyển</span>
                <strong><?php echo htmlspecialchars($order['van_chuyen']); ?></strong>
            </div>
            <div class="info-row">
                <span>Thanh toán</span>
                <strong><?php echo htmlspecialchars($order['thanh_toan']); ?></strong>
            </div>
            <div class="info-row">
                <span>Trạng thái</span>
                <strong><span class="status-badge">⏳ <?php echo $order['trang_thai']; ?></span></strong>
            </div>
            <div class="info-row">
                <span>Thời gian đặt</span>
                <strong><?php echo date('H:i - d/m/Y', strtotime($order['ngay_dat'])); ?></strong>
            </div>
        </div>
    </div>

    <!-- Sản phẩm đã đặt -->
    <div class="order-detail-card">
        <div class="card-header">
            <span>🛒</span>
            <h3>Sản phẩm đã đặt</h3>
        </div>
        <div class="card-body">
            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
            <div class="item-row">
                <span class="item-name"><?php echo htmlspecialchars($item['ten_sp']); ?></span>
                <span class="item-qty">x<?php echo $item['so_luong']; ?></span>
                <span class="item-price"><?php echo number_format($item['gia'] * $item['so_luong'], 0, ',', '.'); ?>đ</span>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="total-row">
            <span>Tổng cộng</span>
            <span class="total-price"><?php echo number_format($order['tong_tien'], 0, ',', '.'); ?>đ</span>
        </div>
    </div>

    <!-- Nút hành động -->
    <div class="action-buttons">
        <a href="Trang_chủ.php" class="btn-home">🏠 Về trang chủ</a>
        <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php" class="btn-continue">🛍️ Tiếp tục mua sắm</a>
    </div>

</div>

<footer style="background:#1e0a3c;color:#fff;width:100%;">
    <div class="footer-grid">
        <div class="footer-brand">
            <img src="logo.jpg" style="width:40px;margin-bottom:12px">
            <p>Chuyên cung cấp văn phòng phẩm chất lượng cao.</p>
        </div>
        <div class="footer-col">
            <h4>Cửa Hàng</h4>
            <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản phẩm</a>
        </div>
        <div class="footer-col">
            <h4>Liên Hệ</h4>
            <a href="#">0913200206</a>
            <a href="#">inkcorner.contact@gmail.com</a>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© 2026 iNK Store. Tất cả các quyền được bảo lưu.</span>
        <span>Thiết kế bởi INK Team</span>
    </div>
</footer>

</body>
</html>