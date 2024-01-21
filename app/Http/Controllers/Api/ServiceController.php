<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdditionalService;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\ServiceCategoryItem;
use Mail;
use App\Models\User;
use App\Models\Page;
use App\Models\ServiceCategory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Blocktrail\CryptoJSAES\CryptoJSAES;

class ServiceController extends Controller
{
    public $jsonResponse;

    public function __construct()
    {
        $this->jsonResponse = [
            'status' => 0,
            'message' => 'Something went wrong!'
        ];
    }

    public function service()
    {
        $services = Service::orderBy('sorting', 'asc')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    "title" => $service->title,
                    "content" => $service->content,
                    "slug" => $service->slug,
                    "icon" => show($service->icon, 'service', '50x50'),
                    "image" => show($service->image, 'service_image', '280x200'),
                    "featured" => $service->featured,
                    "display_type" => $service->display_type,
                    "status" => $service->status,
                ];
            });

        if ($services->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $services,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function serviceDetails($slug)
    {
        $service = Service::query();
        if (!empty($slug)) {
            $service->where('slug', $slug);
        }
        $service->with('category', 'category.items');
        $getDetails = $service->first();

        if (!empty($getDetails)) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $getDetails,
                'metadata' => [
                    'title' => $getDetails->seo_title,
                    'keywords' => $getDetails->seo_keywords,
                    'description' => $getDetails->seo_description,
                    'image' => $getDetails->asset_url,
                ]
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function shortServiceDetails($slug)
    {
        $service = Service::query();
        if (!empty($slug)) {
            $service->where('slug', $slug);
        }
        $service->with('category', 'category.items');
        $getDetails = $service->first();
        // dd($getDetails->toArraY());
        $categoryInfos = [];
        foreach ($getDetails->category as $key => $category) {
            $categoryInfos[$category->id] = [
                'id' => $category->id,
                'name' => $category->title,
                'icon_url' => $category->icon_url
            ];

            foreach ($category->items as $key => $item) {
                $categoryInfos[$category->id]['items'][] = [
                    'id' => $item->id,
                    'name' => $item->title,
                ];
            }
        }
        if (!empty($categoryInfos)) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $categoryInfos,
            ];
        }
        return response()
            ->json($this->jsonResponse);
    }

    public function serviceCategoryDetails(Request $request, $slug)
    {
        $initQuery = ServiceCategory::where('slug', $slug)
            ->with('service', 'items');
        if ($initQuery->count() > 0) {
            $category = $initQuery->first();
            $this->jsonResponse = [
                'status' => 1,
                'data' => [
                    [
                        'title' => $category->title,
                        'content' => $category->content,
                        'slug' => $category->slug,
                        'status' => $category->status,
                        'icon_url' => $category->icon_url,
                        'metadata' => [
                            'title' => $category->seo_title,
                            'keywords' => $category->seo_keywords,
                            'description' => $category->seo_description,
                            // 'image' => $getDetails->asset_url,
                        ],
                        'service' => [
                            'title' => $category->service->title,
                            'slug' => $category->service->slug,
                        ]
                    ]
                ],
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Get All Slugs of Services
     */
    public function getServiceSlugs()
    {
        $services = Service::pluck('slug');
        if ($services->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $services,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function getServiceDetailedSlugs()
    {
        $slugs = [];
        $services = Service::with('category')->get();
        foreach ($services as $key => $service) {
            $collectChild = [];
            foreach ($service->category as $key => $category) {
                // echo "<pre>";
                // print_r($category->toArray());
                // echo "</pre>";
                // $slugs[$service->id]["child"][] = $category->slug;
                $collectChild[] = $category->slug;
            }

            if (!empty($collectChild) && count($collectChild) > 0) {
                $slugs[$service->id] = [
                    "slug" => $service->slug,
                    "child" => $collectChild
                ];
                // $slugs[$service->id]["child"][]
            }
        }
        // echo "<pre>";
        // print_r(array_values($slugs));
        // echo "</pre>";
        // exit;
        if ($services->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => array_values($slugs),
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Gte only items list of all services
     */
    public function getAllServiceItems(Request $request)
    {
        $items = ServiceCategoryItem::orderBy('title')->pluck('title', 'id');
        // dd($items->toArray());
        if ($items->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $items
            ];
        }
        return response()
            ->json($this->jsonResponse);
    }

    public function getServiceCategorySlugs(Request $request, $service)
    {
        $slugList = [];
        $categories = Service::where('slug', $service)
            ->with('category')
            ->first();
        $slugList['parent'] = $categories->slug;

        foreach ($categories->category as $key => $category) {
            $slugList['child'][] = $category->slug;
        }

        if (count($slugList) > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $slugList,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Save incoming Quore Request
     */
    public function storeQuoteRequest(Request $request)
    {
        if ($request->method('POST')) {
            $postData = $request->all();
            $userId = 0;
            $userType = "guest";
            if (!empty($postData['user'])) {
                $user = User::whereEmail($postData['user']);
                if ($user->count() > 0) {
                    $userData = $user->select('id')->first()->toArray();
                    if ($userData['id']) {
                        $userId = $userData['id'];
                        $userType = "registered";
                    }
                }
            }

            // Collect Additional Information
            $additionalServices = [];
            if (!empty($postData['additional_info']['help_details'])) {
                foreach ($postData['additional_info']['help_details'] as $key => $info) {
                    $serviceType = "";
                    if ($info['helpId'] == 20) {
                        $serviceType = "porter";
                    } else if ($info['helpId'] === 21) {
                        $serviceType = "storage";
                    }
                    $additionalServices[] = [
                        'id' => $info['helpId'],
                        'qty' => intval($info['value']),
                        'type' => $serviceType
                    ];
                }
            }

            $saveData = [
                'from_location' => $postData['from_location'],
                'to_location' => $postData['to_location'],
                'fullname' => $postData['userData']['name'],
                'email' => $postData['userData']['email'],
                'mobile' => $postData['userData']['mobile'],
                'service_id' => $postData['selected_service_id'],
                'service_items' => json_encode($postData['selected_items']),
                'pickup_type' => $postData['pickup_data']['pickup_type'],
                'pickup_date' => $postData['pickup_data']['pickup_date'],
                'additional_services' => json_encode($additionalServices),
                //json_encode($postData['additional']),
                'instruction' => $postData['instruction'],
                'category_addl_items' => $postData['category_addl_items'],
                // 'porters' => $postData['porters'],
                'user_id' => $userId,
                'user_type' => $userType,
                'floor_details' => !empty($postData['additional_info']['floor_details']) ? json_encode($postData['additional_info']['floor_details']) : []
            ];
            $quoteRequest = new QuoteRequest($saveData);
            $quoteRequest->save();
            if ($quoteRequest->id) {
                $quoteHash = CryptoJSAES::encrypt($quoteRequest->id, env('CRYPTO_SECRET_KEY'));

                // Notify Admin about the new request
                $notify = $this->notifyUsers($saveData, $quoteRequest->id);

                $this->jsonResponse = [
                    'status' => 1,
                    'quote_number' => $quoteRequest->id,
                    'message' => 'Quote Request received successfully.',
                    'notify' => $notify
                ];
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Notify user by Email and SMS
     */
    public function notifyUsers($data, $quotationId)
    {
        $setting = Setting::select('phone')->first();
        $greeting = "Hi Admin, <br>";
        //$smsMessage = "You have got a new Quote Request. Please login and Review the Request.";
	$smsMessage = 'You have got a new Quote Request. Please <a href="'. env('APP_URL') . 'service/' . $quotationId . '/quote-generate?mode=stream' .'">Review the Request </a> or <a href="https://oneplace2save.co.uk/oneplace2save/admin/login">Login</a> to view.';
        try {
            // Send Email 
            $email = [
                'subject' => "New Quote Request Received",
                'greeting' => "Hi Admin,",
                'body' => [
                    $smsMessage
                ],
                'to' => $setting->company_email,
                'more' => [],
                'action_text' => 'Login to Application',
                'action_url' => url('/user/login'),
            ];
            Mail::to(env('MAIL_FROM_ADDRESS'))
                ->send(new \App\Mail\SendQuoteMail($email));

            // Send SMS to admin


            // send_sms($setting->phone, $smsMessage);

            return "Successfully Notified to User";
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * Return Quote's full details when requested with a unique ID
     */
    public function quoteRequestDetails(Request $request, $id)
    {
        $quoteFullDetails = $this->getQuoteDetails($id);
        if ($quoteFullDetails['status'] == 1) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $quoteFullDetails['data'],
            ];
        } else {
            $this->jsonResponse = [
                'status' => 0,
                'data' => $quoteFullDetails['data'],
                'error' => $quoteFullDetails['error']
            ];
        }


        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Get Quote's Full details
     */
    public function getQuoteDetails($id = "")
    {
        $quoteFullDetails = [];
        try {
            $getRequest = QuoteRequest::where('id', $id);
            if ($getRequest->count() > 0) {
                $quoteDetails = $getRequest->with('pickup_option')->first();
                $serviceItemDetails = $this->getServiceItemDetails($quoteDetails->service_items);
                $getAdditionalServiceDetails = $this->getAdditionalServiceDetails($quoteDetails);
                $serviceDetails = Service::whereId($quoteDetails['service_id'])->select('title', 'id')->first();
                $quoteFullDetails = [
                    "id" => $quoteDetails->id,
                    "serial" => 'QT' . $quoteDetails->id . 'OP' . \Carbon\Carbon::parse($quoteDetails->created_at)->timestamp,
                    "from_location" => $quoteDetails->from_location,
                    "to_location" => $quoteDetails->to_location,
                    "fullname" => $quoteDetails->fullname,
                    "email" => $quoteDetails->email,
                    "mobile" => $quoteDetails->mobile,
                    "service" => [
                        'id' => $serviceDetails->id,
                        'name' => $serviceDetails->title,
                        'category' => $serviceItemDetails
                    ],
                    "custom_item_list" => $quoteDetails->category_addl_items,
                    "pickup_details" => [
                        'id' => $quoteDetails->pickup_option['id'],
                        'name' => $quoteDetails->pickup_option['title'],
                        'date' => dbtoDate($quoteDetails->pickup_date, 'M/d/Y')
                    ],
                    "pickup_type" => $quoteDetails->pickup_option['title'],
                    "pickup_date" => $quoteDetails->pickup_date,
                    "additional_services" => $getAdditionalServiceDetails,
                    "floor_details" => json_decode($quoteDetails->floor_details, true),
                    "porters" => $quoteDetails->porters,
                    "instruction" => $quoteDetails->instruction,
                    "user_id" => $quoteDetails->user_id,
                    "user_type" => $quoteDetails->user_type,
                    "created_at" => dbtoDate($quoteDetails->created_at, 'M/d/Y'),
                ];
                return [
                    'status' => 1,
                    'data' => $quoteFullDetails,
                ];
            }
        } catch (\Exception $ex) {
            return [
                'status' => 0,
                'data' => [],
                'error' => $ex->getMessage()
            ];
        }
        return [
            'status' => 0,
            'data' => [],
            'error' => ''
        ];
    }

    public function getServiceItemDetails($data = [])
    {
        $itemDetails = [];
        if (!empty($data)) {
            $itemShortInfo = json_decode($data, TRUE);
            // dd($itemShortInfo);
            foreach ($itemShortInfo as $key => $info) {
                $serviceDetailsInit = ServiceCategoryItem::whereId($info['id']);
                if ($serviceDetailsInit->count() > 0) {
                    $serviceDetails = $serviceDetailsInit->with('service_categories', 'service_categories.service')->first();
                    if (!empty($serviceDetails->service_categories)) {
                        $itemDetails[$serviceDetails->service_categories->id]['id'] = $serviceDetails->service_categories->id;
                        $itemDetails[$serviceDetails->service_categories->id]['name'] = $serviceDetails->service_categories->title;
                        $itemDetails[$serviceDetails->service_categories->id]['items'][] = [
                            'item' => [
                                'id' => $serviceDetails->id,
                                'name' => $serviceDetails->title,
                                'count' => $info['count']
                            ]
                        ];
                    }
                }
            }
        }
        // dd($itemDetails);
        // dd(array_values($itemDetails));
        return array_values($itemDetails);
    }

    public function getAdditionalServiceDetails($data = [])
    {
        $getAddlSvcDetails = [];
        if (!empty($data->additional_services)) {
            $totalPorterRequested = !empty($data->porters) ? $data->porters : 2;
            $getShortInfo = json_decode($data->additional_services, true);
            // dd($getShortInfo);
            foreach ($getShortInfo as $key => $info) {
                $getDetails = Page::where(['id' => $info, 'type' => 'service_addl_help'])
                    ->first();
                if (!empty($getDetails->id)) {
                    $getAddlSvcDetails[$key] = [
                        'id' => $getDetails->id,
                        'name' => $getDetails->name,
                    ];
                    if (!empty($info['qty'])) {
                        $getAddlSvcDetails[$key]['extra'] = $info['qty'];
                        $getAddlSvcDetails[$key]['type'] = $info['type'];
                    }
                    // if ($getDetails->id == 20) {
                    //     $getAddlSvcDetails[$key]['required_porters'] = $totalPorterRequested;
                    // }
                }
            }
            return $getAddlSvcDetails;
        }
    }
}
