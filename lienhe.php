<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iNK - Liên hệ</title>
    <link rel="stylesheet" href="CSS_Liên_hệ.css">
    <link rel="stylesheet" href="CSS_header.css">
    <link rel="stylesheet" href="CSS_footer.css">
</head>
  <?php include 'header.php'; ?>
<body>
    <div class="container">
        <div class="breadcrumb">
        Trang chủ / <span>Liên hệ</span>
        </div>
        <div class="contact-layout">
            
            <div class="contact-info-col">
                <h1>📞 CONTACT</h1>
                <p class="contact-intro">
                    Nếu bạn có bất kỳ câu hỏi nào về sản phẩm, đơn hàng hoặc cần hỗ trợ trong quá trình mua sắm, đừng ngần ngại liên hệ với InkCorner. Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn một cách nhanh chóng và rõ ràng nhất.<br><br>
                    Dù bạn đang cần tư vấn chọn sản phẩm, kiểm tra tình trạng đơn hàng hay đơn giản là muốn hỏi thêm thông tin, đội ngũ của InkCorner luôn cố gắng phản hồi trong thời gian sớm nhất để đảm bảo bạn có trải nghiệm mua sắm thuận tiện và dễ dàng.
                </p>

                <div class="contact-section">
                    <h3>📧 Thông tin liên hệ</h3>
                    <ul class="contact-list">
                        <li><strong>Email:</strong> inkcorner.contact@gmail.com</li>
                        <li><strong>Điện thoại:</strong> <span class="highlight">0913.200.206</span></li>
                        <li><strong>Thời gian phản hồi:</strong> 9:00 – 18:00 (Thứ 2 – Thứ 7)</li>
                    </ul>
                </div>

                <div class="contact-section">
                    <h3>💬 Liên hệ nhanh</h3>
                    <p style="margin-bottom: 10px; font-size: 14px;">Bạn có thể gửi tin nhắn trực tiếp cho chúng tôi thông qua các nền tảng mạng xã hội:</p>
                    <ul class="contact-list">
                        <li><strong>Facebook:</strong> InkCorner Official</li>
                        <li><strong>Instagram:</strong> inkconer.official</li>
                        <li><strong>TikTok:</strong> inkcorner.official</li>
                    </ul>
                </div>

                <div class="contact-section">
                    <h3>📦 Hỗ trợ đơn hàng</h3>
                    <p style="margin-bottom: 10px; font-size: 14px;">Nếu bạn cần hỗ trợ liên quan đến đơn hàng, vui lòng chuẩn bị sẵn các thông tin sau để InkCorner hỗ trợ bạn nhanh chóng và chính xác hơn:</p>
                    <ul class="contact-list">
                        <li>Mã đơn hàng</li>
                        <li>Tên người đặt</li>
                        <li>Vấn đề bạn đang gặp phải</li>
                    </ul>
                </div>

                <div class="contact-section">
                    <h3>🤝 Lời nhắn từ InkCorner</h3>
                    <div class="message-box">
                        "InkCorner luôn mong muốn mang đến trải nghiệm mua sắm đơn giản, rõ ràng và đáng tin cậy. Mọi ý kiến đóng góp của bạn đều rất quan trọng để chúng tôi có thể cải thiện và phục vụ tốt hơn mỗi ngày."
                    </div>
                </div>
            </div>

            <div class="contact-form-col">
                <h3>Gửi tin nhắn cho InkCorner</h3>
                <form action="#" method="POST">
                    <div class="form-group">
                        <label for="name">Họ và tên của bạn</label>
                        <input type="text" id="name" placeholder="Nhập họ tên...">
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" placeholder="Nhập số điện thoại...">
                    </div>
                    <div class="form-group">
                        <label for="order-id">Mã đơn hàng (nếu có)</label>
                        <input type="text" id="order-id" placeholder="Ví dụ: #INK12345">
                    </div>
                    <div class="form-group">
                        <label for="message">Nội dung / Vấn đề bạn gặp phải</label>
                        <textarea id="message" rows="6" placeholder="Vui lòng mô tả chi tiết để chúng tôi hỗ trợ tốt nhất..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Gửi tin nhắn</button>
                </form>
            </div>

        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>