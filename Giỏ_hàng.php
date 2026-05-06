<?php include 'ket_noi.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Ink Store</title>
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f4f9;
            margin: 0;
            padding-top: 80px;
        }

        .cart-page {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 20px;
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        /* ── BẢNG GIỎ HÀNG ── */
        .cart-main {
            flex: 1;
            background: #fff;
            border-radius: 16px;
            border: 1px solid #ece9f5;
            overflow: hidden;
        }

        .cart-main-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f0ebfa;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-main-header h2 {
            font-size: 18px;
            font-weight: 800;
            color: #5c3290;
            margin: 0;
        }

        /* Table */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th {
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 700;
            color: #888;
            text-align: left;
            border-bottom: 1px solid #f0ebfa;
            background: #faf9ff;
        }

        .cart-table th.center { text-align: center; }

        .cart-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f5f5f5;
            vertical-align: middle;
        }

        .cart-table tr:last-child td { border-bottom: none; }
        .cart-table tr:hover td { background: #fdfcff; }

        /* Checkbox */
        .cart-table input[type="checkbox"] {
            width: 16px; height: 16px;
            cursor: pointer;
            accent-color: #5c3290;
        }

        /* Ảnh + tên */
        .product-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-cell img {
            width: 64px; height: 64px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #ece9f5;
            background: #fff;
            flex-shrink: 0;
        }

        .product-cell-name {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a2e;
            line-height: 1.4;
        }

        .stock-tag {
            display: inline-block;
            margin-top: 4px;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
        }

        .stock-ok  { background:#dcfce7; color:#16a34a; }
        .stock-low { background:#ffedd5; color:#ea580c; }
        .stock-out { background:#fee2e2; color:#dc2626; }

        /* Giá */
        .price-cell {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a2e;
            white-space: nowrap;
        }

        /* Số lượng */
        .qty-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .qty-btn {
            width: 28px; height: 28px;
            border: 1.5px solid #ece9f5;
            border-radius: 6px;
            background: #f8f8f8;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            color: #5c3290;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }

        .qty-btn:hover { background: #f0ebfa; }
        .qty-btn:disabled { color: #ccc; cursor: not-allowed; }

        .qty-input {
            width: 44px; height: 28px;
            text-align: center;
            border: 1.5px solid #ece9f5;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            outline: none;
            color: #1a1a2e;
        }

        /* Thành tiền */
        .total-cell {
            font-size: 15px;
            font-weight: 800;
            color: #d8511c;
            white-space: nowrap;
        }

        /* Xóa */
        .remove-btn {
            background: none;
            border: 1.5px solid #fecaca;
            border-radius: 8px;
            width: 32px; height: 32px;
            cursor: pointer;
            color: #dc2626;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
            margin: 0 auto;
        }

        .remove-btn:hover {
            background: #fee2e2;
        }

        /* Empty */
        .cart-empty-full {
            text-align: center;
            padding: 60px 20px;
            color: #aaa;
        }

        /* ── SIDEBAR TỔNG ── */
        .cart-sidebar {
            width: 300px;
            flex-shrink: 0;
            position: sticky;
            top: 100px;
        }

        .summary-box {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #ece9f5;
            padding: 20px;
            margin-bottom: 16px;
        }

        .summary-box h3 {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a2e;
            margin: 0 0 16px 0;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0ebfa;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .summary-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 2px dashed #f0ebfa;
            margin-top: 8px;
        }

        .summary-total-row span:first-child {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .summary-total-row .big-price {
            font-size: 22px;
            font-weight: 800;
            color: #d8511c;
        }

        .btn-checkout-main {
            display: block;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #5c3290, #7c3aed);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            margin-top: 16px;
            transition: all 0.2s;
            font-family: inherit;
        }

        .btn-checkout-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(92,50,144,0.3);
        }

        .btn-checkout-main:disabled {
            background: #e0e0e0;
            color: #aaa;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .trust-list {
            list-style: none;
            padding: 0; margin: 0;
            font-size: 12px;
            color: #888;
        }

        .trust-list li {
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #f5f5f5;
        }

        .trust-list li:last-child { border: none; }
        .trust-list li span { font-size: 16px; }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <nav>
        <a href="#" class="nav-logo"><img src="logo.jpg" class="logo-img"></a>
        <div class="nav-links">
            <a href="Trang_chủ.php">Trang Chủ</a>
            <a href="Trang_sản_phẩm/Tất_cả_sản_phẩm.php">Sản Phẩm</a>
            <a href="Giới_thiệu_Khuyến_mãi/gioithieu.html">Giới Thiệu</a>
            <a href="Giới_thiệu_Khuyến_mãi/tintuc.html">Blog</a>
            <a href="Liên_hệ.html">Liên Hệ</a>
        </div>
        <div class="nav-right">
            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" placeholder="Tìm kiếm...">
            </div>
            <button class="btn-account">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Tài khoản
            </button>
            <button class="btn-cart" onclick="toggleCart()">
                CART
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <span class="cart-badge" id="cartBadge">0</span>
            </button>
        </div>
    </nav>
    <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-header">
            <h3>Giỏ Hàng</h3>
            <button class="cart-close" onclick="toggleCart()">×</button>
        </div>
        <div class="cart-body" id="cartBody"></div>
        <div id="cartFooter"></div>
    </div>
</header>

<!-- NỘI DUNG TRANG GIỎ HÀNG -->
<div class="cart-page">

    <!-- BẢNG TRÁI -->
    <div class="cart-main">
        <div class="cart-main-header">
            <span style="font-size:22px;">🛒</span>
            <h2>Giỏ hàng của bạn</h2>