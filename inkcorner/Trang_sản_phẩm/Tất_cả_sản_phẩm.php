<?php
session_start();
require_once '../config.php';

// Lấy danh mục
$categories = $conn->query("SELECT * FROM categories ORDER BY id")->fetch_all(MYSQLI_ASSOC);

// Lọc theo danh mục nếu có
$cat_id = intval($_GET['cat'] ?? 0);
if ($cat_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id");
    $stmt->bind_param("i", $cat_id);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $products = $conn->query("SELECT * FROM products ORDER BY id")->fetch_all(MYSQLI_ASSOC);
}

// Tìm kiếm
$keyword = trim($_GET['q'] ?? '');
if ($keyword) {
    $kw = '%' . $keyword . '%';
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? ORDER BY id");
    $stmt->bind_param("s", $kw);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Lấy thông tin user nếu đã đăng nhập
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id, fullname, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Tổng giỏ hàng
$cart_count = array_sum($_SESSION['cart'] ?? []);

// Tên danh mục đang chọn
$current_cat = 'Tất cả sản phẩm';
foreach ($categories as $c) {
    if ($c['id'] == $cat_id) $current_cat = $c['name'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - InkCorner</title>
    <link rel="stylesheet" href="CSS_San_Pham.css">
    <link rel="stylesheet" href="../CSS_header.css">
    <link rel="stylesheet" href="../CSS_footer.css">
    <style>
        /* Toast thông báo */
        .toast-cart {
            position: fixed; bottom: 28px; left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: #5c3290; color: #fff;
            padding: 12px 24px; border-radius: 12px;
            font-size: 14px; font-weight: 500;
            z-index: 9999; transition: .3s;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
            white-space: nowrap; opacity: 0;
        }
        .toast-cart.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
        .btn-add-cart { cursor: pointer; }
        .btn-add-cart:disabled { opacity: .6; cursor: not-allowed; }
        .search-form { display:flex; gap:8px; margin-bottom:20px; }
        .search-form input {
            flex:1; padding:10px 14px;
            border:1.5px solid #e9d5ff; border-radius:8px;
            font-size:14px; outline:none;
        }
        .search-form input:focus { border-color:#5c3290; }
        .search-form button {
            padding:10px 20px; background:#5c3290; color:#fff;
            border:none; border-radius:8px; font-weight:600; cursor:pointer;
        }
        /* DROPDOWN */
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
        }
        .dropdown-item:hover { background:#f3e8ff; color:#5c3290; }
        .dropdown-divider { height:1px; background:#f0e8ff; margin:4px 0; }
        .dropdown-item.logout { color:#d8511c; }
        .dropdown-item.logout:hover { background:#fff5f0; }
    </style>
</head>
<body>

<!-- TOAST -->
<div class="toast-cart" id="toastCart">✅ Đã thêm vào giỏ hàng!</div>

<header>
    <nav>
        <a href="../Trang_chủ.php" class="nav-logo">
            <img src="../logo.jpg" class="logo-img">
        </a>
        <div class="nav-links">
            <a href="../Trang_chủ.php">Trang Chủ</a>
            <a href="Tất_cả_sản_phẩm.php" class="active">Sản Phẩm</a>
            <a href="../Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới Thiệu</a>
            <a href="../Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
            <a href="../Liên_hệ.html">Liên Hệ</a>
        </div>
        <div class="nav-right">
            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" placeholder="Tìm kiếm..." id="searchInput"
                       value="<?= htmlspecialchars($keyword) ?>"
                       onkeydown="if(event.key==='Enter') searchProduct()">
            </div>
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
                    <a href="../User.php?page=profile" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Hồ sơ của tôi
                    </a>
                    <a href="../User.php?page=orders" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                        Lịch sử mua hàng
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="../User.php?logout=1" class="dropdown-item logout">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Đăng xuất
                    </a>
                </div>
                <?php else: ?>
                <a href="../User.php" class="btn-account">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Tài khoản
                </a>
                <?php endif; ?>
            </div>
            <a href="../Giỏ_hàng.php" class="btn-cart">
                CART
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <span class="cart-badge" id="cartBadge"><?= $cart_count ?></span>
            </a>
        </div>
    </nav>
</header>
<br><br><br>

<div class="container main-content">

    <!-- SIDEBAR DANH MỤC -->
    <aside class="sidebar">
        <h3>DANH MỤC SẢN PHẨM</h3>
        <ul class="category-list">
            <li>
                <a href="Tất_cả_sản_phẩm.php" 
                   class="<?= $cat_id == 0 ? 'active' : '' ?>">
                   Tất cả sản phẩm
                </a>
            </li>
            <?php foreach ($categories as $cat): ?>
            <li>
                <a href="Tất_cả_sản_phẩm.php?cat=<?= $cat['id'] ?>"
                   class="<?= $cat_id == $cat['id'] ? 'active' : '' ?>">
                   <?= htmlspecialchars($cat['name']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- KHU VỰC SẢN PHẨM -->
    <section class="product-area">
        <div class="breadcrumb">
            Trang chủ / Sản phẩm / <span><?= htmlspecialchars($current_cat) ?></span>
        </div>

        <!-- Ô tìm kiếm -->
        <div class="search-form">
            <input type="text" id="searchBox" 
                   placeholder="Tìm tên sản phẩm..."
                   value="<?= htmlspecialchars($keyword) ?>">
            <button onclick="searchProduct()">Tìm kiếm</button>
            <?php if ($keyword): ?>
            <a href="Tất_cả_sản_phẩm.php" style="padding:10px 16px;border:1.5px solid #ccc;border-radius:8px;font-size:14px;color:#666;text-decoration:none;">✕ Xóa</a>
            <?php endif; ?>
        </div>

        <?php if ($keyword): ?>
        <p style="margin-bottom:16px;color:#666;font-size:14px;">
            Kết quả tìm kiếm cho "<strong><?= htmlspecialchars($keyword) ?></strong>": 
            <strong><?= count($products) ?></strong> sản phẩm
        </p>
        <?php endif; ?>

        <!-- GRID SẢN PHẨM -->
        <div class="product-grid">
            <?php if (empty($products)): ?>
            <div style="grid-column:1/-1;text-align:center;padding:60px;color:#999;">
                <p style="font-size:18px;">Không tìm thấy sản phẩm nào.</p>
            </div>
            <?php else: ?>
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                <img src="<?= $p['image'] ? '../images/' . htmlspecialchars($p['image']) : '' ?>"
                     alt="<?= htmlspecialchars($p['name']) ?>"
                     class="product-img"
                     onerror="this.style.display='none'">
                <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                <div class="product-price"><?= number_format($p['price'], 0, ',', '.') ?>đ</div>
                <button class="btn-add-cart"
                        data-id="<?= $p['id'] ?>"
                        data-name="<?= htmlspecialchars($p['name']) ?>"
                        onclick="addToCart(this)">
                    Thêm vào giỏ
                </button>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</div>
<br><br>

<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <img src="../logo.jpg" style="width:40px;margin-bottom:12px">
            <p>Chuyên cung cấp văn phòng phẩm chất lượng cao cho cá nhân và doanh nghiệp.</p>
        </div>
        <div class="footer-col">
            <h4>Cửa Hàng</h4>
            <a href="Tất_cả_sản_phẩm.php">Sản phẩm</a>
            <a href="../Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới thiệu</a>
            <a href="../Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
            <a href="../Liên_hệ.html">Liên hệ</a>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ</h4>
            <a href="../chinh_sach.php?page=doi-tra">Chính sách đổi trả</a>
            <a href="../chinh_sach.php?page=huong-dan">Hướng dẫn mua hàng</a>
            <a href="../chinh_sach.php?page=bao-mat">Chính sách bảo mật</a>
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

<script>
// Thêm vào giỏ hàng
function addToCart(btn) {
    const id   = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    btn.disabled = true;
    btn.textContent = 'Đang thêm...';

    fetch('../add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({product_id: parseInt(id), quantity: 1})
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Thêm vào giỏ';
        if (data.success) {
            // Cập nhật badge
            document.getElementById('cartBadge').textContent = data.cart_count;
            // Hiện toast
            showToast('✅ Đã thêm "' + name + '" vào giỏ!');
        } else {
            showToast('❌ ' + data.message);
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Thêm vào giỏ';
        showToast('❌ Lỗi kết nối, thử lại!');
    });
}

// Hiện thông báo toast
function showToast(msg) {
    const t = document.getElementById('toastCart');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

// Tìm kiếm
function searchProduct() {
    const q = document.getElementById('searchBox').value.trim();
    if (q) {
        window.location.href = 'Tất_cả_sản_phẩm.php?q=' + encodeURIComponent(q);
    }
}

// Nhấn Enter tìm kiếm
document.getElementById('searchBox').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') searchProduct();
});

// Toggle dropdown
function toggleDropdown() {
    document.getElementById('dropdownMenu')?.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const w = document.querySelector('.account-wrapper');
    if (w && !w.contains(e.target)) document.getElementById('dropdownMenu')?.classList.remove('open');
});
</script>
</body>
</html>
