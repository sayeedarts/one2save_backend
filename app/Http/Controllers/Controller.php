<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Notification;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $locale;

    public function __construct()
    {
        $getLangauge = \App\Models\Language::whereIsDefault(1)->select('short_form', 'id')->first();
        session()->put('locale', $getLangauge->short_form);
        $this->ln = app()->getLocale();
    }

    public function activeLang()
    {
        return app()->getLocale();
    }

    public function defaultLang()
    {
        $default = \App\Models\Language::whereIsDefault(1)->select('short_form', 'id')->first();
        return $default->short_form;
    }

    public function getLanguages()
    {
        $langs = \App\Models\Language::select('id', 'name', 'short_form', 'is_default')
            ->where('status', 1)
            ->get();
        if ($langs->isNotEmpty()) {
            return $langs->toArray();
        }
    }

    /**
     * Add Notification to Database for specific user
     *
     * @param $notify array
     *
     * @author tanmayapatra
     * @date 26 Dec 2020
     * @return mixed
     */
    public function addToHistory($notify = [])
    {
        $user = \App\Models\User::find(1);
        Notification::send($user, new \App\Notifications\TaskComplete($notify));
    }
}
