@extends('frontend.dashboard.dashboard')
@section('dashboard')

    <div class="container my-5">
        <h1 class="text-center mb-4">Chính Sách Thanh Toán Green Food</h1>
        
        <h3>1. Các hình thức thanh toán</h3>
        <p>Khách hàng có thể lựa chọn bất kỳ hình thức thanh toán nào dưới đây để thanh toán cho đơn hàng của mình khi mua sản phẩm trên website Green Food.</p>

        <h4>1.1. Thanh toán trả trước</h4>
        <p>Là hình thức thanh toán trực tuyến mà khách hàng sử dụng để thanh toán cho đơn hàng, bao gồm:</p>
        <ul>
            <li>Mã giảm giá;</li>
            <li>Thẻ ATM (Thẻ ghi nợ/thanh toán/trả trước nội địa);</li>
            <li>Thẻ thanh toán quốc tế, thẻ tín dụng. (Visa, Master, JCB, Amex, UnionPay…)</li>
        </ul>

        <h4>1.2 Thanh toán trả sau</h4>
        <p>Là hình thức mà khách hàng sử dụng để thanh toán cho đơn hàng khi Green Food giao hàng và chỉ áp dụng với hình thức thanh toán bằng tiền mặt.</p>

        <h4>1.3. Việc kết hợp thanh toán</h4>
        <p>Khách hàng chỉ có thể kết hợp thanh toán Mã giảm giá với một trong các hình thức thanh toán còn lại.</p>

        <h3>2. Chi tiết các hình thức thanh toán</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Hình thức thanh toán</th>
                    <th scope="col">Số lần sử dụng tối đa cho 1 đơn hàng</th>
                    <th scope="col">Chi tiết</th>
                    <th scope="col">Điều kiện để thanh toán được chấp nhận</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Thanh toán bằng tiền mặt</td>
                    <td>Theo lần giao hàng</td>
                    <td>Thu tiền mặt khi giao hàng</td>
                    <td>Tiền Việt Nam Đồng thật và đủ tiêu chuẩn lưu thông theo quy định của Ngân hàng Nhà nước Việt Nam.</td>
                </tr>
                <tr>
                    <td>Mã giảm giá</td>
                    <td>01 mã</td>
                    <td>Là một dạng mã code có giá trị như tiền mặt và được sử dụng trong giao dịch thanh toán hóa đơn hàng hóa, sản phẩm tại Green Food</td>
                    <td>Mã còn hạn & chưa bị sử dụng ở bất kỳ đơn hàng nào trên Green Food. Khách hàng có thể sử dụng để giảm trừ trên tổng giá trị mua hàng hiện tại.</td>
                </tr>
                <tr>
                    <td>Biên nhận thanh toán</td>
                    <td>01 mã</td>
                    <td>Trong trường hợp đổi, trả hàng tại siêu thị, Green Food phát hành 1 BNTT để áp dụng cho 1 lần thanh toán tiếp theo.</td>
                    <td>Biên nhận còn giá trị sử dụng (15 ngày); mã chưa được sử dụng tại bất cứ siêu thị/cửa hàng nào tại chuỗi Green Food.</td>
                </tr>
                <tr>
                    <td>Thẻ ATM (Thẻ ghi nợ/thanh toán /trả trước nội địa)</td>
                    <td>01 thẻ</td>
                    <td>Thẻ ghi nợ/thanh toán/trả trước nội địa của các ngân hàng trong nước phát hành có kết nối với cổng thanh toán Onepay.</td>
                    <td>Thẻ được đăng ký tính năng thanh toán trực tuyến và giao dịch phải được ghi nhận thành công từ hệ thống cổng thanh toán.</td>
                </tr>
                <tr>
                    <td>Thẻ tín dụng, thẻ thanh toán quốc tế</td>
                    <td>01 thẻ</td>
                    <td>Thẻ tín dụng/ghi nợ/trả trước VISA, MasterCard, JCB, UnionPay, Amex có kết nối với cổng thanh toán Onepay.</td>
                    <td>Thẻ đủ điều kiện thanh toán trực tuyến và giao dịch phải được ghi nhận thành công từ cổng thanh toán.</td>
                </tr>
            </tbody>
        </table>

        <h3>3. Quy định về hoàn tiền</h3>
        <p>Hoàn tiền được thực hiện khi Khách hàng đã thanh toán trên Green Food nhưng sau đó phát sinh các vấn đề như:</p>
        <ul>
            <li>Hết hàng nên không thể giao đủ số lượng và mặt hàng trong Đơn đặt hàng của Khách hàng;</li>
            <li>Đổi, trả hàng theo đúng Chính sách đổi trả;</li>
            <li>Hủy hàng thành công trước khi giao hàng;</li>
            <li>Đơn hàng không thành công nhưng hệ thống vẫn trừ tiền của Khách hàng.</li>
        </ul>
        <p>Số tiền Khách hàng được hoàn sẽ không lớn hơn số tiền đã thanh toán (không bao gồm giá trị Mã giảm giá).</p>

        <h4>Hình thức hoàn trả:</h4>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Hình thức thanh toán</th>
                    <th scope="col">Hình thức hoàn trả</th>
                    <th scope="col">Thời gian (ngày làm việc)</th>
                    <th scope="col">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Mã giảm giá</td>
                    <td>Mã giảm giá</td>
                    <td>1 ngày</td>
                    <td>Áp dụng khi đơn hàng có sử dụng Mã giảm giá nhưng Green Food không có đủ hàng.</td>
                </tr>
                <tr>
                    <td>Cổng thanh toán Onepay</td>
                    <td>Thẻ ngân hàng</td>
                    <td>5-11 ngày</td>
                    <td>Thẻ tín dụng, thẻ thanh toán quốc tế.</td>
                </tr>
                <tr>
                    <td>Thẻ ATM (Thẻ ghi nợ/thanh toán /trả trước nội địa)</td>
                    <td>Thẻ ngân hàng</td>
                    <td>3-7 ngày</td>
                    <td>Giao dịch được ghi nhận thành công từ hệ thống cổng thanh toán.</td>
                </tr>
                <tr>
                    <td>Mọi hình thức thanh toán</td>
                    <td>Biên nhận thanh toán</td>
                    <td>Ngay lập tức</td>
                    <td>Nhân viên tại siêu thị cấp cho khách hàng khi hoàn tất thủ tục đổi trả.</td>
                </tr>
            </tbody>
        </table>

    </div>
@endsection
