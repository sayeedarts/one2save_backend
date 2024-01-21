<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\QuoteRequest;
use App\Models\User;
use Mail;
use Illuminate\Support\Facades\Hash;
use League\CommonMark\Extension\SmartPunct\Quote;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
{
    public $request;
    public $data;

    /**
     * Constructer
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->data['required'] = '<span class="text-danger">*</span>';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['title'] = "Admin User List";
        $this->data['users'] = User::with('roles')->where(["role" => "admin"])->latest()->get();

        return view('admin.Users.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $this->data['title'] = "Add Admin User";
        $this->data['mode'] = 'store';
        $this->data['roles'] = Role::where('name', '!=', 'admin')->pluck('name', 'name');
        if (!empty($id)) {
            $this->data['title'] = "Edit Admin User";
            $this->data += User::find($id)->toArray();
            $this->data['mode'] = 'update';
            $user = User::with('roles')->find($id);
            $role = $user->roles->pluck('name')->toArray();
            if (!empty($role[0])) {
                $this->data['role'] = $role[0];
            }
        }
        return view('admin.Users.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = \Validator::make($request->all(), [
            'name' => 'required|min:1',
            // 'role' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|regex:/^\+?\d{1,3}[-.\s]?\(?\d{1,4}\)?[-.\s]?\d{1,10}$/',
            'password' => 'required|string|min:6|max:30|regex:/^[a-zA-Z0-9]+$/',
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            // echo ">" . $request->password . "<"; exit;
            $postData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => "admin",
                'mobile' => $request->mobile,
                'password' => $request->password
            ];

            try {
                $user = User::create($postData);
                $user->assignRole($request->role);
                $message = "User successfully created.";
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
            }
            session()->flash('message', ['success' => $message]);
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|min:1',
            'role' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile' => 'required|regex:/^\+?\d{1,3}[-.\s]?\(?\d{1,4}\)?[-.\s]?\d{1,10}$/',
        ];
        if (!empty($request->password)) {
            $rules += [
                'password' => 'required|string|min:6|max:30|regex:/^[a-zA-Z0-9]+$/',
            ];
        }
        $validate = \Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $postData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => "admin",
                'mobile' => $request->mobile
            ];
            if (!empty($request->password)) {
                $postData += [
                    'password' => Hash::make($request->password)
                ];
            }

            try {
                User::whereId($id)->update($postData);
                $user = User::whereId($id)->first();
                // Remove all roles from the user
                $user->syncRoles([]);
                $user->assignRole($request->role);
                $message = "User successfully updated.";
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
            }
            session()->flash('message', ['success' => $message]);
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::whereId($id)->first();
        // Remove all roles from the user
        $user->syncRoles([]);
        if (User::find($id)->delete()) {
            session()->flash('message', ['success' => 'Record was deleted successfully']);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went wrong']);
        return redirect()->back();
    }

    // /**
    //  * Show Registrants list with various options
    //  * 
    //  * @author tanmayapatra
    //  * @date 01 jan 2021
    //  * @return view
    //  */
    // public function registrants()
    // {
    //     $this->data['title'] = __('general.registrant_list');
    //     $this->data['patients'] = Patient::with(
    //         'user',
    //         'hospital',
    //         'nationality_info',
    //         'religion_info',
    //         'nationalid_type',
    //         'gender_info',
    //         'mrns',
    //         'mrns.hospital'
    //     )->latest()->get();
    //     // dd($this->data['patients']->toArray());
    //     return view('admin.Users.registrant-list', $this->data);
    // }

    // /**
    //  * Active or inactive Registrants
    //  * 
    //  * @param $request Request
    //  * 
    //  * @author tanmayapatra
    //  * @date 01 jan 2021
    //  * @return redirection
    //  */
    // public function toggleRegistrant(Request $request)
    // {
    //     $user = User::find($this->request->user_id);
    //     $user->is_active = !$user->is_active;
    //     $user->save();
    //     $user->refresh();
    //     $status = $user->is_active;
    //     if (!empty($this->request->reason)) {
    //         Patient::whereUserId($this->request->user_id)->update(['rejection_reason' => $this->request->reason]);
    //     }
    //     if (!empty($status)) {
    //         Patient::whereUserId($this->request->user_id)->update(['rejection_reason' => ""]);
    //     }
    //     session()->flash('message', ['success' => __('general.registrant_activated')]);
    //     return redirect()->back();
    // }

    /**
     * Get all Quote List
     */
    public function quoteRequests(Request $request)
    {
        $this->data['title'] = "Quote Requests";

        $quotes = QuoteRequest::latest()->get();
        $this->data['quotes'] = $quotes;
        return view('admin.Users.quote-list', $this->data);
    }

    public function notifyUser(Request $request)
    {
        if (env('NOTIFY_MAIL')) {
            // Send Notification
            if (!empty($request->message)) {
                $quoteId = $request->id;
                $quoteDetails = QuoteRequest::find($quoteId);
                $toEmail = $quoteDetails->email;
                $toMobile = $quoteDetails->mobile;
                $email = [
                    'subject' => "You got a Notification for Quote",
                    'greeting' => "Hi $quoteDetails->fullname,",
                    'body' => [
                        'Admin replied you on your Quote Request. <br> ' . $request->message
                    ],
                    'to' => $toEmail,
                    'more' => [],
                    'action_text' => 'Login to Application',
                    'action_url' => url('/user/login'),
                ];
                try {
                    Mail::to($toEmail)
                        ->send(new \App\Mail\SendQuoteMail($email));

                    session()->flash('message', ['success' => 'Notification send to user successfully']);
                } catch (\Exception $ex) {
                    session()->flash('message', ['danger' => 'Something went wrong! Exception occured.']);
                }
                
            }
        }
        return redirect()->back();
    }
}
