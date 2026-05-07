<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';
$current_page = basename($_SERVER['PHP_SELF']);
// Xử lý đăng nhập
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['login_error'] = 'Email hoặc mật khẩu không đúng!';
        header("Location: " . $_SERVER['PHP_SELF'] . "?login=1");
        exit;
    }
}

// Xử lý đăng ký
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $fullname = trim($_POST['reg_fullname']);
    $email    = trim($_POST['reg_email']);
    $phone    = trim($_POST['reg_phone']);
    $password = $_POST['reg_password'];
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['register_error'] = 'Email này đã được sử dụng!';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $ins = $conn->prepare("INSERT INTO users (fullname, email, phone, password_hash) VALUES (?, ?, ?, ?)");
        $ins->bind_param("ssss", $fullname, $email, $phone, $hash);
        $ins->execute();
        $_SESSION['register_ok'] = true;
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?register=1");
    exit;
}

// Đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
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
.dropdown-header { padding: 16px 18px 12px; background: linear-gradient(135deg,#5c3290,#7c3aed); color:#fff; }
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

/* AUTH POPUP */
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

/* Toast notification */
.cart-toast {
    position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px);
    background:#1a1a2e; color:#fff; padding:12px 22px; border-radius:12px;
    font-size:14px; font-weight:500; z-index:9998;
    transition:transform .3s cubic-bezier(.34,1.56,.64,1), opacity .3s;
    opacity:0; pointer-events:none; white-space:nowrap;
}
.cart-toast.show { transform:translateX(-50%) translateY(0); opacity:1; }
</style>

<?php
$show_login    = isset($_GET['login']);
$show_register = isset($_GET['register']);
$login_error   = $_SESSION['login_error'] ?? '';
$register_error= $_SESSION['register_error'] ?? '';
$register_ok   = isset($_SESSION['register_ok']);
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['register_ok']);
?>

<!-- POPUP ĐĂNG NHẬP / ĐĂNG KÝ -->
<div class="auth-overlay <?= ($show_login||$show_register||$login_error||$register_error||$register_ok)?'show':'' ?>" id="authOverlay">
  <div class="auth-box">
    <button class="auth-close" onclick="document.getElementById('authOverlay').classList.remove('show')">×</button>
    <div class="auth-tabs">
      <div class="auth-tab <?= (!$show_register&&!$register_error&&!$register_ok)?'active':'' ?>" onclick="switchAuthTab('login',this)">Đăng nhập</div>
      <div class="auth-tab <?= ($show_register||$register_error||$register_ok)?'active':'' ?>" onclick="switchAuthTab('register',this)">Đăng ký</div>
    </div>
    <!-- ĐĂNG NHẬP -->
    <div class="auth-form <?= (!$show_register&&!$register_error&&!$register_ok)?'active':'' ?>" id="auth-login">
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
    <div class="auth-form <?= ($show_register||$register_error||$register_ok)?'active':'' ?>" id="auth-register">
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

<!-- TOAST -->
<div class="cart-toast" id="cartToast"></div>

<!-- CART OVERLAY -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>

<!-- CART DRAWER -->
<div class="cart-drawer" id="cartDrawer">
  <div class="cart-drawer-header">
    <div>
      <h3>🛒 Giỏ hàng của bạn</h3>
      <div class="cart-count-label" id="cartCountLabel">0 sản phẩm</div>
    </div>
    <button class="btn-close-cart" onclick="closeCart()">×</button>
  </div>

  <div class="cart-items-list" id="cartItemsList">
    <div class="cart-empty">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
      </svg>
      <p>Giỏ hàng trống</p>
    </div>
  </div>

  <div class="cart-drawer-footer" id="cartFooter" style="display:none;">
    <div class="cart-total-row">
      <span class="label">Tổng cộng:</span>
      <span class="amount" id="cartTotalAmount">0đ</span>
    </div>
    <button class="btn-checkout" onclick="goToCheckout()">Tiến hành thanh toán →</button>
    <button class="btn-clear-cart" onclick="clearCart()">Xóa toàn bộ giỏ hàng</button>
  </div>
</div>

<header>
  <nav>
    <a href="index.php" class="nav-logo">
      <img src="logo.jpg" class="logo-img">
    </a>
    <div class="nav-links">
      <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Trang Chủ</a>
      <a href="sanpham.php" class="<?= ($current_page == 'sanpham.php') ? 'active' : '' ?>">Sản Phẩm</a>
      <a href="gioithieu.php" class="<?= ($current_page == 'gioithieu.php') ? 'active' : '' ?>">Giới Thiệu</a>
      <a href="tintuc.php" class="<?= ($current_page == 'tintuc.php') ? 'active' : '' ?>">Blog</a>
      <a href="lienhe.php" class="<?= ($current_page == 'lienhe.php') ? 'active' : '' ?>">Liên Hệ</a>
    </div>
    <div class="nav-right">
      <div class="search-box">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Tìm kiếm..." id="searchInput"
               onkeydown="if(event.key==='Enter'){const q=this.value.trim();if(q)window.location.href='sanpham.php?q='+encodeURIComponent(q)}">
      </div>

      <!-- NÚT GIỎ HÀNG -->
      <button class="btn-cart" onclick="openCart()" title="Giỏ hàng">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        Giỏ hàng
        <span class="cart-badge" id="cartBadge">0</span>
      </button>

      <!-- NÚT TÀI KHOẢN -->
      <div class="account-wrapper">
        <?php if (isset($_SESSION['user_name'])): ?>
        <button class="btn-account" onclick="toggleDropdown()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?> ▾
        </button>
        <div class="dropdown-menu" id="dropdownMenu">
          <div class="dropdown-header">
            <div class="d-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
            <div class="d-email"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
          </div>
          <a href="taikhoan.php" class="dropdown-item">👤 Hồ sơ của tôi</a>
          <a href="taikhoan.php?tab=orders" class="dropdown-item">📦 Lịch sử mua hàng</a>
          <div class="dropdown-divider"></div>
          <a href="?logout=1" class="dropdown-item logout">🚪 Đăng xuất</a>
        </div>
        <?php else: ?>
        <button class="btn-account" onclick="document.getElementById('authOverlay').classList.add('show')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Tài khoản
        </button>
        <?php endif; ?>
      </div>

    </div>
  </nav>
</header>

<script>
/* ════════════════════════════════
   CART SYSTEM – localStorage-based
   ════════════════════════════════ */

// Lấy giỏ hàng từ localStorage
function getCart() {
  return JSON.parse(localStorage.getItem('ink_cart') || '[]');
}

// Lưu giỏ hàng vào localStorage
function saveCart(cart) {
  localStorage.setItem('ink_cart', JSON.stringify(cart));
}

// Cập nhật badge số lượng trên nút giỏ hàng
function updateCartBadge() {
  const cart = getCart();
  const total = cart.reduce((sum, item) => sum + item.qty, 0);
  const badge = document.getElementById('cartBadge');
  if (!badge) return;
  badge.textContent = total;
  if (total > 0) {
    badge.classList.add('has-items');
    // Hiệu ứng bump
    badge.classList.remove('bump');
    void badge.offsetWidth; // reflow
    badge.classList.add('bump');
    setTimeout(() => badge.classList.remove('bump'), 300);
  } else {
    badge.classList.remove('has-items');
  }
}

// Thêm sản phẩm vào giỏ hàng
// Hàm này được gọi từ sanpham.php: addToCart(id)
// Cần thêm tham số name, price, image (nếu có) hoặc fetch từ server
async function addToCart(id, name, price, image) {
  // Nếu không truyền name/price (gọi từ button đơn giản), fetch từ server
  if (!name || !price) {
    try {
      const res = await fetch(`get_product.php?id=${id}`);
      const p = await res.json();
      if (p) {
        name  = p.name;
        price = p.price;
        image = p.image;
      }
    } catch(e) {
      showToast('❌ Không thể tải thông tin sản phẩm!');
      return;
    }
  }

  const cart = getCart();
  const idx = cart.findIndex(item => item.id == id);
  if (idx >= 0) {
    cart[idx].qty += 1;
  } else {
    cart.push({ id, name, price, image: image || '', qty: 1 });
  }
  saveCart(cart);
  updateCartBadge();
  showToast(`🛒 Đã thêm "${name}" vào giỏ hàng!`);
}

// Thay đổi số lượng sản phẩm
function changeQty(id, delta) {
  const cart = getCart();
  const idx = cart.findIndex(item => item.id == id);
  if (idx < 0) return;
  cart[idx].qty += delta;
  if (cart[idx].qty <= 0) cart.splice(idx, 1);
  saveCart(cart);
  updateCartBadge();
  renderCartDrawer();
}

// Xoá 1 sản phẩm khỏi giỏ
function removeFromCart(id) {
  let cart = getCart().filter(item => item.id != id);
  saveCart(cart);
  updateCartBadge();
  renderCartDrawer();
}

// Xoá toàn bộ giỏ
function clearCart() {
  if (!confirm('Bạn chắc chắn muốn xóa toàn bộ giỏ hàng?')) return;
  saveCart([]);
  updateCartBadge();
  renderCartDrawer();
}

// Render nội dung giỏ hàng trong drawer
function renderCartDrawer() {
  const cart = getCart();
  const list = document.getElementById('cartItemsList');
  const footer = document.getElementById('cartFooter');
  const countLabel = document.getElementById('cartCountLabel');
  if (!list) return;

  const totalQty = cart.reduce((s, i) => s + i.qty, 0);
  if (countLabel) countLabel.textContent = totalQty + ' sản phẩm';

  if (cart.length === 0) {
    list.innerHTML = `
      <div class="cart-empty">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <p>Giỏ hàng trống</p>
      </div>`;
    if (footer) footer.style.display = 'none';
    return;
  }

  let total = 0;
  let html = '';
  cart.forEach(item => {
    const subtotal = item.price * item.qty;
    total += subtotal;
    const imgSrc = item.image ? `images/${item.image}` : 'images/placeholder.jpg';
    html += `
      <div class="cart-item">
        <img src="${imgSrc}" alt="${escHtml(item.name)}" onerror="this.src='images/placeholder.jpg'">
        <div class="cart-item-info">
          <div class="cart-item-name" title="${escHtml(item.name)}">${escHtml(item.name)}</div>
          <div class="cart-item-price">${fmtVND(item.price)}đ</div>
          <div class="cart-item-qty">
            <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
            <span class="qty-val">${item.qty}</span>
            <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
          </div>
        </div>
        <button class="btn-remove-item" onclick="removeFromCart(${item.id})" title="Xóa">×</button>
      </div>`;
  });

  list.innerHTML = html;
  if (footer) {
    footer.style.display = 'block';
    const amt = document.getElementById('cartTotalAmount');
    if (amt) amt.textContent = fmtVND(total) + 'đ';
  }
}

// Mở drawer giỏ hàng
function openCart() {
  renderCartDrawer();
  document.getElementById('cartDrawer').classList.add('open');
  document.getElementById('cartOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

// Đóng drawer
function closeCart() {
  document.getElementById('cartDrawer').classList.remove('open');
  document.getElementById('cartOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

// Chuyển đến trang thanh toán
function goToCheckout() {
  const cart = getCart();
  if (cart.length === 0) { showToast('Giỏ hàng trống!'); return; }
  window.location.href = 'thanhtoan.php';
}

/* ── Helpers ── */
function fmtVND(n) {
  return new Intl.NumberFormat('vi-VN').format(n);
}
function escHtml(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ── Toast ── */
let toastTimer;
function showToast(msg) {
  const t = document.getElementById('cartToast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}

/* ── Auth tab switch ── */
function switchAuthTab(tab, el) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
  if (el) el.classList.add('active');
  else {
    document.querySelectorAll('.auth-tab').forEach(t => {
      if ((tab==='login'&&t.textContent.includes('nhập'))||(tab==='register'&&t.textContent.includes('ký'))) t.classList.add('active');
    });
  }
  document.getElementById('auth-' + tab).classList.add('active');
}

/* ── Dropdown account ── */
function toggleDropdown() {
  document.getElementById('dropdownMenu')?.classList.toggle('open');
}
document.addEventListener('click', function(e) {
  const wrap = document.querySelector('.account-wrapper');
  if (wrap && !wrap.contains(e.target)) {
    document.getElementById('dropdownMenu')?.classList.remove('open');
  }
});

/* ── Khởi tạo badge khi trang load ── */
document.addEventListener('DOMContentLoaded', updateCartBadge);
</script>