<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';

// ── Xử lý đặt hàng (POST) ─────────────────────────────────────────────────
$order_success = false;
$order_id      = null;
$order_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {

    $user_id        = $_SESSION['user_id'] ?? null;
    $name           = trim($_POST['name']           ?? '');
    $phone          = trim($_POST['phone']          ?? '');
    $address        = trim($_POST['address']        ?? '');
    $note           = trim($_POST['note']           ?? '');
    $payment_method = $_POST['payment_method']      ?? 'cod';
    $total          = (int)($_POST['total']         ?? 0);
    $items_json     = $_POST['items_json']          ?? '[]';

    if (!$name || !$phone || !$address) {
        $order_error = 'Vui lòng điền đầy đủ họ tên, số điện thoại và địa chỉ giao hàng!';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO orders (user_id, name, phone, address, note, payment_method, total, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())"
        );
        $stmt->bind_param("isssssi", $user_id, $name, $phone, $address, $note, $payment_method, $total);

        if ($stmt->execute()) {
            $order_id = $conn->insert_id;

            // Insert từng sản phẩm vào order_items
            $items = json_decode($items_json, true);
            if (is_array($items) && count($items) > 0) {
                $tbl = $conn->query("SHOW TABLES LIKE 'order_items'");
                if ($tbl && $tbl->num_rows > 0) {
                    $si = $conn->prepare(
                        "INSERT INTO order_items (order_id, product_id, product_name, price, qty)
                         VALUES (?, ?, ?, ?, ?)"
                    );
                    foreach ($items as $item) {
                        $pid    = (int)($item['id']    ?? 0);
                        $pname  = $item['name']        ?? '';
                        $pprice = (int)($item['price'] ?? 0);
                        $pqty   = (int)($item['qty']   ?? 1);
                        $si->bind_param("iisii", $order_id, $pid, $pname, $pprice, $pqty);
                        $si->execute();
                    }
                }
            }
            $order_success = true;
        } else {
            $order_error = 'Đã xảy ra lỗi khi lưu đơn hàng. Vui lòng thử lại!';
        }
    }
}

// ── Lấy thông tin user từ DB (đúng theo cấu trúc bảng users) ──────────────
$user_name    = '';
$user_phone   = '';
$user_address = '';

if (!empty($_SESSION['user_id'])) {
    $su = $conn->prepare("SELECT fullname, phone, address FROM users WHERE id = ?");
    $su->bind_param("i", $_SESSION['user_id']);
    $su->execute();
    $ur = $su->get_result()->fetch_assoc();
    if ($ur) {
        $user_name    = $ur['fullname'] ?? '';
        $user_phone   = $ur['phone']    ?? '';
        $user_address = $ur['address']  ?? '';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh Toán – Ink & Paper</title>
  <link rel="stylesheet" href="CSS_header.css">
  <link rel="stylesheet" href="CSS_footer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   BASE
══════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Lato', sans-serif;
  background: #f7f4fb;
  color: #1a1a2e;
  min-height: 100vh;
}

/* dot-grid texture */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: -1;
  background-image: radial-gradient(circle, rgba(92,50,144,.12) 1px, transparent 1px);
  background-size: 28px 28px;
}

/* ══════════════════════════════════════════
   PAGE HEADER STRIP
══════════════════════════════════════════ */
.page-strip {
  background: linear-gradient(135deg, #5c3290 0%, #7c3aed 100%);
  padding: 90px 0 36px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.page-strip::after {
  content: '';
  position: absolute; bottom: -1px; left: 0; right: 0; height: 40px;
  background: #f7f4fb;
  clip-path: ellipse(55% 100% at 50% 100%);
}
.page-strip h1 {
  font-family: 'Playfair Display', serif;
  font-size: 30px; color: #fff; font-weight: 600;
  letter-spacing: .5px;
}
.page-strip p { font-size: 13px; color: rgba(255,255,255,.75); margin-top: 6px; }

/* ══════════════════════════════════════════
   PROGRESS STEPS
══════════════════════════════════════════ */
.steps-bar {
  display: flex; align-items: center; justify-content: center;
  gap: 0; margin: 28px auto 0; max-width: 400px;
}
.step {
  display: flex; flex-direction: column; align-items: center; gap: 6px;
  flex: 1; position: relative;
}
.step:not(:last-child)::after {
  content: '';
  position: absolute; top: 15px; left: calc(50% + 16px);
  right: calc(-50% + 16px); height: 2px;
  background: rgba(255,255,255,.3);
}
.step.done::after { background: rgba(255,255,255,.7); }
.step-dot {
  width: 30px; height: 30px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,.5);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; color: rgba(255,255,255,.6);
  background: transparent; transition: .3s;
}
.step.active .step-dot { background: #fff; color: #5c3290; border-color: #fff; }
.step.done   .step-dot { background: rgba(255,255,255,.3); color: #fff; border-color: rgba(255,255,255,.7); }
.step-label { font-size: 11px; color: rgba(255,255,255,.6); white-space: nowrap; }
.step.active .step-label { color: #fff; font-weight: 700; }

/* ══════════════════════════════════════════
   LAYOUT
══════════════════════════════════════════ */
.checkout-wrap {
  max-width: 1060px; margin: 0 auto;
  padding: 36px 20px 72px;
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 24px;
  align-items: start;
}

/* ══════════════════════════════════════════
   CARD
══════════════════════════════════════════ */
.card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #ede7f6;
  box-shadow: 0 2px 16px rgba(92,50,144,.07);
  overflow: hidden;
  animation: fadeUp .4s ease both;
}
.card + .card { margin-top: 20px; }

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.card-head {
  padding: 18px 24px 14px;
  border-bottom: 1px solid #f3eefe;
  display: flex; align-items: center; gap: 12px;
}
.card-head-num {
  width: 28px; height: 28px; border-radius: 50%;
  background: linear-gradient(135deg,#5c3290,#7c3aed);
  color: #fff; font-size: 13px; font-weight: 700;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.card-head h2 {
  font-family: 'Playfair Display', serif;
  font-size: 16px; font-weight: 600; color: #1a1a2e;
}
.card-head .optional-tag {
  margin-left: auto; font-size: 11px; color: #a78bcc;
  background: #f3e8ff; padding: 2px 10px; border-radius: 20px;
}
.card-body { padding: 22px 24px; }

/* ══════════════════════════════════════════
   FORM FIELDS
══════════════════════════════════════════ */
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.field { display: flex; flex-direction: column; gap: 7px; margin-bottom: 16px; }
.field:last-child { margin-bottom: 0; }

.field label {
  font-size: 11.5px; font-weight: 700; color: #5c3290;
  text-transform: uppercase; letter-spacing: .7px;
  display: flex; align-items: center; gap: 5px;
}
.field label .req { color: #e05c2a; }

.input-wrap { position: relative; }

.input-wrap input,
.input-wrap textarea {
  width: 100%;
  padding: 11px 38px 11px 40px;
  border: 1.5px solid #ddd5f5;
  border-radius: 10px;
  font-family: 'Lato', sans-serif;
  font-size: 14px; color: #1a1a2e;
  background: #fdfbff;
  outline: none;
  transition: border-color .2s, box-shadow .2s, background .2s;
}
.input-wrap textarea {
  padding: 11px 14px; resize: none; min-height: 86px;
}
.input-wrap input:focus,
.input-wrap textarea:focus {
  border-color: #5c3290;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(92,50,144,.1);
}
/* auto-filled highlight */
.input-wrap input.prefilled {
  background: #f9f5ff;
  border-color: #c4a8e8;
}

.field-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  color: #b09dd4; pointer-events: none;
}
.edit-icon {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  color: #c8bae3; pointer-events: none; font-size: 13px;
}
.field-hint { font-size: 11.5px; color: #a78bcc; margin-top: 3px; }

/* autofill chip */
.autofill-chip {
  display: inline-flex; align-items: center; gap: 5px;
  background: #f0e8ff; color: #5c3290;
  font-size: 11px; font-weight: 600;
  padding: 2px 9px; border-radius: 20px;
  margin-bottom: 4px;
}

/* ══════════════════════════════════════════
   PAYMENT
══════════════════════════════════════════ */
.payment-list { display: flex; flex-direction: column; gap: 10px; }

.payment-opt {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 16px; border-radius: 12px;
  border: 1.5px solid #ddd5f5; cursor: pointer;
  transition: all .2s; position: relative;
  user-select: none;
}
.payment-opt:hover { border-color: #b49ddb; background: #fdfaff; }
.payment-opt.selected {
  border-color: #5c3290;
  background: linear-gradient(135deg, #faf5ff, #f5edff);
  box-shadow: 0 2px 12px rgba(92,50,144,.12);
}
.payment-opt input[type=radio] { display: none; }

.pay-icon {
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; flex-shrink: 0;
}
.pay-icon.cod  { background: #fff4e6; }
.pay-icon.bank { background: #e8f5e9; }

.pay-text strong { display: block; font-size: 14px; color: #1a1a2e; }
.pay-text span   { font-size: 12px; color: #a78bcc; margin-top: 2px; display: block; }

.radio-dot {
  width: 20px; height: 20px; border-radius: 50%;
  border: 2px solid #c4b0e0; margin-left: auto; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  transition: .2s;
}
.payment-opt.selected .radio-dot {
  border-color: #5c3290; background: #5c3290;
}
.payment-opt.selected .radio-dot::after {
  content: ''; width: 7px; height: 7px; background: #fff; border-radius: 50%;
}

/* bank transfer panel */
.bank-panel {
  display: none; margin-top: 12px;
  background: #f0fdf4; border: 1px solid #a7f3d0;
  border-radius: 12px; padding: 16px 18px;
  font-size: 13px; color: #065f46; line-height: 1.8;
}
.bank-panel.show { display: block; animation: fadeUp .25s ease; }
.bank-panel .bank-row { display: flex; gap: 8px; align-items: baseline; }
.bank-panel .bank-label { font-size: 12px; color: #059669; font-weight: 700; min-width: 130px; }
.bank-panel .bank-val   { font-weight: 600; color: #064e3b; }

/* ══════════════════════════════════════════
   ORDER SUMMARY COLUMN
══════════════════════════════════════════ */
.summary-col { position: sticky; top: 88px; }

.order-items-list { display: flex; flex-direction: column; }
.order-item {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 0; border-bottom: 1px solid #f3eefe;
}
.order-item:last-child { border-bottom: none; }

.order-item-img {
  width: 54px; height: 54px; object-fit: cover;
  border-radius: 8px; border: 1px solid #ede7f6; flex-shrink: 0;
}
.order-item-img-placeholder {
  width: 54px; height: 54px; border-radius: 8px;
  background: #f3e8ff; display: flex; align-items: center;
  justify-content: center; flex-shrink: 0;
  font-size: 20px;
}
.order-item-info  { flex: 1; min-width: 0; }
.order-item-name  { font-size: 13px; font-weight: 700; color: #1a1a2e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.order-item-qty   { font-size: 12px; color: #a78bcc; margin-top: 3px; }
.order-item-price { font-size: 13px; font-weight: 700; color: #5c3290; flex-shrink: 0; }

.order-empty {
  text-align: center; padding: 32px 0;
  color: #c4b0e0; font-size: 14px;
}

/* totals */
.totals { margin-top: 16px; padding-top: 14px; border-top: 1px solid #ede7f6; }
.total-row {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 13px; color: #64748b; padding: 5px 0;
}
.total-row .val { font-weight: 600; color: #1a1a2e; }
.total-row.grand {
  font-size: 18px; color: #1a1a2e; font-weight: 700;
  padding-top: 12px; margin-top: 6px;
  border-top: 2px dashed #ede7f6;
}
.total-row.grand .val { color: #5c3290; font-family: 'Playfair Display', serif; }

/* shipping badge */
.free-ship {
  display: inline-flex; align-items: center; gap: 5px;
  background: #d1fae5; color: #065f46; font-size: 11px; font-weight: 700;
  padding: 3px 10px; border-radius: 20px; margin-top: 4px;
}

/* ══════════════════════════════════════════
   PLACE ORDER BUTTON
══════════════════════════════════════════ */
.btn-place-order {
  width: 100%; margin-top: 18px; padding: 16px;
  background: linear-gradient(135deg, #5c3290, #7c3aed);
  color: #fff; border: none; border-radius: 12px;
  font-family: 'Playfair Display', serif;
  font-size: 17px; cursor: pointer; letter-spacing: .3px;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  transition: transform .15s, box-shadow .15s;
  box-shadow: 0 6px 22px rgba(92,50,144,.38);
  position: relative; overflow: hidden;
}
.btn-place-order::before {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,.08), transparent);
}
.btn-place-order:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(92,50,144,.45); }
.btn-place-order:active { transform: translateY(0); }
.btn-place-order:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* spinner */
.spinner {
  width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.4);
  border-top-color: #fff; border-radius: 50%;
  animation: spin .7s linear infinite; display: none;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* back link */
.back-link {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; color: #a78bcc; text-decoration: none;
  margin-bottom: 20px; transition: color .2s;
}
.back-link:hover { color: #5c3290; }

/* error */
.form-error {
  background: #fff0f0; color: #c0392b; padding: 12px 16px;
  border-radius: 10px; font-size: 13px; margin-bottom: 18px;
  border-left: 4px solid #e74c3c;
  animation: shake .35s ease;
}
@keyframes shake {
  0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)}
}

/* login prompt */
.login-prompt {
  background: #fff; border: 1.5px dashed #b49ddb;
  border-radius: 14px; padding: 16px 18px;
  display: flex; align-items: center; gap: 14px;
  margin-bottom: 18px; animation: fadeUp .4s ease;
}
.login-prompt p { font-size: 13px; color: #64748b; line-height: 1.6; }
.login-prompt a { color: #5c3290; font-weight: 700; }

/* secure badge */
.secure-note {
  display: flex; align-items: center; justify-content: center; gap: 6px;
  font-size: 12px; color: #a78bcc; margin-top: 12px;
}

/* ══════════════════════════════════════════
   SUCCESS POPUP OVERLAY
══════════════════════════════════════════ */
.popup-overlay {
  position: fixed; inset: 0; z-index: 99999;
  background: rgba(15, 8, 35, 0.65);
  backdrop-filter: blur(6px);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; animation: overlayIn .4s ease .1s forwards;
}
@keyframes overlayIn {
  to { opacity: 1; }
}

.popup-box {
  background: #fff; border-radius: 24px;
  padding: 48px 40px 36px;
  max-width: 420px; width: 90vw;
  text-align: center; position: relative;
  box-shadow: 0 24px 80px rgba(92,50,144,.35);
  transform: scale(.7) translateY(40px);
  animation: popBoxIn .55s cubic-bezier(.34,1.56,.64,1) .2s forwards;
}
@keyframes popBoxIn {
  to { transform: scale(1) translateY(0); }
}

/* confetti burst ring */
.popup-burst {
  width: 96px; height: 96px; margin: 0 auto 20px;
  position: relative; display: flex; align-items: center; justify-content: center;
}
.popup-burst-ring {
  position: absolute; inset: 0; border-radius: 50%;
  background: linear-gradient(135deg, #5c3290, #7c3aed);
  animation: ringPulse 1.8s ease infinite;
}
@keyframes ringPulse {
  0%,100% { box-shadow: 0 0 0 0 rgba(92,50,144,.5); }
  50%      { box-shadow: 0 0 0 16px rgba(92,50,144,0); }
}
.popup-burst-icon {
  position: relative; z-index: 1;
  font-size: 42px; line-height: 1;
  animation: iconBounce .6s cubic-bezier(.34,1.56,.64,1) .5s both;
}
@keyframes iconBounce {
  from { transform: scale(0) rotate(-30deg); }
  to   { transform: scale(1) rotate(0); }
}

/* confetti dots */
.confetti-dot {
  position: absolute; width: 8px; height: 8px; border-radius: 50%;
  animation: confettiFloat 1.2s ease forwards;
}
@keyframes confettiFloat {
  0%   { opacity:1; transform: translate(0,0) scale(1); }
  100% { opacity:0; transform: var(--tx) scale(0); }
}

.popup-box h2 {
  font-family: 'Playfair Display', serif;
  font-size: 24px; color: #1a1a2e;
  margin-bottom: 10px; line-height: 1.3;
  animation: fadeUp .4s ease .6s both;
}
.popup-box .popup-msg {
  font-size: 14px; color: #64748b; line-height: 1.75;
  margin-bottom: 18px;
  animation: fadeUp .4s ease .7s both;
}
.popup-order-chip {
  display: inline-block; background: #f3e8ff;
  color: #5c3290; padding: 7px 20px; border-radius: 20px;
  font-size: 13px; font-weight: 700; margin-bottom: 26px;
  animation: fadeUp .4s ease .75s both;
}
.popup-actions {
  display: flex; gap: 10px; justify-content: center;
  animation: fadeUp .4s ease .85s both;
}
.popup-btn {
  padding: 11px 22px; border-radius: 10px;
  font-size: 14px; font-weight: 700; cursor: pointer;
  text-decoration: none; border: none; transition: .2s;
  display: inline-block;
}
.popup-btn.primary {
  background: linear-gradient(135deg,#5c3290,#7c3aed);
  color: #fff;
  box-shadow: 0 4px 16px rgba(92,50,144,.35);
}
.popup-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(92,50,144,.4); }
.popup-btn.ghost  { background: #f3e8ff; color: #5c3290; }
.popup-btn.ghost:hover  { background: #ebe0ff; }

/* ══════════════════════════════════════════
   SUCCESS PAGE
══════════════════════════════════════════ */
.success-wrap {
  max-width: 520px; margin: 0 auto;
  padding: 50px 24px 80px; text-align: center;
  animation: fadeUp .5s ease both;
}
.success-ring {
  width: 90px; height: 90px; margin: 0 auto 24px;
  border-radius: 50%;
  background: linear-gradient(135deg,#5c3290,#7c3aed);
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 8px 32px rgba(92,50,144,.4);
  animation: popIn .5s cubic-bezier(.34,1.56,.64,1) .1s both;
}
@keyframes popIn {
  from { transform: scale(.3); opacity: 0; }
  to   { transform: scale(1);  opacity: 1; }
}
.success-ring svg { color: #fff; }

.success-wrap h1 {
  font-family: 'Playfair Display', serif;
  font-size: 28px; color: #1a1a2e; margin-bottom: 10px;
}
.success-wrap .sub { font-size: 15px; color: #64748b; line-height: 1.75; margin-bottom: 24px; }
.order-id-chip {
  display: inline-block; background: #f3e8ff;
  color: #5c3290; padding: 8px 22px; border-radius: 20px;
  font-size: 14px; font-weight: 700; margin-bottom: 28px;
  letter-spacing: .5px;
}

.success-details {
  background: #fff; border: 1px solid #ede7f6;
  border-radius: 14px; padding: 20px; margin-bottom: 28px;
  text-align: left;
}
.success-detail-row {
  display: flex; gap: 10px; padding: 8px 0;
  border-bottom: 1px solid #f3eefe; font-size: 13px;
}
.success-detail-row:last-child { border-bottom: none; }
.sdl { color: #a78bcc; font-weight: 700; min-width: 130px; }
.sdv { color: #1a1a2e; font-weight: 600; }

.success-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-action {
  padding: 12px 24px; border-radius: 10px;
  font-size: 14px; font-weight: 700; text-decoration: none;
  cursor: pointer; border: none; transition: .2s; display: inline-block;
}
.btn-action.primary { background: #5c3290; color: #fff; }
.btn-action.primary:hover { background: #4a2873; transform: translateY(-1px); }
.btn-action.ghost { background: #f3e8ff; color: #5c3290; }
.btn-action.ghost:hover { background: #ebe0ff; }

/* ══════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════ */
@media(max-width:768px) {
  .checkout-wrap { grid-template-columns: 1fr; padding-top: 24px; }
  .summary-col { position: static; order: -1; }
  .form-grid-2 { grid-template-columns: 1fr; }
  .page-strip { padding-top: 80px; }
}
</style>
</head>
<body>

<?php include 'header.php'; ?>

<?php if ($order_success): ?>
<!-- ══════════ POPUP CHÚC MỪNG ══════════ -->
<div class="popup-overlay" id="successPopup">
  <div class="popup-box" id="popupBox">
    <!-- confetti dots (JS sẽ thêm màu động) -->
    <div class="popup-burst">
      <div class="popup-burst-ring"></div>
      <div class="popup-burst-icon">🎉</div>
    </div>
    <h2>Chúc mừng bạn<br>đặt hàng thành công!</h2>
    <p class="popup-msg">
      Đơn hàng <strong>#<?= str_pad($order_id, 5, '0', STR_PAD_LEFT) ?></strong> của bạn đã được tiếp nhận.<br>
      Chúng mình sẽ liên hệ xác nhận sớm nhất có thể. 💜
    </p>
    <div class="popup-order-chip">📦 Mã đơn: #<?= str_pad($order_id, 5, '0', STR_PAD_LEFT) ?></div>
    <div class="popup-actions">
      <a href="index.php" class="popup-btn primary" onclick="closePopup()">Về trang chủ</a>
      <a href="sanpham.php" class="popup-btn ghost" onclick="closePopup()">Mua tiếp</a>
    </div>
  </div>
</div>

<!-- ══════════ TRANG XÁC NHẬN THÀNH CÔNG ══════════ -->
<div class="page-strip">
  <div class="steps-bar">
    <div class="step done"><div class="step-dot">✓</div><span class="step-label">Giỏ hàng</span></div>
    <div class="step done"><div class="step-dot">✓</div><span class="step-label">Thanh toán</span></div>
    <div class="step active"><div class="step-dot">3</div><span class="step-label">Xác nhận</span></div>
  </div>
  <h1 style="margin-top:20px;">Đặt hàng thành công!</h1>
</div>

<div class="success-wrap">
  <div class="success-ring">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="20 6 9 17 4 12"/>
    </svg>
  </div>
  <h1>Cảm ơn bạn!</h1>
  <p class="sub">Đơn hàng của bạn đã được tiếp nhận thành công.<br>Chúng mình sẽ liên hệ xác nhận trong thời gian sớm nhất.</p>
  <div class="order-id-chip">📦 Mã đơn hàng: #<?= str_pad($order_id, 5, '0', STR_PAD_LEFT) ?></div>

  <div class="success-details">
    <div class="success-detail-row">
      <span class="sdl">👤 Người nhận</span>
      <span class="sdv"><?= htmlspecialchars($_POST['name'] ?? '') ?></span>
    </div>
    <div class="success-detail-row">
      <span class="sdl">📞 Điện thoại</span>
      <span class="sdv"><?= htmlspecialchars($_POST['phone'] ?? '') ?></span>
    </div>
    <div class="success-detail-row">
      <span class="sdl">📍 Địa chỉ</span>
      <span class="sdv"><?= htmlspecialchars($_POST['address'] ?? '') ?></span>
    </div>
    <div class="success-detail-row">
      <span class="sdl">💳 Thanh toán</span>
      <span class="sdv"><?= ($_POST['payment_method'] ?? '') === 'bank' ? '🏦 Chuyển khoản ngân hàng' : '🚚 COD – Thanh toán khi nhận hàng' ?></span>
    </div>
  </div>

  <div class="success-actions">
    <a href="index.php" class="btn-action primary">← Về trang chủ</a>
    <a href="sanpham.php" class="btn-action ghost">Tiếp tục mua sắm</a>
  </div>
</div>

<script>
  // Xóa giỏ hàng sau khi đặt thành công
  localStorage.removeItem('ink_cart');
</script>

<?php else: ?>
<!-- ══════════ TRANG THANH TOÁN ══════════ -->
<div class="page-strip">
  <div class="steps-bar">
    <div class="step done"><div class="step-dot">✓</div><span class="step-label">Giỏ hàng</span></div>
    <div class="step active"><div class="step-dot">2</div><span class="step-label">Thanh toán</span></div>
    <div class="step"><div class="step-dot">3</div><span class="step-label">Xác nhận</span></div>
  </div>
  <h1 style="margin-top:20px;">Thanh Toán</h1>
  <p>Kiểm tra thông tin và hoàn tất đơn hàng của bạn</p>
</div>

<div class="checkout-wrap">

  <!-- ═══ CỘT TRÁI – FORM ═══ -->
  <div>
    <a href="sanpham.php" class="back-link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Quay lại mua sắm
    </a>

    <?php if (!isset($_SESSION['user_id'])): ?>
    <div class="login-prompt">
      <span style="font-size:26px;">👤</span>
      <p>Bạn chưa đăng nhập. <a href="?login=1">Đăng nhập ngay</a> để thông tin được điền tự động và dễ dàng theo dõi đơn hàng!</p>
    </div>
    <?php endif; ?>

    <?php if ($order_error): ?>
    <div class="form-error">⚠️ <?= htmlspecialchars($order_error) ?></div>
    <?php endif; ?>

    <form method="POST" id="checkoutForm" onsubmit="return prepareSubmit(event)">
      <input type="hidden" name="action" value="place_order">
      <input type="hidden" name="total" id="hiddenTotal" value="0">
      <input type="hidden" name="items_json" id="hiddenItems" value="[]">

      <!-- ─── THÔNG TIN NGƯỜI NHẬN ─── -->
      <div class="card">
        <div class="card-head">
          <div class="card-head-num">1</div>
          <h2>Thông tin người nhận</h2>
        </div>
        <div class="card-body">

          <?php if (!empty($user_name) || !empty($user_phone)): ?>
          <div style="margin-bottom:14px;">
            <span class="autofill-chip">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Tự động điền từ tài khoản
            </span>
            <p style="font-size:12px; color:#a78bcc; margin-top:3px;">Bạn có thể chỉnh sửa trực tiếp bên dưới nếu cần.</p>
          </div>
          <?php endif; ?>

          <div class="form-grid-2">
            <!-- Họ tên -->
            <div class="field">
              <label>Họ và tên <span class="req">*</span></label>
              <div class="input-wrap">
                <svg class="field-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input type="text" name="name" id="field_name"
                       value="<?= htmlspecialchars($user_name) ?>"
                       placeholder="Nhập họ và tên..."
                       class="<?= $user_name ? 'prefilled' : '' ?>" required
                       oninput="this.classList.remove('prefilled')">
                <span class="edit-icon">✎</span>
              </div>
            </div>
            <!-- Số điện thoại -->
            <div class="field">
              <label>Số điện thoại <span class="req">*</span></label>
              <div class="input-wrap">
                <svg class="field-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <input type="tel" name="phone" id="field_phone"
                       value="<?= htmlspecialchars($user_phone) ?>"
                       placeholder="Nhập số điện thoại..."
                       class="<?= $user_phone ? 'prefilled' : '' ?>" required
                       oninput="this.classList.remove('prefilled')">
                <span class="edit-icon">✎</span>
              </div>
            </div>
          </div>

          <!-- Địa chỉ -->
          <div class="field" style="margin-bottom:0;">
            <label>Địa chỉ giao hàng <span class="req">*</span></label>
            <div class="input-wrap">
              <svg class="field-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              <input type="text" name="address" id="field_address"
                     value="<?= htmlspecialchars($user_address) ?>"
                     placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành..."
                     class="<?= $user_address ? 'prefilled' : '' ?>" required
                     oninput="this.classList.remove('prefilled')">
              <span class="edit-icon">✎</span>
            </div>
            <div class="field-hint">Nhập đầy đủ địa chỉ để đảm bảo giao hàng chính xác</div>
          </div>

        </div>
      </div>

      <!-- ─── PHƯƠNG THỨC THANH TOÁN ─── -->
      <div class="card">
        <div class="card-head">
          <div class="card-head-num">2</div>
          <h2>Phương thức thanh toán</h2>
        </div>
        <div class="card-body">
          <div class="payment-list">
            <!-- COD -->
            <label class="payment-opt selected" id="opt-cod" onclick="selectPayment('cod')">
              <input type="radio" name="payment_method" value="cod" checked>
              <div class="pay-icon cod">🚚</div>
              <div class="pay-text">
                <strong>Thanh toán khi nhận hàng (COD)</strong>
                <span>Trả tiền mặt trực tiếp cho shipper khi nhận hàng</span>
              </div>
              <div class="radio-dot"></div>
            </label>

            <!-- Bank -->
            <label class="payment-opt" id="opt-bank" onclick="selectPayment('bank')">
              <input type="radio" name="payment_method" value="bank">
              <div class="pay-icon bank">🏦</div>
              <div class="pay-text">
                <strong>Chuyển khoản ngân hàng</strong>
                <span>Thanh toán trước qua tài khoản ngân hàng</span>
              </div>
              <div class="radio-dot"></div>
            </label>
          </div>

          <!-- Bank info -->
          <div class="bank-panel" id="bankPanel">
            <div class="bank-row"><span class="bank-label">🏦 Ngân hàng</span><span class="bank-val">Vietcombank (VCB)</span></div>
            <div class="bank-row"><span class="bank-label">💳 Số tài khoản</span><span class="bank-val">1234 5678 9012 345</span></div>
            <div class="bank-row"><span class="bank-label">👤 Chủ tài khoản</span><span class="bank-val">NGUYEN VAN A</span></div>
            <div class="bank-row" style="margin-top:6px; padding-top:6px; border-top:1px solid #6ee7b7;">
              <span class="bank-label">📝 Nội dung CK</span>
              <span class="bank-val" id="bankContent" style="color:#065f46;">INKPAPER [SĐT của bạn]</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── GHI CHÚ ─── -->
      <div class="card">
        <div class="card-head">
          <div class="card-head-num">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </div>
          <h2>Ghi chú đơn hàng</h2>
          <span class="optional-tag">Tuỳ chọn</span>
        </div>
        <div class="card-body">
          <div class="field" style="margin-bottom:0;">
            <div class="input-wrap">
              <textarea name="note" placeholder="Ghi chú cho shipper: thời gian giao hàng mong muốn, yêu cầu đặc biệt về đóng gói..."></textarea>
            </div>
          </div>
        </div>
      </div>

    </form>
  </div>

  <!-- ═══ CỘT PHẢI – TÓM TẮT ĐƠN HÀNG ═══ -->
  <div class="summary-col">
    <div class="card">
      <div class="card-head">
        <div class="card-head-num">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        </div>
        <h2>Đơn hàng của bạn</h2>
      </div>
      <div class="card-body">

        <!-- Danh sách sản phẩm (render bởi JS) -->
        <div class="order-items-list" id="orderItemsList">
          <div class="order-empty">Đang tải giỏ hàng...</div>
        </div>

        <!-- Tổng tiền -->
        <div class="totals">
          <div class="total-row">
            <span>Tạm tính</span>
            <span class="val" id="subtotalDisplay">0đ</span>
          </div>
          <div class="total-row">
            <span>Phí vận chuyển</span>
            <span class="val" style="color:#059669;">
              Miễn phí
              <span class="free-ship">🎉 Free ship</span>
            </span>
          </div>
          <div class="total-row grand">
            <span>Tổng cộng</span>
            <span class="val" id="grandTotalDisplay">0đ</span>
          </div>
        </div>

        <!-- Nút đặt hàng -->
        <button class="btn-place-order" id="btnPlaceOrder" onclick="document.getElementById('checkoutForm').requestSubmit()">
          <span id="btnText">Đặt hàng ngay</span>
          <span class="spinner" id="btnSpinner"></span>
          <svg id="btnArrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
        <div class="secure-note">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Thông tin được bảo mật tuyệt đối
        </div>

      </div>
    </div>
  </div>

</div><!-- end checkout-wrap -->

<?php endif; ?>

<?php include 'footer.php'; ?>

<script>
/* ══════════════════════════════════════════
   CART HELPERS (dùng chung với header.php)
══════════════════════════════════════════ */
function getCart() { return JSON.parse(localStorage.getItem('ink_cart') || '[]'); }
function fmtVND(n) { return new Intl.NumberFormat('vi-VN').format(n); }
function esc(s)    { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

/* ── Render order summary ── */
function renderSummary() {
  const cart  = getCart();
  const list  = document.getElementById('orderItemsList');
  const sub   = document.getElementById('subtotalDisplay');
  const grand = document.getElementById('grandTotalDisplay');
  if (!list) return;

  if (cart.length === 0) {
    list.innerHTML = `<div class="order-empty">🛒 Giỏ hàng trống!<br><a href="sanpham.php" style="color:#5c3290;font-weight:700;">Chọn sản phẩm ngay →</a></div>`;
    if (sub)   sub.textContent   = '0đ';
    if (grand) grand.textContent = '0đ';
    document.getElementById('hiddenTotal').value = 0;
    document.getElementById('hiddenItems').value = '[]';
    return;
  }

  let total = 0, html = '';
  cart.forEach(item => {
    total += item.price * item.qty;
    const imgSrc = item.image ? `images/${esc(item.image)}` : '';
    html += `
      <div class="order-item">
        ${imgSrc
          ? `<img src="${imgSrc}" class="order-item-img" alt="${esc(item.name)}" onerror="this.style.display='none'">`
          : `<div class="order-item-img-placeholder">📦</div>`
        }
        <div class="order-item-info">
          <div class="order-item-name">${esc(item.name)}</div>
          <div class="order-item-qty">SL: ${item.qty} × ${fmtVND(item.price)}đ</div>
        </div>
        <div class="order-item-price">${fmtVND(item.price * item.qty)}đ</div>
      </div>`;
  });

  list.innerHTML = html;
  if (sub)   sub.textContent   = fmtVND(total) + 'đ';
  if (grand) grand.textContent = fmtVND(total) + 'đ';

  // Đẩy vào hidden inputs để PHP nhận
  document.getElementById('hiddenTotal').value = total;
  document.getElementById('hiddenItems').value = JSON.stringify(cart);
}

/* ── Payment toggle ── */
function selectPayment(method) {
  document.querySelectorAll('.payment-opt').forEach(el => el.classList.remove('selected'));
  document.getElementById('opt-' + method).classList.add('selected');
  document.querySelector(`input[value="${method}"]`).checked = true;

  const bp = document.getElementById('bankPanel');
  if (method === 'bank') {
    bp.classList.add('show');
    // Cập nhật nội dung chuyển khoản theo SĐT
    const phone = document.getElementById('field_phone')?.value || 'SĐT của bạn';
    document.getElementById('bankContent').textContent = `INKPAPER ${phone}`;
  } else {
    bp.classList.remove('show');
  }
}

/* Tự động cập nhật nội dung CK khi nhập SĐT */
document.addEventListener('DOMContentLoaded', function() {
  const phoneInput = document.getElementById('field_phone');
  if (phoneInput) {
    phoneInput.addEventListener('input', function() {
      const bc = document.getElementById('bankContent');
      if (bc) bc.textContent = `INKPAPER ${this.value || 'SĐT của bạn'}`;
    });
  }
  renderSummary();
});

/* ── Success popup ── */
function closePopup() {
  const p = document.getElementById('successPopup');
  if (p) {
    p.style.transition = 'opacity .3s ease';
    p.style.opacity = '0';
    setTimeout(() => p.remove(), 320);
  }
}

function spawnConfetti() {
  const box   = document.getElementById('popupBox');
  if (!box) return;
  const colors = ['#5c3290','#f97316','#d8511c','#7c3aed','#fbbf24','#34d399','#f472b6'];
  const positions = [
    { top:'-10px', left:'20%',  tx:'translate(-30px,-60px)' },
    { top:'-10px', left:'50%',  tx:'translate(0,-70px)' },
    { top:'-10px', left:'80%',  tx:'translate(30px,-60px)' },
    { top:'20%',   left:'-10px',tx:'translate(-60px,-20px)' },
    { top:'20%',   right:'-10px',tx:'translate(60px,-20px)' },
    { top:'50%',   left:'-10px',tx:'translate(-70px,10px)' },
    { top:'50%',   right:'-10px',tx:'translate(70px,10px)' },
    { bottom:'10%',left:'15%',  tx:'translate(-40px,50px)' },
    { bottom:'10%',right:'15%', tx:'translate(40px,50px)' },
    { top:'35%',   left:'30%',  tx:'translate(-20px,-50px)' },
    { top:'35%',   right:'30%', tx:'translate(20px,-50px)' },
    { bottom:'20%',left:'50%',  tx:'translate(0,60px)' },
  ];
  positions.forEach((pos, i) => {
    const dot = document.createElement('div');
    dot.className = 'confetti-dot';
    dot.style.background = colors[i % colors.length];
    dot.style.setProperty('--tx', pos.tx);
    Object.assign(dot.style, pos);
    dot.style.animationDelay = (i * 60) + 'ms';
    box.appendChild(dot);
    setTimeout(() => dot.remove(), 1400);
  });
}

// Khi trang success load → show popup & spawn confetti
document.addEventListener('DOMContentLoaded', function() {
  const popup = document.getElementById('successPopup');
  if (popup) {
    // Đóng khi click overlay bên ngoài box
    popup.addEventListener('click', function(e) {
      if (e.target === popup) closePopup();
    });
    // Spawn confetti sau animation vào
    setTimeout(spawnConfetti, 400);
    setTimeout(spawnConfetti, 900);
  }
  // Xoá giỏ hàng
  localStorage.removeItem('ink_cart');
});

/* ── Submit handler ── */
function prepareSubmit(e) {
  const cart = getCart();
  if (cart.length === 0) {
    alert('Giỏ hàng của bạn đang trống! Vui lòng thêm sản phẩm trước khi đặt hàng.');
    e.preventDefault(); return false;
  }

  // Cập nhật hidden fields một lần nữa trước submit
  let total = cart.reduce((s, i) => s + i.price * i.qty, 0);
  document.getElementById('hiddenTotal').value = total;
  document.getElementById('hiddenItems').value = JSON.stringify(cart);

  // Loading state
  const btn = document.getElementById('btnPlaceOrder');
  btn.disabled = true;
  document.getElementById('btnText').textContent = 'Đang xử lý...';
  document.getElementById('btnSpinner').style.display = 'block';
  document.getElementById('btnArrow').style.display = 'none';
  return true;
}
</script>

</body>
</html>