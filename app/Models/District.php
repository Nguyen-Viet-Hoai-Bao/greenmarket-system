<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    // Quan hệ: Một quận/huyện thuộc một thành phố
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Quan hệ: Một quận/huyện có nhiều phường/xã
    public function wards()
    {
        return $this->hasMany(Ward::class);
    }
}
