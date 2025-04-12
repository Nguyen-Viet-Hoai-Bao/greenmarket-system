<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ward extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    // Quan hệ: Một phường/xã thuộc một quận/huyện
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
