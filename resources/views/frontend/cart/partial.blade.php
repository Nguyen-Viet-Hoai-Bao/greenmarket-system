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
                        <p class="mt-1 mb-0 text-black">{{ $details['name'] }}</p>
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
            Tổng tiền sau giảm giá
            <span class="float-right text-success">
                {{ number_format($total - session('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
            </span>
        </p>
        <hr />
        <h6 class="font-weight-bold mb-0">
            SỐ TIỀN CẦN THANH TOÁN
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

<div class="mb-2 bg-white rounded p-2 clearfix">
    <img class="img-fluid float-left" src="{{ asset('frontend/img/wallet-icon.png') }}">
    <h6 class="font-weight-bold text-right mb-2">
        Tạm tính :
        <span class="text-danger">
            @if (Session::has('coupon'))
                {{ number_format(session('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
            @else
                {{ number_format($total, 0, ',', '.') }} VNĐ
            @endif
        </span>
    </h6>
    <p class="seven-color mb-1 text-right">Phụ phí có thể được áp dụng</p>
</div>

<a href="{{ route('checkout') }}" class="btn btn-success btn-block btn-lg">
    Thanh toán <i class="icofont-long-arrow-right"></i>
</a>
