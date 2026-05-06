<?php
include 'ket_noi.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Lấy tồn kho nhiều sản phẩm cùng lúc
if ($action === 'check_stock') {
    $ids = json_decode(file_get_contents('php://input'), true)['ids'] ?? [];
    if (empty($ids)) { echo json_encode([]); exit; }
    
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT id, stock, name FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stocks = [];
    while ($row = $result->fetch_assoc()) {
        $stocks[$row['id']] = [
            'stock' => (int)$row['stock'],
            'name'  => $row['name']
        ];
    }
    echo json_encode($stocks);
    exit;
}

echo json_encode(['error' => 'Invalid action']);