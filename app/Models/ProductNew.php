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
}
