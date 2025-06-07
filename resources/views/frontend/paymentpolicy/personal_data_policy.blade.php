@extends('frontend.dashboard.dashboard')
@section('dashboard')
<style>
    /* Nhấn mạnh các điểm quan trọng */
    strong {
        color: #e74c3c;
    }

    ol > li {
        margin-bottom: 15px;
    }

    ul > li {
        margin-bottom: 8px;
    }

    a {
        color: #2980b9;
        text-decoration: underline;
    }

    a:hover {
        color: #1c5980;
    }
</style>
<div class="container py-5" style="max-width: 900px;">
    <h1 class="text-center mb-4" style="color: #2c3e50; font-weight: 700;">
        CHÍNH SÁCH XỬ LÝ DỮ LIỆU CÁ NHÂN
    </h1>
    <p class="text-center text-muted mb-5"><em>(Áp dụng từ ngày 01/01/2024)</em></p>

    <p>
        Trong quá trình Khách Hàng giao dịch tại website/ứng dụng hoặc tại cửa hàng của 
        <strong>Công ty Cổ phần Thương mại Green Food</strong> (sau đây gọi là <span style="color:#e74c3c; font-weight: 600;">“Hệ Thống”</span>), 
        <strong>Green Food</strong> có thể thu thập một số dữ liệu cá nhân của Quý Khách Hàng theo các điều khoản dưới đây:
    </p>

    <h3 class="mt-5" style="color: #34495e; font-weight: 600;">1. Phạm vi Dữ liệu cá nhân thu thập:</h3>

    <ol>
        <li class="mb-3">
            <strong>Để mua hàng tại Hệ Thống</strong> (tạo đơn hàng, để lại bình luận đánh giá, liên hệ với chúng tôi, v.v), Quý Khách Hàng có thể sẽ được yêu cầu cung cấp các thông tin:
            <ul style="list-style-type: disc; margin-left: 20px;">
                <li>Email</li>
                <li>Họ tên</li>
                <li>Số điện thoại</li>
                <li>Địa chỉ</li>
                <li>Dữ liệu cá nhân khác cần thiết</li>
            </ul>
            <span style="color: #e74c3c; font-weight: 600;">Mọi thông tin khai báo phải đảm bảo tính chính xác và hợp pháp.</span> 
            Green Food không chịu trách nhiệm trong trường hợp thông tin Khách Hàng cung cấp là không chính xác hoặc vi phạm pháp luật.
        </li>

        <li>
            Trong trường hợp Quý Khách Hàng giao dịch tại website/ứng dụng của Green Food, Hệ Thống cũng có thể thu thập:
            <ul style="list-style-type: disc; margin-left: 20px;">
                <li>Số lần ghé thăm</li>
                <li>Số trang Quý Khách Hàng xem</li>
                <li>Số links (liên kết) mà Khách Hàng click</li>
                <li>Thông tin kết nối khác đến site Green Food</li>
            </ul>
            Ngoài ra, chúng tôi thu thập các thông tin trình duyệt Web mà Khách Hàng sử dụng khi truy cập Hệ Thống, bao gồm:
            <ul style="list-style-type: disc; margin-left: 20px;">
                <li>Địa chỉ IP</li>
                <li>Loại Browser</li>
                <li>Ngôn ngữ sử dụng</li>
                <li>Thời gian truy cập</li>
                <li>Địa chỉ mà Browser truy xuất đến</li>
            </ul>
        </li>
    </ol>

    <h3 class="mt-5" style="color: #34495e; font-weight: 600;">2. Mục đích sử dụng Dữ liệu cá nhân:</h3>
    <p>Các Dữ liệu cá nhân thu thập được sử dụng trong các mục đích sau:</p>
    <ol>
        <li><strong>Xử lý đơn hàng:</strong> Gọi điện, tin nhắn xác nhận đơn hàng, thông báo trạng thái và thời gian giao hàng, xác nhận huỷ đơn (nếu có).</li>
        <li><strong>Gửi thư ngỏ, thư cảm ơn, giới thiệu sản phẩm/dịch vụ mới, chương trình khuyến mại.</strong></li>
        <li><strong>Giải quyết khiếu nại.</strong></li>
        <li><strong>Thực hiện chương trình khuyến mại:</strong> Do Green Food hoặc phối hợp với bên thứ ba, bao gồm trao thưởng.</li>
        <li><strong>Khảo sát chăm sóc Khách Hàng.</strong></li>
        <li><strong>Xác minh danh tính và đảm bảo bảo mật dữ liệu cá nhân.</strong></li>
        <li><strong>Cung cấp dữ liệu theo yêu cầu của cơ quan nhà nước có thẩm quyền hoặc theo quy định pháp luật.</strong></li>
        <li><strong>Mục đích hợp lý khác nhằm phục vụ yêu cầu Khách Hàng.</strong></li>
    </ol>

    <h3 class="mt-5" style="color: #34495e; font-weight: 600;">3. Thời gian lưu trữ Dữ liệu cá nhân:</h3>
    <p>Dữ liệu được lưu trữ cho đến khi Khách Hàng yêu cầu xóa, trừ khi pháp luật quy định không được xóa. Mọi Dữ liệu cá nhân được bảo mật và lưu trên máy chủ của Green Food.</p>

    <h3 class="mt-5" style="color: #34495e; font-weight: 600;">4. Các bên có thể tiếp cận Dữ liệu cá nhân của Quý Khách Hàng:</h3>
    <p>Green Food cam kết bảo mật và chỉ chia sẻ với các bên sau nhằm phục vụ mục đích nêu tại mục 2:</p>
    <ol>
        <li>Đối tác giao hàng, viễn thông, dịch vụ tin nhắn, bảo hiểm, thương nhân khuyến mại, đối tác phối hợp chương trình khuyến mại.</li>
        <li>Công ty liên kết của Green Food (công ty mẹ, con, cùng kiểm soát).</li>
        <li>Luật sư, cố vấn, đơn vị kiểm toán của Green Food.</li>
        <li>Bên xử lý dữ liệu theo hợp đồng cung cấp dịch vụ.</li>
        <li>Các bên thứ ba khác khi được Khách Hàng đồng ý.</li>
    </ol>

    <h3 class="mt-5" style="color: #34495e; font-weight: 600;">5. Cách thức xử lý Dữ liệu cá nhân:</h3>
    <p>Dữ liệu cá nhân thu thập thông qua hoạt động của Khách Hàng trên Hệ Thống được lưu trữ tại hệ thống bên thứ ba và được bảo đảm an toàn dưới sự quản lý của Green Food.</p>

    <h3 class="mt-5" style="color: #34495e; font-weight: 600;">6. Quyền của Khách Hàng đối với Dữ liệu cá nhân:</h3>
    <p>Khách Hàng có các quyền theo quy định pháp luật:</p>
    <ol>
        <li>
            <strong>Quyền truy cập, chỉnh sửa hoặc xóa Dữ liệu cá nhân:</strong>
            <ul style="list-style-type: disc; margin-left: 20px;">
                <li>Đăng nhập trang quản lý thông tin để chỉnh sửa hoặc xóa.</li>
                <li>Gọi tổng đài 1900 4242 hoặc 0786 4242 42 để được hỗ trợ.</li>
                <li>Gửi bình luận hoặc góp ý trực tiếp trên Hệ Thống, Green Food sẽ xác minh và liên hệ lại.</li>
            </ul>
        </li>
        <li>
            <strong>Quyền khác:</strong> Gửi email yêu cầu kèm văn bản về địa chỉ <a href="mailto:greenfood@gmail.com">greenfood@gmail.com</a>. Green Food sẽ thông báo các hậu quả có thể xảy ra trước khi thực hiện yêu cầu.
        </li>
    </ol>

    <h3 class="mt-5" style="color: #34495e; font-weight: 600;">7. Hậu quả, thiệt hại không mong muốn khi xử lý Dữ liệu cá nhân:</h3>
    <ol>
        <li><strong>Hậu quả:</strong> Dữ liệu cá nhân bị tiết lộ cho chủ thể khác mà chưa được sự đồng ý của Khách Hàng, hoặc bị sử dụng vào mục đích khác.</li>
        <li><strong>Thiệt hại có thể xảy ra:</strong> Mất tài sản, danh dự, uy tín bị xâm phạm.</li>
        <li><strong>Hậu quả khác:</strong> Các nguyên nhân khách quan mà Green Food không thể lường trước.</li>
    </ol>

    <h3 class="mt-5" style="color: #34495e; font-weight: 600;">8. Điều khoản chung:</h3>
    <p>
        Green Food có quyền chỉnh sửa, cập nhật Chính Sách này bất kỳ lúc nào. Bản cập nhật sẽ được công bố trên Hệ Thống và có ghi ngày để Khách Hàng nhận biết.
    </p>
</div>

@endsection
