<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductReviewReport extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function productReview()
    {
        return $this->belongsTo(ProductReview::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(Client::class, 'reported_by_client_id');
    }
}
