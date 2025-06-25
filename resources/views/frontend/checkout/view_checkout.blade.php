@extends('frontend.dashboard.dashboard')
@section('dashboard')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>



<section class="offer-dedicated-body mt-4 mb-4 pt-2 pb-2">
  <div class="container">
     <div class="row">
        <div class="col-md-8">
           <div class="offer-dedicated-body-left">

          @php
            $id = Auth::user()->id;
            $profileData = App\Models\User::find($id);
            
          @endphp


  <div class="pt-2"></div>
  <div class="bg-white rounded shadow-sm p-4 mb-4">
    <h4 class="mb-1">Địa chỉ giao hàng</h4>
    <div class="row">
      <div class="col-md-6">
          <div class="bg-white card addresses-item mb-4 border border-success">
            <div class="gold-members p-4">
                <div class="media">
                  <div class="mr-3"><i class="icofont-ui-home icofont-3x"></i></div>
                  <div class="media-body">
                      <h6 class="mb-1 text-black">Nhà riêng</h6>
                      <p class="text-black">{{ $profileData->address }}
                      </p>
                      <p class="mb-0 text-black font-weight-bold"><a class="btn btn-sm btn-success mr-2" href="#"> 30 PHÚT - 2 GIỜ</a> 
                      </p>
                  </div>
                </div>
            </div>
          </div>
      </div>
    </div>
  </div>


            <div class="pt-2"></div>
              <div class="bg-white rounded shadow-sm p-4 osahan-payment">
                 <h4 class="mb-1">Chọn phương thức thanh toán</h4>
                 <h6 class="mb-3 text-black-50">Thanh toán trực tuyến/Tiền mặt</h6>
                 <div class="row">
                    <div class="col-sm-4 pr-0">
                       <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

                        
                          <a class="nav-link active" id="v-pills-cash-tab" data-toggle="pill" href="#v-pills-cash" role="tab" aria-controls="v-pills-cash" aria-selected="false">
                            <i class="icofont-money"></i> 
                            Thanh toán khi giao hàng
                          </a>
                          <a class="nav-link" id="v-pills-vnpay-tab" data-toggle="pill" href="#v-pills-vnpay" role="tab" aria-controls="v-pills-vnpay" aria-selected="false">
                            <i class="icofont-bank-transfer"></i>
                            VNPay
                          </a>
                        


                       </div>
                    </div>
                    <div class="col-sm-8 pl-0">
                       <div class="tab-content h-100" id="v-pills-tabContent">

                        
  @php
    $total_1 = 0;
    if (session('cart')) {
        foreach (session('cart') as $id => $details) {
            $total_1 += (float) $details['price'] * (int) $details['quantity'];
        }
    }

    $shippingFee = Session::get('shipping_fee', 0);
    $couponApplied = false;
    $couponDiscountAmount = 0;

    if (Session::has('coupon')) {
        $couponData = Session::get('coupon');
        
        if (isset($couponData['discount_amount'])) {
            $total_1 = (float) $couponData['discount_amount'] + $shippingFee; 
            $couponApplied = true;
        }
    }

    if (!$couponApplied) {
        $total_1 += $shippingFee;
    }

    if ($total_1 < 0) {
        $total_1 = 0;
    }
  @endphp

  <div class="tab-pane fade show active" id="v-pills-cash" role="tabpanel" aria-labelledby="v-pills-cash-tab">
    <h6 class="mb-3 mt-0">Tiền mặt</h6>
    <p>Vui lòng chuẩn bị tiền lẻ để giúp chúng tôi phục vụ bạn tốt hơn</p>
    <hr>
    <form id="cash-form" action="{{ route('cash_order') }}" method="POST">
      @csrf
      {{-- <input type="hidden" name="name" id="" value="{{ Auth::user()->name }}"> --}}
      {{-- <input type="hidden" name="email" id="" value="{{ Auth::user()->email }}">
      <input type="hidden" name="phone" id="" value="{{ Auth::user()->phone }}">
      <input type="hidden" name="address" id="" value="{{ Auth::user()->address }}"> --}}
      
      <div class="row">
        <div class="form-group col-md-6">
            <label for="name">Họ và tên*</label>
            <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
        </div>
        <div class="form-group col-md-6">
            <label for="email">Email*</label>
            <input type="text" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
        </div>
        <div class="form-group col-md-6">
            <label for="phone">Số điện thoại*</label>
            <input type="text" class="form-control" name="phone" value="{{ Auth::user()->phone }}" required>
        </div>
        
        <!-- Tỉnh/Thành phố -->
        <div class="form-group col-md-6">
            <label for="provinceSelect">Tỉnh/Thành phố*</label>
            <select class="custom-select form-control" name="province_code" id="cashProvinceSelect" onchange="onCashProvinceChange(this)" required>
                <option value="">-- Chọn tỉnh/thành phố --</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Khu vực -->
        <div class="form-group col-md-6">
            <label for="areaSelect">Quận/Huyện*</label>
            <select class="custom-select form-control" name="area_code" id="cashAreaSelect" onchange="onCashAreaChange(this)" required>
                <option value="">-- Chọn quận/huyện --</option>
            </select>
        </div>

        <!-- Địa bàn -->
        <div class="form-group col-md-6">
            <label for="localitySelect">Phường/Xã*</label>
            <select class="custom-select form-control" name="locality_code" id="cashLocalitySelect" onchange="onCashLocalityChange(this)" required>
                <option value="">-- Chọn phường/xã --</option>
            </select>
        </div>

        <div class="form-group col-md-12">
            <label for="address">Số nhà, tên đường*</label>
            <input type="text" class="form-control" name="address" value="{{ Auth::user()->address }}" required>
        </div>
      </div>
      <div class="form-group">
          <label for="amount">Số tiền (VNĐ)</label>
          <input type="number" name="amount" class="form-control" value="{{ $total_1 }}" placeholder="Nhập số tiền" readonly>
      </div>
      <div class="form-group mt-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cashAgreeCheckbox">
          <label class="form-check-label" for="cashAgreeCheckbox">
            Tôi đồng ý với '<a href="{{ route('personal.data.policy') }}" target="_blank">Chính sách xử lý dữ liệu cá nhân</a>'
            {{-- Tôi đồng ý với '<a href="#" target="_blank">Chính sách xử lý dữ liệu cá nhân</a>' --}}
          </label>
        </div>
      </div>

      <button type="submit" class="btn btn-success btn-block btn-lg" disabled>THANH TOÁN
      <i class="icofont-long-arrow-right"></i></butt>
    </form>
  </div>
                    

  <div class="tab-pane fade" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
    <h6 class="mb-3 mt-0">Thêm thẻ mới</h6>
    <p>CHÚNG TÔI CHẤP NHẬN <span class="osahan-card">
      <i class="icofont-visa-alt"></i> <i class="icofont-mastercard-alt"></i> <i class="icofont-american-express-alt"></i> <i class="icofont-payoneer-alt"></i> <i class="icofont-apple-pay-alt"></i> <i class="icofont-bank-transfer-alt"></i> <i class="icofont-discover-alt"></i> <i class="icofont-jcb-alt"></i>
      </span>
    </p>
    <form>
      <div class="form-row">
          <div class="form-group col-md-12">
            <label for="inputPassword4">Số thẻ</label>
            <div class="input-group">
                <input type="number" class="form-control" placeholder="Card number">
                <div class="input-group-append">
                  <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="icofont-card"></i></button>
                </div>
            </div>
          </div>
          <div class="form-group col-md-8">
            <label>Hạn sử dụng (MM/YY)
            </label>
            <input type="number" class="form-control" placeholder="Enter Valid through(MM/YY)">
          </div>
          <div class="form-group col-md-4">
            <label>Mã CVV
            </label>
            <input type="number" class="form-control" placeholder="Enter CVV Number">
          </div>
          <div class="form-group col-md-12">
            <label>Tên trên thẻ
            </label>
            <input type="text" class="form-control" placeholder="Enter Card number">
          </div>
          <div class="form-group col-md-12">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="customCheck1">
                <label class="custom-control-label" for="customCheck1">Lưu thẻ này để thanh toán nhanh hơn lần sau.</label>
            </div>
          </div>
          <div class="form-group col-md-12 mb-0">
            <a href="thanks.html" class="btn btn-success btn-block btn-lg">THANH TOÁN 1329₫
            <i class="icofont-long-arrow-right"></i></a>
          </div>
      </div>
    </form>
  </div>


  <div class="tab-pane fade" id="v-pills-vnpay" role="tabpanel" aria-labelledby="v-pills-vnpay-tab">
    <h6 class="mb-3 mt-0">Thanh toán VNPay</h6>
    <p>Vui lòng nhập số tiền bạn muốn thanh toán bằng VNPay:</p>
    <form id="vnpay-form" method="POST" action="{{ url('/create-payment') }}">
        @csrf
        
        <div class="row">
          <div class="form-group col-md-6">
              <label for="name">Họ và tên*</label>
              <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
          </div>
          <div class="form-group col-md-6">
              <label for="email">Email*</label>
              <input type="text" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
          </div>
          <div class="form-group col-md-6">
              <label for="phone">Số điện thoại*</label>
              <input type="text" class="form-control" name="phone" value="{{ Auth::user()->phone }}" required>
          </div>
          
          <!-- Tỉnh/Thành phố -->
          <div class="form-group col-md-6">
              <label for="provinceSelect">Tỉnh/Thành phố*</label>
              <select class="custom-select form-control" name="province_code" id="provinceSelect" onchange="onProvinceChange(this)" required>
                  <option value="">-- Chọn tỉnh/thành phố --</option>
                  @foreach ($cities as $city)
                      <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                  @endforeach
              </select>
          </div>

          <!-- Khu vực -->
          <div class="form-group col-md-6">
              <label for="areaSelect">Quận/Huyện*</label>
              <select class="custom-select form-control" name="area_code" id="areaSelect" onchange="onAreaChange(this)" required>
                  <option value="">-- Chọn quận/huyện --</option>
              </select>
          </div>

          <!-- Địa bàn -->
          <div class="form-group col-md-6">
              <label for="localitySelect">Phường/Xã*</label>
              <select class="custom-select form-control" name="locality_code" id="localitySelect" onchange="onLocalityChange(this)" required>
                  <option value="">-- Chọn phường/xã --</option>
              </select>
          </div>

          <div class="form-group col-md-12">
              <label for="address">Số nhà, tên đường*</label>
              <input type="text" class="form-control" name="address" value="{{ Auth::user()->address }}" required>
          </div>
        </div>
        <div class="form-group">
            <label for="amount">Số tiền (VNĐ)</label>
            <input type="number" name="amount" class="form-control" value="{{ $total_1 }}" placeholder="Nhập số tiền" readonly>
        </div>
        <div class="form-group mt-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="vnpayAgreeCheckbox">
            <label class="form-check-label" for="vnpayAgreeCheckbox">
              Tôi đồng ý với '<a href="{{ route('personal.data.policy') }}" target="_blank">Chính sách xử lý dữ liệu cá nhân</a>'
              {{-- Tôi đồng ý với '<a href="#" target="_blank">Chính sách xử lý dữ liệu cá nhân</a>' --}}
            </label>
          </div>
        </div>
        <button type="submit" class="btn btn-success btn-block btn-lg">Thanh toán qua VNPay
            <i class="icofont-long-arrow-right"></i>
        </button>
    </form>
</div>




                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>

        

        <div class="col-md-4">
           <div class="generator-bg rounded shadow-sm mb-4 p-4 osahan-cart-item">
              <div class="d-flex mb-4 osahan-cart-item-profile">
                 <img class="img-fluid mr-3 rounded-pill" 
                      alt="osahan" 
                      src="{{ (!empty($profileData->photo)) 
                                ? url($profileData->photo)
                                : url('https://res.cloudinary.com/dth3mz6s9/image/upload/v1750781920/no_img_oznhhy.png')}}
                                ">
                 <div class="d-flex flex-column">
                    <h6 class="mb-1 text-white">
                      {{ $profileData->name }}
                    </h6>
                    <p class="mb-0 text-white">
                      <i class="icofont-location-pin"></i> 
                      {{ $profileData->address }}
                    </p>
                 </div>
              </div>

  <p class="mb-4 text-white">{{ count((array) session('cart')) }} Sản phẩm</p>
  <div class="bg-white rounded shadow-sm mb-2">
      @php $total = 0;@endphp

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
          <p class="mb-1">Tổng số mặt hàng
              <span class="float-right text-dark">
                {{ count((array) session('cart')) }}
              </span>
          </p>
          <p class="mb-1">Tên mã giảm giá 
              <span class="float-right text-dark">
                {{ (session()->get('coupon')['coupon_name']) }}
                ({{ (session()->get('coupon')['discount']) }}%)
                
                <a type="submit" onclick="CouponRemove()">
                    <i class="icofont-ui-delete float-right" style="color: red;"></i>
                </a>
              </span>
          </p>
          <p class="mb-1 text-success">Giảm giá 
              <span class="float-right text-success">
                @if (Session::has('coupon'))
                    {{ number_format($total - Session()->get('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
                @else
                    {{ number_format(0, 0, ',', '.') }} VNĐ
                @endif
              </span>
          </p>
          <hr />
          <h6 class="font-weight-bold mb-0">SỐ TIỀN PHẢI TRẢ
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
              <input type="text" class="form-control" placeholder="Nhập mã khuyến mãi" id="coupon_name">
              <div class="input-group-append">
                <button class="btn btn-primary" type="submit" id="button-addon2" onclick="ApplyCoupon()">
                    <i class="icofont-sale-discount"></i> 
                    ÁP DỤNG
                </button>
              </div>
          </div>
        </div>
        @endif
            <div class="mb-2 bg-white rounded p-2 d-flex align-items-center">
                <i class="fas fa-shipping-fast fa-2x mr-2" style="color: #28a745;"></i> <p class="mb-0 flex-grow-1 text-right">
                    Phí giao hàng: 
                    <strong class="text-dark">
                        {{ number_format(Session::get('shipping_fee', 0), 0, ',', '.') }} VNĐ
                    </strong>
                </p>
            </div>
              <a href="#" class="btn btn-success btn-block btn-lg">
                THANH TOÁN  @if (Session::has('coupon'))
                      {{ number_format(Session()->get('coupon')['discount_amount'] + Session()->get('shipping_fee'), 0, ',', '.') }} VNĐ
                    @else
                      {{ number_format($total + Session()->get('shipping_fee'), 0, ',', '.') }} VNĐ
                    @endif
              <i class="icofont-long-arrow-right"></i></a>
           </div>
            <div class="pt-2"></div>
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
    const vnpayForm = document.getElementById('vnpay-form');
    const vnpayInputs = vnpayForm.querySelectorAll('[required]');
    const vnpaySubmitButton = vnpayForm.querySelector('button[type="submit"]');

    const vnpayAgreeCheckbox = document.getElementById('vnpayAgreeCheckbox');

    function validateVnpayForm() {
        let isValid = true;
        vnpayInputs.forEach(input => {
            const value = input.value;
            if (input.tagName === 'SELECT') {
                if (!value || value === '') {
                    isValid = false;
                }
            } else {
                if (!value.trim()) {
                    isValid = false;
                }
            }
        });
        if (!vnpayAgreeCheckbox.checked) {
            isValid = false;
        }
        vnpaySubmitButton.disabled = !isValid;
    }

    // Gắn sự kiện khi người dùng thay đổi dữ liệu
    vnpayInputs.forEach(input => {
        input.addEventListener('input', validateVnpayForm);
        input.addEventListener('change', validateVnpayForm);
    });

    // Gọi hàm kiểm tra ban đầu
    validateVnpayForm();

    // Gọi validate lại sau khi dữ liệu được load động từ API
    window.onProvinceChange = async function (selectElement) {
        const provinceId = selectElement.value;
        const area = document.getElementById('areaSelect');
        const locality = document.getElementById('localitySelect');

        area.innerHTML = '<option value="">Đang tải...</option>';
        locality.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

        if (provinceId) {
            const res = await fetch(`/get-districts/${provinceId}`);
            const data = await res.json();
            area.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            data.forEach(item => {
                area.innerHTML += `<option value="${item.id}">${item.district_name}</option>`;
            });
        }
        validateVnpayForm(); // validate lại
    }

    window.onAreaChange = async function (selectElement) {
        const areaId = selectElement.value;
        const locality = document.getElementById('localitySelect');

        locality.innerHTML = '<option value="">Đang tải...</option>';

        if (areaId) {
            const res = await fetch(`/get-wards/${areaId}`);
            const data = await res.json();
            locality.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            data.forEach(item => {
                locality.innerHTML += `<option value="${item.id}">${item.ward_name}</option>`;
            });
        }
        validateVnpayForm(); // validate lại
    }

    // Trường hợp người dùng chọn lại từ đầu, cũng cần gọi lại validate
    document.getElementById('localitySelect').addEventListener('change', validateVnpayForm);
});
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const vnpayForm = document.getElementById('vnpay-form');
    const vnpayInputs = vnpayForm.querySelectorAll('[required]');
    const vnpaySubmitButton = vnpayForm.querySelector('button[type="submit"]');
    // Lấy tham chiếu đến checkbox đồng ý của form VNPay
    const vnpayAgreeCheckbox = document.getElementById('vnpayAgreeCheckbox');

    function validateVnpayForm() {
        let isValid = true;
        vnpayInputs.forEach(input => {
            const value = input.value;
            if (input.tagName === 'SELECT') {
                if (!value || value === '') {
                    isValid = false;
                }
            } else {
                if (!value.trim()) {
                    isValid = false;
                }
            }
        });
        // Thêm điều kiện kiểm tra checkbox đồng ý
        if (!vnpayAgreeCheckbox.checked) {
            isValid = false;
        }
        vnpaySubmitButton.disabled = !isValid;
    }

    // Gắn sự kiện khi người dùng thay đổi dữ liệu
    vnpayInputs.forEach(input => {
        input.addEventListener('input', validateVnpayForm);
        input.addEventListener('change', validateVnpayForm);
    });

    // Gắn sự kiện cho checkbox đồng ý
    vnpayAgreeCheckbox.addEventListener('change', validateVnpayForm);

    // Gọi hàm kiểm tra ban đầu
    validateVnpayForm();

    // Gọi validate lại sau khi dữ liệu được load động từ API
    window.onProvinceChange = async function (selectElement) {
        const provinceId = selectElement.value;
        const area = document.getElementById('areaSelect');
        const locality = document.getElementById('localitySelect');

        area.innerHTML = '<option value="">Đang tải...</option>';
        locality.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

        if (provinceId) {
            const res = await fetch(`/get-districts/${provinceId}`);
            const data = await res.json();
            area.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            data.forEach(item => {
                area.innerHTML += `<option value="${item.id}">${item.district_name}</option>`;
            });
        }
        validateVnpayForm(); // validate lại
    }

    window.onAreaChange = async function (selectElement) {
        const areaId = selectElement.value;
        const locality = document.getElementById('localitySelect');

        locality.innerHTML = '<option value="">Đang tải...</option>';

        if (areaId) {
            const res = await fetch(`/get-wards/${areaId}`);
            const data = await res.json();
            locality.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            data.forEach(item => {
                locality.innerHTML += `<option value="${item.id}">${item.ward_name}</option>`;
            });
        }
        validateVnpayForm(); // validate lại
    }

    // Trường hợp người dùng chọn lại từ đầu, cũng cần gọi lại validate
    document.getElementById('localitySelect').addEventListener('change', validateVnpayForm);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cashForm = document.getElementById('cash-form');
    const cashInputs = cashForm.querySelectorAll('[required]');
    const cashSubmitButton = cashForm.querySelector('button[type="submit"]');
    // Lấy tham chiếu đến checkbox đồng ý của form Cash
    const cashAgreeCheckbox = document.getElementById('cashAgreeCheckbox');

    function validateCashForm() {
        let isValid = true;
        cashInputs.forEach(input => {
            const value = input.value;
            if (input.tagName === 'SELECT') {
                if (!value || value === '') {
                    isValid = false;
                }
            } else {
                if (!value.trim()) {
                    isValid = false;
                }
            }
        });
        // Thêm điều kiện kiểm tra checkbox đồng ý
        if (!cashAgreeCheckbox.checked) {
            isValid = false;
        }
        cashSubmitButton.disabled = !isValid;
    }

    cashInputs.forEach(input => {
        input.addEventListener('input', validateCashForm);
        input.addEventListener('change', validateCashForm);
    });

    // Gắn sự kiện cho checkbox đồng ý
    cashAgreeCheckbox.addEventListener('change', validateCashForm);

    validateCashForm();

    window.onCashProvinceChange = async function (selectElement) {
        const provinceId = selectElement.value;
        const area = document.getElementById('cashAreaSelect');
        const locality = document.getElementById('cashLocalitySelect');

        area.innerHTML = '<option value="">Đang tải...</option>';
        locality.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

        if (provinceId) {
            const res = await fetch(`/get-districts/${provinceId}`);
            const data = await res.json();
            area.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            data.forEach(item => {
                area.innerHTML += `<option value="${item.id}">${item.district_name}</option>`;
            });
        }

        validateCashForm();
    }

    window.onCashAreaChange = async function (selectElement) {
        const areaId = selectElement.value;
        const locality = document.getElementById('cashLocalitySelect');

        locality.innerHTML = '<option value="">Đang tải...</option>';

        if (areaId) {
            const res = await fetch(`/get-wards/${areaId}`);
            const data = await res.json();
            locality.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            data.forEach(item => {
                locality.innerHTML += `<option value="${item.id}">${item.ward_name}</option>`;
            });
        }

        validateCashForm();
    }

    document.getElementById('cashLocalitySelect').addEventListener('change', validateCashForm);
});
</script>

@endsection