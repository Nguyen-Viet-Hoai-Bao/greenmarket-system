<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;

class ManageOrderController extends Controller
{
    public function PendingOrder() {
        $allData = Order::where('status', 'pending')
                        ->orderBy('id', 'desc')
                        ->get();
        return view('admin.backend.order.pending_order', compact('allData'));
    }
    // End Method
    
    public function ConfirmOrder() {
        $allData = Order::where('status', 'confirm')
                        ->orderBy('id', 'desc')
                        ->get();
        return view('admin.backend.order.confirm_order', compact('allData'));
    }
    // End Method
    
    public function ProcessingOrder() {
        $allData = Order::where('status', 'processing')
                        ->orderBy('id', 'desc')
                        ->get();
        return view('admin.backend.order.processing_order', compact('allData'));
    }
    // End Method
    
    public function DeliverdOrder() {
        $allData = Order::where('status', 'deliverd')
                        ->orderBy('id', 'desc')
                        ->get();
        return view('admin.backend.order.deliverd_order', compact('allData'));
    }
    // End Method
    
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
        Order::find($id)->update(['status' => 'processing']);
        
        $notification = array(
            'message' => 'Đơn hàng đang được xử lý',
            'alert-type' => 'success'
        );
        return redirect()->route('processing.order')->with($notification);
    }
    // End Method

    public function ProcessingToDiliverd($id) {
        Order::find($id)->update(['status' => 'deliverd']);
        
        $notification = array(
            'message' => 'Đơn hàng đã được giao thành công',
            'alert-type' => 'success'
        );
        return redirect()->route('deliverd.order')->with($notification);
    }
    // End Method

    public function AllClientsOrders() {
        $clientId = Auth::guard('client')->id();
        
        $orderItemGroupData = OrderItem::with(['product', 'order'])
                                        ->where('client_id', $clientId)
                                        ->orderBy('order_id', 'desc')
                                        ->get()
                                        ->groupBy('order_id');
                        
        return view('client.backend.order.all_orders', 
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
        
        return view('client.backend.order.client_order_details', 
                    compact('order', 'orderItem', 'totalPrice', 'totalAmount'));
    }
    // End Method

    public function UserOrderList() {
        $userId = Auth::user()->id;
        
        $allUserOrder = Order::where('user_id', $userId)
                            ->orderBy('id', 'desc')
                            ->get();
                        
        return view('frontend.dashboard.order.order_list', 
                    compact('allUserOrder'));
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

        return view('frontend.dashboard.order.order_details',compact('order','orderItem','totalPrice', 'totalAmount'));
    }
     //End Method 
     
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
