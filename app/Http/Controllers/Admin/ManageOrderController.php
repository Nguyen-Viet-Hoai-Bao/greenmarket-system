<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderProcessing;
use App\Notifications\OrderDelivered;
use App\Notifications\OrderCancelledBySystem;
use App\Notifications\OrderCancelled;

use App\Models\ProductNew;
use App\Models\City;
use App\Models\Menu;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\AdminWallet;
use Illuminate\Support\Facades\DB;

class ManageOrderController extends Controller
{
    // public function PendingOrder() {
    //     $clientId = Auth::guard('client')->id();

    //     $allData = Order::where('status', 'pending')
    //                     ->whereHas('OrderItems', function($query) use ($clientId) {
    //                         $query->where('client_id', $clientId);
    //                     })
    //                     ->orderBy('id', 'desc')
    //                     ->get();

    //     return view('client.backend.order.pending_order', compact('allData'));
    // }

    public function ConfirmOrder() {
        $clientId = Auth::guard('client')->id();

        $allData = Order::where('status', 'confirm')
                        ->whereHas('OrderItems', function($query) use ($clientId) {
                            $query->where('client_id', $clientId);
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        return view('client.backend.order.confirm_order', compact('allData'));
    }

    public function ProcessingOrder() {
        $clientId = Auth::guard('client')->id();

        $allData = Order::where('status', 'processing')
                        ->whereHas('OrderItems', function($query) use ($clientId) {
                            $query->where('client_id', $clientId);
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        return view('client.backend.order.processing_order', compact('allData'));
    }

    public function DeliveredOrder() {
        $clientId = Auth::guard('client')->id();

        $allData = Order::where('status', 'delivered')
                        ->whereHas('OrderItems', function($query) use ($clientId) {
                            $query->where('client_id', $clientId);
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        return view('client.backend.order.delivered_order', compact('allData'));
    }

    public function CancelPendingOrder() {
        $clientId = Auth::guard('client')->id();

        $allData = Order::where('status', 'cancel_pending')
                        ->whereHas('OrderItems', function($query) use ($clientId) {
                            $query->where('client_id', $clientId);
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        return view('client.backend.order.cancel_pending_order', compact('allData'));
    }

    public function CancelledOrder() {
        $clientId = Auth::guard('client')->id();

        $allData = Order::where('status', 'cancelled')
                        ->whereHas('OrderItems', function($query) use ($clientId) {
                            $query->where('client_id', $clientId);
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        return view('client.backend.order.cancelled_order', compact('allData'));
    }
    
    public function AdminOrderDetails($id) {
        $order = Order::with('user')
                        ->where('id', $id)
                        ->first();
        $orderItem = OrderItem::with('product')
                        ->where('order_id', $id)
                        ->orderBy('id', 'desc')
                        ->get();

        $totalAmount = $order->total_amount;
        
        $totalPrice = 0;
        foreach ($orderItem as $item) {
            $totalPrice += $item->price * $item->qty;
        }
        
        return view('admin.backend.order.admin_order_details', 
                    compact('order', 'orderItem', 'totalPrice', 'totalAmount'));
    }
    // End Method

    public function PeningToConfirm($id) {
        Order::find($id)->update(['status' => 'confirm']);
        
        $notification = array(
            'message' => 'Xác nhận đơn hàng thành công',
            'alert-type' => 'success'
        );
        return redirect()->route('confirm.order')->with($notification);
    }
    // End Method

    public function ConfirmToProcessing($id) {
        $order = Order::with('user')->find($id);
        if (!$order) {
            return redirect()->back()->withErrors('Không tìm thấy đơn hàng.');
        }

        $order->update(['status' => 'processing']);

        $user = $order->user;
        if ($user) {
            Notification::send($user, new OrderProcessing($order->invoice_no));
        }

        $notification = [
            'message' => 'Đơn hàng đang được xử lý',
            'alert-type' => 'success',
        ];

        return redirect()->route('processing.order')->with($notification);
    }
    // End Method

    public function ProcessingToDiliverd($id)
    {
        $order = Order::findOrFail($id);

        $order->update(['status' => 'delivered']);

        $serviceFee = $order->service_fee ?? 0;

        if ($serviceFee > 0) {
            $latestWallet = AdminWallet::latest()->first();

            $totalIncome = ($latestWallet->total_income ?? 0) + $serviceFee;
            $totalExpense = $latestWallet->total_expense ?? 0;
            $balance = $totalIncome - $totalExpense;

            AdminWallet::create([
                'type' => 'income',
                'amount' => $serviceFee,
                'description' => 'Phí dịch vụ từ đơn hàng #' . $order->invoice_no,
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'balance' => $balance,
            ]);
        }

        $user = $order->user;
        if ($user) {
            Notification::send($user, new OrderDelivered($order->invoice_no));
        }

        $notification = [
            'message' => 'Đơn hàng đã được giao thành công',
            'alert-type' => 'success'
        ];

        return redirect()->route('delivered.order')->with($notification);
    }

    public function AllOrders() {
        $orderItemGroupData = OrderItem::with(['product', 'order'])
                                        ->orderBy('order_id', 'desc')
                                        ->get()
                                        ->groupBy('order_id');
        return view('admin.backend.order.all_orders', 
                    compact('orderItemGroupData'));
    }
    // End Method
    
    public function ClientOrderDetails($id) {
        $clientId = Auth::guard('client')->id();

        $order = Order::with('user')
                        ->where('id', $id)
                        ->first();
        $orderItem = OrderItem::with('product')
                        ->where('order_id', $id)
                        ->where('client_id', $clientId)
                        ->orderBy('id', 'desc')
                        ->get();

        $totalAmount = $order->total_amount;
        
        $totalPrice = 0;
        foreach ($orderItem as $item) {
            $totalPrice += $item->price * $item->qty;
        }
        
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
        
        return view('client.backend.order.client_order_details', 
                    compact('order', 'orderItem', 'totalPrice', 'totalAmount', 'cities', 'menus_footer', 'products_list'));
    }
    // End Method

    public function UserOrderList() {
        $userId = Auth::user()->id;
        
        $allUserOrder = Order::where('user_id', $userId)
                            ->orderBy('id', 'desc')
                            ->get();

        
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
                        
        return view('frontend.dashboard.order.order_list', 
                    compact('allUserOrder', 'cities', 'menus_footer', 'products_list'));
    }
    // End Method
     
    public function UserOrderDetails($id){
        $order = Order::with('user')
                        ->where('id', $id)
                        ->where('user_id', Auth::id())
                        ->first();

        $orderItem = OrderItem::with('product')
                       ->where('order_id', $id)
                       ->orderBy('id', 'desc')
                       ->get();

        $totalAmount = $order->total_amount;

        $totalPrice = 0;
        foreach($orderItem as $item){
            $totalPrice += $item->price * $item->qty;
        }

        
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
                        
        return view('frontend.dashboard.order.order_details',compact('order','orderItem','totalPrice', 'totalAmount', 'cities', 'menus_footer', 'products_list'));
    }
     //End Method 

    public function CancelOrderByClient(Request $request,$id)
    {
        $order = Order::with('user')->find($id);

        if (!$order || $order->status == 'delivered') {
            return redirect()->back()->with('error', 'Không thể huỷ đơn đã giao.');
        }

        $order->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->cancel_reason
                                ? 'CỬA HÀNG HỦY: ' . $request->cancel_reason
                                : 'CỬA HÀNG HỦY do lý do đặc biệt',

        ]);
        
        $user = $order->user;
        if ($user) {
            Notification::send($user, new OrderCancelledBySystem($order->invoice_no, $order->cancel_reason));
        }
        
        return redirect()->back()->with('success', 'Đã huỷ đơn hàng thành công.');
    }

    public function CancelPendingOrderByClient($id)
    {
        $order = Order::with('user')->find($id);
        if (!$order) {
            return redirect()->back()->withErrors('Không tìm thấy đơn hàng.');
        }

        $order->update([
            'status' => 'cancelled',
        ]);
        
        $user = $order->user;
        if ($user) {
            Notification::send($user, new OrderCancelledBySystem($order->invoice_no, $order->cancel_reason));
        }

        return redirect()->back()->with('success', 'Đã huỷ đơn hàng thành công.');
    }
    // End Method
     
    public function UserInvoiceDownload($id){
        $order = Order::with('user')
                        ->where('id', $id)
                        ->where('user_id', Auth::id())
                        ->first();

        $orderItem = OrderItem::with('product')
                       ->where('order_id', $id)
                       ->orderBy('id', 'desc')
                       ->get();

        $totalAmount = $order->total_amount;

        $totalPrice = 0;
        foreach($orderItem as $item){
            $totalPrice += $item->price * $item->qty;
        }

        $discountAmount = $totalPrice - $totalAmount; 
        $discountPercent = 0;

        if ($totalPrice > 0) {
            $discountPercent = ($discountAmount / $totalPrice) * 100; 
        }

        $pdf = Pdf::loadView('frontend.dashboard.order.invoice_download', compact('order', 'orderItem', 'totalPrice', 'totalAmount', 'discountAmount', 'discountPercent'))
                    ->setPaper('a4')
                    ->setOption([
                        'tempDir' => public_path(),
                        'chroot' => public_path(),
                    ]);
        
        return $pdf->download('invoice.pdf');

    }
     //End Method 
}
