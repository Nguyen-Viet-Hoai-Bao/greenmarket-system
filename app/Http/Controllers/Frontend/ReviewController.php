<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Product; 
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function VerifyOrderForReview(Request $request)
    {
        $user = Auth::guard('web')->user();
        $clientId = $request->input('client_id');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để đánh giá.'
            ], 401);
        }

        // Tìm đơn hàng đã giao có sản phẩm thuộc client này
        $order = Order::with(['OrderItems.product.client'])
                    ->where('user_id', $user->id)
                    ->where('status', 'delivered')
                    ->whereHas('OrderItems.product', function ($query) use ($clientId) {
                        $query->where('client_id', $clientId);
                    })
                    ->latest()
                    ->first();

        // Kiểm tra xem người dùng đã từng đánh giá đơn hàng nào của cửa hàng này chưa
        $reviewedOrder = Review::where('user_id', $user->id)
            ->whereHas('order.OrderItems.product', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->first();

        if ($reviewedOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá cửa hàng này rồi. Không thể đánh giá thêm.'
            ]);
        }

        // Nếu chưa từng đánh giá -> cho phép đánh giá
        return response()->json([
            'success' => true,
            'message' => 'Bạn có thể gửi đánh giá cho cửa hàng này.',
            'order_id' => $order->id
        ]);
    }
    
    public function StoreReview(Request $request){
        $client = $request->client_id;

        $request->validate([
            'comment' => 'required'
        ]);

        Review::insert([
            'client_id' => $client,
            'user_id' => Auth::id(),
            'order_id' => $request->input('order_id'),
            'comment' => $request->comment,
            'rating' => $request->rating,
            'status' => '1',
            'created_at' => Carbon::now(), 
        ]);

        $notification = array(
            'message' => 'Review Will Approlve By Admin',
            'alert-type' => 'success'
        );

        $previousUrl = $request->headers->get('referer');
        $redirectUrl = $previousUrl ? $previousUrl . '#pills-reviews' : route('res.details', ['id' => $client]) . '#pills-reviews';
        return redirect()->to($redirectUrl)->with($notification);

    }
    // End Method 
     
    public function AdminPendingReview(){
        $pedingReview = Review::where('status',0)->orderBy('id','desc')->get();
        return view('admin.backend.review.view_pending_review',compact('pedingReview'));
    }
     // End Method 

     public function AdminApproveReview(){
        $approveReview = Review::where('status',1)
                                ->with(['user', 'client', 'reviewReport'])
                                ->orderBy('id','desc')->get();
        return view('admin.backend.review.view_approve_review',compact('approveReview'));
    }
     // End Method  
 
      public function ReviewChangeStatus(Request $request){
        $review = Review::find($request->review_id);
        $review->status = $request->status;
        $review->save();
        return response()->json(['success' => 'Status Change Successfully']);
    }
     // End Method 
    
    public function ClientAllReviews(){
        $id = Auth::guard('client')->id();

        $allreviews = Review::with(['order'])
                            ->where('status',1)
                            ->where('client_id',$id)
                            ->orderBy('id','desc')
                            ->get();
        return view('client.backend.review.view_all_review',compact('allreviews'));
    }
    // End Method 


}