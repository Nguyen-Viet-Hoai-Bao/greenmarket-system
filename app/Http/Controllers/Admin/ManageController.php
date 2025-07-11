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
use App\Models\ProductNew;
use App\Models\ProductTemplate;
use App\Models\ProductDetail;
use App\Models\Client;
use App\Models\Banner;

use App\Mail\ClientApprovedMailer;
use App\Mail\ClientRejectedMailer;
use App\Mail\ClientBlockedMailer;
use App\Mail\ClientUnblockedMailer;
use Cloudinary\Api\Upload\UploadApi;

class ManageController extends Controller
{
    
    //// ALL PRODUCT METHOD STARTED
    
    public function AdminAllProduct(){
        $product = ProductTemplate::with(['category', 'menu'])
                                ->orderBy('id', 'desc')
                                ->get();
        return view('admin.backend.product.all_product', compact('product'));
    } 
    //End Method

    public function AdminAddProduct(){
        $category = Category::latest()->get();
        $menu = Menu::latest()->get();

        return view('admin.backend.product.add_product', compact('category', 'menu'));
    } 
    //End Method

    public function AdminStoreProduct(Request $request) {

        $pcode = IdGenerator::generate([
            'table' => 'product_templates', 
            'field' => 'code',
            'length' => 5,
            'prefix' => 'PC']);

        if($request->file('image')){
            $image = $request->file('image');
            $upload = (new UploadApi())->upload($image->getRealPath(), [
                'folder' => 'product_template_images'
            ]);
            $save_url = $upload['secure_url'];

            ProductTemplate::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'category_id' => $request->category_id,
                'menu_id' => $request->menu_id,
                'code' => $pcode,
                'size' => $request->size,
                'unit' => $request->unit,
                'stock_mode' => $request->stock_mode,
                'status' => 1,
                'created_at' => Carbon::now(),
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
        $menu = Menu::latest()->get();
        $product = ProductTemplate::with('productDetail')->find($id);
        return view('admin.backend.product.edit_product', compact('category', 'menu', 'product'));

    }
    // End Method
    public function AdminUpdateProduct(Request $request) {
        $pro_id = $request->id;

        $pcode = IdGenerator::generate([
            'table' => 'products', 
            'field' => 'code',
            'length' => 5,
            'prefix' => 'PC'
        ]);

        $updateData = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
            'menu_id' => $request->menu_id,
            'size' => $request->size,
            'unit' => $request->unit,
            'stock_mode' => $request->stock_mode,
            'created_at' => Carbon::now(),
        ];

        if ($request->file('image')) {
            $image = $request->file('image');
            $upload = (new UploadApi())->upload($image->getRealPath(), [
                'folder' => 'product_template_images'
            ]);
            $updateData['image'] = $upload['secure_url'];
        }

        ProductTemplate::find($pro_id)->update($updateData);

        // Cập nhật hoặc tạo ProductDetail
        ProductDetail::updateOrCreate(
            ['product_template_id' => $pro_id],
            [
                'description' => $request->description ?? 'Đang cập nhật',
                'product_info' => $request->product_info ?? 'Đang cập nhật',
                'note' => $request->note ?? 'Đang cập nhật',
                'origin' => $request->origin ?? 'Đang cập nhật',
                'preservation' => $request->preservation ?? 'Đang cập nhật',
                'usage_instructions' => $request->usage_instructions ?? 'Đang cập nhật',
            ]
        );

        $notification = [
            'message' => 'Cập nhật sản phẩm thành công',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.all.product')->with($notification);
    }
    // End Method

    public function AdminDeleteProduct($id) {
        $item = ProductTemplate::find($id);
        ProductTemplate::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Product Template Successfully',
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

    public function RejectedMarket() {
        $client = Client::where('status', 2)->get();
        return view('admin.backend.market.rejected_market', compact('client'));    
    }
    // End Method

    public function SuspendedMarket() {
        $client = Client::where('status', 3)->get();
        return view('admin.backend.market.suspended_market', compact('client'));    
    }
    // End Method

    public function ClientChangeStatus(Request $request)
    {
        $client = Client::find($request->client_id);

        if (!$client) {
            return response()->json(['error' => 'Client not found']);
        }

        $oldStatus = $client->status;
        $newStatus = $request->status;

        $client->status = $newStatus;
        $client->save();

        if ($oldStatus == 0 && $newStatus == 1) {
            // Được duyệt
            
            ClientApprovedMailer::send($client);
        } elseif ($oldStatus == 2 && $newStatus == 1) {
            // Không được duyệt
            ClientApprovedMailer::send($client);
        } elseif ($oldStatus == 0 && $newStatus == 2) {
            // Không được duyệt
            ClientRejectedMailer::send($client);
        } elseif ($oldStatus == 1 && $newStatus == 3) {
            // Bị khóa
            ClientBlockedMailer::send($client);
        } elseif ($oldStatus == 3 && $newStatus == 1) {
            // Mở lại
            ClientUnblockedMailer::send($client);
        }

        return response()->json(['success' => 'Cập nhật trạng thái cửa hàng thành công']);
    }

    public function ApproveMarket() {
        $client = Client::where('status', 1)->get();
        return view('admin.backend.market.approve_market', compact('client'));    
    }
    // End Method

    public function showDetails($id)
    {
        $client = Client::findOrFail($id);
        return view('admin.backend.market.details', compact('client'));
    }
    // End Method
    


    ///// BANNER METHOD
    public function AllBanner() {
        $banner = Banner::latest()->get();
        return view('admin.backend.banner.all_banner', compact('banner'));    
    }
    // End Method
    
    public function BannerStore(Request $request) {
        if ($request->file('image')) {
            $image = $request->file('image');

            // Upload lên Cloudinary
            $uploaded = (new UploadApi())->upload($image->getRealPath(), [
                'folder' => 'banner_images'
            ]);
            $cloudUrl = $uploaded['secure_url'];

            // Lưu thông tin vào DB
            Banner::create([
                'url' => $request->url,
                'image' => $cloudUrl,
            ]);
        }
        
        $notification = array(
            'message' => 'Banner Insert Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
    // End Method

    public function EditBanner($id) {
        $banner = Banner::find($id);
        if ($banner) {
            $banner->image = asset($banner->image);
        }
        return response()->json($banner);

    }
    // End Method

    public function BannerUpdate(Request $request) {
        $banner_id = $request->banner_id;
        $banner = Banner::findOrFail($banner_id);

        if ($request->file('image')) {
            $image = $request->file('image');

            $uploaded = (new UploadApi())->upload($image->getRealPath(), [
                'folder' => 'banner_images'
            ]);
            $secureUrl = $uploaded['secure_url'];

            $banner->update([
                'url' => $request->url,
                'image' => $secureUrl,
            ]);
        } else {
            $banner->update([
                'url' => $request->url,
            ]);
        }

        $notification = [
            'message' => 'Banner Update Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->route('all.banner')->with($notification);
    }
    // End Method
    
    public function DeleteBanner($id) {
        $item = Banner::find($id);
        Banner::find($id)->delete();
        
        $notification = array(
            'message' => 'Delete Banner Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method







    
    
    // //// ALL PRODUCT METHOD STARTED
    
    // public function AdminAllProduct(){
    //     $product = Product::orderBy('id', 'desc')->get();
    //     return view('admin.backend.product.all_product', compact('product'));
    // } 
    // //End Method

    // public function AdminAddProduct(){
    //     $category = Category::latest()->get();
    //     $city = City::latest()->get();
    //     $menu = Menu::latest()->get();
    //     $client = Client::latest()->get();

    //     return view('admin.backend.product.add_product', compact('category', 'city', 'menu', 'client'));
    // } 
    // //End Method

    // public function AdminStoreProduct(Request $request) {

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
    //             'most_popular' => $request->most_popular,
    //             'best_seller' => $request->best_seller,
    //             'status' => 1,
    //             'created_at' => Carbon::now(),
    //             'client_id' => $request->client_id,
    //             'image' => $save_url,
    //         ]);

    //     }
        
    //     $notification = array(
    //         'message' => 'Create Menu Successfully',
    //         'alert-type' => 'success'
    //     );

    //     return redirect()->route('admin.all.product')->with($notification);
    // }
    // // End Method

    // public function AdminEditProduct($id) {
    //     $category = Category::latest()->get();
    //     $city = City::latest()->get();
    //     $menu = Menu::latest()->get();
    //     $product = Product::find($id);
    //     $client = Client::latest()->get();
    //     return view('admin.backend.product.edit_product', compact('category', 'city', 'menu', 'product', 'client'));

    // }
    // // End Method
    
    // public function AdminUpdateProduct(Request $request) {
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
    //             'client_id' => $request->client_id,
    //             'most_popular' => $request->most_popular,
    //             'best_seller' => $request->best_seller,
    //             'created_at' => Carbon::now(),
    //             'image' => $save_url,
    //         ]);

    //         $notification = array(
    //             'message' => 'Create Menu Successfully',
    //             'alert-type' => 'success'
    //         );
    
    //         return redirect()->route('admin.all.product')->with($notification);
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
    //             'client_id' => $request->client_id,
    //             'most_popular' => $request->most_popular,
    //             'best_seller' => $request->best_seller,
    //             'created_at' => Carbon::now(),
    //         ]);

    //         $notification = array(
    //             'message' => 'Create Menu Successfully',
    //             'alert-type' => 'success'
    //         );
    
    //         return redirect()->route('admin.all.product')->with($notification);
    //     }
    // }
    // // End Method

    // public function AdminDeleteProduct($id) {
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

}
