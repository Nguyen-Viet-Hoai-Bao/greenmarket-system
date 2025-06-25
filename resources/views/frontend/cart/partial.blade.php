<h5 class="mb-1 text-white">Đơn Hàng Của Bạn</h5>
<p class="mb-4 text-white">{{ count((array) session('cart')) }} Sản phẩm</p>

<div class="bg-white rounded shadow-sm mb-2">
    @php $total = 0; @endphp

    @if (session('cart'))
        @foreach (session('cart') as $id => $details)
            @php
                $total += (float) $details['price'] * (int) $details['quantity'];
            @endphp

            <div class="gold-members p-2 border-bottom">
                <p class="text-gray mb-0 float-right ml-2 item-total" id="item-total-{{ $id }}">
                    {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
                </p>

                <span class="count-number float-right">
                    <button
                      class="btn btn-outline-secondary btn-sm left btn-change-qty"
                      data-id="{{ $id }}"
                      data-qty="{{ $details['quantity'] - 1 }}"
                    >
                      <i class="icofont-minus"></i>
                    </button>

                    <span class="qty-display" data-id="{{ $id }}">{{ $details['quantity'] }}</span>

                    <button
                      class="btn btn-outline-secondary btn-sm right btn-change-qty"
                      data-id="{{ $id }}"
                      data-qty="{{ $details['quantity'] + 1 }}"
                    >
                        <i class="icofont-plus"></i>
                    </button>

                    <button class="btn btn-outline-danger btn-sm right btn-remove" data-id="{{ $id }}">
                        <i class="icofont-trash"></i>
                    </button>
                </span>

                <div class="media">
                    <div class="mr-2">
                        <img src="{{ asset($details['image']) }}" alt="" width="25px">
                    </div>
                    <div class="media-body">
                        {{-- Liên kết mở Modal chi tiết sản phẩm --}}
                        <p class="mt-1 mb-0 text-black">
                            <a href="#" class="product-name-link" data-product-id="{{ $details['id'] }}" style="text-decoration: none; color: inherit; cursor: pointer;">
                                {{ $details['name'] }}
                            </a>
                        </p>
                        @if (($details['display_mode'] ?? null) === 'unit')
                            <small class="text-muted">
                                {{ $details['weight'] ?? 'N/A' }} KG - {{ isset($details['expiry_date']) ? \Carbon\Carbon::parse($details['expiry_date'])->format('d/m/Y') : 'N/A' }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@if (Session::has('coupon'))
    <div class="mb-2 bg-white rounded p-2 clearfix">
        <p class="mb-1">
            Tổng số sản phẩm
            <span class="float-right text-dark">{{ count((array) session('cart')) }}</span>
        </p>
        <p class="mb-1">
            Mã giảm giá
            <span class="float-right text-dark">
                {{ session('coupon')['coupon_name'] }} ({{ session('coupon')['discount'] }}%)
                <a onclick="CouponRemove()">
                    <i class="icofont-ui-delete float-right" style="color: red;"></i>
                </a>
            </span>
        </p>
        <p class="mb-1 text-success">
            Tổng tiền giảm giá
            <span class="float-right text-success">
                {{ number_format($total - session('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
            </span>
        </p>
        <hr />
        <h6 class="font-weight-bold mb-0">
            SỐ TIỀN SAU GIẢM GIÁ
            <span class="float-right">
                {{ number_format(session('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
            </span>
        </h6>
    </div>
@else
    <div class="mb-2 bg-white rounded p-2 clearfix">
        <div class="input-group input-group-sm mb-2">
            <input type="text" class="form-control" placeholder="Enter promo code" id="coupon_name">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit" id="button-addon2" onclick="ApplyCoupon()">
                    <i class="icofont-sale-discount"></i> ÁP DỤNG
                </button>
            </div>
        </div>
    </div>
@endif
<div class="mb-2 bg-white rounded p-2 d-flex align-items-center">
    <i class="fas fa-shipping-fast fa-2x mr-2" style="color: #3ecf8e ;"></i> <p class="mb-0 flex-grow-1 text-right">
        <a href="{{ route('shipping.policy') }}" target="_blank">Phí giao hàng: </a>
        <strong class="text-dark">
            {{ number_format(Session::get('shipping_fee', 0), 0, ',', '.') }} VNĐ
        </strong>
    </p>
</div>
<div class="mb-2 bg-white rounded p-2 clearfix">
    <img class="img-fluid float-left" src="{{ asset('frontend/img/wallet-icon.png') }}">
    <h6 class="font-weight-bold text-right mb-2">
        Tạm tính :
        <span class="text-danger">
            @if (Session::has('coupon'))
                {{ number_format(session('coupon')['discount_amount'] + session('shipping_fee'), 0, ',', '.') }} VNĐ
            @else
                {{ number_format($total + session('shipping_fee'), 0, ',', '.') }} VNĐ
            @endif
        </span>
    </h6>
    <p class="seven-color mb-1 text-right">Phụ phí có thể được áp dụng</p>
</div>

<a href="{{ route('checkout') }}" class="btn btn-success btn-block btn-lg">
    Thanh toán <i class="icofont-long-arrow-right"></i>
</a>

{{-- Modal Chi tiết sản phẩm (Đặt ở cuối file blade này hoặc trong layout chính) --}}
<div class="modal fade" id="productDetailsModal" tabindex="-1" role="dialog" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailsModalLabel">Chi tiết sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="product-modal-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p>Đang tải thông tin sản phẩm...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.product-name-link').on('click', function(e) {
            e.preventDefault(); // Chặn hành vi mặc định của thẻ <a> (không điều hướng)

            var productId = $(this).data('product-id'); // Lấy ID sản phẩm từ thuộc tính data-product-id
            var modalContent = $('#product-modal-content');

            // Hiển thị trạng thái tải ban đầu trong modal
            modalContent.html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang tải thông tin sản phẩm...</p>
                </div>
            `);

            // Mở modal
            $('#productDetailsModal').modal('show');

            // Gửi yêu cầu AJAX đến server để lấy thông tin sản phẩm
            $.ajax({
                url: '/api/product-details/' + productId, // Sử dụng route API bạn đã định nghĩa
                method: 'GET',
                success: function(data) {
                    // Xây dựng nội dung HTML để hiển thị trong modal
                    var htmlContent = `
                        <div class="text-center mb-3">
                            <img src="${data.image ? data.image : 'https://via.placeholder.com/150?text=No+Image'}" alt="${data.name}" class="img-fluid rounded" style="max-width: 180px;">
                        </div>
                        <h5 class="font-weight-bold">${data.name}</h5>
                        <p><strong>Giá:</strong> <span class="text-danger">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.price)}</span></p>
                    `;
                    
                    if (data.display_mode === 'unit') {
                        htmlContent += `<p><strong>Khối lượng:</strong> <span style="color: red; font-weight: bold;">${data.selected_unit.weight ?? 'N/A'} KG</span></p>`;
                        htmlContent += `<p><strong>Ngày hết hạn:</strong> <span style="color: red; font-weight: bold;">${data.selected_unit.expiry_date ? data.selected_unit.expiry_date : 'N/A'}</span></p>`;
                    } 

                    if (data.display_mode != 'unit') {
                        htmlContent += `<p><strong>Ngày hết hạn:</strong> <span style="color: red; font-weight: bold;">${data.selected_unit.expiry_date ? data.selected_unit.expiry_date : 'N/A'}</span></p>`;
                    } 

                    if (data.description) {
                        htmlContent += `<p><strong>Mô tả:</strong> ${data.description}</p>`;
                    }
                    
                    if (data.product_info) {
                        htmlContent += `<p><strong>Thông tin sản phẩm:</strong> ${data.product_info}</p>`;
                    }
                    if (data.note) {
                        htmlContent += `<p><strong>Lưu ý:</strong> ${data.note}</p>`;
                    }
                    if (data.origin) {
                        htmlContent += `<p><strong>Xuất xứ:</strong> ${data.origin}</p>`;
                    }
                    if (data.preservation) {
                        htmlContent += `<p><strong>Bảo quản:</strong> ${data.preservation}</p>`;
                    }
                    if (data.usage_instructions) {
                        htmlContent += `<p><strong>Hướng dẫn sử dụng:</strong> ${data.usage_instructions}</p>`;
                    }
                    
                    modalContent.html(htmlContent);
                },
                error: function(xhr, status, error) {
                    // Xử lý lỗi nếu có
                    modalContent.html('<p class="text-danger text-center">Không thể tải thông tin sản phẩm. Vui lòng thử lại sau.</p>');
                    console.error('AJAX Error: ', status, error, xhr.responseText);
                }
            });
        });
    });
</script>