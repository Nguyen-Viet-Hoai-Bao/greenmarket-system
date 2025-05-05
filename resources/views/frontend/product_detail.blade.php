@extends('frontend.dashboard.dashboard')
@section('dashboard')

<div class="container my-5">
  <div class="row">
      <!-- Phần hình ảnh và thông tin chính -->
      <div class="col-md-5">
        <img src="{{ asset($product->productTemplate->image) }}" alt="Sản phẩm" class="img-fluid mb-3 border rounded shadow-sm">
    
        <h5 class="fw-bold mb-1">{{ $product->productTemplate->name }}</h5>
        <p class="text-muted mb-2">Mã SP: {{ $product->productTemplate->code ?? 'Đang cập nhật' }}</p>
    
        <p class="mb-1 text-secondary">
            <strong>Giá niêm yết:</strong>
            <del>{{ number_format($product->price, 0, ',', '.') }} VNĐ</del>
        </p>
        <p class="text-danger fw-bold mb-2">
            <strong>Giá khuyến mãi:</strong>
            {{ number_format($product->discount_price, 0, ',', '.') }} VNĐ
        </p>
    
        <p class="mb-1"><strong>Tình trạng:</strong> <span class="text-success">Còn hàng</span></p>
        <p class="mb-1"><strong>Vận chuyển:</strong> Miễn phí cho đơn từ 300.000đ. Giao trong 2 giờ.</p>
        <p class="mb-3"><strong>Loại:</strong> 
            <span class="badge bg-danger text-white p-2">
                {{ $product->productTemplate->size ?? 'Đang cập nhật' }} {{ $product->productTemplate->unit ?? '' }}
            </span>
        </p>
    
        <div class="cart-actions ">
          @php
              $cart = session('cart', []);
              $cartItem = $cart[$product->id] ?? null;
          @endphp
      
          @if ($cartItem)
              <div class="d-flex justify-content-center align-items-center">
                  <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                          data-id="{{ $product->id }}" 
                          data-qty="{{ $cartItem['quantity'] - 1 }}">
                      <i class="icofont-minus"></i>
                  </button>
      
                  <span class="btn btn-sm btn-light mx-2 fw-bold" id="qty-display-{{ $product->id }}">
                      {{ $cartItem['quantity'] }}
                  </span>
      
                  <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                          data-id="{{ $product->id }}" 
                          data-qty="{{ $cartItem['quantity'] + 1 }}">
                      <i class="icofont-plus"></i>
                  </button>
              </div>
          @else
              <form action="{{ route('add_to_cart', $product->id) }}" method="GET" class="mt-2">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                      <i class="icofont-cart"></i> Thêm vào giỏ hàng
                  </button>
              </form>
          @endif
        </div>
      </div>
    

      <!-- Phần mô tả và thông tin sản phẩm -->
      <div class="col-md-7">
          <h5><strong>Mô tả</strong></h5>
          <p><strong>{{ $product->productTemplate->name ?? '' }}</strong></p>
          <p>
            {{ $productDetail->description ?? '' }}
          </p>

          <h6><strong>Thông tin sản phẩm</strong></h6>
          <p>
            {{ $productDetail->product_info ?? '' }}
          </p>

          <h6><strong>Lưu ý</strong></h6>
          <p>
            {{ $productDetail->note ?? '' }}
          </p>

          <hr>

          <h5><strong>Thông tin</strong></h5>
          <p><strong>Xuất xứ:</strong> {{ $productDetail->origin ?? '' }}</p>
          <p><strong>Bảo quản:</strong> 
            {{ $productDetail->preservation ?? '' }}
          </p>
          <p><strong>Hướng dẫn sử dụng:</strong> {{ $productDetail->usage_instructions ?? '' }}</p>
      </div>
  </div>
</div>

@endsection


<script>
  document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.btn-change-qty').forEach(btn => {
          btn.addEventListener('click', function () {
              const id = this.getAttribute('data-id');
              let quantity = parseInt(this.getAttribute('data-qty'));
  
              if (quantity < 1) quantity = 0;
  
              fetch("{{ route('cart.updateQuantity') }}", {
                  method: "POST",
                  headers: {
                      "X-CSRF-TOKEN": '{{ csrf_token() }}',
                      "Content-Type": "application/json"
                  },
                  body: JSON.stringify({
                      id: id,
                      quantity: quantity
                  })
              })
              .then(res => res.json())
              .then(data => {
                 window.location.reload();
              })
              .catch(err => console.error('Lỗi cập nhật giỏ hàng:', err));
          });
      });
  });
</script>
