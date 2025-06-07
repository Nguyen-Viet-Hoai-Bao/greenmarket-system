<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Product;
use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\AdminWallet;
use DateTime;

class ReportController extends Controller
{
    public function AdminAllReports() {
        return view('admin.backend.report.all_report');
    }
    // End Method
    
    public function AdminSearchBydate(Request $request) {
        $date = new DateTime($request->date);
        
        $formatDate = $date->format('d M Y');
        $orderDate = Order::where('order_date', $formatDate)
                            ->latest()
                            ->get();

        $totalRevenue = 0;
        $totalAmount = 0;
        $totalServiceFee = 0;

        foreach ($orderDate as $orderGroup) {
            $totalAmount += $orderGroup->total_amount;
            $totalRevenue += $orderGroup->net_revenue;
            $totalServiceFee += $orderGroup->service_fee;
        }

        return view('admin.backend.report.search_by_date', compact('orderDate', 'formatDate', 'totalServiceFee', 'totalRevenue', 'totalAmount'));
    }
    // End Method
    
    public function AdminSearchBymonth(Request $request) {
        $month = $request->month;
        $years = $request->year_name;
        
        $orderMonth = Order::where('order_month', $month)->with('OrderItems')
                            ->where('order_year', $years)
                            ->latest()
                            ->get();

        $totalRevenue = 0;
        $totalAmount = 0;
        $totalServiceFee = 0;

        foreach ($orderMonth as $orderGroup) {
            $totalAmount += $orderGroup->total_amount;
            $totalRevenue += $orderGroup->net_revenue;
            $totalServiceFee += $orderGroup->service_fee;
        }

        return view('admin.backend.report.search_by_month', compact('orderMonth', 'month', 'years', 'totalServiceFee', 'totalRevenue', 'totalAmount'));
    }
    // End Method
    
    public function AdminSearchByyear(Request $request) {
        $year = $request->year;
        
        $orderYear = Order::where('order_year', $year)
                            ->latest()
                            ->get();

        
        $totalRevenue = 0;
        $totalAmount = 0;
        $totalServiceFee = 0;

        foreach ($orderYear as $orderGroup) {
            $totalAmount += $orderGroup->total_amount;
            $totalRevenue += $orderGroup->net_revenue;
            $totalServiceFee += $orderGroup->service_fee;
        }

        return view('admin.backend.report.search_by_year', compact('orderYear', 'year', 'totalServiceFee', 'totalRevenue', 'totalAmount'));
    }
    // End Method
    
    public function AdminWalletReport()
    {
        $wallets = AdminWallet::orderBy('created_at', 'desc')->get();

        $latest = AdminWallet::latest()->first();

        $totalIncome = $latest?->total_income ?? 0;
        $totalExpense = $latest?->total_expense ?? 0;
        $balance = $latest?->balance ?? 0;

        return view('admin.backend.report.admin_wallet', compact('wallets', 'totalIncome', 'totalExpense', 'balance'));
    }


    //////////////////// CLIENT REPORT ////////////////////
    public function ClientAllReports() {
        return view('client.backend.report.all_report');
    }
    // End Method
    
    public function ClientSearchBydate(Request $request) {
        $date = new DateTime($request->date);
        $formatDate = $date->format('d M Y');

        $client_id = Auth::guard('client')->id();

        $orders = Order::where('order_date', $formatDate)
                        ->whereHas('OrderItems', function ($query) use ($client_id){
                            $query->where('client_id', $client_id);
                        })
                        ->latest()
                        ->get();
        
        $orderItemGroupData = OrderItem::with(['order', 'product'])
                                        ->whereIn('order_id', $orders->pluck('id'))
                                        ->where('client_id', $client_id)
                                        ->orderBy('order_id', 'desc')
                                        ->get()
                                        ->groupBy('order_id');

        $totalRevenue = 0;
        $totalAmount = 0;
        $totalServiceFee = 0;

        foreach ($orderItemGroupData as $orderGroup) {
            foreach ($orderGroup as $item) {
                $totalAmount += $item->order->total_amount;
                $totalRevenue += $item->order->net_revenue;
                $totalServiceFee += $item->order->service_fee;
                break; // mỗi order tính 1 lần
            }
        }

        return view('client.backend.report.search_by_date', compact('orderItemGroupData', 'formatDate', 'totalRevenue', 'totalServiceFee', 'totalAmount'));
    }
    // End Method
    
    public function ClientSearchByMonth(Request $request){
        $month = $request->month;
        $years = $request->year_name;

        $cid = Auth::guard('client')->id();

        $orders = Order::where('order_month',$month)->where('order_year',$years)
                        ->whereHas('OrderItems', function ($query) use ($cid){
                            $query->where('client_id',$cid);
                        })
                        ->latest()
                        ->get();

        $orderItemGroupData = OrderItem::with(['order','product'])
                                        ->whereIn('order_id',$orders->pluck('id'))
                                        ->where('client_id',$cid)
                                        ->orderBy('order_id','desc')
                                        ->get()
                                        ->groupBy('order_id');

        $totalRevenue = 0;
        $totalAmount = 0;
        $totalServiceFee = 0;

        foreach ($orderItemGroupData as $orderGroup) {
            foreach ($orderGroup as $item) {
                $totalAmount += $item->order->total_amount;
                $totalRevenue += $item->order->net_revenue;
                $totalServiceFee += $item->order->service_fee;
                break; // mỗi order tính 1 lần
            }
        }

        return view('client.backend.report.search_by_month',compact('orderItemGroupData','month','years', 'totalRevenue', 'totalServiceFee', 'totalAmount'));
    }
      // End Method 

      public function ClientSearchByYear(Request $request){
         
        $years = $request->year;

        $cid = Auth::guard('client')->id();

        $orders = Order::where('order_year',$years)
                        ->whereHas('OrderItems', function ($query) use ($cid){
                            $query->where('client_id',$cid);
                        })
                        ->latest()
                        ->get();

        $orderItemGroupData = OrderItem::with(['order','product'])
                                        ->whereIn('order_id',$orders->pluck('id'))
                                        ->where('client_id',$cid)
                                        ->orderBy('order_id','desc')
                                        ->get()
                                        ->groupBy('order_id');

        $totalRevenue = 0;
        $totalAmount = 0;
        $totalServiceFee = 0;

        foreach ($orderItemGroupData as $orderGroup) {
            foreach ($orderGroup as $item) {
                $totalAmount += $item->order->total_amount;
                $totalRevenue += $item->order->net_revenue;
                $totalServiceFee += $item->order->service_fee;
                break; // mỗi order tính 1 lần
            }
        }

        return view('client.backend.report.search_by_year',compact('orderItemGroupData','years', 'totalRevenue', 'totalServiceFee', 'totalAmount'));
    }
      // End Method 

}
