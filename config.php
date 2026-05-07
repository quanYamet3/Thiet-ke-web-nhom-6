<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'web_van_phong_pham'); // Đổi tên database nếu cần
define('DB_CHARSET', 'utf8mb4');

// Kết nối
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset(DB_CHARSET);

if ($conn->connect_error) {
    die('Kết nối database thất bại: ' . $conn->connect_error);
}
// Chỉ lưu thông tin, chưa kết nối gì cả
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'web_van_phong_pham');

?>
