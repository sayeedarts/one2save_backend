<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ContactRequest;
use App\Models\Country;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\PickupOption;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Arr;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use League\CommonMark\Extension\SmartPunct\Quote;

class CommonController extends Controller
{
    public $jsonResponse, $perPage;

    public function __construct()
    {
        $this->perPage = 10;
        $this->jsonResponse = [
            'status' => 0,
            'message' => 'Something went wrong!'
        ];
    }

    public function saveContactUs(Request $request)
    {
        if ($request->isMethod('post')) {
            $rawPayload = json_decode(request()->getContent(), true);

            try {
                ContactRequest::create($rawPayload);
                $this->jsonResponse = [
                    'status' => 1,
                    'message' => 'Request details collected successfully',
                ];
            } catch (\Exception $ex) {
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Some Exception was occured',
                ];
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function instaFeeds()
    {
        $response = Http::get('https://www.instagram.com/tanmayapatra09/?__a=1');
        if ($response->status() == 200) {
            echo $response->json();
        }
    }

    public function countries()
    {
        $countries = Country::select('id', 'name', 'phonecode', 'emoji', 'emojiU')
            ->where('id', 232)
            ->orderBy('name', 'asc')
            ->get();

        if ($countries->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $countries,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function cities(Request $request)
    {
        $countries = City::query()
            ->select('id', 'name')
            ->where('country_id', $request->input('country'))
            ->orderBy('name', 'asc')
            ->get();

        if ($countries->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $countries,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function getAddress(Request $request)
    {
        if ($request->isMethod('post')) {
            $payload = json_decode(request()->getContent(), true);
            $email = $payload['email'];
            $user = User::whereEmail($email);
            if ($user->count() > 0) {
                $userInfo = $user->first();
                $userAddress = UserAddress::whereUserId($userInfo->id)->first();
                if (!empty($userAddress)) {
                    $this->jsonResponse = [
                        'status' => 1,
                        'data' => [
                            "fullname" => $userAddress->fullname,
                            "email" => $userAddress->email,
                            "mobile" => $userAddress->mobile,
                            "country" => $userAddress->country,
                            "city" => $userAddress->city,
                            "address" => $userAddress->address,
                            "postcode" => $userAddress->postcode,
                            "same_as_billing" => $userAddress->same_as_billing,
                            "shipping_fullname" => $userAddress->shipping_fullname,
                            "shipping_email" => $userAddress->shipping_email,
                            "shipping_mobile" => $userAddress->shipping_mobile,
                            "shipping_country" => $userAddress->shipping_country,
                            "shipping_city" => $userAddress->shipping_city,
                            "shipping_address" => $userAddress->shipping_address,
                            "shipping_postcode" => $userAddress->shipping_postcode,
                            "updated_at" => dbtoDate($userAddress->updated_at, 'M/d/Y'),
                        ]
                    ];
                }
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function updateAddress(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = json_decode(request()->getContent(), true);
            // Check existance of incoming data
            $userId = null;
            $user = User::whereEmail($postData['anchor']);
            if ($user->count() > 0) {
                $userData = $user->first();
                $userId = $userData->id;
            }
            // $postData = json_decode($payload['data'], true);
            // dd($postData);
            $validate = \Validator::make($postData, [
                'fullname' => 'required',
                // 'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            ]);
            if ($validate->fails()) {
                $errors = $validate->errors()->toArray();
                $getErrors = [];
                foreach ($errors as $error) {
                    $getErrors[] = $error[0];
                }
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Validation Error',
                    'errors' => $getErrors
                ];
            } else {
                $user = User::whereEmail($postData['email'])->select('id')->first();
                $actionId = 0;
                $data = [
                    "fullname" => $postData['fullname'],
                    "email" => $postData['email'],
                    "mobile" => intval($postData['mobile']),
                    // "company" => $postData['company'],
                    "country" => $postData['country'],
                    "city" => $postData['city'],
                    "address" => $postData['address'],
                    "postcode" => intval($postData['postcode']),
                    "same_as_billing" => intval($postData['same_as_billing']),
                    "shipping_fullname" => $postData['shipping_fullname'],
                    "shipping_email" => $postData['shipping_email'],
                    "shipping_mobile" => intval($postData['shipping_mobile']),
                    // "shipping_company" => $postData['shipping_company'],
                    "shipping_country" => $postData['shipping_country'],
                    "shipping_city" => $postData['shipping_city'],
                    "shipping_address" => $postData['shipping_address'],
                    "shipping_postcode" => intval($postData['shipping_postcode']),
                    "user_id" => !empty($userId) ? $userId : 0
                ];
                $address = UserAddress::whereUserId($userId);
                try {
                    if ($address->count() == 0) {
                        // Add
                        $save = new UserAddress($data);
                        $save->save();
                        $actionId = $save->id;
                    } else {
                        // Update
                        $updateData = \Arr::except($data, ['email']);
                        $actionId = $address->update($data);
                    }
                    if (!empty($actionId)) {
                        $this->jsonResponse = [
                            'status' => 1,
                            'message' => 'Address details updated successfully'
                        ];
                    }
                } catch (\Exception $ex) {
                    // echo $ex->getMessage(); exit;
                    $this->jsonResponse = [
                        'status' => 0,
                        'message' => "Some Exception occured during the updation process. Please check and try again"
                    ];
                }
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Get list of Pickup options
     */
    public function pickupOptions(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $initPickup = PickupOption::pluck('title', 'id');
                $this->jsonResponse = [
                    'status' => 1,
                    'data' => $initPickup->toArray()
                ];
            } catch (\Exception $ex) {
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Something went wrong',
                    'exception' => $ex->getMessage()
                ];
            }
        }
        return response()
            ->json($this->jsonResponse, 200);
    }

    public function serviceAdditionalHelps(Request $request)
    {
        if ($request->isMethod('get')) {
            try {
                $getHelps = Page::where('type', 'service_addl_help')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'asset' => show($item->asset, $item->type, '60x60')
                        ];
                    });
                $this->jsonResponse = [
                    'status' => 1,
                    'data' => $getHelps
                ];
            } catch (\Exception $ex) {
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Something went wrong',
                    'exception' => $ex->getMessage()
                ];
            }
        }

        return response()
            ->json($this->jsonResponse, 200);
    }

    public function getHomeMasters(Request $request)
    {
        $homeData = [];
        $resolution = config('params.resolutions');
        if ($request->isMethod('get')) {
            try {
                $pageData = Page::get();
                foreach ($pageData as $key => $data) {
                    $homeData[$data->type][] = [
                        'title' => $data->name,
                        'content' => $data->content,
                        'asset' => !empty($data->asset) ? show($data->asset, $data->type, $resolution[$data->type][0]) : NULL
                    ];
                }
                $settings = Setting::first();
                $homeData['settings'] = [
                    'brand' => [
                        'name' => $settings->company_name,
                        'logo' => asset('/public/uploads/profile') . '/' . $settings->logo,
                    ],
                    "address" => $settings->address,
                    "phone" => $settings->phone,
                    "company_email" => $settings->company_email,
                    "copyright" => $settings->copyright,
                    "android_app_link" => $settings->android_app_link,
                    "ios_app_link" => $settings->ios_app_link,
                    "facebook" => $settings->facebook,
                    "instagram" => $settings->instagram,
                    "twitter" => $settings->twitter,
                    "youtube" => $settings->youtube,
                ];

                $homeData['settings']['metadata'] = [
                    'title' => $settings->seo_title ?? "",
                    'keywords' => $settings->seo_keywords ?? "",
                    'description' => $settings->seo_description ?? "",
                    'image' => asset('/public/uploads/profile') . '/' . $settings->logo,
                ];
    

                $this->jsonResponse = [
                    'status' => 1,
                    'data' => $homeData
                ];
            } catch (\Exception $ex) {
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Something went wrong',
                    'exception' => $ex->getMessage()
                ];
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function refreshState(Request $request)
    {
        $settings = Setting::pluck('refresh_state');
        if ($settings) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $settings[0]
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function updateRefreshState()
    {
        if (Setting::where(['refresh_state' => 1])->update(['refresh_state' => 0])) {
            $this->jsonResponse = [
                'status' => 1,
                'message' =>  "Data updated successfully"
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function getSiteSettings(Request $request)
    {
        if ($request->isMethod('get')) {
            $serviceList = [];
            try {
                // Services
                $services = Service::with('category')
                    // ->where('display_type', ['page'])
                    // ->orWhere('display_type', ['both'])
                    ->orderBy('sorting')
                    ->get();
                foreach ($services as $key => $service) {
                    $categories = [];
                    $hasSubmenus = false;
                    foreach ($service->category as $ind => $category) {
                        $hasSubmenus = (in_array($category->display_type, ['both', 'page'])) ? true : false;
                        $categories[$ind] = [
                            'id' => $category->id,
                            'slug' => $category->slug,
                            'title' => $category->title,
                            'display_type' => $category->display_type
                        ];
                    }
                    $serviceList[$key] = [
                        'id' => $service->id,
                        'title' => $service->title,
                        'slug' => $service->slug,
                        'featured' => $service->featured,
                        'display_type' => $service->display_type,
                        'sorting' => $service->sorting,
                        'has_submenu' => $hasSubmenus,
                        'categories' => $categories
                    ];
                }
                // Mandatory Pages
                // $pageList = [];
                // $pages = Page::where('is_default', 1)->get();
                // foreach ($pages as $key => $page) {
                //     $pageList[$page->slug] = [
                //         'title' => $service->title,
                //         'slug' => $service->slug,
                //         'asset' => $service->asset,
                //         'content' => $service->content,
                //     ];
                // }

                // Settings
                $settings = Setting::first();
                $settingData = new \stdClass();
                $settingData->brand = [
                    'name' => $settings->company_name,
                    'logo' => asset('/public/uploads/profile') . '/' . $settings->logo,
                ];
                $settingData->address = $settings->address;
                $settingData->vat = $settings->vat;
                $settingData->phone = $settings->phone;
                $settingData->company_email = $settings->company_email;
                $settingData->copyright = $settings->copyright;
                $settingData->android_app_link = $settings->android_app_link;
                $settingData->ios_app_link = $settings->ios_app_link;
                $settingData->facebook = $settings->facebook;
                $settingData->instagram = $settings->instagram;
                $settingData->twitter = $settings->twitter;
                $settingData->youtube = $settings->youtube;

                $this->jsonResponse = [
                    'status' => 1,
                    'data' => [
                        'services' => $serviceList,
                        'settings' => [
                            $settingData
                        ],
                        // 'pages' => $pageList
                    ]
                ];
            } catch (\Exception $ex) {
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Something went wrong',
                    'exception' => $ex->getMessage()
                ];
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function getImpSettings()
    {
        try {
            $settings = Setting::first();
            $settingData = new \stdClass();
            $settingData->vat = $settings->vat;

            $this->jsonResponse = [
                'status' => 1,
                'data' => $settingData
            ];
        } catch (\Exception $ex) {
            $this->jsonResponse = [
                'status' => 0,
                'message' => 'Something went wrong',
                'exception' => $ex->getMessage()
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function quoteRequests(Request $request)
    {
        if ($request->isMethod('post')) {
            $payload = json_decode(request()->getContent(), true);
            // Check existance of incoming data
            try {
                $userId = null;
                $user = User::whereEmail($payload['email']);
                if ($user->count() > 0) {
                    $userData = $user->first();
                    $userId = $userData->id;
                }

                $quotes = QuoteRequest::query();
                if (!empty($payload['email'])) {
                    $quotes->where('email', $payload['email']);
                }
                $quotes->orWhere('user_id', $userId);
                $quoteDetails = $quotes->with('pickup_option')->latest()->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'location' => [
                            'from' => $item->from_location,
                            'to' => $item->to_location,
                        ],
                        'user' => [
                            'fullname' => $item->fullname,
                            'email' => $item->email
                        ],
                        'pickup_details' => [
                            'mode' => $item->pickup_option->title,
                            'date' => $item->pickup_date
                        ],
                        'quote_date' => datetoDB($item->created_at)
                    ];
                });

                $this->jsonResponse = [
                    'status' => 1,
                    'data' => $quoteDetails
                ];
            } catch (\Exception $ex) {
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Something went wrong',
                    'exception' => $ex->getMessage()
                ];
            }
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function blogs()
    {
        try {
            $blogs = Page::where(['type' => 'blogs'])
                ->latest()
                ->paginate($this->perPage);
            $this->jsonResponse = [
                'status' => 1,
                'data' => $blogs
            ];
        } catch (\Exception $ex) {
            $this->jsonResponse = [
                'status' => 0,
                'message' => 'Something went wrong',
                'exception' => $ex->getMessage()
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function getBlogSlugs(Request $request)
    {
        $blogs = Page::where('type', 'blogs')->pluck('slug');
        if ($blogs->count() > 0) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $blogs,
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function getBlogDetails(Request $request, $slug = "")
    {
        if (!empty($slug)) {
            try {
                $blogs = Page::where(['type' => 'blogs', 'slug' => $slug])
                    ->first();
                $this->jsonResponse = [
                    'status' => 1,
                    'data' => [
                        [
                            'id' => $blogs->id,
                            'name' => $blogs->name,
                            'slug' => $blogs->slug,
                            'content' => $blogs->content,
                            'created_at' => dbtoDate($blogs->created_at, 'M/d/Y h:i A'),
                            'asset' => show($blogs->asset, $blogs->type, '1200x730'),
                            'metadata' => [
                                'title' => $blogs->seo_title,
                                'description' => $blogs->seo_description,
                                'keywords' => $blogs->seo_keywords,
                                'image' => $blogs->asset_url,
                            ]
                        ]
                    ]
                ];
            } catch (\Exception $ex) {
                $this->jsonResponse = [
                    'status' => 0,
                    'message' => 'Something went wrong',
                    'exception' => $ex->getMessage()
                ];
            }
        }
        return response()
            ->json($this->jsonResponse);
    }

    public function pageDetails(Request $request, $slug = null)
    {
        $pageInfo = [];
        $pageType = $displayType = "";
        try {
            $initService = Service::where('slug', $slug)
                ->where('status', 'yes')
                ->where(function ($q) {
                    $q->where('display_type', 'page')
                        ->orWhere('display_type', 'both');
                })
                ->orderBy('sorting');
            if ($initService->count() > 0) {
                $pageInfo = $initService->first();
                $displayType = $pageInfo->display_type;
                $pageType = "service";
            } else {
                $initPage = Page::where('slug', $slug);
                $pageInfo = $initPage->first();
                $pageType = "page";
            }
            if (!empty($pageInfo)) {
                $this->jsonResponse = [
                    'status' => 1,
                    'data' => [
                        'title' => !empty($pageInfo->name) ? $pageInfo->name : $pageInfo->title,
                        'slug' => $pageInfo->slug,
                        'content' => $pageInfo->content,
                        'asset' => $pageInfo->asset_url,
                        'metadata' => [
                            'title' => $pageInfo->seo_title ?? "",
                            'keywords' => $pageInfo->seo_keywords ?? "",
                            'description' => $pageInfo->seo_description ?? "",
                            'image' => $pageInfo->asset_url,
                        ],
                        'page_type' => $pageType,
                        'service_display_type' => $displayType
                    ]
                ];
            }
        } catch (\Exception $ex) {
            $this->jsonResponse = [
                'status' => 0,
                'message' => $ex->getMessage()
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    /**
     * Get all page's Slugs for SSR Purpose
     */
    public function pageSlugs()
    {
        try {
            $pages = Page::where('is_default', 1)->pluck('slug');
            // $services = Service::where(['status' => 'yes', 'is_page' => 1])->pluck('slug');
            // $slugs = array_merge($pages->toArray(), $services->toArray());
            $services = [];
            $slugs = array_merge($pages->toArray(), $services);
            $this->jsonResponse = [
                'status' => 1,
                'data' => $slugs
            ];
        } catch (\Exception $ex) {
            $this->jsonResponse = [
                'status' => 0,
                'message' => $ex->getMessage()
            ];
        }

        return response()
            ->json($this->jsonResponse);
    }

    public function sendEmail()
    {
        if (env('NOTIFY_MAIL')) {
            $email = [
                'subject' => "Your login verification code",
                'greeting' => "Hi Tanmaya,",
                'body' => [
                    'Here is your login verification code: <br> 897899',
                ],
                'to' => 'tanmayapatra09@gmail.com',
                'more' => [],
                'action_text' => 'Login to Application',
                'action_url' => url('/user/login'),
            ];
            try {
                \Mail::to('tanmayapatra09@gmail.com')
                    ->send(new \App\Mail\SendQuoteMail($email));

                $this->jsonResponse = [
                    'status' => 1,
                    'message' => 'OTP sent to Email address'
                ];
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        }
        return response()
            ->json($this->jsonResponse);
    }

    // public function sendMyEmail()
    // {
    //     $email = new \SendGrid\Mail\Mail();
    //     $email->setFrom("tanmaya@amtechsa.com", "Example User");
    //     $email->setSubject("Sending with SendGrid is Fun");
    //     $email->addTo("tanmayasmtpdev@gmail.com", "Example User");
    //     $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
    //     $email->addContent(
    //         "text/html",
    //         "<strong>and easy to do anywhere, even with PHP</strong>"
    //     );
    //     $sendgrid = new \SendGrid('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');
    //     try {
    //         $response = $sendgrid->send($email);
    //         print $response->statusCode() . "\n";
    //         print_r($response->headers());
    //         print $response->body() . "\n";
    //     } catch (\Exception $e) {
    //         echo 'Caught exception: ' . $e->getMessage() . "\n";
    //     }
    // }

    public function sendSms(Request $request)
    {
        send_sms('+447979855252', "Hello This is a test SMS testing");
    }

    /**
     * Gallery Module
     */
    public function galleries()
    {
        $galleries = Gallery::get()->map(function ($item) {
            return [
                'title' => $item->title,
                'description' => $item->description,
                'asset' => $item->image_url
            ];
        });
        if ($galleries) {
            $this->jsonResponse = [
                'status' => 1,
                'data' => $galleries
            ];
        }
        return response()
            ->json($this->jsonResponse);
    }
}
