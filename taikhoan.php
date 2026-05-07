<?php
session_start();
require_once 'config.php';

// Chưa đăng nhập → về trang chủ
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?login=1");
    exit;
}

$user_id = $_SESSION['user_id'];
$tab = $_GET['tab'] ?? 'profile';

// Lấy thông tin user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Lưu hồ sơ
$save_ok = false;
$save_err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $fullname = trim($_POST['fullname']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);
    $gender   = $_POST['gender'] ?? '';
    $dob      = $_POST['dob'] ?? '';
    $upd = $conn->prepare("UPDATE users SET fullname=?, phone=?, address=?, gender=?, dob=? WHERE id=?");
    $upd->bind_param("sssssi", $fullname, $phone, $address, $gender, $dob, $user_id);
    if ($upd->execute()) {
        $_SESSION['user_name'] = $fullname;
        $save_ok = true;
        // Reload user
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $save_err = 'Lưu thất bại!';
    }
}

// Lấy lịch sử đơn hàng
$orders = [];
$res = $conn->query("SELECT * FROM orders WHERE email = '{$conn->real_escape_string($user['email'])}' ORDER BY ngay_dat DESC");
if ($res) $orders = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản - iNK Store</title>
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <link rel="stylesheet" href="CSS_AI.css">
    <style>
        :root { --ink-purple:#5c3290; --ink-orange:#d8511c; --ink-border:#E9D5FF; --ink-light:#FAF5FF; --ink-dark:#1E0A2E; }
        body { background:#f8f9fa; font-family: Segoe UI, sans-serif; }
        .account-page { max-width:900px; margin:100px auto 60px; padding:0 20px; }

        /* Tabs */
        .acc-tabs { display:flex; gap:0; background:#fff; border-radius:12px 12px 0 0; border:1px solid var(--ink-border); border-bottom:none; overflow:hidden; }
        .acc-tab { flex:1; padding:14px; text-align:center; font-weight:600; font-size:14px; cursor:pointer; color:#888; border-bottom:3px solid transparent; transition:.2s; }
        .acc-tab.active { color:var(--ink-purple); border-bottom-color:var(--ink-purple); background:var(--ink-light); }

        /* Card */
        .acc-card { background:#fff; border:1px solid var(--ink-border); border-radius:0 0 12px 12px; padding:36px; }

        /* Profile */
        .profile-wrap { display:flex; gap:40px; align-items:flex-start; }
        .avatar-area { text-align:center; width:180px; flex-shrink:0; }
        .avatar-circle { width:100px; height:100px; border-radius:50%; background:var(--ink-purple); color:#fff; font-size:36px; font-weight:700; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; }
        .profile-form { flex:1; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:13px; font-weight:600; color:#444; margin-bottom:6px; }
        .form-group input, .form-group select { width:100%; padding:11px 14px; border:1.5px solid #e0e0e0; border-radius:8px; font-size:14px; outline:none; }
        .form-group input:focus { border-color:var(--ink-purple); }
        .form-group input:disabled { background:#f5f5f5; color:#888; }
        .btn-save { padding:12px 32px; background:var(--ink-orange); color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:bold; cursor:pointer; }
        .btn-save:hover { background:#b94215; }
        .alert-ok  { background:#f0fff4; color:#1a7a3c; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px; border:1px solid #b2dfdb; }
        .alert-err { background:#fff0f0; color:#c0392b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px; }

        /* Orders */
        .order-card { border:1px solid var(--ink-border); border-radius:10px; padding:20px; margin-bottom:16px; }
        .order-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .order-id { font-weight:700; color:var(--ink-purple); }
        .order-date { font-size:13px; color:#888; }
        .order-status { padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700; background:#f0fff4; color:#1a7a3c; }
        .order-total { font-size:18px; font-weight:700; color:var(--ink-orange); }
        .empty-state { text-align:center; padding:60px 20px; color:#888; }
        .empty-state div { font-size:48px; margin-bottom:16px; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="account-page">
    <div class="acc-tabs">
        <div class="acc-tab <?= $tab==='profile'?'active':'' ?>" onclick="window.location='taikhoan.php?tab=profile'">👤 Hồ sơ của tôi</div>
        <div class="acc-tab <?= $tab==='orders'?'active':'' ?>"  onclick="window.location='taikhoan.php?tab=orders'">📦 Lịch sử mua hàng</div>
    </div>

    <div class="acc-card">
        <?php if ($tab === 'profile'): ?>
        <!-- HỒ SƠ -->
        <?php if ($save_ok): ?><div class="alert-ok">✅ Đã lưu thành công!</div><?php endif; ?>
        <?php if ($save_err): ?><div class="alert-err">⚠️ <?= $save_err ?></div><?php endif; ?>
        <div class="profile-wrap">
            <div class="avatar-area">
                <div class="avatar-circle"><?= mb_strtoupper(mb_substr($user['fullname'],0,1)) ?></div>
                <div style="font-weight:700;font-size:15px;"><?= htmlspecialchars($user['fullname']) ?></div>
                <div style="font-size:12px;color:#888;margin-top:4px;"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <div class="profile-form">
                <h2 style="color:var(--ink-purple);margin-bottom:20px;">Hồ Sơ Của Tôi</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email đăng nhập</label>
                        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Giới tính</label>
                            <select name="gender">
                                <option value="Nam" <?= ($user['gender']??'')==='Nam'?'selected':'' ?>>Nam</option>
                                <option value="Nữ" <?= ($user['gender']??'')==='Nữ'?'selected':'' ?>>Nữ</option>
                                <option value="Khác" <?= ($user['gender']??'')==='Khác'?'selected':'' ?>>Khác</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="date" name="dob" value="<?= $user['dob'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ giao hàng mặc định</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Nhập địa chỉ...">
                    </div>
                    <button type="submit" name="save_profile" class="btn-save">LƯU THAY ĐỔI</button>
                </form>
            </div>
        </div>

        <?php else: ?>
        <!-- LỊCH SỬ MUA HÀNG -->
        <h2 style="color:var(--ink-purple);margin-bottom:24px;">Lịch Sử Mua Hàng</h2>
        <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div>📦</div>
            <p style="font-weight:600;margin-bottom:8px;">Bạn chưa có đơn hàng nào</p>
            <a href="sanpham.php" style="color:var(--ink-purple);font-weight:600;">Mua sắm ngay →</a>
        </div>
        <?php else: ?>
        <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-head">
                <div>
                    <div class="order-id">Đơn #<?= $order['id'] ?></div>
                    <div class="order-date"><?= $order['ngay_dat'] ?></div>
                </div>
                <div class="order-status"><?= $order['trang_thai'] ?? 'Đang xử lý' ?></div>
            </div>
            <div style="font-size:14px;color:#555;margin-bottom:8px;">
                📍 <?= htmlspecialchars($order['dia_chi']) ?>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:#888;">
                    <?= $order['van_chuyen'] === 'hoa-toc' ? '⚡ Giao hỏa tốc' : '🚚 Giao nhanh' ?>
                    · <?= $order['thanh_toan'] ?? 'COD' ?>
                </span>
                <div class="order-total"><?= number_format($order['tong_tien'],0,',','.') ?>đ</div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php include 'chat_main.php'; ?>
</body>
</html>
