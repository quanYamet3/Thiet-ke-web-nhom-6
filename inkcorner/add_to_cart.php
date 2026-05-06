<?php
// add_to_cart.php - Đặt file này ở thư mục GỐC (cùng chỗ với config.php)
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';

$input      = json_decode(file_get_contents('php://input'), true);
$product_id = intval($input['product_id'] ?? 0);
$quantity   = intval($input['quantity']   ?? 1);

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, price, stock FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
    exit;
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

$total_qty = array_sum($_SESSION['cart']);

echo json_encode([
    'success'    => true,
    'message'    => 'Đã thêm "' . $product['name'] . '" vào giỏ!',
    'cart_count' => $total_qty
]);
?>
