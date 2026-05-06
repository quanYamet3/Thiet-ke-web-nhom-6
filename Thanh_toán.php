<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - Ink Corner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <link rel="stylesheet" href="CSS_Thanh_toán.css">
    <style>
        /* ── PAYMENT METHOD ── */
        .payment-option {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            border: 2px solid #ece9f5;
            border-radius: 12px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
        }
        .payment-option:hover { border-color: #5c3290; background: #faf9ff; }
        .payment-option input[type="radio"] {
            width: 18px; height: 18px;
            accent-color: #5c3290; cursor: pointer; flex-shrink: 0;
        }
        .payment-option.selected { border-color: #5c3290; background: #f5f0ff; }
        .payment-icon { font-size: 22px; }
        .payment-label { font-weight: 600; font-size: 15px; color: #1a1a2e; }
        .payment-desc { font-size: 13px; color: #888; margin-top: 2px; }

        /* ── QR BOX ── */
        #qr-box {
            display: none;
            background: linear-gradient(135deg, #f5f0ff, #fff);
            border: 2px dashed #5c3290;
            border-radius: 16px;
            padding: 24px;
            margin-top: 16px;
            text-align: center;
        }
        #qr-box h4 { color: #5c3290; font-size: 16px; margin-bottom: 16px; }
        .qr-img {
            width: 200px; height: 200px;
            border-radius: 12px;
            border: 3px solid #5c3290;
            margin: 0 auto 16px;
            display: block;
            background: #fff;
            padding: 8px;
        }
        .bank-info {
            background: #fff;
            border-radius: 10px;
            padding: 14px 20px;
            text-align: left;
            border: 1px solid #ece9f5;
            margin-top: 12px;
        }
        .bank-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
        }
        .bank-row:last-child { border: none; }
        .bank-row span:first-child { color: #888; }
        .bank-row strong { color: #1a1a2e; font-size: 15px; }
        .bank-note {
            margin-top: 12px;
            font-size: 13px;
            color: #d8511c;
            font-weight: 600;
        }

        /* ── PRODUCT ITEM in sidebar ── */
        .product-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .product-item:last-of-type { border-bottom: none; }
        .product-img {
            width: 56px; height: 56px;
            border-radius: 8px;
            border: 1px solid #ece9f5;
            background: #faf9ff;
            overflow: hidden;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .product-img img { width: 100%; height: 100%; object-fit: contain; }
        .product-info { flex: 1; min-width: 0; }
        .product-name { font-size: 13px; font-weight: 600; color: #1a1a2e; line-height: 1.4; margin-bottom: 4px; }
        .product-meta { display: flex; justify-content: space-between; align-items: center; }
        .product-meta span { font-size: 13px; color: #888; }
        .product-price { font-size: 14px; font-weight: 700; color: #d8511c !important; }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0 4px;
            border-top: 2px solid #f0ebfa;
            margin-top: 8px;
        }
        .summary-total span:first-child { font-size: 15px; font-weight: 700; }
        .total-price { font-size: 22px; font-weight: 800; color: #d8511c; }
    </style>
</head>
<body>
<header>
    <nav>
        <a href="#" class="nav-logo"><img src="logo.jpg" class="logo-img"></a>
        <div class="nav-links">
            <a href="#">Trang Chủ</a>
            <a href="#">Sản Phẩm</a>
            <a href="#">Giới Thiệu</a>
            <a href="#">Blog</a>
            <a href="#">Liên Hệ</a>
        </div>
        <div class="nav-right">
            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" placeholder="Tìm kiếm...">
            </div>
            <button class="btn-account">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Tài khoản
            </button>
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
    <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-header">
            <h3>Giỏ Hàng</h3>
            <button class="cart-close" onclick="toggleCart()">×</button>
        </div>
        <div class="cart-body" id="cartBody"></div>
        <div id="cartFooter"></div>
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
        <!-- CỘT TRÁI -->
        <div class="main-content">

            <!-- 1. Thông tin giao hàng -->
            <div class="card">
                <h2 class="card-title">1. Thông tin giao hàng</h2>
                <div class="form-group">
                    <label>Họ và tên người nhận <span class="required">*</span></label>
                    <input type="text" id="ho_ten" placeholder="Nhập họ và tên đầy đủ">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số điện thoại <span class="required">*</span></label>
                        <input type="tel" id="sdt" placeholder="090xxxxxxx">
                    </div>
                    <div class="form-group">
                        <label>Email (Không bắt buộc)</label>
                        <input type="email" id="email" placeholder="Để nhận hóa đơn">
                    </div>
                </div>
                <div class="form-group">
                    <label>Địa chỉ nhận hàng <span class="required">*</span></label>
                    <textarea id="dia_chi" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"></textarea>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Ghi chú đơn hàng (Không bắt buộc)</label>
                    <textarea id="ghi_chu" placeholder="Ví dụ: Giao hàng giờ hành chính..."></textarea>
                </div>
            </div>

            <!-- 2. Phương thức giao hàng -->
            <div class="card">
                <h2 class="card-title">2. Phương thức giao hàng</h2>
                <label class="shipping-option" onclick="setShipping(this, 'Giao nhanh', 0)">
                    <input type="radio" name="shipping" checked>
                    <div class="shipping-content">
                        <span class="shipping-name"><i class="fas fa-truck"></i> Giao nhanh (2-3 ngày)</span>
                        <span class="shipping-price">Miễn phí</span>
                    </div>
                </label>
                <label class="shipping-option" onclick="setShipping(this, 'Giao hỏa tốc', 30000)">
                    <input type="radio" name="shipping">
                    <div class="shipping-content">
                        <span class="shipping-name"><i class="fas fa-bolt"></i> Giao hỏa tốc (Trong 2h)</span>
                        <span class="shipping-price">+30.000đ</span>
                    </div>
                </label>
            </div>

            <!-- 3. Phương thức thanh toán -->
            <div class="card">
                <h2 class="card-title">3. Phương thức thanh toán</h2>

                <label class="payment-option selected" onclick="selectPayment(this, 'COD')">
                    <input type="radio" name="payment" checked>
                    <span class="payment-icon">💵</span>
                    <div>
                        <div class="payment-label">Thanh toán khi nhận hàng (COD)</div>
                        <div class="payment-desc">Trả tiền mặt khi nhận hàng tại nhà</div>
                    </div>
                </label>

                <label class="payment-option" onclick="selectPayment(this, 'Chuyển khoản')">
                    <input type="radio" name="payment">
                    <span class="payment-icon">🏦</span>
                    <div>
                        <div class="payment-label">Chuyển khoản ngân hàng</div>
                        <div class="payment-desc">Quét mã QR hoặc chuyển khoản thủ công</div>
                    </div>
                </label>

                <!-- QR Box - chỉ hiện khi chọn chuyển khoản -->
                <div id="qr-box">
                    <h4>📲 Quét mã QR để thanh toán</h4>
                    <img class="qr-img"
                         src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=INK+CORNER+STK:1234567890+MB+BANK"
                         alt="QR Thanh toán Ink Corner">
                    <div class="bank-info">
                        <div class="bank-row">
                            <span>Ngân hàng</span>
                            <strong>MB Bank</strong>
                        </div>
                        <div class="bank-row">
                            <span>Số tài khoản</span>
                            <strong>1234 5678 90</strong>
                        </div>
                        <div class="bank-row">
                            <span>Chủ tài khoản</span>
                            <strong>CÔNG TY INK CORNER</strong>
                        </div>
                        <div class="bank-row">
                            <span>Nội dung CK</span>
                            <strong id="ck-noidung">INK [Số điện thoại]</strong>
                        </div>
                    </div>
                    <div class="bank-note">
                        ⚠️ Vui lòng ghi đúng nội dung chuyển khoản để đơn hàng được xử lý nhanh!
                    </div>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: Tóm tắt đơn hàng -->
        <div class="sidebar">
            <div class="card" id="order-summary-card">
                <h2 class="card-title">Tóm tắt đơn hàng</h2>
                <!-- Sản phẩm sẽ được inject bởi JS -->
                <div id="product-list"></div>

                <div class="summary-row" style="margin-top:12px;">
                    <span>Tạm tính</span>
                    <span id="subtotal-display">0đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span id="shipping-display">0đ</span>
                </div>

                <div class="summary-total">
                    <span>Tổng cộng</span>
                    <span class="total-price" id="total-display">0đ</span>
                </div>

                <button class="btn-submit" onclick="submitOrder()">Xác nhận đặt hàng</button>

                <ul class="trust-badges">
                    <li><i class="fas fa-shield-halved"></i> Cam kết 100% chính hãng</li>
                    <li><i class="fas fa-lock"></i> Bảo mật thông tin SSL</li>
                    <li><i class="fas fa-rotate-left"></i> Đổi trả dễ dàng trong 30 ngày</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<footer style="background:#1e0a3c;color:#fff;width:100%;">
    <div class="footer-grid">
        <div class="footer-brand">
            <img src="logo.jpg" style="width:40px;margin-bottom:12px">
            <p>Chuyên cung cấp văn phòng phẩm chất lượng cao cho cá nhân và doanh nghiệp.</p>
        </div>
        <div class="footer-col">
            <h4>Cửa Hàng</h4>
            <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản phẩm</a>
            <a href="Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới thiệu</a>
            <a href="Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
            <a href="Liên_hệ.html">Liên hệ</a>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ</h4>
            <a href="#">Chính sách đổi trả</a>
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

<script src="js_gh.js"></script>
<script>
// ═══ BIẾN TOÀN CỤC ═══
let cartData   = [];
let subtotal   = 0;
let shippingFee = 0;
let paymentMethod = 'COD';

// ═══ LOAD GIỎ HÀNG ═══
document.addEventListener('DOMContentLoaded', function () {
    cartData = JSON.parse(localStorage.getItem('buy_now_cart') || '[]');
    if (!cartData.length) cartData = JSON.parse(localStorage.getItem('cart') || '[]');
    localStorage.removeItem('buy_now_cart');

    let productList = document.getElementById('product-list');
    subtotal = 0;

    if (cartData.length === 0) {
        productList.innerHTML = '<p style="color:#aaa;text-align:center;padding:20px;">Không có sản phẩm nào.</p>';
    } else {
        cartData.forEach(item => {
            subtotal += item.price * item.qty;
            let imgPath = `Trang_sản_phẩm/images/${item.image}`;
            productList.innerHTML += `
            <div class="product-item">
                <div class="product-img">
                    <img src="${imgPath}" onerror="this.src='images/${item.image}'"
                         alt="${item.name}">
                </div>
                <div class="product-info">
                    <div class="product-name">${item.name}</div>
                    <div class="product-meta">
                        <span>SL: ${item.qty}</span>
                        <span class="product-price">${(item.price * item.qty).toLocaleString('vi-VN')}đ</span>
                    </div>
                </div>
            </div>`;
        });
    }
    updateTotals();
});

function updateTotals() {
    let total = subtotal + shippingFee;
    document.getElementById('subtotal-display').textContent  = subtotal.toLocaleString('vi-VN') + 'đ';
    document.getElementById('shipping-display').textContent  = shippingFee > 0 ? '+' + shippingFee.toLocaleString('vi-VN') + 'đ' : 'Miễn phí';
    document.getElementById('total-display').textContent     = total.toLocaleString('vi-VN') + 'đ';
}

// ═══ GIAO HÀNG ═══
function setShipping(el, name, fee) {
    shippingFee = fee;
    updateTotals();
}

// ═══ THANH TOÁN ═══
function selectPayment(el, method) {
    paymentMethod = method;
    document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('qr-box').style.display = (method === 'Chuyển khoản') ? 'block' : 'none';
}

// ═══ XỬ LÝ ĐẶT HÀNG ═══
function submitOrder() {
    let ho_ten  = document.getElementById('ho_ten').value.trim();
    let sdt     = document.getElementById('sdt').value.trim();
    let dia_chi = document.getElementById('dia_chi').value.trim();

    if (!ho_ten) { alert('Vui lòng nhập họ tên!'); return; }
    if (!sdt)    { alert('Vui lòng nhập số điện thoại!'); return; }
    if (!dia_chi){ alert('Vui lòng nhập địa chỉ nhận hàng!'); return; }
    if (cartData.length === 0) { alert('Giỏ hàng trống!'); return; }

    let van_chuyen = document.querySelector('input[name="shipping"]:checked')
                       ?.closest('.shipping-option')
                       ?.querySelector('.shipping-name')?.textContent?.trim() || 'Giao nhanh';

    let orderData = {
        ho_ten,
        sdt,
        email:      document.getElementById('email').value.trim(),
        dia_chi,
        ghi_chu:    document.getElementById('ghi_chu').value.trim(),
        van_chuyen,
        thanh_toan: paymentMethod,
        tong_tien:  subtotal + shippingFee,
        items:      cartData
    };

    // Gửi lên backend PHP
    fetch('xu_ly_don_hang.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            // Xóa giỏ hàng
            localStorage.removeItem('cart');
            localStorage.removeItem('buy_now_cart');
            // Chuyển sang trang xác nhận
            window.location.href = 'xac_nhan_don_hang.php?order_id=' + res.order_id;
        } else {
            alert('Lỗi: ' + (res.message || 'Không thể đặt hàng!'));
        }
    })
    .catch(() => alert('Lỗi kết nối server!'));
}
</script>
</body>
</html>