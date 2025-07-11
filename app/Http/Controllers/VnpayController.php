<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

use App\Notifications\OrderComplete;
use App\Notifications\OrderPlaced;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Admin;
use App\Models\Client;
use App\Models\ProductNew;

class VnpayController extends Controller
{
    public function create(Request $request)
    {
        // $this->vnpayOrder($request);
        session([
            'checkout_data' => $request->only(['name', 'email', 'address', 'phone', 'locality_code'])
        ]);

        $vnp_TmnCode = "MDAVGEKQ";
        $vnp_HashSecret = "28DK10IJECY00B6GNI3RJPP91H6VIACG";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('return-vnpay');

        $vnp_TxnRef = date("YmdHis");
        $vnp_OrderInfo = "Thanh toán hóa đơn";
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->input('amount') * 100; // *100 do VNPAY yêu cầu
        $vnp_Locale = 'vn';
        // $vnp_IpAddr = request()->ip();
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        ];

        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

    public function return(Request $request)
    {
        if ($request->vnp_ResponseCode == "00") {
            // Lấy lại dữ liệu từ session
            $checkoutData = session('checkout_data');

            // Merge vào request để truyền vào vnpayOrder
            $mergedRequest = new Request(array_merge($checkoutData, $request->all()));

            $this->vnpayOrder($mergedRequest);

            return redirect('/checkout/thanks')->with('success', 'Thanh toán thành công!');
        }
        return redirect('/')->with('error', 'Thanh toán thất bại!');
    }

    // public function return(Request $request)
    // {
    //     // Check VNPay Response
    //     if ($request->vnp_ResponseCode == "00") {
    //         // Payment successful, now save order and other info
    //         return $this->vnpayOrder($request);
    //     }

    //     // Payment failed
    //     return redirect('/')->with('error', 'Thanh toán thất bại!');
    // }

    public function vnpayOrder(Request $request)
    {
        $user = Admin::where('role', 'admin')->get();

        // Order validation
        $validateData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'locality_code' => 'required',
        ]);

        $clientIds = []; // các client_id của order items

        // Calculate total amount from cart
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

        // Generate unique invoice number
        do {
            $invoice = 'Green' . mt_rand(10000000, 99999999);
            $exists = Order::where('invoice_no', $invoice)->exists(); // Ensure no duplicate invoice
        } while ($exists);

        // Insert Order
        $order_id = Order::insertGetId([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'ward_id' => $request->locality_code,
            'address' => $request->address,
            'payment_type' => 'Đã thanh toán bằng VNPay',
            'payment_method' => 'Đã thanh toán bằng VNPay',
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

        // Insert Order Items
        foreach (session()->get('cart', []) as $cart_item) {
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
        }

        // Clear session data after order is created
        session()->forget(['coupon', 'cart', 'shipping_fee']);

        $user_order = Auth::guard('web')->user();
        Notification::send($user_order, new OrderPlaced($invoice));

        Notification::send($user, new OrderComplete($request->name));
        $clients = Client::whereIn('id', array_unique($clientIds))->get();
        Notification::send($clients, new OrderComplete($request->name));
        
        // Show confirmation message to the user
        $notification = [
            'message' => 'Đặt hàng thành công',
            'alert-type' => 'success'
        ];
        return view('frontend.checkout.thanks')->with($notification);
    }


}
















    // public function create(Request $request)
    // {
    //     $vnp_TmnCode = "MDAVGEKQ";
    //     $vnp_HashSecret = "28DK10IJECY00B6GNI3RJPP91H6VIACG";
    //     $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    //     $vnp_Returnurl = route('return-vnpay');

    //     $vnp_TxnRef = date("YmdHis");
    //     $vnp_OrderInfo = "Thanh toán hóa đơn";
    //     $vnp_OrderType = 'billpayment';
    //     $vnp_Amount = $request->input('amount') * 100; // *100 do VNPAY yêu cầu
    //     $vnp_Locale = 'vn';
    //     $vnp_IpAddr = request()->ip();

    //     $inputData = [
    //         "vnp_Version" => "2.0.0",
    //         "vnp_TmnCode" => $vnp_TmnCode,
    //         "vnp_Amount" => $vnp_Amount,
    //         "vnp_Command" => "pay",
    //         "vnp_CreateDate" => date('YmdHis'),
    //         "vnp_CurrCode" => "VND",
    //         "vnp_IpAddr" => $vnp_IpAddr,
    //         "vnp_Locale" => $vnp_Locale,
    //         "vnp_OrderInfo" => $vnp_OrderInfo,
    //         "vnp_OrderType" => $vnp_OrderType,
    //         "vnp_ReturnUrl" => $vnp_Returnurl,
    //         "vnp_TxnRef" => $vnp_TxnRef
    //     ];

    //     ksort($inputData);
    //     $query = "";
    //     $hashdata = "";
    //     $i = 0;
    //     foreach ($inputData as $key => $value) {
    //         if ($i == 1) {
    //             $hashdata .= '&' . $key . "=" . $value;
    //         } else {
    //             $hashdata .= $key . "=" . $value;
    //             $i = 1;
    //         }
    //         $query .= urlencode($key) . "=" . urlencode($value) . '&';
    //     }

    //     $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashdata);
    //     $vnp_Url = $vnp_Url . "?" . $query . "vnp_SecureHashType=SHA256&vnp_SecureHash=" . $vnpSecureHash;

    //     // dd([
    //     //     'vnp_HashSecret' => $vnp_HashSecret,
    //     //     'hashdata' => $hashdata,
    //     //     'vnpSecureHash' => $vnpSecureHash,
    //     //     'redirect_url' => $vnp_Url
    //     // ]);
        
    //     return redirect($vnp_Url);
    // }