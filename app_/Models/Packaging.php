<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packaging extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function images()
    {
        return $this->hasMany(PackagingImage::class);
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id')
            ->where('type', 'packaging')
            ->select('id', 'name');
    }
}
