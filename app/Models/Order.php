<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $visible = [];
    protected $hidden = [];

    public function packing_orders()
    {
        return $this->hasMany(PackagingOrder::class, 'order_id', 'id');
    }

    public function storage_order()
    {
        return $this->hasOne(StorageOrder::class, 'order_id', 'id');
    }

    public function buyer()
    {
        return $this->hasOne(User::class, 'id', 'buyer_id')
            ->select('id', 'name', 'email', 'mobile', 'address', 'postcode');
    }
}
