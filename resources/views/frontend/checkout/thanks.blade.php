@extends('frontend.dashboard.dashboard')
@section('dashboard')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<section class="section pt-5 pb-5 osahan-not-found-page">
  <div class="container">
     <div class="row">
        <div class="col-md-12 text-center pt-5 pb-5">
           <img class="img-fluid mb-5" src="{{ asset('frontend/img/thanks.png') }}" alt="404">
           <h1 class="mt-2 mb-2 text-success">Chúc mừng!</h1>
           <p class="mb-5">Bạn đã đặt hàng thành công</p>
           <a class="btn btn-primary btn-lg" href="{{ url('/user/order/list') }}">Xem đơn hàng :)</a>
           <a class="btn btn-primary btn-lg" href="{{ url('/') }}">Trở về trang chủ</a>
        </div>
     </div>
  </div>
</section>






@endsection