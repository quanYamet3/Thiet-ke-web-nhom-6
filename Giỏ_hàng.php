<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn - InkCorner</title>
    <link rel="stylesheet" href="CSS_Giỏ_hàng.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <link rel="stylesheet" href="CSS_AI.css">
</head>
<body>
<?php include 'header.php'; ?>
<section class="product-area">
            <div class="breadcrumb">
                Trang chủ / <span>Giỏ hàng</span>
            </div>
    <div class="cart-wrapper">
        
        <div class="cart-header">
            <h1>Giỏ hàng của bạn</h1>
            <a href="Trang_chủ.php" class="continue-shopping">« Tiếp tục mua sắm</a>
        </div>

        <div class="cart-layout">
            
            <!-- Danh sách sản phẩm -->
            <div class="cart-items-section">
                
                <!-- Sản phẩm TEST -->
                <div class="cart-item">
                    <div class="item-img">[Ảnh 1]</div>
                    <div class="item-details">
                        <div class="item-title">Sổ Tay Bìa Da Cao Cấp A5</div>
                        <div class="item-variant">Màu sắc: Tím | Kẻ ngang</div>
                        <div class="item-price">45.000đ</div>
                    </div>
                    <div class="quantity-control">
                        <button class="quantity-btn">-</button>
                        <input type="text" class="quantity-input" value="2" readonly>
                        <button class="quantity-btn">+</button>
                    </div>
                    <div class="item-total">90.000đ</div>
                    <button class="btn-remove" title="Xóa sản phẩm">×</button>
                </div>

                <!-- Sản phẩm 2 -->
                <div class="cart-item">
                    <div class="item-img">[Ảnh 2]</div>
                    <div class="item-details">
                        <div class="item-title">Bút Gel Deli Mực Đen 0.5mm</div>
                        <div class="item-variant">Hộp 12 chiếc</div>
                        <div class="item-price">60.000đ</div>
                    </div>
                    <div class="quantity-control">
                        <button class="quantity-btn">-</button>
                        <input type="text" class="quantity-input" value="1" readonly>
                        <button class="quantity-btn">+</button>
                    </div>
                    <div class="item-total">60.000đ</div>
                    <button class="btn-remove" title="Xóa sản phẩm">×</button>
                </div>

            </div>

            <!-- Tóm tắt đơn hàng (Thanh toán) -->
            <div class="cart-summary-section">
                <div class="summary-title">Thông tin đơn hàng</div>
                
                <div class="summary-row">
                    <span>Tạm tính (3 sản phẩm):</span>
                    <span>150.000đ</span>
                </div>
                
                <div class="summary-row">
                    <span>Phí giao hàng:</span>
                    <span>Chưa tính</span>
                </div>

                

                <div class="summary-total">
                    <span>Tổng cộng:</span>
                    <span class="price">150.000đ</span>
                </div>
                <p style="font-size: 12px; color: #888; text-align: right; margin-top: 5px;">(Đã bao gồm VAT nếu có)</p>

                <button class="btn-checkout" onclick="document.location='Thanh_toán.php'">Tiến hành thanh toán</button>
            </div>

        </div>

    </div>
<br>
<br>
<br>
<?php include 'footer.php'; ?>
<?php include 'chat_main.php'; ?>
</body>
</html>