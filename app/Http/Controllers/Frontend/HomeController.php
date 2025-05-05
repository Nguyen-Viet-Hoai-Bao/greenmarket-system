<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Menu;
use App\Models\Gallery;
use App\Models\Wishlist;
use App\Models\Review;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function MarketDetails($id) {
        $client = Client::find($id);
        $menus = Menu::get()
                    ->filter(
                        function($menu){
                            return $menu->products->isNotEmpty();
                        });
        $gallerys = Gallery::where('client_id', $id)->get();

        $reviews = Review::where('client_id', $client->id)
                            ->where('status',1)
                            ->get();
        $totalReviews = $reviews->count();
        $ratingSum = $reviews->sum('rating');
        $averageRating = $totalReviews > 0 ? $ratingSum / $totalReviews : 0;
        $roundedAverageRating = round($averageRating, 1);
        
        $ratingCounts = [
            '5' => $reviews->where('rating',5)->count(),
            '4' => $reviews->where('rating',4)->count(),
            '3' => $reviews->where('rating',3)->count(),
            '2' => $reviews->where('rating',2)->count(),
            '1' => $reviews->where('rating',1)->count(),
        ];
        $ratingPercentages =  array_map(function ($count) use ($totalReviews){
            return $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
        },$ratingCounts);
    
        return view('frontend.details_page',
                        compact('client','menus','gallerys','reviews','roundedAverageRating',
                                'totalReviews','ratingCounts','ratingPercentages'));
    }
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
        $wishlist = Wishlist::where('user_id', Auth::id())
                            ->get();
        return view('frontend.dashboard.all_wishlist', compact('wishlist'));
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

}
