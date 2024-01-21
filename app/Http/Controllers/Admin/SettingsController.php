<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public $data;
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function add()
    {
        $this->data['title'] = "Settings";
        $this->data += Setting::first()->toArray();
        $this->data += User::whereId(\Auth::user()->id)->select('name', 'email')->first()->toArray();
        return view('admin.Settings.update', $this->data);
    }

    public function store()
    {
        $data = [
            // 'name' => $this->request->name,
            'phone' => $this->request->phone,
            'company_email' => $this->request->company_email,
            'vat' => $this->request->vat,
            'company_name' => $this->request->company_name,
            'android_app_link' => $this->request->android_app_link,
            'ios_app_link' => $this->request->ios_app_link,
            'facebook' => $this->request->facebook,
            'instagram' => $this->request->instagram,
            'twitter' => $this->request->twitter,
            'youtube' => $this->request->youtube,
            'address' => $this->request->address,
            'copyright' => $this->request->copyright,
            'refresh_state' => 1,
            'seo_title' => $this->request->seo_title,
            'seo_keywords' => $this->request->seo_keywords,
            'seo_description' => $this->request->seo_description,
        ];
        $user = [
            'name' => $this->request->name,
            'email' => $this->request->email,
        ];
        if ($this->request->hasFile('logo')) {
            $data['logo'] = upload($this->request->logo, 'profile');
        }
        $this->updatePassword();
        if (Setting::count() == 0) {
            Setting::create($data);
        } else {
            $validate = \Validator::make($this->request->all(), [
                'email' => 'unique:users,email,' . $this->request->id,
            ]);
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->errors())->withInput();
            } else {
                Setting::whereId(1)->update($data);
                User::whereId(\Auth::user()->id)->update($user);
                session()->flash('message', ['success' => 'Updated successfully']);
                return redirect()->back();
            }
        }
        session()->flash('message', ['danger' => 'Something went wrong']);
        return redirect()->back();
    }

    public function updatePassword()
    {
        // dd($this->request->old_password);
        if (!empty($this->request->old_password) && $this->request->password) {
            $userPassword = \Auth::user()->password;
            if (\Hash::check($this->request->old_password, $userPassword)) {
                User::whereId(\Auth::user()->id)->update([
                    'password' => \Hash::make($this->request->password),
                ]);
            }
        }
    }

    public function updateLogo()
    {
    }

    public function updateSocialLinks()
    {
    }

    /**
     * Mark all notifications as read by Admin
     *
     * @author Tanmaya <tanmayapatra09@gmail.com>
     * @date   21 Mar 2021
     * @return none
     */
    public function markAllRead()
    {
        $user = User::find(\Auth::user()->id);
        foreach ($user->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        // Redirect back
        return redirect()->back();
    }
}
