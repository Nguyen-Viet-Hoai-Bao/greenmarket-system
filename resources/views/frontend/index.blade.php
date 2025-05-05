@extends('frontend.master')
@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<section class="section pt-5 pb-5 products-section">
  <div class="container">
     <div class="section-header text-center">
        <h2>Popular Market</h2>
        <p>Top markets, vegetables, milks, and ...</p>
        <span class="line"></span>
     </div>
     <div class="row">

@php
    $clients = App\Models\Client::latest()->where('status', '1')->get();
@endphp

@foreach ($clients as $client)
    
   @php
       $products = App\Models\Product::where('client_id', $client->id)->limit(3)->get();
       $menuNames = $products->map(function($product){
         return $product->menu->menu_name;
       })->unique()->toArray();
       $menuNamesString = implode('. ', $menuNames);
       $coupons = App\Models\Coupon::where('client_id', $client->id)
                           ->where('status', '1')->first();
      
      $isWishlisted = App\Models\Wishlist::where('user_id', Auth::id())
                        ->where('client_id', $client->id)
                        ->first();
   @endphp

   @php
      $reviewcount = App\Models\Review::where('client_id',$client->id)->where('status',1)->latest()->get();
      $avarage = App\Models\Review::where('client_id',$client->id)->where('status',1)->avg('rating');
   @endphp

<div class="col-md-3">
   <div class="item pb-3">
      <div class="list-card bg-white h-100 rounded overflow-hidden position-relative shadow-sm">
         <div class="list-card-image">
            <div class="star position-absolute"><span class="badge badge-success"><i class="icofont-star"></i>{{ number_format($avarage,1) }} ({{ count($reviewcount ) }}+)</span></div>

            {{-- <div class="favourite-heart text-danger position-absolute">
               <a aria-label="Add to Wishlist" onclick="addWishlist({{ $client->id }})">
                  <i class="icofont-heart">
                  </i>
               </a>
            </div> --}}
            <div class="favourite-heart position-absolute">
               <a aria-label="Add to Wishlist" onclick="addWishlist({{ $client->id }})">
                   <i class="icofont-heart {{ $isWishlisted ? 'text-danger' : 'text-dark' }}" id="heart-icon-{{ $client->id }}">
                   </i>
               </a>
           </div>
            @if ($coupons)
               <div class="member-plan position-absolute"><span class="badge badge-dark">Promoted</span></div>
            @else

            @endif

            <a href="{{ route('market.details', $client->id) }}">
            <img src="{{ asset('upload/client_images/' . $client->photo) }}" class="img-fluid item-img" style="width: 300px; hight:200px">
            </a>
         </div>
         <div class="p-3 position-relative">
            <div class="list-card-body">
               <h6 class="mb-1"><a href="detail.html" class="text-black">{{ $client->name }}</a></h6>
               <p class="text-gray mb-3">{{ $menuNamesString }}</p>
               <p class="text-gray mb-3 time"><span class="bg-light text-dark rounded-sm pl-2 pb-1 pt-1 pr-2"><i class="icofont-wall-clock"></i> 20–25 min</span> <span class="float-right text-black-50"> $250 FOR TWO</span></p>
            </div>
            <div class="list-card-badge">
               @if ($coupons)
                  <span class="badge badge-success">OFFER</span> <small>{{ $coupons->discount }}% off | Use Coupon {{ $coupons->coupon_name }}</small>
               @else
                  <span class="badge badge-success">OFFER</span> <small>Have Not Coupon</small>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>

@endforeach
     </div>


     <div class="section-header text-center">
      <h2>List sản phẩm</h2>
      <p>Top markets, vegetables, milks, and ...</p>
      <span class="line"></span>
   </div>
   
   <div class="col-12">
      <div class="row" id="product-list">
         @foreach ($products_list as $product)
            <div class="custom-col mb-4">
               <div class="card h-100 shadow-sm rounded border-0 d-flex flex-column">
                  <a href="{{ route('market.details', $product->client_id) }}" class="text-decoration-none">
                     <img src="{{ asset(optional($product->productTemplate)->image ?? 'upload/no_image.jpg') }}"
                        class="card-img-top"
                        alt="{{ optional($product->productTemplate)->name }}"
                        style="height:200px; object-fit:cover;">
                  </a>
                  <div class="card-body d-flex flex-column">
                     <h5 class="card-title mb-2" style="min-height: 48px;">
                        <a href="{{ route('market.details', $product->client_id) }}" class="text-dark">
                           {{ optional($product->productTemplate)->name ?? 'No Name' }}
                        </a>
                     </h5>
                     <p class="card-text text-muted mb-2" style="font-size:14px; min-height: 20px;">
                        {{ $product->productTemplate->category->category_name ?? 'No Category' }}
                     </p>
                     <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-success font-weight-bold">{{ number_format($product->price) }} VNĐ</span>
                        @php
                           $discountPercent = null;
                           if (!empty($product->discount_price) && $product->price > 0) {
                              $discountPercent = round((1 - $product->discount_price / $product->price) * 100);
                           }
                        @endphp
                        @if ($discountPercent)
                           <span class="badge badge-danger">{{ $discountPercent }}% OFF</span>
                        @else
                           <span class="badge badge-secondary">No Discount</span>
                        @endif
                     </div>
            
                     <div class="mt-auto">
                        {{-- <form action="{{ route('cart.add', $product->id) }}" method="POST"> --}}
                        <form action="#" method="POST">
                           @csrf
                           <button type="submit" class="btn btn-sm btn-primary w-100">
                              <i class="icofont-cart"></i> Thêm vào giỏ
                           </button>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         
         @endforeach
      </div>
   </div>

</div>
</section>

<style>
#product-list .card {
   min-height: 100%;
   display: flex;
   flex-direction: column;
}

.custom-col {
      width: 20%;
      padding: 0 10px;
      box-sizing: border-box;
   }

   @media (max-width: 992px) {
      .custom-col {
         width: 33.3333%; /* 3 cột trên tablet */
      }
   }
   @media (max-width: 768px) {
      .custom-col {
         width: 50%; /* 2 cột trên mobile */
      }
   }
   @media (max-width: 576px) {
      .custom-col {
         width: 100%; /* 1 cột nhỏ */
      }
   }
</style>

@endsection