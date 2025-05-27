<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
<i class="fas fa-shopping-basket"></i> Giỏ Hàng
<span class="badge badge-success">{{ count((array) session('cart')) }}</span>
</a>
<div class="dropdown-menu dropdown-cart-top p-0 dropdown-menu-right shadow-sm border-0">

    @foreach ($groupedCart as $clientId => $items)
        @if (isset($clients[$clientId]))
        @php
            $client = $clients[$clientId];
        @endphp
        <div class="dropdown-cart-top-header p-4">
            <img class="img-fluid mr-3" alt="osahan" 
                    src="{{ asset('upload/client_images/' . $client->photo) }}">
            <h6 class="mb-0">{{ $client->name }}</h6>
            <p class="text-secondary mb-0">{{ $client->address }}</p>
            <small><a class="text-primary font-weight-bold" href="#">Xem Menu Chi Tiết</a></small>
        </div>
        @else

        @endif
    @endforeach

    <div class="dropdown-cart-top-body border-top p-4">
        @if (session('cart'))
        @foreach (session('cart') as $id=>$details)

            @php
                //   $total += $details['price'] * $details['quantity']
                $total += (float) $details['price'] * (int) $details['quantity'];
                
            @endphp
            
            <p class="mb-2">
                <i class="icofont-ui-press text-danger food-item"></i> 
                {{ $details['name'] }} x {{ $details['quantity'] }}   
                <span class="float-right text-secondary">
                    {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} 
                </span>
            </p>
        
        @endforeach   
        @endif
    </div>
    <div class="dropdown-cart-top-footer border-top p-4">
        <p class="mb-0 font-weight-bold text-secondary">
        Tổng Tiền 
        <span class="float-right text-dark">
            @if (Session::has('coupon'))
                {{ number_format(Session()->get('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
            @else
                {{ number_format($total, 0, ',', '.') }} VNĐ
            @endif
        </span>
        </p>
    </div>
    <div class="dropdown-cart-top-footer border-top p-2">
        <a class="btn btn-success btn-block btn-lg" href="{{ route('checkout') }}"> Checkout</a>
    </div>
</div>