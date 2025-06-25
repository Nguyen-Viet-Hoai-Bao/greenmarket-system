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
.nav-item.dropdown:hover .dropdown-menu {
    display: block;
    margin-top: 0; /* fix bug khoảng cách */
}
.notification-read{
    background-color: #e9e9e9 !important;
    color: #6c757d !important;
    font-weight: normal !important;
}
.notification-unread {
    background-color: bisque !important;
    color: #212529 !important;
    font-weight: bold !important;
}
.dropdown-menu div[style*="overflow-y: auto"]::-webkit-scrollbar {
    width: 6px; /* Smaller scrollbar */
}

.dropdown-menu div[style*="overflow-y: auto"]::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.dropdown-menu div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.dropdown-menu div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.dropdown-menu li:not(.notification-read):hover {
    background-color: #f8f9fa !important;
}
.dropdown-menu li.notification-read:hover {
    background-color: #e9ecef !important;
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
                        $user = Auth::user();
                        $profileData = $user;
                        $ncount = $user->unreadNotifications()->count();
                    @endphp

                    {{-- Tài khoản --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <img alt="Avatar người dùng"
                                src="{{ $profileData->photo ? url($profileData->photo) : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png') }}"
                                class="nav-osahan-pic rounded-pill me-2"
                                style="width:32px; height:32px; object-fit:cover;">
                            
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="icofont-food-cart"></i> Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.logout') }}"><i class="icofont-sale-discount"></i> Đăng Xuất</a></li>
                        </ul>
                    </li>

                    {{-- Thông báo --}}
                    @php
                        $user = Auth::user();
                        $ncount = $user->unreadNotifications()->count();
                    @endphp

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell" style="font-size: 16px;"></i>

                            @if($ncount > 0)
                                <span id="notification-count" class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 0.65rem;">
                                    {{$ncount}}
                                </span>
                            @endif
                        </a>
                        
                        <ul id="notification-list" class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 shadow-lg border-0 rounded-3 mt-2"
                            aria-labelledby="notificationDropdown"
                            style="width: 290px; max-height: 480px; overflow: hidden; font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 0.85rem;">

                            <li class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-3">
                                <h6 class="mb-0 text-dark" style="font-size: 1rem;">Thông báo</h6>
                                <a href="#" class="small text-primary text-decoration-underline fw-semibold" style="font-size: 0.75rem;">Chưa đọc ({{ $ncount }})</a>
                            </li>

                            <div style="max-height: 410px; overflow-y: auto;">
                                @forelse ($user->notifications()->latest()->limit(10)->get() as $notification)
                                    <li class="py-2 px-3 {{ $notification->read_at ? 'notification-read' : 'notification-unread' }}"
                                        style="border-bottom: 1px solid #eee; transition: background-color 0.2s ease;
                                        {{ $notification->read_at ? 'background-color: #f0f2f5; color: #6c757d;' : 'font-weight: bold;' }}">
                                        <a href="{{ route('user.order.list') }}"
                                        class="text-decoration-none text-dark d-flex align-items-center gap-2"
                                        onclick="markNotificationRead('{{ $notification->id }}')">

                                            <div class="flex-shrink-0">
                                                <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px; line-height: 36px; font-size: 1rem;">
                                                    <i class="fa fa-bell"></i>
                                                </span>
                                            </div>

                                            <div class="flex-grow-1" style="line-height: 1.3;">
                                                <p class="mb-1 fw-medium" style="font-size: 0.8rem; white-space: normal; word-wrap: break-word; {{ $notification->read_at ? 'color: #6c757d;' : 'color: #212529;' }}">
                                                    {{ $notification->data['message'] ?? 'Bạn có thông báo mới' }}
                                                </p>
                                                <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                                    <i class="fa fa-clock-o"></i>
                                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                </small>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="py-3 px-3 text-center text-muted" style="font-size: 0.8rem;">
                                        <p class="mb-0">Không có thông báo nào.</p>
                                    </li>
                                @endforelse
                            </div>
                        </ul>
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
<div id="chooseMarketModal" class="modal fade" tabindex="-1" aria-labelledby="chooseMarketLabel" data-bs-scroll="true">
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
                    <div class="alert alert-info small" role="alert">
                       <i class="bi bi-info-circle-fill me-2"></i>
                       Xin lưu ý: Khi thay đổi chi nhánh cửa hàng, giá bán của sản phẩm có thể có sự chênh lệch do chính sách giá riêng biệt tại mỗi địa điểm.
                   </div>
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


      // Notification
    function markNotificationRead(notificationId){
        fetch('/user-mark-notification-as-read/'+notificationId,{
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('notification-count').textContent = data.count;
            const notificationElement = document.querySelector(`[onclick="markNotificationRead('${notificationId}')"]`);
            if (notificationElement) {
                notificationElement.classList.remove('notification-unread');
                notificationElement.classList.add('notification-read');
            }
        })
        .catch(error => {
            console.log('Error', error);
        });
    }
    function fetchNotifications() {
        $.ajax({
            url: '/user/notifications',
            method: 'GET',
            success: function(data) {
                if (data.count > 0) {
                    $('#notification-count').text(data.count).show();
                } else {
                    $('#notification-count').hide();
                }

                let html = `
                    <li class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-3">
                        <h6 class="mb-0 text-dark" style="font-size: 1rem;">Thông báo</h6>
                        <a href="#" class="small text-primary text-decoration-underline fw-semibold" style="font-size: 0.75rem;">Chưa đọc (${data.count})</a>
                    </li>
                    <div style="max-height: 410px; overflow-y: auto;">`;

                if (data.notifications.length === 0) {
                    html += `
                        <li class="py-3 px-3 text-center text-muted" style="font-size: 0.8rem;">
                            <p class="mb-0">Không có thông báo nào.</p>
                        </li>`;
                } else {
                    data.notifications.forEach(function (item) {
                        const isRead = !!item.read_at;
                        html += `
                            <li class="py-2 px-3 ${isRead ? 'notification-read' : 'notification-unread'}"
                                style="border-bottom: 1px solid #eee; ${isRead ? 'background-color: #f0f2f5; color: #6c757d;' : 'font-weight: bold;'}">
                                <a href="{{ route('user.order.list') }}"
                                    class="text-decoration-none text-dark d-flex align-items-center gap-2"
                                    onclick="markNotificationRead('${item.id}')">
                                    <div class="flex-shrink-0">
                                        <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px; line-height: 36px; font-size: 1rem;">
                                            <i class="fa fa-bell"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1" style="line-height: 1.3;">
                                        <p class="mb-1 fw-medium" style="font-size: 0.8rem; ${isRead ? 'color: #6c757d;' : 'color: #212529;'}">
                                            ${item.message}
                                        </p>
                                        <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                            <i class="fa fa-clock-o"></i>
                                            <span>${item.created_at}</span>
                                        </small>
                                    </div>
                                </a>
                            </li>`;
                    });
                }

                html += `</div>`;
                $('#notification-list').html(html);
            }
        });
    }
    // setInterval(fetchNotifications, 5000);
    fetchNotifications();
</script>

