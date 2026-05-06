<?php
// search_api.php - API tìm kiếm sản phẩm live
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) { echo '[]'; exit; }

$kw = '%' . $q . '%';
$stmt = $conn->prepare(
    "SELECT id, name, price FROM products WHERE name LIKE ? ORDER BY id LIMIT 6"
);
$stmt->bind_param("s", $kw);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$result = array_map(function($p) {
    return [
        'id'        => $p['id'],
        'name'      => $p['name'],
        'price_fmt' => number_format($p['price'], 0, ',', '.') . 'đ'
    ];
}, $rows);

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
