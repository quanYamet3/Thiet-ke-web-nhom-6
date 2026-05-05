<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <link rel="stylesheet" href="CSS_Thanh_toán.css">
</head>
<body>
<header>
<nav>
    <a href="#" class="nav-logo">
    <img src="logo.jpg" class="logo-img">
    </a>
    <div class="nav-links">
      <a href="#">Trang Chủ</a>
      <a href="#">Sản Phẩm</a>
      <a href="#">Giới Thiệu</a>
      <a href="#">Blog</a>
      <a href="#">Liên Hệ</a>
    </div>
    <div class="nav-right">
      <div class="search-box">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Tìm kiếm..." id="searchInput" oninput="filterProducts()">
      </div>
      <button class="btn-account" onclick="openModal('loginModal')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Tài khoản
      </button>
      <!-- Nút giỏ hàng - Small pill -->
      <button class="btn-cart" onclick="toggleCart()">
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
<div class="checkout-wrapper">
    
    <!-- Thanh tiến trình -->
    <div class="progress-bar">
        <div class="progress-step completed">
            <i class="fas fa-shopping-cart"></i> Giỏ hàng
        </div>
        <div class="progress-line completed"></div>
        <div class="progress-step active">
            <i class="fas fa-credit-card"></i> Thanh toán
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
            <i class="fas fa-check-circle"></i> Hoàn tất
        </div>
    </div>

    <div class="checkout-container">
        <!-- Cột Trái: Thông tin & Vận chuyển -->
        <div class="main-content">
            
            <!-- Phần 1: Thông tin giao hàng -->
            <div class="card">
                <h2 class="card-title">1. Thông tin giao hàng</h2>
                
                <div class="form-group">
                    <label>Họ và tên người nhận <span class="required">*</span></label>
                    <input type="text" placeholder="Nhập họ và tên đầy đủ">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Số điện thoại <span class="required">*</span></label>
                        <input type="tel" placeholder="090xxxxxxx">
                    </div>
                    <div class="form-group">
                        <label>Email (Không bắt buộc)</label>
                        <input type="email" placeholder="Để nhận hóa đơn">
                    </div>
                </div>

                <div class="form-group">
                    <label>Địa chỉ nhận hàng <span class="required">*</span></label>
                    <textarea placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Ghi chú đơn hàng (Không bắt buộc)</label>
                    <textarea placeholder="Ví dụ: Giao hàng giờ hành chính..."></textarea>
                </div>
            </div>

            <!-- Phần 2: Phương thức giao hàng -->
            <div class="card">
                <h2 class="card-title">2. Phương thức giao hàng</h2>
                
                <label class="shipping-option">
                    <input type="radio" name="shipping" checked>
                    <div class="shipping-content">
                        <span class="shipping-name">
                            <i class="fas fa-truck"></i> Giao nhanh (2-3 ngày)
                        </span>
                        <span class="shipping-price">Miễn phí</span>
                    </div>
                </label>

                <label class="shipping-option">
                    <input type="radio" name="shipping">
                    <div class="shipping-content">
                        <span class="shipping-name">
                            <i class="fas fa-bolt"></i> Giao hỏa tốc (Trong 2h)
                        </span>
                        <span class="shipping-price">+30.000đ</span>
                    </div>
                </label>
            </div>
            
        </div>

        <!-- Cột Phải: Tóm tắt đơn hàng -->
        <div class="sidebar">
            <div class="card">
                <h2 class="card-title">Tóm tắt đơn hàng</h2>
                
                <div class="product-item">
                    <div class="product-img">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-name">Siro giúp bổ sung lợi khuẩn, tốt cho đường ruột Immune Defence Probiotic Liquid Brauer (45ml)</div>
                        <div class="product-meta">
                            <input type="number" class="qty-input" value="1" min="1">
                            <span class="product-price">440.000đ</span>
                        </div>
                    </div>
                </div>

                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span>440.000đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span>0đ</span>
                </div>

                <div class="summary-total">
                    <span>Tổng cộng</span>
                    <span class="total-price">440.000đ</span>
                </div>

                <button class="btn-submit">Xác nhận đặt hàng</button>

                <ul class="trust-badges">
                    <li><i class="fas fa-shield-halved"></i> Cam kết 100% chính hãng</li>
                    <li><i class="fas fa-lock"></i> Bảo mật thông tin SSL</li>
                    <li><i class="fas fa-rotate-left"></i> Đổi trả dễ dàng trong 30 ngày</li>
                </ul>
            </div>
        </div>
        
    </div>

</div>
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
</body>
</html>