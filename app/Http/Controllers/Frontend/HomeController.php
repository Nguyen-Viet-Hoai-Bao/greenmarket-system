<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Menu;
use App\Models\ProductNew;
use App\Models\District;
use App\Models\Ward;
use App\Models\City;
use App\Models\Gallery;
use App\Models\Wishlist;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function MarketDetails($id = null) {
        if (!$id && session()->has('selected_market_id')) {
            $id = session('selected_market_id');
        }
        $client = Client::find($id);

        $oldMarketId = session('selected_market_id');

        if ($oldMarketId && $oldMarketId != $id) {
            $oldCart = session()->get('cart', []);
            $newCart = [];
            $removed = false;

            foreach ($oldCart as $item) {
                $oldProduct = ProductNew::where('status', 1)->find($item['id']);

                if (!$oldProduct) {
                    $removed = true;
                    continue;
                }

                $newProducts = ProductNew::where('product_template_id', $oldProduct->product_template_id)
                                        ->where('status', 1)
                                        ->where('client_id', $id)
                                        ->get();

                if ($newProducts->count() > 0) {
                    foreach ($newProducts as $newProduct) {
                        $newItem = $item;
                        $newItem['id'] = $newProduct->id;
                        $newItem['client_id'] = $id;
                        $newItem['price'] = $newProduct->discount_price;
                        $newCart[$newProduct->id] = $newItem;
                    }
                } else {
                    $removed = true;
                }
            }


            session()->put('cart', $newCart);

            if ($removed) {
                session()->flash('cart_item_removed', true);
            }
        }

        // Chỉ cập nhật session nếu khác
        if (!session()->has('selected_market_id') || session('selected_market_id') != $id) {
            session([
                'selected_market_id' => $client->id,
                'selected_market_name' => $client->name,
                'selected_market_ward_id' => $client->ward_id,
            ]);
        }
        $menus = Menu::whereHas('products.productNews', function ($query) use ($id) {
            $query->where('client_id', $id);
        })
        ->with([
            'products' => function ($query) use ($id) {
                $query->whereHas('productNews', function ($q) use ($id) {
                    $q->where('client_id', $id);
                })
                ->with(['productNews' => function ($q) use ($id) {
                    $q->where('client_id', $id)->where('qty', '>', 0);
                }]);
            }
        ])
        ->get();
        // dd($menus);

        $gallerys = Gallery::where('client_id', $id)->get();

        $reviews = Review::where('client_id', $client->id)
                            ->where('status',1)
                            ->get();
        $totalReviews = $reviews->count();
        $ratingSum = $reviews->sum('rating');
        $averageRating = $totalReviews > 0 ? $ratingSum / $totalReviews : 0;
        $roundedAverageRating = round($averageRating, 1);
        
        $ratingCounts = [
            '5' => $reviews->where('rating',5)->count(),
            '4' => $reviews->where('rating',4)->count(),
            '3' => $reviews->where('rating',3)->count(),
            '2' => $reviews->where('rating',2)->count(),
            '1' => $reviews->where('rating',1)->count(),
        ];
        $ratingPercentages =  array_map(function ($count) use ($totalReviews){
            return $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
        },$ratingCounts);
    

        
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
                    ->where('qty', '>', 0)
                    ->orderBy('id', 'desc')
                    ->get();

        // dd(session()->all());

        $menusWithCategories = Menu::with(['categories'])->get();
        $products_all = ProductNew::with([
                        'productTemplate.menu',
                        'productTemplate.category'
                    ])
                    ->where('client_id', session('selected_market_id'))
                    ->where('status', 1)
                    ->where('qty', '>', 0)
                    ->orderBy('id', 'desc')
                    ->get();

        return view('frontend.details_page',
                        compact('client','menus','gallerys','reviews','roundedAverageRating', 'menusWithCategories', 'products_all',
                                'totalReviews','ratingCounts','ratingPercentages', 'fullAddress', 'cities', 'menus_footer', 'products_list'));
    }
    // end method

    public function AddWishlist(Request $request, $id) {
        if(Auth::check()){
            $exists = Wishlist::where('user_id', Auth::id())
                            ->where('client_id', $id)
                            ->first();
            if (!$exists) {
                Wishlist::insert([
                    'user_id' => Auth::id(),
                    'client_id' => $id,
                    'created_at' => Carbon::now(),
                ]);
                return response()->json(['success' => 'Add Wishlist Successfully']);
            } else {
                return response()->json(['error' => 'This Market has already on your Wishlist']);
            }
        } else {
            return response()->json(['error' => 'Fisrt Login Your Account']);
        }
    }
    // end method

    public function AllWishlist() {
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

        $wishlist = Wishlist::where('user_id', Auth::id())
                            ->get();
        return view('frontend.dashboard.all_wishlist', compact('wishlist', 'cities', 'menus_footer', 'products_list'));
    }
    // end method

    public function RemoveWishlist($id) {
        Wishlist::find($id)->delete();
        $notification = array(
            'message' => 'Wishlist Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // end method

    // public function RedirectToDetails(Request $request)
    // {
    //     $marketId = $request->market_id;
    //     $market = Client::findOrFail($marketId);

    //     session([
    //         'selected_market_id' => $market->id,
    //         'selected_market_name' => $market->name,
    //         'selected_market_ward_id' => $market->ward_id,
    //     ]);

    //     $marketId = $request->market_id;
    //     return redirect()->route('market.details', $marketId);
    // }
    public function RedirectToDetails(Request $request)
    {
        $newMarketId = $request->market_id;
        $newMarket = Client::findOrFail($newMarketId);
        

        // Cập nhật session thị trường mới
        session([
            'selected_market_id' => $newMarket->id,
            'selected_market_name' => $newMarket->name,
            'selected_market_ward_id' => $newMarket->ward_id,
        ]);
        return redirect()->route('market.details', $newMarketId);
    }

    public function GetDistricts($city_id)
    {
        // return District::where('city_id', $city_id)->get();
        $districts = District::where('city_id', $city_id)->get(['id', 'district_name']);
        return response()->json($districts);
    }

    public function GetWards($district_id)
    {
        return Ward::where('district_id', $district_id)->get();
    }

    public function GetMarketsByWard($ward_id)
    {
        return Client::where('ward_id', $ward_id)->where('status', '1')->get();
    }

    // session()->forget(['selected_market_id', 'selected_market_name', 'selected_market_ward_id']);

}
