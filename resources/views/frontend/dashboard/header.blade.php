<style>
.fixed-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1030;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Tăng độ rộng modal dialog */
.modal-dialog {
    max-width: 600px;
    width: 90%;
}

/* Input tìm kiếm chuyển động rộng */
.search-input {
    width: 250px;
    transition: width 0.4s ease-in-out;
}
.search-input:focus {
    width: 350px;
}
</style>

      <nav class="navbar navbar-expand-lg navbar-light bg-light osahan-nav shadow-sm fixed-header">
         <div class="container">
            <a class="navbar-brand" href="{{ route('index') }}"><img alt="logo" src="{{ asset('frontend/img/logo.png') }}"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <form id="search-form" class="d-flex mx-auto">
               <input
                  class="form-control me-2 search-input"
                  type="search"
                  name="query"
                  placeholder="Bạn cần tìm gì nhỉ?"
                  aria-label="Search"
                  onfocus="this.placeholder='Nhập tên sản phẩm bạn cần tìm'"
                  onblur="this.placeholder='Bạn cần tìm gì nhỉ?'"
               >
               <button class="btn btn-outline-success" type="submit">Tìm</button>
            </form>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
               <ul class="navbar-nav ml-auto">
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
                  @php
                     $selectedMarketId = session('selected_market_id');
                  @endphp

                  @if ($selectedMarketId)
                     <li class="nav-item dropdown">
                        <a class="nav-link" href="{{ route('market.details', ['id' => $selectedMarketId]) }}" role="button" aria-haspopup="true" aria-expanded="false">
                              Cửa hàng của bạn
                        </a>
                     </li>
                  @endif



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
                        <a class="dropdown-item" href="{{ route('dashboard') }}"><i class="icofont-food-cart"></i>Thông tin cá nhân</a>
                        <a class="dropdown-item" href="{{ route('user.logout') }}"><i class="icofont-sale-discount"></i>Đăng Xuất</a>
                     </div>
                  </li>
                  @else
                  <li class="nav-item dropdown">
                     <a class="nav-link" href="{{ route('login') }}" role="button" aria-haspopup="true" aria-expanded="false">
                        Đăng Nhập
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
   <div id="cart-header-container">
      @include('frontend.cart.header_partial')
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
           {{-- <form id="marketSelectorForm" method="GET" action="{{ route('market.details.redirect') }}"> --}}
           <form id="marketSelectorForm" method="GET">
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
   
   document.getElementById('marketSelectorForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const marketId = document.getElementById('marketDropdown').value;
      if (!marketId) {
         alert('Vui lòng chọn cửa hàng');
         return;
      }

      // Chuyển hướng đến route Laravel có dạng /market/details/{id}
      window.location.href = `/market/details/${marketId}`;
   });

</script>

