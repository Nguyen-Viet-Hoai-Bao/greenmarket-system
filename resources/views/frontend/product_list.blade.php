<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

@foreach ($filterProducts as $product)
<div class="col-md-3 col-sm-6 mb-4 pb-2">
    <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm d-flex flex-column">
        <div class="list-card-image position-relative">
            <div class="star position-absolute"><span class="badge badge-success"><i class="icofont-star"></i> </span></div>
            <div class="favourite-heart text-danger position-absolute"><a href="{{ route('product.detail', $product->id) }}"><i class="icofont-heart"></i></a></div>
            <a href="{{ route('product.detail', $product->id) }}">
                <img src="{{ asset($product->productTemplate->image) }}" class="img-fluid item-img">
            </a>
        </div>
        <div class="p-3 position-relative flex-grow-1">
            <div class="list-card-body">
                <h6 class="mb-1 text-truncate"><a href="{{ route('product.detail', $product->id) }}" class="text-black"> {{ $product->productTemplate->name}}</a></h6>
                <p class="text-gray mb-3 time"><span class="bg-light text-dark rounded-sm pl-2 pb-1 pt-1 pr-2"><i class="icofont-wall-clock"></i> 20–25 min</span> <span class="float-right text-black-50"> {{ number_format($product->price, 0, ',', '.') }} VNĐ</span></p>
            </div>
            @php
                $discount = $product->price - $product->discount_price;
                $percent = round(($discount / $product->price) * 100);
            @endphp
            <div class="list-card-badge mb-2 text-center">
                <span class="badge badge-success">GIẢM GIÁ</span>
                <small class="text-success ml-1 font-weight-bold">{{ $percent }}% OFF</small>
            </div>
            @php
                $cart = session('cart', []);
                $cartItem = $cart[$product->id] ?? null;
            @endphp
            
            <div class="cart-actions mt-auto">
                @if ($cartItem)
                    <div class="d-flex justify-content-center align-items-center">
                        <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                data-id="{{ $product->id }}" 
                                data-qty="{{ $cartItem['quantity'] - 1 }}">
                            <i class="icofont-minus"></i>
                        </button>
            
                        <span class="btn btn-sm btn-light mx-2 font-weight-bold" 
                            id="qty-display-{{ $product->id }}">
                            {{ $cartItem['quantity'] }}
                        </span>
            
                        <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                data-id="{{ $product->id }}" 
                                data-qty="{{ $cartItem['quantity'] + 1 }}">
                            <i class="icofont-plus"></i>
                        </button>
                    </div>
                @else 
                    <form action="{{ route('add_to_cart', $product->id) }}" method="GET" class="w-100">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="icofont-cart"></i> Thêm vào giỏ hàng
                        </button>
                    </form>
                @endif
            </div>
        
        </div>
    </div>
</div>
@endforeach