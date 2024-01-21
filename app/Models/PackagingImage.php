<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return show($this->attributes['image'], 'packaging', '600x600');
    }
}
