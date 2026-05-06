<?php
include 'ket_noi.php';
header('Content-Type: application/json');

// Nhận dữ liệu JSON từ frontend
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Không nhận được dữ liệu!']);
    exit;
}

// Lấy thông tin đơn hàng
$ho_ten     = mysqli_real_escape_string($conn, $data['ho_ten']);
$sdt        = mysqli_real_escape_string($conn, $data['sdt']);
$email      = mysqli_real_escape_string($conn, $data['email'] ?? '');
$dia_chi    = mysqli_real_escape_string($conn, $data['dia_chi']);
$ghi_chu    = mysqli_real_escape_string($conn, $data['ghi_chu'] ?? '');
$van_chuyen = mysqli_real_escape_string($conn, $data['van_chuyen']);
$thanh_toan = mysqli_real_escape_string($conn, $data['thanh_toan']);
$tong_tien  = (int)$data['tong_tien'];
$items      = $data['items'];

// Bắt đầu transaction để đảm bảo toàn vẹn dữ liệu
mysqli_begin_transaction($conn);

try {
    // 1. Insert vào bảng orders
    $sql_order = "INSERT INTO orders 
        (ho_ten, sdt, email, dia_chi, ghi_chu, van_chuyen, thanh_toan, tong_tien, trang_thai)
        VALUES 
        ('$ho_ten', '$sdt', '$email', '$dia_chi', '$ghi_chu', '$van_chuyen', '$thanh_toan', $tong_tien, 'Chờ xác nhận')";
    
    mysqli_query($conn, $sql_order);
    $order_id = mysqli_insert_id($conn);

    if (!$order_id) throw new Exception('Không thể tạo đơn hàng!');

    // 2. Insert từng sản phẩm vào order_items + cập nhật tồn kho
    foreach ($items as $item) {
        $product_id = (int)$item['id'];
        $ten_sp     = mysqli_real_escape_string($conn, $item['name']);
        $gia        = (int)$item['price'];
        $so_luong   = (int)$item['qty'];

        // Insert order_items
        $sql_item = "INSERT INTO order_items (order_id, product_id, ten_sp, gia, so_luong)
                     VALUES ($order_id, $product_id, '$ten_sp', $gia, $so_luong)";
        mysqli_query($conn, $sql_item);

        // Cập nhật tồn kho: trừ đi số lượng đã mua
        $sql_stock = "UPDATE products 
                      SET stock = stock - $so_luong 
                      WHERE id = $product_id AND stock >= $so_luong";
        $result = mysqli_query($conn, $sql_stock);
        
        // Kiểm tra nếu không update được (hết hàng)
        if (mysqli_affected_rows($conn) === 0) {
            throw new Exception("Sản phẩm '$ten_sp' không đủ số lượng tồn kho!");
        }
    }

    // Commit transaction
    mysqli_commit($conn);

    echo json_encode([
        'success'  => true,
        'order_id' => $order_id,
        'message'  => 'Đặt hàng thành công!'
    ]);

} catch (Exception $e) {
    // Rollback nếu có lỗi
    mysqli_rollback($conn);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>