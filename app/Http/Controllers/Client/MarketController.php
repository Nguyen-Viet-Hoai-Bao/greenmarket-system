<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Menu;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\ProductDiscount;
use App\Models\ProductNew;
use App\Models\ProductTemplate;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GD\Driver;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Cloudinary\Api\Upload\UploadApi;

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
        if ($request->file('image')) {
            $image = $request->file('image');

            // Sử dụng Cloudinary UploadApi
            $uploadApi = new UploadApi();
            $response = $uploadApi->upload($image->getRealPath(), [
                'folder' => 'menu_images'
            ]);

            $save_url = $response['secure_url'];

            // Lưu menu với URL ảnh từ Cloudinary
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
        $menu_id = $request->id;
        $menu = Menu::find($menu_id);

        if ($request->file('image')) {
            $image = $request->file('image');

            $uploadApi = new UploadApi();
            $response = $uploadApi->upload($image->getRealPath(), [
                'folder' => 'menu_images'
            ]);

            $save_url = $response['secure_url'];
            $menu->update([
                'menu_name' => $request->menu_name,
                'image' => $save_url,
            ]);
        } else {
            $menu->update([
                'menu_name' => $request->menu_name,
            ]);
        }

        $notification = [
            'message' => 'Update Menu Successfully',
            'alert-type' => 'success'
        ];

        return redirect()->route('all.menu')->with($notification);
    }
    // End Method

    public function DeleteMenu($id) {
        $item = Menu::find($id);
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

    // public function AddProduct(){
    //     $menus = Menu::latest()->get();
    //     $categories = Category::latest()->get();
    //     $productTemplates = ProductTemplate::with(['category', 'menu'])
    //                                     ->latest()
    //                                     ->get()
    //                                     ->groupBy('menu_id');
    //     return view('client.backend.product.add_product', compact('productTemplates', 'menus', 'categories'));
    // } 
    // //End Method

    // public function StoreProduct(Request $request) {
    //     $validated = $request->validate([
    //         'product_template_id' => 'required|exists:product_templates,id',
    //         'qty' => 'required|integer|min:1',
    //         'cost_price' => 'required|numeric|min:1000',
    //         'price' => 'required|numeric|min:1000',
    //         'discount_price' => 'nullable|numeric|min:1000',
    //         'most_popular' => 'nullable|boolean',
    //         'best_seller' => 'nullable|boolean',
    //     ]);

    //     // Kiểm tra discount_price < price
    //     if ($request->filled('discount_price') && $request->discount_price >= $request->price) {
    //         return back()->withErrors([
    //             'discount_price' => 'Giá giảm phải nhỏ hơn giá bán.',
    //         ])->withInput();
    //     }

    //     $clientId = Auth::guard('client')->id();

    //     // Tìm sản phẩm đã tồn tại
    //     $existingProduct = ProductNew::where('client_id', $clientId)
    //         ->where('product_template_id', $request->product_template_id)
    //         ->first();

    //     if ($existingProduct) {
    //         // Cập nhật: cộng qty và cập nhật giá
    //         $existingProduct->update([
    //             'qty' => $existingProduct->qty + $request->qty,
    //             'cost_price' => $request->cost_price,
    //             'price' => $request->price,
    //             'discount_price' => $request->discount_price,
    //             'most_popular' => $request->most_popular ?? $existingProduct->most_popular,
    //             'best_seller' => $request->best_seller ?? $existingProduct->best_seller,
    //             'updated_at' => Carbon::now(),
    //         ]);

    //         $notification = [
    //             'message' => 'Updated existing product successfully.',
    //             'alert-type' => 'success'
    //         ];
    //     } else {
    //         // Thêm mới
    //         ProductNew::create([
    //             'client_id' => $clientId,
    //             'product_template_id' => $request->product_template_id,
    //             'qty' => $request->qty,
    //             'cost_price' => $request->cost_price,
    //             'price' => $request->price,
    //             'discount_price' => $request->discount_price,
    //             'most_popular' => $request->most_popular ?? 0,
    //             'best_seller' => $request->best_seller ?? 0,
    //             'status' => 1,
    //             'created_at' => Carbon::now(),
    //         ]);

    //         $notification = [
    //             'message' => 'Create new product successfully.',
    //             'alert-type' => 'success'
    //         ];
    //     }

    //     return redirect()->route('all.product')->with($notification);
    // }
    // // End Method

    public function ProductStock()
    {
        $clientId = Auth::guard('client')->id();
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        $productNews = ProductNew::with([
            'productTemplate.category',
            'productUnits',
            'productDiscounts' => function($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc'); 
            }
        ])
        ->where('client_id', $clientId)
        ->orderBy('product_template_id')
        ->get();

        $stockData = [];

        foreach ($productNews as $news) {
            $productTemplate = $news->productTemplate;
            if (!$productTemplate) {
                continue; 
            }

            $stockMode = $productTemplate->stock_mode;
            $productName = $productTemplate->name;
            $categoryName = $productTemplate->category->category_name ?? 'N/A';
            $unitName = $productTemplate->unit ?? 'N/A';
            $size = $productTemplate->size ?? 'N/A';

            $activeGeneralDiscount = $news->productDiscounts->first();

            if ($stockMode === 'quantity') {
                // Nếu là quantity mode, nhóm theo expiry_date
                $groupedUnits = $news->productUnits
                                     ->whereNull('weight') // Chỉ lấy các đơn vị không có weight (quantity mode)
                                     ->groupBy(function($item) {
                                         return $item->expiry_date ?? 'No Expiry Date'; // Nhóm theo expiry_date
                                     });

                foreach ($groupedUnits as $expiryDate => $units) {
                    $totalQty = $units->sum('batch_qty');
                    $avgCostPrice = $units->avg('cost_price');
                    $avgSalePrice = $units->avg('sale_price');

                    // Tính giá bán cuối cùng cho nhóm
                    $finalSalePrice = $this->calculateFinalPrice(
                        $avgSalePrice,
                        null, // Không có discount_price cụ thể cho nhóm này từ product_units
                        $activeGeneralDiscount
                    );

                    // Lấy ngày nhập kho sớm nhất cho nhóm này
                    $importDate = $units->min('created_at');

                    $stockData[] = [
                        'template_id' => $productTemplate->id,
                        'product_name' => $productName,
                        'category_name' => $categoryName,
                        'unit_name' => $unitName,
                        'size' => $size,
                        'stock_mode' => 'Theo Số Lượng',
                        'type' => 'grouped', // Đánh dấu là dòng tổng hợp
                        'expiry_date' => $expiryDate === 'No Expiry Date' ? null : $expiryDate,
                        'total_quantity' => $totalQty,
                        'total_weight' => null,
                        'average_cost_price' => round($avgCostPrice),
                        'original_sale_price' => round($avgSalePrice), // Thêm giá gốc để so sánh
                        'final_sale_price' => round($finalSalePrice), // Giá sau giảm giá
                        'import_date' => $importDate ? Carbon::parse($importDate)->format('Y-m-d') : 'N/A',
                        'product_unit_id' => null, // Không có ID cụ thể cho dòng nhóm
                        'shelf_life_days' => null,
                    ];
                }

            } elseif ($stockMode === 'unit') {
                // Nếu là unit mode, hiển thị từng khay/đơn vị
                $news->productUnits
                    ->whereNotNull('weight') // Chỉ lấy các đơn vị có weight (unit mode)
                    ->each(function($unit) use (&$stockData, $productName, $categoryName, $unitName, $size, $productTemplate, $activeGeneralDiscount) {

                        // Tính giá bán cuối cùng cho từng đơn vị
                        $finalSalePrice = $this->calculateFinalPrice(
                            $unit->sale_price,
                            $unit->discount_price, // discount_price của từng unit
                            $activeGeneralDiscount
                        );

                        $stockData[] = [
                            'template_id' => $productTemplate->id,
                            'product_name' => $productName,
                            'category_name' => $categoryName,
                            'unit_name' => $unitName,
                            'size' => $size,
                            'stock_mode' => 'Theo Đơn Vị',
                            'type' => 'individual', // Đánh dấu là dòng cá nhân
                            'expiry_date' => $unit->expiry_date,
                            'total_quantity' => $unit->batch_qty, // Thường là 1 cho mỗi đơn vị
                            'total_weight' => $unit->weight,
                            'average_cost_price' => $unit->cost_price,
                            'original_sale_price' => $unit->sale_price, // Thêm giá gốc để so sánh
                            'final_sale_price' => $finalSalePrice, // Giá sau giảm giá
                            'import_date' => $unit->created_at ? Carbon::parse($unit->created_at)->format('Y-m-d') : 'N/A',
                            'product_unit_id' => $unit->id, // ID của ProductUnit cụ thể
                            'shelf_life_days' => $unit->shelf_life_days,
                        ];
                    });
            }
        }

        // Sắp xếp lại dữ liệu theo tên sản phẩm hoặc ID template để hiển thị mạch lạc hơn
        usort($stockData, function($a, $b) {
            return $a['product_name'] <=> $b['product_name'];
        });

        return view('client.backend.product.product_stock', compact('stockData'));
    }

    protected function calculateFinalPrice($originalPrice, $unitDiscountPrice, $generalDiscount)
    {
        // Ưu tiên 1: discount_price trong ProductUnit
        // Kiểm tra nếu discount_price tồn tại, là số, và nhỏ hơn giá gốc
        if ($unitDiscountPrice !== null && is_numeric($unitDiscountPrice) && $unitDiscountPrice >= 0 && $unitDiscountPrice < $originalPrice) {
            return (float) $unitDiscountPrice;
        }

        // Ưu tiên 2: ProductDiscount chung (nếu không có discount_price ở unit hoặc discount_price không hợp lệ)
        if ($generalDiscount) {
            if ($generalDiscount->discount_percent !== null && $generalDiscount->discount_percent > 0) {
                $discountAmount = $originalPrice * ($generalDiscount->discount_percent / 100);
                return max(0, $originalPrice - $discountAmount);
            } elseif ($generalDiscount->discount_price !== null && $generalDiscount->discount_price > 0) {
                return max(0, $originalPrice - $generalDiscount->discount_price);
            }
        }

        // Không có giảm giá nào được áp dụng hoặc không hợp lệ, trả về giá gốc
        return (float) $originalPrice;
    }

    public function AddProduct(){
        $menus = Menu::latest()->get();
        $categories = Category::latest()->get();
        
        // Lấy tất cả product templates và nhóm theo menu_id,
        // đồng thời tạo một map để dễ dàng lấy stock_mode trong JS
        $productTemplates = ProductTemplate::with(['category', 'menu'])->latest()->get();
        $productTemplatesGrouped = $productTemplates->groupBy('menu_id');
        
        // Tạo một map từ product_template_id sang stock_mode
        $productStockModes = $productTemplates->pluck('stock_mode', 'id')->toArray();

        return view('client.backend.product.add_product_to_stock', compact('productTemplatesGrouped', 'menus', 'categories', 'productStockModes'));
    } 

    // Phương thức mới để xử lý nhập kho chi tiết
    public function StoreProduct(Request $request)
    {
        $clientId = Auth::guard('client')->id();

        $productTemplateId = $request->input('product_template_id');
        $productTemplate = ProductTemplate::findOrFail($productTemplateId);
        $stockMode = $productTemplate->stock_mode;

        $notification = [
            'message' => 'Lỗi không xác định khi nhập kho.',
            'alert-type' => 'error'
        ];

        // BẮT ĐẦU PHẦN XỬ LÝ PRODUCTNEWS
        // Tìm ProductNews hiện có cho ProductTemplate và Client này
        $productNews = ProductNew::where('product_template_id', $productTemplateId)
                                  ->where('client_id', $clientId)
                                  ->first();

        // Nếu chưa có ProductNews, tạo mới
        if (!$productNews) {
            $productNews = ProductNew::create([
                'client_id' => $clientId,
                'product_template_id' => $productTemplateId,
                'qty' => 0, // Số lượng ban đầu là 0, sẽ được cập nhật sau
                'price' => $productTemplate->price ?? 0, // Lấy giá từ template hoặc mặc định
                'discount_price' => $productTemplate->discount_price ?? null, // Lấy giá giảm từ template
                'most_popular' => false, // Mặc định
                'best_seller' => false, // Mặc định
                'status' => 'active', // Mặc định
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        $productNewsId = $productNews->id; // Lấy ID của ProductNews để sử dụng cho ProductUnit
        // KẾT THÚC PHẦN XỬ LÝ PRODUCTNEWS

        // Bạn có thể cân nhắc dùng Database Transaction để đảm bảo tính toàn vẹn dữ liệu
        // Nếu một phần bị lỗi, tất cả các thay đổi sẽ được rollback
        DB::beginTransaction();
        try {

            if ($stockMode === 'quantity') {
                $validated = $request->validate([
                    'product_template_id' => 'required|exists:product_templates,id',
                    'batch_qty' => 'required|integer|min:1', // Số lượng nhập kho
                    'cost_price_quantity' => 'required|numeric|min:0', // Giá nhập cho quantity
                    'expiry_date_quantity' => 'nullable|date', // Hạn sử dụng cho quantity
                    'shelf_life_days' => 'nullable|integer|min:0',
                ]);

                $costPrice = $validated['cost_price_quantity'];
                $salePrice = round($costPrice * 1.40); // Giá bán = giá nhập * 140%
                $expiryDate = $validated['expiry_date_quantity'];
                $totalShelfLifeDays = $validated['shelf_life_days'];
                $discountPrice = null;

                // Tạo một bản ghi ProductUnit mới
                ProductUnit::create([
                    'product_news_id' => $productNewsId, // Sử dụng ID của ProductNews đã có hoặc vừa tạo
                    'batch_qty' => $validated['batch_qty'],
                    'weight' => null, // null cho quantity mode
                    'cost_price' => $costPrice,
                    'sale_price' => $salePrice,
                    'discount_price' => $discountPrice,
                    'expiry_date' => $expiryDate,
                    'shelf_life_days' => $totalShelfLifeDays,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Cập nhật tổng số lượng (qty) trong ProductNews
                $productNews->increment('qty', $validated['batch_qty']);

                $notification = [
                    'message' => 'Nhập kho theo số lượng thành công!',
                    'alert-type' => 'success'
                ];

            } elseif ($stockMode === 'unit') {
                $validated = $request->validate([
                    'product_template_id' => 'required|exists:product_templates,id',
                    'units.*.weight' => 'required|numeric|min:0.001', // Cân nặng từng khay
                    'units.*.cost_price_unit' => 'required|numeric|min:0', // Giá nhập từng khay
                    'units.*.expiry_date_unit' => 'nullable|date', // Hạn sử dụng từng khay
                    'units.*.shelf_life_days' => 'nullable|integer|min:0',
                ]);

                $unitsToCreate = [];
                $totalUnitsCount = 0; // Đếm tổng số đơn vị được thêm vào

                foreach ($validated['units'] as $unitData) {
                    $costPrice = $unitData['cost_price_unit'];
                    $salePrice = round($costPrice * 1.70); // Giá bán = giá nhập * 140%
                    $expiryDate = $unitData['expiry_date_unit'];
                    $totalShelfLifeDays = $unitData['shelf_life_days'];
                    $discountPrice = null;

                    // Tính toán giá giảm nếu hạn sử dụng còn một nửa hoặc ít hơn
                    if ($expiryDate) {
                        $today = Carbon::now();
                        $expiryCarbon = Carbon::parse($expiryDate);
                        $daysRemaining = $today->diffInDays($expiryCarbon, false);

                        if ($daysRemaining > 0 && $totalShelfLifeDays && $totalShelfLifeDays > 0) {
                            if ($daysRemaining <= ($totalShelfLifeDays / 2)) {
                                $discountPrice = round($salePrice * 0.80);
                            }
                        }
                    }

                    $unitsToCreate[] = [
                        'product_news_id' => $productNewsId, // Sử dụng ID của ProductNews đã có hoặc vừa tạo
                        'batch_qty' => 1, // Mặc định là 1 cho mỗi khay/đơn vị
                        'weight' => $unitData['weight'],
                        'cost_price' => $costPrice,
                        'sale_price' => $salePrice,
                        'discount_price' => $discountPrice,
                        'expiry_date' => $expiryDate,
                        'shelf_life_days' => $totalShelfLifeDays,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    $totalUnitsCount++; // Tăng số lượng đơn vị
                }

                ProductUnit::insert($unitsToCreate);

                // Cập nhật tổng số lượng (qty) trong ProductNews
                $productNews->increment('qty', $totalUnitsCount);

                $notification = [
                    'message' => 'Nhập kho theo đơn vị (khay) thành công!',
                    'alert-type' => 'success'
                ];

            } else {
                DB::rollBack(); // Rollback nếu chế độ kho không hợp lệ
                return back()->withErrors(['product_template_id' => 'Chế độ tồn kho không xác định.']);
            }

            DB::commit(); // Commit transaction nếu mọi thứ thành công

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback nếu có bất kỳ lỗi nào xảy ra
            $notification = [
                'message' => 'Đã xảy ra lỗi khi nhập kho: ' . $e->getMessage(),
                'alert-type' => 'error'
            ];
        }

        return redirect()->route('product.stock')->with($notification);
    }
    // End Method StoreStock
    
    public function EditProductUnit($id)
    {
        $clientId = Auth::guard('client')->id();
        $productUnit = ProductUnit::with(['productNew.productTemplate.category'])
                                    ->where('id', $id)
                                    ->first();
        if (!$productUnit || $productUnit->productNew->client_id !== $clientId) {
            return redirect()->route('product.stock')->with('error', 'Đơn vị sản phẩm không tồn tại hoặc bạn không có quyền truy cập.');
        }

        return view('client.backend.product.edit_product_unit', compact('productUnit'));
    }

    public function UpdateProductUnit(Request $request, $id)
    {
        $clientId = Auth::guard('client')->id();
        $productUnit = ProductUnit::where('id', $id)->first();

        if (!$productUnit || $productUnit->productNew->client_id !== $clientId) {
            return redirect()->route('product.stock')->with('error', 'Đơn vị sản phẩm không tồn tại hoặc bạn không có quyền truy cập.');
        }

        $stockMode = $productUnit->productNew->productTemplate->stock_mode;

        $rules = [
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ];

        if ($stockMode === 'quantity') {
            $rules['batch_qty'] = 'required|integer|min:1';
            $rules['weight'] = 'nullable|numeric'; // Weight có thể là null cho quantity
        } else { // unit mode
            $rules['weight'] = 'required|numeric|min:0.01';
            $rules['batch_qty'] = 'nullable|integer'; // Batch_qty có thể là null cho unit
        }

        $request->validate($rules);

        $productUnit->cost_price = $request->cost_price;
        $productUnit->sale_price = $request->sale_price;
        $productUnit->expiry_date = $request->expiry_date;

        if ($stockMode === 'quantity') {
            $productUnit->batch_qty = $request->batch_qty;
            $productUnit->weight = null; // Đảm bảo weight là null cho quantity mode
        } else { // unit mode
            $productUnit->weight = $request->weight;
            $productUnit->batch_qty = 1; 
        }
        $productUnit->save();

        $notification = array(
            'message' => 'Cập nhật đơn vị sản phẩm thành công!',
            'alert-type' => 'success'
        );

        return redirect()->route('product.stock')->with($notification);
    }

    public function DeleteProductUnit($id)
    {
        $clientId = Auth::guard('client')->id();
        $productUnit = ProductUnit::where('id', $id)->first();

        if (!$productUnit || $productUnit->productNew->client_id !== $clientId) {
            return redirect()->route('product.stock')->with('error', 'Đơn vị sản phẩm không tồn tại hoặc bạn không có quyền xóa.');
        }

        try {
            $productUnit->delete();

            $notification = array(
                'message' => 'Đơn vị sản phẩm đã được xóa thành công!',
                'alert-type' => 'success'
            );
        } catch (\Exception $e) {
            Log::error("Lỗi khi xóa ProductUnit ID {$id}: " . $e->getMessage());
            $notification = array(
                'message' => 'Có lỗi xảy ra khi xóa đơn vị sản phẩm.',
                'alert-type' => 'error'
            );
        }

        return redirect()->route('product.stock')->with($notification);
    }

    public function AllProductDiscounts()
    {
        $clientId = Auth::guard('client')->id();
        $discounts = ProductDiscount::with('productNew.productTemplate')
                                    ->whereHas('productNew', function($query) use ($clientId) {
                                        $query->where('client_id', $clientId);
                                    })
                                    ->latest()
                                    ->get();
        return view('client.backend.product.discount.all_discounts', compact('discounts'));
    }

    public function AddProductDiscount()
    {
        $clientId = Auth::guard('client')->id();
        // Lấy tất cả ProductNews thuộc về client để chọn sản phẩm áp dụng giảm giá
        $productNews = ProductNew::with('productTemplate')
                                  ->where('client_id', $clientId)
                                  ->get();
        return view('client.backend.product.discount.add_discount', compact('productNews'));
    }

    public function StoreProductDiscount(Request $request)
    {
        $clientId = Auth::guard('client')->id();

        $validated = $request->validate([
            'product_news_id' => 'required|exists:product_news,id',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_price' => 'nullable|numeric|min:0',
            'start_at' => 'required|date_format:Y-m-d\TH:i',
            'end_at' => 'required|date_format:Y-m-d\TH:i|after:start_at',
        ], [
            'product_news_id.required' => 'Vui lòng chọn sản phẩm áp dụng giảm giá.',
            'product_news_id.exists' => 'Sản phẩm được chọn không tồn tại.',
            'discount_percent.numeric' => 'Phần trăm giảm giá phải là số.',
            'discount_percent.min' => 'Phần trăm giảm giá không thể âm.',
            'discount_percent.max' => 'Phần trăm giảm giá không thể lớn hơn 100.',
            'discount_price.numeric' => 'Giá giảm phải là số.',
            'discount_price.min' => 'Giá giảm không thể âm.',
            'start_at.required' => 'Ngày bắt đầu giảm giá là bắt buộc.',
            'start_at.date_format' => 'Ngày bắt đầu giảm giá không đúng định dạng (YYYY-MM-DD HH:MM).',
            'end_at.required' => 'Ngày kết thúc giảm giá là bắt buộc.',
            'end_at.date_format' => 'Ngày kết thúc giảm giá không đúng định dạng (YYYY-MM-DD HH:MM).',
            'end_at.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ]);

        // Đảm bảo chỉ một trong hai loại giảm giá được nhập
        if ($request->filled('discount_percent') && $request->filled('discount_price')) {
            return back()->withErrors(['discount_type' => 'Chỉ được phép nhập một trong hai: phần trăm giảm giá HOẶC giá giảm cố định.'])->withInput();
        }
        if (!$request->filled('discount_percent') && !$request->filled('discount_price')) {
            return back()->withErrors(['discount_type' => 'Vui lòng nhập phần trăm giảm giá HOẶC giá giảm cố định.'])->withInput();
        }

        // Kiểm tra xem product_news_id có thuộc về client hiện tại không
        $productNews = ProductNew::where('id', $validated['product_news_id'])
                                  ->where('client_id', $clientId)
                                  ->first();
        if (!$productNews) {
            return back()->withErrors(['product_news_id' => 'Sản phẩm bạn chọn không hợp lệ hoặc không thuộc về bạn.'])->withInput();
        }

        ProductDiscount::create([
            'product_news_id' => $validated['product_news_id'],
            'discount_percent' => $validated['discount_percent'] ?? null,
            'discount_price' => $validated['discount_price'] ?? null,
            'start_at' => Carbon::parse($validated['start_at']),
            'end_at' => Carbon::parse($validated['end_at']),
            'created_at' => Carbon::now(),
        ]);

        $notification = [
            'message' => 'Đợt giảm giá đã được thêm thành công!',
            'alert-type' => 'success'
        ];

        return redirect()->route('product.discounts.all')->with($notification);
    }

    public function EditProductDiscount($id)
    {
        $clientId = Auth::guard('client')->id();
        $discount = ProductDiscount::with('productNew.productTemplate')
                                  ->where('id', $id)
                                  ->whereHas('productNew', function($query) use ($clientId) {
                                      $query->where('client_id', $clientId);
                                  })
                                  ->first();

        if (!$discount) {
            return redirect()->route('product.discounts.all')->with('error', 'Đợt giảm giá không tồn tại hoặc bạn không có quyền truy cập.');
        }
        
        $productNews = ProductNew::with('productTemplate')
                                  ->where('client_id', $clientId)
                                  ->get();

        return view('client.backend.product.discount.edit_discount', compact('discount', 'productNews'));
    }

    public function UpdateProductDiscount(Request $request, $id)
    {
        $clientId = Auth::guard('client')->id();
        $discount = ProductDiscount::where('id', $id)->first();

        if (!$discount || $discount->productNew->client_id !== $clientId) {
            return redirect()->route('product.discounts.all')->with('error', 'Đợt giảm giá không tồn tại hoặc bạn không có quyền cập nhật.');
        }

        $validated = $request->validate([
            'product_news_id' => 'required|exists:product_news,id',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_price' => 'nullable|numeric|min:0',
            'start_at' => 'required|date_format:Y-m-d\TH:i',
            'end_at' => 'required|date_format:Y-m-d\TH:i|after:start_at',
        ], [
            'product_news_id.required' => 'Vui lòng chọn sản phẩm áp dụng giảm giá.',
            'product_news_id.exists' => 'Sản phẩm được chọn không tồn tại.',
            'discount_percent.numeric' => 'Phần trăm giảm giá phải là số.',
            'discount_percent.min' => 'Phần trăm giảm giá không thể âm.',
            'discount_percent.max' => 'Phần trăm giảm giá không thể lớn hơn 100.',
            'discount_price.numeric' => 'Giá giảm phải là số.',
            'discount_price.min' => 'Giá giảm không thể âm.',
            'start_at.required' => 'Ngày bắt đầu giảm giá là bắt buộc.',
            'start_at.date_format' => 'Ngày bắt đầu giảm giá không đúng định dạng (YYYY-MM-DD HH:MM).',
            'end_at.required' => 'Ngày kết thúc giảm giá là bắt buộc.',
            'end_at.date_format' => 'Ngày kết thúc giảm giá không đúng định dạng (YYYY-MM-DD HH:MM).',
            'end_at.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ]);

        if ($request->filled('discount_percent') && $request->filled('discount_price')) {
            return back()->withErrors(['discount_type' => 'Chỉ được phép nhập một trong hai: phần trăm giảm giá HOẶC giá giảm cố định.'])->withInput();
        }
        if (!$request->filled('discount_percent') && !$request->filled('discount_price')) {
            return back()->withErrors(['discount_type' => 'Vui lòng nhập phần trăm giảm giá HOẶC giá giảm cố định.'])->withInput();
        }

        $productNews = ProductNew::where('id', $validated['product_news_id'])
                                  ->where('client_id', $clientId)
                                  ->first();
        if (!$productNews) {
            return back()->withErrors(['product_news_id' => 'Sản phẩm bạn chọn không hợp lệ hoặc không thuộc về bạn.'])->withInput();
        }

        $discount->update([
            'product_news_id' => $validated['product_news_id'],
            'discount_percent' => $validated['discount_percent'] ?? null,
            'discount_price' => $validated['discount_price'] ?? null,
            'start_at' => Carbon::parse($validated['start_at']),
            'end_at' => Carbon::parse($validated['end_at']),
            'updated_at' => Carbon::now(),
        ]);

        $notification = [
            'message' => 'Đợt giảm giá đã được cập nhật thành công!',
            'alert-type' => 'success'
        ];

        return redirect()->route('product.discounts.all')->with($notification);
    }

    public function DeleteProductDiscount($id)
    {
        $clientId = Auth::guard('client')->id();
        $discount = ProductDiscount::where('id', $id)->first();

        if (!$discount || $discount->productNew->client_id !== $clientId) {
            return redirect()->route('product.discounts.all')->with('error', 'Đợt giảm giá không tồn tại hoặc bạn không có quyền xóa.');
        }

        try {
            $discount->delete();
            $notification = [
                'message' => 'Đợt giảm giá đã được xóa thành công!',
                'alert-type' => 'success'
            ];
        } catch (\Exception $e) {
            Log::error("Lỗi khi xóa ProductDiscount ID {$id}: " . $e->getMessage());
            $notification = [
                'message' => 'Có lỗi xảy ra khi xóa đợt giảm giá.',
                'alert-type' => 'error'
            ];
        }

        return redirect()->route('product.discounts.all')->with($notification);
    }

    public function AddProductMulti(){
        $menus = Menu::latest()->get();
        $categories = Category::latest()->get();
        $productTemplates = ProductTemplate::with('category')
                                         ->get();

        $productTemplatesGrouped = $productTemplates->groupBy('menu_id');

        // Bạn có thể không cần productStockModes riêng biệt nếu đã có trong productTemplatesGrouped
        // Nhưng nếu muốn một mảng riêng chỉ chứa ID và stock_mode:
        $productStockModes = $productTemplates->pluck('stock_mode', 'id');
        return view('client.backend.product.add_product_multi_to_stock', compact('productTemplatesGrouped', 'productStockModes', 'menus', 'categories'));
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