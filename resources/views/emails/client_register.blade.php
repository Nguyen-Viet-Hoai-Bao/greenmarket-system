<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng của bạn đang chờ phê duyệt</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6; /* Nền xám nhạt */
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .email-header {
            text-align: center;
            padding: 30px 20px 20px;
            background-color: #007bff; /* Màu xanh dương chủ đạo */
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
        }
        .icon {
            margin-bottom: 15px;
            width: 60px; /* Kích thước biểu tượng */
            height: 60px;
            background-color: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #007bff;
        }
        .email-body {
            padding: 30px 40px;
        }
        .email-body p {
            margin-bottom: 15px;
            font-size: 15px;
        }
        .email-body .greeting {
            font-size: 17px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .cta-button-container {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #28a745; /* Màu xanh lá cây cho nút */
            color: #ffffff;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .cta-button:hover {
            background-color: #218838;
        }
        .email-footer {
            background-color: #ebf0f0;
            padding: 20px 40px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="icon">&#10003;</div> <h1>Xác nhận đăng ký cửa hàng</h1>
        </div>
        <div class="email-body">
            <p class="greeting">Xin chào {{ $name }},</p>
            <p>Chúng tôi đã nhận được yêu cầu đăng ký cửa hàng của bạn trên hệ thống.</p>
            <p>Hiện tại, hồ sơ của bạn đang trong quá trình chờ đội ngũ quản trị viên của chúng tôi xem xét và phê duyệt. Chúng tôi sẽ thông báo cho bạn ngay khi quá trình này hoàn tất.</p>
            <p>Xin vui lòng kiên nhẫn. Chúng tôi cam kết sẽ liên hệ lại với bạn trong thời gian sớm nhất.</p>
            <p style="margin-top: 25px;">Trân trọng,<br>Đội ngũ [Green Food]</p>
        </div>
        <div class="email-footer">
            <p>Đây là email tự động. Vui lòng không trả lời thư này.</p>
            <p>&copy; {{ date('Y') }} [Green Food].</p>
        </div>
    </div>
</body>
</html>