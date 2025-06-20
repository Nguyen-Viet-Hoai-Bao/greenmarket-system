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
use App\Models\Client;
use App\Models\Menu;
use App\Models\Order;
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

    public function AjaxAddToCart($id)
    {
        $product = ProductNew::with('productTemplate')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.'
            ], 404);
        }

        $selectedMarketId = session('selected_market_id');
        if ($product->client_id != $selectedMarketId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm không thuộc cửa hàng hiện tại.'
            ], 403);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $priceToShow = $product->discount_price ?? $product->price;
            $cart[$id] = [
                'id' => $id,
                'name' => $product->productTemplate->name,
                'image' => $product->productTemplate->image,
                'price' => $priceToShow,
                'client_id' => $product->client_id,
                'quantity' => 1,
                'menu_name' => optional($product->productTemplate->menu)->name ?? null,
            ];
        }

        session()->put('cart', $cart);

        $this->recalculateCoupon();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
            'cartItem' => $cart[$id]
        ]);
    }

    // public function AjaxUpdateCart(Request $request, $id)
    // {
    //     $cart = session('cart', []);
    //     if (isset($cart[$id])) {
    //         $cart[$id]['quantity'] = (int) $request->quantity;
    //         session(['cart' => $cart]);
    //         return response()->json(['status' => 'success', 'cartItem' => $cart[$id]]);
    //     }
    //     return response()->json(['status' => 'error']);
    // }

    public function AjaxUpdateCart(Request $request, $id)
    {
        $cart = session('cart', []);
        
        if (isset($cart[$id])) {
            $product = ProductNew::find($id); 
            
            if (!$product) {
                return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại.']);
            }

            if ($product->qty < (int) $request->quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cửa hàng chỉ còn ' . $product->qty . ' sản phẩm.'
                ]);
            }

            // Nếu đủ số lượng, thì cập nhật
            $cart[$id]['quantity'] = (int) $request->quantity;
            session(['cart' => $cart]);

            $this->recalculateCoupon();
            return response()->json(['status' => 'success', 'cartItem' => $cart[$id]]);
        }

        return response()->json(['status' => 'error', 'message' => 'Sản phẩm không có trong giỏ hàng.']);
    }


    public function AjaxRemoveFromCart(Request $request, $id)
    {
        $cart = session('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
            return response()->json(['status' => 'success']);
        }
        $this->recalculateCoupon();
        return response()->json(['status' => 'error']);
    }

    public function AjaxReloadCart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $coupon = session('coupon');

        foreach ($cart as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        $discountAmount = $coupon ? $total * ($coupon['discount'] / 100) : 0;
        $finalAmount = $total - $discountAmount;

        $this->recalculateCoupon();
        return response()->json([
            'html' => view('frontend.cart.partial', compact('cart', 'total', 'coupon', 'discountAmount', 'finalAmount'))->render()
        ]);
    }

    public function AjaxReloadCartHeader()
    {
        $cart = session()->get('cart', []);
        $groupedCart = [];
        $total = 0;

        foreach ($cart as $item) {
            $groupedCart[$item['client_id']][] = $item;
        }

        $selectedMarketId = session('selected_market_id');

        if (!$selectedMarketId) {
            return response()->json(['error' => 'No selected_market_id in session'], 500);
        }

        $clients = Client::where('id', $selectedMarketId)->get()->keyBy('id');

        $this->recalculateCoupon();

        try {
            $html = view('frontend.cart.header_partial', compact('cart', 'groupedCart', 'clients', 'total'))->render();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'html' => $html,
        ]);
    }


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
        $user = Auth::guard('web')->user();
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
            $userHasUsedCoupon = Order::where('user_id', $user->id)
                                    ->where('coupon_code', $coupon->id)
                                    ->exists();

            if ($userHasUsedCoupon) {
                return response()->json([
                    'error' => 'Bạn đã sử dụng mã giảm giá này trước đó.',
                ]);
            }

            if (count(array_unique($clientIds)) === 1) {
                $cclientId = $coupon->client_id;
                if ($cclientId == $clientIds[0] || $cclientId == 0) {
                    if ($cclientId == 0 && $coupon->quantity_apply <= 0) {
                        return response()->json([
                            'error' => 'Mã giảm giá này đã hết lượt sử dụng.',
                        ]);
                    }
                    
                    $calculatedDiscount = $totalAmount * $coupon->discount / 100;
                    if ($coupon->max_discount_amount && $calculatedDiscount > $coupon->max_discount_amount) {
                        $discountAmount = $coupon->max_discount_amount;
                    } else {
                        $discountAmount = $calculatedDiscount;
                    }

                    $coupon->decrement('quantity_apply');

                    // Tính phí giao hàng
                    $shippingFee = $totalAmount - $discountAmount > 100000 ? 0 : 15000;
                    Session::put('shipping_fee', $shippingFee);

                    Session::put('coupon', [
                        'coupon_id' => $coupon->id,
                        'coupon_name' => $coupon->coupon_name,
                        'discount' => $coupon->discount,
                        'discount_amount' => $totalAmount - $discountAmount,
                    ]);
                    $couponData = Session()->get('coupon');
                    return response()->json(array(
                        'validity' => true,
                        'success' => 'Áp dụng mã giảm giá thành công',
                        'couponData' => $couponData,
                    ));
                } else {
                    return response()->json([
                        'error' => 'Mã giảm giá không phù hợp với cửa hàng này',
                    ]);
                }
            } else {
                return response()->json([
                    'error' => 'Vui lòng chọn sản phẩm trước khi áp dụng',
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
        $couponSession = session()->get('coupon');

        if ($couponSession && isset($couponSession['coupon_id'])) {
            $coupon = Coupon::find($couponSession['coupon_id']);
            if ($coupon) {
                $coupon->increment('quantity_apply');
            }
        }

        Session::forget('coupon');
        
        $cart = session()->get('cart', []);
        $totalAmount = 0;
        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
        }
        // Tính phí giao hàng
        if ($totalAmount === 0) {
            $shippingFee = 0;
        } elseif ($totalAmount > 100000) {
            $shippingFee = 0;
        } else {
            $shippingFee = 15000;
        }

        Session::put('shipping_fee', $shippingFee);
        
        return response()->json([
            'success' => 'Xóa mã giảm giá thành công',
        ]);
    }
    // End Method

    // logic coupon
    private function recalculateCoupon(){
        if (!Session::has('coupon')) {
            $cart = session()->get('cart', []);
            $totalAmount = 0;

            foreach ($cart as $car) {
                $totalAmount += ($car['price'] * $car['quantity']);
            }

            // Tính phí giao hàng
            if ($totalAmount === 0) {
                $shippingFee = 0;
            } elseif ($totalAmount > 100000) {
                $shippingFee = 0;
            } else {
                $shippingFee = 15000;
            }

            Session::put('shipping_fee', $shippingFee);
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
            $clientIds[] = $clid;
        }
        
        if (!$coupon) {
            Session::forget('coupon');
            return;
        }

        if ($coupon->client_id == 0) {
            $calculatedDiscount = $totalAmount * $coupon->discount / 100;
            $discountAmount = ($coupon->max_discount_amount && $calculatedDiscount > $coupon->max_discount_amount)
                ? $coupon->max_discount_amount
                : $calculatedDiscount;

            // Tính phí giao hàng
            if ($totalAmount - $discountAmount > 100000) {
                $shippingFee = 0;
            } else {
                $shippingFee = 15000;
            }
            Session::put('shipping_fee', $shippingFee);

            Session::put('coupon', [
                'coupon_id' => $coupon->id,
                'coupon_name' => $coupon->coupon_name,
                'discount' => $coupon->discount,
                'discount_amount' => $totalAmount - $discountAmount, // ✅ là số tiền còn lại
            ]);
        } elseif ($coupon->client_id != 0 && count(array_unique($clientIds)) === 1 && $coupon->client_id == $clientIds[0]) {
            $discountAmount = $totalAmount * $coupon->discount / 100;

            // Tính phí giao hàng
            if ($totalAmount - $discountAmount > 100000) {
                $shippingFee = 0;
            } else {
                $shippingFee = 15000;
            }
            Session::put('shipping_fee', $shippingFee);

            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'discount' => $coupon->discount,
                'discount_amount' => $totalAmount - $discountAmount,
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
            $shipping_fee = session()->get('shipping_fee');
            $cart = session()->get('cart', []);
            $coupon = session()->get('coupon', []);
            $totalAmount = 0;
            foreach ($cart as $car) {
                $totalAmount += $car['price'];
            }

            if ($totalAmount > 0) {

                return view('frontend.checkout.view_checkout', compact('cart', 'cities', 'menus_footer', 'products_list', 'shipping_fee'));

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