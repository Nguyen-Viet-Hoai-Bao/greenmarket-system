<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\ProductNew; 
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\OrderReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{
    public function VerifyOrderForProductReview(Request $request)
    {
        $user = Auth::guard('web')->user();
        $productId = $request->input('product_id');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để đánh giá.'
            ], 401);
        }

        // Kiểm tra user đã đánh giá sản phẩm này chưa
        $existingReview = ProductReview::where('user_id', $user->id)
                            ->where('product_id', $productId)
                            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này rồi. Không thể đánh giá thêm.'
            ]);
        }

        // Kiểm tra user đã mua sản phẩm này trong đơn hàng đã giao chưa
        $hasBoughtProduct = Order::where('user_id', $user->id)
                        ->where('status', 'delivered')
                        ->whereHas('OrderItems', function($query) use ($productId) {
                            $query->where('product_id', $productId);
                        })
                        ->exists();

        if (!$hasBoughtProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể đánh giá sản phẩm nếu đã mua sản phẩm này.',
                'hasBoughtProduct' => $hasBoughtProduct,
                'productId' => $productId
            ]);
        }

        // Nếu chưa đánh giá và đã mua => cho phép đánh giá
        return response()->json([
            'success' => true,
            'message' => 'Bạn có thể gửi đánh giá cho sản phẩm này.',
            'hasBoughtProduct' => $hasBoughtProduct,
                'productId' => $productId

        ]);
    }
    
    public function StoreProductReview(Request $request){
        $product_id = $request->product_id;
        $product = ProductNew::findOrFail($product_id);

        $request->validate([
            'comment' => 'required'
        ]);

        ProductReview::insert([
            'product_id' => $product_id,
            'client_id' => $product->client_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'rating' => $request->rating,
            'status' => '1',
            'created_at' => Carbon::now(), 
        ]);

        $notification = array(
            'message' => 'ProductReview Will Approlve By Admin',
            'alert-type' => 'success'
        );

        $previousUrl = $request->headers->get('referer');
        $redirectUrl = $previousUrl ? $previousUrl . '#pills-reviews' : route('product.details', ['id' => $product_id]) . '#pills-reviews';
        return redirect()->to($redirectUrl)->with($notification);

    }
    // End Method 
     
    public function AdminPendingProductReview(){
        $pedingProductReview = ProductReview::where('status',0)
                                ->with(['user', 'client', 'reviewReport'])
                                ->orderBy('id','desc')->get();
        return view('admin.backend.product_review.view_pending_review',compact('pedingProductReview'));
    }
     // End Method 

     public function AdminApproveProductReview(){
        $approveProductReview = ProductReview::where('status',1)
                                ->with(['user', 'client', 'reviewReport'])
                                ->orderBy('id','desc')->get();
        return view('admin.backend.product_review.view_approve_review',compact('approveProductReview'));
    }
     // End Method 
 
    public function ProductReviewChangeStatus(Request $request){
        $review = ProductReview::find($request->review_id);
        $review->status = $request->status;
        $review->save();
        return response()->json(['success' => 'Status Change Successfully']);
    }
     // End Method 

    public function AdminOrderReport(){
        $listReport = OrderReport::with(['order', 'client'])
                                ->orderBy('id','desc')->get();
        return view('admin.backend.order_report.view_report',compact('listReport'));
    }
     // End Method  

    public function ChangeOrderReport(Request $request){
        $review = OrderReport::find($request->review_id);
        $review->status = $request->status;
        $review->save();
        return response()->json(['success' => 'Cập nhật trạng thái thành công']);
    }
    // End Method 
    
    public function ClientAllProductReviews(){
        $id = Auth::guard('client')->id();

        $allreviews = ProductReview::with(['user', 'client', 'product'])
                            ->where('status',1)
                            ->where('client_id',$id)
                            ->orderBy('id','desc')
                            ->get();
        return view('client.backend.product_review.view_all_review',compact('allreviews'));
    }
    // End Method 


}