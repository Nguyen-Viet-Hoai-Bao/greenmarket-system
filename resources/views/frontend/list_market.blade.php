@extends('frontend.dashboard.dashboard')
 @section('dashboard')
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
 <section class="breadcrumb-osahan pt-5 pb-5 bg-dark position-relative text-center">
     <h1 class="text-white">Offers Near You</h1>
     <h6 class="text-white-50">Best deals at your favourite markets</h6>
  </section>
  <section class="section pt-5 pb-5 products-listing">
     <div class="container">
        <div class="row d-none-m">
           <div class="col-md-12">
              <div class="dropdown float-right">
                 <a class="btn btn-outline-info dropdown-toggle btn-sm border-white-btn" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 Sort by: <span class="text-theme">Distance</span> &nbsp;&nbsp;
                 </a>
                 <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 ">
                    <a class="dropdown-item" href="#">Distance</a>
                    <a class="dropdown-item" href="#">No Of Offers</a>
                    <a class="dropdown-item" href="#">Rating</a>
                 </div>
              </div>
              <h4 class="font-weight-bold mt-0 mb-3">OFFERS <small class="h6 mb-0 ml-2">299 markets
                 </small>
              </h4>
           </div>
        </div>
        <div class="row">
           <div class="col-md-3">
              <div class="filters shadow-sm rounded bg-white mb-4">
                 <div class="filters-header border-bottom pl-4 pr-4 pt-3 pb-3">
                    <h5 class="m-0">Filter By</h5>
                 </div>
                 
@php
   $categories = App\Models\Category::orderBy('category_name','desc')->get();
@endphp             
<div class="filters-body">
   <div id="accordion">
      <div class="filters-card border-bottom p-4">
         <div class="filters-card-header" id="headingOne">
            <h6 class="mb-0">
               <a href="#" class="btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
               Category <i class="icofont-arrow-down float-right"></i>
               </a>
            </h6>
         </div>

      
      <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
         <div class="filters-card-body card-shop-filters">
            @foreach ($categories as $category) 
               @php
                     $categoryProductCount = $products->filter(function($product) use ($category) {
                        return $product->productTemplate->category_id == $category->id;
                     })->count();
               @endphp
               <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input filter-checkbox" id="category-{{$category->id}}" data-type="category" data-id="{{$category->id}}">
                  <label class="custom-control-label" for="category-{{$category->id}}">{{$category->category_name}} 
                     <small class="text-black-50">({{$categoryProductCount}})</small>   
                  </label>
               </div> 
            @endforeach 
         </div>
      </div>




      </div>  
   </div>
</div>



{{-- @php
   $cities = App\Models\City::orderBy('id','desc')->limit(10)->get();
@endphp             
<div class="filters-body">
   <div id="accordion">
      <div class="filters-card border-bottom p-4">
         <div class="filters-card-header" id="headingOnecity">
            <h6 class="mb-0">
               <a href="#" class="btn-link" data-toggle="collapse" data-target="#collapseOnecity" aria-expanded="true" aria-controls="collapseOnecity">
               City <i class="icofont-arrow-down float-right"></i>
               </a>
            </h6>
         </div>


      <div id="collapseOnecity" class="collapse show" aria-labelledby="headingOnecity" data-parent="#accordion">
         <div class="filters-card-body card-shop-filters">
            @foreach ($cities as $city) 
            @php
               $cityProductCount = $products->where('city_id',$city->id)->count();
            @endphp
            <div class="custom-control custom-checkbox">
               <input type="checkbox" class="custom-control-input filter-checkbox" id="city-{{$city->id}}" data-type="city" data-id="{{$city->id}}">
                <label class="custom-control-label" for="city-{{$city->id}}">{{$city->city_name}} <small class="text-black-50">({{$cityProductCount}})</small>   
               </label>
            </div> 
            @endforeach 
         </div>
      </div>


      </div>  
   </div>
</div> --}}



@php
   $menus = App\Models\Menu::orderBy('menu_name','desc')->limit(10)->get();
@endphp             
<div class="filters-body">
   <div id="accordion">
      <div class="filters-card border-bottom p-4">
         <div class="filters-card-header" id="headingOnemenu">
            <h6 class="mb-0">
               <a href="#" class="btn-link" data-toggle="collapse" data-target="#collapseOnemenu" aria-expanded="true" aria-controls="collapseOnemenu">
               Menu <i class="icofont-arrow-down float-right"></i>
               </a>
            </h6>
         </div>


         <div id="collapseOnemenu" class="collapse show" aria-labelledby="headingOnemenu" data-parent="#accordion">
            <div class="filters-card-body card-shop-filters">
               @foreach ($menus as $menu) 
               @php
                  $menuProductCount = $products->filter(function($product) use ($menu) {
                        return $product->productTemplate->menu_id == $menu->id;
                  })->count();
               @endphp
               <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input filter-checkbox" id="menu-{{$menu->id}}" data-type="menu" data-id="{{$menu->id}}">
                  <label class="custom-control-label" for="menu-{{$menu->id}}">{{$menu->menu_name}} 
                     <small class="text-black-50">({{$menuProductCount}})</small>   
                  </label>
               </div> 
               @endforeach 
            </div>
         </div>
      </div>  
   </div>
</div>


              </div>
             
           </div>
           <div class="col-md-9">
             
              <div class="row" id="product-list">
@foreach ($products as $product)
<div class="col-md-3 col-sm-6 mb-4 pb-2">
   <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm d-flex flex-column">
      <div class="list-card-image">
         <div class="star position-absolute">
            <span class="badge badge-success"><i class="icofont-star"></i></span>
         </div>
         <div class="favourite-heart text-danger position-absolute">
            <a href="{{ route('product.detail', $product->id) }}"><i class="icofont-heart"></i></a>
         </div>
         <a href="{{ route('product.detail', $product->id) }}">
            <img src="{{ asset($product->productTemplate->image) }}" class="img-fluid item-img">
         </a>
      </div>

      <!-- Nội dung sản phẩm dùng flex-grow để giãn đều -->
      <div class="p-3 d-flex flex-column h-100">
         <div class="list-card-body mb-2">
            <h6 class="mb-2 font-weight-bold">
               <a href="{{ route('product.detail', $product->id) }}" class="text-dark">
                  {{ $product->productTemplate->name }}
               </a>
            </h6>
            <p class="text-danger mb-2 d-flex justify-content-between align-items-center small">
               <span class="bg-light rounded px-2 py-1">
                  {{ number_format($product->price, 0, ',', '.') }} VNĐ
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
               <!-- Nếu sản phẩm chưa có trong giỏ -->
               <form action="{{ route('add_to_cart', $product->id) }}" method="GET" class="w-100">
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
                    
              </div>
           </div>
        </div>
     </div>
     </div>
  </section>
 


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
                  window.location.reload();
               })
               .catch(err => console.error('Lỗi cập nhật giỏ hàng:', err));
           });
       });
   });
</script>
   

@endsection