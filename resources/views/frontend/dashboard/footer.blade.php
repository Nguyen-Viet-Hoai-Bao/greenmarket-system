
<section class="section pt-5 pb-5 text-center bg-white">
   <div class="container">
      <div class="row">
         <div class="col-sm-12">
            <h5 class="m-0">Operate food store or restaurants? <a href="{{ route('client.register') }}">Work With Us</a></h5>
         </div>
      </div>
   </div>
 </section>
 
 <section class="footer pt-5 pb-5 bg-light text-center">
    <div class="container">
      <div class="row justify-content-around">
  
        <div class="col-md-3 col-sm-6 mb-4 px-4">
          <h6 class="mb-3">Về GreenFood</h6>
          <ul class="list-unstyled">
            <li><a href="#">Giới thiệu về GreenFood</a></li>
            <li><a href="#">Danh sách cửa hàng</a></li>
            <li><a href="#">Quản lý chất lượng</a></li>
            <li><a href="#">Chính sách bảo mật</a></li>
            <li><a href="#">Điều khoản & Điều kiện giao dịch</a></li>
          </ul>
        </div>
  
        <div class="col-md-3 col-sm-6 mb-4 px-4">
          <h6 class="mb-3">Hỗ Trợ Khách Hàng</h6>
          <ul class="list-unstyled">
            <li><a href="#">Trung tâm hỗ trợ</a></li>
            <li><a href="#">Chính sách giao hàng</a></li>
            <li><a href="#">Chính sách thanh toán</a></li>
            <li><a href="#">Chính sách đổi trả</a></li>
            <li><a href="#">Đánh giá & góp ý</a></li>
          </ul>
        </div>
  
        <div class="col-md-3 col-sm-6 mb-4 px-4">
          <h6 class="mb-3">Chăm Sóc Khách Hàng</h6>
          <ul class="list-unstyled">
            <li><strong>Mua Online:</strong> 0786 xxx244</li>
            <li><strong>Email:</strong> nguyenviethoaibao@gmail.com</li>
          </ul>
        </div>
  
      </div>
  
      <div class="row mt-4">
        <div class="col-12">
          <p class="small text-muted">
            GreenFood - Công Ty Cổ Phần Dịch Vụ Thương Mại GreenFood<br>
            Mã số doanh nghiệp: xxxxxxxxxx - Đăng ký lần đầu ngày 21/09/2021,<br>
            đăng ký thay đổi lần thứ 4, ngày 30/06/2023
          </p>
        </div>
      </div>
    </div>
  </section>
  
  
 <section class="footer-bottom-search pt-5 pb-5 bg-white">
    <div class="container">
       <div class="row">
          <div class="col-xl-12">
             <p class="text-black">POPULAR MENUS</p>
             <div class="search-links">
                @foreach ($menus_footer as $menu)
                  <a href="#">{{ $menu->menu_name }}</a>@if (!$loop->last) | @endif
                @endforeach
             </div>
  
             <p class="mt-4 text-black">POPULAR PRODUCTS</p>
             <div class="search-links">
                @foreach ($products_list as $product)
                  <a href="#">{{ $product->productTemplate->name ?? 'Unnamed Product' }}</a>@if (!$loop->last) | @endif
                @endforeach
             </div>
          </div>
       </div>
    </div>
 </section>
  