<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS_San_Pham.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css"> 
    <link rel="stylesheet" href="CSS_AI.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <br>
    <br>
    <br>
    <div class="container main-content">
        
        <aside class="sidebar">
            <h3>DANH MỤC SẢN PHẨM</h3>
            <ul class="category-list">
                <li><a href="sanpham.php" class="active">Tất cả sản phẩm</a></li>
                <li><a href="Giấy&sổ.php">Giấy & Sổ</a></li>
                <li><a href="Bút_viết.php">Bút viết</a></li>
                <li><a href="Kẹp&Ghim.php">Kẹp & Ghim</a></li>
                <li><a href="Dụng_cụ.php">Dụng cụ</a></li>
                <li><a href="Lưu_trữ.php">Lưu trữ</a></li>
                <li><a href="Hộp_đựng.php">Hộp đựng</a></li>
                <li><a href="Khác.php">Các phụ kiện khác</a></li>
            </ul>
        </aside>

        <section class="product-area">
            <div class="breadcrumb">
                Trang chủ / Sản phẩm / <span>Tất cả sản phẩm</span>
            </div>
            
            <div class="product-grid">
            <?php
            // 1. Truy vấn lấy toàn bộ sản phẩm
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Định dạng tiền tệ (Ví dụ: 60000 -> 60.000)
                    $gia_dinh_dang = number_format($row["price"], 0, ',', '.');
                    
                    // BẮT ĐẦU HIỂN THỊ THẺ SẢN PHẨM
                    // onclick="openProductModal(...)": Khi bấm vào thẻ sẽ gọi hàm mở hộp thoại chi tiết
                    echo '<div class="product-card" onclick="openProductModal(' . $row["id"] . ')">';
                    
                    // Hiển thị ảnh (Lấy từ thư mục images/)
                    echo '    <div class="product-img-wrapper">';
                    echo '        <img src="images/' . $row["image"] . '" alt="' . $row["name"] . '" class="product-img">';
                    echo '    </div>';
                    
                    // Hiển thị tên và giá
                    echo '    <div class="product-title">' . $row["name"] . '</div>';
                    echo '    <div class="product-price">' . $gia_dinh_dang . 'đ</div>';
                    
                    // NÚT THÊM VÀO GIỎ HÀNG
                    // event.stopPropagation(): Cực kỳ quan trọng! 
                    // Lệnh này giúp trình duyệt hiểu rằng: "Chỉ thực hiện thêm vào giỏ, ĐỪNG mở hộp thoại chi tiết lên"
                    echo '    <button class="btn-add-cart" onclick="event.stopPropagation(); addToCart(' . $row["id"] . ')">';
                    echo '        Thêm vào giỏ';
                    echo '    </button>';
                    
                    echo '</div>';
                }
            } else {
                echo "<p>Cửa hàng hiện đang cập nhật sản phẩm...</p>";
            }
            ?>
        </div>
        </section>

    </div>
    <br>
    <br>
    <?php include 'footer.php'; ?>
    <?php include 'chat_main.php'; ?>
    <div id="productModal" class="product-modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeProductModal()">&times;</span>
        <div id="modalData">
            <p style="text-align:center;">Đang tải dữ liệu...</p>
        </div>
    </div>
</div>
<script> 
async function openProductModal(id) {
    const modal = document.getElementById('productModal');
    const modalData = document.getElementById('modalData');
    
    modal.style.display = "block"; // Hiện hộp thoại
    modalData.innerHTML = "Đang tải...";

    try {
        // Gửi yêu cầu đến backend để lấy thông tin 1 sản phẩm
        // Bạn có thể dùng chung file cart.php hoặc tạo file get_product.php
        const response = await fetch(`get_product.php?id=${id}`);
        const product = await response.json();

        if(product) {
            modalData.innerHTML = `
                <div style="display:flex; gap:30px;">
                    <div style="flex:1;">
                        <img src="images/${product.image}" style="width:100%; border-radius:10px;">
                    </div>
                    <div style="flex:1.5;">
                        <h2 style="color:#5c3290;">${product.name}</h2>
                        <p style="color:#d8511c; font-size:24px; font-weight:bold;">${new Intl.NumberFormat('vi-VN').format(product.price)}đ</p>
                        <hr style="margin:15px 0; border:0; border-top:1px solid #eee;">
                        <div style="font-size:14px; color:#666; max-height:250px; overflow-y:auto;">
                            <strong>Mô tả:</strong> <br> ${product.description} <br><br>
                            <strong>Đặc điểm:</strong> <br> ${product.features}
                        </div>
                        <button onclick="addToCart(${product.id})" style="margin-top:20px; width:100%; padding:15px; background:#5c3290; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:bold;">
                            THÊM VÀO GIỎ HÀNG
                        </button>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        modalData.innerHTML = "Lỗi tải dữ liệu sản phẩm.";
    }
}

function closeProductModal() {
    document.getElementById('productModal').style.display = "none";
}

// Đóng khi click ra ngoài hộp thoại
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target == modal) {
        closeProductModal();
    }
}
</script> 
</body>
</html>