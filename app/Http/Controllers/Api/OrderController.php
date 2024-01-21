<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PackagingOrder;
use App\Models\StorageOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public $jsonResponse;

    public function __construct()
    {
        $this->jsonResponse = [
            'status' => 0,
            'message' => 'Something went wrong!'
        ];
    }

    public function list(Request $request)
    {
        if ($request->isMethod('post')) {
            $payload = json_decode(request()->getContent(), true);
            $userDetails = userDetails($payload['email']);
            $module = !empty($payload['module']) ? $payload['module'] : "storage";
            $userId = $userDetails['id'];

            if ($module == "storage") {
                $orders = StorageOrder::where('user_id', $userId)
                    ->with('storage', 'order', 'user');
                if ($orders->count() > 0) {
                    $orders = Order::where(['buyer_id' => $userId, 'module' => 'storage'])
                        ->with('storage_order', 'storage_order.storage', 'storage_order.storage.images')
                        ->latest()
                        ->get();
                    $this->jsonResponse = [
                        'status' => 1,
                        'data' => $orders,
                    ];
                }
            } else if ($module == "packing") {
                $packingOrderDetails = [];
                $orders = Order::where(['buyer_id' => $userId, 'module' => 'packing'])
                    ->with('packing_orders', 'packing_orders.short_packing', 'packing_orders.short_packing.images')
                    ->latest()
                    ->get();
                // dd($orders->toArray());
                $this->jsonResponse = [
                    'status' => 1,
                    'data' => $orders->toArray(),
                ];
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function getOrder(Request $request)
    {
        # code...
    }

    //
    public function store($module, $data, $payload, $buyerId)
    {
        if (!empty($data)) {
            $order = new Order($data);
            $order->save();
            $orderId = $order->id;
            $update = Order::find($orderId);
            if ($orderId < 1000) {
                $orderNumber = 1000 + $orderId;
            }
            if ($update->update(['order_number' => $orderNumber])) {
                ///
                if ($module == "storage") {
                    $purchaseDetails = $payload['details'];

                    $purchaseDetails += [
                        'order_id' => $orderId,
                        'storage_id' => intval($payload['storage']),
                        'user_id' => $buyerId,
                    ];
                    StorageOrder::create($purchaseDetails);
                } else if ($module == "packing") {
                    $cartData = [];
                    if (!empty($payload['cart'])) {
                        foreach ($payload['cart'] as $key => $cart) {
                            $cartData[] = [
                                'order_id' => $orderId,
                                'packaging_id' => $cart['id'],
                                'user_id' => $buyerId,
                                'user_address_id' => 33,
                                "quantity" => $cart['quantity'],
                                "subtotal" => $cart['subtotal'],
                                "price" => $cart['price']
                            ];
                        }
                        PackagingOrder::insert($cartData);
                    }
                }

                return $orderNumber;
            }
        }
        return false;
    }
}
