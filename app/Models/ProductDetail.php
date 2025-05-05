<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_template_id',
        'description',
        'product_info',
        'note',
        'origin',
        'preservation',
        'usage_instructions',
    ];

    /**
     * Quan hệ với ProductTemplate
     */
    public function productTemplate()
    {
        return $this->belongsTo(ProductTemplate::class);
    }
}
