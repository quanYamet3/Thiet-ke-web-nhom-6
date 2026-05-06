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
    <link rel="stylesheet" href="CSS_AI.css">
</head>
<body>
  <?php include 'header.php'; ?>
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
<?php include 'footer.php'; ?>
<?php include 'chat_main.php'; ?>
</body>
</html>