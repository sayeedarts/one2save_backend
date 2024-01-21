<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $fillable = ['order_id', 'packaging_id', 'quantity', 'subtotal', 'price', 'user_id', 'user_address_id'];

    /**
     * Full Details
     */
    public function packing()
    {
        return $this->hasOne(Packaging::class, 'id', 'packaging_id');
    }

    /**
     * Less details
     */
    public function short_packing()
    {
        return $this->hasOne(Packaging::class, 'id', 'packaging_id')
        ->select('id', 'name', 'price');
    }
}
