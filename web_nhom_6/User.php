<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - InkCorner</title>
    <link rel="stylesheet" href="User.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
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
<br>
<br>
<br>
    <div class="profile-card">
        
        <!-- Khu vực ảnh đại diện -->
        <div class="profile-sidebar">
            <div class="avatar-preview">
                A
                <!-- Khi có ảnh thật sẽ dùng thẻ img dưới đây -->
                <!-- <img src="link-anh.jpg" alt="Avatar"> -->
            </div>
            <button class="btn-upload">Chọn Ảnh</button>
            <div class="avatar-note">
                Dụng lượng file tối đa 1 MB<br>
                Định dạng: .JPEG, .PNG
            </div>
        </div>

        <!-- Khu vực form nhập liệu -->
        <div class="profile-content">
            <h2>Hồ Sơ Của Tôi</h2>
            <p>Quản lý thông tin hồ sơ để bảo mật tài khoản</p>

            <form action="#" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullname">Họ và tên</label>
                        <input type="text" id="fullname" value="Nguyễn Văn A">
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" value="0913200206">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="email">Email đăng nhập</label>
                    <input type="email" id="email" value="inkcorner.contact@gmail.com" disabled>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="gender">Giới tính</label>
                        <select id="gender">
                            <option value="male" selected>Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dob">Ngày sinh</label>
                        <input type="date" id="dob" value="1995-01-01">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="address">Địa chỉ giao hàng mặc định</label>
                    <input type="text" id="address" value="29 đường DD11, Quận 12, Thành phố Hồ Chí Minh">
                </div>

                <div class="action-buttons">
                    <button type="button" class="btn-save">LƯU THAY ĐỔI</button>
                    <button type="button" class="btn-cancel">Hủy</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
    <div class="footer-grid">
      <div class="footer-brand">
        <a class="nav-logo">I<span style="color:var(--ink-crimson)">N</span>K</a>
        <p>Chuyên cung cấp văn phòng phẩm chất lượng cao cho cá nhân và doanh nghiệp. Chất lượng là cam kết của chúng tôi.</p>
      </div>
      <div class="footer-col">
        <h4>Cửa Hàng</h4>
        <a href="#">Sản Phẩm</a>
        <a href="#">Giới Thiệu</a>
        <a href="#">Blog</a>
        <a href="#">Liên Hệ</a>
      </div>
      <div class="footer-col">
        <h4>Hỗ trợ</h4>
        <a href="#">Chính sách Đổi Trả</a>
        <a href="#">Hướng Dẫn Mua Hàng</a>
        <a href="#">Phương Thức Thanh Toán</a>
        <a href="#">Chính Sách Bảo Mật</a>
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