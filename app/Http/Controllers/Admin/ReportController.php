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
use DateTime;

class ReportController extends Controller
{
    public function AdminAllReports() {
        return view('admin.backend.report.all_report');
    }
    // End Method
    
    public function AdminSearchBydate(Request $request) {
        $date = new DateTime($request->date);
        
        $formatDate = $date->format('d F Y');
        $orderDate = Order::where('order_date', $formatDate)
                            ->latest()
                            ->get();

        return view('admin.backend.report.search_by_date', compact('orderDate', 'formatDate'));
    }
    // End Method
    
    public function AdminSearchBymonth(Request $request) {
        $month = $request->month;
        $years = $request->year_name;
        
        $orderMonth = Order::where('order_month', $month)
                            ->where('order_year', $years)
                            ->latest()
                            ->get();

        return view('admin.backend.report.search_by_month', compact('orderMonth', 'month', 'years'));
    }
    // End Method
    
    public function AdminSearchByyear(Request $request) {
        $year = $request->year;
        
        $orderYear = Order::where('order_year', $year)
                            ->latest()
                            ->get();

        return view('admin.backend.report.search_by_year', compact('orderYear', 'year'));
    }
    // End Method
    


    //////////////////// CLIENT REPORT ////////////////////
    public function ClientAllReports() {
        return view('client.backend.report.all_report');
    }
    // End Method
    
    public function ClientSearchBydate(Request $request) {
        $date = new DateTime($request->date);
        $formatDate = $date->format('d F Y');

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

        return view('client.backend.report.search_by_date', compact('orderItemGroupData', 'formatDate'));
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

        return view('client.backend.report.search_by_month',compact('orderItemGroupData','month','years'));
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

        return view('client.backend.report.search_by_year',compact('orderItemGroupData','years'));
    }
      // End Method 

}
