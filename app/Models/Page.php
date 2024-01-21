<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends Model
{
    use HasFactory;
    use Sluggable;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['asset_url', 'added_on'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function getAssetUrlAttribute()
    {
        return show($this->attributes['asset'], $this->attributes['type'], '1200x730');
    }

    public function getAddedOnAttribute()
    {
        return dbtoDate($this->attributes['created_at'], 'M/d/Y');
    }

    public function service_addl_help()
    {
        return $this->belongsTo('App\Models\AdditionalService', 'service_category_id', 'id');
    }
}
