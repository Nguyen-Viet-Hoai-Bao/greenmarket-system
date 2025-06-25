<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function product() {
        return $this->belongsTo(ProductNew::class, 'product_id', 'id');
    }

    public function productUnit() {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id', 'id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function client() {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
