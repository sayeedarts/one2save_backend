<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategoryItem extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function service_categories()
    {
        return $this->belongsTo('App\Models\ServiceCategory', 'service_category_id', 'id');
    }
}
