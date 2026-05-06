<?php
session_start();
require_once 'config.php';

// Xử lý ĐĂNG NHẬP
$login_error = '';
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    if ($found && password_verify($password, $found['password_hash'])) {
        $_SESSION['user_id']   = $found['id'];
        $_SESSION['user_name'] = $found['fullname'];
        header("Location: User.php");
        exit;
    } else {
        $login_error = 'Email hoặc mật khẩu không đúng!';
    }
}

// Xử lý ĐĂNG KÝ
$register_error = '';
$register_ok    = false;
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $fullname = trim($_POST['reg_fullname']);
    $email    = trim($_POST['reg_email']);
    $phone    = trim($_POST['reg_phone']);
    $password = $_POST['reg_password'];
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $register_error = 'Email này đã được sử dụng!';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $ins  = $conn->prepare("INSERT INTO users (fullname, email, phone, password_hash) VALUES (?, ?, ?, ?)");
        $ins->bind_param("ssss", $fullname, $email, $phone, $hash);
        if ($ins->execute()) { $register_ok = true; }
        else { $register_error = 'Đăng ký thất bại, thử lại!'; }
    }
}

// Xử lý ĐĂNG XUẤT
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: User.php");
    exit;
}

// Xử lý LƯU THÔNG TIN
if (isset($_POST['action']) && $_POST['action'] === 'save' && isset($_SESSION['user_id'])) {
    $id       = $_SESSION['user_id'];
    $fullname = trim($_POST['fullname']);
    $phone    = trim($_POST['phone']);
    $gender   = $_POST['gender'];
    $dob      = $_POST['dob'];
    $address  = trim($_POST['address']);
    $stmt = $conn->prepare("UPDATE users SET fullname=?, phone=?, gender=?, dob=?, address=? WHERE id=?");
    $stmt->bind_param("sssssi", $fullname, $phone, $gender, $dob, $address, $id);
    $stmt->execute();
    $_SESSION['user_name'] = $fullname;
    header("Location: User.php?saved=1");
    exit;
}

// Lấy thông tin user
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Lấy lịch sử đơn hàng
$orders = [];
if ($user) {
    $stmt = $conn->prepare(
        "SELECT o.*, COUNT(oi.id) AS total_items 
         FROM orders o 
         LEFT JOIN order_items oi ON o.id = oi.order_id 
         WHERE o.user_id = ? 
         GROUP BY o.id 
         ORDER BY o.created_at DESC"
    );
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$show_popup  = !$user || $login_error || $register_error || $register_ok;
$active_tab  = ($register_error || $register_ok) ? 'register' : 'login';
$active_page = isset($_GET['page']) ? $_GET['page'] : 'profile';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản - InkCorner</title>
    <link rel="stylesheet" href="User.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <style>
        /* ── POPUP ── */
        .auth-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; align-items:center; justify-content:center; }
        .auth-overlay.show { display:flex; }
        .auth-box { background:#fff; border-radius:16px; padding:36px 32px; width:420px; max-width:95vw; box-shadow:0 20px 60px rgba(0,0,0,.2); position:relative; }
        .auth-close { position:absolute; top:14px; right:18px; background:none; border:none; font-size:22px; cursor:pointer; color:#999; }
        .auth-tabs { display:flex; margin-bottom:24px; border-bottom:2px solid #eee; }
        .auth-tab { flex:1; padding:10px; text-align:center; font-weight:bold; font-size:15px; cursor:pointer; color:#999; border-bottom:3px solid transparent; margin-bottom:-2px; transition:.2s; }
        .auth-tab.active { color:#5c3290; border-bottom-color:#5c3290; }
        .auth-form { display:none; }
        .auth-form.active { display:block; }
        .auth-form h2 { font-size:22px; color:#5c3290; margin-bottom:6px; }
        .auth-form p  { font-size:13px; color:#888; margin-bottom:20px; }
        .auth-field { margin-bottom:14px; }
        .auth-field label { display:block; font-size:13px; font-weight:bold; color:#444; margin-bottom:6px; }
        .auth-field input { width:100%; padding:11px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-size:14px; outline:none; transition:.2s; }
        .auth-field input:focus { border-color:#5c3290; }
        .btn-auth { width:100%; padding:13px; background:#5c3290; color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:bold; cursor:pointer; margin-top:6px; transition:.2s; }
        .btn-auth:hover { background:#4a2873; }
        .auth-error   { background:#fff0f0; color:#c0392b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:14px; border:1px solid #f5c6cb; }
        .auth-success { background:#f0fff4; color:#1a7a3c; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:14px; border:1px solid #b2dfdb; }
        .auth-switch { text-align:center; font-size:13px; color:#888; margin-top:16px; }
        .auth-switch a { color:#5c3290; cursor:pointer; font-weight:bold; }

        /* ── DROPDOWN TÀI KHOẢN ── */
        .account-wrapper { position:relative; }
        .dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            border: 1px solid #e9d5ff;
            min-width: 220px;
            z-index: 5000;
            overflow: hidden;
        }
        .dropdown-menu.open { display: block; }

        .dropdown-header {
            padding: 16px 18px 12px;
            background: linear-gradient(135deg, #5c3290, #7c3aed);
            color: #fff;
        }
        .dropdown-header .d-name { font-weight: 700; font-size: 15px; }
        .dropdown-header .d-email { font-size: 12px; opacity: .8; margin-top: 2px; }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            font-size: 14px;
            color: #333;
            text-decoration: none;
            transition: background .15s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        .dropdown-item:hover { background: #f3e8ff; color: #5c3290; }
        .dropdown-item svg { flex-shrink: 0; }
        .dropdown-divider { height: 1px; background: #f0e8ff; margin: 4px 0; }
        .dropdown-item.logout { color: #d8511c; }
        .dropdown-item.logout:hover { background: #fff5f0; }

        /* ── TABS TRANG ── */
        .page-tabs {
            display: flex;
            gap: 8px;
            max-width: 800px;
            margin: 0 auto 24px;
            padding: 0 16px;
        }
        .page-tab {
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid #e9d5ff;
            background: #fff;
            color: #5c3290;
            text-decoration: none;
            transition: .2s;
        }
        .page-tab.active, .page-tab:hover { background: #5c3290; color: #fff; border-color: #5c3290; }

        /* ── LỊCH SỬ MUA HÀNG ── */
        .order-list { max-width: 800px; margin: 0 auto; padding: 0 16px; }
        .order-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e9d5ff;
            padding: 20px;
            margin-bottom: 16px;
        }
        .order-card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .order-id   { font-weight:700; color:#5c3290; font-size:15px; }
        .order-date { font-size:12px; color:#999; }
        .order-status {
            padding: 4px 12px; border-radius: 999px;
            font-size: 12px; font-weight: 600;
        }
        .status-pending   { background:#fff3cd; color:#856404; }
        .status-confirmed { background:#d1ecf1; color:#0c5460; }
        .status-shipping  { background:#d4edda; color:#155724; }
        .status-delivered { background:#d4edda; color:#155724; }
        .status-cancelled { background:#f8d7da; color:#721c24; }
        .order-total { font-size:16px; font-weight:700; color:#d8511c; }
        .order-empty { text-align:center; padding:60px; color:#999; font-size:15px; }
    </style>
</head>
<body>

<!-- ===== POPUP ===== -->
<div class="auth-overlay <?= $show_popup ? 'show' : '' ?>" id="authOverlay">
    <div class="auth-box">
        <?php if (!$user): ?>
        <button class="auth-close" onclick="document.getElementById('authOverlay').classList.remove('show')">×</button>
        <?php endif; ?>
        <div class="auth-tabs">
            <div class="auth-tab <?= $active_tab==='login' ? 'active':'' ?>" onclick="switchTab('login', this)">Đăng nhập</div>
            <div class="auth-tab <?= $active_tab==='register' ? 'active':'' ?>" onclick="switchTab('register', this)">Đăng ký</div>
        </div>

        <!-- ĐĂNG NHẬP -->
        <div class="auth-form <?= $active_tab==='login' ? 'active':'' ?>" id="tab-login">
            <h2>Chào mừng trở lại!</h2>
            <p>Đăng nhập để xem thông tin tài khoản</p>
            <?php if ($login_error): ?><div class="auth-error">⚠️ <?= $login_error ?></div><?php endif; ?>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <div class="auth-field"><label>Email</label><input type="email" name="email" placeholder="Nhập email..." required></div>
                <div class="auth-field"><label>Mật khẩu</label><input type="password" name="password" placeholder="Nhập mật khẩu..." required></div>
                <button type="submit" class="btn-auth">Đăng nhập</button>
            </form>
            <div class="auth-switch">Chưa có tài khoản? <a onclick="switchTab('register', null)">Đăng ký ngay</a></div>
        </div>

        <!-- ĐĂNG KÝ -->
        <div class="auth-form <?= $active_tab==='register' ? 'active':'' ?>" id="tab-register">
            <h2>Tạo tài khoản</h2>
            <p>Đăng ký để mua hàng dễ dàng hơn</p>
            <?php if ($register_error): ?><div class="auth-error">⚠️ <?= $register_error ?></div><?php endif; ?>
            <?php if ($register_ok): ?><div class="auth-success">✅ Đăng ký thành công! Hãy đăng nhập.</div><?php endif; ?>
            <form method="POST">
                <input type="hidden" name="action" value="register">
                <div class="auth-field"><label>Họ và tên</label><input type="text" name="reg_fullname" placeholder="Nhập họ tên..." required></div>
                <div class="auth-field"><label>Email</label><input type="email" name="reg_email" placeholder="Nhập email..." required></div>
                <div class="auth-field"><label>Số điện thoại</label><input type="tel" name="reg_phone" placeholder="Nhập SĐT..."></div>
                <div class="auth-field"><label>Mật khẩu</label><input type="password" name="reg_password" placeholder="Tạo mật khẩu..." required></div>
                <button type="submit" class="btn-auth" style="background:#d8511c;">Đăng ký</button>
            </form>
            <div class="auth-switch">Đã có tài khoản? <a onclick="switchTab('login', null)">Đăng nhập</a></div>
        </div>
    </div>
</div>

<!-- ===== HEADER ===== -->
<header>
<nav>
    <a href="Trang_chủ.php" class="nav-logo"><img src="logo.jpg" class="logo-img"></a>
    <div class="nav-links">
      <a href="Trang_chủ.php">Trang Chủ</a><a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản Phẩm</a>
      <a href="Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới Thiệu</a><a href="Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a><a href="Liên_hệ.html">Liên Hệ</a>
    </div>
    <div class="nav-right">
      <div class="search-box">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Tìm kiếm..." id="searchInput" onkeydown="if(event.key==='Enter'){const q=this.value.trim();if(q)window.location.href='Trang_sản_phẩm/Tất_cả_sản_phẩm.php?q='+encodeURIComponent(q)}">
      </div>

      <!-- NÚT TÀI KHOẢN + DROPDOWN -->
      <div class="account-wrapper">
        <?php if ($user): ?>
          <button class="btn-account" onclick="toggleDropdown()" id="accountBtn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <?= htmlspecialchars(explode(' ', $user['fullname'])[0]) ?> ▾
          </button>
          <!-- DROPDOWN MENU -->
          <div class="dropdown-menu" id="dropdownMenu">
            <div class="dropdown-header">
              <div class="d-name"><?= htmlspecialchars($user['fullname']) ?></div>
              <div class="d-email"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <a href="User.php?page=profile" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              Hồ sơ của tôi
            </a>
            <a href="User.php?page=orders" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
              Lịch sử mua hàng
            </a>
            <div class="dropdown-divider"></div>
            <a href="?logout=1" class="dropdown-item logout">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Đăng xuất
            </a>
          </div>
        <?php else: ?>
          <button class="btn-account" onclick="document.getElementById('authOverlay').classList.add('show')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Tài khoản
          </button>
        <?php endif; ?>
      </div>

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
<br><br><br>

<!-- ===== NỘI DUNG ===== -->
<?php if ($user): ?>

<?php if (isset($_GET['saved'])): ?>
<div style="background:#d4edda;color:#155724;padding:12px;text-align:center;max-width:800px;margin:0 auto 16px;border-radius:8px;">✅ Lưu thành công!</div>
<?php endif; ?>

<!-- TABS CHUYỂN TRANG -->
<div class="page-tabs">
    <a href="User.php?page=profile" class="page-tab <?= $active_page==='profile' ? 'active':'' ?>">👤 Hồ sơ của tôi</a>
    <a href="User.php?page=orders"  class="page-tab <?= $active_page==='orders'  ? 'active':'' ?>">📦 Lịch sử mua hàng</a>
</div>

<?php if ($active_page === 'profile'): ?>
<!-- HỒ SƠ -->
<div class="profile-card">
    <div class="profile-sidebar">
        <div class="avatar-preview">
            <?php if ($user['avatar']): ?>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar">
            <?php else: ?>
                <?= strtoupper(mb_substr($user['fullname'], 0, 1, 'UTF-8')) ?>
            <?php endif; ?>
        </div>
        <button class="btn-upload">Chọn Ảnh</button>
        <div class="avatar-note">Tối đa 1MB<br>.JPEG, .PNG</div>
    </div>
    <div class="profile-content">
        <h2>Hồ Sơ Của Tôi</h2>
        <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
        <form method="POST">
            <input type="hidden" name="action" value="save">
            <div class="form-row">
                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>">
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group full-width">
                <label>Email đăng nhập</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Giới tính</label>
                    <select name="gender">
                        <option value="male"   <?= $user['gender']=='male'  ?'selected':'' ?>>Nam</option>
                        <option value="female" <?= $user['gender']=='female'?'selected':'' ?>>Nữ</option>
                        <option value="other"  <?= $user['gender']=='other' ?'selected':'' ?>>Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group full-width">
                <label>Địa chỉ giao hàng mặc định</label>
                <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn-save">LƯU THAY ĐỔI</button>
                <button type="button" class="btn-cancel" onclick="window.location.reload()">Hủy</button>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- LỊCH SỬ MUA HÀNG -->
<div class="order-list">
    <?php if (empty($orders)): ?>
        <div class="order-empty">
            📦 Bạn chưa có đơn hàng nào.<br>
            <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php" style="color:#5c3290;font-weight:600;">Mua sắm ngay →</a>
        </div>
    <?php else: ?>
        <?php
        $status_labels = [
            'pending'   => ['label'=>'Chờ xác nhận', 'class'=>'status-pending'],
            'confirmed' => ['label'=>'Đã xác nhận',  'class'=>'status-confirmed'],
            'shipping'  => ['label'=>'Đang giao',     'class'=>'status-shipping'],
            'delivered' => ['label'=>'Đã giao',       'class'=>'status-delivered'],
            'cancelled' => ['label'=>'Đã hủy',        'class'=>'status-cancelled'],
        ];
        foreach ($orders as $order):
            $s = $status_labels[$order['status']] ?? ['label'=>$order['status'],'class'=>''];
        ?>
        <div class="order-card">
            <div class="order-card-header">
                <div>
                    <div class="order-id">#INK<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></div>
                    <div class="order-date"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                </div>
                <span class="order-status <?= $s['class'] ?>"><?= $s['label'] ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;color:#666;">
                <span><?= $order['total_items'] ?? 0 ?> sản phẩm · <?= $order['receiver_name'] ?></span>
                <span class="order-total"><?= number_format($order['total_amount'],0,',','.') ?>đ</span>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php else: ?>
<div style="text-align:center;padding:80px 20px;color:#888;">
    <p style="font-size:18px;">Vui lòng đăng nhập để xem thông tin tài khoản.</p>
    <button onclick="document.getElementById('authOverlay').classList.add('show')"
        style="margin-top:16px;padding:12px 32px;background:#5c3290;color:#fff;border:none;border-radius:8px;font-size:15px;cursor:pointer;">
        Đăng nhập ngay
    </button>
</div>
<?php endif; ?>

<footer>
    <div class="footer-grid">
      <div class="footer-brand"><img src="logo.jpg" style="width:40px;margin-bottom:12px"><p>Chuyên cung cấp văn phòng phẩm chất lượng cao.</p></div>
      <div class="footer-col"><h4>Cửa Hàng</h4><a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản Phẩm</a><a href="Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới Thiệu</a><a href="Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a><a href="Liên_hệ.html">Liên Hệ</a></div>
      <div class="footer-col"><h4>Hỗ trợ</h4><a href="chinh_sach.php?page=doi-tra">Chính sách Đổi Trả</a><a href="chinh_sach.php?page=huong-dan">Hướng Dẫn Mua Hàng</a><a href="chinh_sach.php?page=bao-mat">Chính Sách Bảo Mật</a></div>
      <div class="footer-col"><h4>Liên Hệ</h4><a href="#">0913200206</a><a href="#">inkcorner.contact@gmail.com</a><a href="#">79, Hồ Tùng Mậu, Hà Nội</a></div>
    </div>
    <div class="footer-bottom"><span>© 2026 iNK Store. Tất cả các quyền được bảo lưu.</span><span>Thiết kế bởi INK Team</span></div>
</footer>

<script>
// Chuyển tab đăng nhập / đăng ký
function switchTab(tab, el) {
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    if (el) el.classList.add('active');
    else document.querySelectorAll('.auth-tab')[tab==='login'?0:1].classList.add('active');
}

// Toggle dropdown tài khoản
function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('open');
}

// Đóng dropdown khi click ra ngoài
document.addEventListener('click', function(e) {
    const wrapper = document.querySelector('.account-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        const menu = document.getElementById('dropdownMenu');
        if (menu) menu.classList.remove('open');
    }
});
</script>
</body>
</html>
