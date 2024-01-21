<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return show($this->attributes['image'], 'gallery', '1200x730');
    }
}
