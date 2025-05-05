<section class="pt-5 pb-5 homepage-search-block position-relative">
   <div class="banner-overlay"></div>
   <div class="container">
      <div class="row d-flex align-items-center py-lg-4">
         <div class="col-lg-8 mx-auto">
            <div class="homepage-search-title text-center">
               <h1 class="mb-2 display-4 text-shadow text-white font-weight-normal">
                   <span class="font-weight-bold">GreenFood - Hệ thống thực phẩm sạch hàng đầu Việt Nam 🇻🇳</span>
               </h1>
               <h5 class="mb-5 text-shadow text-white-50 font-weight-normal">
                  Danh sách các siêu thị, cửa hàng nổi bật được cập nhật theo xu hướng
              </h5>
            </div>
 
            <div class="homepage-search-form">
               <form class="form-noborder">
                  <div class="form-row">
                     <div class="col-lg-3 col-md-3 col-sm-12 form-group">
                        @php
                           $menus = App\Models\Menu::get();
                        @endphp
                        <div class="location-dropdown">
                           <i class="icofont-location-arrow"></i>
                           <select class="custom-select form-control-lg">
                              <option value="">Chọn danh mục sản phẩm</option>
                              @foreach ($menus as $menu)
                                  <option value="{{ $menu->id }}">{{ $menu->menu_name }}</option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-lg-7 col-md-7 col-sm-12 form-group">
                        <input type="text" placeholder="Nhập địa điểm giao hàng của bạn" class="form-control form-control-lg">
                        <a class="locate-me" href="#"><i class="icofont-ui-pointer"></i> Xác định vị trí</a>
                     </div>
                     <div class="col-lg-2 col-md-2 col-sm-12 form-group">
                        <a href="listing.html" class="btn btn-primary btn-block btn-lg btn-gradient">Tìm kiếm</a>
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
 </section>
 