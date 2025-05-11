@extends('frontend.dashboard.dashboard')
@section('dashboard')

<div class="container py-5">
    <h1 class="mb-4">Chính sách giao dịch</h1>
    <p><strong>Green Food</strong> cam kết xây dựng môi trường giao dịch minh bạch, an toàn và hiệu quả, đảm bảo quyền lợi của cả người mua và người bán. Dưới đây là các quy định và chính sách cụ thể liên quan đến quá trình giao dịch.</p>

    <h4>1. Quy trình giao dịch</h4>
    <p>Khách hàng thực hiện giao dịch theo các bước sau:</p>
    <ol>
        <li>Chọn sản phẩm và thêm vào giỏ hàng.</li>
        <li>Tiến hành đặt hàng và điền thông tin cá nhân đầy đủ.</li>
        <li>Chọn hình thức thanh toán phù hợp.</li>
        <li>Xác nhận đơn hàng qua email hoặc điện thoại.</li>
        <li>Nhận hàng và thanh toán (nếu thanh toán sau).</li>
    </ol>

    <h4>2. Hình thức thanh toán</h4>
    <p>Green Food hỗ trợ các phương thức thanh toán sau:</p>
    <ul>
        <li>Thanh toán tiền mặt khi nhận hàng (COD).</li>
        <li>Chuyển khoản ngân hàng theo thông tin do Green Food cung cấp.</li>
        <li>Thanh toán trực tuyến qua cổng thanh toán điện tử (ZaloPay, Momo, VNPay...).</li>
        <li>Thanh toán bằng thẻ tín dụng hoặc thẻ ghi nợ quốc tế (Visa, MasterCard, v.v.) nếu được hỗ trợ.</li>
    </ul>

    <h4>3. Xác nhận và xử lý đơn hàng</h4>
    <ul>
        <li>Sau khi đặt hàng, hệ thống sẽ tự động gửi email xác nhận.</li>
        <li>Nhân viên Green Food sẽ liên hệ để xác minh thông tin và tiến hành xử lý đơn hàng.</li>
        <li>Đơn hàng sẽ được xử lý trong vòng 24h làm việc kể từ thời điểm xác nhận.</li>
    </ul>

    <h4>4. Hủy đơn hàng</h4>
    <ul>
        <li>Khách hàng có thể yêu cầu hủy đơn trước khi đơn được giao hoặc trong vòng 2 giờ sau khi đặt hàng.</li>
        <li>Green Food có quyền hủy đơn hàng trong các trường hợp sau:
            <ul>
                <li>Thông tin khách hàng không chính xác hoặc không liên hệ được.</li>
                <li>Có dấu hiệu gian lận hoặc vi phạm điều khoản sử dụng.</li>
                <li>Hết hàng hoặc lý do bất khả kháng khác.</li>
            </ul>
        </li>
    </ul>

    <h4>5. Bảo mật thông tin giao dịch</h4>
    <p>Green Food đảm bảo:</p>
    <ul>
        <li>Bảo mật tuyệt đối thông tin cá nhân và giao dịch của khách hàng.</li>
        <li>Không tiết lộ, chia sẻ thông tin cho bên thứ ba nếu không có sự đồng ý của khách hàng.</li>
        <li>Sử dụng các biện pháp mã hóa và bảo mật dữ liệu hiện đại để chống truy cập trái phép.</li>
    </ul>

    <h4>6. Khiếu nại và giải quyết tranh chấp</h4>
    <ul>
        <li>Khách hàng có thể gửi khiếu nại qua email hoặc hotline trong vòng 7 ngày kể từ khi phát sinh sự cố.</li>
        <li>Green Food sẽ tiếp nhận và phản hồi trong vòng tối đa 3 ngày làm việc.</li>
        <li>Thời gian xử lý khiếu nại tối đa là 7 ngày làm việc.</li>
        <li>Trong trường hợp cần thiết, các bên có thể đưa vụ việc ra cơ quan có thẩm quyền giải quyết theo pháp luật Việt Nam.</li>
    </ul>

    <h4>7. Liên hệ hỗ trợ</h4>
    <p>Để được giải đáp thắc mắc hoặc hỗ trợ giao dịch, vui lòng liên hệ:</p>
    <ul>
        <li>Hotline: 1900 1234</li>
        <li>Email: support@greenfood.vn</li>
        <li>Thời gian làm việc: 08:00 - 17:00 từ Thứ 2 đến Thứ 7</li>
    </ul>
</div>

@endsection
