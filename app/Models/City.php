<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Quan hệ: Một thành phố có nhiều quận/huyện
    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
