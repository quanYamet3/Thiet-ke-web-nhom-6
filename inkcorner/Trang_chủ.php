<?php
session_start();
require_once 'config.php';

// Lấy thông tin user nếu đã đăng nhập
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id, fullname, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Xử lý đăng nhập từ trang chủ
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
        header("Location: Trang_chủ.php");
        exit;
    } else {
        $login_error = 'Email hoặc mật khẩu không đúng!';
    }
}

// Xử lý đăng ký từ trang chủ
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
        if ($ins->execute()) $register_ok = true;
        else $register_error = 'Đăng ký thất bại!';
    }
}

// Đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: Trang_chủ.php");
    exit;
}

$cart_count = array_sum($_SESSION['cart'] ?? []);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>iNK - Văn phòng phẩm</title>
  <link rel="stylesheet" href="CSS_trang_chủ.css">
  <link rel="stylesheet" href="CSS_header.css">
  <link rel="stylesheet" href="CSS_footer.css">
  <style>
    /* DROPDOWN TÀI KHOẢN */
    .account-wrapper { position: relative; }
    .dropdown-menu {
      display: none; position: absolute;
      top: calc(100% + 10px); right: 0;
      background: #fff; border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
      border: 1px solid #e9d5ff; min-width: 220px;
      z-index: 5000; overflow: hidden;
    }
    .dropdown-menu.open { display: block; }
    .dropdown-header {
      padding: 16px 18px 12px;
      background: linear-gradient(135deg,#5c3290,#7c3aed); color:#fff;
    }
    .dropdown-header .d-name  { font-weight:700; font-size:15px; }
    .dropdown-header .d-email { font-size:12px; opacity:.8; margin-top:2px; }
    .dropdown-item {
      display:flex; align-items:center; gap:10px;
      padding:12px 18px; font-size:14px; color:#333;
      text-decoration:none; transition:background .15s;
      border:none; background:none; width:100%; text-align:left; cursor:pointer;
    }
    .dropdown-item:hover { background:#f3e8ff; color:#5c3290; }
    .dropdown-divider { height:1px; background:#f0e8ff; margin:4px 0; }
    .dropdown-item.logout { color:#d8511c; }
    .dropdown-item.logout:hover { background:#fff5f0; }

    /* SEARCH LIVE */
    .search-box { position: relative; }
    .search-results {
      position:absolute; top:100%; left:0; right:0;
      background:#fff; border-radius:0 0 12px 12px;
      box-shadow:0 8px 24px rgba(0,0,0,.12);
      border:1px solid #e9d5ff; border-top:none;
      max-height:300px; overflow-y:auto;
      z-index:4000; display:none;
    }
    .search-results.show { display:block; }
    .search-result-item {
      display:flex; align-items:center; gap:12px;
      padding:10px 16px; cursor:pointer;
      transition:background .15s; text-decoration:none; color:#333;
    }
    .search-result-item:hover { background:#f3e8ff; }
    .search-result-name  { font-size:13px; font-weight:500; flex:1; }
    .search-result-price { font-size:13px; color:#d8511c; font-weight:600; }
    .search-result-empty { padding:16px; text-align:center; color:#999; font-size:13px; }

    /* POPUP AUTH */
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
    .btn-auth { width:100%; padding:13px; background:#5c3290; color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:bold; cursor:pointer; margin-top:6px; }
    .btn-auth:hover { background:#4a2873; }
    .auth-error   { background:#fff0f0; color:#c0392b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:14px; border:1px solid #f5c6cb; }
    .auth-success { background:#f0fff4; color:#1a7a3c; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:14px; border:1px solid #b2dfdb; }
    .auth-switch { text-align:center; font-size:13px; color:#888; margin-top:16px; }
    .auth-switch a { color:#5c3290; cursor:pointer; font-weight:bold; }
  </style>
</head>
<body>

<?php if ($login_error || $register_error || $register_ok): ?>
<!-- Hiện popup nếu có lỗi -->
<script>window.onload = function(){ document.getElementById('authOverlay').classList.add('show'); }</script>
<?php endif; ?>

<!-- POPUP ĐĂNG NHẬP / ĐĂNG KÝ -->
<div class="auth-overlay" id="authOverlay">
  <div class="auth-box">
    <button class="auth-close" onclick="document.getElementById('authOverlay').classList.remove('show')">×</button>
    <div class="auth-tabs">
      <div class="auth-tab <?= $register_error||$register_ok ? '':'active' ?>" onclick="switchAuthTab('login',this)">Đăng nhập</div>
      <div class="auth-tab <?= $register_error||$register_ok ? 'active':'' ?>" onclick="switchAuthTab('register',this)">Đăng ký</div>
    </div>
    <!-- ĐĂNG NHẬP -->
    <div class="auth-form <?= $register_error||$register_ok ? '':'active' ?>" id="auth-login">
      <h2>Chào mừng trở lại!</h2>
      <p>Đăng nhập để mua sắm dễ dàng hơn</p>
      <?php if ($login_error): ?><div class="auth-error">⚠️ <?= $login_error ?></div><?php endif; ?>
      <form method="POST">
        <input type="hidden" name="action" value="login">
        <div class="auth-field"><label>Email</label><input type="email" name="email" placeholder="Nhập email..." required></div>
        <div class="auth-field"><label>Mật khẩu</label><input type="password" name="password" placeholder="Nhập mật khẩu..." required></div>
        <button type="submit" class="btn-auth">Đăng nhập</button>
      </form>
      <div class="auth-switch">Chưa có tài khoản? <a onclick="switchAuthTab('register',null)">Đăng ký ngay</a></div>
    </div>
    <!-- ĐĂNG KÝ -->
    <div class="auth-form <?= $register_error||$register_ok ? 'active':'' ?>" id="auth-register">
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
      <div class="auth-switch">Đã có tài khoản? <a onclick="switchAuthTab('login',null)">Đăng nhập</a></div>
    </div>
  </div>
</div>

<header>
  <nav>
    <a href="Trang_chủ.php" class="nav-logo">
      <img src="logo.jpg" class="logo-img">
    </a>
    <div class="nav-links">
      <a href="Trang_chủ.php" class="active">Trang Chủ</a>
      <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản Phẩm</a>
      <a href="Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới Thiệu</a>
      <a href="Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
      <a href="Liên_hệ.html">Liên Hệ</a>
    </div>
    <div class="nav-right">
      <!-- TÌM KIẾM LIVE -->
      <div class="search-box" id="searchWrapper">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Tìm kiếm..." id="searchInput"
               autocomplete="off"
               oninput="liveSearch(this.value)"
               onkeydown="if(event.key==='Enter') goSearch()">
        <div class="search-results" id="searchResults"></div>
      </div>

      <!-- NÚT TÀI KHOẢN -->
      <div class="account-wrapper">
        <?php if ($user): ?>
        <button class="btn-account" onclick="toggleDropdown()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <?= htmlspecialchars(explode(' ', $user['fullname'])[0]) ?> ▾
        </button>
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

      <!-- GIỎ HÀNG -->
      <button class="btn-cart" onclick="document.location='Giỏ_hàng.php'">
        CART
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <span class="cart-badge" id="cartBadge"><?= $cart_count ?></span>
      </button>
    </div>
  </nav>
</header>
<!-- HOME PAGE -->
<div id="home" class="page active">
  <section class="hero">
    <div class="hero-bg-circle c1"></div>
    <div class="hero-bg-circle c2"></div>
      <div class="hero-content">
        <div class="hero-tag">
          <span class="hero-tag-dot"></span>
          Hàng mới về - Bút bi cao cấp 2026
        </div>
        <h1>Văn phòng phẩm <em>Đẳng cấp</em> cho mọi nhu cầu</h1>
        <p>iNK cung cấp đầy đủ sản phẩm văn phòng phẩm chất lượng cao, từ bút, sổ tay đến thiết bị văn phòng hiện đại.</p>
        <div class="hero-btns">
          <button class="btn-primary" onclick="document.location='Trang_sản_phẩm/Tất_cả_sản_phẩm.html'">Mua Ngay</button>
          <button class="btn-secondary" onclick="document.location='Giới_thiệu_Khuyến_mãi/gioithieu.html'">Khám Phá INK</button>
        </div>
      </div>
      <!-- Phần này của back end, k                                                                                                                                                                                                      hông cần chỉnh sửa --> 
      <div class="hero-visual">
        <div class="hero-card">
          <div class="hero-card-grid">
            <div class="hero-prod" onclick="addToCartById(0)">
              <div class="hero-prod-icon">&#128393;</div>
              <div class="hero-prod-name">Bút bi cao Cấp</div>
              <div class="hero-prod-price">25.000d</div>
            </div>
            <div class="hero-prod" onclick="addToCartById(1)">
              <div class="hero-prod-icon">&#128214;</div>
              <div class="hero-prod-name">Sổ tay A5</div>
              <div class="hero-prod-price">55.000d</div>
            </div>
            <div class="hero-prod" onclick="addToCartById(4)">
              <div class="hero-prod-icon">&#9986;</div>
              <div class="hero-prod-name">Kéo cắt</div>
              <div class="hero-prod-price">18.000d</div>
            </div>
            <div class="hero-prod" onclick="addToCartById(5)">
              <div class="hero-prod-icon">&#128204;</div>
              <div class="hero-prod-name">File hồ sơ</div>
              <div class="hero-prod-price">12.000d</div>
            </div>
          </div>
          <div class="hero-stats">
            <div class="hero-stat"><strong>500+</strong><span>Sản phẩm</span></div>
            <div class="hero-stat"><strong>10K+</strong><span>Khách hàng</span></div>
            <div class="hero-stat"><strong>4.9</strong><span>Đánh giá</span></div>
          </div>
        </div>
      </div>
    </section>

    <!-- CATEGORIES -->
    <section>
      <div class="section-label">Danh Mục</div>
      <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:32px">
        <div>
          <div class="section-title">Khám phá danh mục</div>
          <p style="color:var(--ink-gray);font-size:15px">Tất cả những gì bạn cần cho văn phòng</p>
        </div>
      </div>
      <div class="cat-grid">
        <div class="cat-card" onclick="document.location='Trang_sản_phẩm/Bút_viết.html';setActiveByIndex(1)">
          <div class="cat-icon">&#128393;</div>
          <div class="cat-name">Bút Viết</div>
          <div class="cat-count">9 sản phẩm</div>
        </div>
        <div class="cat-card" onclick="document.location='Trang_sản_phẩm/Tất_cả_sản_phẩm.html';setActiveByIndex(1)">
          <div class="cat-icon">&#128214;</div>
          <div class="cat-name">Sổ Tay</div>
          <div class="cat-count">3 sản phẩm</div>
        </div>
        <div class="cat-card" onclick="document.location='Trang_sản_phẩm/Hộp_đựng.html';setActiveByIndex(1)">
          <div class="cat-icon">&#9986;</div>
          <div class="cat-name">Văn Phòng</div>
          <div class="cat-count">24 sản phẩm</div>
        </div>
        <div class="cat-card" onclick="document.location='Trang_sản_phẩm/Tất_cả_sản_phẩm.html';setActiveByIndex(1)">
          <div class="cat-icon">&#128203;</div>
          <div class="cat-name">Giấy In</div>
          <div class="cat-count">2 sản phẩm</div>
        </div>
      </div>
    </section>

    <!-- BLOG PREVIEW -->
    <section style="padding-top:0">
      <div class="section-label">Blog</div>
      <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:32px;flex-wrap:wrap;gap:12px">
        <div class="section-title">Tin tức mới nhất</div>
        <button class="filter-tab" onclick="document.location='Giới_thiệu_Khuyến_mãi/tintuc.html'">Xem tất cả</button>
      </div>
      <div class="blog-grid">
        <div class="blog-card" onclick="document.location='Giới_thiệu_Khuyến_mãi/baiviet1.html'";>
          <div class="blog-img">
            <img
            src="https://i.ibb.co/d4kkZD4Z/huong-dan-luu-tru-chung-tu-hop-dong-cho-ke-toan-nhan-su-3.jpg"
              alt="News"
              style="height:180px;width:100%; object-fit:cover; border-radius:4px"
            />
            <div class="blog-tag" style="background:var(--ink-purple)">Mẹo hay</div>
          </div>
          <div class="blog-info">
            <div class="blog-meta"><span>20 Th 4 2026</span><span>3 phút đọc</span></div>
            <div class="blog-title">Vì sao văn phòng phẩm là vật dụng không thể thiếu?</div>
            <div class="blog-excerpt">Văn phòng phẩm luôn xuất hiện trong mọi môi trường làm việc và học tập. Mặc dù ...</div>
          </div>
        </div>
        <div class="blog-card" onclick="document.location='Giới_thiệu_Khuyến_mãi/baiviet2.html'";>
          <div class="blog-img">
            <img src="https://huyphu.com/cdn/720/Product/Kn-748a48/1581065193995.jpg" alt="News" style="height:180px;width:100%; object-fit:cover; border-radius:4px"/>
            <div class="blog-tag" style="background:var(--ink-orange)">Hướng dẫn</div>
          </div>
          <div class="blog-info">
            <div class="blog-meta"><span>23 Th 4 2026</span><span>5 phút đọc</span></div>
            <div class="blog-title">5 Cách Sử Dụng Sổ Tay</div>
            <div class="blog-excerpt">Sổ tay là vật dụng quen thuộc trong cuộc sống hàng ngày. Dù ở trường...</div>
          </div>
        </div>
        <div class="blog-card" onclick="document.location='Giới_thiệu_Khuyến_mãi/baiviet3.html'";>
          <div class="blog-img">
            <img src="https://i.ibb.co/Nn15V3QZ/cach-bao-quan-but-tap-so-tay-giay-in-de-dung-lau-va-giu-chat-luong-04-1.jpg" alt="News" style="height:180px;width:100%; object-fit:cover; border-radius:4px"/>
            <div class="blog-tag" style="background:var(--ink-crimson)">Review</div>
          </div>
          <div class="blog-info">
            <div class="blog-meta"><span>25 Th 4 2026</span><span>4 phút đọc</span></div>
            <div class="blog-title">Nguồn gốc và ý nghĩa của sổ tay văn phòng</div>
            <div class="blog-excerpt">Sổ tay văn phòng là vật dụng quen thuộc trong môi trường công sở...</div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- FOOTER -->
  <footer>
    <div class="footer-grid">
      <div class="footer-brand">
        <img src="logo.jpg" style="width:40px;margin-bottom:12px">
        <p>Chuyên cung cấp văn phòng phẩm chất lượng cao cho cá nhân và doanh nghiệp. Chất lượng là cam kết của chúng tôi.</p>
      </div>
      <div class="footer-col">
        <h4>Cửa Hàng</h4>
        <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.html">Sản phẩm</a>
        <a href="Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới thiệu</a>
        <a href="Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
        <a href="Liên_hệ.html">Liên hệ</a>
      </div>
      <div class="footer-col">
        <h4>Hỗ trợ</h4>
        <a href="#">Chính sách đổi trả</a>
        <a href="#">Điều khoản sử dụng</a>
        <a href="#">Chính sách vận chuyển</a>
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

  <!-- AI CHAT -->
  <button class="ai-toggle" onclick="toggleAI()">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
  </button>
  <div class="ai-box" id="aiBox">
    <div class="ai-head">
      <div class="ai-avatar">AI</div>
      <div class="ai-head-info">
        <strong>INK Assistant</strong>
        <span>Dang hoat dong</span>
      </div>
    </div>
    <div class="ai-messages" id="aiMessages">
      <div class="ai-msg bot">Xin chao! Toi la AI ho tro cua INK. Toi co the giup gi cho ban hom nay?</div>
    </div>
    <div class="ai-quick" id="aiQuick">
      <div class="ai-chip" onclick="sendAI('San pham nao ban chay nhat?')">Ban chay nhat</div>
      <div class="ai-chip" onclick="sendAI('Chinh sach giao hang nhu the nao?')">Chinh sach giao hang</div>
      <div class="ai-chip" onclick="sendAI('Co ma giam gia khong?')">Ma giam gia</div>
    </div>
    <div class="ai-input-row">
      <input class="ai-input" id="aiInput" placeholder="Nhan tin..." onkeydown="if(event.key==='Enter')sendAI()">
      <button class="ai-send" onclick="sendAI()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </div>
  </div>

  <!-- LOGIN MODAL -->
  <div class="modal-overlay" id="loginModal" onclick="closeModal('loginModal',event)">
    <div class="modal">
      <h2>Dang Nhap</h2>
      <p>Chao mung ban tro lai voi INK Store</p>
      <div class="form-group"><label>Email</label><input type="email" placeholder="email@example.com"></div>
      <div class="form-group"><label>Mat Khau</label><input type="password" placeholder="••••••••"></div>
      <button class="btn-submit" onclick="showToast('Dang nhap thanh cong!');closeModal('loginModal')">Dang Nhap</button>
      <div class="modal-switch">Chua co tai khoan? <a onclick="closeModal('loginModal');openModal('registerModal')">Dang Ky Ngay</a></div>
    </div>
  </div>

  <!-- REGISTER MODAL -->
  <div class="modal-overlay" id="registerModal" onclick="closeModal('registerModal',event)">
    <div class="modal">
      <h2>Dang Ky</h2>
      <p>Tao tai khoan de trai nghiem tot hon</p>
      <div class="form-group"><label>Ho va Ten</label><input type="text" placeholder="Nguyen Van A"></div>
      <div class="form-group"><label>Email</label><input type="email" placeholder="email@example.com"></div>
      <div class="form-group"><label>Mat Khau</label><input type="password" placeholder="••••••••"></div>
      <button class="btn-submit" onclick="showToast('Dang ky thanh cong! Chao mung den INK!');closeModal('registerModal')">Dang Ky</button>
      <div class="modal-switch">Da co tai khoan? <a onclick="closeModal('registerModal');openModal('loginModal')">Dang Nhap</a></div>
    </div>
  </div>

  <!-- TOAST -->
  <div class="toast" id="toast"></div>

  <script>
  // ── DATA ──
  const products = [
    {id:0,name:'But Bi Xanh Cap',brand:'INK Premium',price:25000,oldPrice:35000,cat:'But',badge:'sale',bg:'linear-gradient(135deg,#EDE9FE,#C4B5FD)',icon:'&#128393;'},
    {id:1,name:'So Tay Dot A5',brand:'INK Notebook',price:55000,oldPrice:0,cat:'So',badge:'new',bg:'linear-gradient(135deg,#FEF3C7,#FDE68A)',icon:'&#128214;'},
    {id:2,name:'Bo But Mau Nhat 12 Mau',brand:'INK Color',price:85000,oldPrice:120000,cat:'Mau',badge:'sale',bg:'linear-gradient(135deg,#FCE7F3,#FBCFE8)',icon:'&#127912;'},
    {id:3,name:'Giay In A4 500 To',brand:'INK Paper',price:95000,oldPrice:0,cat:'Giay',badge:'',bg:'linear-gradient(135deg,#DBEAFE,#BFDBFE)',icon:'&#128203;'},
    {id:4,name:'Keo Cat Van Phong',brand:'INK Office',price:18000,oldPrice:0,cat:'Van phong',badge:'',bg:'linear-gradient(135deg,#D1FAE5,#A7F3D0)',icon:'&#9986;'},
    {id:5,name:'File Ho So Cung',brand:'INK Filing',price:12000,oldPrice:0,cat:'Van phong',badge:'hot',bg:'linear-gradient(135deg,#FFF7ED,#FED7AA)',icon:'&#128204;'},
    {id:6,name:'But Chì Kim 0.5mm',brand:'INK Draw',price:32000,oldPrice:45000,cat:'But',badge:'sale',bg:'linear-gradient(135deg,#F0FDF4,#BBF7D0)',icon:'&#9999;'},
    {id:7,name:'Bang Trang Bo Boc',brand:'INK Board',price:145000,oldPrice:0,cat:'Van phong',badge:'new',bg:'linear-gradient(135deg,#F5F3FF,#EDE9FE)',icon:'&#128221;'},
  ];

  let cart = [];
  let currentFilter = '';

  function renderProducts(arr, containerId) {
    const grid = document.getElementById(containerId);
    if(!grid) return;
    grid.innerHTML = arr.map(p => `
      <div class="prod-card">
        <div class="prod-img" style="background:${p.bg}">
          <span style="font-size:52px">${p.icon}</span>
          ${p.badge ? `<div class="prod-badge badge-${p.badge}">${p.badge.toUpperCase()}</div>` : ''}
        </div>
        <div class="prod-info">
          <div class="prod-brand">${p.brand}</div>
          <div class="prod-name">${p.name}</div>
          <div class="prod-footer">
            <div>
              <span class="prod-price">${p.price.toLocaleString('vi-VN')}d</span>
              ${p.oldPrice ? `<span class="prod-price-old">${p.oldPrice.toLocaleString('vi-VN')}d</span>` : ''}
            </div>
            <button class="btn-add" onclick="addToCartById(${p.id})">+</button>
          </div>
        </div>
      </div>
    `).join('');
  }

  function filterProducts() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const filtered = products.filter(p =>
      p.name.toLowerCase().includes(q) &&
      (currentFilter === '' || p.cat.includes(currentFilter))
    );
    renderProducts(filtered, 'productsGrid');
  }

  function filterByCategory(cat, btn) {
    currentFilter = cat;
    if(btn) {
      document.querySelectorAll('#filterTabs .filter-tab').forEach(t => t.classList.remove('active'));
      btn.classList.add('active');
    }
    filterProducts();
  }

  function addToCartById(id) {
    const p = products.find(x => x.id === id);
    if(!p) return;
    const existing = cart.find(x => x.id === id);
    if(existing) existing.qty++;
    else cart.push({...p, qty:1});
    updateCart();
    showToast(p.name + ' da them vao gio hang!');
  }

  function removeFromCart(id) {
    cart = cart.filter(x => x.id !== id);
    updateCart();
  }

  function changeQty(id, delta) {
    const item = cart.find(x => x.id === id);
    if(!item) return;
    item.qty += delta;
    if(item.qty <= 0) removeFromCart(id);
    else updateCart();
  }

  function updateCart() {
    const badge = document.getElementById('cartBadge');
    const total = cart.reduce((s,x) => s + x.qty, 0);
    badge.textContent = total;

    const body = document.getElementById('cartBody');
    const footer = document.getElementById('cartFooter');

    if(cart.length === 0) {
      body.innerHTML = `<div class="cart-empty">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin:0 auto 16px;display:block"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <p style="font-weight:600;margin-bottom:6px;">Gio hang trong</p>
        <p style="font-size:13px;">Hay them san pham vao gio hang</p>
      </div>`;
      footer.style.display = 'none';
    } else {
      body.innerHTML = cart.map(item => `
        <div class="cart-item">
          <div class="cart-item-img" style="background:${item.bg}">${item.icon}</div>
          <div class="cart-item-info">
            <div class="cart-item-name">${item.name}</div>
            <div class="cart-item-price">${item.price.toLocaleString('vi-VN')}d</div>
            <div class="cart-qty">
              <button class="qty-btn" onclick="changeQty(${item.id},-1)">-</button>
              <span style="font-weight:600;min-width:20px;text-align:center">${item.qty}</span>
              <button class="qty-btn" onclick="changeQty(${item.id},1)">+</button>
              <button class="cart-del" onclick="removeFromCart(${item.id})">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
              </button>
            </div>
          </div>
        </div>
      `).join('');
      const totalPrice = cart.reduce((s,x) => s + x.price * x.qty, 0);
      document.getElementById('cartTotal').textContent = totalPrice.toLocaleString('vi-VN') + 'd';
      footer.style.display = 'block';
    }
  }

  function toggleCart() {
    document.getElementById('cartDrawer').classList.toggle('open');
    document.getElementById('cartOverlay').classList.toggle('open');
  }

  // ── PAGES ──
  function showPage(id) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    window.scrollTo(0,0);
    if(id === 'products') renderProducts(products, 'productsGrid');
  }

  function setActive(el) {
    document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
    el.classList.add('active');
  }

  function setActiveByIndex(i) {
    const links = document.querySelectorAll('.nav-links a');
    links.forEach(a => a.classList.remove('active'));
    if(links[i]) links[i].classList.add('active');
  }

  // ── MODALS ──
  function openModal(id) { document.getElementById(id).classList.add('open'); }
  function closeModal(id, e) {
    if(!e || e.target === document.getElementById(id))
      document.getElementById(id).classList.remove('open');
  }

  // ── TOAST ──
  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
  }
</script>
  // ── AI CHAT ──
<button class="ai-toggle" onclick="toggleAI()">
  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
  </svg>
</button>

<div class="ai-box" id="aiBox">
  <div class="ai-head">
    <div class="ai-head-info">
      <strong class="ai-title">INK Assistant</strong>
      <span class="ai-status">Đang hoạt động</span>
    </div>
    <button class="ai-close" onclick="toggleAI()">×</button>
  </div>

  <div class="ai-messages" id="aiMessages">
    <div class="ai-msg bot">Xin chào! Tôi là AI hỗ trợ của INK. Tôi có thể giúp gì cho bạn hôm nay?</div>
  </div>

  <div class="ai-quick" id="aiQuick" style="display:flex; gap:8px; padding:10px 20px; overflow-x:auto; background:#fff; border-top:1px solid var(--ink-border);">
    <div class="ai-chip" style="padding:6px 12px; background:var(--ink-light); border-radius:14px; font-size:12px; cursor:pointer; white-space:nowrap;" onclick="sendAI('Sản phẩm nào bán chạy nhất?')">Bán chạy nhất</div>
    <div class="ai-chip" style="padding:6px 12px; background:var(--ink-light); border-radius:14px; font-size:12px; cursor:pointer; white-space:nowrap;" onclick="sendAI('Chính sách giao hàng như thế nào?')">Chính sách giao hàng</div>
    <div class="ai-chip" style="padding:6px 12px; background:var(--ink-light); border-radius:14px; font-size:12px; cursor:pointer; white-space:nowrap;" onclick="sendAI('Có mã giảm giá không?')">Mã giảm giá</div>
  </div>
</div>
<script>
  function toggleAI() {
    const aiBox = document.getElementById('aiBox');
    if (aiBox) aiBox.classList.toggle('open');
  }
  // Cấu trúc dữ liệu 8 nhóm và các câu hỏi cấp 2
  const chatMenu = {
    categories: [
      { id: 'san_pham', label: 'Sản phẩm' },
      { id: 'nhu_cau', label: 'Phù hợp nhu cầu' },
      { id: 'gia_ca', label: 'Giá cả & Khuyến mãi' },
      { id: 'dat_hang', label: 'Đặt hàng' },
      { id: 'giao_hang', label: 'Giao hàng' },
      { id: 'doi_tra', label: 'Đổi trả' },
      { id: 'ban_si', label: 'Bán sỉ' },
      { id: 'thanh_toan', label: 'Thanh toán' }
    ],
    questions: {
      san_pham: [
        'Có bán bút, vở, giấy in không?',
        'Có bán file hồ sơ, bìa còng, bìa kẹp không?',
        'Có bán đồ dùng học tập không?',
        'Có bán đồ dùng cho văn phòng công ty không?'
      ],
      nhu_cau: [
        'Tôi cần mua đồ cho học sinh lớp 1–12, shop có gì?',
        'Tôi cần mua đồ cho văn phòng, shop có gợi ý gì?',
        'Có combo văn phòng phẩm tiết kiệm không?',
        'Có sản phẩm dùng để học online, ghi chép, in ấn không?'
      ],
      gia_ca: [
        'Có sản phẩm giá rẻ không?',
        'Có combo tiết kiệm theo bộ không?',
        'Có giảm giá cho đơn hàng lớn không?',
        'Có mã giảm giá hoặc khuyến mãi theo mùa không?'
      ],
      dat_hang: [
        'Đặt hàng trên website như thế nào?',
        'Có thể đặt nhanh qua chat không?',
        'Có cần đăng ký tài khoản mới đặt được không?',
        'Có thể thay đổi đơn sau khi đặt không?'
      ],
      giao_hang: [
        'Shop có giao toàn quốc không?',
        'Giao hàng mất bao lâu?',
        'Phí ship được tính như thế nào?',
        'Có hỗ trợ kiểm tra hàng trước khi nhận không?'
      ],
      doi_tra: [
        'Hàng bị lỗi thì xử lý thế nào?',
        'Có đổi trả nếu nhận sai sản phẩm không?',
        'Trong bao lâu thì được đổi trả?',
        'Cần giữ hóa đơn hay ảnh chụp khiếu nại không?'
      ],
      ban_si: [
        'Có nhận đơn số lượng lớn không?',
        'Có báo giá sỉ cho trường học, công ty không?',
        'Có hỗ trợ xuất hóa đơn không?',
        'Có giao hàng định kỳ cho doanh nghiệp không?'
      ],
      thanh_toan: [
        'Có thanh toán khi nhận hàng không?',
        'Có chuyển khoản ngân hàng không?',
        'Có thanh toán qua ví điện tử không?',
        'Có hỗ trợ thanh toán cho đơn công ty không?'
      ]
    }
  };

  const messageContainer = document.getElementById('aiMessages');
  const quickMenuContainer = document.getElementById('aiQuick');

  // Hàm tạo nút bấm (Chip)
  function createChip(text, isBackBtn = false) {
    const chip = document.createElement('div');
    chip.className = 'ai-chip';
    chip.style.cssText = `padding:10px 14px; border-radius:12px; font-size:13px; cursor:pointer; font-weight: 600; text-align: center; transition: 0.2s; margin-bottom: 6px;`;
    
    if (isBackBtn) {
      chip.style.background = '#F1F5F9';
      chip.style.color = 'var(--ink-gray)';
      chip.style.border = '1px solid #CBD5E1';
    } else {
      chip.style.background = 'var(--ink-light)';
      chip.style.color = 'var(--ink-purple)';
      chip.style.border = '1.5px solid var(--ink-purple)';
      chip.onmouseover = () => { chip.style.background = 'var(--ink-purple)'; chip.style.color = '#fff'; };
      chip.onmouseout = () => { chip.style.background = 'var(--ink-light)'; chip.style.color = 'var(--ink-purple)'; };
    }
    
    chip.textContent = text;
    return chip;
  }

  // Khởi tạo Menu Cấp 1
  function showCategories() {
    quickMenuContainer.innerHTML = ''; 
    
    chatMenu.categories.forEach(cat => {
      const btn = createChip(cat.label);
      btn.onclick = () => {
        addMessage(`Tôi cần hỗ trợ về: ${cat.label.replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]\s*/g, '')}`, 'user');
        addMessage(`Bạn đang quan tâm đến vấn đề cụ thể nào trong mục "${cat.label.replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]\s*/g, '')}"?`, 'bot');
        showSubQuestions(cat.id); 
      };
      quickMenuContainer.appendChild(btn);
    });
  }

  // Khởi tạo Menu Cấp 2
  function showSubQuestions(categoryId) {
    quickMenuContainer.innerHTML = ''; 
    
    const backBtn = createChip('← Quay lại danh mục chính', true);
    backBtn.onclick = () => { showCategories(); };
    quickMenuContainer.appendChild(backBtn);

    chatMenu.questions[categoryId].forEach(q => {
      const btn = createChip(q);
      btn.onclick = () => { sendToAPI(q); };
      quickMenuContainer.appendChild(btn);
    });
  }

  // Hàm in tin nhắn. Thêm tính năng cho phép chèn link HTML (để link FB click được)
  function addMessage(text, sender, isHTML = false) {
    const msgDiv = document.createElement('div');
    msgDiv.className = `ai-msg ${sender}`;
    if (isHTML) {
      msgDiv.innerHTML = text;
    } else {
      msgDiv.textContent = text;
    }
    messageContainer.appendChild(msgDiv);
    messageContainer.scrollTop = messageContainer.scrollHeight;
  }

  async function sendToAPI(questionText) {
    addMessage(questionText, 'user');
    
    // Tạm thời xóa menu đi để khách không bấm lung tung khi đang chờ AI trả lời
    quickMenuContainer.innerHTML = '';

    const typing = document.createElement('div');
    typing.className = 'typing';
    typing.innerHTML = '<div class="dot"></div><div class="dot"></div><div class="dot"></div>';
    messageContainer.appendChild(typing);
    messageContainer.scrollTop = messageContainer.scrollHeight;

    try {
      const response = await fetch('chat_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: questionText })
      });
      const data = await response.json();

      setTimeout(() => {
        messageContainer.removeChild(typing);
        addMessage(data.reply, 'bot');
        
        // Gọi hàm hiện nút Feedback sau khi bot trả lời xong
        showFeedbackOptions();
      }, 600);

    } catch (error) {
      messageContainer.removeChild(typing);
      addMessage("Hệ thống đang bảo trì, vui lòng thử lại sau.", 'bot');
      showCategories(); 
    }
  }

  // ==========================================
  // PHẦN MỚI: XỬ LÝ FEEDBACK CỦA KHÁCH HÀNG
  // ==========================================

  function showFeedbackOptions() {
    quickMenuContainer.innerHTML = ''; 

    // Nút Option 1
    const btn1 = createChip('👍 Mình hiểu rồi, cảm ơn bạn');
    btn1.onclick = () => { handleFeedback('good'); };
    quickMenuContainer.appendChild(btn1);

    // Nút Option 2 (Đổi style một chút để khách dễ nhận diện)
    const btn2 = createChip('🤔 Mình chưa hiểu lắm', true);
    btn2.onclick = () => { handleFeedback('bad'); };
    quickMenuContainer.appendChild(btn2);
  }

  function handleFeedback(type) {

<script>
// Toggle dropdown tài khoản
function toggleDropdown() {
  document.getElementById('dropdownMenu')?.classList.toggle('open');
}
document.addEventListener('click', function(e) {
  const w = document.querySelector('.account-wrapper');
  if (w && !w.contains(e.target)) document.getElementById('dropdownMenu')?.classList.remove('open');
});

// Chuyển tab popup
function switchAuthTab(tab, el) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
  document.getElementById('auth-' + tab).classList.add('active');
  if (el) el.classList.add('active');
  else document.querySelectorAll('.auth-tab')[tab==='login'?0:1].classList.add('active');
}

// Tìm kiếm live
let searchTimer;
function liveSearch(q) {
  clearTimeout(searchTimer);
  const results = document.getElementById('searchResults');
  if (q.length < 2) { results.classList.remove('show'); return; }
  searchTimer = setTimeout(() => {
    fetch('search_api.php?q=' + encodeURIComponent(q))
    .then(r => r.json())
    .then(data => {
      if (data.length === 0) {
        results.innerHTML = '<div class="search-result-empty">Không tìm thấy sản phẩm</div>';
      } else {
        results.innerHTML = data.map(p =>
          `<a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php?q=${encodeURIComponent(p.name)}" class="search-result-item">
            <span class="search-result-name">${p.name}</span>
            <span class="search-result-price">${p.price_fmt}</span>
          </a>`
        ).join('');
      }
      results.classList.add('show');
    });
  }, 300);
}

// Đóng kết quả tìm kiếm khi click ra ngoài
document.addEventListener('click', function(e) {
  if (!document.getElementById('searchWrapper')?.contains(e.target))
    document.getElementById('searchResults')?.classList.remove('show');
});

// Nhấn Enter tìm kiếm
function goSearch() {
  const q = document.getElementById('searchInput').value.trim();
  if (q) window.location.href = 'Trang_sản_phẩm/Tất_cả_sản_phẩm.php?q=' + encodeURIComponent(q);
}
</script>
</body>
</html>
