<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\Websitemail;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Cloudinary\Api\Upload\UploadApi;

class AdminController extends Controller
{
    public function AdminLogin() {
        return view('admin.login');
    }
    //End Method

    public function AdminDashboard(Request $request)
    {
        $branchId = $request->input('branch_id'); // Nếu muốn lọc theo chi nhánh

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $endOfThisWeek = Carbon::now()->endOfWeek();

        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        // Hàm tính toán số liệu theo thời gian và chi nhánh (nếu có)
        $calculateStats = function ($startDate, $endDate) use ($branchId) {
            $orderQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

            if ($branchId) {
                $orderQuery->where('branch_id', $branchId);
            }

            $orders = $orderQuery->get();
            $orderIds = $orders->pluck('id');

            $orderItemsQuery = OrderItem::whereIn('order_id', $orderIds);

            $orderItems = $orderItemsQuery->get();

            $totalRevenue = $orders->sum('total_amount');
            $transactionCount = $orders->count();
            $investment = $orderItems->sum(function ($item) {
                return $item->qty * ($item->product->cost_price ?? 0);
            });
            $profit = $totalRevenue - $investment;
            $profitRate = $investment > 0 ? ($profit / $investment) * 100 : 0;

            return [
                'revenue' => $totalRevenue,
                'transactions' => $transactionCount,
                'investment' => $investment,
                'profit' => $profit,
                'profitRate' => $profitRate,
            ];
        };

        $calculateTotalStats = function () use ($branchId) {
            $orderQuery = Order::query();

            if ($branchId) {
                $orderQuery->where('branch_id', $branchId);
            }

            $orders = $orderQuery->get();
            $orderIds = $orders->pluck('id');

            $orderItems = OrderItem::whereIn('order_id', $orderIds)->get();

            $totalRevenue = $orders->sum('total_amount');
            $transactionCount = $orders->count();
            $investment = $orderItems->sum(function ($item) {
                return $item->qty * ($item->product->cost_price ?? 0);
            });
            $profit = $totalRevenue - $investment;
            $profitRate = $investment > 0 ? ($profit / $investment) * 100 : 0;

            return [
                'revenue' => $totalRevenue,
                'transactions' => $transactionCount,
                'investment' => $investment,
                'profit' => $profit,
                'profitRate' => $profitRate,
            ];
        };

        $currentWeek = $calculateStats($startOfThisWeek, $endOfThisWeek);
        $lastWeek = $calculateStats($startOfLastWeek, $endOfLastWeek);

        $totalStats = $calculateTotalStats();

        $diff = [
            'revenueDiff'      => $currentWeek['revenue'] - $lastWeek['revenue'],
            'transactionDiff'  => $currentWeek['transactions'] - $lastWeek['transactions'],
            'investmentDiff'   => $currentWeek['investment'] - $lastWeek['investment'],
            'profitDiff'       => $currentWeek['profit'] - $lastWeek['profit'],
            'profitRateDiff'   => $currentWeek['profitRate'] - $lastWeek['profitRate'],
        ];

        
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product.productTemplate') // eager load liên kết sâu hơn
            ->take(5)
            ->get();

        $topOrders = DB::table('orders')
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'orders.id',
                'orders.name',
                'orders.email',
                'orders.phone',
                'orders.address',
                'orders.payment_type',
                'orders.order_date',
                'orders.status',
                DB::raw('SUM(order_items.qty * order_items.price) as total_amount')
            )
            ->groupBy(
                'orders.id', 
                'orders.name', 
                'orders.email', 
                'orders.phone', 
                'orders.address', 
                'orders.payment_type', 
                'orders.order_date', 
                'orders.status'
            )
            ->orderByDesc('total_amount')
            ->take(5)
            ->get();


        return view('admin.index', [
            'totalRevenue'       => $totalStats['revenue'],
            'transactionCount'   => $totalStats['transactions'],
            'investment'         => $totalStats['investment'],
            'profit'             => $totalStats['profit'],
            'profitRate'         => $totalStats['profitRate'],

            'revenueDiff'        => $diff['revenueDiff'],
            'transactionDiff'    => $diff['transactionDiff'],
            'investmentDiff'     => $diff['investmentDiff'],
            'profitDiff'         => $diff['profitDiff'],
            'profitRateDiff'     => $diff['profitRateDiff'],

            'branchId'           => $branchId, 
            'topProducts'        => $topProducts, 
            'topOrders'          => $topOrders, 
        ]);
    }
    //End Method
    
    public function AdminLoginSubmit(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $check = $request->all();
        $data = [
            'email' => $check['email'],
            'password' => $check['password'],
        ];
        if (Auth::guard('admin')->attempt($data)) {
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công');
        }else{
            return redirect()->route('admin.login')->with('error', 'Thông tin đăng nhập không hợp lệ');
        }
    }
    //End Method
    
    public function AdminLogout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Logout Success');
    }
    //End Method

    public  function AdminForgetPassword() {
        return view('admin.forget_password');
    }
    //End Method

    public function AdminPasswordSubmit(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);

        $admin_data = Admin::where('email', $request->email)->first();
        if (!$admin_data) {
            return redirect()->back()->with('error', 'Email Not Found');
        }
        $token = hash('sha256', time());
        $admin_data->token = $token;
        $admin_data->update();

        $reset_link = url('admin/reset-password/'.$token.'/'.$request->email);
        $subject = "Reset Password";
        $message = "Please Click on below to reset password<br>";
        $message .= "<a href='".$reset_link."'>Click Here</a>";

        \Mail::to($request->email)->send(new Websitemail($subject, $message));

        return redirect()->back()->with('success', 'Reset Password Link Send On Your Email');
    }
    //End Method

    public function AdminResetPassword($token, $email) {
        $admin_data = Admin::where('email', $email)->where('token', $token)->first();
        if (!$admin_data) {
            return redirect()->route('admin.login')->with('error', 'Invalid Token or Email');
        }
        return view('admin.reset_password', compact('token', 'email'));
    }
    //End Method

    public function AdminResetPasswordSubmit(Request $request) {
        $request->validate([
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);
        $admin_data = Admin::where('email', $request->email)->where('token', $request->token)->first();
        $admin_data->password = Hash::make($request->password);
        $admin_data->token = "";
        $admin_data->update();

        return redirect()->route('admin.login')->with('success', 'Password Reset Successfully');
    }
    //End Method

    public function AdminProfile() {
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);
        return view('admin.admin_profile', compact('profileData'));
    }
    //End Method

    public function AdminProfileStore(Request $request) {
        $id = Auth::guard('admin')->id();
        $data = Admin::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        
        $oldPhotoPath = $data->photo;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $uploadApi = new UploadApi();

            try {
                $uploaded = $uploadApi->upload($file->getRealPath(), [
                    'folder' => 'admin_images',
                ]);
                $secureUrl = $uploaded['secure_url'];
                $data->photo = $secureUrl;
            } catch (\Exception $e) {
                return redirect()->back()->with([
                    'message' => 'Upload thất bại: ' . $e->getMessage(),
                    'alert-type' => 'error'
                ]);
            }
        }
        $data->save();
        $notification = array(
            'message' => 'Profile Update Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    //End Method

    private function deleteOldImage(string $oldPhotoPath) : void {
        $fullPath = public_path('upload/admin_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
        }
    }
    //End Private Method

    public function AdminChangePassword() {
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);


        return view('admin.admin_change_password', compact('profileData'));
    }
    //End Method

    public function AdminPasswordUpdate(Request $request) {
        $admin = Auth::guard('admin')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, $admin->password)) {
            $notification = array(
                'message' => 'Old Password Does Not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        // Update new password
        Admin::whereId($admin->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        $notification = array(
            'message' => 'Password Change Successfully!',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }
    //End Method
}
