<?php
// save_user.php - Lưu thông tin user vào database
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id  = intval($_POST['user_id']);
    $fullname = trim($_POST['fullname']);
    $phone    = trim($_POST['phone']);
    $gender   = $_POST['gender'];
    $dob      = $_POST['dob'];
    $address  = trim($_POST['address']);

    $stmt = $conn->prepare(
        "UPDATE users SET fullname=?, phone=?, gender=?, dob=?, address=? WHERE id=?"
    );
    $stmt->bind_param("sssssi", $fullname, $phone, $gender, $dob, $address, $user_id);
    
    if ($stmt->execute()) {
        // Lưu thành công → quay lại trang User với thông báo
        header("Location: User.php?saved=1");
    } else {
        header("Location: User.php?error=1");
    }
    exit;
}
?>
