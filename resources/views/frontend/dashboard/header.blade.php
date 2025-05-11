
      <nav class="navbar navbar-expand-lg navbar-light bg-light osahan-nav shadow-sm">
         <div class="container">
            <a class="navbar-brand" href="{{ route('index') }}"><img alt="logo" src="{{ asset('frontend/img/logo.png') }}"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
               <ul class="navbar-nav ml-auto">
                  <li class="nav-item active">
                     <a class="nav-link" href="index.html">Trang Chủ <span class="sr-only">(current)</span></a>
                  </li>
                  
           <li class="nav-item">
            @if (session()->has('selected_market_id') && isset($fullAddress))
               <a class="nav-link text-success"
                  href="#"
                  data-bs-toggle="modal"
                  data-bs-target="#chooseMarketModal">
                  <i class="icofont-location-pin"></i>
                  {{ session('selected_market_name') }} - {{ $fullAddress }}
               </a>
            @else
               <a class="nav-link"
                  href="#"
                  data-bs-toggle="modal"
                  data-bs-target="#chooseMarketModal">
                  <i class="icofont-cart"></i> Chọn cửa hàng
               </a>
            @endif
         </li>       
                  <li class="nav-item dropdown">
                     <a class="nav-link" href="{{ route('list.market') }}" role="button" aria-haspopup="true" aria-expanded="false">
                     Cửa hàng
                     </a>
                  </li>


@auth
@php
   $id = Auth::user()->id;
   $profileData = App\Models\User::find($id);
@endphp
<li class="nav-item dropdown">
   <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
   <img alt="Generic placeholder image" 
      src="{{ (!empty($profileData->photo)) 
      ? url('upload/user_images/'.$profileData->photo)
      : url('upload/no_image.jpg')}}"    
      class="nav-osahan-pic rounded-pill"> Tài Khoản Của Tôi
   </a>
   <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
      <a class="dropdown-item" href="{{ route('dashboard') }}"><i class="icofont-food-cart"></i>Bảng Điều Khiển</a>
      <a class="dropdown-item" href="{{ route('user.logout') }}"><i class="icofont-sale-discount"></i>Đăng Xuất</a>
   </div>
</li>
@else
<li class="nav-item dropdown">
   <a class="nav-link" href="{{ route('login') }}" role="button" aria-haspopup="true" aria-expanded="false">
      Đăng Nhập
   </a>
</li>
<li class="nav-item dropdown">
   <a class="nav-link" href="{{ route('register') }}" role="button" aria-haspopup="true" aria-expanded="false">
      Đăng Ký
   </a>
</li>
@endauth


@php
    $total = 0;
    $cart = session()->get('cart', []);
    $groupedCart = [];

    foreach ($cart as $item) {
      $groupedCart[$item['client_id']][] = $item;
    }

    $clients = App\Models\Client::whereIn('id', array_keys($groupedCart))
                                 ->get()
                                 ->keyBy('id');

@endphp

<li class="nav-item dropdown dropdown-cart">
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
</li>
               </ul>
            </div>
         </div>
      </nav>

      

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
