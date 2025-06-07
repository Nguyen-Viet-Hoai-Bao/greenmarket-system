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
        $categories = Category::latest()->get();
        $productTemplates = ProductTemplate::with(['category', 'menu'])
                                        ->latest()
                                        ->get()
                                        ->groupBy('menu_id');
        return view('client.backend.product.add_product', compact('productTemplates', 'menus', 'categories'));
    } 
    //End Method

    public function StoreProduct(Request $request) {
        $validated = $request->validate([
            'product_template_id' => 'required|exists:product_templates,id',
            'qty' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:1000',
            'price' => 'required|numeric|min:1000',
            'discount_price' => 'nullable|numeric|min:1000',
            'most_popular' => 'nullable|boolean',
            'best_seller' => 'nullable|boolean',
        ]);

        // Kiểm tra discount_price < price
        if ($request->filled('discount_price') && $request->discount_price >= $request->price) {
            return back()->withErrors([
                'discount_price' => 'Giá giảm phải nhỏ hơn giá bán.',
            ])->withInput();
        }

        $clientId = Auth::guard('client')->id();

        // Tìm sản phẩm đã tồn tại
        $existingProduct = ProductNew::where('client_id', $clientId)
            ->where('product_template_id', $request->product_template_id)
            ->first();

        if ($existingProduct) {
            // Cập nhật: cộng qty và cập nhật giá
            $existingProduct->update([
                'qty' => $existingProduct->qty + $request->qty,
                'cost_price' => $request->cost_price,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'most_popular' => $request->most_popular ?? $existingProduct->most_popular,
                'best_seller' => $request->best_seller ?? $existingProduct->best_seller,
                'updated_at' => Carbon::now(),
            ]);

            $notification = [
                'message' => 'Updated existing product successfully.',
                'alert-type' => 'success'
            ];
        } else {
            // Thêm mới
            ProductNew::create([
                'client_id' => $clientId,
                'product_template_id' => $request->product_template_id,
                'qty' => $request->qty,
                'cost_price' => $request->cost_price,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'most_popular' => $request->most_popular ?? 0,
                'best_seller' => $request->best_seller ?? 0,
                'status' => 1,
                'created_at' => Carbon::now(),
            ]);

            $notification = [
                'message' => 'Create new product successfully.',
                'alert-type' => 'success'
            ];
        }

        return redirect()->route('all.product')->with($notification);
    }
    // End Method
    
    public function AddProductMulti(){
        $menus = Menu::latest()->get();
        $categories = Category::latest()->get();
        $productTemplates = ProductTemplate::with(['category', 'menu'])
                                        ->latest()
                                        ->get()
                                        ->groupBy('menu_id');
        return view('client.backend.product.add_product_multi', compact('productTemplates', 'menus', 'categories'));
    } 
    //End Method

    public function StoreProductMulti(Request $request)
    {
        $clientId = Auth::guard('client')->id();

        $validatedData = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_template_id' => 'required|exists:product_templates,id',
            'products.*.qty' => 'required|integer|min:1',
            'products.*.cost_price' => 'required|numeric|min:1000',
            'products.*.price' => 'required|numeric|min:1000',
            'products.*.discount_price' => 'nullable|numeric|min:1000',
        ]);

        foreach ($validatedData['products'] as $productData) {
            if (!empty($productData['discount_price']) && $productData['discount_price'] >= $productData['price']) {
                return back()->withErrors([
                    'products' => 'Giá giảm phải nhỏ hơn giá bán.',
                ])->withInput();
            }

            $existingProduct = ProductNew::where('client_id', $clientId)
                ->where('product_template_id', $productData['product_template_id'])
                ->first();

            if ($existingProduct) {
                $existingProduct->update([
                    'qty' => $existingProduct->qty + $productData['qty'],
                    'cost_price' => $productData['cost_price'],
                    'price' => $productData['price'],
                    'discount_price' => $productData['discount_price'],
                    'updated_at' => now(),
                ]);
            } else {
                ProductNew::create([
                    'client_id' => $clientId,
                    'product_template_id' => $productData['product_template_id'],
                    'qty' => $productData['qty'],
                    'cost_price' => $productData['cost_price'],
                    'price' => $productData['price'],
                    'discount_price' => $productData['discount_price'],
                    'status' => 1,
                    'created_at' => now(),
                ]);
            }
        }
        
        $notification = array(
            'message' => 'Đã thêm/cập nhật sản phẩm hàng loạt thành công.',
            'alert-type' => 'success'
        );

        return redirect()->route('all.product')->with($notification);
    }

    public function GetProductInfo($template_id)
    {
        $clientId = Auth::guard('client')->id();

        $product = ProductNew::where('client_id', $clientId)
                    ->where('product_template_id', $template_id)
                    ->first();

        if ($product) {
            return response()->json([
                'exists' => true,
                'cost_price' => $product->cost_price,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'qty' => $product->qty
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

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
        $validated = $request->validate([
            'product_template_id' => 'required|exists:product_templates,id',
            'qty' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:1000',
            'discount_price' => 'nullable|numeric|min:1000',
            'most_popular' => 'nullable|boolean',
            'best_seller' => 'nullable|boolean',
        ]);

        $pro_id = $request->id;
    
        $data = [
            'product_template_id' => $request->product_template_id,
            'qty' => $request->qty,
            'cost_price' => $request->cost_price,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'most_popular' => $request->most_popular ?? 0,
            'best_seller' => $request->best_seller ?? 0,
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
        $product = ProductNew::find($request->product_id);
        $product->status = $request->status;
        $product->save();
        return response()->json(['success' => 'Cập nhật trạng thái sản phẩm thành công']);
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