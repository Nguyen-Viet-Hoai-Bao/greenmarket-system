<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReviewReport;
use App\Models\ProductReviewReport;
use Illuminate\Support\Facades\Auth; 

class ReviewReportController extends Controller
{
    public function ReportReview(Request $request)
    {
        $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'reason' => 'required|string|max:500',
        ]);

        $clientId = Auth::guard('client')->id();

        $exists = ReviewReport::where('review_id', $request->review_id)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Đánh giá này đã được báo cáo trước đó.'
            ], 409); // Sử dụng mã lỗi rõ ràng hơn
        }

        ReviewReport::create([
            'review_id' => $request->review_id,
            'reported_by_client_id' => $clientId,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Yêu cầu ẩn đánh giá đã được gửi đến admin',
        ]);
    }

    public function ReportReviewProduct(Request $request)
    {
        $request->validate([
            'review_id' => 'required|exists:product_reviews,id',
            'reason' => 'required|string|max:500',
        ]);

        $clientId = Auth::guard('client')->id();

        $exists = ProductReviewReport::where('product_review_id', $request->review_id)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Đánh giá này đã được báo cáo trước đó.'
            ], 409); // Sử dụng mã lỗi rõ ràng hơn
        }

        ProductReviewReport::create([
            'product_review_id' => $request->review_id,
            'reported_by_client_id' => $clientId,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'message' => 'Yêu cầu ẩn đánh giá đã được gửi đến admin',
        ]);
    }
}
