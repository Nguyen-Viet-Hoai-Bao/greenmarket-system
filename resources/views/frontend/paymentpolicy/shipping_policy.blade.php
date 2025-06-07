@extends('frontend.dashboard.dashboard')
@section('dashboard')

<style>
    body {
        background-color: #f8f9fa; /* Màu nền nhẹ nhàng */
    }
    .section-container {
        max-width: 960px; /* Chiều rộng tổng thể cho các phần */
        margin: 50px auto; /* Canh giữa và tạo khoảng cách trên dưới */
        padding: 20px;
    }

    /* Styles cho Tiêu đề chung */
    .main-title {
        color: #3ecf8e; /* Màu xanh lá cây tương tự Grab/Bách Hóa Xanh */
        font-weight: bold;
        font-size: 2.2rem;
        text-align: center;
        margin-bottom: 10px;
    }
    .main-subtitle {
        color: #6c757d;
        text-align: center;
        margin-bottom: 40px;
        font-style: italic;
    }

    /* Styles cho Chính sách giao hàng */
    .delivery-policy-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 50px;
    }
    .delivery-table-header {
        background-color: #3ecf8e;
        color: white;
        font-weight: bold;
        padding: 15px 20px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        display: flex;
        justify-content: space-around;
        font-size: 1.1rem;
    }
    .delivery-table-row {
        padding: 15px 20px;
        display: flex;
        justify-content: space-around;
        border-bottom: 1px solid #dee2e6;
    }
    .delivery-table-row:last-child {
        border-bottom: none;
    }
    .delivery-table-row div {
        flex: 1;
        text-align: center;
        font-size: 1.05rem;
        padding: 5px 0;
    }

    /* Styles cho Chính sách bảo hành đổi trả */
    .return-policy-section {
        display: flex;
        justify-content: space-between;
        gap: 30px; /* Khoảng cách giữa 2 cột */
        margin-bottom: 50px;
    }
    .policy-card {
        background-color: #f0f8f0; /* Nền xanh nhạt cho card */
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1; /* Chiếm đều không gian */
        border: 1px solid #d4edda; /* Viền xanh nhạt */
    }
    .policy-card-title {
        font-size: 1.3rem;
        font-weight: bold;
        color: #343a40;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center; /* Canh giữa icon và text */
        text-align: center;
    }
    .policy-card-title i {
        font-size: 2rem;
        color: #3ecf8e; /* Icon màu xanh lá cây */
        margin-right: 10px;
    }
    .policy-item {
        display: flex;
        align-items: flex-start; /* Icon và text thẳng hàng ở đầu */
        margin-bottom: 15px;
        line-height: 1.5;
    }
    .policy-item i.fa-check-circle {
        color: #3ecf8e; /* Icon check màu xanh */
        margin-right: 10px;
        font-size: 1.2rem;
        flex-shrink: 0; /* Ngăn icon bị co lại */
    }
    .policy-item span {
        color: #343a40;
        font-size: 1.05rem;
    }

    .time-tag {
        display: inline-block;
        background-color: #3ecf8e; /* Màu xanh lá cây cho tag thời gian */
        color: white;
        padding: 5px 12px;
        border-radius: 20px; /* Bo tròn tag */
        font-size: 0.95rem;
        font-weight: bold;
        margin-right: 10px;
        min-width: 70px; /* Đảm bảo chiều rộng tối thiểu cho tag */
        text-align: center;
    }

    /* Styles cho Cam kết dịch vụ */
    .commitment-section {
        text-align: center;
        margin-bottom: 50px;
    }
    .commitment-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #3ecf8e;
        margin-bottom: 20px;
        display: inline-flex; /* Để icon và text cùng nằm trên một hàng */
        align-items: center;
        padding: 5px 20px;
        border: 1px dashed #3ecf8e; /* Viền nét đứt */
        border-radius: 5px;
    }
    .commitment-title i {
        font-size: 1.8rem;
        margin-right: 10px;
        color: #3ecf8e;
    }
    .commitment-text {
        font-size: 1.2rem;
        color: #343a40;
        padding: 20px;
        border: 1px dashed #3ecf8e;
        border-radius: 8px;
        display: inline-block; /* Để border bao quanh text vừa đủ */
        max-width: 600px;
        margin-top: 15px;
    }
    .commitment-text strong {
        color: #3ecf8e; /* Màu xanh cho phần quan trọng */
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .return-policy-section {
            flex-direction: column; /* Chuyển thành cột trên màn hình nhỏ */
            gap: 20px;
        }
        .policy-card {
            padding: 20px;
        }
        .policy-card-title {
            font-size: 1.2rem;
        }
        .policy-card-title i {
            font-size: 1.8rem;
        }
        .policy-item span {
            font-size: 1rem;
        }
        .main-title {
            font-size: 1.8rem;
        }
        .commitment-title {
            font-size: 1.3rem;
        }
        .commitment-text {
            font-size: 1.1rem;
        }
    }
</style>
<div class="section-container">
    <h1 class="main-title">CHÍNH SÁCH GIAO HÀNG</h1>
    <p class="main-subtitle">- Áp dụng từ ngày 01/04/2024 -</p>

    <div class="delivery-policy-card">
        <div class="delivery-table-header">
            <div>Giá trị đơn hàng</div>
            <div>Phí giao hàng</div>
        </div>
        <div class="delivery-table-row">
            <div>>=100.000đ</div>
            <div>Miễn phí giao</div>
        </div>
        <div class="delivery-table-row">
            <div>< 100.000đ</div>
            <div>15.000đ/đơn</div>
        </div>
    </div>

    <h1 class="main-title">CHÍNH SÁCH BẢO HÀNH ĐỔI TRẢ</h1>
    <p class="main-subtitle">- Cập nhật gần nhất: 01/06/2025 -</p> <div class="return-policy-section">
        <div class="policy-card">
            <h3 class="policy-card-title">
                <i class="fas fa-undo-alt"></i> Đổi trả hàng trong các trường hợp
            </h3>
            <div class="policy-item">
                <i class="fas fa-check-circle"></i> <span>Hàng bị rách bao bì hoặc hư hỏng trong quá trình giao.</span>
            </div>
            <div class="policy-item">
                <i class="fas fa-check-circle"></i> <span>Hàng hư hỏng do vi khuẩn hoặc côn trùng xâm nhập.</span>
            </div>
            <div class="policy-item">
                <i class="fas fa-check-circle"></i> <span>Hàng hết hạn sử dụng.</span>
            </div>
        </div>

        <div class="policy-card">
            <h3 class="policy-card-title">
                <i class="fas fa-clock"></i> Thời gian áp dụng
            </h3>
            <div class="policy-item">
                <span class="time-tag">1 ngày</span> <span>Hàng đông lạnh, thịt cá, hải sản.</span>
            </div>
            <div class="policy-item">
                <span class="time-tag">2 ngày</span> <span>Trái cây, rau củ tươi.</span>
            </div>
            <div class="policy-item">
                <span class="time-tag">7 ngày</span> <span>Hàng còn lại.</span>
            </div>
        </div>
    </div>

    <div class="commitment-section">
        <div class="commitment-title">
            <i class="fas fa-award"></i> CAM KẾT DỊCH VỤ
        </div>
        <p class="commitment-text">
            Đền ngay <strong style="color: #3ecf8e;">50.000đ</strong> mua hàng tại web <strong style="color: #3ecf8e;">GREEN FOOD</strong> nếu giao trễ
        </p>
    </div>

</div>
@endsection
