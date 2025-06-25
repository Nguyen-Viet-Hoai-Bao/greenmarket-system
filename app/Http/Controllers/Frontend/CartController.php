<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\ProductNew;
use App\Models\Coupon;
use App\Models\Client;
use App\Models\Menu;
use App\Models\Order;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected function calculateFinalPrice($originalPrice, $unitDiscountPrice, $generalDiscount)
    {
        $finalPrice = (float) $originalPrice; // Bắt đầu với giá gốc

        if ($unitDiscountPrice !== null && is_numeric($unitDiscountPrice) && $unitDiscountPrice >= 0 && $unitDiscountPrice < $finalPrice) {
            $finalPrice = (float) $unitDiscountPrice;
            return max(0, round($finalPrice));
        }

        if ($generalDiscount) {
            $discountAmount = 0; // Số tiền được giảm

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

        $finalDisplayPrice = $this->calculateFinalPrice(
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

    // public function AddToCart($id) {
    //     $now = Carbon::now();
    //     $product = ProductNew::with([
    //         'productTemplate',
    //         'productUnits' => function($query) {
    //             $query->where(function($q) {
    //                 $q->whereNull('weight')->where('batch_qty', '>', 0);
    //             })->orWhere(function($q) {
    //                 $q->whereNotNull('weight')->where('weight', '>', 0);
    //             })
    //             ->orderBy('expiry_date', 'asc')
    //             ->limit(1); 
    //         },
    //         'productDiscounts' => function($query) use ($now) {
    //             $query->where('start_at', '<=', $now)
    //                 ->where('end_at', '>=', $now)
    //                 ->orderBy('created_at', 'desc');
    //         }
    //     ])->find($id);

    //     if (!$product) {
    //         return redirect()->back()->with([
    //             'message' => 'Sản phẩm không tìm thấy.',
    //             'alert-type' => 'error'
    //         ]);
    //     }

    //     $processedProduct = $this->processProductForCart($product, $now);

    //     if (!$processedProduct->has_stock || !$processedProduct->selected_unit) { // Kiểm tra ProductUnit có tồn tại và còn hàng không
    //         return redirect()->back()->with([
    //             'message' => 'Sản phẩm này hiện đã hết hàng hoặc không có đơn vị hợp lệ.',
    //             'alert-type' => 'error'
    //         ]);
    //     }

    //     $selectedMarketId = session('selected_market_id');
    //     if ($processedProduct->client_id != $selectedMarketId) {
    //         return redirect()->back()->with([
    //             'message' => 'Sản phẩm không thuộc cửa hàng hiện tại.',
    //             'alert-type' => 'error'
    //         ]);
    //     }

    //     $cart = session()->get('cart', []);
        
    //     // **THAY ĐỔI LỚN TẠI ĐÂY:** Khóa giỏ hàng sẽ là ProductNewId + ProductUnitId
    //     $itemKey = $id . '_' . $processedProduct->selected_unit->id; // Ví dụ: "48_123"

    //     if ($processedProduct->display_mode === 'unit') {
    //         if (isset($cart[$itemKey])) {
    //             return redirect()->back()->with([
    //                 'message' => 'Đơn vị sản phẩm này (HSD: ' . Carbon::parse($processedProduct->selected_unit->expiry_date)->format('d/m/Y') . ') đã có trong giỏ hàng.',
    //                 'alert-type' => 'info'
    //             ]);
    //         }
    //         // Nếu chưa có, thêm với quantity = 1
    //         $cart[$itemKey] = [
    //             'id' => $id, // ProductNew ID
    //             'product_unit_id' => $processedProduct->selected_unit->id,
    //             'name' => $processedProduct->productTemplate->name,
    //             'image' => $processedProduct->productTemplate->image,
    //             'price' => $processedProduct->final_display_price, // Giá cuối cùng của unit đó
    //             'client_id' => $processedProduct->client_id,
    //             'quantity' => 1, // Luôn là 1 cho chế độ 'unit'
    //             'menu_name' => optional($processedProduct->productTemplate->menu)->name ?? null,
    //             'expiry_date' => $processedProduct->selected_unit->expiry_date,
    //             'weight' => $processedProduct->selected_unit->weight,
    //             'unit_batch_qty' => null, // Không dùng trong mode 'unit'
    //             'display_mode' => 'unit'
    //         ];

    //     } elseif ($processedProduct->display_mode === 'quantity') {
    //         $itemKey = $id; // Trong chế độ quantity, khóa chỉ là ProductNewId

    //         // Lưu ProductUnitId của unit được chọn để dùng khi recalculate hoặc update
    //         $selectedProductUnitId = $processedProduct->selected_unit ? $processedProduct->selected_unit->id : null;

    //         if (isset($cart[$itemKey])) {
    //             $currentQuantity = $cart[$itemKey]['quantity'];
    //             $newQuantity = $currentQuantity + 1;

    //             if ($newQuantity > $processedProduct->total_available_quantity) {
    //                 return redirect()->back()->with([
    //                     'message' => 'Cửa hàng chỉ còn ' . $processedProduct->total_available_quantity . ' ' . ($processedProduct->productTemplate->unit ?? 'sản phẩm') . '.',
    //                     'alert-type' => 'error'
    //                 ]);
    //             }
    //             $cart[$itemKey]['quantity'] = $newQuantity;
    //             // Cập nhật giá và ProductUnitId nếu đơn vị được chọn thay đổi (ví dụ: do HSD gần nhất)
    //             $cart[$itemKey]['price'] = $processedProduct->final_display_price;
    //             $cart[$itemKey]['product_unit_id'] = $selectedProductUnitId;

    //         } else {
    //             $cart[$itemKey] = [
    //                 'id' => $id, // ProductNew ID
    //                 'product_unit_id' => $selectedProductUnitId, // Lưu ProductUnitId được chọn
    //                 'name' => $processedProduct->productTemplate->name,
    //                 'image' => $processedProduct->productTemplate->image,
    //                 'price' => $processedProduct->final_display_price,
    //                 'client_id' => $processedProduct->client_id,
    //                 'quantity' => 1,
    //                 'menu_name' => optional($processedProduct->productTemplate->menu)->name ?? null,
    //                 'expiry_date' => $processedProduct->selected_unit ? $processedProduct->selected_unit->expiry_date : null,
    //                 'weight' => $processedProduct->selected_unit ? $processedProduct->selected_unit->weight : null,
    //                 'unit_batch_qty' => $processedProduct->selected_unit ? $processedProduct->selected_unit->batch_qty : null,
    //                 'display_mode' => 'quantity'
    //             ];
    //         }
    //     }

    //     session()->put('cart', $cart);
    //     $this->recalculateCoupon();

    //     return redirect()->back()->with([
    //         'message' => 'Thêm vào giỏ hàng thành công.',
    //         'alert-type' => 'success'
    //     ]);
    // }

    // =================================================================
    public function AjaxAddToCartWithUnit(Request $request, $productId)
    {
        $productUnitId = $request->input('product_unit_id');
        $quantity = (int) $request->input('quantity', 1);

        if ($quantity <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Số lượng không hợp lệ.'], 400);
        }

        $now = Carbon::now();
        $product = ProductNew::with([
            'productUnits' => function ($query) use ($productUnitId) {
                $query->where('id', $productUnitId); // Chỉ lấy unit đã chọn
            },
            'productTemplate',
            'productDiscounts' => function ($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc');
            }
        ])->find($productId);

        if (!$product || $product->productUnits->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm hoặc đơn vị đã chọn không tồn tại.'], 404);
        }

        $processedProduct = $this->processProductForCart($product, $now, $productUnitId);

        if (!$processedProduct->has_stock || !$processedProduct->selected_unit || $processedProduct->final_display_price <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm hoặc đơn vị đã chọn không còn hàng hoặc không hợp lệ.'], 400);
        }

        $cart = session()->get('cart', []);

        // Tạo itemKey dựa trên display_mode và product_unit_id để duy nhất trong giỏ hàng
        $itemKey = ($processedProduct->display_mode === 'unit')
                         ? $productId . '_' . $processedProduct->selected_unit->id
                         : $productId; // Đối với mode 'quantity', key có thể chỉ là productId

        // Kiểm tra số lượng tồn kho trước khi thêm/cập nhật
        $currentCartQuantity = $cart[$itemKey]['quantity'] ?? 0;
        $requestedTotalQuantity = $currentCartQuantity + $quantity; // Tổng số lượng sẽ có trong giỏ

        // Kiểm tra theo display_mode của processedProduct->selected_unit
        if ($processedProduct->display_mode === 'unit') {
            // Ở chế độ 'unit', mỗi item trong giỏ là một ProductUnit duy nhất.
            // Số lượng của item đó trong giỏ luôn là 1. Không cho phép tăng quá 1.
            if ($requestedTotalQuantity > 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đơn vị sản phẩm này không thể tăng số lượng. Vui lòng thêm một đơn vị khác nếu có.',
                ], 400);
            }
        } elseif ($processedProduct->display_mode === 'quantity') {
            // Ở chế độ 'quantity', số lượng có thể tăng, nhưng không vượt quá tồn kho
            if ($requestedTotalQuantity > $processedProduct->total_available_quantity) {
                 return response()->json([
                    'status' => 'error',
                    'message' => 'Cửa hàng chỉ còn ' . $processedProduct->total_available_quantity . ' ' . ($processedProduct->productTemplate->unit ?? 'sản phẩm') . '.',
                ], 400);
            }
        }

        // Cập nhật hoặc thêm vào giỏ hàng
        $cart[$itemKey] = [
            "id" => $productId,
            "product_unit_id" => $processedProduct->selected_unit->id, // Luôn lưu unit ID đã chọn
            'client_id' => $processedProduct->client_id,
            "name" => $processedProduct->productTemplate->name,
            "quantity" => $requestedTotalQuantity,
            "price" => $processedProduct->final_display_price,
            "image" => $processedProduct->productTemplate->image,
            "display_mode" => $processedProduct->display_mode,
            "weight" => $processedProduct->selected_unit->weight,
            "expiry_date" => $processedProduct->selected_unit->expiry_date,
            "unit_batch_qty" => $processedProduct->selected_unit->batch_qty, // Thêm thông tin này
            "unit_price" => $processedProduct->selected_unit->price, // Có thể thêm giá gốc của unit
            // ... các thông tin khác bạn muốn lưu
        ];

        session()->put('cart', $cart);
        $this->recalculateCoupon();

        return response()->json([
            'status' => 'success',
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng.',
            'cartItem' => $cart[$itemKey] // Trả về thông tin item trong giỏ để JS cập nhật
        ]);
    }
    public function getProductDetailsForCart($id)
    {
        $now = Carbon::now();
        $product = ProductNew::with([
            'productUnits' => function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                            $q->whereNull('expiry_date')
                              ->orWhere('expiry_date', '>', $now);
                        })
                      ->where(function ($q) {
                            $q->where('batch_qty', '>', 0)
                              ->orWhere('weight', '>', 0);
                      })
                      ->orderBy('expiry_date', 'asc');
            },
            'productTemplate',
            'productDiscounts' => function ($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc');
            }
        ])->find($id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại.'], 404);
        }

        // Lấy danh sách các Product Unit ID hiện có trong giỏ hàng cho sản phẩm này
        $cart = session()->get('cart', []);
        $existingProductUnitIdsInCart = [];
        foreach ($cart as $itemKey => $cartItem) {
            // Kiểm tra xem itemKey có phải là của sản phẩm hiện tại và có product_unit_id không
            $parts = explode('_', $itemKey);
            if ($parts[0] == $id && isset($cartItem['product_unit_id'])) {
                $existingProductUnitIdsInCart[] = $cartItem['product_unit_id'];
            }
        }
        // Loại bỏ các ID trùng lặp
        $existingProductUnitIdsInCart = array_unique($existingProductUnitIdsInCart);


        // Xử lý từng product unit
        $unitsData = $product->productUnits->map(function ($unit) use ($product, $now, $existingProductUnitIdsInCart) {
            $processedUnit = $this->processProductForCart($product, $now, $unit->id);

            return [
                'id' => $unit->id,
                'weight' => $unit->weight,
                'expiry_date' => $unit->expiry_date ? Carbon::parse($unit->expiry_date)->format('d/m/Y') : 'N/A',
                'batch_qty' => $unit->batch_qty,
                'price' => $processedUnit->final_display_price,
                'has_stock' => $processedUnit->has_stock,
                'display_mode' => $processedUnit->display_mode,
                'total_available_quantity' => $processedUnit->total_available_quantity,
                'is_in_cart' => in_array($unit->id, $existingProductUnitIdsInCart), // Thêm cờ này
            ];
        })->filter(function ($unit) {
            // Vẫn chỉ trả về các đơn vị còn hàng, nhưng frontend sẽ xử lý làm sẫm màu
            return $unit['has_stock'];
        })->values();

        $productData = [
            'id' => $product->id,
            'name' => $product->productTemplate->name,
            'description' => $product->productTemplate->description,
            'image' => $product->productTemplate->image,
            'base_price' => $product->final_display_price,
            'display_mode' => $product->display_mode,
            'unit' => $product->productTemplate->unit,
            'units' => $unitsData,
        ];

        return response()->json([
            'status' => 'success',
            'product' => $productData
        ]);
    }
    // =================================================================

    public function AjaxAddToCart($id, $unitId = null)
    {
        $now = Carbon::now();
        $product = ProductNew::with([
            'productTemplate',
            'productUnits' => function($query) use ($unitId) {
                if ($unitId) {
                    $query->where('id', $unitId); // Chỉ tải unit cụ thể nếu có unitId được truyền
                } else {
                    // Nếu không có unitId, tải các unit còn hàng để chọn cái HSD gần nhất
                    $query->where(function($q) {
                        $q->whereNull('weight')->where('batch_qty', '>', 0);
                    })->orWhere(function($q) {
                        $q->whereNotNull('weight')->where('weight', '>', 0);
                    })
                    ->orderBy('expiry_date', 'asc');
                }
            },
            'productDiscounts' => function($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc');
            }
        ])->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm không tìm thấy.'
            ], 404);
        }

        // Truyền ProductUnitId cụ thể nếu có để processProductForCart chọn đúng unit
        $processedProduct = $this->processProductForCart($product, $now, $unitId);

        if (!$processedProduct->has_stock || !$processedProduct->selected_unit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm này hiện đã hết hàng hoặc không có đơn vị hợp lệ.'
            ], 403);
        }

        $selectedMarketId = session('selected_market_id');
        if ($processedProduct->client_id != $selectedMarketId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm không thuộc cửa hàng hiện tại.'
            ], 403);
        }

        $cart = session()->get('cart', []);
        $itemKey = $processedProduct->display_mode === 'unit'
                           ? $id . '_' . $processedProduct->selected_unit->id // Khóa bao gồm ProductUnit ID
                           : $id; // Khóa chỉ là ProductNew ID

        if ($processedProduct->display_mode === 'unit') {
            // Trong chế độ 'unit', mỗi ProductUnit là một mục riêng biệt và quantity luôn là 1.
            if (isset($cart[$itemKey])) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Đơn vị sản phẩm này (HSD: ' . Carbon::parse($processedProduct->selected_unit->expiry_date)->format('d/m/Y') . ') đã có trong giỏ hàng.',
                ], 409); // Conflict
            }
            $cart[$itemKey] = [
                'id' => $id,
                'product_unit_id' => $processedProduct->selected_unit->id,
                'name' => $processedProduct->productTemplate->name,
                'image' => $processedProduct->productTemplate->image,
                'price' => $processedProduct->final_display_price,
                'client_id' => $processedProduct->client_id,
                'quantity' => 1, // Luôn là 1 cho chế độ 'unit'
                'menu_name' => optional($processedProduct->productTemplate->menu)->name ?? null,
                'expiry_date' => $processedProduct->selected_unit->expiry_date,
                'weight' => $processedProduct->selected_unit->weight,
                'unit_batch_qty' => null, // Không dùng trong mode 'unit'
                'display_mode' => 'unit'
            ];
        } elseif ($processedProduct->display_mode === 'quantity') {
            // Trong chế độ 'quantity', có thể tăng số lượng của cùng một ProductNew
            $selectedProductUnitId = $processedProduct->selected_unit ? $processedProduct->selected_unit->id : null;

            if (isset($cart[$itemKey])) {
                $currentQuantity = $cart[$itemKey]['quantity'];
                $newQuantity = $currentQuantity + 1;

                if ($newQuantity > $processedProduct->total_available_quantity) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cửa hàng chỉ còn ' . $processedProduct->total_available_quantity . ' ' . ($processedProduct->productTemplate->unit ?? 'sản phẩm') . '.'
                    ], 400); // Bad Request
                }
                $cart[$itemKey]['quantity'] = $newQuantity;
                // Cập nhật giá và ProductUnitId nếu đơn vị được chọn thay đổi
                $cart[$itemKey]['price'] = $processedProduct->final_display_price;
                $cart[$itemKey]['product_unit_id'] = $selectedProductUnitId;
            } else {
                $cart[$itemKey] = [
                    'id' => $id,
                    'product_unit_id' => $selectedProductUnitId,
                    'name' => $processedProduct->productTemplate->name,
                    'image' => $processedProduct->productTemplate->image,
                    'price' => $processedProduct->final_display_price,
                    'client_id' => $processedProduct->client_id,
                    'quantity' => 1,
                    'menu_name' => optional($processedProduct->productTemplate->menu)->name ?? null,
                    'expiry_date' => $processedProduct->selected_unit ? $processedProduct->selected_unit->expiry_date : null,
                    'weight' => $processedProduct->selected_unit ? $processedProduct->selected_unit->weight : null,
                    'unit_batch_qty' => $processedProduct->selected_unit ? $processedProduct->selected_unit->batch_qty : null,
                    'display_mode' => 'quantity'
                ];
            }
        }

        session()->put('cart', $cart);
        $this->recalculateCoupon();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
            'cartItem' => $cart[$itemKey] // Trả về thông tin item đã được thêm/cập nhật
        ]);
    }

    public function AjaxUpdateCart(Request $request, $itemKey)
    {
        $cart = session('cart', []);
        if (!isset($cart[$itemKey])) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không có trong giỏ hàng.'], 404);
        }

        $newQuantity = (int) $request->quantity;
        if ($newQuantity < 0) { // Không cho phép số lượng âm
             return response()->json(['status' => 'error', 'message' => 'Số lượng không hợp lệ.'], 400);
        }

        // Tách ProductNewId và ProductUnitId từ itemKey
        $parts = explode('_', $itemKey);
        $productId = $parts[0];
        $productUnitId = $parts[1] ?? null; // Sẽ là null nếu itemKey chỉ là ProductNewId (chế độ quantity)

        $now = Carbon::now();
        $product = ProductNew::with([
            'productUnits' => function($query) use ($productUnitId, $itemKey) {
                if ($productUnitId) {
                    $query->where('id', $productUnitId);
                } else {
                    // Để đảm bảo có một unit được chọn cho quantity mode, 
                    // chúng ta cần lấy unit_id đã lưu trong cart item nếu có, 
                    // hoặc fallback về HSD gần nhất nếu không.
                    // Tuy nhiên, việc fetch ProductUnit ở đây cần tương ứng với cách unit được lưu trong cart
                    // Nếu là mode 'quantity', unit_id trong cart là cái được chọn ban đầu khi add.
                    // Giả sử $cart[$itemKey]['product_unit_id'] đã được lưu đúng khi thêm vào giỏ.
                    $cartItemUnitId = session('cart')[$itemKey]['product_unit_id'] ?? null;
                    if ($cartItemUnitId) {
                        $query->where('id', $cartItemUnitId);
                    } else {
                        $query->where(function($q) {
                            $q->whereNull('weight')->where('batch_qty', '>', 0);
                        })->orWhere(function($q) {
                            $q->whereNotNull('weight')->where('weight', '>', 0);
                        })
                        ->orderBy('expiry_date', 'asc')
                        ->limit(1); 
                    }
                }
            },
            'productTemplate',
            'productDiscounts' => function($query) use ($now) {
                $query->where('start_at', '<=', $now)
                      ->where('end_at', '>=', $now)
                      ->orderBy('created_at', 'desc');
            }
        ])->find($productId);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Sản phẩm không tồn tại.'], 404);
        }

        // Truyền ProductUnitId chính xác để processProductForCart xử lý đúng
        // Nếu là quantity mode, unitId có thể lấy từ cart item nếu đã được lưu.
        $unitIdToProcess = $productUnitId ?? (session('cart')[$itemKey]['product_unit_id'] ?? null);
        $processedProduct = $this->processProductForCart($product, $now, $unitIdToProcess);

        if (!$processedProduct->has_stock || !$processedProduct->selected_unit || $processedProduct->final_display_price <= 0) {
            // Nếu sản phẩm hoặc đơn vị hết hàng, không hợp lệ, hoặc giá 0, remove khỏi giỏ hàng
            unset($cart[$itemKey]);
            session(['cart' => $cart]);
            $this->recalculateCoupon();
            return response()->json([
                'status' => 'info',
                'message' => 'Sản phẩm đã hết hàng hoặc không hợp lệ và đã được xóa khỏi giỏ.',
                'removed' => true
            ]);
        }

        // Kiểm tra số lượng tồn kho theo display_mode
        if ($processedProduct->display_mode === 'unit') {
            // Trong chế độ 'unit', mỗi item trong giỏ là một ProductUnit duy nhất.
            // Số lượng của item đó trong giỏ luôn là 1. Không cho phép tăng.
            if ($newQuantity > 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đơn vị sản phẩm này không thể tăng số lượng. Vui lòng thêm một đơn vị khác nếu có.'
                ], 400);
            }
            // Nếu newQuantity là 0, sẽ unset ở dưới
        } elseif ($processedProduct->display_mode === 'quantity') {
            if ($newQuantity > $processedProduct->total_available_quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cửa hàng chỉ còn ' . $processedProduct->total_available_quantity . ' ' . ($processedProduct->productTemplate->unit ?? 'sản phẩm') . '.',
                ], 400);
            }
        }

        // Nếu đủ số lượng, thì cập nhật
        $cart[$itemKey]['quantity'] = $newQuantity;

        if ($cart[$itemKey]['quantity'] <= 0) {
            unset($cart[$itemKey]);
        } else {
            // Cập nhật lại giá và các thông tin khác (nếu có thay đổi)
            $cart[$itemKey]['price'] = $processedProduct->final_display_price;
            $cart[$itemKey]['name'] = $processedProduct->productTemplate->name;
            $cart[$itemKey]['image'] = $processedProduct->productTemplate->image;
            $cart[$itemKey]['expiry_date'] = $processedProduct->selected_unit->expiry_date;
            $cart[$itemKey]['weight'] = $processedProduct->selected_unit->weight;
            $cart[$itemKey]['unit_batch_qty'] = $processedProduct->selected_unit->batch_qty;
            $cart[$itemKey]['display_mode'] = $processedProduct->display_mode;
            $cart[$itemKey]['product_unit_id'] = $processedProduct->selected_unit->id; // Cập nhật lại unit ID nếu có thay đổi
        }

        session(['cart' => $cart]);
        $this->recalculateCoupon();

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật giỏ hàng thành công.',
            'cartItem' => $cart[$itemKey] ?? null // Trả về null nếu item đã bị xóa
        ]);
    }

    public function AjaxRemoveFromCart(Request $request, $itemKey)
    {
        $cart = session('cart', []);
        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            session(['cart' => $cart]);
            $this->recalculateCoupon(); // Recalculate coupon sau khi xóa
            return response()->json(['status' => 'success', 'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng.']);
        }
        return response()->json(['status' => 'error', 'message' => 'Sản phẩm không có trong giỏ hàng.'], 404);
    }



    // private function recalculateCoupon() {
    //     $cart = session()->get('cart', []);
    //     $totalAmount = 0;
    //     $clientIds = [];
    //     $now = Carbon::now();
    //     $updatedCart = []; // Giỏ hàng mới sau khi kiểm tra

    //     foreach ($cart as $itemKey => $car) {
    //         $parts = explode('_', $itemKey);
    //         $productId = $parts[0];
    //         // Lấy ProductUnitId từ itemKey hoặc từ dữ liệu trong cart item
    //         $productUnitId = $parts[1] ?? ($car['product_unit_id'] ?? null);

    //         $product = ProductNew::with([
    //             'productUnits' => function($query) use ($productUnitId) {
    //                 if ($productUnitId) {
    //                     $query->where('id', $productUnitId);
    //                 }
    //             },
    //             'productTemplate',
    //             'productDiscounts' => function($query) use ($now) {
    //                 $query->where('start_at', '<=', $now)
    //                       ->where('end_at', '>=', $now)
    //                       ->orderBy('created_at', 'desc');
    //             }
    //         ])->find($productId);

    //         if ($product) {
    //             // Đảm bảo truyền đúng productUnitId (lấy từ itemKey hoặc cart item)
    //             $processedProduct = $this->processProductForCart($product, $now, $productUnitId);

    //             // Kiểm tra xem đơn vị cụ thể (nếu có) còn tồn tại/hợp lệ không
    //             // Hoặc kiểm tra xem productNew còn hợp lệ không nếu là quantity mode
    //             if ($processedProduct->has_stock && $processedProduct->final_display_price > 0 && $processedProduct->selected_unit &&
    //                 ($processedProduct->display_mode === 'quantity' || ($processedProduct->display_mode === 'unit' && $processedProduct->selected_unit->id == $productUnitId))) {

    //                 // Cập nhật lại thông tin trong giỏ hàng với dữ liệu mới nhất từ DB
    //                 $car['price'] = $processedProduct->final_display_price;
    //                 $car['name'] = $processedProduct->productTemplate->name;
    //                 $car['image'] = $processedProduct->productTemplate->image;
    //                 $car['expiry_date'] = $processedProduct->selected_unit->expiry_date;
    //                 $car['weight'] = $processedProduct->selected_unit->weight;
    //                 $car['unit_batch_qty'] = $processedProduct->selected_unit->batch_qty;
    //                 $car['display_mode'] = $processedProduct->display_mode;
    //                 $car['product_unit_id'] = $processedProduct->selected_unit->id; // Đảm bảo unit ID luôn đúng

    //                 // Kiểm tra lại số lượng nếu vượt quá tồn kho (cho display_mode quantity)
    //                 if ($processedProduct->display_mode === 'quantity' && $car['quantity'] > $processedProduct->total_available_quantity) {
    //                     $car['quantity'] = $processedProduct->total_available_quantity; // Giảm số lượng về mức tồn kho
    //                     // Có thể thêm thông báo "số lượng đã được điều chỉnh"
    //                 } elseif ($processedProduct->display_mode === 'unit' && $car['quantity'] > 1) {
    //                     $car['quantity'] = 1; // Luôn là 1 cho unit mode
    //                 }

    //                 $updatedCart[$itemKey] = $car;
    //                 $totalAmount += ($car['price'] * $car['quantity']);
    //                 $clientIds[] = $product->client_id;
    //             } else {
    //                 // Sản phẩm/đơn vị không còn hợp lệ, bỏ qua để loại bỏ khỏi giỏ
    //             }
    //         } else {
    //             // ProductNew không tồn tại, bỏ qua để loại bỏ khỏi giỏ
    //         }
    //     }

    //     session()->put('cart', $updatedCart); // Cập nhật session với giỏ hàng đã kiểm tra và làm sạch

    //     // Phần logic tính toán và áp dụng coupon
    //     if (!Session::has('coupon') || empty($updatedCart)) {
    //         $shippingFee = ($totalAmount === 0 || $totalAmount > 100000) ? 0 : 15000;
    //         Session::put('shipping_fee', $shippingFee);
    //         return;
    //     }

    //     $coupon_name = Session::get('coupon')['coupon_name'];
    //     $coupon = Coupon::where('coupon_name', $coupon_name)
    //                          ->where('validity', '>=', Carbon::now()->format('Y-m-d'))
    //                          ->first();

    //     if (!$coupon) {
    //         Session::forget('coupon');
    //         $shippingFee = ($totalAmount === 0 || $totalAmount > 100000) ? 0 : 15000;
    //         Session::put('shipping_fee', $shippingFee);
    //         return;
    //     }

    //     // Kiểm tra người dùng đã sử dụng coupon chưa
    //     if (Auth::check()) {
    //         $userHasUsedCoupon = Order::where('user_id', Auth::id())
    //                                      ->where('coupon_code', $coupon->id)
    //                                      ->exists();
    //         if ($userHasUsedCoupon) {
    //             Session::forget('coupon');
    //             $shippingFee = ($totalAmount === 0 || $totalAmount > 100000) ? 0 : 15000;
    //             Session::put('shipping_fee', $shippingFee);
    //             return;
    //         }
    //     }

    //     // Kiểm tra logic áp dụng coupon theo client_id
    //     $validCoupon = false;
    //     if ($coupon->client_id == 0) { // Coupon toàn hệ thống
    //         $validCoupon = true;
    //     } elseif (!empty($clientIds) && count(array_unique($clientIds)) === 1 && $coupon->client_id == $clientIds[0]) {
    //         // Coupon dành riêng cho một client và tất cả sản phẩm trong giỏ thuộc client đó
    //         $validCoupon = true;
    //     }

    //     if ($validCoupon) {
    //         $calculatedDiscount = $totalAmount * $coupon->discount / 100;
    //         $discountAmount = ($coupon->max_discount_amount && $calculatedDiscount > $coupon->max_discount_amount)
    //             ? $coupon->max_discount_amount
    //             : $calculatedDiscount;

    //         Session::put('coupon', [
    //             'coupon_id' => $coupon->id,
    //             'coupon_name' => $coupon->coupon_name,
    //             'discount' => $coupon->discount,
    //             'discount_amount' => $discountAmount,
    //         ]);
    //     } else {
    //         Session::forget('coupon');
    //     }

    //     // Tính phí giao hàng sau khi áp dụng/kiểm tra coupon
    //     $finalAmountAfterDiscount = $totalAmount - (Session::has('coupon') ? Session::get('coupon')['discount_amount'] : 0);
    //     $shippingFee = ($finalAmountAfterDiscount === 0 || $finalAmountAfterDiscount > 100000) ? 0 : 15000;
    //     Session::put('shipping_fee', $shippingFee);
    // }
    
    // logic coupon
    private function recalculateCoupon(){
        if (!Session::has('coupon')) {
            $cart = session()->get('cart', []);
            $totalAmount = 0;

            foreach ($cart as $car) {
                $totalAmount += ($car['price'] * $car['quantity']);
            }

            // Tính phí giao hàng
            if ($totalAmount === 0) {
                $shippingFee = 0;
            } elseif ($totalAmount > 100000) {
                $shippingFee = 0;
            } else {
                $shippingFee = 15000;
            }

            Session::put('shipping_fee', $shippingFee);
            return;
        }

        $coupon_name = Session::get('coupon')['coupon_name'];

        $coupon = Coupon::where('coupon_name', $coupon_name)
                        ->where('validity', '>=', Carbon::now()->format('Y-m-d'))
                        ->first();

        $cart = session()->get('cart', []);
        $totalAmount = 0;
        $clientIds = [];

        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
            $pd = ProductNew::find($car['id']);
            $clid = $pd->client_id;
            $clientIds[] = $clid;
        }
        
        if (!$coupon) {
            Session::forget('coupon');
            return;
        }

        if ($coupon->client_id == 0) {
            $calculatedDiscount = $totalAmount * $coupon->discount / 100;
            $discountAmount = ($coupon->max_discount_amount && $calculatedDiscount > $coupon->max_discount_amount)
                ? $coupon->max_discount_amount
                : $calculatedDiscount;

            // Tính phí giao hàng
            if ($totalAmount - $discountAmount > 100000) {
                $shippingFee = 0;
            } else {
                $shippingFee = 15000;
            }
            Session::put('shipping_fee', $shippingFee);

            Session::put('coupon', [
                'coupon_id' => $coupon->id,
                'coupon_name' => $coupon->coupon_name,
                'discount' => $coupon->discount,
                'discount_amount' => $totalAmount - $discountAmount, // ✅ là số tiền còn lại
            ]);
        } elseif ($coupon->client_id != 0 && count(array_unique($clientIds)) === 1 && $coupon->client_id == $clientIds[0]) {
            $discountAmount = $totalAmount * $coupon->discount / 100;

            // Tính phí giao hàng
            if ($totalAmount - $discountAmount > 100000) {
                $shippingFee = 0;
            } else {
                $shippingFee = 15000;
            }
            Session::put('shipping_fee', $shippingFee);

            Session::put('coupon', [
                'coupon_name' => $coupon->coupon_name,
                'discount' => $coupon->discount,
                'discount_amount' => $totalAmount - $discountAmount,
            ]);
        } else {
            Session::forget('coupon');
        }
    }
    // End Method

    public function AjaxReloadCart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $coupon = session('coupon');

        foreach ($cart as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        $discountAmount = $coupon ? $total * ($coupon['discount'] / 100) : 0;
        $finalAmount = $total - $discountAmount;

        $this->recalculateCoupon();
        return response()->json([
            'html' => view('frontend.cart.partial', compact('cart', 'total', 'coupon', 'discountAmount', 'finalAmount'))->render()
        ]);
    }

    public function AjaxReloadCartHeader()
    {
        $cart = session()->get('cart', []);
        $groupedCart = [];
        $total = 0;

        foreach ($cart as $item) {
            $groupedCart[$item['client_id']][] = $item;
        }

        $selectedMarketId = session('selected_market_id');

        if (!$selectedMarketId) {
            return response()->json(['error' => 'No selected_market_id in session'], 500);
        }

        $clients = Client::where('id', $selectedMarketId)->get()->keyBy('id');

        $this->recalculateCoupon();

        try {
            $html = view('frontend.cart.header_partial', compact('cart', 'groupedCart', 'clients', 'total'))->render();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'html' => $html,
        ]);
    }

    
    public function ApplyCoupon(Request $request) {
        $user = Auth::guard('web')->user();
        $coupon = Coupon::where('coupon_name', $request->coupon_name)
                        ->where('validity', '>=', Carbon::now()->format('Y-m-d'))
                        ->first();
        $cart = session()->get('cart', []);
        $totalAmount = 0;
        $clientIds = [];

        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
            $pd = ProductNew::find($car['id']);
            $clid = $pd->client_id;
            array_push($clientIds, $clid);
        }

        if ($coupon) {
            $userHasUsedCoupon = Order::where('user_id', $user->id)
                                    ->where('coupon_code', $coupon->id)
                                    ->exists();

            if ($userHasUsedCoupon) {
                return response()->json([
                    'error' => 'Bạn đã sử dụng mã giảm giá này trước đó.',
                ]);
            }

            if (count(array_unique($clientIds)) === 1) {
                $cclientId = $coupon->client_id;
                if ($cclientId == $clientIds[0] || $cclientId == 0) {
                    if ($cclientId == 0 && $coupon->quantity_apply <= 0) {
                        return response()->json([
                            'error' => 'Mã giảm giá này đã hết lượt sử dụng.',
                        ]);
                    }
                    
                    $calculatedDiscount = $totalAmount * $coupon->discount / 100;
                    if ($coupon->max_discount_amount && $calculatedDiscount > $coupon->max_discount_amount) {
                        $discountAmount = $coupon->max_discount_amount;
                    } else {
                        $discountAmount = $calculatedDiscount;
                    }

                    $coupon->decrement('quantity_apply');

                    // Tính phí giao hàng
                    $shippingFee = $totalAmount - $discountAmount > 100000 ? 0 : 15000;
                    Session::put('shipping_fee', $shippingFee);

                    Session::put('coupon', [
                        'coupon_id' => $coupon->id,
                        'coupon_name' => $coupon->coupon_name,
                        'discount' => $coupon->discount,
                        'discount_amount' => $totalAmount - $discountAmount,
                    ]);
                    $couponData = Session()->get('coupon');
                    return response()->json(array(
                        'validity' => true,
                        'success' => 'Áp dụng mã giảm giá thành công',
                        'couponData' => $couponData,
                    ));
                } else {
                    return response()->json([
                        'error' => 'Mã giảm giá không phù hợp với cửa hàng này',
                    ]);
                }
            } else {
                return response()->json([
                    'error' => 'Vui lòng chọn sản phẩm trước khi áp dụng',
                ]);
            }
        } else {
            return response()->json([
                'error' => 'Invalid Coupon',
            ]);
        }
    }
    // End Method

    public function RemoveCoupon() {
        $couponSession = session()->get('coupon');

        if ($couponSession && isset($couponSession['coupon_id'])) {
            $coupon = Coupon::find($couponSession['coupon_id']);
            if ($coupon) {
                $coupon->increment('quantity_apply');
            }
        }

        Session::forget('coupon');
        
        $cart = session()->get('cart', []);
        $totalAmount = 0;
        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
        }
        // Tính phí giao hàng
        if ($totalAmount === 0) {
            $shippingFee = 0;
        } elseif ($totalAmount > 100000) {
            $shippingFee = 0;
        } else {
            $shippingFee = 15000;
        }

        Session::put('shipping_fee', $shippingFee);
        
        return response()->json([
            'success' => 'Xóa mã giảm giá thành công',
        ]);
    }
    // End Method

    public function MarketCheckout(){
        // For Footer
        $cities = City::all();
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


        if(Auth::check()){
            $shipping_fee = session()->get('shipping_fee');
            $cart = session()->get('cart', []);
            $coupon = session()->get('coupon', []);
            $totalAmount = 0;
            foreach ($cart as $car) {
                $totalAmount += $car['price'] * $car['quantity']; // Cần nhân với quantity
            }
            if ($totalAmount > 0) {

                return view('frontend.checkout.view_checkout', compact('cart', 'cities', 'menus_footer', 'products_list', 'shipping_fee'));

            } else {

                $notification = array(
                    'message' => 'Vui lòng mua ít nhất một món hàng',
                    'alert-type' => 'error'
                );
                return redirect()->to('/')->with($notification);
                
            }
            

        } else {
            $notification = array(
                'message' => 'Vui lòng hãy đăng nhập trước khi sử dụng',
                'alert-type' => 'error'
            );
            return redirect()->route('login')->with($notification);
        }
    }
    // End Method
}