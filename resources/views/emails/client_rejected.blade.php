<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu đăng ký cửa hàng không được duyệt</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
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
            background-color: #dc3545; /* Màu đỏ */
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
        }
        .icon {
            margin-bottom: 15px;
            width: 60px;
            height: 60px;
            background-color: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #dc3545;
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
            <div class="icon">&#10008;</div> <h1>Yêu cầu đăng ký cửa hàng không được duyệt</h1>
        </div>
        <div class="email-body">
            <p class="greeting">Xin chào {{ $name }},</p>
            <p>Chúng tôi rất tiếc phải thông báo rằng yêu cầu đăng ký cửa hàng của bạn trên hệ thống đã **không được phê duyệt**.</p>
            <p>Sau khi xem xét kỹ lưỡng, chúng tôi nhận thấy rằng hồ sơ của bạn chưa đáp ứng đủ các tiêu chí của chúng tôi.</p>
            <p>Nếu bạn có bất kỳ câu hỏi nào hoặc muốn biết thêm chi tiết về lý do từ chối, vui lòng liên hệ với đội ngũ hỗ trợ của chúng tôi tại [greanfood@gmail.com] hoặc [0786428244].</p>
            <p>Bạn có thể thử đăng ký lại sau khi đã khắc phục các vấn đề liên quan.</p>
            <p style="margin-top: 25px;">Trân trọng,<br>Đội ngũ [Green Food]</p>
        </div>
        <div class="email-footer">
            <p>Đây là email tự động. Vui lòng không trả lời thư này.</p>
            <p>&copy; {{ date('Y') }} [Green Food].</p>
        </div>
    </div>
</body>
</html>