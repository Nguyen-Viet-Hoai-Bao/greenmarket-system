<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Menu;
use App\Models\City;
use App\Models\Ward;
use App\Models\ProductNew;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function Index() {
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
        $cities = City::all();
        $menus = Menu::all();

        $fullAddress = null;
        if (session()->has('selected_market_ward_id')) {
            $ward = Ward::with('district.city')->find(session('selected_market_ward_id'));
        
            if ($ward && $ward->district && $ward->district->city) {
                $fullAddress = $ward->ward_name . ', ' 
                             . $ward->district->district_name . ', ' 
                             . $ward->district->city->city_name;
            }
        }
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

        return view('frontend.index', compact('products_list', 'cities', 'fullAddress', 'menus', 'menus_footer', 'products_list'));
    }
    // End Method
    
    public function ProfileStore(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::find($id);

        // Nếu email thay đổi, reset xác minh
        if ($data->email !== $request->email) {
            $data->email_verified_at = null;
        }

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        $oldPhotoPath = $data->photo;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/user_images'), $filename);
            $data->photo = $filename;

            if ($oldPhotoPath && $oldPhotoPath !== $filename) {
                $this->deleteOldImage($oldPhotoPath);
            }
        }

        $data->save();

        $notification = [
            'message' => 'Profile Update Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }
    //End Method
    
    private function deleteOldImage(string $oldPhotoPath) : void {
        $fullPath = public_path('upload/user_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
    //End Private Method

    public function UserLogout() {
        Auth::guard('web')->logout();
        return redirect()->route('login')->with('success', 'Logout Successfully');
    }
    //End Method

    public function ChangePassword() {
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
        return view('frontend.dashboard.change_password', compact('cities', 'menus_footer', 'products_list'));        
    }
    //End Method
    
    
    public function UserPasswordUpdate(Request $request) {
        $user = Auth::guard('web')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            $notification = array(
                'message' => 'Mật khẩu cũ không khớp!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        // Update new password
        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        $notification = array(
            'message' => 'Thay đổi mật khẩu thành công!',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }
    //End Method

}
