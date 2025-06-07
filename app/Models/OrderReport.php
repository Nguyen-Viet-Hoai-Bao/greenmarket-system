<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderReport extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function client() {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
