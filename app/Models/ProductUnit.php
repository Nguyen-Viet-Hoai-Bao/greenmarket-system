<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductUnit extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function productNew()
    {
        return $this->belongsTo(ProductNew::class, 'product_news_id', 'id');
    }
}
