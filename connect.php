<?php 
// Nhúng cuốn sổ tay config vào
require_once 'config.php'; 

// Bắt đầu thực thi kết nối
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Kiểm tra xem kết nối có thành công không
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}
mysqli_set_charset($conn, "utf8mb4");
?>