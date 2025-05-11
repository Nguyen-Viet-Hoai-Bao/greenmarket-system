@extends('frontend.master')
@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<section class="section pt-5 pb-5 products-section">
  <div class="container">
     <div class="section-header text-center">
        <h2>Cửa hàng phổ biến</h2>
        <p>Các cửa hàng hàng đầu, rau củ, sữa và nhiều hơn nữa...</p>
        <span class="line"></span>
     </div>
     <div class="row">

@php
    $clients = App\Models\Client::latest()->where('status', '1')->get();
@endphp

@foreach ($clients as $client)
    
   @php
       $products = App\Models\Product::where('client_id', $client->id)->limit(3)->get();
       $menuNames = $products->map(function($product){
         return $product->menu->menu_name;
       })->unique()->toArray();
       $menuNamesString = implode('. ', $menuNames);
       $coupons = App\Models\Coupon::where('client_id', $client->id)
                           ->where('status', '1')->first();
      
      $isWishlisted = App\Models\Wishlist::where('user_id', Auth::id())
                        ->where('client_id', $client->id)
                        ->first();
   @endphp

   @php
      $reviewcount = App\Models\Review::where('client_id',$client->id)->where('status',1)->latest()->get();
      $avarage = App\Models\Review::where('client_id',$client->id)->where('status',1)->avg('rating');
   @endphp

<div class="col-md-3">
   <div class="item pb-3">
      <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
         <div class="list-card-image">
            <div class="star position-absolute"><span class="badge badge-success"><i class="icofont-star"></i>{{ number_format($avarage,1) }} ({{ count($reviewcount ) }}+)</span></div>

            {{-- <div class="favourite-heart text-danger position-absolute">
               <a aria-label="Add to Wishlist" onclick="addWishlist({{ $client->id }})">
                  <i class="icofont-heart">
                  </i>
               </a>
            </div> --}}
            <div class="favourite-heart position-absolute">
               <a aria-label="Add to Wishlist" onclick="addWishlist({{ $client->id }})">
                   <i class="icofont-heart {{ $isWishlisted ? 'text-danger' : 'text-dark' }}" id="heart-icon-{{ $client->id }}">
                   </i>
               </a>
           </div>
            @if ($coupons)
               <div class="member-plan position-absolute"><span class="badge badge-dark">Được quảng cáo</span></div>
            @else

            @endif

            <a href="{{ route('market.details', $client->id) }}">
            <img src="{{ asset('upload/client_images/' . $client->photo) }}" class="img-fluid item-img" style="width: 300px; hight:200px">
            </a>
         </div>
         <div class="p-3 position-relative">
            <div class="list-card-body">
               <h6 class="mb-1"><a href="detail.html" class="text-black">{{ $client->name }}</a></h6>
               <p class="text-gray mb-3">{{ $menuNamesString }}</p>
               <p class="text-gray mb-3 time"><span class="bg-light text-dark rounded-sm pl-2 pb-1 pt-1 pr-2"><i class="icofont-wall-clock"></i> 20–25 phút</span> <span class="float-right text-black-50"> 250.000 VNĐ cho 2 người</span></p>
            </div>
            <div class="list-card-badge">
               @if ($coupons)
                  <span class="badge badge-success">KHUYẾN MÃI</span> <small>{{ $coupons->discount }}% giảm | Dùng mã {{ $coupons->coupon_name }}</small>
               @else
                  <span class="badge badge-success">KHUYẾN MÃI</span> <small>Chưa có mã giảm giá</small>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>

@endforeach
     </div>


     <div class="section-header text-center">
      <h2>Danh sách sản phẩm</h2>
      <p>Các cửa hàng hàng đầu, rau củ, sữa và nhiều hơn nữa...</p>
      <span class="line"></span>
   </div>
   
   <div class="col-12">
      <div class="row" id="product-list">
         @foreach ($products_list as $product)
            <div class="custom-col mb-4">
               <div class="card h-100 shadow-sm rounded border-0 d-flex flex-column">
                  {{-- <a href="{{ route('market.details', $product->client_id) }}" class="text-decoration-none"> --}}
                  <div class="text-decoration-none">
                     <img src="{{ asset(optional($product->productTemplate)->image ?? 'upload/no_image.jpg') }}"
                        class="card-img-top"
                        alt="{{ optional($product->productTemplate)->name }}"
                        style="height:200px; object-fit:cover;">
                  </div>
                  <div class="card-body d-flex flex-column">
                     <h5 class="card-title mb-2" style="min-height: 48px;">
                        {{-- <a href="{{ route('market.details', $product->client_id) }}" class="text-dark"> --}}
                        <div class="text-dark">
                           {{ optional($product->productTemplate)->name ?? 'No Name' }}
                        </div>
                     </h5>
                     <p class="card-text text-muted mb-2" style="font-size:14px; min-height: 20px;">
                        {{ $product->productTemplate->category->category_name ?? 'No Category' }}
                     </p>
                     <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-success font-weight-bold">{{ number_format($product->price) }} VNĐ</span>
                        @php
                           $discountPercent = null;
                           if (!empty($product->discount_price) && $product->price > 0) {
                              $discountPercent = round((1 - $product->discount_price / $product->price) * 100);
                           }
                        @endphp
                        @if ($discountPercent)
                           <span class="badge badge-danger">{{ $discountPercent }}% GIẢM</span>
                        @else
                           <span class="badge badge-secondary">Không có giảm giá</span>
                        @endif
                     </div>
            
                     <div class="mt-auto">
                        <button type="button"
                           class="btn btn-sm btn-primary w-100"
                           data-bs-toggle="modal"
                           data-bs-target="#chooseMarketModal"
                           data-product-id="{{ $product->id }}">
                           <i class="icofont-cart"></i> Thêm vào giỏ
                        </button>
                     </div>
                     {{-- <div class="mt-auto">
                        @if(session()->has('selected_market_id'))
                           <a href="{{ route('add_to_cart', $product->id) }}" 
                              class="btn btn-sm btn-primary w-100">
                                 <i class="icofont-cart"></i> Thêm vào giỏ
                           </a>
                        @else
                           <button type="button"
                                    class="btn btn-sm btn-primary w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#chooseMarketModal">
                                 <i class="icofont-cart"></i> Thêm vào giỏ
                           </button>
                        @endif
                     </div> --}}

                  </div>
               </div>
            </div>
         
         @endforeach
      </div>
   </div>

</div>
</section>


<!-- Modal: Choose Market -->
<div id="chooseMarketModal" class="modal fade" tabindex="-1" aria-labelledby="chooseMarketLabel" aria-hidden="true" data-bs-scroll="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <form id="marketSelectorForm" method="GET" action="{{ route('market.details.redirect') }}">
               @csrf
               <input type="hidden" name="product_id" id="selectedProductId">
               
               <div class="modal-header">
                   <h5 class="modal-title" id="chooseMarketLabel">Chọn địa điểm và cửa hàng</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
               </div>

               <div class="modal-body">
                   <div class="mb-3">
                       <label for="cityDropdown" class="form-label">Thành phố</label>
                       <select class="form-select" name="city_id" id="cityDropdown">
                           <option value="">-- Chọn thành phố --</option>
                           @foreach ($cities as $city)
                               <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                           @endforeach
                       </select>
                   </div>

                   <div class="mb-3">
                       <label for="districtDropdown" class="form-label">Quận/Huyện</label>
                       <select class="form-select" name="district_id" id="districtDropdown">
                           <option value="">-- Chọn quận/huyện --</option>
                       </select>
                   </div>

                   <div class="mb-3">
                       <label for="wardDropdown" class="form-label">Phường/Xã</label>
                       <select class="form-select" name="ward_id" id="wardDropdown">
                           <option value="">-- Chọn phường/xã --</option>
                       </select>
                   </div>

                   <div class="mb-3">
                       <label for="marketDropdown" class="form-label">Cửa hàng</label>
                       <select class="form-select" name="market_id" id="marketDropdown">
                           <option value="">-- Chọn cửa hàng --</option>
                       </select>
                   </div>
               </div>

               <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                   <button type="submit" class="btn btn-primary">Xem chi tiết cửa hàng</button>
               </div>
           </form>
       </div>
   </div>
</div>

<style>
#product-list .card {
   min-height: 100%;
   display: flex;
   flex-direction: column;
}

.custom-col {
      width: 20%;
      padding: 0 10px;
      box-sizing: border-box;
   }

   @media (max-width: 992px) {
      .custom-col {
         width: 33.3333%; /* 3 cột trên tablet */
      }
   }
   @media (max-width: 768px) {
      .custom-col {
         width: 50%; /* 2 cột trên mobile */
      }
   }
   @media (max-width: 576px) {
      .custom-col {
         width: 100%; /* 1 cột nhỏ */
      }
   }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
   document.addEventListener('DOMContentLoaded', function () {
       const chooseMarketModal = document.getElementById('chooseMarketModal');
   
       // Gán sự kiện khi modal hiển thị
       chooseMarketModal.addEventListener('shown.bs.modal', function (event) {
           const productId = event.relatedTarget.getAttribute('data-product-id');
           document.getElementById('selectedProductId').value = productId;
   
           const city = chooseMarketModal.querySelector('#cityDropdown');
           const district = chooseMarketModal.querySelector('#districtDropdown');
           const ward = chooseMarketModal.querySelector('#wardDropdown');
           const market = chooseMarketModal.querySelector('#marketDropdown');
   
           // Gỡ event cũ trước khi gán lại
           city.onchange = async function () {
               const cityId = this.value;
               district.innerHTML = '<option>Loading...</option>';
               ward.innerHTML = '<option>-- Chọn phường/xã --</option>';
               market.innerHTML = '<option>-- Chọn cửa hàng --</option>';
   
               if (cityId) {
                   const res = await fetch(`/get-districts/${cityId}`);
                   const data = await res.json();
                   district.innerHTML = '<option>-- Chọn quận/huyện --</option>';
                   data.forEach(d => {
                       district.innerHTML += `<option value="${d.id}">${d.district_name}</option>`;
                   });
               }
           };
   
           district.onchange = async function () {
               const districtId = this.value;
               ward.innerHTML = '<option>Loading...</option>';
               market.innerHTML = '<option>-- Chọn cửa hàng --</option>';
   
               if (districtId) {
                   const res = await fetch(`/get-wards/${districtId}`);
                   const data = await res.json();
                   ward.innerHTML = '<option>-- Chọn phường/xã --</option>';
                   data.forEach(w => {
                       ward.innerHTML += `<option value="${w.id}">${w.ward_name}</option>`;
                   });
               }
           };
   
           ward.onchange = async function () {
               const wardId = this.value;
               market.innerHTML = '<option>Loading...</option>';
   
               if (wardId) {
                   const res = await fetch(`/get-markets-by-ward/${wardId}`);
                   const data = await res.json();
                   market.innerHTML = '<option>-- Chọn cửa hàng --</option>';
                   data.forEach(m => {
                       market.innerHTML += `<option value="${m.id}">${m.name}</option>`;
                   });
               }
           };
       });
   });
</script>
   


@endsection