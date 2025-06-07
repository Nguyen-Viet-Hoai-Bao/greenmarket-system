<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản cửa hàng của bạn đã bị khóa</title>
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
            background-color: #ffc107; /* Màu vàng */
            color: #333; /* Màu chữ đậm hơn để nổi bật */
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
            color: #ffc107;
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
            <div class="icon">&#128274;</div> <h1>Thông báo: Tài khoản cửa hàng của bạn đã bị khóa</h1>
        </div>
        <div class="email-body">
            <p class="greeting">Xin chào {{ $name }},</p>
            <p>Chúng tôi thông báo rằng tài khoản cửa hàng của bạn trên hệ thống đã **bị khóa**.</p>
            <p>Quyết định này được đưa ra do [Lý do khóa tài khoản, ví dụ: vi phạm chính sách, hoạt động đáng ngờ].</p>
            <p>Bạn sẽ không thể truy cập vào cửa hàng hoặc thực hiện bất kỳ giao dịch nào trên hệ thống của chúng tôi.</p>
            <p>Nếu bạn tin rằng đây là một sự nhầm lẫn hoặc muốn thảo luận về vấn đề này, vui lòng liên hệ với đội ngũ hỗ trợ của chúng tôi tại [greanfood@gmail.com] hoặc [0786428244].</p>
            <p style="margin-top: 25px;">Trân trọng,<br>Đội ngũ [Green Food]</p>
        </div>
        <div class="email-footer">
            <p>Đây là email tự động. Vui lòng không trả lời thư này.</p>
            <p>&copy; {{ date('Y') }} [Green Food].</p>
        </div>
    </div>
</body>
</html>