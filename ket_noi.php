<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "web_van_phong_pham"; 

// Kết nối
$conn = mysqli_connect($host, $user, $pass, $db);

// Kiểm tra
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Tiếng Việt
mysqli_set_charset($conn, "utf8mb4");
?>