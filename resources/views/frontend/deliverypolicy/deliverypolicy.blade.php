@extends('frontend.dashboard.dashboard')

@section('dashboard')

<div class="container py-5">
    <h1 class="mb-4">Chính sách vận chuyển và giao hàng Green Food</h1>

    <h4>Quy định về phí giao hàng, khu vực giao hàng, thời gian nhận hàng</h4>

    <h5 class="mt-4">1. Phí giao hàng</h5>
    <ul>
        <li><strong>Green Food</strong> miễn phí vận chuyển đối với đơn hàng từ 300.000đ trở lên trong bán kính 5Km.</li>
        <li>Phí giao hàng: 5.000đ/Km với đơn hàng dưới 300.000đ.</li>
        <li>Phí 5.000đ/Km áp dụng cho phần vượt ngoài 5Km với đơn hàng từ 300.000đ trở lên.</li>
    </ul>

    <h5 class="mt-4">2. Khu vực giao hàng</h5>
    <ul>
        <li>Giao hàng trong bán kính 7Km tại Hà Nội, Hồ Chí Minh và các tỉnh thành khác.</li>
        <li>Giao tận nhà, trừ khu vực hạn chế như văn phòng, chung cư (chỉ giao tại chân toà nhà).</li>
        <li>Phục vụ cả Thứ 7, Chủ Nhật và ngày Lễ.</li>
        <li>Các khu vực phục vụ: An Giang, Bắc Cạn, Bắc Ninh, Hòa Bình, Hải Dương, Hà Nam, Hải Phòng, ... (liệt kê đầy đủ các tỉnh thành như bạn đưa).</li>
    </ul>

    <h5 class="mt-4">3. Thời gian giao hàng</h5>
    <ul>
        <li>Giao trong vòng 2 tiếng sau khi xác nhận. Nếu xác nhận sau 18h, giao trước 12h hôm sau.</li>
        <li>Thời gian có thể thay đổi nếu thông tin địa chỉ không chính xác, thiên tai, cấm đường, v.v.</li>
        <li>Green Food sẽ thông báo nếu có thay đổi qua điện thoại, tin nhắn hoặc email.</li>
        <li>Không giao hàng đối với đơn hàng từ 10 triệu đồng trở lên nếu nghi ngờ đầu cơ.</li>
    </ul>

    <h5 class="mt-4">4. Quy định kiểm tra hàng hóa khi giao hàng</h5>
    <ul>
        <li>Giao hàng nguyên đai, nguyên kiện. Khách có thể kiểm tra phiếu giao hàng, tình trạng đóng gói bên ngoài.</li>
        <li>Không kiểm tra sâu sản phẩm như mở seal, cắm điện, sử dụng thử, v.v.</li>
        <li>Khách ký nhận hóa đơn và giữ lại một liên.</li>
        <li>Liên hệ nếu hàng không đúng, hư hỏng, thiếu số lượng.</li>
    </ul>

    <h5 class="mt-4">5. Liên hệ hỗ trợ</h5>
    <ul>
        <li>Hotline chăm sóc khách hàng: <strong>0247 106 6866</strong> (8h - 21h)</li>
        <li>Email: <strong>cskh@greenfood.vn</strong></li>
    </ul>

    <p class="mt-4"><strong>Chân thành cảm ơn quý khách!</strong></p>
</div>

@endsection
