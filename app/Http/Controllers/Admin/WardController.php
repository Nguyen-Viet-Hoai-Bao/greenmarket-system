<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ward;
use App\Models\District;
use Illuminate\Support\Str;

class WardController extends Controller
{
    // Tất cả phường/xã
    public function AllWard()
    {
        $ward = Ward::with('district')->latest()->get();
        $districts = District::with('city')->get();
        return view('admin.backend.ward.all_ward', compact('ward', 'districts'));
    }

    // Thêm phường/xã
    public function StoreWard(Request $request, $district_id)
    {
        Ward::create([
            'district_id' => $district_id,
            'ward_name' => $request->ward_name,
            'ward_slug' => Str::slug($request->ward_name),
        ]);

        $notification = [
            'message' => 'Create Ward Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    // Lấy thông tin để sửa
    public function EditWard($id)
    {
        $ward = Ward::find($id);
        return response()->json($ward);
    }

    // Cập nhật phường/xã
    public function UpdateWard(Request $request)
    {
        $id = $request->cat_id_2;

        Ward::find($id)->update([
            'ward_name' => $request->ward_name,
            'ward_slug' => Str::slug($request->ward_name),
        ]);

        $notification = [
            'message' => 'Update Ward Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    // Xoá phường/xã
    public function DeleteWard($id)
    {
        Ward::find($id)->delete();

        $notification = [
            'message' => 'Delete Ward Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }
}
