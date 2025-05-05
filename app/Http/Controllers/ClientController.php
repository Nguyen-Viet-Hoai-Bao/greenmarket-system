<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;

class ClientController extends Controller
{
    //
    public function ClientLogin() {
        return view('client.client_login');
    }
    //End Method

    public function ClientRegister() {
        return view('client.client_register');
    }
    //End Method

    public function ClientRegisterSubmit(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'string', 'unique:clients'
            ]
        ]);
        Client::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'ward_id' => $request->ward_id,
            'password' => Hash::make($request->password),
            'role' => 'client',
            'status' => '0',
        ]);

        $notification = array(
            'message' => 'Client Register Successfully!',
            'alert-type' => 'success'
        );

        return redirect()->route('client.login')->with($notification);
    }
    //End Method
    
    public function ClientLoginSubmit(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $check = $request->all();
        $data = [
            'email' => $check['email'],
            'password' => $check['password'],
        ];
        if (Auth::guard('client')->attempt($data)) {
            return redirect()->route('client.dashboard')->with('success', 'Login Successfully');
        }else{
            return redirect()->route('client.login')->with('error', 'Invalid Creadentials');
        }
    }
    //End Method

    public function ClientDashboard() {
        return view('client.index');
    }
    //End Method
    
    public function ClientLogout() {
        Auth::guard('client')->logout();
        return redirect()->route('client.login')->with('success', 'Logout Success');
    }
    //End Method
    
    public function ClientProfile() {
        $city = City::latest()->get();
        $id = Auth::guard('client')->id();
        $profileData = Client::with('ward.district.city')->find($id);
        return view('client.client_profile', compact('profileData', 'city'));
    }
    //End Method
    
    public function ClientProfileStore(Request $request) {
        $id = Auth::guard('client')->id();
        $data = Client::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->ward_id = $request->ward_id;
        $data->city_id = $request->city_id;
        $data->shop_info = $request->shop_info;
        $data->cover_photo = $request->cover_photo;
        
        $oldPhotoPath = $data->photo;

        if($request->hasFile('photo')){
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/client_images'), $filename);
            $data->photo = $filename;

            if ($oldPhotoPath && $oldPhotoPath !== $filename) {
                $this->deleteOldImage($oldPhotoPath);
            }
        }
        
        if($request->hasFile('cover_photo')){
            $file1 = $request->file('cover_photo');
            $filename1 = time().'.'.$file1->getClientOriginalExtension();
            $file1->move(public_path('upload/client_images'), $filename1);
            $data->cover_photo = $filename1;
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
        $fullPath = public_path('upload/client_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
    //End Private Method

    public function ClientChangePassword() {
        $id = Auth::guard('client')->id();
        $profileData = Client::find($id);


        return view('client.client_change_password', compact('profileData'));
    }
    //End Method
    
    public function ClientPasswordUpdate(Request $request) {
        $client = Auth::guard('client')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, $client->password)) {
            $notification = array(
                'message' => 'Old Password Does Not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        // Update new password
        Client::whereId($client->id)->update([
            'password' => Hash::make($request->new_password)
        ]);
        $notification = array(
            'message' => 'Password Change Successfully!',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }
    //End Method


    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . 
            $this->ward?->ward_name . ', ' . 
            $this->district?->district_name . ', ' . 
            $this->city?->city_name;
    }

    public function GetDistrictAjax($city_id)
    {
        $district = District::where('city_id', $city_id)->orderBy('district_name', 'ASC')->get();
        return response()->json($district);
    }

    public function GetWardAjax($district_id)
    {
        $ward = Ward::where('district_id', $district_id)->orderBy('ward_name', 'ASC')->get();
        return response()->json($ward);
    }
}
