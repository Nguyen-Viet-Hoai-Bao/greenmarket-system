<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Menu;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductNew;
use App\Models\ProductTemplate;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MarketController extends Controller
{
    // public function AllMenu() {
    //     $id = Auth::guard('client')->id();
    //     $menu = Menu::where('client_id', $id)->orderBy('id', 'desc')->get();
    //     return view('client.backend.menu.all_menu', compact('menu'));
    // }


    public function AllMenu(){
        $menu = Menu::orderBy('id', 'desc')->get();
        return view('admin.backend.menu.all_menu', compact('menu'));
    } 
    //End Method

    public function AddMenu(){

        return view('admin.backend.menu.add_menu');
    } 
    //End Method

    public function StoreMenu(Request $request) {
        if($request->file('image')){
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/menu_images/'
                .$name_gen));
            $save_url = 'upload/menu_images/'.$name_gen;

            Menu::create([
                'menu_name' => $request->menu_name,
                'image' => $save_url,
            ]);

        }
        
        $notification = array(
            'message' => 'Create Menu Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.menu')->with($notification);
    }
    // End Method

    public function EditMenu($id) {
        $menu = Menu::find($id);
        return view('admin.backend.menu.edit_menu', compact('menu'));

    }
    // End Method
    
    public function UpdateMenu(Request $request) {
        $cat_id = $request->id;

        if($request->file('image')){
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/menu_images/'
                .$name_gen));
            $save_url = 'upload/menu_images/'.$name_gen;

            Menu::find($cat_id)->update([
                'menu_name' => $request->menu_name,
                'image' => $save_url,
            ]);

            $notification = array(
                'message' => 'Update Menu Successfully',
                'alert-type' => 'success'
            );
        } else {
            Menu::find($cat_id)->update([
                'menu_name' => $request->menu_name,
            ]);
            $notification = array(
                'message' => 'Update Menu Successfully',
                'alert-type' => 'success'
            );
        }
        

        return redirect()->route('all.menu')->with($notification);
    }
    // End Method

    public function DeleteMenu($id) {
        $item = Menu::find($id);
        $img = $item->image;
        unlink($img);

        Menu::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Menu Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method





    //// ALL PRODUCT METHOD STARTED
    
    public function AllProduct(){
        $id = Auth::guard('client')->id();
        $product = ProductNew::with(['productTemplate'])
                            ->where('client_id', $id)->orderBy('id', 'desc')->get();
        return view('client.backend.product.all_product', compact('product'));
    } 
    //End Method

    public function AddProduct(){
        $menus = Menu::latest()->get();
        $productTemplates = ProductTemplate::with(['category', 'menu'])
                                        ->latest()
                                        ->get()
                                        ->groupBy('menu_id');

        return view('client.backend.product.add_product', compact('productTemplates', 'menus'));
    } 
    //End Method

    public function StoreProduct(Request $request) {
        ProductNew::create([
            'client_id' => Auth::guard('client')->id(),
            'product_template_id' => $request->product_template_id,
            'qty' => $request->qty,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'most_popular' => $request->most_popular,
            'best_seller' => $request->best_seller,
            'status' => 1,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Create Product New Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.product')->with($notification);
    }
    // End Method

    public function EditProduct($id) {
        $product = ProductNew::findOrFail($id);
        $menus = Menu::latest()->get();
        $productTemplates = ProductTemplate::with(['category', 'menu'])
                                            ->latest()
                                            ->get()
                                            ->groupBy('menu_id');

        // Tìm ra template hiện tại của sản phẩm
        $productTemplateEdit = ProductTemplate::where('id', $product->product_template_id)
                                            ->first();
    
        return view('client.backend.product.edit_product', compact('productTemplates', 'menus', 'product', 'productTemplateEdit'));
    }
    // End Method
    
    public function UpdateProduct(Request $request) {
        $pro_id = $request->id;
    
        $data = [
            'product_template_id' => $request->product_template_id,
            'qty' => $request->qty,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'most_popular' => $request->most_popular,
            'best_seller' => $request->best_seller,
            'client_id' => Auth::guard('client')->id(),
            'updated_at' => Carbon::now(),
        ];
    
        ProductNew::findOrFail($pro_id)->update($data);
    
        $notification = array(
            'message' => 'Update Product Successfully',
            'alert-type' => 'success'
        );
    
        return redirect()->route('all.product')->with($notification);
    }
    // End Method

    public function DeleteProduct($id) {
        ProductNew::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Product Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method

    public function ChangeStatus(Request $request) {
        $product = Product::find($request->product_id);
        $product->status = $request->status;
        $product->save();
        return response()->json(['success' => 'Status Change Successfully']);
    }
    // End Method
    
    public function ChangeStatusProductTemplate(Request $request) {
        $product = ProductTemplate::find($request->product_id);
        $product->status = $request->status;
        $product->save();
        return response()->json(['success' => 'Status Change Successfully']);
    }
    // End Method
}


























    // //// ALL PRODUCT METHOD STARTED
    
    // public function AllProduct(){
    //     $id = Auth::guard('client')->id();
    //     $product = Product::where('client_id', $id)->orderBy('id', 'desc')->get();
    //     return view('client.backend.product.all_product', compact('product'));
    // } 
    // //End Method

    // public function AddProduct(){
    //     $category = Category::latest()->get();
    //     $city = City::latest()->get();
    //     $menu = Menu::latest()->get();

    //     return view('client.backend.product.add_product', compact('category', 'city', 'menu'));
    // } 
    // //End Method

    // public function StoreProduct(Request $request) {

    //     $pcode = IdGenerator::generate([
    //         'table' => 'products', 
    //         'field' => 'code',
    //         'length' => 5,
    //         'prefix' => 'PC']);

    //     if($request->file('image')){
    //         $image = $request->file('image');
    //         $manage = new ImageManager(new Driver());
    //         $name_gen = hexdec(uniqid()).'.'
    //                     .$image->getClientOriginalExtension();
    //         $img = $manage->read($image);
    //         $img->resize(300, 300)->save(public_path('upload/product_images/'
    //             .$name_gen));
    //         $save_url = 'upload/product_images/'.$name_gen;

    //         Product::create([
    //             'name' => $request->name,
    //             'slug' => Str::slug($request->name),
    //             'category_id' => $request->category_id,
    //             'city_id' => $request->city_id,
    //             'menu_id' => $request->menu_id,
    //             'code' => $pcode,
    //             'qty' => $request->qty,
    //             'size' => $request->size,
    //             'price' => $request->price,
    //             'discount_price' => $request->discount_price,
    //             'client_id' => Auth::guard('client')->id(),
    //             'most_popular' => $request->most_popular,
    //             'best_seller' => $request->best_seller,
    //             'status' => 1,
    //             'created_at' => Carbon::now(),
    //             'client_id' => Auth::guard('client')->id(),
    //             'image' => $save_url,
    //         ]);

    //     }
        
    //     $notification = array(
    //         'message' => 'Create Menu Successfully',
    //         'alert-type' => 'success'
    //     );

    //     return redirect()->route('all.product')->with($notification);
    // }
    // // End Method

    // public function EditProduct($id) {
    //     $client_id = Auth::guard('client')->id();
    //     $category = Category::latest()->get();
    //     $city = City::latest()->get();
    //     $menu = Menu::where('client_id', $client_id)->latest()->get();
    //     $product = Product::find($id);
    //     return view('client.backend.product.edit_product', compact('category', 'city', 'menu', 'product'));

    // }
    // // End Method
    
    // public function UpdateProduct(Request $request) {
    //     $pro_id = $request->id;
    //     $pcode = IdGenerator::generate([
    //         'table' => 'products', 
    //         'field' => 'code',
    //         'length' => 5,
    //         'prefix' => 'PC']);

    //     if($request->file('image')){
    //         $image = $request->file('image');
    //         $manage = new ImageManager(new Driver());
    //         $name_gen = hexdec(uniqid()).'.'
    //                     .$image->getClientOriginalExtension();
    //         $img = $manage->read($image);
    //         $img->resize(300, 300)->save(public_path('upload/product_images/'
    //             .$name_gen));
    //         $save_url = 'upload/product_images/'.$name_gen;

    //         Product::find($pro_id)->update([
    //             'name' => $request->name,
    //             'slug' => Str::slug($request->name),
    //             'category_id' => $request->category_id,
    //             'city_id' => $request->city_id,
    //             'menu_id' => $request->menu_id,
    //             'qty' => $request->qty,
    //             'size' => $request->size,
    //             'price' => $request->price,
    //             'discount_price' => $request->discount_price,
    //             'client_id' => Auth::guard('client')->id(),
    //             'most_popular' => $request->most_popular,
    //             'best_seller' => $request->best_seller,
    //             'created_at' => Carbon::now(),
    //             'image' => $save_url,
    //         ]);

    //         $notification = array(
    //             'message' => 'Create Menu Successfully',
    //             'alert-type' => 'success'
    //         );
    
    //         return redirect()->route('all.product')->with($notification);
    //     } else {
    //         Product::find($pro_id)->update([
    //             'name' => $request->name,
    //             'slug' => Str::slug($request->name),
    //             'category_id' => $request->category_id,
    //             'city_id' => $request->city_id,
    //             'menu_id' => $request->menu_id,
    //             'qty' => $request->qty,
    //             'size' => $request->size,
    //             'price' => $request->price,
    //             'discount_price' => $request->discount_price,
    //             'client_id' => Auth::guard('client')->id(),
    //             'most_popular' => $request->most_popular,
    //             'best_seller' => $request->best_seller,
    //             'created_at' => Carbon::now(),
    //         ]);

    //         $notification = array(
    //             'message' => 'Create Menu Successfully',
    //             'alert-type' => 'success'
    //         );
    
    //         return redirect()->route('all.product')->with($notification);
    //     }
    // }
    // // End Method

    // public function DeleteProduct($id) {
    //     $item = Product::find($id);
    //     $img = $item->image;
    //     unlink($img);

    //     Product::find($id)->delete();
        
    //     $notification = array(
    //         'message' => 'Delete Product Successfully',
    //         'alert-type' => 'success'
    //     );
    //     return redirect()->back()->with($notification);
    // }
    // // End Method