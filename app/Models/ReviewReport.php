<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewReport extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'reported_by_client_id');
    }
}
