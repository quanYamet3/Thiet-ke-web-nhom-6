<!--                    AI CHAT                      -->
  <button class="ai-toggle" onclick="toggleAI()">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
  </button>

  <div class="ai-box" id="aiBox">
    <div class="ai-head">
      <div class="ai-head-info">
        <strong class="ai-title">INK Assistant</strong>
        <span class="ai-status">Đang hoạt động</span>
      </div>
      <button class="ai-close" onclick="toggleAI()">×</button>
    </div>

    <div class="ai-messages" id="aiMessages">
      <div class="ai-msg bot">Xin chào! Tôi là AI hỗ trợ của INK. Tôi có thể giúp gì cho bạn hôm nay?</div>
    </div>

    <div class="ai-quick" id="aiQuick" style="display:flex; gap:8px; padding:10px 20px; overflow-x:auto; background:#fff; border-top:1px solid var(--ink-border);">
      <div class="ai-chip" style="padding:6px 12px; background:var(--ink-light); border-radius:14px; font-size:12px; cursor:pointer; white-space:nowrap;" onclick="sendAI('Sản phẩm nào bán chạy nhất?')">Bán chạy nhất</div>
      <div class="ai-chip" style="padding:6px 12px; background:var(--ink-light); border-radius:14px; font-size:12px; cursor:pointer; white-space:nowrap;" onclick="sendAI('Chính sách giao hàng như thế nào?')">Chính sách giao hàng</div>
      <div class="ai-chip" style="padding:6px 12px; background:var(--ink-light); border-radius:14px; font-size:12px; cursor:pointer; white-space:nowrap;" onclick="sendAI('Có mã giảm giá không?')">Mã giảm giá</div>
    </div>
  </div>

<script>
  function toggleAI() {
    const aiBox = document.getElementById('aiBox');
    if (aiBox) aiBox.classList.toggle('open');
  }
  // Cấu trúc dữ liệu 8 nhóm và các câu hỏi cấp 2
  const chatMenu = {
    categories: [
      { id: 'san_pham', label: 'Sản phẩm' },
      { id: 'nhu_cau', label: 'Phù hợp nhu cầu' },
      { id: 'gia_ca', label: 'Giá cả & Khuyến mãi' },
      { id: 'dat_hang', label: 'Đặt hàng' },
      { id: 'giao_hang', label: 'Giao hàng' },
      { id: 'doi_tra', label: 'Đổi trả' },
      { id: 'ban_si', label: 'Bán sỉ' },
      { id: 'thanh_toan', label: 'Thanh toán' }
    ],
    questions: {
      san_pham: [
        'Có bán bút, vở, giấy in không?',
        'Có bán file hồ sơ, bìa còng, bìa kẹp không?',
        'Có bán đồ dùng học tập không?',
        'Có bán đồ dùng cho văn phòng công ty không?'
      ],
      nhu_cau: [
        'Tôi cần mua đồ cho học sinh lớp 1–12, shop có gì?',
        'Tôi cần mua đồ cho văn phòng, shop có gợi ý gì?',
        'Có combo văn phòng phẩm tiết kiệm không?',
        'Có sản phẩm dùng để học online, ghi chép, in ấn không?'
      ],
      gia_ca: [
        'Có sản phẩm giá rẻ không?',
        'Có combo tiết kiệm theo bộ không?',
        'Có giảm giá cho đơn hàng lớn không?',
        'Có mã giảm giá hoặc khuyến mãi theo mùa không?'
      ],
      dat_hang: [
        'Đặt hàng trên website như thế nào?',
        'Có thể đặt nhanh qua chat không?',
        'Có cần đăng ký tài khoản mới đặt được không?',
        'Có thể thay đổi đơn sau khi đặt không?'
      ],
      giao_hang: [
        'Shop có giao toàn quốc không?',
        'Giao hàng mất bao lâu?',
        'Phí ship được tính như thế nào?',
        'Có hỗ trợ kiểm tra hàng trước khi nhận không?'
      ],
      doi_tra: [
        'Hàng bị lỗi thì xử lý thế nào?',
        'Có đổi trả nếu nhận sai sản phẩm không?',
        'Trong bao lâu thì được đổi trả?',
        'Cần giữ hóa đơn hay ảnh chụp khiếu nại không?'
      ],
      ban_si: [
        'Có nhận đơn số lượng lớn không?',
        'Có báo giá sỉ cho trường học, công ty không?',
        'Có hỗ trợ xuất hóa đơn không?',
        'Có giao hàng định kỳ cho doanh nghiệp không?'
      ],
      thanh_toan: [
        'Có thanh toán khi nhận hàng không?',
        'Có chuyển khoản ngân hàng không?',
        'Có thanh toán qua ví điện tử không?',
        'Có hỗ trợ thanh toán cho đơn công ty không?'
      ]
    }
  };

  const messageContainer = document.getElementById('aiMessages');
  const quickMenuContainer = document.getElementById('aiQuick');

  // Hàm tạo nút bấm (Chip)
  function createChip(text, isBackBtn = false) {
    const chip = document.createElement('div');
    chip.className = 'ai-chip';
    chip.style.cssText = `padding:10px 14px; border-radius:12px; font-size:13px; cursor:pointer; font-weight: 600; text-align: center; transition: 0.2s; margin-bottom: 6px;`;
    
    if (isBackBtn) {
      chip.style.background = '#F1F5F9';
      chip.style.color = 'var(--ink-gray)';
      chip.style.border = '1px solid #CBD5E1';
    } else {
      chip.style.background = 'var(--ink-light)';
      chip.style.color = 'var(--ink-purple)';
      chip.style.border = '1.5px solid var(--ink-purple)';
      chip.onmouseover = () => { chip.style.background = 'var(--ink-purple)'; chip.style.color = '#fff'; };
      chip.onmouseout = () => { chip.style.background = 'var(--ink-light)'; chip.style.color = 'var(--ink-purple)'; };
    }
    
    chip.textContent = text;
    return chip;
  }

  // Khởi tạo Menu Cấp 1
  function showCategories() {
    quickMenuContainer.innerHTML = ''; 
    
    chatMenu.categories.forEach(cat => {
      const btn = createChip(cat.label);
      btn.onclick = () => {
        addMessage(`Tôi cần hỗ trợ về: ${cat.label.replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]\s*/g, '')}`, 'user');
        addMessage(`Bạn đang quan tâm đến vấn đề cụ thể nào trong mục "${cat.label.replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]\s*/g, '')}"?`, 'bot');
        showSubQuestions(cat.id); 
      };
      quickMenuContainer.appendChild(btn);
    });
  }

  // Khởi tạo Menu Cấp 2
  function showSubQuestions(categoryId) {
    quickMenuContainer.innerHTML = ''; 
    
    const backBtn = createChip('← Quay lại danh mục chính', true);
    backBtn.onclick = () => { showCategories(); };
    quickMenuContainer.appendChild(backBtn);

    chatMenu.questions[categoryId].forEach(q => {
      const btn = createChip(q);
      btn.onclick = () => { sendToAPI(q); };
      quickMenuContainer.appendChild(btn);
    });
  }

  // Hàm in tin nhắn. Thêm tính năng cho phép chèn link HTML (để link FB click được)
  function addMessage(text, sender, isHTML = false) {
    const msgDiv = document.createElement('div');
    msgDiv.className = `ai-msg ${sender}`;
    if (isHTML) {
      msgDiv.innerHTML = text;
    } else {
      msgDiv.textContent = text;
    }
    messageContainer.appendChild(msgDiv);
    messageContainer.scrollTop = messageContainer.scrollHeight;
  }

  async function sendToAPI(questionText) {
    addMessage(questionText, 'user');
    
    // Tạm thời xóa menu đi để khách không bấm lung tung khi đang chờ AI trả lời
    quickMenuContainer.innerHTML = '';

    const typing = document.createElement('div');
    typing.className = 'typing';
    typing.innerHTML = '<div class="dot"></div><div class="dot"></div><div class="dot"></div>';
    messageContainer.appendChild(typing);
    messageContainer.scrollTop = messageContainer.scrollHeight;

    try {
      const response = await fetch('chat_AI.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: questionText })
      });
      const data = await response.json();

      setTimeout(() => {
        messageContainer.removeChild(typing);
        addMessage(data.reply, 'bot');
        
        // Gọi hàm hiện nút Feedback sau khi bot trả lời xong
        showFeedbackOptions();
      }, 600);

    } catch (error) {
      messageContainer.removeChild(typing);
      addMessage("Hệ thống đang bảo trì, vui lòng thử lại sau.", 'bot');
      showCategories(); 
    }
  }

  // ==========================================
  // PHẦN MỚI: XỬ LÝ FEEDBACK CỦA KHÁCH HÀNG
  // ==========================================

  function showFeedbackOptions() {
    quickMenuContainer.innerHTML = ''; 

    // Nút Option 1
    const btn1 = createChip('👍 Mình hiểu rồi, cảm ơn bạn');
    btn1.onclick = () => { handleFeedback('good'); };
    quickMenuContainer.appendChild(btn1);

    // Nút Option 2 (Đổi style một chút để khách dễ nhận diện)
    const btn2 = createChip('🤔 Mình chưa hiểu lắm', true);
    btn2.onclick = () => { handleFeedback('bad'); };
    quickMenuContainer.appendChild(btn2);
  }

  function handleFeedback(type) {
    if (type === 'good') {
      addMessage('Mình hiểu rồi, cảm ơn bạn', 'user');
      addMessage('Chúc bạn mua sắm văn phòng phẩm thật tiện lợi và tiết kiệm tại shop nhé! 🛒', 'bot');
    } else {
      addMessage('Mình chưa hiểu lắm', 'user');
      
      // In ra dòng thông báo có chứa link Facebook (Dùng cờ isHTML = true)
      const contactMsg = `Bạn vui lòng liên hệ hotline <strong>0373111481</strong> hoặc nhắn tin qua Facebook: <a href="https://www.facebook.com/nhung.inh.125242/" target="_blank" style="color: var(--ink-purple); font-weight: bold; text-decoration: underline;">TẠI ĐÂY</a> để được hỗ trợ chi tiết hơn nhé!`;
      addMessage(contactMsg, 'bot', true);
    }

    // Sau khi phản hồi, tạo độ trễ 2 giây để khách đọc tin nhắn rồi hiện lại Menu Chính
    setTimeout(() => {
      showCategories();
    }, 10000);
  }

  // Khởi chạy khi load trang
  showCategories();
</script>