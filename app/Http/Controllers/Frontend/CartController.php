<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Product;

class CartController extends Controller
{
    public function AddToCart($id) {
        $product = Product::find($id);
        $cart = session()->get('cart', []);
        if(isset($cart[$id])){
            $cart[$id]['quantity']++;

        } else {
            $priceToShow = isset($product->discount_price) 
                            ? $product->discount_price
                            : $product->price;
            $cart[$id] = [
                'id' => $id,
                'name' => $product->name,
                'image' => $product->image,
                'price' => $priceToShow,
                'client_id' => $product->client_id,
                'quantity' => 1
            ];
        }
        session()->put('cart', $cart);
        // return response()->json($cart);
        $notification = array(
            'message' => 'Add to Cart Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }
    // End Method

    public function UpdateCartQuantity(Request $request) {
        $cart = session()->get('cart', []);
        if(isset($cart[$request->id])){
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }
        return response()->json([
            'message' => 'Quantity Updated',
            'alert-type' => 'success'
        ]);
    }
    // End Method

    public function CartRemove(Request $request) {
        $cart = session()->get('cart', []);
        if(isset($cart[$request->id])){
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'message' => 'Cart Remove Successfully',
            'alert-type' => 'success'
        ]);
    }
    // End Method
}