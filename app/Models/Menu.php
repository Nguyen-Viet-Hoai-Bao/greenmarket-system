<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function products() {
        return $this->hasMany(ProductTemplate::class, 'menu_id', 'id');
    }

    public function categories() {
        return $this->hasMany(Category::class, 'menu_id', 'id');
    }
}
