<?php
// ============================================================
// functions.php — Các hàm dùng chung toàn project
// Nhóm 6 - iNK Store
// HƯỚNG DẪN: Đặt file này ở thư mục gốc, include sau config.php
// ============================================================
require_once 'config.php';


// ──────────────────────────────────────────────────────────
// NHÓM 1: HÀM SẢN PHẨM
// ──────────────────────────────────────────────────────────

/**
 * Lấy tất cả sản phẩm (có thể lọc theo danh mục)
 * Dùng cho: Tất_cả_sản_phẩm.html, trang chủ
 */
function getAllProducts($category_id = null, $limit = 50) {
    global $conn;
    if ($category_id) {
        $stmt = $conn->prepare(
            "SELECT p.*, c.name AS category_name 
             FROM products p 
             JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = ? 
             ORDER BY p.id ASC 
             LIMIT ?"
        );
        $stmt->bind_param("ii", $category_id, $limit);
    } else {
        $stmt = $conn->prepare(
            "SELECT p.*, c.name AS category_name 
             FROM products p 
             JOIN categories c ON p.category_id = c.id
             ORDER BY p.id ASC 
             LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy 1 sản phẩm theo ID
 * Dùng cho: trang chi tiết sản phẩm
 */
function getProductById($id) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT p.*, c.name AS category_name 
         FROM products p 
         JOIN categories c ON p.category_id = c.id
         WHERE p.id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Lấy tất cả danh mục
 * Dùng cho: sidebar trang sản phẩm
 */
function getAllCategories() {
    global $conn;
    $result = $conn->query("SELECT * FROM categories ORDER BY id ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}


// ──────────────────────────────────────────────────────────
// NHÓM 2: HÀM USER
// ──────────────────────────────────────────────────────────

/**
 * Lấy thông tin user theo ID
 * Dùng cho: User.php - hiển thị hồ sơ cá nhân
 */
function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT id, fullname, email, phone, gender, dob, address, avatar, role 
         FROM users WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Cập nhật thông tin cá nhân
 * Dùng cho: User.php - nút "LƯU THAY ĐỔI"
 */
function updateUser($id, $fullname, $phone, $gender, $dob, $address) {
    global $conn;
    $stmt = $conn->prepare(
        "UPDATE users 
         SET fullname=?, phone=?, gender=?, dob=?, address=? 
         WHERE id=?"
    );
    $stmt->bind_param("sssssi", $fullname, $phone, $gender, $dob, $address, $id);
    return $stmt->execute();
}

/**
 * Đăng ký tài khoản mới
 */
function registerUser($fullname, $email, $phone, $password) {
    global $conn;
    // Kiểm tra email đã tồn tại chưa
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Email đã được sử dụng'];
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare(
        "INSERT INTO users (fullname, email, phone, password_hash) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $fullname, $email, $phone, $hash);
    if ($stmt->execute()) {
        return ['success' => true, 'user_id' => $conn->insert_id];
    }
    return ['success' => false, 'message' => 'Đăng ký thất bại'];
}

/**
 * Đăng nhập
 */
function loginUser($email, $password) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT id, fullname, email, password_hash, role FROM users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password_hash'])) {
        unset($user['password_hash']); // Không trả về hash
        return ['success' => true, 'user' => $user];
    }
    return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'];
}


// ──────────────────────────────────────────────────────────
// NHÓM 3: HÀM GIỎ HÀNG
// ──────────────────────────────────────────────────────────

/**
 * Lấy giỏ hàng của user
 * Dùng cho: Giỏ_hàng.php
 */
function getCartByUser($user_id) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT c.id, c.quantity, p.id AS product_id, p.name, p.price, p.image,
                (c.quantity * p.price) AS subtotal
         FROM cart c
         JOIN products p ON c.product_id = p.id
         WHERE c.user_id = ?"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Thêm sản phẩm vào giỏ (hoặc tăng số lượng nếu đã có)
 * Dùng cho: nút "Thêm vào giỏ"
 */
function addToCart($user_id, $product_id, $quantity = 1) {
    global $conn;
    $stmt = $conn->prepare(
        "INSERT INTO cart (user_id, product_id, quantity) 
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE quantity = quantity + ?"
    );
    $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
    return $stmt->execute();
}

/**
 * Xóa sản phẩm khỏi giỏ
 */
function removeFromCart($user_id, $product_id) {
    global $conn;
    $stmt = $conn->prepare(
        "DELETE FROM cart WHERE user_id = ? AND product_id = ?"
    );
    $stmt->bind_param("ii", $user_id, $product_id);
    return $stmt->execute();
}

/**
 * Tính tổng tiền giỏ hàng
 */
function getCartTotal($user_id) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT SUM(c.quantity * p.price) AS total 
         FROM cart c JOIN products p ON c.product_id = p.id 
         WHERE c.user_id = ?"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row['total'] ?? 0;
}


// ──────────────────────────────────────────────────────────
// NHÓM 4: HÀM ĐƠN HÀNG
// ──────────────────────────────────────────────────────────

/**
 * Tạo đơn hàng mới
 * Dùng cho: Thanh_toán.php - nút "Xác nhận đặt hàng"
 */
function createOrder($data) {
    global $conn;
    $conn->begin_transaction();
    try {
        // Bước 1: Tạo đơn hàng
        $stmt = $conn->prepare(
            "INSERT INTO orders 
             (user_id, receiver_name, receiver_phone, receiver_email, address, note, shipping_method, shipping_fee, total_amount, payment_method)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "issssssdds",
            $data['user_id'], $data['receiver_name'], $data['receiver_phone'],
            $data['receiver_email'], $data['address'], $data['note'],
            $data['shipping_method'], $data['shipping_fee'],
            $data['total_amount'], $data['payment_method']
        );
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Bước 2: Thêm từng sản phẩm vào order_items
        foreach ($data['items'] as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $stmt2 = $conn->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt2->bind_param(
                "iiidd",
                $order_id, $item['product_id'], $item['quantity'],
                $item['unit_price'], $subtotal
            );
            $stmt2->execute();
        }

        // Bước 3: Xóa giỏ hàng sau khi đặt thành công
        if (!empty($data['user_id'])) {
            $del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $del->bind_param("i", $data['user_id']);
            $del->execute();
        }

        $conn->commit();
        return ['success' => true, 'order_id' => $order_id];

    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Lấy lịch sử đơn hàng của user
 */
function getOrdersByUser($user_id) {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT o.*, 
                COUNT(oi.id) AS total_items
         FROM orders o
         LEFT JOIN order_items oi ON o.id = oi.order_id
         WHERE o.user_id = ?
         GROUP BY o.id
         ORDER BY o.created_at DESC"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


// ──────────────────────────────────────────────────────────
// NHÓM 5: HÀM LIÊN HỆ
// ──────────────────────────────────────────────────────────

/**
 * Lưu tin nhắn liên hệ
 * Dùng cho: Liên_hệ.html - form gửi tin nhắn
 */
function saveContact($fullname, $phone, $order_id, $message) {
    global $conn;
    $stmt = $conn->prepare(
        "INSERT INTO contacts (fullname, phone, order_id, message) VALUES (?, ?, ?, ?)"
    );
    $order_id = $order_id ?: null;
    $stmt->bind_param("ssss", $fullname, $phone, $order_id, $message);
    return $stmt->execute();
}


// ──────────────────────────────────────────────────────────
// NHÓM 6: HÀM TIỆN ÍCH
// ──────────────────────────────────────────────────────────

/** Format giá tiền: 60000 → "60.000đ" */
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}

/** Lấy trạng thái đơn hàng bằng tiếng Việt */
function getStatusLabel($status) {
    $map = [
        'pending'   => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao',
        'delivered' => 'Đã giao',
        'cancelled' => 'Đã hủy',
    ];
    return $map[$status] ?? $status;
}
?>
