<?php
// Dùng đường dẫn tuyệt đối để tránh lỗi relative path
include $_SERVER['DOCUMENT_ROOT'] . '/Thiet-ke-web-nhom-6-php/connect.php';

// Kiểm tra có keyword không
if (!isset($_GET['keyword']) || trim($_GET['keyword']) === '') {
    exit;
}

$keyword = '%' . $_GET['keyword'] . '%';

$stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
$stmt->bind_param("s", $keyword);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='padding:12px;color:#888;font-size:14px;'>Không tìm thấy sản phẩm nào.</p>";
} else {
    while ($row = $result->fetch_assoc()) {
        echo "
        <div style='padding:10px 12px;border-bottom:1px solid #f0f0f0;font-size:14px;'>
            <a href='sanpham-detail.php?id=" . $row['id'] . "' style='color:#1a1a1a;text-decoration:none;'>
                " . htmlspecialchars($row['name']) . "
            </a>
        </div>";
    }
}
?>