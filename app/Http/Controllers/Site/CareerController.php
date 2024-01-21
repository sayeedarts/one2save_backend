<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\CareerRequest;
use App\Models\Country;
use App\Models\User;
use App\Models\Department;
use App\Models\Hospital;
use App\Models\HospitalDepartment;
use App\Models\Nationality;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class CareerController extends Controller
{
    public $data;
    public $required = '<span class="text-danger">*</span>';
    protected $rules = [
        'firstname' => 'required|min:3',
        'lastname' => 'required|min:3',
        'email' => 'required|email',
        'phone' => 'required|digits_between:10,15',
        'gender' => 'required',
        'nationality' => 'required',
    ];

    public function vacancies(Request $request)
    {
        $this->data = $request->all();
        $this->data['title'] = __('job_vacancies');
        $this->data['hospitals'] = Hospital::latest()->pluck('name_' . app()->getLocale(), 'id');
        $this->data['departments'] = Department::latest()->pluck('name_' . app()->getLocale(), 'id');
        // Process Filter Params
        $career = Career::whereDate('publish_on', '<', date('Y-m-d'));
        if (!empty($request->search)) {
            $career->where('title', 'LIKE', '%' . $request->search . '%');
        }
        if (!empty($request->department)) {
            $hospitals = HospitalDepartment::where('department_id', $request->department)->pluck('hospital_id')->toArray();
            $career->whereIn('hospital_id', $hospitals);
        }
        if (!empty($request->hospital)) {
            $career->where('hospital_id', $request->hospital);
        }
        $this->data['careers'] = $career->with('hospital')->latest()->get();
        return view('site.career.list', $this->data);
    }

    public function vacancyDetails($id)
    {
        $this->data['title'] = __('job_vacancies');
        $this->data['career'] = Career::where('id', $id)->with('hospital')->first();
        return view('site.career.details', $this->data);
    }

    public function applyJob($id = null)
    {
        $this->data['title'] = __('apply_online');
        $this->data['required'] = $this->required;
        if ($id) {
            $this->data['career_id'] = $id;
            $this->data['career'] = Career::find($id)->toArray();
        }
        $this->data['nationality'] = Nationality::pluck('name_' . app()->getLocale(), 'id');
        return view('site.career.apply', $this->data);
    }

    public function applyJobPost(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $resumePath = upload($request->textfile, 'resumes');
            $request->request->add([
                'file' => $resumePath,
                'user_id' => Auth::user()->id,
                'status' => 'pending',
            ]);
            CareerRequest::create($request->all());

            if (env('NOTIFY_MAIL')) {
                // Send Notification
                $email = [
                    'subject' => __('received_your_application'),
                    'greeting' => 'Hi ' . (string) $request->firstname,
                    'body' => [
                        __('received_your_application_will_reply_soon') . " Please login into our application to review your application"
                    ],
                    'action_text' => 'Login to Application',
                    'action_url' => url('/user/login'),
                ];
                $user = User::find(Auth::user()->id);
                Notification::send($user, new \App\Notifications\UserNotify($email));
            }

            session()->flash('message', ['success' => __('received_your_application')]);
            return redirect()->back();
        }
    }

}
