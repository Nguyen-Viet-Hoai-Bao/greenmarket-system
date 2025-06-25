<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductNew extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function productTemplate()
    {
        return $this->belongsTo(ProductTemplate::class);
    }
    
    public function client() {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class, 'product_news_id', 'id');
    }

    public function productDiscounts()
    {
        return $this->hasMany(ProductDiscount::class, 'product_news_id', 'id');
    }
    
    public function getActiveDiscount()
    {
        return $this->productDiscounts()
                    ->where('start_at', '<=', Carbon::now())
                    ->where('end_at', '>=', Carbon::now())
                    ->orderBy('discount_percent', 'desc') // Ưu tiên giảm giá theo phần trăm lớn hơn
                    ->orderBy('discount_price', 'desc') // Hoặc giảm giá cố định lớn hơn
                    ->first();
    }
}
