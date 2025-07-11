@extends('frontend.dashboard.dashboard')
 @section('dashboard')
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
 
 <section class="breadcrumb-osahan pt-5 pb-5 bg-dark position-relative text-center">
     <h1 class="text-white">Các Ưu Đãi Gần Bạn</h1>
     <h6 class="text-white-50">Các giao dịch tốt nhất tại các cửa hàng yêu thích của bạn</h6>
  </section>
  <section class="section pt-5 pb-5 products-listing">
     <div class="container">
        <div class="row d-none-m">
           <div class="col-md-12">
              <div class="dropdown float-right">
                 <a class="btn btn-outline-info dropdown-toggle btn-sm border-white-btn" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Sắp xếp theo: <span class="text-theme">Khoảng cách</span> &nbsp;&nbsp;
                 </a>
                 <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 ">
                    <a class="dropdown-item" href="#">Khoảng cách</a>
                    <a class="dropdown-item" href="#">Số Lượng Ưu Đãi</a>
                    <a class="dropdown-item" href="#">Đánh Giá</a>
                 </div>
              </div>
              <h4 class="font-weight-bold mt-0 mb-3">ƯU ĐÃI <small class="h6 mb-0 ml-2">299 cửa hàng
                 </small>
              </h4>
           </div>
        </div>
        <div class="row">
           <div class="col-md-3">
              <div class="filters shadow-sm rounded bg-white mb-4">
                 <div class="filters-header border-bottom pl-4 pr-4 pt-3 pb-3">
                    <h5 class="m-0">Lọc Theo</h5>
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
                  Danh Mục <i class="icofont-arrow-down float-right"></i>
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

@php
   $menus = App\Models\Menu::orderBy('menu_name','desc')->limit(10)->get();
@endphp             
<div class="filters-body">
   <div id="accordion">
      <div class="filters-card border-bottom p-4">
         <div class="filters-card-header" id="headingOnemenu">
            <h6 class="mb-0">
               <a href="#" class="btn-link" data-toggle="collapse" data-target="#collapseOnemenu" aria-expanded="true" aria-controls="collapseOnemenu">
                  Thực Đơn <i class="icofont-arrow-down float-right"></i>
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
   <div class="col-md-4 col-sm-6 mb-4 pb-2">
      <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
         <div class="list-card-image">
               <div class="star position-absolute"><span class="badge badge-success"><i class="icofont-star"></i> 3.1 (300+)</span></div>
               <div class="favourite-heart text-danger position-absolute"><a href="{{ route('market.details',$product->client_id) }}"><i class="icofont-heart"></i></a></div>
               <div class="member-plan position-absolute"><span class="badge badge-dark">Quảng Cáo</span></div>
               <a href="{{ route('market.details',$product->client_id) }}">
               <img src="{{ asset($product->productTemplate->image) }}" class="img-fluid item-img">
               </a>
         </div>
         <div class="p-3 position-relative">
               <div class="list-card-body">
               <h6 class="mb-1"><a href="{{ route('market.details',$product->client_id) }}" class="text-black"> {{ $product->productTemplate->name}}</a></h6>
               <p class="text-gray mb-3">Ẩm thực Bắc Ấn • Mỹ • Thuần chay</p>
               <p class="text-gray mb-3 time">
                  <span class="bg-light text-dark rounded-sm pl-2 pb-1 pt-1 pr-2">
                     <i class="icofont-wall-clock"></i> 
                     20–25 phút\
                  </span> 
                  <span class="float-right text-black-50"> 
                     {{ $product->price }}
                  </span>
               </p>
               </div>
               <div class="list-card-badge">
               <span class="badge badge-success">ƯU ĐÃI</span> <small>Giảm 65% | Dùng mã OSAHAN50</small>
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
         $('#product-list').html(response)
      }
   });

   });
})
</script> 

@endsection