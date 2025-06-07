<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductReview extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function client(){
        return $this->belongsTo(Client::class, 'client_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function product(){
        return $this->belongsTo(ProductNew::class, 'product_id','id');
    }

    public function reviewReport()
    {
        return $this->hasOne(ProductReviewReport::class);
    }
}
