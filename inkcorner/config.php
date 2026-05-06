<?php
// ============================================================
// config.php — Kết nối Database
// Nhóm 6 - iNK Store
// HƯỚNG DẪN: Đặt file này ở thư mục gốc của project
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Tên user MySQL của bạn
define('DB_PASS', '');            // Mật khẩu MySQL (XAMPP mặc định để trống)
define('DB_NAME', 'data grp 6');
define('DB_CHARSET', 'utf8mb4');

// Tạo kết nối
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Đặt charset UTF-8 (hỗ trợ tiếng Việt)
$conn->set_charset(DB_CHARSET);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die(json_encode([
        'error' => true,
        'message' => 'Kết nối database thất bại: ' . $conn->connect_error
    ]));
}
?>
