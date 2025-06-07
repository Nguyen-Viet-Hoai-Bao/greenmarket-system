<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;
use Haruncpi\LaravelIdGenerator\IdGenerator;

use App\Models\Coupon;
use App\Models\AdminWallet;

class CouponController extends Controller
{
    public function AllCoupon(){
        $client_id = Auth::guard('client')->id();
        $coupon = Coupon::where('client_id', $client_id)->latest()->get();
        return view('client.backend.coupon.all_coupon', compact('coupon'));
    } 
    //End Method

    public function AddCoupon(){

        return view('client.backend.coupon.add_coupon');
    } 
    //End Method

    public function StoreCoupon(Request $request) {
        
        Coupon::create([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_desc' => $request->coupon_desc,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'client_id' => Auth::guard('client')->id(),
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Insert Coupon Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.coupon')->with($notification);
    }
    // End Method

    public function EditCoupon($id) {
        $coupon = Coupon::find($id);
        return view('client.backend.coupon.edit_coupon', compact('coupon'));

    }
    // End Method
    
    public function UpdateCoupon(Request $request) {
        $cou_id = $request->id;
        
        Coupon::find($cou_id)->update([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_desc' => $request->coupon_desc,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'client_id' => Auth::guard('client')->id(),
            'created_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Update Coupon Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.coupon')->with($notification);
    }
    // End Method

    public function DeleteCoupon($id) {
        $item = Coupon::find($id);

        Coupon::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Coupon Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method


    //////////////////// ADMIN COUPON ////////////////////
    public function AdminAllCoupon(){
        // Ví admin
        $wallets = AdminWallet::orderBy('created_at', 'desc')->get();
        $latest = AdminWallet::latest()->first();
        $totalIncome = $latest?->total_income ?? 0;
        $totalExpense = $latest?->total_expense ?? 0;
        $balance = $latest?->balance ?? 0;

        $coupon = Coupon::where('client_id', 0)->latest()->get();
        return view('admin.backend.coupon.all_coupon', compact('coupon', 'totalIncome', 'totalExpense', 'balance'));
    } 
    //End Method

    public function AdminAddCoupon(){
        // Ví admin
        $wallets = AdminWallet::orderBy('created_at', 'desc')->get();
        $latest = AdminWallet::latest()->first();
        $totalIncome = $latest?->total_income ?? 0;
        $totalExpense = $latest?->total_expense ?? 0;
        $balance = $latest?->balance ?? 0;

        return view('admin.backend.coupon.add_coupon', compact('totalIncome', 'totalExpense', 'balance'));
    } 
    //End Method

    public function AdminStoreCoupon(Request $request) {
        $quantity = $request->quantity;
        $max_discount_amount = $request->max_discount_amount;

        $adminWallet = AdminWallet::latest()->first();
        $balance = $adminWallet ? $adminWallet->balance : 0;

        $totalRequired = $quantity * $max_discount_amount;

        if ($balance < $totalRequired) {
            $notification = array(
                'message' => 'Số dư ví admin không đủ để tạo mã giảm giá với số lượng và giới hạn này',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }

        $save_url = null;
        if ($request->file('image')) {
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/coupon_images/' . $name_gen));
            $save_url = 'upload/coupon_images/' . $name_gen;
        }

        Coupon::create([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_desc' => $request->coupon_desc,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'quantity' => $quantity,
            'quantity_apply' => $quantity,
            'max_discount_amount' => $max_discount_amount,
            'client_id' => 0,
            'image_path' => $save_url,
            'created_at' => Carbon::now(),
        ]);
        
        AdminWallet::create([
            'type' => 'expense',
            'amount' => $totalRequired,
            'description' => 'Chi phí tạo coupon ' . strtoupper($request->coupon_name),
            'total_income' => $adminWallet ? $adminWallet->total_income : 0,
            'total_expense' => ($adminWallet ? $adminWallet->total_expense : 0) + $totalRequired,
            'balance' => $balance - $totalRequired,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Thêm mã giảm giá thành công',
            'alert-type' => 'success'
        );

        return redirect()->route('admin.all.coupon')->with($notification);
    }

    public function AdminEditCoupon($id) {
        // Ví admin
        $wallets = AdminWallet::orderBy('created_at', 'desc')->get();
        $latest = AdminWallet::latest()->first();
        $totalIncome = $latest?->total_income ?? 0;
        $totalExpense = $latest?->total_expense ?? 0;
        $balance = $latest?->balance ?? 0;

        $coupon = Coupon::find($id);
        return view('admin.backend.coupon.edit_coupon', compact('coupon', 'totalIncome', 'totalExpense', 'balance'));
    }
    // End Method
    
    public function AdminUpdateCoupon(Request $request) {
        $cou_id = $request->id;
        $newQuantity = $request->quantity;
        $newMaxDiscountAmount = $request->max_discount_amount;

        $coupon = Coupon::find($cou_id);
        if (!$coupon) {
            // xử lý coupon không tồn tại
            return redirect()->back()->with([
                'message' => 'Mã giảm giá không tồn tại',
                'alert-type' => 'error'
            ]);
        }

        $oldTotalCommit = $coupon->quantity * $coupon->max_discount_amount;
        $newTotalCommit = $newQuantity * $newMaxDiscountAmount;

        $adminWallet = AdminWallet::latest()->first();
        $balance = $adminWallet ? $adminWallet->balance : 0;
        $totalIncome = $adminWallet ? $adminWallet->total_income : 0;
        $totalExpense = $adminWallet ? $adminWallet->total_expense : 0;

        // Tính phần chênh lệch cam kết
        $difference = $newTotalCommit - $oldTotalCommit;

        if ($difference > 0) {
            // Cam kết tăng → kiểm tra ví admin còn đủ tiền không
            if ($balance < $difference) {
                return redirect()->back()->with([
                    'message' => 'Số dư ví admin không đủ để tăng giới hạn mã giảm giá',
                    'alert-type' => 'error'
                ]);
            }
            // Trừ tiền ví admin (chi tiêu)
            AdminWallet::create([
                'type' => 'expense',
                'amount' => $difference,
                'description' => 'Tăng chi phí cập nhật coupon ' . strtoupper($coupon->coupon_name),
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense + $difference,
                'balance' => $balance - $difference,
                'created_at' => Carbon::now(),
            ]);
        } else if ($difference < 0) {
            // Cam kết giảm → hoàn trả tiền (thu nhập)
            $refund = abs($difference);
            $latestWallet = AdminWallet::latest()->first();
            $totalIncome = $latestWallet ? $latestWallet->total_income : 0;
            $totalExpense = $latestWallet ? $latestWallet->total_expense : 0;
            $balance = $latestWallet ? $latestWallet->balance : 0;
            
            AdminWallet::create([
                'type' => 'income',
                'amount' => $refund,
                'description' => 'Hoàn trả chi phí cập nhật coupon ' . strtoupper($coupon->coupon_name),
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense - $refund,
                'balance' => $balance + $refund,
                'created_at' => Carbon::now(),
            ]);
        }

        $save_url = $coupon->image_path;
        if ($request->file('image')) {
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/coupon_images/' . $name_gen));
            $save_url = 'upload/coupon_images/' . $name_gen;
        }

        $coupon->update([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_desc' => $request->coupon_desc,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'quantity' => $newQuantity,
            'quantity_apply' => max(0, $newQuantity - ($coupon->quantity - $coupon->quantity_apply)),
            'max_discount_amount' => $newMaxDiscountAmount,
            'client_id' => 0,
            'image_path' => $save_url,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.all.coupon')->with([
            'message' => 'Cập nhật mã giảm giá thành công',
            'alert-type' => 'success'
        ]);
    }
    // End Method

    public function AdminDeleteCoupon($id) {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return redirect()->back()->with([
                'message' => 'Mã giảm giá không tồn tại',
                'alert-type' => 'error'
            ]);
        }

        if ($coupon->quantity_apply < $coupon->quantity) {
            return redirect()->back()->with([
                'message' => 'Không thể xóa mã giảm giá đã được sử dụng',
                'alert-type' => 'error'
            ]);
        }

        $refundAmount = $coupon->quantity_apply * $coupon->max_discount_amount;

        if ($refundAmount > 0) {
            $latestWallet = AdminWallet::latest()->first();
            $totalIncome = $latestWallet ? $latestWallet->total_income : 0;
            $totalExpense = $latestWallet ? $latestWallet->total_expense : 0;
            $balance = $latestWallet ? $latestWallet->balance : 0;

            AdminWallet::create([
                'type' => 'income',
                'amount' => $refundAmount,
                'description' => 'Hoàn tiền khi xóa coupon ' . strtoupper($coupon->coupon_name),
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense - $refundAmount,
                'balance' => $balance + $refundAmount,
                'created_at' => Carbon::now(),
            ]);
        }

        // Xóa coupon
        $coupon->delete();

        return redirect()->back()->with([
            'message' => 'Xóa mã giảm giá thành công',
            'alert-type' => 'success'
        ]);
    }


}
