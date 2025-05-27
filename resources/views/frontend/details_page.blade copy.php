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
      color: #f39c12; /* màu vàng cho sao được chọn hoặc đang hover */
   }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

@php
// $products = App\Models\ProductNew::with('productTemplate.menu')
//     ->where('client_id', $client->id)
//     ->limit(3)
//     ->get();
$products = \App\Models\ProductNew::with('productTemplate.menu')
   ->where('client_id', $client->id)
   ->get()
   ->groupBy(fn($product) => $product->productTemplate?->menu?->id)
   ->map(fn($group) => $group->take(3))
   ->flatten();

$menuNames = $products->map(function($product) {
    return $product->productTemplate?->menu?->menu_name;
})->filter()->unique()->toArray();

$menuNamesString = implode('. ', $menuNames);

$coupons = App\Models\Coupon::where('client_id', $client->id)
                    ->where('status', '1')->first();
@endphp

<section class="restaurant-detailed-banner">
  <div class="text-center">
     <img class="img-fluid cover" src="{{ asset('upload/client_images/'. $client->cover_photo) }}">
  </div>
  <div class="restaurant-detailed-header">
     <div class="container">
        <div class="row d-flex align-items-end">
           <div class="col-md-8">
              <div class="restaurant-detailed-header-left">
                 <img class="img-fluid mr-3 float-left" alt="osahan" src="{{ asset('upload/client_images/'. $client->photo) }}">
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
                     {{ $isFavourite ? 'Unmark Favourite' : 'Mark as Favourite' }}
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
  @php
    $populers = App\Models\ProductNew::with('productTemplate')
                ->where('status', 1)
                ->where('client_id', $client->id)
                ->where('most_popular', 1)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
  @endphp
  <div id="menu" class="bg-white rounded shadow-sm p-4 mb-4 explore-outlets">
   <h6 class="mb-3">Sản Phẩm Phổ Biến Nhất <span class="badge badge-success"><i class="icofont-tags"></i> Giảm 15% Cho Tất Cả Sản Phẩm </span></h6>
   <div class="owl-carousel owl-theme owl-carousel-five offers-interested-carousel mb-3">
       @foreach ($populers as $populer)
           <div class="item">
               <div class="mall-category-item">
                   <a href="{{ route('product.detail', $populer->id) }}">
                       <img class="img-fluid" src="{{ asset($populer->productTemplate->image ?? 'upload/no_image.jpg') }}" alt="">
                       <h6>{{ $populer->productTemplate->name ?? $populer->name }}</h6>

                       @if ($populer->discount_price == NULL)
                           {{ number_format($populer->price, 0, ',', '.') }}
                       @else
                           <del>{{ number_format($populer->price, 0, ',', '.') }}</del>
                           {{ number_format($populer->discount_price, 0, ',', '.') }}
                       @endif

                       <span class="float-right">
                           <a class="btn btn-outline-secondary btn-sm" href="{{ route('add_to_cart', $populer->id) }}">
                              THÊM VÀO GIỎ
                           </a>
                       </span>
                   </a>
               </div>
           </div>
       @endforeach
   </div>
</div>


{{-- Best Sellers --}}
   @php
   $bestsellers = App\Models\ProductNew::with('productTemplate')
               ->where('status', 1)
               ->where('client_id', $client->id)
               ->where('best_seller', 1)
               ->orderBy('id', 'desc')
               ->limit(3)
               ->get();
   @endphp

<div class="row">
   <h5 class="mb-4 mt-3 col-md-12">Bán Chạy Nhất</h5>
   @foreach ($bestsellers as $bestseller)
     <div class="col-md-4 col-sm-6 mb-4">
       <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
           <div class="list-card-image">
             <div class="star position-absolute"><span class="badge badge-success"><i class="icofont-star"></i> 3.1 (300+)</span></div>
             <div class="favourite-heart text-danger position-absolute"><a href="#"><i class="icofont-heart"></i></a></div>
             <div class="member-plan position-absolute"><span class="badge badge-dark">Được Quảng Bá</span></div>
            {{-- <a href="{{ route('product.detail', $product->id) }}"><i class="icofont-heart"></i></a> --}}
             <a href="{{ route('product.detail', $bestseller->id) }}">
               <img src="{{ asset($bestseller->productTemplate->image ?? $bestseller->image) }}" class="img-fluid item-img" alt="">
             </a>
           </div>
           <div class="p-3 position-relative">
             <div class="list-card-body">
                 <h6 class="mb-1">
                   <a href="#" class="text-black">
                     {{ $bestseller->productTemplate->name ?? $bestseller->name }}
                   </a>
                 </h6>
                 <p class="text-gray mb-2">
                   {{ $bestseller->productTemplate->category->category_name ?? '-' }}
                 </p>
                 <p class="text-gray time mb-0">
                   @if ($bestseller->discount_price == NULL)
                     <a class="btn btn-link btn-sm text-black" href="#">
                       {{ number_format($bestseller->price, 0, ',', '.') }}
                     </a>  
                   @else
                     <del>{{ number_format($bestseller->price, 0, ',', '.') }}</del>
                     <a class="btn btn-link btn-sm text-black" href="#">
                       {{ number_format($bestseller->discount_price, 0, ',', '.') }}
                     </a>  
                   @endif
                   <span class="float-right"> 
                     <a class="btn btn-outline-secondary btn-sm" href="{{ route('add_to_cart', $bestseller->id) }}">
                        THÊM VÀO GIỎ
                     </a>
                   </span>
                 </p>
             </div>
           </div>
       </div>
     </div>
   @endforeach
 </div>
 
 @foreach ($menus as $menu)
 <div class="row">
   <h5 class="mb-4 mt-3 col-md-12">
       {{ $menu->menu_name }}
       <small class="h6 text-black-50">{{ $menu->products->sum(fn($p) => $p->productNews->count()) }} Sản phẩm</small>
   </h5>
   <div class="col-md-12">
     <div class="bg-white rounded border shadow-sm mb-4">
         @php
            $count = 0;
         @endphp

         @foreach ($menu->products as $productTemplate)
            @foreach ($productTemplate->productNews as $product)
               @if ($count >= 5)
                     @break 2 {{-- Thoát cả hai vòng lặp --}}
               @endif

               <div class="menu-list p-3 border-bottom">
                     <a class="btn btn-outline-secondary btn-sm float-right" 
                        href="{{ route('add_to_cart', $product->id) }}">
                        THÊM VÀO GIỎ
                     </a>
                     <div class="media">
                        <img class="mr-3 rounded-pill" 
                              src="{{ asset($productTemplate->image ?? 'upload/no_image.jpg') }}" 
                              alt="{{ $product->name }}">
                        <div class="media-body">
                           <h6 class="mb-1">{{ $productTemplate->name }}</h6>
                           <p class="text-gray mb-0">
                                 @if ($product->discount_price == NULL)
                                    {{ number_format($product->price, 0, ',', '.') }} VNĐ 
                                 @else
                                    <del>{{ number_format($product->price, 0, ',', '.') }}</del> 
                                    {{ number_format($product->discount_price, 0, ',', '.') }} VNĐ 
                                 @endif
                                 ({{ $productTemplate->size ?? '' }} {{ $productTemplate->unit }})
                           </p>
                        </div>
                     </div>
               </div>

               @php $count++; @endphp
            @endforeach
         @endforeach

       
     </div>
     
     <div class="text-center my-3">
      <a href="{{ route('list.market') }}" class="btn btn-success px-4 py-2">Xem thêm <i class="icofont-long-arrow-right"></i></a>
    </div>
   </div>
 </div>
@endforeach

                    
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
 
                 {{-- <div class="tab-pane fade" id="pills-book" role="tabpanel" aria-labelledby="pills-book-tab">
                    <div id="book-a-table" class="bg-white rounded shadow-sm p-4 mb-5 rating-review-select-page">
                       <h5 class="mb-4">Book A Table</h5>
                       <form>
                          <div class="row">
                             <div class="col-sm-6">
                                <div class="form-group">
                                   <label>Full Name</label>
                                   <input class="form-control" type="text" placeholder="Enter Full Name">
                                </div>
                             </div>
                             <div class="col-sm-6">
                                <div class="form-group">
                                   <label>Email Address</label>
                                   <input class="form-control" type="text" placeholder="Enter Email address">
                                </div>
                             </div>
                          </div>
                          <div class="row">
                             <div class="col-sm-6">
                                <div class="form-group">
                                   <label>Mobile number</label>
                                   <input class="form-control" type="text" placeholder="Enter Mobile number">
                                </div>
                             </div>
                             <div class="col-sm-6">
                                <div class="form-group">
                                   <label>Date And Time</label>
                                   <input class="form-control" type="text" placeholder="Enter Date And Time">
                                </div>
                             </div>
                          </div>
                          <div class="form-group text-right">
                             <button class="btn btn-primary" type="button"> Submit </button>
                          </div>
                       </form>
                    </div>
                 </div> --}}
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
            <a href="#"><img alt="Generic placeholder image" src="{{ (!empty($review->user->photo)) ? url('upload/user_images/'.$review->user->photo) : url('upload/no_image.jpg') }}" class="mr-3 rounded-pill"></a>
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
               <div class="reviews-members-footer">
                  <a class="total-like" href="#"><i class="icofont-thumbs-up"></i> ...</a> <a class="total-like" href="#"><i class="icofont-thumbs-down"></i> ...</a> 
                  
               </div>
            </div>
         </div>
      </div>

      @endforeach
      <hr>
      <hr>
   <a class="text-center w-100 d-block mt-4 font-weight-bold" href="#">Xem Tất Cả Nhận Xét</a>
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

                     <div class="modal fade" id="orderVerificationModal" tabindex="-1" role="dialog" aria-labelledby="orderVerificationModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                           <div class="modal-content">
                                 <div class="modal-header">
                                    <h5 class="modal-title" id="orderVerificationModalLabel">Nhập chi tiết đơn hàng của bạn</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                       <span aria-hidden="true">&times;</span>
                                    </button>
                                 </div>
                                 <div class="modal-body">
                                    <p>Vui lòng kiểm tra đơn hàng đã đặt để tìm mã số đơn hàng và email đặt hàng:</p>
                                    <form id="orderVerificationForm">
                                       <div class="form-group">
                                             <label for="orderCodeInput">Mã số đơn hàng</label>
                                             <input type="text" class="form-control" id="orderCodeInput" required>
                                       </div>
                                       <div class="form-group">
                                             <label for="orderEmailInput">Email đặt hàng</label>
                                             <input type="email" class="form-control" id="orderEmailInput" required>
                                       </div>
                                       <div class="alert alert-info mt-3" role="alert">
                                             Chỉ khách đặt hàng thành công qua website chúng tôi mới có thể viết đánh giá. Điều này giúp chúng tôi thu thập các đánh giá từ khách thực, như bạn vậy.
                                       </div>
                                       <div id="verificationMessage" class="mt-3"></div>
                                    </form>
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                    <button type="button" class="btn btn-primary" id="verifyOrderButton">Đánh giá trải nghiệm đặt hàng của bạn</button>
                                 </div>
                           </div>
                        </div>
                     </div>
                
                   @endguest
                   </div>


                 </div>
              </div>
           </div>
        </div>

@php
    $coupon = App\Models\Coupon::where('client_id', $client->id)
                                 ->where('validity', '>=', Carbon\Carbon::now()->format('Y-m-d'))
                                 ->latest()
                                 ->first();
@endphp        

         <div class="col-md-4">
            <div class="pb-2">
               <div class="bg-white rounded shadow-sm text-white mb-4 p-4 clearfix restaurant-detailed-earn-pts card-icon-overlap">
               <img class="img-fluid float-left mr-3" src="{{ asset('frontend/img/earn-score-icon.png') }}">
               <h6 class="pt-0 text-primary mb-1 font-weight-bold">ƯU ĐÃI</h6>
               
               {{-- <pre>{{ print_r(Session::get('coupon'), true) }}</pre> --}}

               @if ($coupon == NULL)
                  <p class="mb-0">Không có mã giảm giá
                  </p>
               @else
                  <p class="mb-0">
                     <span class="text-danger font-weight-bold">{{ $coupon->discount }}</span>% cho đơn hàng | Sử dụng mã 
                     <span class="text-danger font-weight-bold">{{ $coupon->coupon_name }}</span>
                  </p>
               @endif

               <div class="icon-overlap">
                  <i class="icofont-sale-discount"></i>
               </div>
            </div>
            </div>
           <div class="generator-bg rounded shadow-sm mb-4 p-4 osahan-cart-item">
              <h5 class="mb-1 text-white">Đơn Hàng Của Bạn</h5>
              <p class="mb-4 text-white">{{ count((array) session('cart')) }} Sản phẩm</p>

<div class="bg-white rounded shadow-sm mb-2">

@php
    $total = 0;
@endphp

 @if (session('cart'))
    @foreach (session('cart') as $id=>$details)

    @php
      //   $total += $details['price'] * $details['quantity']
      $total += (float) $details['price'] * (int) $details['quantity'];
        
    @endphp
        
      <div class="gold-members p-2 border-bottom">
         <p class="text-gray mb-0 float-right ml-2 item-total" id="item-total-{{ $id }}">
            {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
         </p>
         <span class="count-number float-right">

         <button class="btn btn-outline-secondary  btn-sm left dec" 
                  data-id="{{ $id }}">
            <i class="icofont-minus"></i> 
         </button>

         <input class="count-number-input" type="text" 
                  value="{{ $details['quantity'] }}" 
                  id="qty-{{ $id }}"
                  readonly="">

         <button class="btn btn-outline-secondary btn-sm right inc" 
                  data-id="{{ $id }}"> 
            <i class="icofont-plus"></i> 
         </button>

         <button class="btn btn-outline-danger btn-sm right remove"
                  data-id="{{ $id }}"> 
            <i class="icofont-trash"></i> 
         </button>

         </span>
         <div class="media">
            <div class="mr-2">
               <img src="{{ asset($details['image']) }}" alt="" width="25px"></img>
            </div>
            <div class="media-body">
               <p class="mt-1 mb-0 text-black">{{ $details['name'] }}</p>
            </div>
         </div>
      </div>

      @endforeach
  @else
      
  @endif

</div>

@if (Session::has('coupon'))
   <div class="mb-2 bg-white rounded p-2 clearfix">
      <p class="mb-1">Tổng số sản phẩm
         <span class="float-right text-dark">
            {{ count((array) session('cart')) }}
         </span>
      </p>
      <p class="mb-1">Mã giảm giá
         <span class="float-right text-dark">
            {{ (session()->get('coupon')['coupon_name']) }}
            ({{ (session()->get('coupon')['discount']) }}%)
            
            <a type="submit" onclick="CouponRemove()">
               <i class="icofont-ui-delete float-right" style="color: red;"></i>
            </a>
         </span>
      </p>
      <p class="mb-1 text-success">Tổng tiền sau giảm giá
         <span class="float-right text-success">
            @if (Session::has('coupon'))
               {{ number_format($total - Session()->get('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
            @else
               {{ number_format(0, 0, ',', '.') }} VNĐ
            @endif
         </span>
      </p>
      <hr />
      <h6 class="font-weight-bold mb-0">SỐ TIỀN CẦN THANH TOÁN  
         <span class="float-right">
            @if (Session::has('coupon'))
               {{ number_format(Session()->get('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
            @else
               {{ number_format($total, 0, ',', '.') }} VNĐ
            @endif
         </span>
      </h6>
   </div>
@else
   <div class="mb-2 bg-white rounded p-2 clearfix">
      <div class="input-group input-group-sm mb-2">
         <input type="text" class="form-control" placeholder="Enter promo code" id="coupon_name">
         <div class="input-group-append">
            <button class="btn btn-primary" type="submit" id="button-addon2" onclick="ApplyCoupon()">
               <i class="icofont-sale-discount"></i> 
               ÁP DỤNG
            </button>
         </div>
      </div>
   </div>
@endif


<div class="mb-2 bg-white rounded p-2 clearfix">
   <img class="img-fluid float-left" src="{{ asset('frontend/img/wallet-icon.png') }}">
   <h6 class="font-weight-bold text-right mb-2">Tạm tính : 
      <span class="text-danger">
         @if (Session::has('coupon'))
            {{ number_format(Session()->get('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
         @else
            {{ number_format($total, 0, ',', '.') }} VNĐ
         @endif
      </span>
   </h6>
   <p class="seven-color mb-1 text-right">Phụ phí có thể được áp dụng</p>
</div>
              <a href="{{ route('checkout') }}" class="btn btn-success btn-block btn-lg">Thanh toán <i class="icofont-long-arrow-right"></i></a>
           </div>
   
   <div class="text-center pt-2 mb-4">
   </div>
   <div class="text-center pt-2">
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

    document.addEventListener('DOMContentLoaded', function () {
        const leaveReviewButton = document.getElementById('leaveReviewButton');
        const orderVerificationModal = new bootstrap.Modal(document.getElementById('orderVerificationModal')); // Sử dụng Bootstrap 5
        const orderVerificationForm = document.getElementById('orderVerificationForm');
        const orderCodeInput = document.getElementById('orderCodeInput');
        const orderEmailInput = document.getElementById('orderEmailInput');
        const verifyOrderButton = document.getElementById('verifyOrderButton');
        const reviewForm = document.getElementById('reviewForm');
        const orderIdForReview = document.getElementById('orderIdForReview');
        const verificationMessage = document.getElementById('verificationMessage');

        // Khi nút "Để lại đánh giá của bạn" được click
        leaveReviewButton.addEventListener('click', function() {
            // Reset form và thông báo
            orderVerificationForm.reset();
            verificationMessage.innerHTML = '';
            verificationMessage.className = 'mt-3'; // Reset class
            orderVerificationModal.show(); // Hiển thị modal xác thực đơn hàng
        });

        // Khi nút "Đánh giá trải nghiệm đặt hàng của bạn" trong modal được click
        verifyOrderButton.addEventListener('click', async function() {
            const orderCode = orderCodeInput.value.trim();
            const orderEmail = orderEmailInput.value.trim();

            if (!orderCode || !orderEmail) {
                verificationMessage.innerHTML = '<div class="alert alert-danger">Vui lòng điền đầy đủ Mã số đơn hàng và Email.</div>';
                return;
            }

            verificationMessage.innerHTML = '<div class="alert alert-info">Đang kiểm tra đơn hàng...</div>';
            verificationMessage.className = 'mt-3'; // Reset class

            try {
                const response = await fetch('/verify-order-for-review', { // Endpoint để xác thực đơn hàng (sẽ tạo ở Laravel)
                    method: 'POST',
                    headers: {
                     'Content-Type': 'application/json',
                     'Accept': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                     },
                    body: JSON.stringify({
                        order_code: orderCode,
                        order_email: orderEmail,
                        client_id: document.querySelector('input[name="client_id"]').value // Lấy client_id từ hidden input
                    })
                });

                const data = await response.json();

                if (data.success) {
                    verificationMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    orderIdForReview.value = data.order_id; // Đặt order_id vào hidden input của form đánh giá
                    orderVerificationModal.hide(); // Ẩn modal
                    reviewForm.style.display = 'block'; // Hiển thị form đánh giá
                    leaveReviewButton.style.display = 'none'; // Ẩn nút "Để lại đánh giá"
                    // Cuộn tới form đánh giá
                    reviewForm.scrollIntoView({ behavior: 'smooth' });

                } else {
                    verificationMessage.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            } catch (error) {
                console.error('Error verifying order:', error);
                verificationMessage.innerHTML = '<div class="alert alert-danger">Đã xảy ra lỗi khi kiểm tra đơn hàng. Vui lòng thử lại sau.</div>';
            }
        });
    });
</script>

@endsection
