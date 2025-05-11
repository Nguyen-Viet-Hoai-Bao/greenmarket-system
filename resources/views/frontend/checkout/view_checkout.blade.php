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
    <h4 class="mb-1">Chọn địa chỉ giao hàng</h4>
    <h6 class="mb-3 text-black-50">Nhiều địa chỉ tại vị trí này</h6>
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
                      <p class="mb-0 text-black font-weight-bold"><a class="btn btn-sm btn-success mr-2" href="#"> DELIVER HERE</a> 
                        <span>30 PHÚT</span>
                      </p>
                  </div>
                </div>
            </div>
          </div>
      </div>
      <div class="col-md-6">
          <div class="bg-white card addresses-item mb-4">
            <div class="gold-members p-4">
                <div class="media">
                  <div class="mr-3"><i class="icofont-briefcase icofont-3x"></i></div>
                  <div class="media-body">
                      <h6 class="mb-1 text-secondary">Nơi làm việc</h6>
                      <p>Đà Nẵng
                      </p>
                      <p class="mb-0 text-black font-weight-bold"><a class="btn btn-sm btn-secondary mr-2" href="#"> DELIVER HERE</a> 
                        <span>40 PHÚT</span>
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
                 <h6 class="mb-3 text-black-50">Thẻ Tín Dụng/Ghi Nợ</h6>
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

  <div class="tab-pane fade show active" id="v-pills-cash" role="tabpanel" aria-labelledby="v-pills-cash-tab">
    <h6 class="mb-3 mt-0">Tiền mặt</h6>
    <p>Vui lòng chuẩn bị tiền lẻ để giúp chúng tôi phục vụ bạn tốt hơn</p>
    <hr>
    <form action="{{ route('cash_order') }}" method="POST">
      @csrf
      <input type="hidden" name="name" id="" value="{{ Auth::user()->name }}">
      <input type="hidden" name="email" id="" value="{{ Auth::user()->email }}">
      <input type="hidden" name="phone" id="" value="{{ Auth::user()->phone }}">
      <input type="hidden" name="address" id="" value="{{ Auth::user()->address }}">

      <button type="submit" class="btn btn-success btn-block btn-lg">THANH TOÁN
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

  @php
    $total_1 = 0; // Khởi tạo tổng số tiền
    // Kiểm tra nếu giỏ hàng có tồn tại trong session
    if (session('cart')) {
        // Duyệt qua từng sản phẩm trong giỏ hàng
        foreach (session('cart') as $id => $details) {
            // Tính tổng giá trị của giỏ hàng
            $total_1 += (float) $details['price'] * (int) $details['quantity'];
        }
    }
  @endphp

  <div class="tab-pane fade" id="v-pills-vnpay" role="tabpanel" aria-labelledby="v-pills-vnpay-tab">
    <h6 class="mb-3 mt-0">Thanh toán VNPay</h6>
    <p>Vui lòng nhập số tiền bạn muốn thanh toán bằng VNPay:</p>
    <form method="POST" action="{{ url('/create-payment') }}">
        @csrf
        <input type="hidden" name="name" id="" value="{{ Auth::user()->name }}">
        <input type="hidden" name="email" id="" value="{{ Auth::user()->email }}">
        <input type="hidden" name="phone" id="" value="{{ Auth::user()->phone }}">
        <input type="hidden" name="address" id="" value="{{ Auth::user()->address }}">  
        <div class="form-group">
            <label for="amount">Số tiền (VNĐ)</label>
            <input type="number" name="amount" class="form-control" value="{{ $total_1 }}" placeholder="Nhập số tiền">
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
                                ? url('upload/user_images/'.$profileData->photo)
                                : url('upload/no_image.jpg')}}
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

  

              <a href="thanks.html" class="btn btn-success btn-block btn-lg">
                THANH TOÁN  @if (Session::has('coupon'))
                      {{ number_format(Session()->get('coupon')['discount_amount'], 0, ',', '.') }} VNĐ
                    @else
                      {{ number_format($total, 0, ',', '.') }} VNĐ
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


@endsection