<?php
// header_common.php - Header dùng chung cho toàn bộ website
// CÁCH DÙNG: include 'header_common.php'; ở đầu mỗi trang
// Biến $base_path: đường dẫn về thư mục gốc (mặc định là '' nếu ở thư mục gốc, '../' nếu trong thư mục con)

if (!isset($base_path)) $base_path = '';

// Đảm bảo session đã start
if (session_status() === PHP_SESSION_NONE) session_start();

// Lấy thông tin user nếu đã đăng nhập
$header_user = null;
if (isset($_SESSION['user_id'])) {
    // Kết nối nếu chưa có
    if (!isset($conn)) require_once $base_path . 'config.php';
    $stmt = $conn->prepare("SELECT id, fullname, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $header_user = $stmt->get_result()->fetch_assoc();
}

$cart_count = array_sum($_SESSION['cart'] ?? []);

// Trang tìm kiếm
$search_url = $base_path . 'Trang_sản_phẩm/Tất_cả_sản_phẩm.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'iNK Store' ?></title>
    <link rel="stylesheet" href="<?= $base_path ?>CSS_header.css">
    <link rel="stylesheet" href="<?= $base_path ?>CSS_footer.css">
    <?php if (isset($extra_css)): ?>
    <?= $extra_css ?>
    <?php endif; ?>
    <style>
        /* ── DROPDOWN TÀI KHOẢN ── */
        .account-wrapper { position: relative; }
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
        .dropdown-header .d-name  { font-weight: 700; font-size: 15px; }
        .dropdown-header .d-email { font-size: 12px; opacity: .8; margin-top: 2px; }
        .dropdown-item {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 18px; font-size: 14px; color: #333;
            text-decoration: none; transition: background .15s;
            cursor: pointer; border: none; background: none;
            width: 100%; text-align: left;
        }
        .dropdown-item:hover { background: #f3e8ff; color: #5c3290; }
        .dropdown-divider { height: 1px; background: #f0e8ff; margin: 4px 0; }
        .dropdown-item.logout { color: #d8511c; }
        .dropdown-item.logout:hover { background: #fff5f0; }

        /* ── SEARCH RESULTS DROPDOWN ── */
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            border: 1px solid #e9d5ff;
            border-top: none;
            max-height: 320px;
            overflow-y: auto;
            z-index: 4000;
            display: none;
        }
        .search-results.show { display: block; }
        .search-result-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 16px; cursor: pointer;
            transition: background .15s; text-decoration: none; color: #333;
        }
        .search-result-item:hover { background: #f3e8ff; }
        .search-result-name { font-size: 13px; font-weight: 500; flex: 1; }
        .search-result-price { font-size: 13px; color: #d8511c; font-weight: 600; }
        .search-result-empty { padding: 16px; text-align: center; color: #999; font-size: 13px; }

        .search-box { position: relative; }
    </style>
</head>
<body>

<header>
<nav>
    <a href="<?= $base_path ?>Trang_chủ.php" class="nav-logo">
        <img src="<?= $base_path ?>logo.jpg" class="logo-img">
    </a>
    <div class="nav-links">
        <a href="<?= $base_path ?>Trang_chủ.php" <?= (isset($active_page) && $active_page==='home') ? 'class="active"':'' ?>>Trang Chủ</a>
        <a href="<?= $base_path ?>Trang_sản_phẩm/Tất_cả_sản_phẩm.php" <?= (isset($active_page) && $active_page==='products') ? 'class="active"':'' ?>>Sản Phẩm</a>
        <a href="#">Giới Thiệu</a>
        <a href="#">Blog</a>
        <a href="<?= $base_path ?>Liên_hệ.html" <?= (isset($active_page) && $active_page==='contact') ? 'class="active"':'' ?>>Liên Hệ</a>
    </div>
    <div class="nav-right">

        <!-- Ô tìm kiếm có gợi ý -->
        <div class="search-box" id="searchWrapper">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Tìm kiếm..." id="globalSearch"
                   autocomplete="off"
                   onkeydown="if(event.key==='Enter') goSearch()"
                   oninput="liveSearch(this.value)">
            <div class="search-results" id="searchResults"></div>
        </div>

        <!-- Nút tài khoản -->
        <div class="account-wrapper">
            <?php if ($header_user): ?>
            <button class="btn-account" onclick="toggleDropdown()" id="accountBtn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <?= htmlspecialchars(explode(' ', $header_user['fullname'])[0]) ?> ▾
            </button>
            <div class="dropdown-menu" id="dropdownMenu">
                <div class="dropdown-header">
                    <div class="d-name"><?= htmlspecialchars($header_user['fullname']) ?></div>
                    <div class="d-email"><?= htmlspecialchars($header_user['email']) ?></div>
                </div>
                <a href="<?= $base_path ?>User.php?page=profile" class="dropdown-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Hồ sơ của tôi
                </a>
                <a href="<?= $base_path ?>User.php?page=orders" class="dropdown-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    Lịch sử mua hàng
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?= $base_path ?>User.php?logout=1" class="dropdown-item logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Đăng xuất
                </a>
            </div>
            <?php else: ?>
            <a href="<?= $base_path ?>User.php" class="btn-account">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Tài khoản
            </a>
            <?php endif; ?>
        </div>

        <!-- Giỏ hàng -->
        <a href="<?= $base_path ?>Giỏ_hàng.php" class="btn-cart">
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

<script>
// Toggle dropdown tài khoản
function toggleDropdown() {
    document.getElementById('dropdownMenu')?.classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const w = document.querySelector('.account-wrapper');
    if (w && !w.contains(e.target)) {
        document.getElementById('dropdownMenu')?.classList.remove('open');
    }
});

// Tìm kiếm live
let searchTimer;
function liveSearch(q) {
    clearTimeout(searchTimer);
    const results = document.getElementById('searchResults');
    if (q.length < 2) { results.classList.remove('show'); return; }
    searchTimer = setTimeout(() => {
        fetch('<?= $base_path ?>search_api.php?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                results.innerHTML = '<div class="search-result-empty">Không tìm thấy sản phẩm</div>';
            } else {
                results.innerHTML = data.map(p =>
                    `<a href="<?= $base_path ?>Trang_sản_phẩm/Tất_cả_sản_phẩm.php?q=${encodeURIComponent(p.name)}" class="search-result-item">
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
    if (!document.getElementById('searchWrapper')?.contains(e.target)) {
        document.getElementById('searchResults')?.classList.remove('show');
    }
});

// Nhấn Enter tìm kiếm
function goSearch() {
    const q = document.getElementById('globalSearch').value.trim();
    if (q) window.location.href = '<?= $base_path ?>Trang_sản_phẩm/Tất_cả_sản_phẩm.php?q=' + encodeURIComponent(q);
}
</script>
