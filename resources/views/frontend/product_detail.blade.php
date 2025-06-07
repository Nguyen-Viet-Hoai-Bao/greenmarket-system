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
  <div class="tab-pane fade show active" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
    <div class="row"  style="margin-top: 30px;">
        <!-- LEFT COLUMN: Xếp hạng và đánh giá -->
        <div class="col-md-6 m-6">
            <div class="bg-white rounded shadow-sm p-4 mb-4 clearfix graph-star-rating">
                <h5 class="mb-4">Xếp hạng và đánh giá sản phẩm</h5>
                <div class="graph-star-rating-header">
                    <div class="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                        <a href="#"><i class="icofont-ui-rating {{ $i <= round($roundedAverageRating) ? 'active' : ''}}"></i></a>
                        @endfor
                        <b class="text-black ml-2">{{ $totalReviews }}</b>
                    </div>
                    <p class="text-black mb-4 mt-2">Được đánh giá {{$roundedAverageRating}} trên 5</p>
                </div>

                <div class="graph-star-rating-body">
                    @foreach ($ratingCounts as $star => $count) 
                    <div class="rating-list">
                        <div class="rating-list-left text-black">
                            {{ $star }} sao
                        </div>
                        <div class="rating-list-center">
                            <div class="progress">
                                <div style="width: {{ $ratingPercentages[$star] }}%" aria-valuemax="5" aria-valuemin="0" aria-valuenow="5" role="progressbar" class="progress-bar bg-primary">
                                    <span class="sr-only">{{ $ratingPercentages[$star] }}% Hoàn thành</span>
                                </div>
                            </div>
                        </div>
                        <div class="rating-list-right text-black">{{ number_format($ratingPercentages[$star],2) }}%</div>
                    </div>
                    @endforeach
                </div>

                <div class="graph-star-rating-footer text-center mt-3 mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm">Xếp hạng và đánh giá</button>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Tất cả đánh giá và Để lại nhận xét -->
        <div class="col-md-6">
            <!-- All reviews -->
            <div class="bg-white rounded shadow-sm p-4 mb-4 restaurant-detailed-ratings-and-reviews">
                <a href="#" class="btn btn-outline-primary btn-sm float-right">Đánh Giá Cao Nhất</a>
                <h5 class="mb-1">Tất Cả Đánh Giá Và Nhận Xét</h5>

                <style>
                    .icofont-ui-rating { color: #ccc; }
                    .icofont-ui-rating.active { color: #dd646e; }
                </style>

                @php
                $reviews = App\Models\ProductReview::where('product_id',$product->id)->where('status',1)->latest()->limit(5)->get();
                @endphp   

                @foreach ($reviews as $review)
                <div class="reviews-members pt-4 pb-4">
                    <div class="media">
                        <a href="#"><img alt="User Image" src="{{ (!empty($review->user->photo)) ? url('upload/user_images/'.$review->user->photo) : url('upload/no_image.jpg') }}" class="mr-3 rounded-pill"></a>
                        <div class="media-body">
                            <div class="reviews-members-header">
                                <span class="star-rating float-right">
                                    @php $rating = $review->rating ?? 0; @endphp
                                    @for ($i = 1; $i <= 5; $i++)
                                        <a href="#"><i class="icofont-ui-rating {{ $i <= $rating ? 'active' : '' }}"></i></a>
                                    @endfor
                                </span>
                                <h6 class="mb-1"><a class="text-black" href="#">{{ $review->user->name }}</a></h6>
                                <p class="text-gray">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</p>
                            </div>
                            <div class="reviews-members-body">
                                <p>{{ $review->comment }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Leave a review -->
            <div class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                @guest
                <p><b>Để thêm đánh giá cho địa điểm, bạn cần đăng nhập trước <a href="{{ route('login') }}"> Đăng nhập tại đây </a></b></p>
                @else

                <style>
                .star-rating label {
                    display: inline-flex;
                    margin-right: 5px;
                    cursor: pointer;
                }
                .star-rating input[type="radio"] {
                    display: none;
                }
                .star-rating input[type="radio"]:checked + .star-icon {
                    color: #dd646e;
                }
                </style> 

                <h5 class="mb-4">Để Lại Nhận Xét</h5>
                <p class="mb-2">Đánh Giá Địa Điểm</p>

                <button type="button" class="btn btn-success mb-3" id="leaveReviewButton"
                    data-product-id="{{ $product->id }}">
                    Để lại đánh giá của bạn
                </button>

                <div id="verificationMessage"></div>

                <form method="post" action="{{ route('store.product.review') }}" id="reviewForm" style="display: none;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-4">
                        <span class="star-rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <label for="rating-{{ $i }}">
                                    <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" hidden>
                                    <i class="icofont-ui-rating icofont-2x star-icon"></i>
                                </label>
                            @endfor
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Nhận Xét Của Bạn</label>
                        <textarea class="form-control" name="comment" id="comment"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="order_id" id="orderIdForReview">
                        <button class="btn btn-primary btn-sm" type="submit"> Gửi Nhận Xét </button>
                    </div>
                </form>

                @endguest
            </div>
        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const leaveReviewButton = document.getElementById('leaveReviewButton');
    const reviewForm = document.getElementById('reviewForm');
    const orderIdForReview = document.getElementById('orderIdForReview');
    const verificationMessage = document.getElementById('verificationMessage');

    if (leaveReviewButton) {
        leaveReviewButton.addEventListener('click', async function () {
            const productId = leaveReviewButton.getAttribute('data-product-id');
            verificationMessage.innerHTML = '<div class="alert alert-info">Đang kiểm tra đơn hàng của bạn...</div>';

            try {
                const response = await fetch('/verify-order-for-product-review', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ product_id: productId })
                });

                const data = await response.json();

                if (data.success) {
                    verificationMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    orderIdForReview.value = data.order_id;
                    reviewForm.style.display = 'block';
                    leaveReviewButton.style.display = 'none';
                    resetStarColors();
                    reviewForm.scrollIntoView({ behavior: 'smooth' });
                } else {
                    verificationMessage.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                }
            } catch (error) {
                verificationMessage.innerHTML = '<div class="alert alert-danger">Lỗi xảy ra khi kiểm tra. Vui lòng thử lại.</div>';
            }
        });
    }

    function resetStarColors() {
        const stars = document.querySelectorAll('#reviewForm .star-rating label i');
        stars.forEach(star => {
            star.style.color = '#ccc';
        });
    }

    document.querySelectorAll('#reviewForm .star-rating input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const ratingValue = parseInt(radio.value);
            const stars = document.querySelectorAll('#reviewForm .star-rating label i');
            stars.forEach((star, index) => {
                star.style.color = index < ratingValue ? '#f39c12' : '#ccc';
            });
        });
    });
});
</script>
