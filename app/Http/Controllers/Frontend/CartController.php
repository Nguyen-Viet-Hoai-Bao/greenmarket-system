<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\ProductNew;
use App\Models\Coupon;
use App\Models\Menu;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function AddToCart($id) {
        $product = ProductNew::with('productTemplate')->find($id);

        // Kiểm tra tồn tại
        if (!$product) {
            return redirect()->back()->with([
                'message' => 'Product not found.',
                'alert-type' => 'error'
            ]);
        }

        // Kiểm tra client_id có trùng với selected_market_id
        $selectedMarketId = session('selected_market_id');

        if ($product->client_id != $selectedMarketId) {
            return redirect()->back()->with([
                'message' => 'Sản phẩm không thuộc cửa hàng hiện tại.',
                'alert-type' => 'error'
            ]);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $priceToShow = isset($product->discount_price) 
                            ? $product->discount_price 
                            : $product->price;

            $cart[$id] = [
                'id' => $id,
                'name' => $product->productTemplate->name,
                'image' => $product->productTemplate->image,
                'price' => $priceToShow,
                'client_id' => $product->client_id,
                'quantity' => 1,
                'menu_name' => optional($product->productTemplate->menu)->name ?? null, // nếu muốn thêm
            ];
        }

        session()->put('cart', $cart);

        $this->recalculateCoupon();

        return redirect()->back()->with([
            'message' => 'Thêm vào giỏ hàng thành công.',
            'alert-type' => 'success'
        ]);
    }

    // End Method

    public function UpdateCartQuantity(Request $request) {
        $cart = session()->get('cart', []);
    
        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
    
            if ($cart[$request->id]['quantity'] <= 0) {
                unset($cart[$request->id]);
            }
    
            session()->put('cart', $cart);
        }
    
        $this->recalculateCoupon();
    
        return response()->json([
            'message' => 'Quantity Updated',
            'alert-type' => 'success'
        ]);
    }    
    // End Method

    public function CartRemove(Request $request) {
        $cart = session()->get('cart', []);
        if(isset($cart[$request->id])){
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }
        
        // Recalculate Coupon
        $this->recalculateCoupon();

        return response()->json([
            'message' => 'Cart Remove Successfully',
            'alert-type' => 'success'
        ]);
    }
    // End Method

    public function ApplyCoupon(Request $request) {
        $coupon = Coupon::where('coupon_name', $request->coupon_name)
                        ->where('validity', '>=', Carbon::now()->format('Y-m-d'))
                        ->first();
        $cart = session()->get('cart', []);
        $totalAmount = 0;
        $clientIds = [];

        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
            $pd = ProductNew::find($car['id']);
            $clid = $pd->client_id;
            array_push($clientIds, $clid);
        }

        if ($coupon) {
            if (count(array_unique($clientIds)) === 1) {
                $cclientId = $coupon->client_id;
                if ($cclientId == $clientIds[0]) {
                    Session::put('coupon', [
                        'coupon_name' => $coupon->coupon_name,
                        'discount' => $coupon->discount,
                        'discount_amount' => $totalAmount - ($totalAmount * $coupon->discount / 100),
                    ]);
                    $couponData = Session()->get('coupon');
                    return response()->json(array(
                        'validity' => true,
                        'success' => 'Coupon Applied Successfully',
                        'couponData' => $couponData,
                    ));
                } else {
                    return response()->json([
                        'error' => 'This Coupon Not Valid for Market',
                    ]);
                }
            } else {
                return response()->json([
                    'error' => 'This Coupon for one of the selected Market',
                ]);
            }
        } else {
            return response()->json([
                'error' => 'Invalid Coupon',
            ]);
        }
    }
    // End Method

    public function RemoveCoupon() {
        Session::forget('coupon');
        return response()->json([
            'success' => 'Coupon Remove Successfully',
        ]);
    }
    // End Method

    // logic coupon
    private function recalculateCoupon(){
        if (!Session::has('coupon')) {
            return;
        }

        $coupon_name = Session::get('coupon')['coupon_name'];

        $coupon = Coupon::where('coupon_name', $coupon_name)
                        ->where('validity', '>=', Carbon::now()->format('Y-m-d'))
                        ->first();

        $cart = session()->get('cart', []);
        $totalAmount = 0;
        $clientIds = [];

        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
            $pd = ProductNew::find($car['id']);
            $clid = $pd->client_id;
            array_push($clientIds, $clid);
        }

        if ($coupon && count(array_unique($clientIds)) === 1 && $coupon->client_id == $clientIds[0]) {
            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'discount' => $coupon->discount,
                'discount_amount' => $totalAmount - ($totalAmount * $coupon->discount / 100),
            ]);
        } else {
            Session::forget('coupon');
        }
    }
    // End Method

    public function MarketCheckout(){
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


        if(Auth::check()){
            $cart = session()->get('cart', []);
            $totalAmount = 0;
            foreach ($cart as $car) {
                $totalAmount += $car['price'];
            }

            if ($totalAmount > 0) {

                return view('frontend.checkout.view_checkout', compact('cart', 'cities', 'menus_footer', 'products_list'));

            } else {

                $notification = array(
                    'message' => 'Vui lòng mua ít nhất một món hàng',
                    'alert-type' => 'error'
                );
                return redirect()->to('/')->with($notification);
                
            }
            

        } else {
            $notification = array(
                'message' => 'Vui lòng hãy đăng nhập trước khi sử dụng',
                'alert-type' => 'error'
            );
            return redirect()->route('login')->with($notification);
        }
    }
    // End Method

}