<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\OrderController;
use App\Models\User;
use Blocktrail\CryptoJSAES\CryptoJSAES;

class StorageController extends Controller
{
    public $jsonResponse;

    public function __construct()
    {
        $this->jsonResponse = [
            'status' => 0,
            'message' => 'Something went wrong!'
        ];
    }

    public function storage(Request $request)
    {
        $services = Storage::orderBy('sorting')->with('images', 'type')
            ->get()
            ->map(function ($storage) {
                $images = [];
                foreach ($storage->images as $image) {
                    $images[] = $image->image_url;
                }
                return [
                    'id' => $storage->id,
                    "type" => $storage->type->name,
                    "name" => $storage->name,
                    "file" => !empty($storage->file) ? show_file($storage->file, 'storage') : "",
                    "price" => $storage->price,
                    "area" => $storage->area,
                    "dimension" => $storage->dimension,
                    "description" => $storage->description,
                    "user_id" => $storage->user_id,
                    "created_at" => $storage->created_at,
                ];
            });

        if ($services->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'message' => 'Patients Data successfully fetched',
                'data' => $services,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function storageDetails(Request $request)
    {
    }

    public function storagePayment(Request $request)
    {
        if ($request->isMethod('post')) {
            $rawPayload = json_decode(request()->getContent(), true);
            $payload = CryptoJSAES::decrypt($rawPayload['data'], env('CRYPTO_SECRET_KEY'));
            if ($payload) {
                $payloadArr = json_decode($payload, true);
                $buyerId = 0;
                $buyerType = "guest";
                if (!empty($payloadArr['user'])) {
                    $user = User::whereEmail($payloadArr['user']);
                    if ($user->count() > 0) {
                        $user = $user->pluck('id')->toArray();
                        $buyerId =  $user[0];
                        $buyerType = "registered";
                    }
                }
                $orderId = $payloadArr['paymentResponse']['orderID'];
                $orderDetails = $this->checkPaymentDetails($orderId);

                if ($orderDetails['purchase_units'][0]['amount']['value'] === $payloadArr['paymentDetails']['total']) {
                    $subTotal = $payloadArr['paymentDetails']['subtotal'];
                    $vat = $payloadArr['paymentDetails']['vat'];
                    $total = $payloadArr['paymentDetails']['total'];
                }

                $paymentDetails = [
                    'order_number' => 0,
                    'module' => 'storage',
                    'buyer_id' => $buyerId,
                    'buyer_type' => $buyerType,
                    'price_subtotal' => $subTotal, //$orderDetails['purchase_units'][0]['amount']['value'],
                    'price_total' => $total, //$orderDetails['purchase_units'][0]['amount']['value'],
                    'price_vat' => $vat,
                    'price_currency' => $orderDetails['purchase_units'][0]['amount']['currency_code'],
                    'payment_status' => $orderDetails['status'],
                    'payment_method' => 'paypal',
                    'payment_order_id' => $orderDetails['id'],
                ];
                try {
                    $order = new OrderController();
                    $orderNumber = $order->store('storage', $paymentDetails, $payloadArr, $buyerId);

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
                        'message' => 'Exception occured while processing the request'
                    ];
                }

                return response()
                    ->json($this->jsonResponse);
            }
        }
    }

    public function checkPaymentDetails($orderId = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-m.sandbox.paypal.com/v2/checkout/orders/' . $orderId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic QWVDcFU0RGdqZW1wOE9UVi1FRzZEckViZGZYQ0NnNzFYV3FZOU1oeW9rTmsxMS1YYjE3bUhqMldIOFNpb3hOR1pyckgyV2ExX2tDMlAwVDk6RUVnWE84anBvWENiVlZTUlhXUGY2SllONk5aeHNwakQzVE4zZlV2VjBZTXNkbWtJbk1NdGJzRk9BYzdyQTlEb3VjVlBuenNxczE2dmpQclA='
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        if ($response) {
            return json_decode($response, true);
        }
        return [];
    }
}
