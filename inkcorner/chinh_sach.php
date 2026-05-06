<?php
// chinh_sach.php - Trang chính sách động
// Dùng chung cho tất cả các trang chính sách
$page = $_GET['page'] ?? 'doi-tra';

$pages = [
    'doi-tra' => [
        'title' => '🔄 Chính Sách Đổi Trả & Hoàn Tiền',
        'content' => '
            <p>InkCorner cam kết mang đến cho khách hàng những sản phẩm văn phòng phẩm chất lượng cùng trải nghiệm mua sắm đáng tin cậy.</p>
            <h3>📋 Điều kiện đổi trả</h3>
            <p>Khách hàng có thể yêu cầu đổi hoặc trả sản phẩm khi:</p>
            <ul>
                <li>Sản phẩm gặp lỗi từ phía nhà sản xuất</li>
                <li>Bị hư hỏng trong quá trình vận chuyển</li>
                <li>Giao sai so với đơn hàng hoặc thiếu sản phẩm</li>
            </ul>
            <h3>⏰ Thời gian đổi trả</h3>
            <p>Trong vòng <strong>3–7 ngày</strong> kể từ khi nhận hàng. Vui lòng liên hệ sớm nhất có thể.</p>
            <h3>💰 Hoàn tiền</h3>
            <p>Thời gian hoàn tiền từ <strong>3–7 ngày làm việc</strong> qua chuyển khoản ngân hàng hoặc phương thức thanh toán ban đầu.</p>
            <h3>🚚 Chi phí vận chuyển đổi trả</h3>
            <ul>
                <li><strong>Lỗi từ shop:</strong> InkCorner chịu toàn bộ phí vận chuyển</li>
                <li><strong>Lý do cá nhân:</strong> Khách hàng chịu phí vận chuyển</li>
            </ul>
            <h3>❌ Trường hợp không được đổi trả</h3>
            <ul>
                <li>Sản phẩm đã qua sử dụng hoặc bị hư hỏng do người dùng</li>
                <li>Không còn đầy đủ phụ kiện hoặc bao bì</li>
                <li>Quá thời gian quy định</li>
                <li>Không có bằng chứng mua hàng hợp lệ</li>
            </ul>
        '
    ],
    'bao-mat' => [
        'title' => '🔒 Chính Sách Bảo Mật',
        'content' => '
            <p>InkCorner cam kết tôn trọng và bảo vệ quyền riêng tư của khách hàng khi truy cập và mua sắm tại website.</p>
            <h3>📊 Thông tin thu thập</h3>
            <p>Chúng tôi có thể thu thập: họ tên, địa chỉ email, số điện thoại, địa chỉ nhận hàng và thông tin giao dịch nhằm mục đích xử lý đơn hàng và cải thiện trải nghiệm.</p>
            <h3>🎯 Mục đích sử dụng</h3>
            <ul>
                <li>Xác nhận và xử lý đơn hàng</li>
                <li>Liên hệ hỗ trợ khách hàng</li>
                <li>Gửi thông tin sản phẩm mới (nếu đồng ý)</li>
                <li>Cải thiện chất lượng dịch vụ</li>
            </ul>
            <h3>🤝 Chia sẻ thông tin</h3>
            <p>InkCorner <strong>không bán, trao đổi</strong> thông tin cá nhân cho bên thứ ba. Chỉ chia sẻ với đơn vị vận chuyển/thanh toán khi cần thiết.</p>
            <h3>🛡️ Bảo mật dữ liệu</h3>
            <p>Hệ thống lưu trữ được kiểm soát và giới hạn quyền truy cập, cập nhật thường xuyên để đảm bảo an toàn.</p>
            <h3>✏️ Quyền của khách hàng</h3>
            <p>Bạn có quyền yêu cầu kiểm tra, cập nhật hoặc xóa thông tin cá nhân bất cứ lúc nào bằng cách liên hệ với InkCorner.</p>
        '
    ],
    'van-chuyen' => [
        'title' => '🚚 Chính Sách Vận Chuyển',
        'content' => '
            <p>InkCorner cam kết mang đến dịch vụ giao hàng nhanh chóng, an toàn và minh bạch trên toàn quốc.</p>
            <h3>🗺️ Phạm vi giao hàng</h3>
            <p>Hỗ trợ giao hàng toàn lãnh thổ Việt Nam qua các đơn vị vận chuyển uy tín.</p>
            <h3>⏱️ Thời gian giao hàng</h3>
            <ul>
                <li><strong>Xử lý đơn:</strong> 1–2 ngày làm việc</li>
                <li><strong>Nội thành/trung tâm tỉnh:</strong> 2–4 ngày làm việc</li>
                <li><strong>Ngoại thành/vùng xa:</strong> 3–7 ngày làm việc</li>
                <li><strong>Giao hỏa tốc:</strong> Trong 2 giờ (+30.000đ)</li>
            </ul>
            <h3>💵 Phí vận chuyển</h3>
            <p>Phí được tính theo địa chỉ, khối lượng và đơn vị vận chuyển. Hiển thị rõ tại bước thanh toán. Một số chương trình có miễn phí vận chuyển.</p>
            <h3>📦 Nhận hàng</h3>
            <p>Khách hàng nên kiểm tra tình trạng gói hàng trước khi nhận. Nếu phát hiện vấn đề, liên hệ ngay với InkCorner.</p>
            <h3>⚠️ Lưu ý</h3>
            <p>Cung cấp thông tin nhận hàng chính xác. InkCorner không chịu trách nhiệm nếu giao hàng thất bại do thông tin sai.</p>
        '
    ],
    'dieu-khoan' => [
        'title' => '📜 Điều Khoản Sử Dụng',
        'content' => '
            <p>Khi truy cập và sử dụng website InkCorner, bạn đồng ý tuân thủ các điều khoản dưới đây.</p>
            <h3>📝 Đặt hàng</h3>
            <p>Khách hàng có trách nhiệm cung cấp thông tin chính xác. InkCorner không chịu trách nhiệm với sự cố do thông tin sai lệch từ phía khách hàng.</p>
            <h3>✅ Xác nhận đơn hàng</h3>
            <p>Đơn hàng chỉ được xác nhận khi InkCorner hoàn tất kiểm tra. Chúng tôi có quyền từ chối đơn trong trường hợp hết hàng, sai giá hoặc lỗi hệ thống.</p>
            <h3>©️ Sở hữu trí tuệ</h3>
            <p>Toàn bộ nội dung website thuộc quyền sở hữu của InkCorner. Không được sao chép, phân phối lại khi chưa có sự đồng ý bằng văn bản.</p>
            <h3>🚫 Hành vi bị cấm</h3>
            <ul>
                <li>Can thiệp vào hệ thống website</li>
                <li>Phát tán mã độc</li>
                <li>Sử dụng website vào mục đích bất hợp pháp</li>
            </ul>
            <h3>🔄 Cập nhật điều khoản</h3>
            <p>InkCorner có thể cập nhật điều khoản theo thời gian. Thay đổi sẽ được công bố trên website và có hiệu lực kể từ ngày đăng tải.</p>
        '
    ],
    'huong-dan' => [
        'title' => '📖 Hướng Dẫn Mua Hàng',
        'content' => '
            <h3>Bước 1: Chọn sản phẩm</h3>
            <p>Vào mục <strong>Sản Phẩm</strong>, lọc theo danh mục hoặc tìm kiếm theo tên. Bấm <strong>"Thêm vào giỏ"</strong> để chọn sản phẩm.</p>
            <h3>Bước 2: Kiểm tra giỏ hàng</h3>
            <p>Bấm vào biểu tượng giỏ hàng góc trên phải để xem và chỉnh sửa số lượng sản phẩm.</p>
            <h3>Bước 3: Thanh toán</h3>
            <p>Bấm <strong>"Tiến hành thanh toán"</strong>, điền thông tin giao hàng và chọn phương thức thanh toán phù hợp.</p>
            <h3>Bước 4: Xác nhận đơn hàng</h3>
            <p>Sau khi đặt hàng, bạn sẽ nhận được mã đơn hàng. InkCorner sẽ liên hệ xác nhận trong thời gian sớm nhất.</p>
            <h3>💳 Phương thức thanh toán</h3>
            <ul>
                <li>Thanh toán khi nhận hàng (COD)</li>
                <li>Chuyển khoản ngân hàng</li>
                <li>Ví điện tử (tùy thời điểm)</li>
            </ul>
            <h3>📞 Cần hỗ trợ?</h3>
            <p>Liên hệ qua email <strong>inkcorner.contact@gmail.com</strong> hoặc hotline <strong>0913.200.206</strong></p>
        '
    ],
];

$current = $pages[$page] ?? $pages['doi-tra'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= strip_tags($current['title']) ?> - InkCorner</title>
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <style>
        :root {
            --ink-purple: #5c3290;
            --ink-orange: #d8511c;
            --ink-dark: #1E0A2E;
            --ink-light: #FAF5FF;
            --ink-border: #E9D5FF;
            --ink-gray: #64748B;
            --ink-crimson: #BE123C;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Segoe UI, sans-serif; background:#f8f9fa; color:#333; }

        .page-wrapper {
            max-width: 1100px;
            margin: 100px auto 60px;
            padding: 0 20px;
            display: flex;
            gap: 30px;
        }

        /* Sidebar menu */
        .policy-sidebar {
            width: 260px;
            flex-shrink: 0;
        }
        .policy-sidebar h3 {
            font-size: 13px;
            font-weight: 700;
            color: var(--ink-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--ink-border);
        }
        .policy-nav a {
            display: block;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: var(--ink-gray);
            text-decoration: none;
            margin-bottom: 4px;
            transition: .2s;
        }
        .policy-nav a:hover { background: var(--ink-light); color: var(--ink-purple); }
        .policy-nav a.active { background: var(--ink-purple); color: #fff; }

        /* Nội dung */
        .policy-content {
            flex: 1;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            border: 1px solid var(--ink-border);
        }
        .policy-content h1 {
            font-size: 26px;
            color: var(--ink-purple);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--ink-border);
        }
        .policy-content h3 {
            font-size: 17px;
            color: var(--ink-dark);
            margin: 24px 0 10px;
        }
        .policy-content p {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 12px;
        }
        .policy-content ul {
            margin: 8px 0 16px 20px;
        }
        .policy-content ul li {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 6px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-wrapper { flex-direction: column; margin-top: 80px; }
            .policy-sidebar { width: 100%; }
        }
    </style>
</head>
<body>

<header>
<nav>
    <a href="Trang_chủ.php" class="nav-logo"><img src="logo.jpg" class="logo-img"></a>
    <div class="nav-links">
        <a href="Trang_chủ.php">Trang Chủ</a>
        <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản Phẩm</a>
        <a href="#">Giới Thiệu</a>
        <a href="#">Blog</a>
        <a href="Liên_hệ.html">Liên Hệ</a>
    </div>
    <div class="nav-right">
        <div class="search-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Tìm kiếm...">
        </div>
        <a href="User.php" class="btn-account">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Tài khoản
        </a>
        <button class="btn-cart">
            CART
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span class="cart-badge">0</span>
        </button>
    </div>
</nav>
</header>

<div class="page-wrapper">
    <!-- Sidebar -->
    <div class="policy-sidebar">
        <h3>Chính sách</h3>
        <nav class="policy-nav">
            <a href="chinh_sach.php?page=doi-tra"   class="<?= $page==='doi-tra'   ?'active':'' ?>">🔄 Chính sách đổi trả</a>
            <a href="chinh_sach.php?page=bao-mat"   class="<?= $page==='bao-mat'   ?'active':'' ?>">🔒 Chính sách bảo mật</a>
            <a href="chinh_sach.php?page=van-chuyen" class="<?= $page==='van-chuyen'?'active':'' ?>">🚚 Chính sách vận chuyển</a>
            <a href="chinh_sach.php?page=dieu-khoan" class="<?= $page==='dieu-khoan'?'active':'' ?>">📜 Điều khoản sử dụng</a>
            <a href="chinh_sach.php?page=huong-dan" class="<?= $page==='huong-dan' ?'active':'' ?>">📖 Hướng dẫn mua hàng</a>
        </nav>
    </div>

    <!-- Nội dung -->
    <div class="policy-content">
        <h1><?= $current['title'] ?></h1>
        <?= $current['content'] ?>
    </div>
</div>

<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <img src="logo.jpg" style="width:40px;margin-bottom:12px">
            <p>Chuyên cung cấp văn phòng phẩm chất lượng cao cho cá nhân và doanh nghiệp.</p>
        </div>
        <div class="footer-col">
            <h4>Cửa Hàng</h4>
            <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản phẩm</a>
            <a href="#">Giới thiệu</a>
            <a href="#">Blog</a>
            <a href="Liên_hệ.html">Liên hệ</a>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ</h4>
            <a href="chinh_sach.php?page=doi-tra">Chính sách đổi trả</a>
            <a href="chinh_sach.php?page=huong-dan">Hướng dẫn mua hàng</a>
            <a href="chinh_sach.php?page=van-chuyen">Chính sách vận chuyển</a>
            <a href="chinh_sach.php?page=bao-mat">Chính sách bảo mật</a>
            <a href="chinh_sach.php?page=dieu-khoan">Điều khoản sử dụng</a>
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

</body>
</html>
