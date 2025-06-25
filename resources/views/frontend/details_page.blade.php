@extends('frontend.dashboard.dashboard')
@section('dashboard')

<style>
   .star-rating label {
      cursor: pointer;
      color: #ccc; /* màu mặc định của sao */
   }

   .star-rating input[type="radio"]:checked ~ label i,
   .star-rating label:hover ~ label i {
      color: #ccc; /* các sao sau sao được chọn hoặc hover không màu */
   }

   .star-rating input[type="radio"]:checked + label i,
   .star-rating label:hover i {
      color: #f39c12;
   }

   .mall-category-item h6 {
      min-height: 3em; 
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 3; 
      -webkit-box-orient: vertical;
   }
   .image-container {
      position: relative; /* Rất quan trọng để discount-badge định vị theo */
      overflow: hidden; /* Đảm bảo mọi thứ bên trong không tràn ra ngoài */
      display: block; /* Đảm bảo div chiếm đủ không gian */
   }

   .image-container img {
      display: block; 
      width: 100%; 
      height: auto; 
   }

   .discount-badge {
      position: absolute; 
      top: 5px; 
      right: 5px; 
      background-color: #03c800;
      color: white; 
      padding: 3px 8px;
      border-radius: 5px; 
      font-size: 0.75em; 
      font-weight: bold;
      z-index: 10; 
      white-space: nowrap;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
   }
   .list-card-image img.item-img {
      max-height: 150px;
      width: 100%; 
      object-fit: cover;
   }
   .btn-unit-select.in-cart {
      opacity: 0.8; /* Làm mờ đi */
      cursor: not-allowed; /* Thay đổi con trỏ chuột */
      background-color: #f0f0f0 !important; /* Nền xám hơn */
      border-color: #ddd !important;
      color: #666 !important;
      pointer-events: none; /* Ngăn chặn sự kiện click */
   }
   .btn-unit-select:disabled {
      opacity: 0.8;
      cursor: not-allowed;
      background-color: #f8f8f8 !important;
      border-color: #eee !important;
      color: #999 !important;
      pointer-events: none;
   }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

@php
// $products = App\Models\ProductNew::with('productTemplate.menu')
//     ->where('client_id', $client->id)
//     ->limit(3)
//     ->get();
$products = \App\Models\ProductNew::with('productTemplate.menu')
   ->where('qty', '>', 0)
   ->where('client_id', $client->id)
   ->get()
   ->groupBy(fn($product) => $product->productTemplate?->menu?->id)
   ->map(fn($group) => $group->take(3))
   ->flatten();

$menuNames = $products->map(function($product) {
    return $product->productTemplate?->menu?->menu_name;
})->filter()->unique()->take(5)->toArray();

$menuNamesString = implode('. ', $menuNames);

$coupons = App\Models\Coupon::where('client_id', $client->id)
                    ->where('status', '1')->first();
@endphp

<section class="restaurant-detailed-banner">
  <div class="text-center">
     <img class="img-fluid cover" src="{{ asset($client->cover_photo) }}">
  </div>
  <div class="restaurant-detailed-header">
     <div class="container">
        <div class="row d-flex align-items-end">
           <div class="col-md-8">
              <div class="restaurant-detailed-header-left">
                 <img class="img-fluid mr-3 float-left" alt="osahan" src="{{ asset($client->photo) }}">
                 <h2 class="text-white">{{ $client->name }}</h2>
                 <p class="text-white mb-1"><i class="icofont-location-pin"></i> {{ $client->fullAddress }} <span class="badge badge-success">Mở</span>
                 </p>
                 <p class="text-white mb-0"><i class="icofont-food-cart"></i> {{ $menuNamesString }}</p>
              </div>
           </div>
           <div class="col-md-4">
              <div class="restaurant-detailed-header-right text-right">
                 <button class="btn btn-success" type="button"><i class="icofont-clock-time"></i> 25–35 phút
                 </button>
                 <h6 class="text-white mb-0 restaurant-detailed-ratings">
                  <span class="generator-bg rounded text-white">
                     <i class="icofont-star"></i> {{ $roundedAverageRating }}
                  </span> 
                  {{ $totalReviews }} Đánh giá 
                  <i class="ml-3 icofont-speech-comments"></i> {{ $reviews->count() }} đánh giá                  
              </div>
           </div>
        </div>
     </div>
  </div>
  </div> 
</section>
<section class="offer-dedicated-nav bg-white border-top-0 shadow-sm">
  <div class="container">
     <div class="row">
        <div class="col-md-12">
           <span class="restaurant-detailed-action-btn float-right">
            @auth
               @php
               $isFavourite = auth()->user()
                           ->favourites()
                           ->where('client_id', $client->id)
                           ->exists();
               @endphp
               <button class="btn btn-light btn-sm border-light-btn" type="button" onclick="addWishlist({{ $client->id }})">
                  <i id="heart-icon-{{ $client->id }}" class="icofont-heart {{ $isFavourite ? 'text-danger' : 'text-muted' }}"></i>
                  <span id="favourite-label-{{ $client->id }}">
                     {{ $isFavourite ? 'Mark as Favourite' : '' }}
                  </span>
               </button>
            @endauth
        
           </span>
           <ul class="nav" id="pills-tab" role="tablist">
              <li class="nav-item">
                 <a class="nav-link active" id="pills-order-online-tab" data-toggle="pill" href="#pills-order-online" role="tab" aria-controls="pills-order-online" aria-selected="true">Đặt hàng trực tuyến</a>
              </li>
              <li class="nav-item">
                 <a class="nav-link" id="pills-gallery-tab" data-toggle="pill" href="#pills-gallery" role="tab" aria-controls="pills-gallery" aria-selected="false">Thư viện ảnh</a>
              </li>
              <li class="nav-item">
                 <a class="nav-link" id="pills-restaurant-info-tab" data-toggle="pill" href="#pills-restaurant-info" role="tab" aria-controls="pills-restaurant-info" aria-selected="false">Thông tin cửa hàng</a>
              </li>
              {{-- <li class="nav-item">
                 <a class="nav-link" id="pills-book-tab" data-toggle="pill" href="#pills-book" role="tab" aria-controls="pills-book" aria-selected="false">Đặt bàn</a>
              </li> --}}
              <li class="nav-item">
                 <a class="nav-link" id="pills-reviews-tab" data-toggle="pill" href="#pills-reviews" role="tab" aria-controls="pills-reviews" aria-selected="false">Đánh giá & Phản hồi</a>
              </li>
           </ul>
        </div>
     </div>
  </div>
</section>
<section class="offer-dedicated-body pt-2 pb-2 mt-4 mb-4">
  <div class="container">
     <div class="row">
        <div class="col-md-8">
           <div class="offer-dedicated-body-left">
              <div class="tab-content" id="pills-tabContent">
                 <div class="tab-pane fade show active" id="pills-order-online" role="tabpanel" aria-labelledby="pills-order-online-tab">
                     {{-- Most Popular --}}
                     <div id="menu" class="bg-white rounded shadow-sm p-4 mb-4 explore-outlets">
                     <h6 class="mb-3">Sản Phẩm Phổ Biến Nhất </h6>
                     <div class="owl-carousel owl-theme owl-carousel-five offers-interested-carousel">@foreach ($processedPopulers as $populer) {{-- Sử dụng biến đã xử lý --}}
                     <div class="item">
                        <div class="mall-category-item">
                              <a href="{{ route('product.detail', $populer->id) }}">
                                 {{-- Thêm div bao quanh ảnh và đặt position: relative --}}
                                 <div class="image-container" style="position: relative; overflow: hidden;"> 
                                    <img class="img-fluid" src="{{ asset($populer->productTemplate->image ?? 'https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}" alt="">
                                    @if (
                                          ($populer->display_mode === 'unit' && $populer->available_units->isNotEmpty() && $populer->available_units->first()->final_sale_price < $populer->available_units->first()->sale_price) ||
                                          ($populer->display_mode === 'quantity' && isset($populer->display_original_price) && $populer->display_original_price > 0 && $populer->final_display_price < $populer->display_original_price)
                                    )
                                          <span class="discount-badge">Giảm giá</span> {{-- Thẻ Giảm giá sẽ nằm đè lên ảnh --}}
                                    @endif
                                 </div>
                                 <h6>{{ $populer->productTemplate->name ?? $populer->name }}</h6>
                                 <small class="text-muted d-block mb-1">
                                    <i class="icofont-sale-discount text-danger"></i> Đã bán: {{ $populer->sold }}
                                 </small>

                                 @if ($populer->display_mode === 'unit')
                                    {{-- Chế độ UNIT: Hiển thị giá của từng khay chưa bán, ưu tiên HSD gần nhất --}}
                                    @forelse ($populer->available_units as $unit)
                                          <div class="price-info">
                                             @if ($unit->final_sale_price < $unit->sale_price)
                                                <del>{{ number_format($unit->sale_price, 0, ',', '.') }} VNĐ</del>
                                                <span class="text-success">{{ number_format($unit->final_sale_price, 0, ',', '.') }} VNĐ</span>
                                             @else
                                                <span>{{ number_format($unit->sale_price, 0, ',', '.') }} VNĐ</span>
                                             @endif
                                             <br>
                                             <small class="text-muted">
                                                {{ $unit->weight }} KG/{{ $populer->productTemplate->unit ?? 'Đơn vị' }} - HSD: {{ \Carbon\Carbon::parse($unit->expiry_date)->format('d/m/Y') }}
                                             </small>
                                          </div>
                                          @break 
                                    @empty
                                          <span class="text-danger">Hết hàng</span>
                                    @endforelse
                                 @elseif ($populer->display_mode === 'quantity')
                                    <p class="mb-0">
                                          <span class="text-primary font-weight-bold">Còn: {{ $populer->total_available_quantity }} {{ $populer->productTemplate->unit ?? 'sản phẩm' }}</span>
                                    </p>
                                    <p class="mb-0">
                                          @if (isset($populer->display_original_price) && $populer->display_original_price > 0)
                                             @if ($populer->final_display_price < $populer->display_original_price)
                                                <del>{{ number_format($populer->display_original_price, 0, ',', '.') }} VNĐ</del>
                                                <span class="text-success">{{ number_format($populer->final_display_price, 0, ',', '.') }} VNĐ</span>
                                             @else
                                                <span>{{ number_format($populer->final_display_price, 0, ',', '.') }} VNĐ</span>
                                             @endif
                                          @else
                                             <span class="text-danger">Hết hàng</span>
                                          @endif
                                    </p>
                                 @endif
                              </a>

                              @php
                                 $cart = session('cart', []);
                                 $cartItem = $cart[$populer->id] ?? null;
                              @endphp

                              <div class="cart-actions-1">
                                 @if ($cartItem)
                                    <div class="d-flex justify-content-center align-items-center mt-2">
                                          <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                             data-id="{{ $populer->id }}"
                                             data-qty="{{ $cartItem['quantity'] - 1 }}">
                                             <i class="icofont-minus"></i>
                                          </button>
                                          <span class="btn btn-sm btn-light mx-2 font-weight-bold qty-display" data-id="{{ $populer->id }}">
                                             {{ $cartItem['quantity'] }}
                                          </span>
                                          <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                             data-id="{{ $populer->id }}"
                                             data-qty="{{ $cartItem['quantity'] + 1 }}">
                                             <i class="icofont-plus"></i>
                                          </button>
                                    </div>
                                 @else
                                    <button type="button" class="btn btn-primary btn-sm w-100 btn-add-to-cart mt-2" data-id="{{ $populer->id }}">
                                          <i class="icofont-cart"></i> Thêm vào giỏ
                                    </button>
                                 @endif
                              </div>
                        </div>
                     </div>
                  @endforeach
               </div>
            </div>
                     {{-- Best Sellers --}}
                     <div id="menu" class="bg-white rounded shadow-sm p-3 mb-4 explore-outlets">
                        <h6 class="mb-3">Bán Chạy Nhất </h6>
                        <div class="owl-carousel owl-theme owl-carousel-five offers-interested-carousel">
                        @foreach ($processedBestsellers as $product)
                           <div class="item">
                              <div class="mall-category-item">
                                    <a href="{{ route('product.detail', $product->id) }}">
                                       {{-- Thêm div bao quanh ảnh và đặt position: relative --}}
                                       <div class="image-container" style="position: relative; overflow: hidden;">
                                          <img class="img-fluid" src="{{ asset($product->productTemplate->image ?? 'https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}" alt="">
                                          @if (
                                                ($product->display_mode === 'unit' && isset($product->display_unit_original_price) && $product->display_unit_original_price > 0 && $product->display_unit_price < $product->display_unit_original_price) ||
                                                ($product->display_mode === 'quantity' && isset($product->display_original_price) && $product->display_original_price > 0 && $product->final_display_price < $product->display_original_price)
                                          )
                                                <span class="discount-badge">Giảm giá</span> {{-- Thẻ Giảm giá sẽ nằm đè lên ảnh --}}
                                          @endif
                                       </div>
                                       <h6>{{ $product->productTemplate->name ?? $product->name }}</h6>
                                       <small class="text-muted d-block mb-1">
                                          <i class="icofont-sale-discount text-danger"></i> Đã bán: {{ $product->sold }}
                                       </small>

                                       @if ($product->display_mode === 'unit')
                                          {{-- Chế độ UNIT: Hiển thị thông tin chính của unit HSD gần nhất --}}
                                          @if (isset($product->display_unit_original_price) && $product->display_unit_original_price > 0)
                                                <p class="mb-0">
                                                   @if ($product->display_unit_price < $product->display_unit_original_price)
                                                      <del>{{ number_format($product->display_unit_original_price, 0, ',', '.') }} VNĐ</del>
                                                      <span class="text-success">{{ number_format($product->display_unit_price, 0, ',', '.') }} VNĐ</span>
                                                      {{-- <span class="badge badge-success">Giảm giá</span> --}} {{-- Đã di chuyển lên trên ảnh --}}
                                                   @else
                                                      <span>{{ number_format($product->display_unit_price, 0, ',', '.') }} VNĐ</span>
                                                   @endif
                                                </p>
                                                <small class="text-muted">
                                                   {{ $product->productUnits->first()->weight }} KG/{{ $product->productTemplate->unit ?? 'Đơn vị' }} - HSD: {{ \Carbon\Carbon::parse($product->productUnits->first()->expiry_date)->format('d/m/Y') }}
                                                </small>
                                          @else
                                                <span class="text-danger">Hết hàng</span>
                                          @endif
                                       @elseif ($product->display_mode === 'quantity')
                                          {{-- Chế độ QUANTITY: Hiển thị tổng số lượng và giá đã giảm (từ unit HSD gần nhất) --}}
                                          <p class="mb-0">
                                                <span class="text-primary font-weight-bold">
                                                   Còn: {{ $product->total_available_quantity }} {{ $product->productTemplate->unit ?? 'sản phẩm' }}
                                                </span>
                                          </p>
                                          <p class="mb-0">
                                                @if (isset($product->display_original_price) && $product->display_original_price > 0)
                                                   @if ($product->final_display_price < $product->display_original_price)
                                                      <del>{{ number_format($product->display_original_price, 0, ',', '.') }} VNĐ</del>
                                                      <span class="text-success">{{ number_format($product->final_display_price, 0, ',', '.') }} VNĐ</span>
                                                      {{-- <span class="badge badge-success">Giảm giá</span> --}} {{-- Đã di chuyển lên trên ảnh --}}
                                                   @else
                                                      <span>{{ number_format($product->final_display_price, 0, ',', '.') }} VNĐ</span>
                                                   @endif
                                                @else
                                                   <span class="text-danger">Hết hàng</span>
                                                @endif
                                          </p>
                                       @endif
                                    </a>

                                    {{-- Phần thêm/bớt giỏ hàng (giữ nguyên) --}}
                                    @php
                                       $cart = session('cart', []);
                                       $cartItem = $cart[$product->id] ?? null;
                                    @endphp

                                    <div class="cart-actions-1">
                                       @if ($cartItem)
                                          <div class="d-flex justify-content-center align-items-center mt-2">
                                                <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                                   data-id="{{ $product->id }}"
                                                   data-qty="{{ $cartItem['quantity'] - 1 }}">
                                                   <i class="icofont-minus"></i>
                                                </button>
                                                <span class="btn btn-sm btn-light mx-2 font-weight-bold qty-display" data-id="{{ $product->id }}">
                                                   {{ $cartItem['quantity'] }}
                                                </span>
                                                <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                                   data-id="{{ $product->id }}"
                                                   data-qty="{{ $cartItem['quantity'] + 1 }}">
                                                   <i class="icofont-plus"></i>
                                                </button>
                                          </div>
                                       @else
                                          <button type="button" class="btn btn-primary btn-sm w-100 btn-add-to-cart mt-2" data-id="{{ $product->id }}">
                                                <i class="icofont-cart"></i> Thêm vào giỏ
                                          </button>
                                       @endif
                                    </div>
                              </div>
                           </div>
                        @endforeach
                        </div>
                     </div>
                  </div>

{{-- pills-gallery --}}
<div class="tab-pane fade" id="pills-gallery" role="tabpanel" aria-labelledby="pills-gallery-tab">
  <div id="gallery" class="bg-white rounded shadow-sm p-4 mb-4">
      <div class="restaurant-slider-main position-relative homepage-great-deals-carousel">
        <div class="owl-carousel owl-theme homepage-ad">

          @foreach ($gallerys as $index => $gallery)
            <div class="item">
              <img class="img-fluid" src="{{ asset($gallery->gallery_img) }}">
              <div class="position-absolute restaurant-slider-pics bg-dark text-white">{{ $index + 1 }} of {{ $gallery->count() }} Ảnh</div>
            </div>
          @endforeach
        </div>
      </div>
  </div>
</div>

{{-- pills-market-info --}}
<div class="tab-pane fade" id="pills-restaurant-info" role="tabpanel" aria-labelledby="pills-restaurant-info-tab">
   <div id="restaurant-info" class="bg-white rounded shadow-sm p-4 mb-4">
       <div class="address-map float-right ml-5">
         <div class="mapouter">
             <div class="gmap_canvas">
               <iframe width="300" height="170" id="gmap_canvas"
                       src="https://maps.google.com/maps?q=university%20of%20san%20francisco&t=&z=9&ie=UTF8&iwloc=&output=embed"
                       frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
             </div>
         </div>
       </div>
       <h5 class="mb-4">Thông tin cửa hàng</h5>
       <p class="mb-3">{{ $client->fullAddress }} </p>
       <p class="mb-2 text-black"><i class="icofont-phone-circle text-primary mr-2"></i> {{ $client->phone }} </p>
       <p class="mb-2 text-black"><i class="icofont-email text-primary mr-2"></i> {{ $client->email }} </p>
       <p class="mb-2 text-black"><i class="icofont-clock-time text-primary mr-2"></i> Hôm nay 11h – 17h, 18h – 23h
         <span class="badge badge-success"> ĐANG MỞ CỬA </span>
       </p>
       <hr class="clearfix">
       <p class="text-black mb-0">Bạn cũng có thể xem bản đồ 3D bằng cách nhấn vào &nbsp;&nbsp;&nbsp; 
         <a class="text-info font-weight-bold" href="#">Bản đồ địa điểm</a>
       </p>
       <hr class="clearfix">
       <h5 class="mt-4 mb-4">Loại thực đơn</h5>
       <p class="mb-3">
         {{ $menus->pluck('menu_name')->implode(', ') }}
       </p>
       <div class="border-btn-main mb-4">
         @foreach ($menus as $menu)
           <a class="border-btn text-success mr-2" href="#">
             <i class="icofont-check-circled"></i> {{ $menu->menu_name }}
           </a>
         @endforeach
       </div>
   </div>
 </div>
                 <div class="tab-pane fade" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
                    <div id="ratings-and-reviews" class="bg-white rounded shadow-sm p-4 mb-4 clearfix restaurant-detailed-star-rating">
                       <span class="star-rating float-right">
         @for ($i = 1; $i <= 5; $i++)
         <a href="#"><i class="icofont-ui-rating icofont-2x {{ $i <= round($roundedAverageRating) ? 'active' : ''}}"></i></a>
         @endfor
                       </span>
                       <h5 class="mb-0 pt-1">Đánh giá địa điểm này</h5>
                    </div>


<div class="bg-white rounded shadow-sm p-4 mb-4 clearfix graph-star-rating">
   <h5 class="mb-4">Xếp hạng và đánh giá</h5>
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
                  <span class="sr-only">{{ $ratingPercentages[$star] }}% Hoàn thành (danger)</span>
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
                    
<div class="bg-white rounded shadow-sm p-4 mb-4 restaurant-detailed-ratings-and-reviews">
   <a href="#" class="btn btn-outline-primary btn-sm float-right">Đánh Giá Cao Nhất</a>
      <h5 class="mb-1">Tất Cả Đánh Giá Và Nhận Xét</h5>

      <style>
         .icofont-ui-rating {
            color: #ccc;
         }
         .icofont-ui-rating.active {
            color: #dd646e;
         }
      </style> 

      @php
      $reviews = App\Models\Review::where('client_id',$client->id)->where('status',1)->latest()->limit(5)->get();
      @endphp   
      @foreach ($reviews as $review)
         
      <div class="reviews-members pt-4 pb-4">
         <div class="media">
            <a href="#"><img alt="Generic placeholder image" src="{{ (!empty($review->user->photo)) ? url($review->user->photo) : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}" class="mr-3 rounded-pill"></a>
            <div class="media-body">
               <div class="reviews-members-header">
                  <span class="star-rating float-right">
                     @php
                        $rating = $review->rating ?? 0;
                     @endphp 
                     @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $rating)
                        <a href="#"><i class="icofont-ui-rating active"></i></a> 
                        @else
                        <a href="#"><i class="icofont-ui-rating"></i></a>
                        @endif   
                     @endfor 
                  </span>
                  <h6 class="mb-1"><a class="text-black" href="#">{{ $review->user->name }}</a></h6>
                  <p class="text-gray"> {{ Carbon\Carbon::parse($review->created_at)->diffForHumans() }} </p>
               </div>
               <div class="reviews-members-body">
                  <p> {{ $review->comment }} </p>
               </div>
            </div>
         </div>
      </div>

      @endforeach
</div>

                    <div class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                     @guest
                     <p><b>Để thêm đánh giá cho địa điểm, bạn cần đăng nhập trước <a href="{{ route('login') }}"> Đăng nhập tại đây </a> </b></p>
                     @else 
                
                  <style>
                   .star-rating label {
                      display: inline-flex;
                      margin-right: 5px;
                      cursor: pointer;
                   }
                   .star-rating input[type="radio"]{
                      display: none;
                   }
                   .star-rating input[type="radio"]:checked + .star-icon{
                      color: #dd646e;
                   }
                  </style> 
                  
                     <h5 class="mb-4">Để Lại Nhận Xét</h5>
                     <p class="mb-2">Đánh Giá Địa Điểm</p>

                     <button type="button" class="btn btn-success mb-3" id="leaveReviewButton">
                        Để lại đánh giá của bạn
                     </button>

                     <div id="verificationMessage"></div>

                     {{-- Form đánh giá ban đầu (sẽ được ẩn/hiện bởi JS) --}}
                     <form method="post" action="{{ route('store.review') }}" id="reviewForm" style="display: none;">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <div class="mb-4">
                           <span class="star-rating">
                                 <label for="rating-1">
                                    <input type="radio" name="rating" id="rating-1" value="1" hidden><i class="icofont-ui-rating icofont-2x star-icon"></i></label>
                                 <label for="rating-2">
                                    <input type="radio" name="rating" id="rating-2" value="2" hidden><i class="icofont-ui-rating icofont-2x star-icon"></i></label>
                                 <label for="rating-3">
                                    <input type="radio" name="rating" id="rating-3" value="3" hidden><i class="icofont-ui-rating icofont-2x star-icon"></i></label>
                                 <label for="rating-4">
                                    <input type="radio" name="rating" id="rating-4" value="4" hidden><i class="icofont-ui-rating icofont-2x star-icon"></i></label>
                                 <label for="rating-5">
                                    <input type="radio" name="rating" id="rating-5" value="5" hidden><i class="icofont-ui-rating icofont-2x star-icon"></i></label> 
                           </span>
                        </div>
                        <div class="form-group">
                           <label>Nhận Xét Của Bạn</label>
                           <textarea class="form-control" name="comment" id="comment"></textarea>
                        </div>
                        <div class="form-group">
                           {{-- Thêm hidden input cho order_id sau khi xác thực --}}
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
@php
    use App\Models\Coupon;
    use App\Models\Order;
    use Carbon\Carbon;

    $coupons = Coupon::where(function ($query) use ($client) {
                    $query->where('client_id', $client->id)
                          ->orWhere('client_id', 0);
                })
                ->where('validity', '>=', Carbon::now()->format('Y-m-d'))
                ->orderByDesc('created_at')
                ->get();

    $usedCouponIds = [];

    if (auth()->check()) {
        $orders = Order::where('user_id', auth()->id())->get();
        $usedCouponIds = $orders->pluck('coupon_code')->filter()->unique()->toArray();
    }
@endphp

<div class="col-md-4">
   <div class="pb-2">
    <div class="bg-white rounded shadow-sm text-white mb-4 p-4 clearfix restaurant-detailed-earn-pts card-icon-overlap">
        <img class="img-fluid float-left mr-3" src="{{ asset('frontend/img/earn-score-icon.png') }}">
        <h6 class="pt-0 text-primary mb-1 font-weight-bold">ƯU ĐÃI</h6>

        @if ($coupons->isEmpty())
            <p class="mb-0">Không có mã giảm giá</p>
        @else
            <ul class="pl-3 text-dark">
                @foreach ($coupons as $coupon)
                    @php
                        // Kiểm tra coupon đã được dùng chưa
                        $isUsed = in_array($coupon->id, $usedCouponIds);
                        $discountClass = $isUsed ? 'text-muted' : 'text-danger font-weight-bold';
                        $nameClass = $isUsed ? 'text-muted font-weight-bold text-decoration-underline' : 'text-danger font-weight-bold text-decoration-underline';
                    @endphp
                    <li class="mb-2">
                        <span class="{{ $discountClass }}">{{ $coupon->discount }}%</span> cho đơn hàng - 

                        <a href="#" 
                            class="{{ $nameClass }}" 
                            data-toggle="modal" 
                            data-target="#couponModal{{ $coupon->id }}">
                                {{ $coupon->coupon_name }}
                        </a>
                    </li>

                    {{-- Modal --}}
                    <div class="modal fade" id="couponModal{{ $coupon->id }}" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel{{ $coupon->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header text-white" style="background-color: #3ecf8e;">
                                    <h5 class="modal-title font-weight-bold" id="couponModalLabel{{ $coupon->id }}">Chi tiết mã giảm giá</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Đóng">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="row align-items-center">
                                        {{-- Hình ảnh coupon --}}
                                        <div class="col-md-5 text-center mb-3 mb-md-0">
                                            <img src="{{ asset($coupon->image_path ?? 'https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}" class="img-fluid rounded shadow-sm" alt="Coupon Image">
                                        </div>

                                        {{-- Thông tin chi tiết --}}
                                        <div class="col-md-7">
                                            <h4 class="text-primary font-weight-bold">{{ $coupon->coupon_name }}</h4>
                                            <p class="mb-2"><strong>Giảm giá:</strong> <span class="text-danger">{{ $coupon->discount }}%</span></p>
                                            @if ($coupon->max_discount_amount)
                                                <p class="mb-2"><strong>Giới hạn:</strong> <span class="text-danger">
                                                    {{ number_format($coupon->max_discount_amount, 0, ',', '.') }} VNĐ
                                                    trên một đơn hàng</span></p>
                                            @endif
                                            <p class="mb-2"><strong>Hạn sử dụng:</strong> {{ \Carbon\Carbon::parse($coupon->validity)->format('d/m/Y') }}</p>
                                            <p class="mb-3"><strong>Mô tả:</strong> {{ $coupon->coupon_desc ?? 'Không có mô tả.' }}</p>

                                            {{-- Copy mã --}}
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="{{ $coupon->coupon_name }}" id="couponCode{{ $coupon->id }}" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('couponCode{{ $coupon->id }}')">Copy</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </ul>
        @endif

        <div class="icon-overlap">
            <i class="icofont-sale-discount"></i>
        </div>
    </div>
</div>

    <div class="generator-bg rounded shadow-sm mb-4 p-4 osahan-cart-item">
         <div id="cart-container">
            @include('frontend.cart.partial')
         </div>
    </div>
</div>


     </div>
  </div>

  
{{-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////// --}}

<div class="container" id="product-search">
   <div class="row">
         <!-- Sidebar -->
         <div class="col-md-3">
            <div class="filters-body">
               <div id="accordion">
                  <div class="filters-card-body card-shop-filters">
                     @foreach ($menusWithCategories as $index => $menu)
                        @php
                           $totalProductsInMenu = 0;
                           foreach ($menu->categories as $category) {
                                 $totalProductsInMenu += $products_all->filter(function ($product) use ($category) {
                                    return $product->productTemplate->category_id == $category->id;
                                 })->count();
                           }
                        @endphp
                        <div class="card mb-2 border-0">
                           <div id="headingMenu{{ $index }}">
                                 <h6 class="mb-0">
                                    <a class="btn btn-link text-left w-100 d-flex align-items-center justify-content-between"
                                       data-toggle="collapse"
                                       href="#collapseMenu{{ $index }}"
                                       aria-expanded="false"
                                       aria-controls="collapseMenu{{ $index }}">
                                       <span>
                                             <img src="{{ asset($menu->image ?? 'default.jpg') }}"
                                                alt="{{ $menu->menu_name }}" width="20" class="mr-2">
                                             {{ $menu->menu_name }}
                                             <small class="text-black-50">({{ $totalProductsInMenu }})</small>
                                       </span>
                                       <i class="icofont-arrow-down"></i>
                                    </a>
                                 </h6>
                           </div>

                           <div id="collapseMenu{{ $index }}" class="collapse"
                                 aria-labelledby="headingMenu{{ $index }}"
                                 data-parent="#accordion">
                                 <div class="card-body py-2 px-3">
                                    @foreach ($menu->categories as $category)
                                       @php
                                             $categoryProductCount = $products_all->filter(function ($product) use ($category) {
                                                return $product->productTemplate->category_id == $category->id;
                                             })->count();
                                       @endphp
                                       <div class="custom-control custom-checkbox ml-3">
                                             <input type="checkbox"
                                                   class="custom-control-input filter-checkbox"
                                                   id="category-{{ $category->id }}"
                                                   data-type="category"
                                                   data-id="{{ $category->id }}">
                                             <label class="custom-control-label"
                                                   for="category-{{ $category->id }}">
                                                {{ $category->category_name }}
                                                <small class="text-black-50">({{ $categoryProductCount }})</small>
                                             </label>
                                       </div>
                                    @endforeach
                                 </div>
                           </div>
                        </div>
                     @endforeach
                  </div>
               </div>
            </div>
         </div>
<div class="col-md-9">
    <div class="row" id="product-list">
        @foreach ($processedProductsAll as $product)
            <div class="col-md-3 col-sm-6 mb-4 pb-2">
                <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm d-flex flex-column">
                    <div class="list-card-image">
                        <div class="star position-absolute">
                            @if ($product->best_seller == 1)
                                <span class="badge badge-success"><i class="icofont-star"></i></span>
                            @endif
                        </div>
                        <div class="favourite-heart text-danger position-absolute">
                            @if ($product->most_popular == 1)
                                <a href="{{ route('product.detail', $product->id) }}"><i class="icofont-heart"></i></a>
                            @endif
                        </div>
                        <a href="{{ route('product.detail', $product->id) }}">
                            {{-- Đảm bảo productTemplate->image tồn tại trước khi dùng --}}
                            <img src="{{ asset($product->productTemplate->image ?? 'https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}"
                                class="img-fluid item-img">
                        </a>
                    </div>

                    <div class="p-3 d-flex flex-column h-100">
                        <div class="list-card-body mb-2">
                            <h6 class="mb-2 font-weight-bold">
                                <a href="{{ route('product.detail', $product->id) }}" class="text-dark">
                                    {{ $product->productTemplate->name ?? $product->name }}
                                </a>
                            </h6>
                            <small class="text-muted d-block mb-1">
                                <i class="icofont-sale-discount text-danger"></i> Đã bán: {{ $product->sold }}
                            </small>
                            

                            @if ($product->display_mode === 'unit')
                              <small class="text-muted">
                                 {{ $product->weight }} KG/{{ $product->productTemplate->unit ?? 'Đơn vị' }} - HSD: {{ \Carbon\Carbon::parse($product->expiry_date)->format('d/m/Y') }}
                              </small>
                                @if (isset($product->display_unit_original_price) && $product->display_unit_original_price > 0)
                                    <p class="text-success mb-2 d-flex justify-content-between align-items-center fs-6">
                                        @if ($product->display_unit_price < $product->display_unit_original_price)
                                            <del class="text-muted mr-1">{{ number_format($product->display_unit_original_price, 0, ',', '.') }} VNĐ</del>
                                            <span class="bg-light rounded px-2 py-1 font-weight-bold">
                                                {{ number_format($product->display_unit_price, 0, ',', '.') }} VNĐ
                                            </span>
                                            {{-- Tính phần trăm giảm giá cho unit mode nếu muốn --}}
                                            @php
                                                $discount = $product->display_unit_original_price - $product->display_unit_price;
                                                $percent = ($product->display_unit_original_price > 0) ? round(($discount / $product->display_unit_original_price) * 100) : 0;
                                            @endphp
                                            @if ($percent > 0)
                                                <span class="badge badge-light text-danger font-weight-bold px-2">
                                                    -{{ $percent }}%
                                                </span>
                                            @endif
                                        @else
                                            <span class="bg-light rounded px-2 py-1 font-weight-bold">
                                                {{ number_format($product->display_unit_price, 0, ',', '.') }} VNĐ
                                            </span>
                                        @endif
                                    </p>
                                @else
                                     <p class="text-danger mb-2">Hết hàng</p>
                                @endif
                            @elseif ($product->display_mode === 'quantity')
                                {{-- Chế độ QUANTITY: Hiển thị tổng số lượng và giá đã giảm (từ unit HSD gần nhất) --}}
                                <p class="mb-0 text-primary font-weight-bold">
                                    Còn: {{ $product->total_available_quantity }} {{ $product->productTemplate->unit ?? 'sản phẩm' }}
                                </p>
                                @if (isset($product->display_original_price) && $product->display_original_price > 0)
                                    <p class="text-success mb-2 d-flex justify-content-between align-items-center fs-6">
                                        @if ($product->final_display_price < $product->display_original_price)
                                            <del class="text-muted mr-1">{{ number_format($product->display_original_price, 0, ',', '.') }} VNĐ</del>
                                            <span class="bg-light rounded px-2 py-1 font-weight-bold">
                                                {{ number_format($product->final_display_price, 0, ',', '.') }} VNĐ
                                            </span>
                                            @php
                                                $discount = $product->display_original_price - $product->final_display_price;
                                                $percent = ($product->display_original_price > 0) ? round(($discount / $product->display_original_price) * 100) : 0;
                                            @endphp
                                            @if ($percent > 0)
                                                <span class="badge badge-light text-danger font-weight-bold px-2">
                                                    -{{ $percent }}%
                                                </span>
                                            @endif
                                        @else
                                            <span class="bg-light rounded px-2 py-1 font-weight-bold">
                                                {{ number_format($product->final_display_price, 0, ',', '.') }} VNĐ
                                            </span>
                                        @endif
                                    </p>
                                @else
                                    <p class="text-danger mb-2">Hết hàng</p>
                                @endif
                            @endif
                            {{-- KẾT THÚC LOGIC HIỂN THỊ GIÁ MỚI --}}

                        </div> {{-- end list-card-body --}}

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
                                    <span class="btn btn-sm btn-light mx-2 font-weight-bold qty-display" data-id="{{ $product->id }}">
                                        {{ $cartItem['quantity'] }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                        data-id="{{ $product->id }}"
                                        data-qty="{{ $cartItem['quantity'] + 1 }}">
                                        <i class="icofont-plus"></i>
                                    </button>
                                </div>
                            @else
                                <button type="button" class="btn btn-primary btn-sm w-100 btn-add-to-cart" data-id="{{ $product->id }}">
                                    <i class="icofont-cart"></i> Thêm vào giỏ hàng
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
   </div> <!-- End of row -->
</div> <!-- End of container -->

<div class="modal fade" id="productSelectionModal" tabindex="-1" role="dialog" aria-labelledby="productSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productSelectionModalLabel">Chọn chi tiết sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="product-selection-modal-content">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2">Đang tải thông tin sản phẩm...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="confirmAddToCartBtn" disabled>Thêm vào giỏ hàng</button>
            </div>
        </div>
    </div>
</div>

</section>

<script>
   $(document).ready(function(){
      const Toast = Swal.mixin({
         toast: true,
         position: 'top-end',
         showConfirmButton: false,
         timer: 1500,
         timerProgressBar: true,
         didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
         }
      });

      $('.inc').on('click', function() {
         var id = $(this).data('id');
         var input = $(this).closest('span').find('input');
         var newQuantity = parseInt(input.val()) + 1;
         updateQuantity(id, newQuantity);
      });

      $('.dec').on('click', function() {
         var id = $(this).data('id');
         var input = $(this).closest('span').find('input');
         var newQuantity = parseInt(input.val()) - 1;
         if (newQuantity >= 1) {
            updateQuantity(id, newQuantity);
         }
      });

      $('.remove').on('click', function() {
         var id = $(this).data('id');
         removeFromCart(id);
      });

      
      function updateQuantity(id, quantity) {
         $.ajax({
            url: '{{ route("cart.updateQuantity") }}',
            method: 'POST',
            data: {
               _token: '{{ csrf_token() }}',
               id: id,
               quantity: quantity,
            },
            success: function (response) {
               Toast.fire({
                  icon: 'success',
                  title: 'Quantity Updated'
               }).then(() => {
                  location.reload();
               });
            }
         });
      }
      
      function removeFromCart(id) {
         $.ajax({
            url: '{{ route("cart.remove") }}',
            method: 'POST',
            data: {
               _token: '{{ csrf_token() }}',
               id: id,
            },
            success: function (response) {
               Toast.fire({
                  icon: 'success',
                  title: 'Cart Remove Successfully'
               }).then(() => {
                  location.reload();
               });
            }
         });
      }

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
               const clientId = document.querySelector('input[name="client_id"]').value;
               verificationMessage.innerHTML = '<div class="alert alert-info">Đang kiểm tra đơn hàng của bạn...</div>';

               try {
                  const response = await fetch('/verify-order-for-review', {
                        method: 'POST',
                        headers: {
                           'Content-Type': 'application/json',
                           'Accept': 'application/json',
                           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ client_id: clientId })
                  });

                  const data = await response.json();

                  if (data.success) {
                        console.log(data);
                        verificationMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                        orderIdForReview.value = data.order_id;
                        reviewForm.style.display = 'block';
                        leaveReviewButton.style.display = 'none';
                        reviewForm.scrollIntoView({ behavior: 'smooth' });
                  } else {
                        verificationMessage.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                  }
               } catch (error) {
                  verificationMessage.innerHTML = '<div class="alert alert-danger">Lỗi xảy ra khi kiểm tra. Vui lòng thử lại.</div>';
               }
            });
         } else {
            console.log('Không tìm thấy nút leaveReviewButton');
         }
    });

   document.querySelectorAll('.star-rating input[type="radio"]').forEach(radio => {
      radio.addEventListener('change', () => {
         const ratingValue = parseInt(radio.value);
         const stars = document.querySelectorAll('.star-rating label i');
         
         stars.forEach((star, index) => {
               if (index < ratingValue) {
                  star.style.color = '#f39c12'; // màu vàng cho sao được chọn và các sao trước đó
               } else {
                  star.style.color = '#ccc'; // màu xám cho sao chưa chọn
               }
         });
      });
   });

   function copyToClipboard(id) {
      var copyText = document.getElementById(id);
      copyText.select();
      copyText.setSelectionRange(0, 99999); // For mobile
      document.execCommand("copy");
   }
</script>

@endsection
