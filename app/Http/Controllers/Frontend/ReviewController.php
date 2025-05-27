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
        $validator = Validator::make($request->all(), [
            'order_code' => 'required|string',
            'order_email' => 'required|email',
            'client_id' => 'required|exists:clients,id', // client_id của cửa hàng ĐANG ĐƯỢC ĐÁNH GIÁ
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        $orderCode = $request->input('order_code');
        $orderEmail = $request->input('order_email');
        $requestedClientId = (int) $request->input('client_id');

        $order = Order::with(['OrderItems.product.client'])
                      ->where('invoice_no', $orderCode)
                      ->where('email', $orderEmail)
                      ->where('status', 'delivered')
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Mã số đơn hàng hoặc email không chính xác, hoặc đơn hàng chưa được giao.'
            ]);
        }

        // *********** THAY THẾ KIỂM TRA HAS_REVIEW BẰNG TRUY VẤN BẢNG REVIEWS ***********
        $existingReview = Review::where('order_id', $order->id)->first();
        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này đã được đánh giá rồi. Mỗi đơn hàng chỉ được đánh giá một lần.'
            ]);
        }
        // ********************************************************************************

        // Logic kiểm tra đơn hàng có thuộc cửa hàng này không
        $belongsToRequestedClient = false;
        if ($order->OrderItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không có sản phẩm hợp lệ để đánh giá.'
            ]);
        }

        foreach ($order->OrderItems as $item) {
            if ($item->product && $item->product->client && (int)$item->product->client->id === $requestedClientId) {
                $belongsToRequestedClient = true;
                break;
            }
        }

        if (!$belongsToRequestedClient) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này không chứa sản phẩm từ cửa hàng bạn đang xem.'
            ]);
        }

        // Nếu tất cả các kiểm tra đều thành công
        return response()->json([
            'success' => true,
            'message' => 'Xác thực đơn hàng thành công! Vui lòng để lại đánh giá của bạn.',
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
        $approveReview = Review::where('status',1)->orderBy('id','desc')->get();
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