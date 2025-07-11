<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductTemplate extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function productNews()
    {
        return $this->hasMany(ProductNew::class);
    }

    public function menu() {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    
    public function productDetail()
    {
        return $this->hasOne(ProductDetail::class, 'product_template_id');
    }

}
