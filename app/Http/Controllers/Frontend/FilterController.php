<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; 

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class FilterController extends Controller
{
    public function ListMarket(){
        $products = Product::all();
        return view('frontend.list_market', compact('products'));
    }
    //End Method 

    public function FilterProducts(Request $request){
 // Log::info('request data' , $request->all());
 
 
         $categoryId = $request->input('categorys');
         $menuId = $request->input('menus');
         $cityId = $request->input('citys');
 
         $products = Product::query();
 
         if ($categoryId) {
             $products->whereIn('category_id',$categoryId);
         }
         if ($menuId) {
             $products->whereIn('menu_id',$menuId);
         }
         if ($cityId) {
             $products->whereIn('city_id',$cityId);
         }
 
         $filterProducts = $products->get();
 
         return view('frontend.product_list',compact('filterProducts'))->render();
    }
    //End Method 
}
