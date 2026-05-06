<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
<header>
  <nav>
    <a href="#" class="nav-logo">
    <img src="logo.jpg" class="logo-img">
    </a>
    <div class="nav-links">
      <a href="index.php" >Trang Chủ</a>
      <a href="sanpham.php">Sản Phẩm</a>
      <a href="gioithieu.php">Giới Thiệu</a>
      <a href="tintuc.php">Blog</a>
      <a href="lienhe.php">Liên Hệ</a>
    </div>
    <div class="nav-right">
        <div class="search-box">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Tìm kiếm..." id="searchInput" oninput="searchProducts()">
        </div>
        <button class="btn-account" onclick="openModal('loginModal')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Tai Khoan
        </button>


      <!-- Nút giỏ hàng - Small pill -->
      <button class="btn-cart" onclick="document.location='Giỏ_hàng.php'">
        CART
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <span class="cart-badge" id="cartBadge">0</span>
      </button>
    </div>
  </nav>

  <!-- CART OVERLAY & DRAWER -->
  <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
  <div class="cart-drawer" id="cartDrawer">
    <div class="cart-header">
      <h3>Gio Hang</h3>
      <button class="cart-close" onclick="toggleCart()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="cart-body" id="cartBody">
      <div class="cart-empty">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin:0 auto 16px;display:block"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <p style="font-weight:600;margin-bottom:6px;">Giỏ hàng của tôi</p>
        <p style="font-size:13px;">Hãy thêm sản phẩm vào giỏ hàng</p>
      </div>
    </div>
    <div class="cart-footer" id="cartFooter" style="display:none">
      <div class="cart-total"><span>Tổng cộng:</span><strong id="cartTotal">0d</strong></div>
      <button class="btn-checkout">Thanh Toán Ngay</button>
    </div>
  </div>
</header>
<div id="result"></div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Lấy tên file hiện tại từ thanh địa chỉ URL
    let currentPath = window.location.pathname.split('/').pop();
    currentPath = decodeURIComponent(currentPath); // Giải mã URL tiếng Việt
    
    if (currentPath === '') currentPath = 'index.php'; // Mặc định là trang chủ

    // Mảng các trang danh mục con của Sản Phẩm
    const productPages = [
        'sanpham.php', 'Giấy&sổ.php', 'Bút_viết.php', 'Kẹp&Ghim.php', 
        'Dụng_cụ.php', 'Lưu_trữ.php', 'Hộp_đựng.php', 'Khác.php'
    ];

    // Xóa class active cũ (nếu có)
    document.querySelectorAll('.nav-links a').forEach(link => link.classList.remove('active'));

    // Gắn class active
    if (productPages.includes(currentPath)) {
        // Nếu đang ở bất kỳ trang sản phẩm nào, active nút "Sản Phẩm"
        document.querySelector('.nav-links a[href="sanpham.php"]').classList.add('active');
    } else {
        // Active các trang bình thường khác
        let activeLink = document.querySelector(`.nav-links a[href="${currentPath}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
});
</script>
