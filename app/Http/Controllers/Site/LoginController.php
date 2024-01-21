<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use App\Jobs\PatientBackgroundJob;

class LoginController extends Controller
{
    public $data;
    public $request;
    protected $rules = [
        'email' => 'required|min:3',
        'password' => 'required|min:3',
    ];

    public function __contruct(Request $request)
    {
        $this->request = $request;
        if (Auth::check()) {
            return redirect()->route("admin-dashboard");
        }
    }

    public function show()
    {
        $this->data['title'] = __("login");
        return view('site.auth.login', $this->data);
    }

    /**
     * Login user with credentials
     */
    public function doLogin(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $user = User::where(['email' => $request->email, 'role' => 'patient'])->first();
            if ($user) {
                // if (Auth::guard($user->role)->attempt(['email' => $request->email, 'password' => $request->password])) {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    Auth::loginUsingId($user->id, true);
                    // Add jobs to batch and process it
                    $batch = $this->runJobs([
                        "patient_visits", "lab_reports", "radio_reports", "patient_booking", "patient_medicine"
                    ]);
                    // dd($batch);
                    session(['batch_id' => $batch->id]);
                    return redirect()->to('/user/my-account');
                }
            }
        }
        session()->flash('message', ['danger' => 'Username or Password is wrong']);
        return back()->withInput($request->only('email', 'remember'));
    }

    public function runJobs($jobs)
    {
        $batch = \Illuminate\Support\Facades\Bus::batch([])->dispatch();
        foreach ($jobs as $key => $job) {
            $batch->add(new PatientBackgroundJob([
                'type' => $job,
                'user_id' => Auth::user()->id
            ]));
        }

        return $batch;
    }

    /**
     * Admin Login Page
     */
    public function adminLoginShow()
    {
        $this->data['title'] = "Login";
        return view('site.auth.admin-login', $this->data);
    }

    public function adminLoginPost(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                // if (Auth::guard($user->role)->attempt(['email' => $request->email, 'password' => $request->password])) {
                if (Auth::guard("admin")->attempt(['email' => $request->email, 'password' => $request->password])) {
                    Auth::loginUsingId($user->id, TRUE);
                    session()->flash('message', ['success' => 'Registration is success. Please check you email']);
                    return redirect()->to('/admin/dashboard');
                }
            }
        }
        session()->flash('message', ['danger' => 'Username or Password is wrong']);
        return redirect()->back();
    }

    /**
     * Logout from the session for all Users
     * 
     * @param $request 
     * 
     * @author tanmayapatra
     * @date 30 dec 2020
     * @return mixed
     */
    public function logout(Request $request)
    {
        $guard = Auth::user()->role;
        if (Auth::user()->role == "patient") {
            $guard = "user";
        }
        // Auth::guard('web')->logout();
        // Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->flush();
        Auth::logout();
        return redirect(route($guard . '.login'));
    }
}
