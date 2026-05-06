<?php
header('Content-Type: application/json; charset=utf-8');

$inputData = json_decode(file_get_contents('php://input'), true);
// Chuyển câu hỏi nhận được thành chữ thường để dễ dàng so khớp
$messageText = isset($inputData['message']) ? mb_strtolower(trim($inputData['message']), 'UTF-8') : '';

// 1. TẠO TỪ ĐIỂN CÂU HỎI - TRẢ LỜI
// (Khóa là các câu hỏi đã được chuyển thành chữ thường)
$faqDatabase = [
    // --- 1) Sản phẩm ---
    'có bán bút, vở, giấy in không?' => 'Shop cung cấp nhiều mặt hàng văn phòng phẩm như bút, vở, giấy in, file hồ sơ, bìa còng, kẹp giấy, ghim, sổ tay, nhãn dán và các dụng cụ học tập, làm việc khác.',
    'có bán file hồ sơ, bìa còng, bìa kẹp không?' => 'Shop có đầy đủ đồ dùng văn phòng như giấy in, bìa hồ sơ, bìa kẹp, bút ký, ghim bấm, máy tính cầm tay, sổ ghi chép và vật dụng bàn làm việc.',
    'có bán đồ dùng học tập không?' => 'Có. Shop có các sản phẩm phù hợp cho học sinh như bút viết, thước kẻ, compa, vở, sổ tay, hộp bút và đồ dùng học tập cơ bản.',
    'có bán đồ dùng cho văn phòng công ty không?' => 'Có. Shop có đầy đủ đồ dùng văn phòng như giấy in, bìa hồ sơ, bìa kẹp, bút ký, ghim bấm, máy tính cầm tay, sổ ghi chép và vật dụng bàn làm việc.',

    // --- 2) Phù hợp nhu cầu ---
    'tôi cần mua đồ cho học sinh lớp 1–12, shop có gì?' => 'Có. Shop có các sản phẩm phù hợp cho học sinh như bút viết, thước kẻ, compa, vở, sổ tay, hộp bút và đồ dùng học tập cơ bản.',
    'tôi cần mua đồ cho văn phòng, shop có gợi ý gì?' => 'Có. Shop có đầy đủ đồ dùng văn phòng như giấy in, bìa hồ sơ, bìa kẹp, bút ký, ghim bấm, máy tính cầm tay, sổ ghi chép và vật dụng bàn làm việc.',
    'có combo văn phòng phẩm tiết kiệm không?' => 'Có. Shop thường có các combo văn phòng phẩm theo nhu cầu như combo học sinh, combo đi làm, combo giấy in hoặc combo đồ dùng bàn học.',
    'có sản phẩm dùng để học online, ghi chép, in ấn không?' => 'Shop cung cấp đa dạng mặt hàng như giấy in, sổ tay, bút và các vật dụng phục vụ cực tốt cho việc học online và làm việc tại nhà.',

    // --- 3) Giá cả và khuyến mãi ---
    'có sản phẩm giá rẻ không?' => 'Shop có nhiều mức giá khác nhau, từ sản phẩm phổ thông tiết kiệm đến sản phẩm cao cấp, phù hợp cho cả học sinh và dân văn phòng.',
    'có combo tiết kiệm theo bộ không?' => 'Có. Shop thường có các combo văn phòng phẩm theo nhu cầu như combo học sinh, combo đi làm, combo giấy in hoặc combo đồ dùng bàn học.',
    'có giảm giá cho đơn hàng lớn không?' => 'Có. Đơn hàng số lượng lớn thường được hưởng giá tốt hơn và có thể được hỗ trợ thêm tùy chương trình.',
    'có mã giảm giá hoặc khuyến mãi theo mùa không?' => 'Shop luôn có các chương trình khuyến mãi, mã giảm giá đặc biệt theo từng mùa tựu trường hoặc lễ hội. Bạn nhớ theo dõi website nhé!',

    // --- 4) Đặt hàng ---
    'đặt hàng trên website như thế nào?' => 'Bạn chỉ cần chọn sản phẩm, thêm vào giỏ hàng, nhập thông tin nhận hàng và xác nhận đơn. Sau đó shop sẽ liên hệ để hoàn tất đơn hàng.',
    'có thể đặt nhanh qua chat không?' => 'Có. Bạn chỉ cần gửi tên sản phẩm, số lượng và địa chỉ nhận hàng, shop sẽ hỗ trợ tạo đơn nhanh cho bạn.',
    'có cần đăng ký tài khoản mới đặt được không?' => 'Không bắt buộc. Bạn vẫn có thể đặt hàng bằng cách điền thông tin mua hàng trực tiếp trên website hoặc qua chat.',
    'có thể thay đổi đơn sau khi đặt không?' => 'Bạn hoàn toàn có thể thay đổi đơn hàng. Hãy nhắn tin báo lại cho shop sớm nhất trước khi đơn hàng được giao cho đơn vị vận chuyển nhé.',

    // --- 5) Giao hàng ---
    'shop có giao toàn quốc không?' => 'Có. Shop nhận giao hàng đến nhiều tỉnh thành trên toàn quốc.',
    'giao hàng mất bao lâu?' => 'Thời gian giao hàng phụ thuộc khu vực nhận hàng. Thông thường nội thành sẽ nhanh hơn, còn các tỉnh xa có thể cần thêm thời gian.',
    'phí ship được tính như thế nào?' => 'Phí vận chuyển sẽ được tính theo khu vực, khối lượng đơn hàng và đơn vị giao hàng. Bạn sẽ được báo trước khi xác nhận đơn.',
    'có hỗ trợ kiểm tra hàng trước khi nhận không?' => 'Tùy vào quy định của đơn vị vận chuyển tại thời điểm giao hàng, bạn có thể kiểm tra ngoại quan kiện hàng trước khi nhận.',

    // --- 6) Đổi trả ---
    'hàng bị lỗi thì xử lý thế nào?' => 'Nếu sản phẩm bị lỗi, shop sẽ hỗ trợ đổi trả theo chính sách hiện hành. Bạn chỉ cần liên hệ sớm và gửi thông tin đơn hàng.',
    'có đổi trả nếu nhận sai sản phẩm không?' => 'Có. Nếu shop giao nhầm sản phẩm, shop sẽ hỗ trợ đổi lại đúng hàng cho bạn.',
    'trong bao lâu thì được đổi trả?' => 'Thời gian đổi trả sẽ tùy theo chính sách của shop, thường áp dụng trong vài ngày sau khi nhận hàng.',
    'cần giữ hóa đơn hay ảnh chụp khiếu nại không?' => 'Để việc đổi trả diễn ra nhanh chóng, bạn vui lòng giữ lại hóa đơn và quay video/chụp ảnh tình trạng gói hàng lúc vừa mở nhé.',

    // --- 7) Bán sỉ ---
    'có nhận đơn số lượng lớn không?' => 'Có. Shop nhận đơn số lượng lớn cho trường học, công ty, văn phòng và đại lý.',
    'có báo giá sỉ cho trường học, công ty không?' => 'Có. Với đơn số lượng lớn, shop có thể cung cấp báo giá phù hợp theo từng nhu cầu.',
    'có hỗ trợ xuất hóa đơn không?' => 'Có. Shop hỗ trợ xuất hóa đơn VAT đầy đủ cho trường học và các doanh nghiệp khi có yêu cầu.',
    'có giao hàng định kỳ cho doanh nghiệp không?' => 'Có. Shop nhận ký hợp đồng cung cấp và giao hàng văn phòng phẩm định kỳ hàng tháng cho các công ty, doanh nghiệp.',

    // --- 8) Thanh toán ---
    'có thanh toán khi nhận hàng không?' => 'Có. Bạn có thể thanh toán khi nhận hàng nếu khu vực giao hàng hỗ trợ COD.',
    'có chuyển khoản ngân hàng không?' => 'Có. Shop hỗ trợ chuyển khoản ngân hàng để bạn thanh toán thuận tiện hơn.',
    'có thanh toán qua ví điện tử không?' => 'Có thể có, tùy theo phương thức thanh toán shop đang hỗ trợ tại thời điểm đặt hàng.',
    'có hỗ trợ thanh toán cho đơn công ty không?' => 'Có. Shop hỗ trợ thanh toán qua tài khoản công ty và cung cấp đầy đủ chứng từ cần thiết.'
];

// 2. TÌM KIẾM CÂU TRẢ LỜI
if (isset($faqDatabase[$messageText])) {
    // Nếu tìm thấy câu hỏi trong mảng, lấy câu trả lời tương ứng
    $botReply = $faqDatabase[$messageText];
} else {
    // Nếu không tìm thấy (trường hợp an toàn)
    $botReply = "Xin lỗi, hiện tại tôi chưa hiểu rõ câu hỏi này. Bạn vui lòng liên hệ trực tiếp qua số Hotline để được nhân viên hỗ trợ chi tiết nhé!";
}

// Trả kết quả về cho web
echo json_encode(['reply' => $botReply]);
?>