<style>
   .homepage-search-form label {
      font-size: 0.875rem;
      font-weight: 500;
      margin-bottom: 0.3rem;
   }

   .homepage-search-form select,
   .homepage-search-form input {
      background: rgba(255, 255, 255, 0.9);
      border: none;
   }

   .homepage-search-form button {
      height: 48px;
   }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="pt-5 pb-5 homepage-search-block position-relative">
   <div class="banner-overlay"></div>
   <div class="container">
      <div class="row d-flex align-items-center py-lg-4">
         <div class="col-lg-12 mx-auto">
            <div class="homepage-search-title text-center">
               <h1 class="mb-2 display-4 text-shadow text-white font-weight-normal">
                  <span class="font-weight-bold">GreenFood - Hệ thống thực phẩm sạch hàng đầu Việt Nam</span>
               </h1>
               <h5 class="mb-5 text-shadow text-white-50 font-weight-normal">
                  Danh sách các siêu thị, cửa hàng nổi bật được cập nhật theo xu hướng
               </h5>
            </div>

            <div class="homepage-search-form">
               <h5 class="text-shadow text-white font-weight-normal text-center">
                  Hãy cho chúng tôi biết chi nhánh bạn muốn đến
               </h5>
               <form id="branchSearchForm" method="GET" class="form-noborder">
                  @csrf
                  <div class="form-row">
                     <!-- Tỉnh/Thành phố -->
                     <div class="col-lg-3 col-md-6 col-sm-12 form-group">
                        <label for="provinceSelect" class="text-white">Tỉnh/Thành phố</label>
                        <select class="custom-select form-control-lg" name="province_code" id="provinceSelect"
                              onchange="onProvinceChange(this)">
                           <option value="">-- Chọn tỉnh/thành phố --</option>
                           @foreach ($cities as $city)
                              <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                           @endforeach
                        </select>
                     </div>

                     <!-- Khu vực -->
                     <div class="col-lg-3 col-md-6 col-sm-12 form-group">
                        <label for="areaSelect" class="text-white">Quận/Huyện</label>
                        <select class="custom-select form-control-lg" name="area_code" id="areaSelect"
                              onchange="onAreaChange(this)">
                           <option value="">-- Chọn quận/huyện --</option>
                        </select>
                     </div>

                     <!-- Địa bàn -->
                     <div class="col-lg-3 col-md-6 col-sm-12 form-group">
                        <label for="localitySelect" class="text-white">Phường/Xã</label>
                        <select class="custom-select form-control-lg" name="locality_code" id="localitySelect"
                              onchange="onLocalityChange(this)">
                           <option value="">-- Chọn phường/xã --</option>
                        </select>
                     </div>

                     <!-- Chi nhánh -->
                     <div class="col-lg-2 col-md-6 col-sm-12 form-group">
                        <label for="branchSelect" class="text-white">Chi nhánh</label>
                        <select class="custom-select form-control-lg" name="branch_code" id="branchSelect">
                           <option value="">-- Chọn chi nhánh --</option>
                        </select>
                     </div>

                     <!-- Nút Tìm kiếm -->
                     <div class="col-lg-1 col-md-12 col-sm-12 form-group d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block btn-lg btn-gradient w-100">Tìm</button>
                     </div>
                  </div>
               </form>

            </div>

            <h6 class="mt-4 text-shadow text-white font-weight-normal">
               Danh mục sản phẩm phổ biến: THỊT, CÁ, TRỨNG, TRÁI CÂY TƯƠI, BIA, NƯỚC GIẢI KHÁT,...
            </h6>

            <div class="owl-carousel owl-carousel-category owl-theme">
               @php
                  $topClientId = App\Models\ProductNew::select('client_id', DB::raw('COUNT(*) as total'))
                        ->groupBy('client_id')
                        ->orderByDesc('total')
                        ->value('client_id');

                  $products = App\Models\ProductNew::where('client_id', $topClientId)
                     ->with('productTemplate')
                     ->latest()
                     ->limit(10)
                     ->get()
                     ->pluck('productTemplate')
                     ->filter(); 
               @endphp           
               @foreach ($products as $product) 
                  <div class="item">
                     <div class="osahan-category-item">
                        <a href="#">
                           <img class="img-fluid" src="{{ asset($product->image) }}" alt="">
                           <h6>{{ Str::limit($product->name, 12) }}</h6>
                        </a>
                     </div>
                  </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>
</div>

<script>
    async function onProvinceChange(selectElement) {
        const provinceId = selectElement.value;
        const area = document.getElementById('areaSelect');
        const locality = document.getElementById('localitySelect');
        const branch = document.getElementById('branchSelect');

        area.innerHTML = '<option>Đang tải...</option>';
        locality.innerHTML = '<option>-- Chọn Phường/Xã --</option>';
        branch.innerHTML = '<option>-- Chọn chi nhánh --</option>';

        if (provinceId) {
            const res = await fetch(`/get-districts/${provinceId}`);
            const data = await res.json();
            area.innerHTML = '<option>-- Chọn Quận/Huyện --</option>';
            data.forEach(item => {
                area.innerHTML += `<option value="${item.id}">${item.district_name}</option>`;
            });
        }
    }

    async function onAreaChange(selectElement) {
        const areaId = selectElement.value;
        const locality = document.getElementById('localitySelect');
        const branch = document.getElementById('branchSelect');

        locality.innerHTML = '<option>Đang tải...</option>';
        branch.innerHTML = '<option>-- Chọn chi nhánh --</option>';

        if (areaId) {
            const res = await fetch(`/get-wards/${areaId}`);
            const data = await res.json();
            locality.innerHTML = '<option>-- Chọn Phường/Xã --</option>';
            data.forEach(item => {
                locality.innerHTML += `<option value="${item.id}">${item.ward_name}</option>`;
            });
        }
    }

    async function onLocalityChange(selectElement) {
        const localityId = selectElement.value;
        const branch = document.getElementById('branchSelect');

        branch.innerHTML = '<option>Đang tải...</option>';

        if (localityId) {
            const res = await fetch(`/get-markets-by-ward/${localityId}`);
            const data = await res.json();
            branch.innerHTML = '<option>-- Chọn chi nhánh --</option>';
            data.forEach(item => {
                branch.innerHTML += `<option value="${item.id}">${item.name}</option>`;
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('branchSearchForm').onsubmit = function (e) {
            e.preventDefault();
            const branch = document.getElementById('branchSelect');
            const branchId = branch.value;
            if (!branchId) {
                alert('Vui lòng chọn chi nhánh');
                return;
            }
            window.location.href = `/market/details/${branchId}`;
        };
    });
</script>
