@extends('frontend.dashboard.dashboard')
@section('dashboard')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <section class="breadcrumb-osahan pt-5 pb-5 bg-dark position-relative text-center">
        <h1 class="text-white">Ưu Đãi Gần Bạn</h1>
        <h6 class="text-white-50">Những ưu đãi tốt nhất tại các cửa hàng yêu thích của bạn</h6>
    </section>

    <section class="section pt-5 pb-5 products-listing">
        <div class="container">
            <div class="row d-none-m">
                <div class="col-md-12">
                    <div class="dropdown float-right">
                        <a class="btn btn-outline-info dropdown-toggle btn-sm border-white-btn" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Sắp xếp theo: <span class="text-theme">Khoảng cách</span> &nbsp;&nbsp;
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                            <a class="dropdown-item" href="#">Khoảng cách</a>
                            <a class="dropdown-item" href="#">Số Lượng Ưu Đãi</a>
                            <a class="dropdown-item" href="#">Đánh Giá</a>
                        </div>
                    </div>
                    <h4 class="font-weight-bold mt-0 mb-3">ƯU ĐÃI
                        <small class="h6 mb-0 ml-2">299 cửa hàng</small>
                    </h4>
                </div>
            </div>

            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3">
                     <div class="filters-body">
                        <div id="accordion">
                           <div class="filters-card-body card-shop-filters">
                              @foreach ($menusWithCategories as $index => $menu)
                                 @php
                                    $totalProductsInMenu = 0;
                                    foreach ($menu->categories as $category) {
                                          $totalProductsInMenu += $products->filter(function ($product) use ($category) {
                                             return $product->productTemplate->category_id == $category->id;
                                          })->count();
                                    }
                                 @endphp
                                 <div class="card mb-2 border-0">
                                    <div id="headingMenu{{ $index }}">
                                          <h6 class="mb-0">
                                             <a class="btn btn-link text-left w-100 d-flex align-items-center justify-content-between"
                                                data-toggle="collapse"
                                                href="#collapseMenu{{ $index }}"
                                                aria-expanded="false"
                                                aria-controls="collapseMenu{{ $index }}">
                                                <span>
                                                      <img src="{{ asset($menu->image ?? 'default.jpg') }}"
                                                         alt="{{ $menu->menu_name }}" width="20" class="mr-2">
                                                      {{ $menu->menu_name }}
                                                      <small class="text-black-50">({{ $totalProductsInMenu }})</small>
                                                </span>
                                                <i class="icofont-arrow-down"></i>
                                             </a>
                                          </h6>
                                    </div>

                                    <div id="collapseMenu{{ $index }}" class="collapse"
                                          aria-labelledby="headingMenu{{ $index }}"
                                          data-parent="#accordion">
                                          <div class="card-body py-2 px-3">
                                             @foreach ($menu->categories as $category)
                                                @php
                                                      $categoryProductCount = $products->filter(function ($product) use ($category) {
                                                         return $product->productTemplate->category_id == $category->id;
                                                      })->count();
                                                @endphp
                                                <div class="custom-control custom-checkbox ml-3">
                                                      <input type="checkbox"
                                                            class="custom-control-input filter-checkbox"
                                                            id="category-{{ $category->id }}"
                                                            data-type="category"
                                                            data-id="{{ $category->id }}">
                                                      <label class="custom-control-label"
                                                            for="category-{{ $category->id }}">
                                                         {{ $category->category_name }}
                                                         <small class="text-black-50">({{ $categoryProductCount }})</small>
                                                      </label>
                                                </div>
                                             @endforeach
                                          </div>
                                    </div>
                                 </div>
                              @endforeach
                           </div>
                        </div>
                     </div>
                </div>

                <!-- Product Listing -->
                <div class="col-md-9">
                    <div class="row" id="product-list">
                        @foreach ($products as $product)
                            <div class="col-md-3 col-sm-6 mb-4 pb-2">
                                <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm d-flex flex-column">
                                    <div class="list-card-image">
                                        <div class="star position-absolute">
                                            @if ($product->best_seller == 1)
                                                <span class="badge badge-success"><i class="icofont-star"></i></span>
                                            @endif
                                        </div>
                                        <div class="favourite-heart text-danger position-absolute">
                                            @if ($product->most_popular == 1)
                                                <a href="{{ route('product.detail', $product->id) }}"><i class="icofont-heart"></i></a>
                                            @endif
                                        </div>
                                        <a href="{{ route('product.detail', $product->id) }}">
                                            <img src="{{ asset($product->productTemplate->image) }}"
                                                 class="img-fluid item-img">
                                        </a>
                                    </div>

                                    <div class="p-3 d-flex flex-column h-100">
                                        <div class="list-card-body mb-2">
                                            <h6 class="mb-2 font-weight-bold">
                                                <a href="{{ route('product.detail', $product->id) }}" class="text-dark">
                                                    {{ $product->productTemplate->name }}
                                                </a>
                                            </h6>
                                            <p class="text-danger mb-2 d-flex justify-content-between align-items-center small">
                                                <span class="bg-light rounded px-2 py-1">
                                                    {{ number_format($product->discount_price, 0, ',', '.') }} VNĐ
                                                </span>
                                                <span class="text-muted font-weight-bold">
                                                    <i class="icofont-wall-clock"></i> 20–25 m
                                                </span>
                                            </p>
                                        </div>

                                        @php
                                            $discount = $product->price - $product->discount_price;
                                            $percent = round(($discount / $product->price) * 100);
                                        @endphp

                                        <div class="list-card-badge mb-2 text-center">
                                            <span class="badge badge-success">GIẢM GIÁ</span>
                                            <small class="text-success ml-1 font-weight-bold">{{ $percent }}% OFF</small>
                                        </div>

                                        @php
                                            $cart = session('cart', []);
                                            $cartItem = $cart[$product->id] ?? null;
                                        @endphp

                                        <div class="cart-actions mt-auto">
                                            @if ($cartItem)
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                                        data-id="{{ $product->id }}"
                                                        data-qty="{{ $cartItem['quantity'] - 1 }}">
                                                        <i class="icofont-minus"></i>
                                                    </button>

                                                    <span class="btn btn-sm btn-light mx-2 font-weight-bold"
                                                        id="qty-display-{{ $product->id }}">
                                                        {{ $cartItem['quantity'] }}
                                                    </span>

                                                    <button class="btn btn-sm btn-outline-primary mx-2 btn-change-qty"
                                                        data-id="{{ $product->id }}"
                                                        data-qty="{{ $cartItem['quantity'] + 1 }}">
                                                        <i class="icofont-plus"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <form action="{{ route('add_to_cart', $product->id) }}" method="GET"
                                                    class="w-100">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                                        <i class="icofont-cart"></i> Thêm vào giỏ hàng
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> <!-- End of product-list -->
                </div> <!-- End of col-md-9 -->
            </div> <!-- End of row -->
        </div> <!-- End of container -->
    </section>

    
<script>
    $(document).ready(function() {
        function getSelectedFilters() {
            var selectedMenus = JSON.parse(localStorage.getItem('selectedMenus')) || [];
            var selectedCategories = JSON.parse(localStorage.getItem('selectedCategories')) || [];
            return {
                menus: selectedMenus,
                categories: selectedCategories
            };
        }
        
        // Chọn lại các checkbox đã lưu khi reload trang
        function loadSelectedFilters() {
            var filters = getSelectedFilters();

            filters.menus.forEach(function(menuId) {
                $('#menu-' + menuId).prop('checked', true);
            });

            filters.categories.forEach(function(categoryId) {
                $('#category-' + categoryId).prop('checked', true);

                // Mở các menu chứa category được chọn
                const $categoryCheckbox = $('#category-' + categoryId);
                const $collapse = $categoryCheckbox.closest('.collapse');
                $collapse.addClass('show');

                const collapseId = $collapse.attr('id');
                const $button = $('a[href="#' + collapseId + '"]');
                $button.attr('aria-expanded', 'true');
            });
        }

        // Xử lý khi người dùng thay đổi checkbox
        $('.filter-checkbox').on('change', function() {
            var selectedMenus = [];
            var selectedCategories = [];

            $('.filter-checkbox:checked').each(function() {
                var type = $(this).data('type');
                var id = $(this).data('id');

                if (type === 'menu') {
                    selectedMenus.push(id);
                } else if (type === 'category') {
                    selectedCategories.push(id);
                }
            });

            // Cập nhật localStorage
            localStorage.setItem('selectedMenus', JSON.stringify(selectedMenus));
            localStorage.setItem('selectedCategories', JSON.stringify(selectedCategories));

        });

        // Khi trang tải xong, gọi hàm load
        loadSelectedFilters();
    });
</script>


<script>
$(document).ready(function(){
   $('.filter-checkbox').on('change',function(){
      var filters = {
         categories: [],
         cities: [],
         menus: []
      };
   $('.filter-checkbox:checked').each(function(){
      var type = $(this).data('type');
      var id = $(this).data('id');

      if (!filters[type + 's']) {
         filters[type + 's'] = [];
      }
      filters[type + 's'].push(id);
   });

   $.ajax({
      url: '{{ route('filter.products') }}',
      method: 'GET',
      data: filters,
      success: function(response){
         $('#product-list').html(response);
         $('#product-list').on('click', '.btn-change-qty', function(){
            const btn = $(this);
            const id = btn.data('id');
            let quantity = parseInt(btn.data('qty'));

            if (quantity < 1) quantity = 0;

            // Gửi request AJAX cập nhật số lượng
            fetch("{{ route('cart.updateQuantity') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": '{{ csrf_token() }}',
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id: id,
                    quantity: quantity
                })
            })
            .then(res => res.json())
            .then(data => {
                window.location.reload();
            })
            .catch(err => console.error('Lỗi cập nhật giỏ hàng:', err));
        });
      }
   });

   });
})
</script> 
<script>
   document.addEventListener('DOMContentLoaded', function () {
       // Đảm bảo rằng sự kiện click được gắn vào các nút khi DOM tải xong
       document.querySelectorAll('.btn-change-qty').forEach(btn => {
           btn.addEventListener('click', function () {
               const id = this.getAttribute('data-id');
               let quantity = parseInt(this.getAttribute('data-qty'));

               if (quantity < 1) quantity = 0;

               fetch("{{ route('cart.updateQuantity') }}", {
                   method: "POST",
                   headers: {
                       "X-CSRF-TOKEN": '{{ csrf_token() }}',
                       "Content-Type": "application/json"
                   },
                   body: JSON.stringify({
                       id: id,
                       quantity: quantity
                   })
               })
               .then(res => res.json())
               .then(data => {
                  window.location.reload(); // Reload lại trang sau khi cập nhật giỏ hàng
               })
               .catch(err => console.error('Lỗi cập nhật giỏ hàng:', err));
           });
       });
   });

   // Nếu nút được thêm vào động (sau khi AJAX), gắn lại sự kiện cho các nút mới
   document.addEventListener('click', function(event) {
      if(event.target.matches('.btn-change-qty')) {
         const btn = event.target;
         const id = btn.getAttribute('data-id');
         let quantity = parseInt(btn.getAttribute('data-qty'));

         if (quantity < 1) quantity = 0;

         fetch("{{ route('cart.updateQuantity') }}", {
             method: "POST",
             headers: {
                 "X-CSRF-TOKEN": '{{ csrf_token() }}',
                 "Content-Type": "application/json"
             },
             body: JSON.stringify({
                 id: id,
                 quantity: quantity
             })
         })
         .then(res => res.json())
         .then(data => {
            window.location.reload(); // Reload lại trang sau khi cập nhật giỏ hàng
         })
         .catch(err => console.error('Lỗi cập nhật giỏ hàng:', err));
      }
   });
</script>


@endsection