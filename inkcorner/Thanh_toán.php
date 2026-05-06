<?php
session_start();
require_once 'config.php';

// Lấy thông tin user nếu đã đăng nhập
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Xử lý khi bấm "Xác nhận đặt hàng"
$order_success = false;
$order_id_new  = null;
$error_msg     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $receiver_name  = trim($_POST['receiver_name']);
    $receiver_phone = trim($_POST['receiver_phone']);
    $receiver_email = trim($_POST['receiver_email'] ?? '');
    $address        = trim($_POST['address']);
    $note           = trim($_POST['note'] ?? '');
    $shipping       = $_POST['shipping'] ?? 'standard';
    $payment        = $_POST['payment']  ?? 'cod';
    $shipping_fee   = ($shipping === 'express') ? 30000 : 0;

    // Lấy giỏ hàng từ session
    $cart_items = $_SESSION['cart'] ?? [];

    if (empty($receiver_name) || empty($receiver_phone) || empty($address)) {
        $error_msg = 'Vui lòng điền đầy đủ thông tin giao hàng!';
    } elseif (empty($cart_items)) {
        $error_msg = 'Giỏ hàng trống! Hãy thêm sản phẩm trước.';
    } else {
        // Tính tổng tiền
        $total = 0;
        $items_detail = [];
        foreach ($cart_items as $product_id => $qty) {
            $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $prod = $stmt->get_result()->fetch_assoc();
            if ($prod) {
                $subtotal = $prod['price'] * $qty;
                $total   += $subtotal;
                $items_detail[] = [
                    'product_id' => $prod['id'],
                    'name'       => $prod['name'],
                    'quantity'   => $qty,
                    'unit_price' => $prod['price'],
                    'subtotal'   => $subtotal,
                ];
            }
        }
        $total += $shipping_fee;

        // Bắt đầu transaction
        $conn->begin_transaction();
        try {
            // 1. Tạo đơn hàng
            $user_id = $user ? $user['id'] : null;
            $stmt = $conn->prepare(
                "INSERT INTO orders (user_id, receiver_name, receiver_phone, receiver_email, address, note, shipping_method, shipping_fee, total_amount, payment_method)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("issssssdds",
                $user_id, $receiver_name, $receiver_phone, $receiver_email,
                $address, $note, $shipping, $shipping_fee, $total, $payment
            );
            $stmt->execute();
            $order_id_new = $conn->insert_id;

            // 2. Lưu từng sản phẩm vào order_items
            foreach ($items_detail as $item) {
                $stmt2 = $conn->prepare(
                    "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt2->bind_param("iiidd",
                    $order_id_new, $item['product_id'],
                    $item['quantity'], $item['unit_price'], $item['subtotal']
                );
                $stmt2->execute();
            }

            $conn->commit();

            // 3. Xóa giỏ hàng sau khi đặt thành công
            $_SESSION['cart'] = [];
            $order_success = true;

        } catch (Exception $e) {
            $conn->rollback();
            $error_msg = 'Đặt hàng thất bại: ' . $e->getMessage();
        }
    }
}

// Lấy giỏ hàng từ session để hiển thị
$cart_items   = $_SESSION['cart'] ?? [];
$cart_details = [];
$subtotal_all = 0;
foreach ($cart_items as $product_id => $qty) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $prod = $stmt->get_result()->fetch_assoc();
    if ($prod) {
        $sub = $prod['price'] * $qty;
        $subtotal_all += $sub;
        $cart_details[] = ['product' => $prod, 'qty' => $qty, 'sub' => $sub];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - InkCorner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <link rel="stylesheet" href="CSS_Thanh_toán.css">
</head>
<body>
<header>
<nav>
    <a href="Trang_chủ.php" class="nav-logo"><img src="logo.jpg" class="logo-img"></a>
    <div class="nav-links">
      <a href="Trang_chủ.php">Trang Chủ</a>
      <a href="#">Sản Phẩm</a>
      <a href="#">Giới Thiệu</a>
      <a href="#">Blog</a>
      <a href="#">Liên Hệ</a>
    </div>
    <div class="nav-right">
      <div class="search-box">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Tìm kiếm...">
      </div>
      <a href="User.php" class="btn-account">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <?= $user ? htmlspecialchars(explode(' ', $user['fullname'])[0]) : 'Tài khoản' ?>
      </a>
    </div>
</nav>
</header>

<?php if ($order_success): ?>
<!-- ===== THÀNH CÔNG ===== -->
<div class="checkout-wrapper" style="text-align:center;padding:80px 20px;">
    <div style="font-size:64px;margin-bottom:20px;">🎉</div>
    <h2 style="color:#5c3290;font-size:28px;margin-bottom:12px;">Đặt hàng thành công!</h2>
    <p style="color:#666;font-size:16px;margin-bottom:8px;">
        Mã đơn hàng của bạn: <strong style="color:#d8511c;">#INK<?= str_pad($order_id_new, 5, '0', STR_PAD_LEFT) ?></strong>
    </p>
    <p style="color:#888;font-size:14px;margin-bottom:32px;">Chúng tôi sẽ liên hệ xác nhận đơn hàng sớm nhất!</p>
    <a href="Trang_chủ.php" style="padding:14px 36px;background:#5c3290;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;">
        Tiếp tục mua sắm
    </a>
    <?php if ($user): ?>
    <br><br>
    <a href="User.php?page=orders" style="color:#5c3290;font-size:14px;">Xem lịch sử đơn hàng →</a>
    <?php endif; ?>
</div>

<?php else: ?>
<!-- ===== TRANG THANH TOÁN ===== -->
<div class="checkout-wrapper">
    <!-- Thanh tiến trình -->
    <div class="progress-bar">
        <div class="progress-step completed"><i class="fas fa-shopping-cart"></i> Giỏ hàng</div>
        <div class="progress-line completed"></div>
        <div class="progress-step active"><i class="fas fa-credit-card"></i> Thanh toán</div>
        <div class="progress-line"></div>
        <div class="progress-step"><i class="fas fa-check-circle"></i> Hoàn tất</div>
    </div>

    <?php if ($error_msg): ?>
    <div style="background:#fff0f0;color:#c0392b;padding:14px 20px;border-radius:8px;margin-bottom:20px;border:1px solid #f5c6cb;text-align:center;">
        ⚠️ <?= $error_msg ?>
    </div>
    <?php endif; ?>

    <form method="POST">
    <input type="hidden" name="action" value="checkout">
    <div class="checkout-container">

        <!-- Cột trái -->
        <div class="main-content">

            <!-- Thông tin giao hàng -->
            <div class="card">
                <h2 class="card-title">1. Thông tin giao hàng</h2>
                <div class="form-group">
                    <label>Họ và tên người nhận <span class="required">*</span></label>
                    <input type="text" name="receiver_name" placeholder="Nhập họ và tên đầy đủ"
                           value="<?= $user ? htmlspecialchars($user['fullname']) : '' ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số điện thoại <span class="required">*</span></label>
                        <input type="tel" name="receiver_phone" placeholder="090xxxxxxx"
                               value="<?= $user ? htmlspecialchars($user['phone'] ?? '') : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email (Không bắt buộc)</label>
                        <input type="email" name="receiver_email" placeholder="Để nhận hóa đơn"
                               value="<?= $user ? htmlspecialchars($user['email']) : '' ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Địa chỉ nhận hàng <span class="required">*</span></label>
                    <textarea name="address" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required><?= $user ? htmlspecialchars($user['address'] ?? '') : '' ?></textarea>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Ghi chú (Không bắt buộc)</label>
                    <textarea name="note" placeholder="Ví dụ: Giao hàng giờ hành chính..."></textarea>
                </div>
            </div>

            <!-- Phương thức giao hàng -->
            <div class="card">
                <h2 class="card-title">2. Phương thức giao hàng</h2>
                <label class="shipping-option">
                    <input type="radio" name="shipping" value="standard" checked>
                    <div class="shipping-content">
                        <span class="shipping-name"><i class="fas fa-truck"></i> Giao nhanh (2-3 ngày)</span>
                        <span class="shipping-price">Miễn phí</span>
                    </div>
                </label>
                <label class="shipping-option">
                    <input type="radio" name="shipping" value="express">
                    <div class="shipping-content">
                        <span class="shipping-name"><i class="fas fa-bolt"></i> Giao hỏa tốc (Trong 2h)</span>
                        <span class="shipping-price">+30.000đ</span>
                    </div>
                </label>
            </div>

            <!-- Phương thức thanh toán -->
            <div class="card">
                <h2 class="card-title">3. Phương thức thanh toán</h2>
                <label class="shipping-option">
                    <input type="radio" name="payment" value="cod" checked>
                    <div class="shipping-content">
                        <span class="shipping-name"><i class="fas fa-money-bill"></i> Thanh toán khi nhận hàng (COD)</span>
                    </div>
                </label>
                <label class="shipping-option">
                    <input type="radio" name="payment" value="bank_transfer">
                    <div class="shipping-content">
                        <span class="shipping-name"><i class="fas fa-university"></i> Chuyển khoản ngân hàng</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Cột phải: Tóm tắt -->
        <div class="sidebar">
            <div class="card">
                <h2 class="card-title">Tóm tắt đơn hàng</h2>

                <?php if (empty($cart_details)): ?>
                <p style="color:#999;text-align:center;padding:20px;">Giỏ hàng trống</p>
                <?php else: ?>
                <?php foreach ($cart_details as $item): ?>
                <div class="product-item">
                    <div class="product-img">
                        <?php if ($item['product']['image']): ?>
                            <img src="images/<?= htmlspecialchars($item['product']['image']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:4px;" onerror="this.style.display='none'">
                        <?php else: ?>
                            <i class="fas fa-image"></i>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($item['product']['name']) ?></div>
                        <div class="product-meta">
                            <span style="font-size:13px;color:#666;">x<?= $item['qty'] ?></span>
                            <span class="product-price"><?= number_format($item['sub'], 0, ',', '.') ?>đ</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span id="subtotalDisplay"><?= number_format($subtotal_all, 0, ',', '.') ?>đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span id="shippingDisplay">0đ</span>
                </div>
                <div class="summary-total">
                    <span>Tổng cộng</span>
                    <span class="total-price" id="totalDisplay"><?= number_format($subtotal_all, 0, ',', '.') ?>đ</span>
                </div>

                <button type="submit" class="btn-submit">Xác nhận đặt hàng</button>

                <ul class="trust-badges">
                    <li><i class="fas fa-shield-halved"></i> Cam kết 100% chính hãng</li>
                    <li><i class="fas fa-lock"></i> Bảo mật thông tin SSL</li>
                    <li><i class="fas fa-rotate-left"></i> Đổi trả dễ dàng trong 30 ngày</li>
                </ul>
            </div>
        </div>
    </div>
    </form>
</div>
<?php endif; ?>

<footer>
    <div class="footer-grid">
      <div class="footer-brand">
        <img src="logo.jpg" style="width:40px;margin-bottom:12px">
        <p>Chuyên cung cấp văn phòng phẩm chất lượng cao.</p>
      </div>
      <div class="footer-col"><h4>Cửa Hàng</h4><a href="#">Sản phẩm</a><a href="#">Giới thiệu</a><a href="#">Blog</a><a href="#">Liên hệ</a></div>
      <div class="footer-col"><h4>Hỗ trợ</h4><a href="#">Chính sách đổi trả</a><a href="#">Chính sách vận chuyển</a><a href="#">Chính sách bảo mật</a></div>
      <div class="footer-col"><h4>Liên Hệ</h4><a href="#">0913200206</a><a href="#">inkcorner.contact@gmail.com</a><a href="#">79, Hồ Tùng Mậu, Hà Nội</a></div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 iNK Store. Tất cả các quyền được bảo lưu.</span>
      <span>Thiết kế bởi INK Team</span>
    </div>
</footer>

<script>
// Cập nhật phí ship khi chọn phương thức
const subtotal = <?= $subtotal_all ?>;
document.querySelectorAll('input[name="shipping"]').forEach(r => {
    r.addEventListener('change', function() {
        const fee    = this.value === 'express' ? 30000 : 0;
        const total  = subtotal + fee;
        document.getElementById('shippingDisplay').textContent = fee > 0 ? '+30.000đ' : '0đ';
        document.getElementById('totalDisplay').textContent    = total.toLocaleString('vi-VN') + 'đ';
    });
});
</script>
</body>
</html>
