<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $fillable = ['name_period', 'market', 'link', 'img_link', 'generation', 'brand_id'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
