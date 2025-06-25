<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Menu;
use App\Models\ProductNew;
use App\Models\District;
use App\Models\Ward;
use App\Models\City;
use App\Models\Gallery;
use App\Models\Wishlist;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
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

    public function MarketDetails($id = null)
    {
        // session()->flush();
        if (!$id && session()->has('selected_market_id')) {
            $id = session('selected_market_id');
        }
        $client = Client::find($id);

        $oldMarketId = session('selected_market_id');

        if ($oldMarketId && $oldMarketId != $id) {
            $oldCart = session()->get('cart', []);
            $newCart = [];
            $removed = false;

            foreach ($oldCart as $item) {
                $oldProduct = ProductNew::where('status', 1)->find($item['id']);

                if (!$oldProduct) {
                    $removed = true;
                    continue;
                }

                // Cập nhật eager loading cho newProduct: chỉ lấy ProductUnit CÒN HÀNG
                // Thêm whereHas để chỉ lấy ProductNew nếu có ít nhất 1 ProductUnit còn hàng
                $newProduct = ProductNew::with([
                    'productTemplate',
                    'productUnits' => function ($query) {
                        $query->where(function ($q) {
                            $q->whereNull('weight')->where('batch_qty', '>', 0); // Quantity mode
                        })->orWhere(function ($q) {
                            // Unit mode: weight > 0, batch_qty > 0 VÀ is_sold_out = 0 (chưa bán hết)
                            $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                        })
                            ->orderBy('expiry_date', 'asc'); // Sắp xếp để lấy HSD gần nhất
                    },
                    'productDiscounts' => function ($query) {
                        $query->where('start_at', '<=', Carbon::now())
                            ->where('end_at', '>=', Carbon::now())
                            ->orderBy('created_at', 'desc');
                    }
                ])
                    ->where('product_template_id', $oldProduct->product_template_id)
                    ->where('status', 1)
                    ->where('client_id', $id)
                    ->whereHas('productUnits', function ($query) { // THÊM ĐIỀU KIỆN NÀY
                        $query->where(function ($q) {
                            $q->whereNull('weight')->where('batch_qty', '>', 0);
                        })->orWhere(function ($q) {
                            $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                        });
                    })
                    ->first();

                // dd($id, session(), $newProduct);

                if ($newProduct) {
                    $itemPrice = 0;
                    // Lấy unit hợp lệ theo stock_mode
                    $firstAvailableUnit = $this->getValidProductUnit(
                        $newProduct->productUnits,
                        $newProduct->productTemplate->stock_mode ?? 'quantity'
                    );

                    if ($firstAvailableUnit) {
                        $itemPrice = $this->calculateFinalPrice(
                            $firstAvailableUnit->sale_price,
                            $firstAvailableUnit->discount_price,
                            $newProduct->productDiscounts->first()
                        );
                    } else {
                        // Nếu không có unit nào còn hàng, có thể giá là 0 hoặc giá mặc định
                        $itemPrice = 0; // Hoặc giá từ template nếu bạn muốn
                    }

                    $newItem = $item;
                    $newItem['id'] = $newProduct->id;
                    $newItem['client_id'] = $id;
                    $newItem['price'] = $itemPrice; // Cập nhật giá dựa trên logic mới

                    $itemKey = $newProduct->productTemplate->stock_mode === 'unit'
                           ? $newProduct->id . '_' . $newProduct->productUnits[0]->id // Khóa bao gồm ProductUnit ID
                           : $newProduct->id;


                    $newCart[$itemKey] = [
                        'id' => $newProduct->id,
                        'product_unit_id' => $newProduct->productUnits[0]->id,
                        'name' => $newProduct->productTemplate->name,
                        'image' => $newProduct->productTemplate->image,
                        'price' => $itemPrice,
                        'client_id' => $newProduct->client_id,
                        'quantity' => 1, // Luôn là 1 cho chế độ 'unit'
                        'menu_name' => optional($newProduct->productTemplate->menu)->name ?? null,
                        'expiry_date' => $newProduct->productUnits[0]->expiry_date,
                        'weight' => $newProduct->productUnits[0]->weight,
                        'unit_batch_qty' => $newProduct->productTemplate->stock_mode === 'unit'
                                            ? null
                                            : $newProduct->productUnits[0]->batch_qty,
                        'display_mode' => 'unit'
                    ];
                } else {
                    $removed = true;
                }
            }
            // dd($id, session(), $newCart);
            // dd($oldCart, $newCart);

            session()->put('cart', $newCart);

            if ($removed) {
                session()->flash('cart_item_removed', true);
            }
        }

        if (!session()->has('selected_market_id') || session('selected_market_id') != $id) {
            session([
                'selected_market_id' => $client->id,
                'selected_market_name' => $client->name,
                'selected_market_ward_id' => $client->ward_id,
            ]);
        }

        // Cập nhật truy vấn cho menus để chỉ bao gồm productNews có ít nhất 1 productUnit còn hàng
        $menus = Menu::whereHas('products.productNews', function ($query) use ($id) {
            $query->where('client_id', $id)
                ->whereHas('productUnits', function ($q) { // THÊM ĐIỀU KIỆN NÀY
                    $q->where(function ($qq) {
                        $qq->whereNull('weight')->where('batch_qty', '>', 0);
                    })->orWhere(function ($qq) {
                        $qq->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                    });
                });
        })
            ->with([
                'products' => function ($query) use ($id) {
                    $query->whereHas('productNews', function ($q) use ($id) {
                        $q->where('client_id', $id)
                            ->whereHas('productUnits', function ($qq) { // THÊM ĐIỀU KIỆN NÀY
                                $qq->where(function ($qqq) {
                                    $qqq->whereNull('weight')->where('batch_qty', '>', 0);
                                })->orWhere(function ($qqq) {
                                    $qqq->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                                });
                            });
                    })
                        ->with(['productNews' => function ($q) use ($id) {
                            $q->where('client_id', $id)
                                ->whereHas('productUnits', function ($qq) { // THÊM ĐIỀU KIỆN NÀY
                                    $qq->where(function ($qqq) {
                                        $qqq->whereNull('weight')->where('batch_qty', '>', 0);
                                    })->orWhere(function ($qqq) {
                                        $qqq->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                                    });
                                });
                        }]);
                }
            ])
            ->get();

        $gallerys = Gallery::where('client_id', $id)->get();

        $reviews = Review::where('client_id', $client->id)
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

        $fullAddress = null;
        if (session()->has('selected_market_ward_id')) {
            $ward = Ward::with('district.city')->find(session('selected_market_ward_id')); // Sửa lại 'selected_market_ward_ward_id' thành 'selected_market_ward_id'

            if ($ward && $ward->district && $ward->district->city) {
                $fullAddress = $ward->ward_name . ', '
                    . $ward->district->district_name . ', '
                    . $ward->district->city->city_name;
            }
        }
        $cities = City::all();

        // For Footer
        $menus_footer = Menu::all();
        $topClientId = ProductNew::select('client_id', DB::raw('COUNT(*) as total'))
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->value('client_id');

        $now = Carbon::now();

        // Xử lý sản phẩm bán chạy nhất ($bestsellers)
        $bestsellers = ProductNew::with([
            'productTemplate',
            'productUnits' => function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0); // Quantity mode
                })->orWhere(function ($q) {
                    // Unit mode: weight > 0, batch_qty > 0 VÀ is_sold_out = 0 (chưa bán hết)
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                })
                    ->orderBy('expiry_date', 'asc'); // Ưu tiên HSD gần nhất
            },
            'productDiscounts' => function ($query) use ($now) {
                $query->where('start_at', '<=', $now)
                    ->where('end_at', '>=', $now)
                    ->orderBy('created_at', 'desc');
            }
        ])
            ->where('status', 1)
            ->where('client_id', $client->id) // Lấy sản phẩm bán chạy của chợ hiện tại
            ->where('best_seller', 1)
            ->whereHas('productUnits', function ($query) { // THÊM ĐIỀU KIỆN NÀY
                $query->where(function ($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function ($q) {
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        $processedBestsellers = $bestsellers->map(function ($product) use ($now) {
            $stockMode = $product->productTemplate->stock_mode ?? 'quantity';
            $activeGeneralDiscount = $product->productDiscounts->first();

            // Lấy product_unit có HSD gần nhất VÀ CÒN HÀNG (đã được lọc trong eager loading)
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

            if ($stockMode === 'unit') {
                $product->display_mode = 'unit';
                $product->available_units = $product->productUnits->whereNotNull('weight')->map(function ($unit) use ($activeGeneralDiscount) {
                    // Gán lại final_sale_price cho từng unit (chỉ những unit còn hàng)
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
                // Tổng số lượng còn hàng của các unit (đã được lọc trong eager loading)
                $product->total_available_quantity = $product->productUnits->whereNull('weight')->sum('batch_qty');
                $product->final_display_price = $finalDisplayPrice;
                $product->display_original_price = $basePrice;
            }

            return $product;
        });

        // --- Cập nhật truy vấn và xử lý cho sản phẩm phổ biến ($populers) ---
        $populers = ProductNew::with([
            'productTemplate',
            'productUnits' => function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0); // Quantity mode
                })->orWhere(function ($q) {
                    // Unit mode: weight > 0, batch_qty > 0 VÀ is_sold_out = 0 (chưa bán hết)
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                })
                    ->orderBy('expiry_date', 'asc'); // Ưu tiên HSD gần nhất
            },
            'productDiscounts' => function ($query) use ($now) {
                $query->where('start_at', '<=', $now)
                    ->where('end_at', '>=', $now)
                    ->orderBy('created_at', 'desc');
            }
        ])
            ->where('status', 1)
            ->where('client_id', $client->id)
            ->where('most_popular', 1)
            ->whereHas('productUnits', function ($query) { // THÊM ĐIỀU KIỆN NÀY
                $query->where(function ($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function ($q) {
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        $processedPopulers = $populers->map(function ($populer) use ($now) {
            $stockMode = $populer->productTemplate->stock_mode ?? 'quantity';
            $activeGeneralDiscount = $populer->productDiscounts->first();

            // Lấy product_unit có HSD gần nhất VÀ CÒN HÀNG
            $firstAvailableUnit = $this->getValidProductUnit($populer->productUnits, $stockMode);

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

            if ($stockMode === 'unit') {
                $populer->display_mode = 'unit';
                $populer->available_units = $populer->productUnits->whereNotNull('weight')->map(function ($unit) use ($activeGeneralDiscount) {
                    // Dù là chế độ unit, giá của từng unit vẫn cần được tính đúng
                    $unit->final_sale_price = $this->calculateFinalPrice(
                        $unit->sale_price,
                        $unit->discount_price,
                        $activeGeneralDiscount
                    );
                    return $unit;
                });
                // Thêm thuộc tính hiển thị chính cho chế độ unit (giá của unit HSD gần nhất)
                $populer->display_unit_price = $finalDisplayPrice;
                $populer->display_unit_original_price = $basePrice; // Giá gốc của unit HSD gần nhất
            } elseif ($stockMode === 'quantity') {
                $populer->display_mode = 'quantity';
                $populer->total_available_quantity = $populer->productUnits->whereNull('weight')->sum('batch_qty');
                $populer->final_display_price = $finalDisplayPrice;
                $populer->display_original_price = $basePrice; // Giá gốc là sale_price của unit HSD gần nhất
            }

            return $populer;
        });

        // --- Cập nhật truy vấn và xử lý cho tất cả sản phẩm ($products_all) ---
        $products_all_query = ProductNew::with([
            'productTemplate.menu', // Đã thêm
            'productTemplate.category', // Đã thêm
            'productTemplate',
            'productUnits' => function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0); // Quantity mode
                })->orWhere(function ($q) {
                    // Unit mode: weight > 0, batch_qty > 0 VÀ is_sold_out = 0 (chưa bán hết)
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                })
                    ->orderBy('expiry_date', 'asc');
            },
            'productDiscounts' => function ($query) use ($now) {
                $query->where('start_at', '<=', $now)
                    ->where('end_at', '>=', $now)
                    ->orderBy('created_at', 'desc');
            }
        ])
            ->where('client_id', session('selected_market_id'))
            ->where('status', 1)
            ->whereHas('productUnits', function ($query) { // THÊM ĐIỀU KIỆN NÀY
                $query->where(function ($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function ($q) {
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                });
            })
            ->orderBy('id', 'desc');

        // Gán $products_all ban đầu từ query
        $products_all = $products_all_query->get();

        $processedProductsAll = $products_all->map(function ($product) use ($now) {
            $stockMode = $product->productTemplate->stock_mode ?? 'quantity';
            $activeGeneralDiscount = $product->productDiscounts->first();

            // Lấy product_unit có HSD gần nhất VÀ CÒN HÀNG
            $firstAvailableUnit = $this->getValidProductUnit($product->productUnits, $stockMode);

            // Mặc định giá gốc là 0
            $basePrice = $firstAvailableUnit?->sale_price ?? 0;

            // Tính giá hiển thị cuối cùng
            $finalDisplayPrice = $this->calculateFinalPrice(
                $basePrice,
                $firstAvailableUnit?->discount_price,
                $activeGeneralDiscount
            );

            // Gán các thông tin chung từ unit đầu tiên (còn hàng)
            $product->expiry_date = $firstAvailableUnit?->expiry_date;
            $product->weight = $firstAvailableUnit?->weight;

            if ($stockMode === 'unit') {
                $product->display_mode = 'unit';
                $product->available_units = $product->productUnits->whereNotNull('weight')->map(function ($unit) use ($activeGeneralDiscount) {
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

            return $product;
        });

        // products_list cũng cần được xử lý tương tự
        $products_list = ProductNew::with([
            'productTemplate',
            'productUnits' => function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0); // Quantity mode
                })->orWhere(function ($q) {
                    // Unit mode: weight > 0, batch_qty > 0 VÀ is_sold_out = 0 (chưa bán hết)
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                })
                    ->orderBy('expiry_date', 'asc');
            },
            'productDiscounts' => function ($query) use ($now) {
                $query->where('start_at', '<=', $now)
                    ->where('end_at', '>=', $now)
                    ->orderBy('created_at', 'desc');
            }
        ])
            ->where('client_id', $topClientId)
            ->whereHas('productUnits', function ($query) { // THÊM ĐIỀU KIỆN NÀY
                $query->where(function ($q) {
                    $q->whereNull('weight')->where('batch_qty', '>', 0);
                })->orWhere(function ($q) {
                    $q->whereNotNull('weight')->where('weight', '>', 0)->where('is_sold_out', 0);
                });
            })
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($product) use ($now) {
                $stockMode = $product->productTemplate->stock_mode ?? 'quantity';
                $activeGeneralDiscount = $product->productDiscounts->first();

                // Lấy product_unit có HSD gần nhất VÀ CÒN HÀNG
                $firstAvailableUnit = $this->getValidProductUnit($product->productUnits, $stockMode);

                $basePrice = $firstAvailableUnit ? $firstAvailableUnit->sale_price : 0;

                $finalDisplayPrice = $this->calculateFinalPrice(
                    $basePrice,
                    $firstAvailableUnit ? $firstAvailableUnit->discount_price : null,
                    $activeGeneralDiscount
                );

                $product->display_mode = $stockMode;
                $product->final_display_price = $finalDisplayPrice;
                $product->display_original_price = $basePrice;
                // Tổng số lượng còn hàng của các unit (đã được lọc trong eager loading)
                $product->total_available_quantity = $product->productUnits->whereNull('weight')->sum('batch_qty');
                if ($stockMode === 'unit') {
                    $product->available_units = $product->productUnits->whereNotNull('weight')->map(function ($unit) use ($activeGeneralDiscount) {
                        $unit->final_sale_price = $this->calculateFinalPrice(
                            $unit->sale_price,
                            $unit->discount_price,
                            $activeGeneralDiscount
                        );
                        return $unit;
                    });
                }

                return $product;
            });

        // Đoạn này hình như bị trùng lặp ProductNew::with ... products_all
        // Nếu bạn muốn products_all cuối cùng có thêm menu và category thông tin,
        // bạn nên load chúng trong query ban đầu cho products_all_query
        $menusWithCategories = Menu::with(['categories'])->get();
        // $products_all ở đây đã được định nghĩa ở trên ($products_all = $products_all_query->get();)
        // và $processedProductsAll là kết quả của việc map trên $products_all.
        // Dòng này (dòng cũ của bạn) là dư thừa và ghi đè lên $products_all đã được xử lý.
        // products_all hiện tại sẽ chứa thông tin menu và category nếu bạn đã thêm vào $products_all_query.
        /*
        $products_all = ProductNew::with([
            'productTemplate.menu',
            'productTemplate.category'
        ])
        ->where('client_id', session('selected_market_id'))
        ->where('status', 1)
        ->where('qty', '>', 0)
        ->orderBy('id', 'desc')
        ->get();
        */


        return view('frontend.details_page',
            compact(
                'client',
                'menus',
                'gallerys',
                'reviews',
                'roundedAverageRating',
                'menusWithCategories',
                'products_all', // Không cần truyền products_all gốc nếu đã có processedProductsAll
                'processedProductsAll', // Truyền phiên bản đã xử lý giá và tình trạng
                'totalReviews',
                'ratingCounts',
                'ratingPercentages',
                'fullAddress',
                'cities',
                'menus_footer',
                'products_list', // products_list đã được xử lý
                'processedPopulers',
                'processedBestsellers'
            )
        );
    }

    // public function MarketDetails($id = null) {
    //     if (!$id && session()->has('selected_market_id')) {
    //         $id = session('selected_market_id');
    //     }
    //     $client = Client::find($id);

    //     $oldMarketId = session('selected_market_id');

    //     if ($oldMarketId && $oldMarketId != $id) {
    //         $oldCart = session()->get('cart', []);
    //         $newCart = [];
    //         $removed = false;

    //         foreach ($oldCart as $item) {
    //             $oldProduct = ProductNew::where('status', 1)->find($item['id']);

    //             if (!$oldProduct) {
    //                 $removed = true;
    //                 continue;
    //             }

    //             $newProducts = ProductNew::where('product_template_id', $oldProduct->product_template_id)
    //                                     ->where('status', 1)
    //                                     ->where('client_id', $id)
    //                                     ->get();

    //             if ($newProducts->count() > 0) {
    //                 foreach ($newProducts as $newProduct) {
    //                     $newItem = $item;
    //                     $newItem['id'] = $newProduct->id;
    //                     $newItem['client_id'] = $id;
    //                     $newItem['price'] = $newProduct->discount_price;
    //                     $newCart[$newProduct->id] = $newItem;
    //                 }
    //             } else {
    //                 $removed = true;
    //             }
    //         }


    //         session()->put('cart', $newCart);

    //         if ($removed) {
    //             session()->flash('cart_item_removed', true);
    //         }
    //     }

    //     // Chỉ cập nhật session nếu khác
    //     if (!session()->has('selected_market_id') || session('selected_market_id') != $id) {
    //         session([
    //             'selected_market_id' => $client->id,
    //             'selected_market_name' => $client->name,
    //             'selected_market_ward_id' => $client->ward_id,
    //         ]);
    //     }
    //     $menus = Menu::whereHas('products.productNews', function ($query) use ($id) {
    //         $query->where('client_id', $id);
    //     })
    //     ->with([
    //         'products' => function ($query) use ($id) {
    //             $query->whereHas('productNews', function ($q) use ($id) {
    //                 $q->where('client_id', $id);
    //             })
    //             ->with(['productNews' => function ($q) use ($id) {
    //                 $q->where('client_id', $id)->where('qty', '>', 0);
    //             }]);
    //         }
    //     ])
    //     ->get();
    //     // dd($menus);

    //     $gallerys = Gallery::where('client_id', $id)->get();

    //     $reviews = Review::where('client_id', $client->id)
    //                         ->where('status',1)
    //                         ->get();
    //     $totalReviews = $reviews->count();
    //     $ratingSum = $reviews->sum('rating');
    //     $averageRating = $totalReviews > 0 ? $ratingSum / $totalReviews : 0;
    //     $roundedAverageRating = round($averageRating, 1);
        
    //     $ratingCounts = [
    //         '5' => $reviews->where('rating',5)->count(),
    //         '4' => $reviews->where('rating',4)->count(),
    //         '3' => $reviews->where('rating',3)->count(),
    //         '2' => $reviews->where('rating',2)->count(),
    //         '1' => $reviews->where('rating',1)->count(),
    //     ];
    //     $ratingPercentages =  array_map(function ($count) use ($totalReviews){
    //         return $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
    //     },$ratingCounts);
    

        
    //     $fullAddress = null;
    //     if (session()->has('selected_market_ward_id')) {
    //         $ward = Ward::with('district.city')->find(session('selected_market_ward_id'));
        
    //         if ($ward && $ward->district && $ward->district->city) {
    //             $fullAddress = $ward->ward_name . ', ' 
    //                          . $ward->district->district_name . ', ' 
    //                          . $ward->district->city->city_name;
    //         }
    //     }
    //     $cities = City::all();



    //     // For Footer        
    //     $menus_footer = Menu::all();
    //     $topClientId = ProductNew::select('client_id', DB::raw('COUNT(*) as total'))
    //                             ->groupBy('client_id')
    //                             ->orderByDesc('total')
    //                             ->value('client_id'); 
    //     $products_list = ProductNew::with([
    //                     'productTemplate.menu',
    //                     'productTemplate.category'
    //                 ])
    //                 ->where('client_id', $topClientId)
    //                 ->where('qty', '>', 0)
    //                 ->orderBy('id', 'desc')
    //                 ->get();

    //     // dd(session()->all());

    //     $menusWithCategories = Menu::with(['categories'])->get();
    //     $products_all = ProductNew::with([
    //                     'productTemplate.menu',
    //                     'productTemplate.category'
    //                 ])
    //                 ->where('client_id', session('selected_market_id'))
    //                 ->where('status', 1)
    //                 ->where('qty', '>', 0)
    //                 ->orderBy('id', 'desc')
    //                 ->get();

    //     return view('frontend.details_page',
    //                     compact('client','menus','gallerys','reviews','roundedAverageRating', 'menusWithCategories', 'products_all',
    //                             'totalReviews','ratingCounts','ratingPercentages', 'fullAddress', 'cities', 'menus_footer', 'products_list'));
    // }
    // end method

    public function AddWishlist(Request $request, $id) {
        if(Auth::check()){
            $exists = Wishlist::where('user_id', Auth::id())
                            ->where('client_id', $id)
                            ->first();
            if (!$exists) {
                Wishlist::insert([
                    'user_id' => Auth::id(),
                    'client_id' => $id,
                    'created_at' => Carbon::now(),
                ]);
                return response()->json(['success' => 'Add Wishlist Successfully']);
            } else {
                return response()->json(['error' => 'This Market has already on your Wishlist']);
            }
        } else {
            return response()->json(['error' => 'Fisrt Login Your Account']);
        }
    }
    // end method

    public function AllWishlist() {
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

        $wishlist = Wishlist::where('user_id', Auth::id())
                            ->get();
        return view('frontend.dashboard.all_wishlist', compact('wishlist', 'cities', 'menus_footer', 'products_list'));
    }
    // end method

    public function RemoveWishlist($id) {
        Wishlist::find($id)->delete();
        $notification = array(
            'message' => 'Wishlist Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // end method

    // public function RedirectToDetails(Request $request)
    // {
    //     $marketId = $request->market_id;
    //     $market = Client::findOrFail($marketId);

    //     session([
    //         'selected_market_id' => $market->id,
    //         'selected_market_name' => $market->name,
    //         'selected_market_ward_id' => $market->ward_id,
    //     ]);

    //     $marketId = $request->market_id;
    //     return redirect()->route('market.details', $marketId);
    // }
    public function RedirectToDetails(Request $request)
    {
        $newMarketId = $request->market_id;
        $newMarket = Client::findOrFail($newMarketId);
        

        // Cập nhật session thị trường mới
        session([
            'selected_market_id' => $newMarket->id,
            'selected_market_name' => $newMarket->name,
            'selected_market_ward_id' => $newMarket->ward_id,
        ]);
        return redirect()->route('market.details', $newMarketId);
    }

    public function GetDistricts($city_id)
    {
        // return District::where('city_id', $city_id)->get();
        $districts = District::where('city_id', $city_id)->get(['id', 'district_name']);
        return response()->json($districts);
    }

    public function GetWards($district_id)
    {
        return Ward::where('district_id', $district_id)->get();
    }

    public function GetMarketsByWard($ward_id)
    {
        return Client::where('ward_id', $ward_id)->where('status', '1')->get();
    }

    // session()->forget(['selected_market_id', 'selected_market_name', 'selected_market_ward_id']);

}
