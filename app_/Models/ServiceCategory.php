<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class ServiceCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Sluggable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['icon_url'];
    protected $hidden = ['icon'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function service()
    {
        return $this->hasOne('App\Models\Service', 'id', 'service_id');
    }

    public function category_item()
    {
        return $this->hasMany('App\Models\ServiceCategoryItem', 'service_category_id', 'id');
    }

    public function getIconUrlAttribute()
    {
        return show($this->attributes['icon'], 'service_category', '50x50');
    }

    public function items()
    {
        return $this->hasMany(ServiceCategoryItem::class);
    }
}
