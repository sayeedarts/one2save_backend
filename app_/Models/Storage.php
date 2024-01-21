<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function images()
    {
        return $this->hasMany(StorageImage::class);
    }

    public function type()
    {
        return $this->hasOne(StorageType::class, 'id', 'storage_type_id');
    }
}
