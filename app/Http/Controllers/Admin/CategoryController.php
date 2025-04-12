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

class CategoryController extends Controller
{
    public function AllCategory() {
        $category = Category::latest()->get();
        return view('admin.backend.category.all_category', compact('category'));
    }
    // End Method

    public function AddCategory() {
        return view('admin.backend.category.add_category');
        
    }
    // End Method

    public function StoreCategory(Request $request) {
        if($request->file('image')){
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/category/'
                .$name_gen));
            $save_url = 'upload/category/'.$name_gen;

            Category::create([
                'category_name' => $request->category_name,
                'image' => $save_url,
            ]);

        }
        
        $notification = array(
            'message' => 'Create Category Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.category')->with($notification);
    }
    // End Method

    public function EditCategory($id) {
        $category = Category::find($id);
        return view('admin.backend.category.edit_category', compact('category'));

    }
    // End Method
    
    public function UpdateCategory(Request $request) {
        $cat_id = $request->id;

        if($request->file('image')){
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/category/'
                .$name_gen));
            $save_url = 'upload/category/'.$name_gen;

            Category::find($cat_id)->update([
                'category_name' => $request->category_name,
                'image' => $save_url,
            ]);

            $notification = array(
                'message' => 'Update Category Successfully',
                'alert-type' => 'success'
            );
        } else {
            Category::find($cat_id)->update([
                'category_name' => $request->category_name,
            ]);
            $notification = array(
                'message' => 'Update Category Successfully',
                'alert-type' => 'success'
            );
        }
        

        return redirect()->route('all.category')->with($notification);
    }
    // End Method

    public function DeleteCategory($id) {
        $item = Category::find($id);
        $img = $item->image;
        unlink($img);

        Category::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Category Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method

    /// ALL City Method 
    public function AllCity() {
        $city = City::latest()->get();
        return view('admin.backend.city.all_city', compact('city'));
    }
    // End Method

    public function StoreCity(Request $request) {
        City::create([
            'city_name' => $request->city_name,
            'city_slug' => strtolower(str_replace(' ', '-', $request->city_name)),
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
            'city_slug' => strtolower(str_replace(' ', '-', $request->city_name)),
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
