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
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Admin;

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
            'payment_type' => 'Thanh toán khi nhận hàng',
            'payment_method' => 'Thanh toán khi nhận hàng',

            'currency' => 'VNĐ',
            'amount' => $totalAmount,
            'total_amount' => $tt,
            'invoice_no' => $invoice,
            'order_date' => Carbon::now()->format('d F Y'),
            'order_month' => Carbon::now()->format('F'),
            'order_year' => Carbon::now()->format('Y'),

            'status' => 'pending',
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
        return view('frontend.checkout.thanks')->with($notification);
        
    }
    // End Method








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
