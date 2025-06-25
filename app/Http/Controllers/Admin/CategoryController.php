<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Category;
use App\Models\City;
use App\Models\Menu;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;
use Illuminate\Support\Str;
use Cloudinary\Api\Upload\UploadApi;

class CategoryController extends Controller
{
    public function AllCategory() {
        $category = Category::with('menu')->latest()->get();
        return view('admin.backend.category.all_category', compact('category'));
    }
    // End Method

    public function AddCategory() {
        $menus = Menu::all();
        return view('admin.backend.category.add_category', compact('menus'));
        
    }
    // End Method

    public function StoreCategory(Request $request) {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'menu_id' => 'required|exists:menus,id',
            'image' => 'nullable|image'
        ]);

        $data = [
            'category_name' => $request->category_name,
            'menu_id' => $request->menu_id,
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $upload = (new UploadApi())->upload($file->getRealPath(), [
                'folder' => 'category_images'
            ]);
            $data['image'] = $upload['secure_url'];
        }

        Category::create($data);

        return redirect()->route('all.category')->with([
            'message' => 'Create Category Successfully',
            'alert-type' => 'success'
        ]);
    }
    // End Method

    public function EditCategory($id) {
        $category = Category::find($id);
        $menus = Menu::all();
        return view('admin.backend.category.edit_category', compact('category', 'menus'));

    }
    // End Method
    
    public function UpdateCategory(Request $request) {
        $cat_id = $request->id;

        $data = [
            'category_name' => $request->category_name,
            'menu_id' => $request->menu_id,
        ];

        if ($request->file('image')) {
            $file = $request->file('image');
            $upload = (new UploadApi())->upload($file->getRealPath(), [
                'folder' => 'category_images'
            ]);
            $data['image'] = $upload['secure_url'];
        }

        Category::find($cat_id)->update($data);

        return redirect()->route('all.category')->with([
            'message' => 'Update Category Successfully',
            'alert-type' => 'success'
        ]);
    }
    // End Method

    public function DeleteCategory($id) {
        Category::find($id)->delete();

        return redirect()->back()->with([
            'message' => 'Delete Category Successfully',
            'alert-type' => 'success'
        ]);
    }
    // End Method

}
