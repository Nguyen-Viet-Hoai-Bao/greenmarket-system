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
use App\Models\ProductNew;
use App\Models\Order;
use App\Models\OrderItem;

class FilterController extends Controller
{
    public function ListMarket(){
        // $products = ProductNew::all();
        $products = ProductNew::with([
                                    'productTemplate.menu',
                                    'productTemplate.category'
                                ])->orderBy('id', 'desc')->get();
        
        return view('frontend.list_market', compact('products'));
    }
    //End Method 

    public function FilterProducts(Request $request)
    {
        $categoryId = $request->input('categorys'); // sửa lại đúng tên biến từ JS
        $menuId = $request->input('menus');
        $cityId = $request->input('cities');

        $products = ProductNew::with([
            'productTemplate.menu',
            'productTemplate.category'
        ])->whereHas('productTemplate', function ($query) use ($categoryId, $menuId) {
            if ($categoryId) {
                $query->whereIn('category_id', $categoryId);
            }
            if ($menuId) {
                $query->whereIn('menu_id', $menuId);
            }
        });

        if ($cityId) {
            $products->whereIn('city_id', $cityId);
        }

        $filterProducts = $products->orderBy('id', 'desc')->get();

        return view('frontend.product_list', compact('filterProducts'))->render();
    }
    //End Method 
}
