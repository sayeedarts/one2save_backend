<?php

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Hospital;
use App\Models\HospitalDepartment;
use App\Models\Patient;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
// use Form;
use Illuminate\Support\Facades\File;

// OnePlace2Save Helper Methods

function item($seq = 1, $records = [], $fill = false)
{
    $uuid = 'UUID';
    if (!empty($fill)) {
        $uuid = (string) Str::uuid();
    }
    $removeBtn = '';
    if (!empty($seq)) {
        $removeBtn = '<label for="simpleinput">&nbsp;</label><input type="button" data-id="' . $uuid . '" value="Remove" class="btn btn-sm btn-danger remove-item "/>';
    }
    if (!empty($records)) {
        $title = $records['title'];
        $status = $records['status'];
    }

    $itemHtml = '<div class="row item_' . $uuid . '"><div class="col-sm-5"><div class="form-group mb-3"><label for="simpleinput">Title</label>' . Form::text('item[]', !empty($title) ? $title : old('title'), ['class' => 'form-control', 'id' => 'simpleinput']) . '</div></div><div class="col-sm-5"><div class="form-group mb-3"><label for="simpleinput">Featured?</label>' . Form::select('item_featured[]', [1 => 'Yes', 0 => 'No'], $status ?? 1, ['class' => 'form-control hospital', 'id' => 'featured']) . '</div></div><div class="col-sm-2">' . $removeBtn . '</div></div>';

    return $itemHtml;
}

function yesNo()
{
    return [
        'yes' => 'Yes',
        'no' => 'No'
    ];
}

// Old Methods

function days()
{
    $days = [];
    for ($i = 0; $i < 7; $i++) {
        $days[] = strftime("%A", strtotime("last monday +$i day"));
    }

    return $days;
}

/**
 * Get currency code and  symbol
 */
function currency($type = "symbol")
{
    return $type == "code" ? "GBP" : "£";
}

// convert to other language
function lang($string)
{
    // $currentLang = app()->getLocale();
    // if ($currentLang != "en") {
    //     $lang = new GoogleTranslate();
    //     return $lang->setSource('en')->setTarget($currentLang)->translate($string);
    // }

    return $string;
}

function publicMenu()
{
    if (Hospital::count() > 0) {
        return Hospital::latest()->get()->toArray();
    }
    return [];
}
function path($module = null)
{
    $dir = "public";
    if ($module == "admin") {
        return $dir . '/admin/';
    }
    $dir .= '/';
    return $dir;
}

function datetoDB($date)
{
    return date('Y-m-d', strtotime($date));
}

function timetoDB($time)
{
    return date('H:i', strtotime($time));
}

function dbtoDate($date, $format = "m/d/Y")
{
    return date($format, strtotime($date));
}

/**
 * Calculate Age from the Date of Birth
 */
function age($date)
{
    return !empty($date) ? \Carbon\Carbon::parse($date)->age : null;
}

/**
 * Clean Oracle Date
 *
 * The date value come from HMH Oracle side
 * need to be formatted correctly
 */
function cleanOrDate($date, $deli = "/")
{
    $cleanDate = str_replace($deli, "-", $date);
    return [
        'date' => date('Y-m-d', strtotime($cleanDate)),
        'time' => date('H:i:s', strtotime($cleanDate)),
    ];
}

function ago($date)
{
    return \Carbon\Carbon::parse($date)->diffForHumans();
}

function lastNDays($day)
{
    $dates = [];
    for ($i = 0; $i < $day; $i++) {
        $dates[] = date('Y-m-d', strtotime("-{$i} days"));
    }
    return $dates;
}

/**
 * The array having key and value can be sort here
 */
function sortArray($array)
{
    $flipArray = array_flip($array);
    ksort($flipArray, SORT_STRING);
    return $flipArray;
}

function image_size()
{
    return [
        'thumb' => '100x100',
        'small' => '263x202',
        'medium' => '350x230',
        'large' => '750x400',
    ];
}

function upload($file, $dir = "")
{
    $fileName = "";
    if ($file) {
        $destinationPath = 'public/uploads/' . $dir;
        $extension = $file->getClientOriginalExtension();
        $fileName = md5($file->__toString()) . '.' . $extension;
        if ($file->move($destinationPath, $fileName)) {
            return $fileName;
        }
    }
    return null;
}

/**
 * Send SMS to mobile numbers by using SMS APIs
 */
function send_sms($to, $message)
{
    // Your Account SID and Auth Token from twilio.com/console
    $sid = env('TW_SMS_SID');
    $token = env('TW_SMS_TOKEN');
    $phoneNo = env('TW_SMS_NUM');
    $client = new \Twilio\Rest\Client($sid, $token);

    return "SMS Delivered";
    exit;

    // Use the client to do fun stuff like send text messages!
    $client->messages->create(
        // the number you'd like to send the message to
        $to,
        [
            // A Twilio phone number you purchased at twilio.com/console
            'from' => $phoneNo,
            // the body of the text message you'd like to send
            'body' => $message
        ]
    );
}

function uploadImage($file, $directory = "", $resolution = [])
{
    $fileName = "";
    $dimensionsList = config("params.resolutions");
    $dimensions = $dimensionsList[$directory];

    $destinationPath = public_path('uploads/' . $directory . '/');
    if (!File::isDirectory($destinationPath)) {
        File::makeDirectory($destinationPath, 0777, true, true);
    }

    // $dimensions = !empty($resolution) ? $resolution : image_size();
    if ($file) {
        // calculate md5 hash of encoded image
        $extension = $file->getClientOriginalExtension();
        $fileName = md5($file->__toString()) . '.' . $extension;
        foreach ($dimensions as $dimen) {
            $dimension = explode('x', $dimen);
            $convertImg = Interven::make($file)->resize($dimension[0], $dimension[1], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('jpg');
            // use hash as a name
            // $path = "uploads/";
            // if (!empty($directory)) {
            //     $path .= $directory . '/';
            // }
            $path = $destinationPath . "{$dimen}_{$fileName}";
            $convertImg->save($path);
        }
        return $fileName;
    }
    return null;
}

function show_file($name, $path)
{
    return asset('public') . '/uploads/' . $path . "/" . $name;
}

/**
 * Show different images with different imgae sizes
 */
function show($name, $category, $size)
{
    $directory = path() . "uploads";
    $sizeArray = image_size();
    $location = asset($directory);
    $phsicalPath = public_path() . '/' . $category . '/' . $size . '_' . $name;
    return $location . '/' . $category . '/' . $size . '_' . $name;
}

/**
 * delete files from the public folder according to the given module name
 */
function deleteFile($name, $category)
{
    $dimensionsList = config("params.resolutions");
    $resolutions = $dimensionsList[$category];
    $deleteFiles = [];
    foreach ($resolutions as $resolution) {
        $deleteFiles[] = public_path('uploads/' . $category . '/' . $resolution . '_' . $name);
    }
    return File::delete($deleteFiles);
}

function removeFile($name, $category)
{
    $dimensionsList = config("params.resolutions");
    $resolutions = $dimensionsList[$category];
    $deleteFiles = [];
    foreach ($resolutions as $resolution) {
        $deleteFiles[] = public_path('uploads/' . $category . '/' . $name);
    }
    return File::delete($deleteFiles);
}

/**
 * Format Price
 */

function format($amount, $decimal = 2)
{
    return number_format((float) $amount, $decimal, '.', '');
}

/**
 * Text Processings
 */

function limit_text($string, $limit = 70)
{
    return \Str::of(strip_tags($string))->limit($limit);
}

function random()
{
    return \Str::random(10) . strtotime("now");
}

function userDetails($email)
{
    $user = User::whereEmail($email);
    if ($user->count() > 0) {
        $userDetails = $user->first()->toArray();
        return $userDetails;
    }
}

function getPages()
{
    return \App\Models\Page::whereType('page')->orderBy('sort', 'ASC')->get()->toArray();
}

function page_types()
{
    return [
        'page' => 'Dynamic Page',
        'blogs' => 'Blogs',
        'faq' => 'Home > FAQ',
        'testimonials' => 'Home > Testimonials',
        'how_it_works' => 'Home > How it works',
        'storage_dvc_home_ad' => 'Home > Storage Device',
        'service_addl_help' => 'Service > Additional Help',
    ];
}

function readableText($string)
{
    return ucfirst(str_replace("_", " ", $string));
}

function checkPaypalOrderDetails($orderId = null)
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

function convert_to_title_case($string) {
    // Replace dashes with spaces
    $string = str_replace('-', ' ', $string);

    // Capitalize each word
    $string = ucwords($string);

    return $string;
}

function get_component_names()
{
    $components = [
        [
            "name" => "Manage Service",
            "access" => [
                "add-service",
                "view-service",
                "edit-service",
                "delete-service",
                "sort-service"
            ]
        ],
        [
            "name" => "Manage Service Category",
            "access" => [
                "add-service-category",
                "view-service-category",
                "edit-service-category",
                "delete-service-category"
            ]
        ],
        [
            "name" => "Manage Storage",
            "access" => [
                "add-storage",
                "view-storage",
                "edit-storage",
                "delete-storage"
            ]
        ],
        [
            "name" => "Manage Package",
            "access" => [
                "add-package",
                "view-package",
                "edit-package",
                "delete-package"
            ]
        ],
        [
            "name" => "Manage Page",
            "access" => [
                "add-page",
                "view-page",
                "edit-page",
                "delete-page"
            ]
        ],
        [
            "name" => "Manage Gallery",
            "access" => [
                "add-gallery",
                "view-gallery",
                "edit-gallery",
                "delete-gallery"
            ]
        ],
        [
            "name" => "Manage Files",
            "access" => [
                "add-file",
                "view-file",
                "delete-file"
            ]
        ],
        [
            "name" => "Manage Orders",
            "access" => [
                "view-order",
                "view-order-invoice",
                // "notify-order",
            ]
        ],
        [
            "name" => "Manage Quotes",
            "access" => [
                "view-quote",
                "notify-quote",
            ]
        ],
        [
            "name" => "Manage Templates",
            "access" => [
                "add-template",
                "view-template",
                "edit-template",
                "delete-template"
            ]
        ],
        [
            "name" => "Manage Brand Settings",
            "access" => [
                // "view-brand-settings",
                "edit-brand-settings",
            ]
        ]
    ];

    return $components;
}

/**
 * Check if a file is video or image
 */
function get_file_type($filePath) {
    $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedVideoExtensions = ['mp4', 'avi', 'mov'];

    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

    if (in_array(strtolower($fileExtension), $allowedImageExtensions)) {
        return 'image';
    } elseif (in_array(strtolower($fileExtension), $allowedVideoExtensions)) {
        return 'video';
    } else {
        return 'unknown';
    }
}
