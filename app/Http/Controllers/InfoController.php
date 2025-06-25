<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Menu;
use App\Models\ProductNew;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    public function AboutUs(){

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

        return view('frontend.about.about', compact('cities', 'menus_footer', 'products_list'));
    }

    public function ListMarkets(){
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
        
        $markets = [
            [
                'name' => 'Green Food Quận 1',
                'address' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
                'phone' => '0909 123 456',
            ],
            [
                'name' => 'Green Food Quận 7',
                'address' => '456 Lê Văn Lương, Quận 7, TP.HCM',
                'phone' => '0909 654 321',
            ],
            [
                'name' => 'Green Food Hà Nội',
                'address' => '789 Giải Phóng, Hà Nội',
                'phone' => '0912 888 999',
            ],
        ];

        return view('frontend.about.markets', compact('cities', 'menus_footer', 'products_list', 'markets'));
    }

    public function QualityManage(){
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

        // Danh sách sản phẩm sẽ được lấy từ cơ sở dữ liệu nếu cần, hiện tại hardcoded
        $products = [
            'Bánh La Gustosa nhân kem hương dừa',
            'Bánh cookie giòn VinMart Cook',
            'Thịt heo kho trứng VinMart Cook',
            'Bánh Palmier VinMart Cook',
            'Đậu nhồi thịt sốt cà chua VinMart Cook',
            'Giò xào VinMart Cook',
            'REMUS bộ dao 8 món có giá đỡ màu xám',
            'KOHL dao thép không gỉ',
            'Tô trộn',
            'KEAN dụng cụ kẹp tỏi',
            '... (và các mục khác – đã rút gọn)',
        ];

        return view('frontend.manage.quality', compact('cities', 'menus_footer', 'products_list', 'products'));
    }

    public function PrivacyPolicy(){
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

        return view('frontend.privacypolicy.privacypolicy', compact('cities', 'menus_footer', 'products_list'));
    }

    public function TransactionPolicy(){
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

        return view('frontend.transactionpolicy.transactionpolicy', compact('cities', 'menus_footer', 'products_list'));
    }

    public function CustomerSupport(){

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

        return view('frontend.customersupport.customersupport', compact('cities', 'menus_footer', 'products_list'));
    }

    public function DeliveryPolicy(){
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

        return view('frontend.deliverypolicy.deliverypolicy', compact('cities', 'menus_footer', 'products_list'));
    }

    public function PaymentPolicy(){
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

        return view('frontend.paymentpolicy.paymentpolicy', compact('cities', 'menus_footer', 'products_list'));
    }

    public function OrderConditions(){
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

        return view('frontend.paymentpolicy.order_conditions', compact('cities', 'menus_footer', 'products_list'));
    }
    
    public function PersonalDataPolicy(){
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

        return view('frontend.paymentpolicy.personal_data_policy', compact('cities', 'menus_footer', 'products_list'));
    }
    
    public function ShippingPolicy(){
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

        return view('frontend.paymentpolicy.shipping_policy', compact('cities', 'menus_footer', 'products_list'));
    }

    public function ReturnAndExchangePolicy(){
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
        return view('frontend.returnspolicy.returnspolicy', compact('cities', 'menus_footer', 'products_list'));
    }
}
