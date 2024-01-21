<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        // app()->bind("Hello", function($app, $params) {
        //     dd($paramsm );
        //     return "Got you";
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        $settings = [];
        if (Setting::count() > 0) {
            $settings = Setting::whereId(1)->first()->toArray();
            // Get Footer Contents
            $footer = Page::where('type', 'footer_two')->orWhere('type', 'footer_three');
            if ($footer->count() > 0) {
                $settings['footer'] = $footer->get()->toArray();
            }
        }
        $languages = \App\Models\Language::select('id', 'name', 'short_form', 'is_default')->where('status', 1)->get();
        if ($languages->isNotEmpty()) {
            $settings['languages'] = $languages->toArray();
        }
        // $defLang = \App\Models\Language::select('id', 'name', 'short_form')->where('status', 1)->first();
        // // echo app()->getLocale(); exit;
        // $settings['locale'] = app()->getLocale();
        View::share('settings', $settings);
    }
}
