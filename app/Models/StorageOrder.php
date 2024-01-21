<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageOrder extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'mobile', 'postcode', 'start_date', 'end_date', 'order_id', 'storage_id', 'user_id', 'reason_for_storage'];
    protected $guarded = ['id', 'created_at'];


    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
    public function storage()
    {
        return $this->hasOne(Storage::class, 'id', 'storage_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
