@php
    $id = Auth::user()->id;
    $profileData = App\Models\User::find($id);
@endphp

<div class="col-md-3">
  <div class="osahan-account-page-left shadow-sm rounded bg-white h-100">
     <div class="border-bottom p-4">
        <div class="osahan-user text-center">
           <div class="osahan-user-media">
              <img class="mb-3 rounded-pill shadow-sm mt-1" 
                    src="{{ (!empty($profileData->photo)) 
                    ? url($profileData->photo)
                    : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png')}}" 
                    alt="gurdeep singh osahan">
              <div class="osahan-user-media-body">
                 <h6 class="mb-2">{{ $profileData->name }}</h6>
                 <p class="mb-1">{{ $profileData->phone }}</p>
                 <p>{{ $profileData->email }}</p>
              </div>
           </div>
        </div>
     </div>
     <ul class="nav nav-tabs flex-column border-0 pt-4 pl-4 pb-4" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link {{ Route::currentRouteName() === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}" role="tab" aria-controls="orders" aria-selected="true"><i class="icofont-location-pin"></i> Hồ Sơ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Route::currentRouteName() === 'change.password' ? 'active' : '' }}" href="{{ route('change.password') }}" role="tab" aria-controls="orders" aria-selected="true"><i class="icofont-food-cart"></i> Đổi Mật Khẩu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Route::currentRouteName() === 'all.wishlist' ? 'active' : '' }}" href="{{ route('all.wishlist') }}" role="tab" aria-controls="orders" aria-selected="true"><i class="icofont-heart"></i> Danh Sách Yêu Thích</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Route::currentRouteName() === 'user.order.list' ? 'active' : '' }}" href="{{ route('user.order.list') }}" role="tab" aria-controls="orders" aria-selected="true"><i class="icofont-credit-card"></i> Đơn Hàng</a>
        </li>
     </ul>
  </div>
</div>
