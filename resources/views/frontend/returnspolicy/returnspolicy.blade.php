@extends('frontend.dashboard.dashboard')
@section('dashboard')
<div class="container mt-5">
    <h2>CHÍNH SÁCH ĐỔI TRẢ SẢN PHẨM - Green Food</h2>

    <h3>1. Quy định chung</h3>
    <p>
        - Chính sách đổi/ trả hàng được áp dụng trong vòng 07 ngày kể từ ngày mua hàng.<br>
        - Khách hàng có quyền đổi hoặc trả khi mua phải hàng hóa kém chất lượng, hỏng hóc, quá hạn sử dụng hoặc do lỗi kỹ thuật khi thao tác của nhân viên Green Food.<br>
        - Khách hàng có thể đổi hoặc trả hàng do thay đổi ý định mua hàng đối với những hàng hóa không thuộc “Nhóm hàng hóa không áp dụng đổi/ trả” dưới đây nếu đáp ứng điều kiện đổi sản phẩm quy định tại Mục 3.<br>
    </p>

    <h3>2. Nhóm hàng hóa không áp dụng đổi/ trả:</h3>
    <ul>
        <li>Hàng điện, điện tử.</li>
        <li>Mỹ Phẩm, phụ kiện, trang phục lót, đồ bơi, đồ tập thể dục.</li>
        <li>Rượu, thuốc lá.</li>
        <li>Thực phẩm tươi sống/ đông lạnh/ bảo quản lạnh.</li>
        <li>Sản phẩm sơ chế hoặc nấu chín, hoặc thực phẩm có hạn sử dụng dưới 07 ngày.</li>
        <li>Hàng tặng, hàng khuyến mại, hàng thanh lý (có thông báo chính thức tại quầy hàng)</li>
        <li>Sản phẩm hư hỏng do không tuân thủ hướng dẫn sử dụng hoặc bảo quản của nhà sản xuất.</li>
    </ul>

    <h3>3. Điều kiện đổi – Trả hàng:</h3>
    <p>
        - Hàng hóa đổi – trả chỉ được thực hiện tại quầy DVKH của siêu thị Green Food đã tiến hành xuất hàng cho Khách hàng mua hàng trước đó (thông tin siêu thị đã xuất bán thể hiện trên Hóa đơn mua hàng).<br>
        - Hàng hóa được đổi/ trả bằng đúng với giá của khách hàng đã mua khi được xuất trình cùng với các chứng từ liên quan (Bản gốc hóa đơn bán lẻ/hóa đơn GTGT).<br>
        - Điểm tích lũy tương ứng với giá trị hàng trả sẽ khấu trừ trên hệ thống.<br>
        - Điều kiện bắt buộc về hàng hóa để đổi/ trả khi khách hàng thay đổi ý định mua hàng:<br>
        <ul>
            <li>Sản phẩm phải còn trong điều kiện tốt (không vỡ hỏng hóc), còn nguyên tem/nhãn không bị móp, rách và có thể bán lại được.</li>
            <li>Các bộ phận, phụ kiện chi tiết khác đính kèm sản phẩm, tem/phiếu bảo hành, hướng dẫn kỹ thuật…phải còn đầy đủ và nguyên vẹn.</li>
            <li>Sản phẩm không bị dính bẩn, trầy xước có dấu hiệu đã qua giặt tẩy hoặc có mùi lạ.</li>
        </ul>
    </p>

    <h3>4. Các trường hợp đổi/ trả:</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Trường hợp</th>
                <th>Đổi hàng</th>
                <th>Trả hàng</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Hàng hóa kém chất lượng, hỏng hóc, quá hạn sử dụng hoặc lỗi kỹ thuật.</td>
                <td>“Một đổi một” theo yêu cầu của khách hàng</td>
                <td>Khách hàng được hoàn trả bằng biên nhận thanh toán (chỉ sử dụng để thanh toán khi mua hàng ở siêu thị trong vòng 15 ngày kể từ ngày cấp phát)</td>
            </tr>
            <tr>
                <td>Khách hàng thay đổi quyết định mua hàng</td>
                <td>Đổi màu/ kích cỡ cùng mã hàng: thực hiện “Một đổi một”</td>
                <td>Khách hàng được hoàn trả bằng biên nhận thanh toán (chỉ sử dụng để thanh toán khi mua hàng ở siêu thị trong vòng 15 ngày kể từ ngày cấp phát)</td>
            </tr>
        </tbody>
    </table>

    <p>
        Trong mọi trường hợp, Green Food sẽ không hoàn lại Mã giảm giá mà Khách hàng đã sử dụng để thanh toán cho đơn hàng đó.
    </p>
</div>
@endsection
