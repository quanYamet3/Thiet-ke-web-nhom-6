<footer>
  <div class="footer-grid">

    <!-- BRAND -->
    <div class="footer-brand">
      <a href="Trang_chủ.php" class="nav-logo">
        <img src="logo.jpg" class="logo-img">
      </a>
      <p>Chuyên cung cấp văn phòng phẩm chất lượng cao cho cá nhân và doanh nghiệp. Chất lượng là cam kết của chúng tôi.</p>
    </div>

    <!-- CỬA HÀNG -->
    <div class="footer-col">
      <h4>Cửa Hàng</h4>
      <a href="sanpham.php">Sản Phẩm</a>
      <a href="gioithieu.php">Giới Thiệu</a>
      <a href="tintuc.php">Blog</a>
      <a href="lienhe.php">Liên Hệ</a>
    </div>

    <!-- HỖ TRỢ -->
    <div class="footer-col">
      <h4>Hỗ Trợ</h4>
      <a class="footer-link" onclick="openFooterModal('doi-tra')">
        <i class="ti ti-refresh"></i> Chính sách đổi trả
      </a>
      <a class="footer-link" onclick="openFooterModal('mua-hang')">
        <i class="ti ti-shopping-cart"></i> Hướng dẫn mua hàng
      </a>
      <a class="footer-link" onclick="openFooterModal('dieu-khoan')">
        <i class="ti ti-credit-card"></i> Điều khoản sử dụng
      </a>
      <a class="footer-link" onclick="openFooterModal('bao-mat')">
        <i class="ti ti-shield-lock"></i> Chính sách bảo mật
      </a>
    </div>

    <!-- LIÊN HỆ -->
    <div class="footer-col">
      <h4>Liên Hệ</h4>
      <a href="tel:0913200206">📞 0913200206</a>
      <a href="mailto:inkcorner.contact@gmail.com">✉️ inkcorner@gmail.com</a>
      <a href="https://www.google.com/maps?q=79+Hồ+Tùng+Mậu+Hà+Nội" target="_blank">
        📍 79, Hồ Tùng Mậu, Hà Nội
      </a>
    </div>

  </div>

  <!-- FOOTER BOTTOM -->
  <div class="footer-bottom">
    <span>© <?php echo date('Y'); ?> INK Corner. All rights reserved.</span>
    <span>Thiết kế bởi INK Team</span>
  </div>
</footer>


<!-- ===== MODAL OVERLAY ===== -->
<div class="footer-overlay" id="footerOverlay" onclick="handleFooterOverlayClick(event)">
  <div class="footer-modal" id="footerModal">
    <div class="footer-modal-header">
      <h2 id="footerModalTitle"></h2>
      <button class="footer-modal-close" onclick="closeFooterModal()" aria-label="Đóng">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <div class="footer-modal-body" id="footerModalBody"></div>
  </div>
</div>


<script>
  const footerModalContent = {
    'doi-tra': {
      title: 'Chính sách đổi trả',
      html: `
        <p>Chúng tôi cam kết mang đến trải nghiệm mua sắm tốt nhất. Nếu sản phẩm không đáp ứng kỳ vọng, bạn có thể đổi trả dễ dàng.</p>
        <h3>Điều kiện đổi trả</h3>
        <p>Sản phẩm còn nguyên tem, nhãn, chưa qua sử dụng. Còn hóa đơn mua hàng. Trong vòng <strong>30 ngày</strong> kể từ ngày nhận hàng.</p>
        <h3>Các trường hợp được đổi trả</h3>
        <span class="footer-tag">Hàng bị lỗi từ nhà sản xuất</span>
        <span class="footer-tag">Sai màu sắc / kích thước</span>
        <span class="footer-tag">Hàng không đúng mô tả</span>
        <span class="footer-tag">Thiếu phụ kiện đi kèm</span>
        <h3>Quy trình</h3>
        <p>Liên hệ CSKH → Xác nhận yêu cầu → Gửi hàng về → Nhận hàng mới hoặc hoàn tiền trong 3–5 ngày làm việc.</p>
      `
    },
    'mua-hang': {
      title: 'Hướng dẫn mua hàng',
      html: `
        <p>Mua sắm tại INK Corner rất đơn giản, nhanh chóng và an toàn.</p>
        <h3>Bước 1 — Chọn sản phẩm</h3>
        <p>Tìm kiếm sản phẩm theo tên hoặc duyệt theo danh mục. Chọn màu sắc, kích thước phù hợp rồi bấm <em>Thêm vào giỏ hàng</em>.</p>
        <h3>Bước 2 — Xem lại giỏ hàng</h3>
        <p>Kiểm tra số lượng, áp dụng mã giảm giá (nếu có) và xác nhận tổng thanh toán.</p>
        <h3>Bước 3 — Đặt hàng</h3>
        <p>Điền thông tin giao hàng, chọn phương thức thanh toán và bấm <em>Đặt hàng</em>. Bạn sẽ nhận email xác nhận ngay sau đó.</p>
        <h3>Bước 4 — Nhận hàng</h3>
        <p>Đơn hàng được giao trong <strong>2–5 ngày</strong> tùy khu vực. Bạn có thể theo dõi trạng thái đơn hàng trong mục <em>Tài khoản của tôi</em>.</p>
      `
    },
    'dieu-khoan': {
      title: 'Điều khoản sử dụng',
      html: `
        <p>Khi truy cập và sử dụng website hoặc các nền tảng bán hàng của <strong>InkCorner</strong>, bạn đồng ý tuân thủ các điều khoản và điều kiện được quy định dưới đây. Những điều khoản này được thiết lập nhằm đảm bảo quyền lợi của cả khách hàng và InkCorner, đồng thời duy trì một môi trường mua sắm minh bạch, an toàn và đáng tin cậy. Nếu bạn không đồng ý với bất kỳ nội dung nào trong điều khoản này, vui lòng ngừng sử dụng dịch vụ của chúng tôi.</p>
        
        <h3>1. Thông tin sản phẩm và dịch vụ</h3>
        <p>InkCorner cung cấp thông tin sản phẩm, dịch vụ và nội dung liên quan với mục đích hỗ trợ khách hàng trong quá trình mua sắm. Chúng tôi luôn nỗ lực đảm bảo rằng mọi thông tin hiển thị, bao gồm mô tả sản phẩm, hình ảnh, giá cả và tình trạng hàng hóa, đều chính xác và được cập nhật thường xuyên.</p>
        <p>Tuy nhiên, trong một số trường hợp ngoài ý muốn, có thể xảy ra sai sót hoặc chậm trễ trong việc cập nhật. InkCorner có quyền điều chỉnh hoặc thay đổi thông tin mà không cần thông báo trước, đồng thời không chịu trách nhiệm đối với những sai lệch không đáng kể.</p>

        <h3>2. Trách nhiệm của khách hàng</h3>
        <p>Khi thực hiện đặt hàng, khách hàng có trách nhiệm cung cấp thông tin chính xác và đầy đủ, bao gồm tên, địa chỉ, số điện thoại và các thông tin cần thiết khác để phục vụ quá trình xử lý và giao hàng. InkCorner không chịu trách nhiệm đối với các vấn đề phát sinh do thông tin sai lệch hoặc không đầy đủ từ phía khách hàng.</p>
        <p>Trong trường hợp phát hiện dấu hiệu gian lận hoặc sử dụng thông tin không hợp lệ, chúng tôi có quyền từ chối hoặc hủy đơn hàng mà không cần thông báo trước.</p>

        <h3>3. Xác nhận và Hủy đơn hàng</h3>
        <p>Mọi đơn hàng chỉ được xác nhận khi InkCorner hoàn tất việc kiểm tra thông tin và khả năng cung ứng sản phẩm. Chúng tôi có quyền từ chối hoặc hủy đơn hàng trong các trường hợp như sản phẩm hết hàng, sai giá, lỗi hệ thống hoặc các vấn đề phát sinh ngoài kiểm soát. Trong những trường hợp này, khách hàng sẽ được thông báo và hoàn tiền (nếu đã thanh toán) theo quy định.</p>

        <h3>4. Quyền sở hữu trí tuệ</h3>
        <p>Tất cả nội dung trên website, bao gồm hình ảnh, văn bản, thiết kế và logo, đều thuộc quyền sở hữu của InkCorner hoặc các bên liên quan và được bảo vệ bởi các quy định về sở hữu trí tuệ. Khách hàng không được phép sao chép, sử dụng hoặc phân phối lại nội dung dưới bất kỳ hình thức nào khi chưa có sự đồng ý bằng văn bản từ InkCorner.</p>

        <h3>5. Cam kết sử dụng và Giới hạn trách nhiệm</h3>
        <p>Trong quá trình sử dụng dịch vụ, khách hàng cam kết không thực hiện các hành vi gây ảnh hưởng đến hoạt động của website hoặc gây thiệt hại đến InkCorner, bao gồm nhưng không giới hạn ở việc can thiệp vào hệ thống, phát tán mã độc hoặc sử dụng website vào mục đích không hợp pháp. InkCorner có quyền tạm ngưng hoặc chấm dứt quyền truy cập của người dùng trong trường hợp phát hiện hành vi vi phạm.</p>
        <p>InkCorner không chịu trách nhiệm đối với các thiệt hại phát sinh ngoài phạm vi kiểm soát hợp lý, bao gồm nhưng không giới hạn ở sự cố hệ thống, gián đoạn dịch vụ, lỗi kỹ thuật hoặc các yếu tố khách quan khác. Tuy nhiên, chúng tôi luôn nỗ lực tối đa để đảm bảo hệ thống hoạt động ổn định và hỗ trợ khách hàng trong mọi tình huống phát sinh.</p>

        <h3>6. Cập nhật điều khoản</h3>
        <p>Các điều khoản sử dụng này có thể được cập nhật hoặc điều chỉnh theo thời gian nhằm phù hợp với hoạt động kinh doanh và quy định pháp luật hiện hành. Mọi thay đổi sẽ được công bố trên website và có hiệu lực kể từ thời điểm đăng tải. Khách hàng nên thường xuyên theo dõi để cập nhật những thông tin mới nhất.</p>

        <hr style="border: 0.5px solid #eaeaea; margin: 15px 0;">
        <p><em>Việc tiếp tục sử dụng dịch vụ của InkCorner sau khi các điều khoản được cập nhật đồng nghĩa với việc bạn chấp nhận những thay đổi đó. Nếu bạn có bất kỳ câu hỏi nào liên quan đến điều khoản sử dụng, vui lòng liên hệ với chúng tôi để được giải đáp và hỗ trợ kịp thời.</em></p>
      `
    },
    'bao-mat': {
      title: 'Chính sách bảo mật',
      html: `
        <p><strong>InkCorner</strong> cam kết tôn trọng và bảo vệ quyền riêng tư của khách hàng khi truy cập và mua sắm tại website hoặc các nền tảng bán hàng của chúng tôi. Chính sách bảo mật này được xây dựng nhằm giải thích cách chúng tôi thu thập, sử dụng và bảo vệ thông tin cá nhân, đồng thời đảm bảo rằng mọi dữ liệu của khách hàng đều được xử lý một cách minh bạch và an toàn.</p>
        
        <h3>1. Thông tin thu thập</h3>
        <p>Khi bạn truy cập website hoặc thực hiện mua hàng tại InkCorner, chúng tôi có thể thu thập một số thông tin cá nhân cần thiết như họ và tên, địa chỉ email, số điện thoại, địa chỉ nhận hàng và các thông tin liên quan đến giao dịch. Những thông tin này được thu thập nhằm mục đích xử lý đơn hàng, giao hàng, cung cấp dịch vụ hỗ trợ khách hàng và cải thiện trải nghiệm mua sắm.</p>
        <p>Trong một số trường hợp, chúng tôi cũng có thể thu thập dữ liệu về hành vi truy cập như thời gian truy cập, trang đã xem hoặc thiết bị sử dụng để tối ưu hóa hoạt động của website.</p>

        <h3>2. Mục đích sử dụng</h3>
        <p>InkCorner sử dụng thông tin khách hàng một cách có trách nhiệm và chỉ trong phạm vi cần thiết để phục vụ hoạt động kinh doanh. Cụ thể, thông tin được sử dụng để xác nhận đơn hàng, liên hệ khi cần thiết, hỗ trợ khách hàng, cải thiện chất lượng dịch vụ và gửi thông tin về sản phẩm mới hoặc chương trình ưu đãi nếu khách hàng đồng ý nhận thông báo.</p>
        <p>Chúng tôi cam kết không sử dụng thông tin cá nhân cho các mục đích ngoài phạm vi đã nêu mà không có sự đồng ý của khách hàng.</p>

        <h3>3. Về việc chia sẻ thông tin</h3>
        <p>InkCorner không bán, trao đổi hoặc chia sẻ thông tin cá nhân của khách hàng cho bên thứ ba vì mục đích thương mại. Tuy nhiên, trong một số trường hợp cần thiết, chúng tôi có thể cung cấp thông tin cho các đối tác liên quan như đơn vị vận chuyển hoặc nền tảng thanh toán để hoàn tất quá trình giao dịch. Những đối tác này chỉ được phép sử dụng thông tin trong phạm vi cần thiết và có trách nhiệm bảo mật dữ liệu theo quy định.</p>

        <h3>4. Cam kết bảo mật</h3>
        <p>Chúng tôi áp dụng các biện pháp bảo mật phù hợp nhằm bảo vệ thông tin cá nhân của khách hàng khỏi việc truy cập trái phép, mất mát hoặc lạm dụng. Các hệ thống lưu trữ dữ liệu được kiểm soát và giới hạn quyền truy cập, đồng thời được cập nhật thường xuyên để đảm bảo an toàn.</p>
        <p>Tuy nhiên, khách hàng cũng cần chủ động bảo vệ thông tin cá nhân của mình, chẳng hạn như không chia sẻ thông tin tài khoản hoặc dữ liệu nhạy cảm cho người khác.</p>

        <h3>5. Quyền lợi và Cập nhật chính sách</h3>
        <p>Khách hàng có quyền yêu cầu kiểm tra, cập nhật hoặc xóa thông tin cá nhân của mình bất cứ lúc nào bằng cách liên hệ trực tiếp với InkCorner. Chúng tôi sẽ tiếp nhận và xử lý các yêu cầu này trong thời gian hợp lý, nhằm đảm bảo quyền lợi và sự an tâm của khách hàng khi sử dụng dịch vụ.</p>
        <p>InkCorner có thể cập nhật hoặc điều chỉnh chính sách bảo mật theo thời gian để phù hợp với hoạt động kinh doanh và quy định pháp luật hiện hành. Mọi thay đổi sẽ được thông báo trên website để khách hàng dễ dàng theo dõi và nắm bắt.</p>
        
        <hr style="border: 0.5px solid #eaeaea; margin: 15px 0;">
        <p><em>Chúng tôi hiểu rằng sự tin tưởng của khách hàng là yếu tố quan trọng nhất trong quá trình phát triển. Vì vậy, InkCorner luôn nỗ lực bảo vệ thông tin cá nhân một cách nghiêm túc, minh bạch và có trách nhiệm. Nếu bạn có bất kỳ câu hỏi nào liên quan đến chính sách bảo mật, vui lòng liên hệ với chúng tôi để được hỗ trợ kịp thời.</em></p>
      `
    }
  };

  function openFooterModal(id) {
    const c = footerModalContent[id];
    document.getElementById('footerModalTitle').textContent = c.title;
    document.getElementById('footerModalBody').innerHTML = c.html;
    document.getElementById('footerOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeFooterModal() {
    document.getElementById('footerOverlay').classList.remove('active');
    document.body.style.overflow = '';
  }

  function handleFooterOverlayClick(e) {
    if (e.target === document.getElementById('footerOverlay')) {
      closeFooterModal();
    }
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeFooterModal();
  });
</script>