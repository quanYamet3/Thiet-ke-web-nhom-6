<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>iNK - Văn phòng phẩm</title>
  <link rel="stylesheet" href="CSS_trang_chủ.css">
  <link rel="stylesheet" href="CSS_header.css">
  <link rel="stylesheet" href="CSS_footer.css">
</head>
<?php include 'header.php'; ?>
<body>
  
<!-- ═══════════ HOME PAGE ═══════════ -->
  <div id="home" class="page active">
    <!-- HERO -->
    <section class="hero">
      <div class="hero-bg-circle c1"></div>
      <div class="hero-bg-circle c2"></div>
      <div class="hero-content">
        <div class="hero-tag">
          <span class="hero-tag-dot"></span>
          Hàng mới về - Bút bi cao cấp 2026
        </div>
        <h1>Văn phòng phẩm <em>Đẳng cấp</em> cho mọi nhu cầu</h1>
        <p>INK cung cấp đầy đủ sản phẩm văn phòng phẩm chất lượng cao, từ bút, sổ tay đến thiết bị văn phòng hiện đại.</p>
        <div class="hero-btns">
          <button class="btn-primary" onclick="document.location='sanpham.php'">Mua Ngay</button>
          <button class="btn-secondary" onclick="document.location='gioithieu.php'">Khám Phá INK</button>
        </div>
      </div>
      <!-- 4 ô ở trang đầu -->
      <div class="hero-visual">
        <div class="hero-card">
          <div class="hero-card-grid">
            <div class="hero-prod" onclick="document.location='Sản_phẩm/Bút_viết.html'">
              <div class="hero-prod-icon">🖋️</div>
              <div class="hero-prod-name">Bút bi cao Cấp</div>
              <div class="hero-prod-price"> Từ 25.000d</div>
            </div>
            <div class="hero-prod" onclick="document.location='Sản_phẩm/Sổ_tay.html'">
              <div class="hero-prod-icon">📝</div>
              <div class="hero-prod-name">Sổ tay A5</div>
              <div class="hero-prod-price"> Từ 55.000d</div>
            </div>
            <div class="hero-prod" onclick="document.location='Sản_phẩm/Khác.html'">
              <div class="hero-prod-icon">✂️</div>
              <div class="hero-prod-name">Kéo cắt</div>
              <div class="hero-prod-price"> Từ 18.000d</div>
            </div>
            <div class="hero-prod" onclick="document.location='Sản_phẩm/Lưu_trữ.html'">
              <div class="hero-prod-icon">📁</div>
              <div class="hero-prod-name">File hồ sơ</div>
              <div class="hero-prod-price"> Từ 12.000d</div>
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
        <div class="cat-card" onclick="document.location='/*link danh mục bút*/';setActiveByIndex(1)">
          <div class="cat-icon">&#128393;</div>
          <div class="cat-name">Bút Viết</div>
        </div>
        <div class="cat-card" onclick="document.location='/*link danh mục sổ*/';setActiveByIndex(1)">
          <div class="cat-icon">&#128214;</div>
          <div class="cat-name">Sổ Tay</div>
          
        </div>
        <div class="cat-card" onclick="document.location='/* link danh mục văn phòng ';setActiveByIndex(1)">
          <div class="cat-icon">&#9986;</div>
          <div class="cat-name">Văn Phòng</div>
        </div>
        <div class="cat-card" onclick="document.location='/* link danh muc giấy */';setActiveByIndex(1)">
          <div class="cat-icon">&#128203;</div>
          <div class="cat-name">Giấy In</div>
        </div>
      </div>
    </section>

    <!-- BLOG PREVIEW -->
    <section style="padding-top:0">
      <div class="section-label">Blog</div>
      <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:32px;flex-wrap:wrap;gap:12px">
        <div class="section-title">Tin tức mới nhất</div>
        <button class="filter-tab" onclick="document.location='gioithieu.phphtml'">Xem tất cả</button>
      </div>
      <div class="blog-grid">
        <div class="blog-card" onclick="document.location='gioithieu.php1.html'";>
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
        <div class="blog-card" onclick="document.location='gioithieu.php2.html'";>
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
        <div class="blog-card" onclick="document.location='gioithieu.php3.html'";>
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
<?php include 'footer.php'; ?>
<?php include 'chat_main.php'; ?>
<script><!-- JavaScript cho AI Chatbot -->
  // 1. BIẾN TRẠNG THÁI
let products = []; // Chờ PHP đổ dữ liệu vào
let cart = JSON.parse(localStorage.getItem('ink_cart')) || []; // Lấy giỏ hàng từ LocalStorage nếu có
let currentFilter = '';

// 2. HÀM TẢI DỮ LIỆU TỪ PHP (CSDL)
async function fetchProducts() {
  try {
    // Gọi API từ backend (Ví dụ: get_products.php)
    const res = await fetch('get_products.php');
    products = await res.json();
    
    // In ra màn hình sau khi có data
    renderProducts(products, 'productsGrid');
  } catch (err) {
    console.error("Lỗi tải sản phẩm:", err);
  }
}

// 3. CÁC HÀM XỬ LÝ GIAO DIỆN (UI)
function renderProducts(arr, containerId) {
  // Giữ nguyên ruột hàm renderProducts của bạn
  // ...
}

function updateCart() {
  // Giữ nguyên ruột hàm updateCart của bạn
  // Thêm dòng này ở cuối để lưu giỏ hàng không bị mất khi F5:
  localStorage.setItem('ink_cart', JSON.stringify(cart));
}

// Giữ lại các hàm xử lý Modal, Toast, Đóng/Mở Cart Drawer
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id, e) { if(!e || e.target === document.getElementById(id)) document.getElementById(id).classList.remove('open'); }
function showToast(msg) { /* ... */ }
function toggleCart() { /* ... */ }

// Các hàm Thêm/Sửa/Xóa giỏ hàng giữ lại nhưng nhớ gọi updateCart() sau khi thay đổi
function addToCartById(id) { /* ... */ }
function removeFromCart(id) { /* ... */ }
function changeQty(id, delta) { /* ... */ }

// 4. KHỞI CHẠY
fetchProducts(); // Gọi CSDL
updateCart();    // Hiện số lượng giỏ hàng cũ
</script>
</body>
</html>