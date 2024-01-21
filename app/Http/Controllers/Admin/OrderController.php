<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orderList(Request $request)
    {
        $this->data['title'] = "Orders List";

        $orders = Order::with('buyer')->latest()->get();
        $this->data['orders'] = $orders;
        return view('admin.Order.list', $this->data);
    }
}
