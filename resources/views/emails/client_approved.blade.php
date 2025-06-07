<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng của bạn đã được duyệt!</title>
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
            background-color: #28a745; /* Màu xanh lá cây */
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
            color: #28a745;
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
            background-color: #007bff; /* Màu xanh dương */
            color: #ffffff;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .cta-button:hover {
            background-color: #0056b3;
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
            <div class="icon">&#10003;</div>
            <h1>Chúc mừng! Cửa hàng của bạn đã được duyệt!</h1>
        </div>
        <div class="email-body">
            <p class="greeting">Xin chào {{ $name }},</p>
            <p>Chúng tôi rất vui mừng thông báo rằng yêu cầu đăng ký cửa hàng của bạn trên hệ thống đã được **phê duyệt** thành công!</p>
            <p>Giờ đây, bạn đã có thể đăng nhập vào tài khoản của mình và bắt đầu quản lý cửa hàng, đăng sản phẩm, và phục vụ khách hàng.</p>
            <div class="cta-button-container">
                <a href="{{ url('/login') }}" class="cta-button">Đăng nhập ngay</a>
            </div>
            <p>Nếu bạn có bất kỳ câu hỏi nào trong quá trình sử dụng, đừng ngần ngại liên hệ với đội ngũ hỗ trợ của chúng tôi.</p>
            <p style="margin-top: 25px;">Trân trọng,<br>Đội ngũ [Green Food]</p>
        </div>
        <div class="email-footer">
            <p>Đây là email tự động. Vui lòng không trả lời thư này.</p>
            <p>&copy; {{ date('Y') }} [Green Food].</p>
        </div>
    </div>
</body>
</html>