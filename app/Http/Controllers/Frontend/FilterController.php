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
use App\Models\Ward;
use App\Models\City;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    public function ListMarket(){
        // $products = ProductNew::with([
        //                             'productTemplate.menu',
        //                             'productTemplate.category'
        //                         ])->orderBy('id', 'desc')->get();
        $query = ProductNew::with([
            'productTemplate.menu',
            'productTemplate.category'
        ]);
    
        if (session()->has('selected_market_id')) {
            $selectedMarketId = session('selected_market_id');
            $query->where('client_id', $selectedMarketId); 
        }
    
        $products = $query->orderByDesc('most_popular') 
                            ->orderByDesc('best_seller')
                            ->orderBy('id', 'desc')->get();

        $fullAddress = null;
        if (session()->has('selected_market_ward_id')) {
            $ward = Ward::with('district.city')->find(session('selected_market_ward_id'));
        
            if ($ward && $ward->district && $ward->district->city) {
                $fullAddress = $ward->ward_name . ', ' 
                                . $ward->district->district_name . ', ' 
                                . $ward->district->city->city_name;
            }
        }

        $cities = City::all();

        $menusWithCategories = Menu::with(['categories'])->get();

        // For Footer
        $menus_footer = Menu::all();
        $topClientId = ProductNew::select('client_id', DB::raw('COUNT(*) as total'))
                                ->groupBy('client_id')
                                ->orderByDesc('total')
                                ->value('client_id'); 
        $products_list = ProductNew::with([
                        'productTemplate.menu',
                        'productTemplate.category'
                    ])
                    ->where('client_id', $topClientId)
                    ->orderBy('id', 'desc')
                    ->get();
        
        return view('frontend.list_market', compact('products', 'fullAddress', 'menusWithCategories', 'cities', 'menus_footer', 'products_list'));
    }
    //End Method 

    // public function FilterProducts(Request $request)
    // {
    //     $categoryId = $request->input('categorys'); // sửa lại đúng tên biến từ JS
    //     $menuId = $request->input('menus');

    //     $products = ProductNew::with([
    //         'productTemplate.menu',
    //         'productTemplate.category'
    //     ])->whereHas('productTemplate', function ($query) use ($categoryId, $menuId) {
    //         if ($categoryId) {
    //             $query->whereIn('category_id', $categoryId);
    //         }
    //         if ($menuId) {
    //             $query->whereIn('menu_id', $menuId);
    //         }
    //     });
    //     // Lọc theo market được chọn trong session
    //     if (session()->has('selected_market_id')) {
    //         $selectedMarketId = session('selected_market_id');
    //         $products->where('client_id', $selectedMarketId); // đổi nếu cột khác tên
    //     }

    //     $filterProducts = $products->orderByDesc('most_popular') 
    //                         ->orderByDesc('best_seller')
    //                         ->orderBy('id', 'desc')->get();

    //     return view('frontend.product_list', compact('filterProducts'))->render();
    // }
    // //End Method 

    public function FilterProducts(Request $request)
    {
        $categoryIds = $request->input('categories', []); // array
        $menuIds = $request->input('menus', []); // array

        $productsQuery = ProductNew::with([
            'productTemplate.menu',
            'productTemplate.category'
        ])->where('qty', '>', 0)->whereHas('productTemplate', function ($query) use ($categoryIds, $menuIds) {
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
            if (!empty($menuIds)) {
                $query->whereIn('menu_id', $menuIds);
            }
        });

        // Filter by market from session
        if (session()->has('selected_market_id')) {
            $selectedMarketId = session('selected_market_id');
            $productsQuery->where('client_id', $selectedMarketId);
        }

        $filterProducts = $productsQuery
            ->orderByDesc('most_popular')
            ->orderByDesc('best_seller')
            ->orderByDesc('id')
            ->get();

        return view('frontend.filter_product', compact('filterProducts'))->render();
    }

    public function ProductDetail($id)
    {
        $product = ProductNew::with('productTemplate')->find($id);
        

        if (!$product) {
            abort(404);
        }

        // Lấy productDetail từ productTemplate
        $productDetail = $product->productTemplate->productDetail;

        // For Footer
        $cities = City::all();
        $menus_footer = Menu::all();
        $topClientId = ProductNew::select('client_id', DB::raw('COUNT(*) as total'))
                                ->groupBy('client_id')
                                ->orderByDesc('total')
                                ->value('client_id'); 
        $products_list = ProductNew::with([
                        'productTemplate.menu',
                        'productTemplate.category'
                    ])
                    ->where('client_id', $topClientId)
                    ->orderBy('id', 'desc')
                    ->get();
        
        return view('frontend.product_detail', compact('product', 'productDetail', 'cities', 'menus_footer', 'products_list'));
    }

    public function SearchProducts(Request $request)
    {
        $search = $request->input('query');

        $marketId = session('selected_market_id');
        
        // Lấy product theo tên search và market
        $products_search = ProductNew::with(['productTemplate.menu', 'productTemplate.category'])
            ->where('client_id', $marketId)
            ->whereHas('productTemplate', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->orderByDesc('most_popular')
            ->orderByDesc('best_seller')
            ->orderByDesc('id')
            ->get();

        // Lấy các category ID của các product tìm được
        $categoryIds = $products_search->pluck('productTemplate.category_id')->unique()->toArray();
        $menuIdsToExpand = $products_search->pluck('productTemplate.menu_id')->unique()->toArray();

        // Lấy danh sách menu và category (chỉ giữ các category có trong $categoryIds)
        // $menusWithCategories = Menu::with(['categories' => function ($query) use ($categoryIds) {
        //     $query->whereIn('id', $categoryIds);
        // }])->get();
        $menusWithCategories = Menu::with(['categories'])->get();
        $products_all = ProductNew::with([
                        'productTemplate.menu',
                        'productTemplate.category'
                    ])
                    ->where('client_id', session('selected_market_id'))
                    ->orderBy('id', 'desc')
                    ->get();

        return view('frontend.search_product', compact('products_all', 'products_search', 'menusWithCategories', 'categoryIds', 'menuIdsToExpand'))->render();
    }

    
}
