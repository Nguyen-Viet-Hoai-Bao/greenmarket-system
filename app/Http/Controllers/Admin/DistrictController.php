<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\District;
use App\Models\City;
use Illuminate\Support\Str;

class DistrictController extends Controller
{
    // Tất cả quận/huyện
    public function AllDistricts()
    {
        $district = District::with('city')->latest()->get();
        $city = City::latest()->get();
        return view('admin.backend.city.all_city', compact('district', 'city'));
    }

    // Thêm quận/huyện
    public function StoreDistrict(Request $request, $city_id)
    {
        District::create([
            'city_id' => $city_id,
            'district_name' => $request->district_name,
            'district_slug' => Str::slug($request->district_name),
        ]);

        $notification = [
            'message' => 'Create District Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    // Lấy thông tin để sửa
    public function EditDistrict($id)
    {
        $district = District::find($id);
        return response()->json($district);
    }

    // Cập nhật quận/huyện
    public function UpdateDistrict(Request $request)
    {
        $id = $request->cat_id_1;

        District::find($id)->update([
            'district_name' => $request->district_name,
            'district_slug' => Str::slug($request->district_name),
        ]);

        $notification = [
            'message' => 'Update District Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    // Xoá quận/huyện
    public function DeleteDistrict($id)
    {
        District::find($id)->delete();

        $notification = [
            'message' => 'Delete District Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
}
