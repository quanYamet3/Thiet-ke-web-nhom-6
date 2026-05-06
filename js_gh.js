// ═══════════════════════════════════════
//  GIỎ HÀNG - localStorage
// ═══════════════════════════════════════

function getCart() {
    return JSON.parse(localStorage.getItem('cart') || '[]');
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Thêm vào giỏ (mặc định qty = 1)
function addToCart(id, name, price, image, qty = 1) {
    let cart = getCart();
    let existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty += qty;
    } else {
        cart.push({ id, name, price, image, qty: qty, selected: true });
    }
    saveCart(cart);
    updateCartUI();
    showToast('✓ ' + name + ' đã được thêm vào giỏ!');
}

// Mua ngay - truyền qty từ trang chi tiết hoặc mặc định 1
function buyNow(id, name, price, image, qty = 1) {
    localStorage.setItem('buy_now_cart', JSON.stringify([
        { id, name, price, image, qty: qty, selected: true }
    ]));
    let path = window.location.pathname;
    if (path.includes('Trang_s')) {
        window.location.href = '../Thanh_toán.php';
    } else {
        window.location.href = 'Thanh_toán.php';
    }
}

// ═══════════════════════════════════════
//  RENDER GIỎ HÀNG DRAWER
// ═══════════════════════════════════════
function updateCartUI() {
    let cart = getCart();

    // Cập nhật badge
    let totalQty = cart.reduce((sum, i) => sum + i.qty, 0);
    let badge = document.getElementById('cartBadge');
    if (badge) badge.textContent = totalQty;

    let cartBody   = document.getElementById('cartBody');
    let cartFooter = document.getElementById('cartFooter');
    if (!cartBody) return;

    // Đảm bảo selected tồn tại
    cart.forEach(item => { if (item.selected === undefined) item.selected = true; });
    saveCart(cart);

    // Giỏ trống
    if (cart.length === 0) {
        cartBody.innerHTML = `
            <div style="text-align:center;padding:50px 20px;color:#aaa;">
                <div style="font-size:52px;margin-bottom:12px;">🛒</div>
                <p style="font-weight:600;color:#555;margin-bottom:6px;">Giỏ hàng trống</p>
                <p style="font-size:13px;">Hãy thêm sản phẩm vào giỏ hàng</p>
            </div>`;
        if (cartFooter) cartFooter.innerHTML = '';
        return;
    }

    let selectedItems = cart.filter(i => i.selected);
    let selectedTotal = selectedItems.reduce((s, i) => s + i.price * i.qty, 0);
    let allSelected   = cart.length > 0 && selectedItems.length === cart.length;

    // ── BODY ──
    let bodyHTML = `
    <div style="padding:10px 14px;border-bottom:1px solid #f0ebfa;
                display:flex;align-items:center;gap:8px;background:#faf9ff;">
        <input type="checkbox" id="selectAll" ${allSelected ? 'checked' : ''}
            onchange="toggleSelectAll(this.checked)"
            style="width:15px;height:15px;cursor:pointer;accent-color:#5c3290;">
        <label for="selectAll"
            style="font-size:13px;font-weight:600;color:#5c3290;cursor:pointer;margin:0;">
            Chọn tất cả (${cart.length})
        </label>
    </div>
    <ul style="list-style:none;padding:0;margin:0;">`;

    cart.forEach((item, idx) => {
        bodyHTML += `
        <li style="display:flex;gap:10px;padding:12px 14px;
                   border-bottom:1px solid #f5f5f5;
                   background:${item.selected ? '#fff' : '#fafafa'};
                   align-items:flex-start;">

            <input type="checkbox" ${item.selected ? 'checked' : ''}
                onchange="toggleSelectItem(${idx}, this.checked)"
                style="width:15px;height:15px;margin-top:4px;
                       cursor:pointer;accent-color:#5c3290;flex-shrink:0;">

            <img src="images/${item.image}"
                 onerror="this.onerror=null;this.src='../images/${item.image}'"
                 style="width:54px;height:54px;object-fit:contain;border-radius:8px;
                        border:1px solid #ece9f5;background:#fff;flex-shrink:0;">

            <div style="flex:1;min-width:0;">
                <div style="font-size:12px;font-weight:600;color:#1a1a2e;
                            line-height:1.4;margin-bottom:4px;
                            overflow:hidden;display:-webkit-box;
                            -webkit-line-clamp:2;-webkit-box-orient:vertical;">
                    ${item.name}
                </div>
                <div style="font-size:13px;color:#d8511c;font-weight:700;margin-bottom:8px;">
                    ${item.price.toLocaleString('vi-VN')}đ
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <button onclick="cartUpdateQty(${idx},-1)"
                        style="width:24px;height:24px;border:1.5px solid #ddd;
                               border-radius:6px;background:#f8f8f8;
                               font-size:15px;font-weight:700;cursor:pointer;
                               color:#5c3290;line-height:1;padding:0;">−</button>

                    <input type="number" value="${item.qty}" min="1"
                        onchange="cartSetQty(${idx}, this.value)"
                        style="width:38px;height:24px;text-align:center;
                               border:1.5px solid #ddd;border-radius:6px;
                               font-size:12px;font-weight:700;outline:none;padding:0;">

                    <button onclick="cartUpdateQty(${idx},1)"
                        style="width:24px;height:24px;border:1.5px solid #ddd;
                               border-radius:6px;background:#f8f8f8;
                               font-size:15px;font-weight:700;cursor:pointer;
                               color:#5c3290;line-height:1;padding:0;">+</button>

                    <span style="font-size:11px;color:#999;margin-left:2px;">
                        = ${(item.price * item.qty).toLocaleString('vi-VN')}đ
                    </span>
                </div>
            </div>

            <button onclick="cartRemove(${idx})"
                style="background:none;border:none;font-size:20px;
                       cursor:pointer;color:#ccc;flex-shrink:0;
                       padding:0 2px;line-height:1;"
                onmouseover="this.style.color='#dc2626'"
                onmouseout="this.style.color='#ccc'">×</button>
        </li>`;
    });

    bodyHTML += '</ul>';
    cartBody.innerHTML = bodyHTML;

    // ── FOOTER ──
    // Nếu không có #cartFooter thì tạo mới trong cartDrawer
    let drawerEl = document.getElementById('cartDrawer');
    if (!cartFooter && drawerEl) {
        cartFooter = document.createElement('div');
        cartFooter.id = 'cartFooter';
        drawerEl.appendChild(cartFooter);
    }

    if (cartFooter) {
        cartFooter.style.display = 'block';
        let canCheckout = selectedItems.length > 0;
        cartFooter.innerHTML = `
        <div style="padding:12px 14px;border-top:2px solid #f0ebfa;background:#fff;">
            <div style="display:flex;justify-content:space-between;
                        align-items:center;margin-bottom:4px;">
                <span style="font-size:13px;color:#666;">
                    Đã chọn (${selectedItems.length} sp):
                </span>
                <span style="font-size:17px;font-weight:800;color:#d8511c;">
                    ${selectedTotal.toLocaleString('vi-VN')}đ
                </span>
            </div>
            <div style="font-size:11px;color:#bbb;margin-bottom:10px;text-align:right;">
                Chưa bao gồm phí vận chuyển
            </div>
            <button onclick="cartCheckout()"
                ${canCheckout ? '' : 'disabled'}
                style="width:100%;padding:12px;border-radius:10px;border:none;
                       font-size:14px;font-weight:700;
                       cursor:${canCheckout ? 'pointer' : 'not-allowed'};
                       font-family:inherit;margin-bottom:8px;
                       background:${canCheckout
                           ? 'linear-gradient(135deg,#d8511c,#e8671c)'
                           : '#e0e0e0'};
                       color:${canCheckout ? '#fff' : '#aaa'};">
                ⚡ Thanh toán (${selectedItems.length} sản phẩm)
            </button>
            ${canCheckout ? `
            <button onclick="cartRemoveSelected()"
                style="width:100%;padding:8px;border-radius:10px;
                       background:#fff;color:#dc2626;
                       border:1.5px solid #fecaca;font-size:12px;
                       font-weight:600;cursor:pointer;font-family:inherit;">
                🗑 Xóa sản phẩm đã chọn
            </button>` : ''}
        </div>`;
    }
}

// ═══════════════════════════════════════
//  CÁC HÀM XỬ LÝ GIỎ HÀNG
// ═══════════════════════════════════════

function toggleSelectAll(checked) {
    let cart = getCart();
    cart.forEach(i => i.selected = checked);
    saveCart(cart);
    updateCartUI();
}

function toggleSelectItem(idx, checked) {
    let cart = getCart();
    cart[idx].selected = checked;
    saveCart(cart);
    updateCartUI();
}

function cartUpdateQty(idx, delta) {
    let cart = getCart();
    cart[idx].qty = Math.max(1, cart[idx].qty + delta);
    saveCart(cart);
    updateCartUI();
}

function cartSetQty(idx, val) {
    let cart = getCart();
    let q = parseInt(val) || 1;
    cart[idx].qty = Math.max(1, q);
    saveCart(cart);
    updateCartUI();
}

function cartRemove(idx) {
    let cart = getCart();
    let name = cart[idx].name;
    cart.splice(idx, 1);
    saveCart(cart);
    updateCartUI();
    showToast('Đã xóa: ' + name);
}

function cartRemoveSelected() {
    let cart = getCart().filter(i => !i.selected);
    saveCart(cart);
    updateCartUI();
    showToast('Đã xóa các sản phẩm được chọn');
}

function cartCheckout() {
    let selected = getCart().filter(i => i.selected);
    if (!selected.length) { showToast('Vui lòng chọn ít nhất 1 sản phẩm!'); return; }
    localStorage.setItem('buy_now_cart', JSON.stringify(selected));
    let path = window.location.pathname;
    if (path.includes('Trang_s')) {
        window.location.href = '../Thanh_toán.php';
    } else {
        window.location.href = 'Thanh_toán.php';
    }
}

function toggleCart() {
    let drawer  = document.getElementById('cartDrawer');
    let overlay = document.getElementById('cartOverlay');
    if (!drawer) return;
    let isOpen = drawer.classList.toggle('open');
    if (overlay) overlay.classList.toggle('open');
    if (isOpen) updateCartUI();
}

function showToast(msg) {
    let t = document.getElementById('_toast');
    if (!t) {
        t = document.createElement('div');
        t.id = '_toast';
        t.style.cssText = `position:fixed;bottom:28px;right:28px;z-index:99999;
            background:#5c3290;color:#fff;padding:12px 18px;border-radius:10px;
            font-size:14px;font-weight:500;box-shadow:0 4px 20px rgba(0,0,0,.2);
            max-width:300px;transition:opacity .3s;pointer-events:none;`;
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.style.opacity = '1';
    clearTimeout(t._tid);
    t._tid = setTimeout(() => t.style.opacity = '0', 2500);
}

document.addEventListener('DOMContentLoaded', updateCartUI);