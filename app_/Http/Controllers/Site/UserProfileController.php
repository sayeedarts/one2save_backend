<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models as ApMo;
use App\Models\Appointment;
use App\Models\Gender;
use App\Models\Nationality;
use App\Models\Patient;
use App\Models\PatientTest;
use App\Models\Religion;
use App\Models\Report;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

// use App\Http\Controllers\Sync\PatientSync;

class UserProfileController extends Controller
{
    /**
     * Data array for sending information to view
     */
    public $data = [];

    /**
     * Define validation rules for the update form
     */
    protected $rules = [
        'firstname' => 'required|min:3',
        'lastname' => 'required|min:3',
        // 'email' => 'required|email|unique:users,email',
        'phone' => 'required|digits_between:10,15',
        'gender' => 'required',
        'national_id' => 'required',
        'nationality' => 'required',
        'religion' => 'required',
        'dob' => 'required',
    ];

    /**
     * User's Dashboard
     */
    public function myAccount()
    {
        $this->data['page_title'] = __("general.my_account");
        $mrnDetails = Patient::where('user_id', Auth::user()->id)
            ->with('mrns', 'mrns.hospital')
            ->first();
        // dd($this->data);
        $this->data['sync_progress'] = $this->patientSyncStatus('min');
        $this->data['mrn_details'] = !empty($mrnDetails) ? $mrnDetails : [];
        return view('site.user.my-account', $this->data);
    }

    /**
     * Reports section for patients
     */
    public function myReports(Request $request)
    {
        $this->data['page_title'] = __("my_reports");
        $userId = Auth::user()->id;
        $getPatientId = Patient::whereUserId(Auth::user()->id)->first();
        $patientId = $getPatientId->id;
        // Setup Filter default params
        $this->data['filters'] = [];
        if ((empty($request->from) && empty($request->to))) {
            $this->data['filters'] = [
                'from' => date("Y-m-d", strtotime("-12 month")),
                'to' => date("Y-m-d", strtotime("-1 day")),
            ];
            $request->request->add([
                'from' => $this->data['filters']['from'],
                'to' => $this->data['filters']['to'],
            ]);
        }
        $reports = Report::query();
        if (!empty($request->hospital)) {
            $reports->where('hospital_id', $request->hospital);
            $this->data['filters'] += ['hospital' => $request->hospital];
        }
        if ((!empty($request->from) && !empty($request->to))) {
            $reports->whereDate('visit_date', '>=', $request->from);
            $reports->whereDate('visit_date', '<=', $request->to);
            $this->data['filters'] += [
                'from' => $request->from,
                'to' => $request->to,
            ];
        }
        $reports->wherePatientId($patientId)
            ->with(
                'doctor',
                'lab_tests',
                'radio_tests',
                'prescriptions',
                'sick_leave',
                'hospital'
            );
        $getReports = $reports->get();
        $this->data['reports'] = !empty($getReports) ? $getReports : [];
        // Working with Filters
        $this->data['hospitals'] = \App\Models\Hospital::pluck('name_en', 'id');
        return view('site.user.my-reports', $this->data);
    }

    /**
     * Report details by Report ID
     */
    public function myLabReportsDetails($id)
    {
        $this->data['page_title'] = __("My Reports Details");
        $userId = Auth::user()->id;
        // $labDetails = PatientTest::whereReportId($id)->get();
        $this->data['lab_requests'] = $this->separateLabByRequest($id);
        $this->data['report_details'] = Report::whereId($id)->select('id', 'visit_date')->first();

        return view('site.user.my-lab-report-details', $this->data);
    }

    public function separateLabByRequest($id)
    {
        $patientTestDetails = PatientTest::whereReportId($id)->get();
        $requestList = [];
        foreach ($patientTestDetails as $key => $test) {
            $requestList[] = $test->request_id;
        }
        return array_unique($requestList);
    }

    /**
     * Download Patient's Labortatory Report by Reuqest ID
     */
    public function downloadLabReports($requestId, $reportId, $mode)
    {
        $patientTestDetails = PatientTest::whereRequestId($requestId)
            ->with('lab_test_details')
            ->get();
        $reportDetails = Report::whereId($reportId)->with('patient_details', 'hospital')
            ->first();
        $this->data['reportInfo'] = [
            "mrn" => $reportDetails->mrn,
            "visit_id" => $reportDetails->visit_id,
            "visit_date" => $reportDetails->visit_date,
        ];
        $this->data['labTestDetails'] = $patientTestDetails;
        $this->data['hospital'] = $reportDetails->hospital;
        $this->data['patientDetails'] = $reportDetails->patient_details;
        $reportName = "LAB_" . $reportDetails->mrn
        . "_" . date('Ymd', strtotime($reportDetails->created_at))
        . "_" . rand(11, 9999) . ".pdf";

        $pdf = \PDF::loadView('site.reports.lab', $this->data);
        if (in_array($mode, ['stream', 'download'])) {
            return $pdf->{$mode}($reportName);
        }
        // return view('site.reports.lab', $this->data);
    }

    public function myMedReportsDetails($reportId)
    {
        $this->data['page_title'] = __("My Reports Details");
        $userId = Auth::user()->id;
        // $labDetails = PatientTest::whereReportId($id)->get();
        $this->data['med_plan_nos'] = $this->separateMedByRequest($reportId);
        $this->data['report_details'] = Report::whereId($reportId)
            ->select('id', 'visit_date')
            ->first();

        return view('site.user.my-med-report-details', $this->data);
    }

    public function separateMedByRequest($id)
    {
        $patientMedDetails = ApMo\ReportMedicine::whereReportId($id)->get();
        $medPlanNumbers = [];
        foreach ($patientMedDetails as $key => $test) {
            $medPlanNumbers[] = $test->medplan_no;
        }
        return array_unique($medPlanNumbers);
    }

    /**
     * Download Patient's Labortatory Report by Reuqest ID
     */
    public function downloadMedicineReports($medplanNo, $reportId, $mode)
    {
        $patientMedDetails = ApMo\ReportMedicine::whereMedplanNo($medplanNo)
            ->get();
        // dd($patientMedDetails[0]);
        $reportDetails = Report::whereId($reportId)->with('patient_details', 'hospital')
            ->first();
        $this->data['reportInfo'] = [
            "medicine_plan_no" => $patientMedDetails[0]['medplan_no'],
            "medicine_plan_date" => dbtoDate($patientMedDetails[0]['medplan_date']),
            "mrn" => $reportDetails->mrn,
            "visit_id" => $reportDetails->visit_id,
            "visit_date" => $reportDetails->visit_date,
        ];
        $this->data['medicineDetails'] = $patientMedDetails;
        $this->data['hospital'] = $reportDetails->hospital;
        $this->data['patientDetails'] = $reportDetails->patient_details;
        // dd($this->data);
        $reportName = "MED_" . $reportDetails->mrn
        . "_" . date('Ymd', strtotime($reportDetails->created_at)) . "_" . rand(11, 9999) . ".pdf";

        $pdf = \PDF::loadView('site.reports.medicine', $this->data);
        if (in_array($mode, ['stream', 'download'])) {
            return $pdf->{$mode}($reportName);
        }
        // return view('site.reports.lab', $this->data);
    }

    public function downloadRadiology($id, $mode)
    {
        $patientTestDetails = Report::whereId($id)
            ->with(
                'patient_details',
                'patient_details.nationality_info',
                'patient_details.gender_info',
                'radio_tests',
                // 'lab_tests.lab_test_details',
                'hospital',
            )
            ->first();
        $this->data['reportInfo'] = [
            "mrn" => $patientTestDetails->mrn,
            "visit_id" => $patientTestDetails->visit_id,
            "visit_date" => $patientTestDetails->visit_date,
        ];
        $this->data['radioTestDetails'] = $patientTestDetails->radio_tests;
        $this->data['hospital'] = $patientTestDetails->hospital;
        $this->data['patientDetails'] = $patientTestDetails->patient_details;

        $reportName = "RADIO_" . $patientTestDetails->mrn
        . "_" . date('Ymd', strtotime($patientTestDetails->created_at)) . "_" . rand(11, 9999) . ".pdf";

        $pdf = \PDF::loadView('site.reports.radio', $this->data);
        if (in_array($mode, ['stream', 'download'])) {
            return $pdf->{$mode}($reportName);
        }
        // return $pdf->download($reportName);
        // return view('site.reports.lab', $this->data);
    }

    public function downloadSickLeaveReports($reportId, $mode)
    {
        $getSickLeaveDetails = ApMo\ReportSickLeave::whereReportId($reportId)->first();
        // dd($getSickLeaveDetails->toArray());
        // dd($patientMedDetails[0]);
        $reportDetails = Report::whereId($reportId)->with('patient_details', 'hospital')
            ->first();
        $this->data['reportInfo'] = [
            // "medicine_plan_no" => $patientMedDetails[0]['medplan_no'],
            // "medicine_plan_date" => dbtoDate($patientMedDetails[0]['medplan_date']),
            "mrn" => $reportDetails->mrn,
            "visit_id" => $reportDetails->visit_id,
            "visit_date" => $reportDetails->visit_date,
        ];
        $this->data['sickLeaveDetails'] = $getSickLeaveDetails;
        $this->data['hospital'] = $reportDetails->hospital;
        $this->data['patientDetails'] = $reportDetails->patient_details;
        // dd($this->data);
        $reportName = "MED_" . $reportDetails->mrn
        . "_" . date('Ymd', strtotime($reportDetails->created_at)) . "_" . rand(11, 9999) . ".pdf";

        $pdf = \PDF::loadView('site.reports.sick-leave', $this->data);
        if (in_array($mode, ['stream', 'download'])) {
            return $pdf->{$mode}($reportName);
        }
        // return view('site.reports.sick-leave', $this->data);
    }

    public function showSickLeaveRequest(Request $request)
    {
        $this->data['page_title'] = __('sick_leave_request');
        $userId = Auth::user()->id;
        $this->data['doctors'] = ApMo\Doctor::pluck('name_' . app()->getLocale(), 'id');
        $visits = ApMo\PatientVisit::wherePatientId(getPatientId());
        $visits->with(
            'doctor',
            'doctor.hospital',
            'doctor.department',
            'patient_id',
        );
        $visitDetails = $visits->get();
        // dd($visitDetails->toArray());
        $this->data['visit_details'] = !empty($visitDetails) ? $visitDetails : [];
        return view('site.user.my-sick-leaves', $this->data);
    }

    public function saveSickLeaveRequest(Request $request)
    {
        if (!empty($request->visit_id)) {
            $visitDetails = ApMo\PatientVisit::whereId($request->visit_id)
                ->select('ref_id', 'patient_id')
                ->first();
            $visitData = [
                'patient_id' => $visitDetails->patient_id,
                'visit_id' => $visitDetails->ref_id,
            ];
            ApMo\PatientVisit::whereId($request->visit_id)->update(['leave_requested' => 1]);
            \App\Jobs\OnSubmitJob::dispatch(['data' => $visitData, 'type' => 'sick_leave'])->delay(now());
            return redirect()->back();
        }
    }

    /**
     * Show Profile page and Fillup necessary data
     *
     * @date 07 nov 2020
     * @author tanmayap
     * @return view
     */
    public function profile()
    {
        $this->data = Patient::where('user_id', Auth::user()->id)->with('user')->first()->toArray();
        $this->data += Auth::user()->toArray();
        $this->data['nationalities'] = Nationality::pluck('name_' . app()->getLocale(), 'id');
        $this->data['religions'] = Religion::pluck('name', 'id');
        $this->data['genders'] = Gender::pluck('name', 'code');
        $this->data['tab'] = '';
        $this->data['page_title'] = "Update Profile";
        return view('site.user.profile', $this->data);
    }

    /**
     * Save/Update Profile informations
     *
     * @date 07 nov 2020
     * @author tanmayap
     * @return redirection
     */
    public function profileUpdate(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $update = $request->except('_token', 'email');
            $patientUpdate = Patient::where('user_id', Auth::user()->id);
            $patientUpdate->update($update);
            // Update info in users table too
            $getPatient = $patientUpdate->first();
            if (!empty($getPatient->full_name)) {
                $user = User::find(Auth::user()->id);
                $user->name = $getPatient->full_name;
                $user->save();
            }

            // Update to Int. server
            \App\Jobs\OnSubmitJob::dispatch(['user_id' => Auth::user()->id, 'type' => 'patient_update'])->delay(now());
            session()->flash('message', ['success' => 'Profile information updated successfully']);
            return redirect()->back();
        }
    }

    public function changePassword()
    {
        $this->data['page_title'] = __('change_password');
        return view('site.user.change-password', $this->data);
    }

    public function changePasswordPost(Request $request)
    {
        $oldPassword = $request->current;
        if (\Hash::check($oldPassword, Auth::user()->password)) {
            $user = User::find(Auth::user()->id);
            $user->password = $request->password;
            $user->save();
            session()->flash('message', ['success' => 'Profile information updated successfully']);
        } else {
            session()->flash('message', ['danger' => 'Something went wrong. Please try again']);
        }
        return redirect()->back();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        return redirect(route('landing'));
    }

    public function appointments()
    {
        $this->data['page_title'] = __('my_appointments');
        $userId = Auth::user()->id;
        $this->data['appointments'] = Appointment::where('user_id', $userId)
            ->with('hospital', 'department', 'doctor')
            ->orderBy('date', 'DESC')
        // ->latest()
            ->get();
        return view('site.user.appointments', $this->data);
    }

    public function patientSyncStatus($mode = "")
    {
        // header('Content-Type: application/json');
        $jsonResponse = ["status" => 0, "message" => "Something went wrong."];
        $batchId = session()->get('batch_id', null);
        if (!empty($batchId)) {
            $batch = \Illuminate\Support\Facades\Bus::findBatch($batchId);
            if (!empty($batch)) {
                $jsonResponse = [
                    "status" => intval($batch->finished()),
                    "id" => $batch->id,
                    "total" => $batch->totalJobs,
                    "processed" => $batch->processedJobs(),
                    "failed" => $batch->failedJobs,
                    "progress" => $batch->progress(),
                    "message" => "Your Account is now fully SYNCed.",
                ];
                if (!empty($mode) && $mode == "min") {
                    return intval($batch->progress());
                }
            }
        }
        echo \json_encode($jsonResponse);
    }
}
