<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Menu;
use App\Models\Gallery;

class HomeController extends Controller
{
    public function MarketDetails($id) {
        $client = Client::find($id);
        $menus = Menu::where('client_id', $client->id)
                    ->get()
                    ->filter(
                        function($menu){
                            return $menu->products->isNotEmpty();
                        });
        $gallerys = Gallery::where('client_id', $id)->get();
        return view('frontend.details_page', compact('client', 'menus', 'gallerys'));
    }
    // end method

}
