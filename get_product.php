<?php
include 'config.php'; // Đảm bảo file này chứa $conn
header('Content-Type: application/json');

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Khớp tên cột: name, price, image, description, features...
    $sql = "SELECT id, name, price, image, description, features FROM products WHERE id = $id";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Not found']);
    }
}
exit;