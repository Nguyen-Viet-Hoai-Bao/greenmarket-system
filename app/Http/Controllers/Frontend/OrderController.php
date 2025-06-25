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
use App\Notifications\OrderPlaced;
use App\Notifications\OrderCancelRequested;
use App\Notifications\OrderCancelRequestedByUser;

use App\Models\ProductNew;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Client;
use App\Models\Admin;
use App\Models\OrderReport;
use App\Models\ProductUnit;

use App\Models\Menu;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function CashOrder(Request $request) {

        $admin = Admin::where('role', 'admin')
                        ->get();

        $validateData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);

        $cart = session()->get('cart', []);
        $shipping_fee = session()->get('shipping_fee');
        $totalAmount = 0;
        $totalCostPrice = 0;

        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);

            // Lấy cost_price
            $productUnit = ProductUnit::find($car['product_unit_id']);
            $costPrice = $productUnit->cost_price ?? 0;
            $totalCostPrice += ($costPrice * $car['quantity']);
        }
        // Tính phí dịch vụ 8% của tổng đơn
        $serviceFee = $totalAmount * 0.08;

        if (Session()->has('coupon')) {
            $coupon_code = (Session()->get('coupon')['coupon_id']);
            $tt = (Session()->get('coupon')['discount_amount']);
        } else {
            $tt = $totalAmount;
            $coupon_code = null;
        }

        // Tính net_revenue
        $netRevenue = $tt - $serviceFee - $totalCostPrice;

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
            'total_amount' => $tt + $shipping_fee,
            'coupon_code' => $coupon_code,
            'service_fee'    => $serviceFee,
            'shipping_fee'    => $shipping_fee,
            'net_revenue'    => $netRevenue + $shipping_fee,
            'invoice_no' => $invoice,
            'order_date' => Carbon::now()->format('d M Y'),
            'order_month' => Carbon::now()->format('M'),
            'order_year' => Carbon::now()->format('Y'),

            'status' => 'pending',
            'created_at' => Carbon::now(),
        ]);

        $clientIds = []; // các client_id của order items

        $carts = session()->get('cart', []);
        foreach ($carts as $cart_item) {
            OrderItem::insert([
                'order_id' => $order_id,
                'product_id' => $cart_item['id'],
                'product_unit_id' => $cart_item['product_unit_id'] ?? null,
                'client_id' => $cart_item['client_id'],
                'qty' => $cart_item['quantity'],
                'price' => $cart_item['price'],
                'created_at' => Carbon::now(),
            ]);

            $clientIds[] = $cart_item['client_id'];
            
            $product = ProductNew::with('productTemplate')->find($cart_item['id']);
            $productUnit = ProductUnit::find($cart_item['product_unit_id']);

            if ($product && $product->productTemplate && $productUnit) {
                $stockMode = $product->productTemplate->stock_mode;

                if ($stockMode === 'quantity') {
                    $productUnit->increment('sold');
                    $productUnit->decrement('batch_qty');
                } elseif ($stockMode === 'unit') {
                    $productUnit->update([
                        'is_sold_out' => true,
                        'batch_qty' => max($productUnit->batch_qty - 1, 0),
                    ]);
                }
            }

            // Giảm số lượng sản phẩm tương ứng
            // ProductNew::where('id', $cart_item['id'])->decrement('qty', $cart_item['quantity']);
            // Tăng số lượng đã bán
            ProductNew::where('id', $cart_item['id'])->increment('sold', $cart_item['quantity']);
        } // end foreach

        if (Session::has('coupon')) {
            Session::forget('coupon');
        } 
        
        if (Session::has('cart')) {
            Session::forget('cart');
        } 

        if (Session::has('shipping_fee')) {
            Session::forget('shipping_fee');
        } 

        $user = Auth::guard('web')->user();
        Notification::send($user, new OrderPlaced($invoice));

        Notification::send($admin, new OrderComplete($request->name));
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
                        ->with('user', 'OrderItems')
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
        
        $user = $order->user;
        if ($user) {
            Notification::send($user, new OrderCancelRequested($order->invoice_no));
        }

        $firstOrderItem = $order->OrderItems->first();
        if ($firstOrderItem) {
            $client = Client::where('id', $firstOrderItem->client_id)
                        ->first();
            Notification::send($client, new OrderCancelRequestedByUser($order->invoice_no, $order->cancel_reason));
        }

        return redirect()->route('user.order.list')->with('success', 'Huỷ đơn hàng thành công.');
    }

    public function ReportOrder(Request $request, Order $order)
    {
        $user = Auth::guard('web')->user();
        $order = Order::with('orderItems')->find($order->id);
        $clientId = $order->orderItems->first()->client_id ?? null;

        if ($order->status !== 'delivered') {
            return redirect()->back()->with('error', 'Chỉ có thể báo cáo với đơn hàng đã hoàn tất.');
        }

        $existingReport = OrderReport::where('order_id', $order->id)->first();
        if ($existingReport) {
            return redirect()->back()->with('error', 'Đơn hàng này đã có báo cáo, không thể báo cáo thêm.');
        }

        $request->validate([
            'report_content' => 'required|string|max:1000',
        ]);

        OrderReport::create([
            'order_id' => $order->id,
            'client_id' => $clientId, 
            'issue_type' => $request->issue_type,
            'content' => $request->report_content,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Báo cáo của bạn đã được gửi. Chúng tôi sẽ xử lý sớm nhất.');
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

    public function ClientMarkAsRead(Request $request, $notificationId){
        $user = Auth::guard('client')->user();
        $notification = $user->notifications()->where('id',$notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['count' => $user->unreadNotifications()->count()]);
    }
    //End Method 

    public function UserMarkAsRead(Request $request, $notificationId){
        $user = Auth::guard('web')->user();
        $notification = $user->notifications()->where('id',$notificationId)->first();

        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['count' => $user->unreadNotifications()->count()]);
    }
    //End Method 
}
