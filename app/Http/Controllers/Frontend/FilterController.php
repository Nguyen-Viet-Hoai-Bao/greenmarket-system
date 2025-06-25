<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; 

use App\Models\Product;
use App\Models\ProductNew;
use App\Models\Order;
use App\Models\Ward;
use App\Models\City;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    public function ListMarket(){
        // $products = ProductNew::with([
        //                             'productTemplate.menu',
        //                             'productTemplate.category'
        //                         ])->orderBy('id', 'desc')->get();
        $query = ProductNew::with([
            'productTemplate.menu',
            'productTemplate.category'
        ]);
    
        if (session()->has('selected_market_id')) {
            $selectedMarketId = session('selected_market_id');
            $query->where('client_id', $selectedMarketId); 
        }
    
        $products = $query->orderByDesc('most_popular') 
                            ->orderByDesc('best_seller')
                            ->orderBy('id', 'desc')->get();

        $fullAddress = null;
        if (session()->has('selected_market_ward_id')) {
            $ward = Ward::with('district.city')->find(session('selected_market_ward_id'));
        
            if ($ward && $ward->district && $ward->district->city) {
                $fullAddress = $ward->ward_name . ', ' 
                                . $ward->district->district_name . ', ' 
                                . $ward->district->city->city_name;
            }
        }

        $cities = City::all();

        $menusWithCategories = Menu::with(['categories'])->get();

        // For Footer
        $menus_footer = Menu::all();
        $topClientId = ProductNew::select('client_id', DB::raw('COUNT(*) as total'))
                                ->groupBy('client_id')
                                ->orderByDesc('total')
                                ->value('client_id'); 
        $products_list = ProductNew::with([
                        'productTemplate.menu',
                        'productTemplate.category'
                    ])
                    ->where('client_id', $topClientId)
                    ->orderBy('id', 'desc')
                    ->get();
        
        return view('frontend.list_market', compact('products', 'fullAddress', 'menusWithCategories', 'cities', 'menus_footer', 'products_list'));
    }
    //End Method 

    // public function FilterProducts(Request $request)
    // {
    //     $categoryId = $request->input('categorys'); // sửa lại đúng tên biến từ JS
    //     $menuId = $request->input('menus');

    //     $products = ProductNew::with([
    //         'productTemplate.menu',
    //         'productTemplate.category'
    //     ])->whereHas('productTemplate', function ($query) use ($categoryId, $menuId) {
    //         if ($categoryId) {
    //             $query->whereIn('category_id', $categoryId);
    //         }
    //         if ($menuId) {
    //             $query->whereIn('menu_id', $menuId);
    //         }
    //     });
    //     // Lọc theo market được chọn trong session
    //     if (session()->has('selected_market_id')) {
    //         $selectedMarketId = session('selected_market_id');
    //         $products->where('client_id', $selectedMarketId); // đổi nếu cột khác tên
    //     }

    //     $filterProducts = $products->orderByDesc('most_popular') 
    //                         ->orderByDesc('best_seller')
    //                         ->orderBy('id', 'desc')->get();

    //     return view('frontend.product_list', compact('filterProducts'))->render();
    // }
    // //End Method 

    protected function calculateFinalPrice($originalPrice, $unitDiscountPrice, $generalDiscount)
    {
        // Ưu tiên 1: discount_price trong ProductUnit
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
        // Không có giảm giá nào được áp dụng hoặc không hợp lệ, trả về giá gốc của ProductUnit
        return (float) $originalPrice;
    }

    // Hàm getValidProductUnit được giữ nguyên như bạn đã cung cấp ở đoạn trước
    // Hàm này sẽ sử dụng lại trong quá trình xử lý Collection, không phải trong query chính
    protected function getValidProductUnit($productUnits, $stockMode)
    {
        if ($stockMode === 'unit') {
            return $productUnits
                ->whereNotNull('weight')
                ->where('batch_qty', '>', 0)
                ->sortBy('expiry_date')
                ->first();
        } elseif ($stockMode === 'quantity') {
            return $productUnits
                ->whereNull('weight')
                ->where('batch_qty', '>', 0)
                ->sortBy('expiry_date')
                ->first();
        }

        return null;
    }

    public function FilterProducts(Request $request)
    {
        $categoryIds = $request->input('categories', []); // array
        $menuIds = $request->input('menus', []); // array
        $now = Carbon::now(); // Lấy thời gian hiện tại để so sánh với các khuyến mãi

        $productsQuery = ProductNew::with([
            'productTemplate.menu',
            'productTemplate.category',
            'productUnits' => function($query) {
                // Eager loading vẫn chỉ lấy các ProductUnit còn hàng
                $query->where(function($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function($q) {
                    // Cập nhật điều kiện is_sold_out = 0 cho chế độ 'unit'
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                })
                ->orderBy('expiry_date', 'asc'); // Ưu tiên HSD gần nhất
            },
            'productDiscounts' => function($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc');
            }
        ])
        // Điều kiện cho ProductNew
        ->where('status', 1) // Chỉ lấy sản phẩm có trạng thái hoạt động
        // Thêm whereHas để chỉ lấy ProductNew nếu có ít nhất 1 ProductUnit còn hàng
        ->whereHas('productUnits', function ($query) {
            $query->where(function ($q) {
                $q->whereNull('weight')->where('batch_qty', '>', 0);
            })->orWhere(function ($q) {
                // Đảm bảo logic lọc tương tự trong eager loading
                $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
            });
        })
        ->whereHas('productTemplate', function ($query) use ($categoryIds, $menuIds) {
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
            if (!empty($menuIds)) {
                $query->whereIn('menu_id', $menuIds);
            }
        });

        // Filter by market from session
        if (session()->has('selected_market_id')) {
            $selectedMarketId = session('selected_market_id');
            $productsQuery->where('client_id', $selectedMarketId);
        }

        $filterProducts = $productsQuery
            ->orderByDesc('most_popular')
            ->orderByDesc('best_seller')
            ->orderByDesc('id')
            ->get();

        // Áp dụng logic xử lý sản phẩm tương tự như đã làm cho bestsellers và products_all
        $processedFilterProducts = $filterProducts->map(function($product) use ($now) {
            $stockMode = $product->productTemplate->stock_mode ?? 'quantity';
            $activeGeneralDiscount = $product->productDiscounts->first();

            // Lấy product_unit có HSD gần nhất VÀ CÒN HÀNG thông qua hàm trợ giúp
            $firstAvailableUnit = $this->getValidProductUnit($product->productUnits, $stockMode);
            $basePrice = 0; // Giá cơ sở mặc định

            if ($firstAvailableUnit) {
                $basePrice = $firstAvailableUnit->sale_price; // Giá gốc là sale_price của unit HSD gần nhất
            }

            // Tính giá hiển thị cuối cùng
            $finalDisplayPrice = $this->calculateFinalPrice(
                $basePrice,
                $firstAvailableUnit ? $firstAvailableUnit->discount_price : null,
                $activeGeneralDiscount
            );

            // Gán các thông tin chung từ unit đầu tiên (còn hàng)
            $product->expiry_date = $firstAvailableUnit?->expiry_date;
            $product->weight = $firstAvailableUnit?->weight; // Nếu cần hiển thị weight cho chế độ quantity

            if ($stockMode === 'unit') {
                $product->display_mode = 'unit';
                // Chỉ lấy các unit có weight (phù hợp với chế độ unit) và còn hàng
                $product->available_units = $product->productUnits
                    ->whereNotNull('weight')
                    ->where('batch_qty', '>', 0) // Đảm bảo chỉ những unit còn hàng mới được đưa vào available_units
                    ->map(function($unit) use ($activeGeneralDiscount) {
                        $unit->final_sale_price = $this->calculateFinalPrice(
                            $unit->sale_price,
                            $unit->discount_price,
                            $activeGeneralDiscount
                        );
                        return $unit;
                    });
                $product->display_unit_price = $finalDisplayPrice;
                $product->display_unit_original_price = $basePrice;
            } elseif ($stockMode === 'quantity') {
                $product->display_mode = 'quantity';
                // Tổng số lượng còn hàng của các unit (chỉ những unit phù hợp với chế độ quantity)
                $product->total_available_quantity = $product->productUnits->whereNull('weight')->sum('batch_qty');
                $product->final_display_price = $finalDisplayPrice;
                $product->display_original_price = $basePrice;
            }

            return $product;
        });

        // Truyền biến đã xử lý vào view
        return view('frontend.filter_product', compact('processedFilterProducts'))->render();
    }
    // public function FilterProducts(Request $request)
    // {
    //     $categoryIds = $request->input('categories', []); // array
    //     $menuIds = $request->input('menus', []); // array

    //     $productsQuery = ProductNew::with([
    //         'productTemplate.menu',
    //         'productTemplate.category'
    //         ])->where('qty', '>', 0)
    //         ->where('status', 1)
    //         ->whereHas('productTemplate', function ($query) use ($categoryIds, $menuIds) {
    //             if (!empty($categoryIds)) {
    //                 $query->whereIn('category_id', $categoryIds);
    //             }
    //             if (!empty($menuIds)) {
    //                 $query->whereIn('menu_id', $menuIds);
    //             }
    //     });

    //     // Filter by market from session
    //     if (session()->has('selected_market_id')) {
    //         $selectedMarketId = session('selected_market_id');
    //         $productsQuery->where('client_id', $selectedMarketId);
    //     }

    //     $filterProducts = $productsQuery
    //         ->orderByDesc('most_popular')
    //         ->orderByDesc('best_seller')
    //         ->orderByDesc('id')
    //         ->get();

    //     return view('frontend.filter_product', compact('filterProducts'))->render();
    // }

    public function ProductDetail($id)
    {
        $now = Carbon::now();

        // Eager load các mối quan hệ cần thiết cho sản phẩm chính
        // Lấy productUnits với ưu tiên HSD gần nhất và chỉ lấy unit CÒN HÀNG
        $product = ProductNew::with([
            'productTemplate.productDetail',
            'productUnits' => function($query) {
                $query->where(function($q) {
                    // Lọc những unit có số lượng > 0 hoặc trọng lượng > 0
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function($q) {
                    $q->whereNotNull('weight')->where('weight', '>', 0);
                })
                ->orderBy('expiry_date', 'asc') // Sắp xếp theo HSD gần nhất
                ->limit(1); // CHỈ LẤY MỘT ĐƠN VỊ CÓ HSD GẦN NHẤT
            },
            'productDiscounts' => function($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc'); // Lấy khuyến mãi mới nhất
            }
        ])->find($id);

        if (!$product) {
            abort(404);
        }

        // --- Xử lý sản phẩm chính ---
        // Hàm processProduct sẽ nhận biết rằng productUnits chỉ chứa 1 phần tử (hoặc rỗng)
        $processedProduct = $this->processProduct($product, $now);

        // Lấy productDetail từ productTemplate
        $productDetail = $processedProduct->productTemplate->productDetail;

        // ----- Reviews cho sản phẩm -----
        $reviews = ProductReview::where('product_id', $product->id)
                                ->where('status', 1)
                                ->get();

        $totalReviews = $reviews->count();
        $ratingSum = $reviews->sum('rating');
        $averageRating = $totalReviews > 0 ? $ratingSum / $totalReviews : 0;
        $roundedAverageRating = round($averageRating, 1);

        $ratingCounts = [
            '5' => $reviews->where('rating', 5)->count(),
            '4' => $reviews->where('rating', 4)->count(),
            '3' => $reviews->where('rating', 3)->count(),
            '2' => $reviews->where('rating', 2)->count(),
            '1' => $reviews->where('rating', 1)->count(),
        ];

        $ratingPercentages = array_map(function ($count) use ($totalReviews) {
            return $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
        }, $ratingCounts);

        // ----- Dữ liệu dùng chung -----
        $cities = City::all();
        $menus_footer = Menu::all();

        $topClientId = ProductNew::select('client_id', DB::raw('COUNT(*) as total'))
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->value('client_id');

        // Eager load và xử lý cho products_list (các sản phẩm liên quan)
        // Phần này giữ nguyên như sửa đổi trước đó cho filter, vì danh sách liên quan vẫn cần hiển thị nhiều sản phẩm
        $products_list = ProductNew::with([
                'productTemplate.menu',
                'productTemplate.category',
                'productUnits' => function($query) {
                    $query->where(function($q) {
                        $q->whereNull('weight')->where('batch_qty', '>', 0);
                    })->orWhere(function($q) {
                        $q->whereNotNull('weight')->where('weight', '>', 0);
                    })
                    ->orderBy('expiry_date', 'asc');
                },
                'productDiscounts' => function($query) use ($now) {
                    $query->where('start_at', '<=', $now)
                          ->where('end_at', '>=', $now)
                          ->orderBy('created_at', 'desc');
                }
            ])
            ->where('client_id', $topClientId)
            ->where('id', '!=', $id) // Không hiển thị lại sản phẩm đang xem
            ->whereHas('productUnits', function ($query) {
                // Đảm bảo chỉ lấy ProductNew nào có ít nhất 1 ProductUnit còn hàng
                $query->where(function($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function($q) {
                    $q->whereNotNull('weight')->where('weight', '>', 0);
                });
            })
            ->orderBy('id', 'desc')
            ->limit(8) // Giới hạn số lượng sản phẩm liên quan hiển thị
            ->get();

        // Xử lý từng sản phẩm trong products_list
        $processedProductsList = $products_list->map(function($productItem) use ($now) {
            return $this->processProduct($productItem, $now);
        });
        return view('frontend.product_detail', compact(
            'processedProduct',
            'productDetail',
            'cities',
            'menus_footer',
            'processedProductsList',
            'products_list',
            'reviews',
            'roundedAverageRating',
            'totalReviews',
            'ratingCounts',
            'ratingPercentages'
        ));
    }

    /**
     * Hàm trợ giúp để xử lý logic giá và tồn kho cho một ProductNew.
     * Hàm này cần được điều chỉnh để ưu tiên HSD gần nhất.
     */
    private function processProduct($product, $now)
    {
        $stockMode = $product->productTemplate->stock_mode ?? 'quantity';
        $activeGeneralDiscount = $product->productDiscounts->first();

        // **Lưu ý:** productUnits đã được eager load và chỉ có 1 phần tử (hoặc rỗng) nếu là sản phẩm chính
        $firstAvailableUnit = $product->productUnits->first(); // Luôn lấy phần tử đầu tiên (duy nhất hoặc null)

        $basePrice = $firstAvailableUnit ? $firstAvailableUnit->sale_price : 0;

        $finalDisplayPrice = $this->calculateFinalPrice(
            $basePrice,
            $firstAvailableUnit ? $firstAvailableUnit->discount_price : null,
            $activeGeneralDiscount
        );

        $product->display_mode = $stockMode;
        $product->final_display_price = $finalDisplayPrice;
        $product->display_original_price = $basePrice;
        $product->has_stock = false; // Mặc định là không có hàng

        if ($firstAvailableUnit) { // Nếu có ít nhất một đơn vị còn hàng
            $product->has_stock = true;

            if ($stockMode === 'unit') {
                $product->display_unit_price = $finalDisplayPrice; // Giá cuối cùng của unit đó
                $product->display_unit_original_price = $basePrice; // Giá gốc của unit đó
                $product->selected_unit = $firstAvailableUnit; // Lưu trữ unit được chọn để dễ truy cập trong view
            } elseif ($stockMode === 'quantity') {
                $product->total_available_quantity = $firstAvailableUnit->batch_qty; // Số lượng của đơn vị đó
            }
        }

        return $product;
    }

    // public function SearchProducts(Request $request)
    // {
    //     $search = $request->input('query');

    //     $marketId = session('selected_market_id');
        
    //     // Lấy product theo tên search và market
    //     $products_search = ProductNew::with(['productTemplate.menu', 'productTemplate.category'])
    //         ->where('client_id', $marketId)
    //         ->where('status', 1)
    //         ->whereHas('productTemplate', function ($query) use ($search) {
    //             $query->where('name', 'LIKE', "%{$search}%");
    //         })
    //         ->orderByDesc('most_popular')
    //         ->orderByDesc('best_seller')
    //         ->orderByDesc('id')
    //         ->get();

    //     // Lấy các category ID của các product tìm được
    //     $categoryIds = $products_search->pluck('productTemplate.category_id')->unique()->toArray();
    //     $menuIdsToExpand = $products_search->pluck('productTemplate.menu_id')->unique()->toArray();

    //     // Lấy danh sách menu và category (chỉ giữ các category có trong $categoryIds)
    //     // $menusWithCategories = Menu::with(['categories' => function ($query) use ($categoryIds) {
    //     //     $query->whereIn('id', $categoryIds);
    //     // }])->get();
    //     $menusWithCategories = Menu::with(['categories'])->get();
    //     $products_all = ProductNew::with([
    //                     'productTemplate.menu',
    //                     'productTemplate.category'
    //                 ])
    //                 ->where('client_id', session('selected_market_id'))
    //                 ->orderBy('id', 'desc')
    //                 ->get();
    //     $isEmpty = false;
    //     if ($products_search->isEmpty()) {
    //         $notification = array(
    //             'message' => 'Không tìm thấy sản phẩm nào phù hợp. Vui lòng thử từ khóa khác hoặc xem các sản phẩm bên dưới.',
    //             'alert-type' => 'error'
    //         );
    //         $isEmpty = true;
    //         $products_search = ProductNew::with([
    //                     'productTemplate.menu',
    //                     'productTemplate.category'
    //                 ])
    //                 ->where('client_id', session('selected_market_id'))
    //                 ->where('status', 1)
    //                 ->where('qty', '>', 0)
    //                 ->orderBy('id', 'desc')
    //                 ->get();

    //         return view('frontend.search_product', compact('products_all', 'products_search', 'menusWithCategories', 'categoryIds', 'menuIdsToExpand', 'isEmpty'))
    //             ->with($notification);
    //     }

    //     return view('frontend.search_product', compact('products_all', 'products_search', 'menusWithCategories', 'categoryIds', 'menuIdsToExpand', 'isEmpty'))
    //                 ->render();
    // }

    protected function calculateFinalPriceSearch($originalPrice, $unitDiscountPrice, $generalDiscount)
    {
        $finalPrice = (float) $originalPrice; // Bắt đầu với giá gốc

        // Ưu tiên 1: Áp dụng discount_price từ ProductUnit nếu có và hợp lệ
        // (nhỏ hơn giá gốc và không âm)
        if ($unitDiscountPrice !== null && is_numeric($unitDiscountPrice) && $unitDiscountPrice >= 0 && $unitDiscountPrice < $finalPrice) {
            $finalPrice = (float) $unitDiscountPrice;
            // Nếu tìm thấy unitDiscountPrice hợp lệ, ưu tiên nó và trả về ngay
            return max(0, round($finalPrice));
        }

        // Ưu tiên 2: Áp dụng ProductDiscount chung
        if ($generalDiscount) {
            $discountAmount = 0; // Số tiền được giảm

            // Nếu có discount_percent
            if ($generalDiscount->discount_percent !== null && $generalDiscount->discount_percent > 0) {
                $discountAmount = $originalPrice * ($generalDiscount->discount_percent / 100);
            } 
            // Nếu không có discount_percent nhưng có discount_price (giá trị giảm cố định)
            elseif ($generalDiscount->discount_price !== null && $generalDiscount->discount_price > 0) {
                // Đây là số tiền giảm cố định, không phải giá cuối cùng
                $discountAmount = $generalDiscount->discount_price;
            }

            // Áp dụng giới hạn giảm giá tối đa (max_discount_amount)
            if ($generalDiscount->max_discount_amount !== null && $generalDiscount->max_discount_amount > 0 && $discountAmount > $generalDiscount->max_discount_amount) {
                $discountAmount = $generalDiscount->max_discount_amount;
            }
            
            // Tính giá sau khi áp dụng giảm giá chung
            $priceAfterGeneralDiscount = $originalPrice - $discountAmount;
            
            // So sánh giá sau giảm giá chung với giá hiện tại (đang là originalPrice vì unitDiscount đã bị bỏ qua)
            // Lấy giá thấp hơn (vì unitDiscount đã được ưu tiên ở trên và thoát hàm)
            $finalPrice = min($finalPrice, $priceAfterGeneralDiscount);
        }

        // Đảm bảo giá không âm và làm tròn
        return max(0, round($finalPrice));
    }

    /**
     * Hàm trợ giúp để xử lý logic giá và tồn kho cho một ProductNew.
     * Hàm này ưu tiên ProductUnit có HSD gần nhất.
     * Hoặc tìm ProductUnit cụ thể nếu được cung cấp productUnitId.
     */
    private function processProductForCart($product, $now, $specificProductUnitId = null)
    {
        $stockMode = $product->productTemplate->stock_mode ?? 'quantity';
        // Lấy khuyến mãi chung đang hoạt động, cần đảm bảo chỉ lấy một cái duy nhất
        $activeGeneralDiscount = $product->productDiscounts->first(); 

        $selectedUnit = null;
        // Nếu có ProductUnit ID cụ thể, tìm unit đó
        if ($specificProductUnitId) {
            foreach ($product->productUnits as $unit) {
                if ($unit->id == $specificProductUnitId) {
                    $selectedUnit = $unit;
                    break;
                }
            }
        } else {
            // Nếu không có unit ID cụ thể, lấy unit có HSD gần nhất và còn hàng
            $availableUnits = $product->productUnits->filter(function($unit) use ($stockMode) {
                return ($stockMode === 'unit' && ($unit->weight > 0 || $unit->batch_qty > 0)) ||
                       ($stockMode === 'quantity' && $unit->batch_qty > 0);
            });
            if ($availableUnits->isNotEmpty()) {
                $selectedUnit = $availableUnits->sortBy('expiry_date')->first();
            }
        }

        $basePrice = $selectedUnit ? $selectedUnit->sale_price : 0;
        $unitDiscountPrice = $selectedUnit ? $selectedUnit->discount_price : null;

        $finalDisplayPrice = $this->calculateFinalPriceSearch(
            $basePrice,
            $unitDiscountPrice,
            $activeGeneralDiscount
        );

        $product->display_mode = $stockMode;
        $product->final_display_price = $finalDisplayPrice;
        $product->display_original_price = $basePrice; // Giữ nguyên giá gốc để hiển thị
        $product->has_stock = false;
        $product->selected_unit = $selectedUnit; // Gán unit được chọn

        if ($selectedUnit) {
            if ($stockMode === 'unit' && ($selectedUnit->weight > 0 || $selectedUnit->batch_qty > 0)) {
                $product->has_stock = true;
                $product->display_unit_price = $finalDisplayPrice;
                $product->display_unit_original_price = $basePrice;
            } elseif ($stockMode === 'quantity' && $selectedUnit->batch_qty > 0) {
                $product->has_stock = true;
                $product->total_available_quantity = $selectedUnit->batch_qty;
            }
        }

        return $product;
    }

    protected function processProductForSearch($product, $now)
    {
        $stockMode = $product->productTemplate->stock_mode ?? 'quantity';
        $activeGeneralDiscount = $product->productDiscounts->first();

        // Lấy product_unit có HSD gần nhất VÀ CÒN HÀNG (đã được lọc qua eager loading)
        $firstAvailableUnit = null;
        if ($stockMode === 'unit') {
            $firstAvailableUnit = $product->productUnits
                ->whereNotNull('weight')
                ->where('is_sold_out', 0) // Quan trọng: thêm điều kiện này
                ->where('batch_qty', '>', 0) // Hoặc điều kiện số lượng > 0
                ->sortBy('expiry_date')
                ->first();
        } elseif ($stockMode === 'quantity') {
            $firstAvailableUnit = $product->productUnits
                ->whereNull('weight')
                ->where('batch_qty', '>', 0)
                ->sortBy('expiry_date')
                ->first();
        }

        $basePrice = 0;
        $product->has_stock = false; // Mặc định là không có hàng

        if ($firstAvailableUnit) {
            $basePrice = $firstAvailableUnit->sale_price;
            $product->has_stock = true; // Đánh dấu là có hàng nếu tìm thấy unit hợp lệ
        }

        $finalDisplayPrice = $this->calculateFinalPrice(
            $basePrice,
            $firstAvailableUnit ? $firstAvailableUnit->discount_price : null,
            $activeGeneralDiscount
        );

        // Gán các thông tin cần hiển thị
        $product->expiry_date = $firstAvailableUnit?->expiry_date;
        $product->weight = $firstAvailableUnit?->weight; // Nếu cần hiển thị weight cho chế độ quantity

        if ($stockMode === 'unit') {
            $product->display_mode = 'unit';
            $product->available_units = $product->productUnits
                ->whereNotNull('weight')
                ->where('is_sold_out', 0) // Chỉ hiển thị các unit còn hàng
                ->where('batch_qty', '>', 0)
                ->map(function($unit) use ($activeGeneralDiscount) {
                    $unit->final_sale_price = $this->calculateFinalPrice(
                        $unit->sale_price,
                        $unit->discount_price,
                        $activeGeneralDiscount
                    );
                    return $unit;
                });
            $product->display_unit_price = $finalDisplayPrice;
            $product->display_unit_original_price = $basePrice;
        } elseif ($stockMode === 'quantity') {
            $product->display_mode = 'quantity';
            $product->total_available_quantity = $product->productUnits->whereNull('weight')->sum('batch_qty');
            $product->final_display_price = $finalDisplayPrice;
            $product->display_original_price = $basePrice;
        }
        
        $product->final_display_price = $finalDisplayPrice;

        return $product;
    }

    public function SearchProducts(Request $request)
    {
        $search = $request->input('query');
        $marketId = session('selected_market_id');
        $now = Carbon::now(); 

        // --- Truy vấn sản phẩm tìm kiếm ---
        $products_search_query = ProductNew::with([
            'productTemplate.menu',
            'productTemplate.category',
            'productUnits' => function($query) {
                // Eager loading chỉ tải các product units CÒN HÀNG
                $query->where(function($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function($q) {
                    // Thêm điều kiện is_sold_out = 0 cho chế độ 'unit'
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                })
                ->orderBy('expiry_date', 'asc'); // Sắp xếp để dễ chọn unit có HSD gần nhất
            },
            'productDiscounts' => function($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc'); // Lấy khuyến mãi mới nhất
            }
        ])
        ->where('client_id', $marketId)
        ->where('status', 1)
        // THÊM whereHas ĐỂ CHỈ LẤY PRODUCTNEW NẾU CÓ ÍT NHẤT 1 PRODUCTUNIT CÒN HÀNG
        ->whereHas('productUnits', function ($query) {
            $query->where(function ($q) {
                $q->whereNull('weight')->where('batch_qty', '>', 0);
            })->orWhere(function ($q) {
                $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
            });
        })
        ->whereHas('productTemplate', function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        });

        $products_search = $products_search_query->get()->map(function ($product) use ($now) {
            // Xử lý từng sản phẩm để tính toán giá cuối cùng và kiểm tra tồn kho
            return $this->processProductForSearch($product, $now);
        })->filter(function ($product) {
            // Lọc ra những sản phẩm không còn hàng hoặc giá hiển thị bằng 0 (có thể do lỗi tính toán hoặc không có unit)
            return $product->has_stock && $product->final_display_price > 0;
        })
        ->sortByDesc('most_popular')
        ->sortByDesc('best_seller')
        ->sortByDesc('id'); // Sắp xếp sau khi đã xử lý giá và tồn kho

        // Lấy các category ID và menu ID của các product tìm được (sau khi đã lọc)
        $categoryIds = $products_search->pluck('productTemplate.category_id')->unique()->toArray();
        $menuIdsToExpand = $products_search->pluck('productTemplate.menu_id')->unique()->toArray();

        // Lấy danh sách menu và category (hiển thị tất cả menus và categories)
        $menusWithCategories = Menu::with(['categories'])->get();
        
        // --- Truy vấn tất cả sản phẩm của thị trường hiện tại (cho gợi ý nếu không tìm thấy) ---
        $products_all_query = ProductNew::with([
            'productTemplate.menu',
            'productTemplate.category',
            'productUnits' => function($query) {
                // Eager loading chỉ tải các product units CÒN HÀNG
                $query->where(function($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function($q) {
                    // Thêm điều kiện is_sold_out = 0 cho chế độ 'unit'
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                })
                ->orderBy('expiry_date', 'asc');
            },
            'productDiscounts' => function($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc');
            }
        ])
        ->where('client_id', session('selected_market_id'))
        ->where('status', 1)
        // THÊM whereHas TƯƠNG TỰ ĐỂ CHỈ LẤY PRODUCTNEW NẾU CÓ ÍT NHẤT 1 PRODUCTUNIT CÒN HÀNG
        ->whereHas('productUnits', function ($query) {
            $query->where(function ($q) {
                $q->whereNull('weight')->where('batch_qty', '>', 0);
            })->orWhere(function ($q) {
                $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
            });
        })
        ->orderByDesc('id');

        $products_all = $products_all_query->get()->map(function ($product) use ($now) {
            return $this->processProductForSearch($product, $now);
        })->filter(function ($product) {
            return $product->has_stock && $product->final_display_price > 0;
        }); // Sắp xếp sau khi xử lý

        $isEmpty = $products_search->isEmpty();

        if ($isEmpty) {
            $notification = [
                'message' => 'Không tìm thấy sản phẩm nào phù hợp. Vui lòng thử từ khóa khác hoặc xem các sản phẩm bên dưới.',
                'alert-type' => 'error'
            ];
            return view('frontend.search_product', compact('products_all', 'products_search', 'menusWithCategories', 'categoryIds', 'menuIdsToExpand', 'isEmpty'))
                ->with($notification);
        }

        return view('frontend.search_product', compact('products_all', 'products_search', 'menusWithCategories', 'categoryIds', 'menuIdsToExpand', 'isEmpty'));
    }
    
    public function getProductDetails($id)
    {
        $now = Carbon::now();

        $product = ProductNew::with([
            'productTemplate',
            'productUnits' => function($query) {
                $query->where(function($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function($q) {
                    $q->whereNotNull('weight')->where('weight', '>', 0);
                })
                ->orderBy('expiry_date', 'asc'); // Sắp xếp theo HSD gần nhất
            },
            'productDiscounts' => function($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc'); // Hoặc theo mức giảm giá, tùy logic ưu tiên của bạn
            }
        ])->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $stockMode = $product->productTemplate->stock_mode ?? 'quantity';
        $activeGeneralDiscount = $product->productDiscounts->first(); // Lấy khuyến mãi chung đầu tiên

        $selectedUnit = null;
        if ($product->productUnits->isNotEmpty()) {
            $selectedUnit = $product->productUnits->first();
        }

        $basePrice = $selectedUnit ? $selectedUnit->sale_price : 0;
        $unitDiscountPrice = $selectedUnit ? $selectedUnit->discount_price : null;

        $finalDisplayPrice = $this->calculateFinalPrice(
            $basePrice,
            $unitDiscountPrice,
            $activeGeneralDiscount
        );

        $data = [
            'id' => $product->id,
            'name' => $product->productTemplate->name, // Ưu tiên tên từ template
            'description' => $product->productTemplate->productDetail->description ?? $product->description,
            'image' => $product->productTemplate->image ?? $product->image, // Ưu tiên ảnh từ template
            'price' => $finalDisplayPrice, // Giá cuối cùng đã tính toán
            'original_price' => $basePrice, // Giá gốc (chưa giảm)
            'display_mode' => $stockMode,
            'has_stock' => (bool) $selectedUnit, // Kiểm tra xem có unit nào được chọn không (tức là còn hàng)
            'client_id' => $product->client_id, // Nếu cần thông tin client_id

            'product_info' => $product->productTemplate->productDetail->product_info ?? $product->product_info,
            'note' => $product->productTemplate->productDetail->note ?? $product->note,
            'origin' => $product->productTemplate->productDetail->origin ?? $product->origin,
            'preservation' => $product->productTemplate->productDetail->preservation ?? $product->preservation,
            'usage_instructions' => $product->productTemplate->productDetail->usage_instructions ?? $product->usage_instructions,

            'selected_unit' => null, // Thông tin unit được chọn (nếu có)
            'active_discount' => null, // Thông tin khuyến mãi đang áp dụng (nếu có)
        ];

        if ($selectedUnit) {
            $data['selected_unit'] = [
                'id' => $selectedUnit->id,
                'sale_price' => $selectedUnit->sale_price,
                'discount_price' => $selectedUnit->discount_price,
                'expiry_date' => $selectedUnit->expiry_date ? Carbon::parse($selectedUnit->expiry_date)->format('d/m/Y') : null,
                'weight' => $selectedUnit->weight,
                'batch_qty' => $selectedUnit->batch_qty,
            ];
            if ($stockMode === 'quantity') {
                $data['total_available_quantity'] = $selectedUnit->batch_qty;
            }
        }

        if ($activeGeneralDiscount) {
            $data['active_discount'] = [
                'name' => $activeGeneralDiscount->name,
                'discount_percent' => $activeGeneralDiscount->discount_percent,
                'discount_price_value' => $activeGeneralDiscount->discount_price, // Giá trị giảm cố định
                'max_discount_amount' => $activeGeneralDiscount->max_discount_amount,
            ];
        }

        return response()->json($data);
    }
}
