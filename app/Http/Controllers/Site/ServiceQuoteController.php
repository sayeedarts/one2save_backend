<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App\Http\Controllers\Api\ServiceController;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Str;

class ServiceQuoteController extends Controller
{
    public function __construct()
    {
        // $this->data
    }

    /**
     * Get quote details and generate the PDF
     */
    public function generatePdf(Request $request, $id)
    {
        $data['pageTitle'] = "this is a example";
        $data['quote_details'] = [];
        $mode = !empty($request->mode) ? $request->mode : "download";

        // Get Service Details
        $quote = new ServiceController();
        $getDetails = $quote->getQuoteDetails($id);
        if ($getDetails['status'] == 1) {
            $pdfContent = $getDetails;
            // Get Template Details
            $template = Template::whereCategory('quotation')->first();
            $templateContent = $template->content;
            $pdfContent = Str::replace('[QUOTE_SL]', $pdfContent['data']['serial'], $templateContent);
            // Gather All informations
            $data['quote_details'] = $getDetails;
            $data['template_data'] = $pdfContent;
            // return view('pdf.quote-details', $data);
            $fileName = time() . $id . "_" . 'quote_request' . '.pdf';
            $pdf = PDF::loadView('pdf.quote-details', $data);
            return $pdf->{$mode}($fileName);
        } else {
            return abort(404);
        }
    }

    public function updateTemplate($data)
    {
        
    }

    /**
     * Generate Invocies for Orders
     */
    public function generateOrderInvoice(Request $request, $id)
    {
        $data['pageTitle'] = "Invoice";
        $mode = !empty($request->mode) ? $request->mode : "download";
        $invoice = [];
        $settings = Setting::first();
        $invoice = [
            'company' => [
                'company' => $settings->company_name,
                'logo' => asset('/public/uploads/profile') . '/' . $settings->logo,
                "address" => $settings->address,
                "phone" => $settings->phone,
                "company_email" => $settings->company_email,
                "copyright" => $settings->copyright,
            ],
            'user' => []
        ];
        $orderDetails = [];
        $storageDetails = $this->getStorageDetails($id);
        $packageDetails = $this->getPackageDetails($id);

        $module = "";
        if (!empty($storageDetails)) {
            $orderDetails = $storageDetails;
            $module = "STO";
        } else if (!empty($packageDetails)) {
            $orderDetails = $packageDetails;
            $module = "PCK";
        }
        $invoice['user'] = $orderDetails['user'];

        $invocieNumber = "";
        $invocieNumber .= $orderDetails['order_number'] . $module;
        $invocieNumber .= $orderDetails['buyer_type'] == "registered" ? "REG" : "URG";
        $invocieNumber .= date('Ymd', strtotime($orderDetails['order_date']));
        $invoice['invoice_no'] = $invocieNumber;
        $invoice['invoice_date'] = date('M/d/Y h:i:A', strtotime($orderDetails['order_date']));

        if ($module == "STO") {
            $invoice['data'][] = [
                'title' => $orderDetails['storage']['name'],
                'description' => 'Storage was purchased for ' . $orderDetails['storage']['duration'] . ' week(s)',
                'unit_price' => $orderDetails['price_subtotal'],
                'quantity' => 1,
                'total' => $orderDetails['price_subtotal'],
            ];
        } else {
            foreach ($orderDetails['packing'] as $key => $packing) {
                $invoice['data'][] = [
                    'title' => $packing['name'],
                    'description' => $packing['name'],
                    'unit_price' => $packing['price'],
                    'quantity' => $packing['quantity'],
                    'total' => ($packing['price'] * $packing['quantity']),
                ];
            }
        }
        $invoice['pricing'] = [
            'subtotal' => $orderDetails['price_subtotal'],
            'vat' => $orderDetails['price_vat'],
            'total' => $orderDetails['price_total'],
            'currency' => [
                'symbol' => '£',
                'code' => 'GBP'
            ]
        ];

        $data['invoice'] = $invoice;
        return view('pdf.order-invoice', $data);
    }

    public function getStorageDetails($orderId)
    {
        $details = [];
        try {
            $getOrder = Order::where(['id' => $orderId, 'module' => 'storage'])
                ->with('storage_order', 'storage_order.storage')
                ->first();

            // Calculate Difference
            $startDate = \Carbon\Carbon::parse($getOrder->storage_order->start_date);
            $endDate = \Carbon\Carbon::parse($getOrder->storage_order->end_date);
            $diff = $startDate->diffInDays($endDate);
            $details = [
                "id" => $getOrder->id,
                "order_number" => $getOrder->order_number,
                "buyer_type" => $getOrder->buyer_type,
                "price_subtotal" => $getOrder->price_subtotal,
                "price_vat" => $getOrder->price_vat,
                "price_total" => $getOrder->price_total,
                "price_currency" => $getOrder->price_currency,
                "status" => $getOrder->status,
                "payment_method" => $getOrder->payment_method,
                "payment_order_id" => $getOrder->payment_order_id,
                "payment_status" => ucfirst(strtolower($getOrder->payment_status)),
                "order_date" => $getOrder->created_at,
                "storage" => [
                    'id' => $getOrder->storage_order->storage->id,
                    'name' => $getOrder->storage_order->storage->name,
                    "price" => $getOrder->storage_order->storage->price,
                    "quantity" => 1,
                    "start_date" => $getOrder->storage_order->start_date,
                    "end_date" => $getOrder->storage_order->end_date,
                    "duration" => ceil($diff / 7),
                    "duration_in" => "week(s)",
                    "currency" => $getOrder->price_currency
                ],
                "user" => [
                    "name" => $getOrder->storage_order->name,
                    "email" => $getOrder->storage_order->email,
                    "mobile" => $getOrder->storage_order->mobile,
                    "postcode" => $getOrder->storage_order->postcode,
                ]
            ];
        } catch (\Exception $ex) {
        }

        return $details;
        // dump($details);
        // dd($getOrder->toArray());
    }

    public function getPackageDetails($orderId)
    {
        $details = [];
        try {
            $getOrder = Order::where(['id' => $orderId, 'module' => 'packing'])
                ->with('packing_orders', 'packing_orders.packing')
                ->first();

            $details = [
                "id" => $getOrder->id,
                "order_number" => $getOrder->order_number,
                "buyer_type" => $getOrder->buyer_type,
                "price_subtotal" => $getOrder->price_subtotal,
                "price_vat" => $getOrder->price_vat,
                "price_total" => $getOrder->price_total,
                "price_currency" => $getOrder->price_currency,
                "status" => $getOrder->status,
                "payment_method" => $getOrder->payment_method,
                "payment_order_id" => $getOrder->payment_order_id,
                "payment_status" => ucfirst(strtolower($getOrder->payment_status)),
                "order_date" => $getOrder->created_at,
                "packing" => [],
                "user" => []
            ];
            $userId = $getOrder->buyer_id;
            $getUser = User::find($userId);
            $details["user"] = [
                "name" => $getUser->name,
                "email" => $getUser->email,
                "mobile" => $getUser->mobile,
                // "address" => $getUser->address,
                "postcode" => $getUser->postcode,
            ];

            foreach ($getOrder->packing_orders as $key => $packing) {
                $details["packing"][] = [
                    "id" => $packing->packing->id,
                    "name" => $packing->packing->name,
                    "price" => $packing->price,
                    "quantity" => $packing->quantity,
                ];
            }
        } catch (\Exception $ex) {
            //throw $th;
        }

        return $details;
    }
}
