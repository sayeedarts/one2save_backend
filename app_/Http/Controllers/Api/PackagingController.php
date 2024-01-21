<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Packaging;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Blocktrail\CryptoJSAES\CryptoJSAES;

class PackagingController extends Controller
{
    public $jsonResponse;

    public function __construct()
    {
        $this->jsonResponse = [
            'status' => 0,
            'message' => 'Something went wrong!'
        ];
    }

    public function packagings()
    {
        $packagings = Packaging::latest()
            ->with('images')
            ->get()
            ->map(function ($packaging) {
                $images = [];
                foreach ($packaging->images as $key => $item) {
                    $images[] = $item->image_url;
                }
                return [
                    'id' => $packaging->id,
                    "name" => $packaging->name,
                    "price" => $packaging->price,
                    "currency" => currency(),
                    "short_description" => $packaging->short_description,
                    "images" => $images
                ];
            });
        if ($packagings->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'message' => 'Patients Data successfully fetched',
                'data' => $packagings,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Get Single packaging Details
     */
    function packaging($id = null)
    {
        $packaging = Packaging::whereId($id)
            ->with('images', 'category')
            ->first();
        $images = [];
        if (!empty($packaging) || $packaging != null) {
            foreach ($packaging->images as $key => $item) {
                $images[] = $item->image_url;
            }
            $relatedPackings = Packaging::whereCategoryId($packaging['category_id'])
                ->with('images')
                ->limit(4)
                ->get();

            $packingDetails = new \stdClass();
            $packingDetails->id = $packaging['id'];
            $packingDetails->name = $packaging['name'];
            $packingDetails->price = $packaging['price'];
            $packingDetails->category = $packaging['category']['name'];
            $packingDetails->dimension = $packaging['dimension'];
            $packingDetails->short_description = $packaging['short_description'];
            $packingDetails->description = $packaging['description'];
            $packingDetails->images = $images;
            $packingDetails->created_at = $packaging['created_at'];
            $packingDetails->related = $relatedPackings;
            $this->jsonResponse = [
                'status' => 1,
                'message' => 'Patients Data successfully fetched',
                'data' => [
                    $packingDetails
                ],
            ];
        } else {
            $this->jsonResponse = [
                'status' => 0,
                'message' => 'Requested Packing Details not found!'
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function packageIds()
    {
        $packagingIds = Packaging::pluck('id');
        if ($packagingIds->count()) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $packagingIds,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function packingPayment(Request $request)
    {
        if ($request->isMethod('post')) {
            $rawPayload = json_decode(request()->getContent(), true);
            $payload = CryptoJSAES::decrypt($rawPayload['data'], env('CRYPTO_SECRET_KEY'));
            if ($payload) {
                $payloadArr = json_decode($payload, true);
                // dd($payloadArr);
                $buyerId = 0;
                $buyerType = "guest";
                if (!empty($payloadArr['user'])) {
                    $user = User::whereEmail($payloadArr['user']);
                    if ($user->count() > 0) {
                        $user = $user->pluck('id')->toArray();
                        $buyerId =  $user[0];
                        $buyerType = "registered";
                        // Check and Get the Billing address
                        $address = UserAddress::where('user_id', $buyerId)->pluck('id')[0];
                    }
                }

                $orderId = $payloadArr['paymentResponse']['orderID'];
                $orderDetails = checkPaypalOrderDetails($orderId);

                if (format($payloadArr['price']['total'], 2) === format($orderDetails['purchase_units'][0]['amount']['value'], 2)) {
                    $subTotal = $payloadArr['price']['subtotal'];
                    $vat = $payloadArr['price']['vat'];
                    $total = $payloadArr['price']['total'];
                }

                $paymentDetails = [
                    'order_number' => 0,
                    'module' => 'packing',
                    'buyer_id' => $buyerId,
                    'buyer_type' => $buyerType,
                    'price_vat' => $vat,
                    'price_subtotal' => $subTotal,
                    'price_total' => $total, //$orderDetails['purchase_units'][0]['amount']['value'],
                    'price_currency' => $orderDetails['purchase_units'][0]['amount']['currency_code'],
                    'payment_status' => $orderDetails['status'],
                    'payment_method' => 'paypal',
                    'payment_order_id' => $orderDetails['id'],
                ];
                try {
                    $order = new OrderController();
                    $orderNumber = $order->store('packing', $paymentDetails, $payloadArr, $buyerId);

                    if ($orderDetails['status'] == "COMPLETED") {
                        $this->jsonResponse = [
                            'status' => 1,
                            'message' => 'Order is successfully placed',
                            'data' => [
                                'order_number' => $orderNumber
                            ],
                        ];
                    }
                } catch (\Exception $ex) {
                    $this->jsonResponse = [
                        'status' => 0,
                        'message' => 'Exception occured while processing the request',
                        'details' => $ex->getMessage()
                    ];
                }

                return response()
                    ->json($this->jsonResponse);
            }
        }
    }
}
