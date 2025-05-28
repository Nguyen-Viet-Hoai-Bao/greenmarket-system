<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

use App\Notifications\OrderComplete;
use App\Models\ProductNew;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Client;
use App\Models\Admin;

use App\Models\Menu;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function CashOrder(Request $request) {

        $user = Admin::where('role', 'admin')
                        ->get();

        $validateData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);

        $cart = session()->get('cart', []);
        $totalAmount = 0;

        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
        }

        if (Session()->has('coupon')) {
            $tt = (Session()->get('coupon')['discount_amount']);
        } else {
            $tt = $totalAmount;
        }

        do {
            $invoice = 'Green' . mt_rand(10000000, 99999999);
            $exists = Order::where('invoice_no', $invoice)->exists(); // kiểm tra trùng
        } while ($exists);

        $order_id = Order::insertGetId([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'ward_id' => $request->locality_code,
            'payment_type' => 'Thanh toán khi nhận hàng',
            'payment_method' => 'Thanh toán khi nhận hàng',

            'currency' => 'VNĐ',
            'amount' => $totalAmount,
            'total_amount' => $tt,
            'invoice_no' => $invoice,
            'order_date' => Carbon::now()->format('d F Y'),
            'order_month' => Carbon::now()->format('F'),
            'order_year' => Carbon::now()->format('Y'),

            'status' => 'confirm',
            'created_at' => Carbon::now(),
        ]);

        $clientIds = []; // các client_id của order items

        $carts = session()->get('cart', []);
        foreach ($carts as $cart_item) {
            OrderItem::insert([
                'order_id' => $order_id,
                'product_id' => $cart_item['id'],
                'client_id' => $cart_item['client_id'],
                'qty' => $cart_item['quantity'],
                'price' => $cart_item['price'],
                'created_at' => Carbon::now(),
            ]);

            $clientIds[] = $cart_item['client_id'];
            
            // Giảm số lượng sản phẩm tương ứng
            ProductNew::where('id', $cart_item['id'])->decrement('qty', $cart_item['quantity']);
        } // end foreach

        if (Session::has('coupon')) {
            Session::forget('coupon');
        } 
        
        if (Session::has('cart')) {
            Session::forget('cart');
        } 

        Notification::send($user, new OrderComplete($request->name));
        $clients = Client::whereIn('id', array_unique($clientIds))->get();
        Notification::send($clients, new OrderComplete($request->name));
        
        $notification = array(
            'message' => 'Đặt hàng thành công',
            'alert-type' => 'success'
        );

        
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

        return view('frontend.checkout.thanks', compact('cities', 'menus_footer', 'products_list'))->with($notification);
        
    }
    // End Method



    public function VnpayOrder(Request $request) {

        $user = Admin::where('role', 'admin')
                        ->get();

        $validateData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);

        $cart = session()->get('cart', []);
        $totalAmount = 0;

        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
        }

        if (Session()->has('coupon')) {
            $tt = (Session()->get('coupon')['discount_amount']);
        } else {
            $tt = $totalAmount;
        }

        do {
            $invoice = 'Green' . mt_rand(10000000, 99999999);
            $exists = Order::where('invoice_no', $invoice)->exists(); // kiểm tra trùng
        } while ($exists);

        $order_id = Order::insertGetId([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'payment_type' => 'Đã thanh toán bằng VNPay',
            'payment_method' => 'Đã thanh toán bằng VNPay',

            'currency' => 'VNĐ',
            'amount' => $totalAmount,
            'total_amount' => $tt,
            'invoice_no' => $invoice,
            'order_date' => Carbon::now()->format('d F Y'),
            'order_month' => Carbon::now()->format('F'),
            'order_year' => Carbon::now()->format('Y'),

            'status' => 'confirm',
            'created_at' => Carbon::now(),
        ]);

        $carts = session()->get('cart', []);
        foreach ($carts as $cart_item) {
            OrderItem::insert([
                'order_id' => $order_id,
                'product_id' => $cart_item['id'],
                'client_id' => $cart_item['client_id'],
                'qty' => $cart_item['quantity'],
                'price' => $cart_item['price'],
                'created_at' => Carbon::now(),
            ]);
        } // end foreach

        if (Session::has('coupon')) {
            Session::forget('coupon');
        } 
        
        if (Session::has('cart')) {
            Session::forget('cart');
        } 

        // Send Notification to Admin
        Notification::send($user, new OrderComplete($request->name));
        
        $notification = array(
            'message' => 'Đặt hàng thành công',
            'alert-type' => 'success'
        );
        
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
        return view('frontend.checkout.thanks', compact('cities', 'menus_footer', 'products_list'))->with($notification);
        
    }
    // End Method

    public function CancelOrder(Request $request, $id)
    {
        $order = Order::where('id', $id)
                        ->where('user_id', Auth::id())
                        ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng.');
        }

        if (!in_array($order->status, ['confirm'])) {
            return redirect()->back()->with('error', 'Đơn hàng không thể huỷ ở trạng thái hiện tại.');
        }

        $order->update([
            'status' => 'cancel_pending',
            'cancel_reason' => $request->cancel_reason ?? 'Người dùng huỷ',
        ]);

        return redirect()->route('user.order.list')->with('success', 'Huỷ đơn hàng thành công.');
    }


    public function CheckoutThanks(){
        $notification = array(
            'message' => 'Đặt hàng thành công',
            'alert-type' => 'success'
        );
        
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
        return view('frontend.checkout.thanks', compact('cities', 'menus_footer', 'products_list'))->with($notification);
    }

    public function MarkAsRead(Request $request, $notificationId){
        $user = Auth::guard('admin')->user();
        $notification = $user->notifications()->where('id',$notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['count' => $user->unreadNotifications()->count()]);
    }
    //End Method 
}
