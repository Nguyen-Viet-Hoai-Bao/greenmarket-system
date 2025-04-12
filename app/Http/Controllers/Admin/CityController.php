<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Category;
use App\Models\City;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;
use Illuminate\Support\Str;

class CityController extends Controller
{
    /// ALL City Method 
    public function AllCity() {
        $city = City::latest()->get();
        return view('admin.backend.city.all_city', compact('city'));
    }
    // End Method

    public function StoreCity(Request $request) {
        City::create([
            'city_name' => $request->city_name,
            'city_slug' => Str::slug($request->city_name),
        ]);
        
        $notification = array(
            'message' => 'Create City Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
    // End Method

    public function EditCity($id) {
        $city = City::find($id);
        return response()->json($city);
    }
    // End Method

    public function UpdateCity(Request $request) {

        $cat_id = $request->cat_id;

        City::find($cat_id)->update([
                'city_name' => $request->city_name,
                'city_slug' => Str::slug($request->city_name),
        ]);
        
        $notification = array(
            'message' => 'Update City Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
    // End Method

    public function DeleteCity($id) {
        City::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete City Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    
    }
    // End Method

}
