<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\Category;
use App\Models\Menu;
use App\Models\City;
use App\Models\Product;
use App\Models\Client;

class ManageController extends Controller
{
    //// ALL PRODUCT METHOD STARTED
    
    public function AdminAllProduct(){
        $product = Product::orderBy('id', 'desc')->get();
        return view('admin.backend.product.all_product', compact('product'));
    } 
    //End Method

    public function AdminAddProduct(){
        $category = Category::latest()->get();
        $city = City::latest()->get();
        $menu = Menu::latest()->get();
        $client = Client::latest()->get();

        return view('admin.backend.product.add_product', compact('category', 'city', 'menu', 'client'));
    } 
    //End Method

    public function AdminStoreProduct(Request $request) {

        $pcode = IdGenerator::generate([
            'table' => 'products', 
            'field' => 'code',
            'length' => 5,
            'prefix' => 'PC']);

        if($request->file('image')){
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/product_images/'
                .$name_gen));
            $save_url = 'upload/product_images/'.$name_gen;

            Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'category_id' => $request->category_id,
                'city_id' => $request->city_id,
                'menu_id' => $request->menu_id,
                'code' => $pcode,
                'qty' => $request->qty,
                'size' => $request->size,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'most_popular' => $request->most_popular,
                'best_seller' => $request->best_seller,
                'status' => 1,
                'created_at' => Carbon::now(),
                'client_id' => $request->client_id,
                'image' => $save_url,
            ]);

        }
        
        $notification = array(
            'message' => 'Create Menu Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('admin.all.product')->with($notification);
    }
    // End Method

    public function AdminEditProduct($id) {
        $category = Category::latest()->get();
        $city = City::latest()->get();
        $menu = Menu::latest()->get();
        $product = Product::find($id);
        $client = Client::latest()->get();
        return view('admin.backend.product.edit_product', compact('category', 'city', 'menu', 'product', 'client'));

    }
    // End Method
    
    public function AdminUpdateProduct(Request $request) {
        $pro_id = $request->id;
        $pcode = IdGenerator::generate([
            'table' => 'products', 
            'field' => 'code',
            'length' => 5,
            'prefix' => 'PC']);

        if($request->file('image')){
            $image = $request->file('image');
            $manage = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'
                        .$image->getClientOriginalExtension();
            $img = $manage->read($image);
            $img->resize(300, 300)->save(public_path('upload/product_images/'
                .$name_gen));
            $save_url = 'upload/product_images/'.$name_gen;

            Product::find($pro_id)->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'category_id' => $request->category_id,
                'city_id' => $request->city_id,
                'menu_id' => $request->menu_id,
                'qty' => $request->qty,
                'size' => $request->size,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'client_id' => $request->client_id,
                'most_popular' => $request->most_popular,
                'best_seller' => $request->best_seller,
                'created_at' => Carbon::now(),
                'image' => $save_url,
            ]);

            $notification = array(
                'message' => 'Create Menu Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('admin.all.product')->with($notification);
        } else {
            Product::find($pro_id)->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'category_id' => $request->category_id,
                'city_id' => $request->city_id,
                'menu_id' => $request->menu_id,
                'qty' => $request->qty,
                'size' => $request->size,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'client_id' => $request->client_id,
                'most_popular' => $request->most_popular,
                'best_seller' => $request->best_seller,
                'created_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Create Menu Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('admin.all.product')->with($notification);
        }
    }
    // End Method

    public function AdminDeleteProduct($id) {
        $item = Product::find($id);
        $img = $item->image;
        unlink($img);

        Product::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Product Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method


    //////// Pending and Approve market
    public function PendingMarket() {
        $client = Client::where('status', 0)->get();
        return view('admin.backend.market.pending_market', compact('client'));    
    }
    // End Method

    public function ClientChangeStatus(Request $request) {
        $client = Client::find($request->client_id);
        $client->status = $request->status;
        $client->save();
        return response()->json(['success' => 'Status Change Successfully']);
    }
    // End Method

    public function ApproveMarket() {
        $client = Client::where('status', 1)->get();
        return view('admin.backend.market.approve_market', compact('client'));    
    }
    // End Method
}
