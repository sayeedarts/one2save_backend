<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Media;
use App\Models\NewsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Admin\DoctorsController as DoctorAdmin;

class ListingsController extends Controller
{
    public $data;
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct();
    }
    /**
     * Get all department list
     *
     * @author tanmayap
     * @date 11 nov 2020
     */
    public function departments()
    {
        \DB::enableQueryLog();
        $this->data['title'] = __('departments');
        $this->data['hospitals'] = Hospital::pluck('name_' . app()->getLocale(), 'id');
        $department = Department::query();
        if ($this->request->search) {
            $search = $this->request->search;
            $department->where("name_en", "LIKE", "%" . $search . "%");
        }
        if ($this->request->hospital) {
            $hospitalId = $this->request->hospital;
            $department = $department->whereHas('hospital_department', function ($q) use ($hospitalId) {
                return $q->where('hospital_id', $hospitalId);
            });
        }
        $this->data['departments'] = $department->latest()->get();
        // dd(\DB::getQueryLog());
        return view('site.listings.departments', $this->data)->with($this->request->all());
    }

    /**
     * Get Department Details
     *
     * @author tanmayap
     * @date 11 nov 2020
     */
    public function department(Request $request, $slug)
    {
        $departments = Department::whereSlug($slug)->with('features');

        if (!empty($request->hospital)) {
            $hospitalId = Crypt::decrypt($request->hospital);
            $departments->with('doctors', function($query) use ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            });
        }
        $this->data['department'] = $departments->first();
            
            // ->whereHas('hospital_department' , function($query) {
            //     $query->where('hospital_id', 13);
            // })
            // ->first();
        $this->data['departments'] = Department::latest()->pluck('name_' . app()->getLocale(), 'slug');
        return view('site.listings.department-details', $this->data);
    }

    /**
     * Show list of doctors with some filters
     *
     * @params $request
     *
     * @author tanmayapatra
     * @date 29 Dec 2020
     * @return view
     */
    public function doctors(Request $request)
    {
        $this->data['title'] = __('doctor_list');
        $doctors = Doctor::query();
        if ($request->hospital) {
            $doctors->whereHospitalId($request->hospital);
        }
        if ($request->department) {
            $doctors->whereDepartmentId($request->department);
        }
        if ($request->search) {
            $doctors->where('name_en', 'LIKE', '%' . $request->search . '%');
        }
        $this->data['doctors'] = $doctors->latest()->with(
            'hospital', 'department', 'valid_avails'
        )->paginate(16);

        $this->data['doctors_list'] = Doctor::latest()->pluck('name_' . $this->activeLang(), 'id');
        $this->data['hospitals'] = Hospital::latest()->pluck('name_' . $this->activeLang(), 'id');
        $this->data['departments'] = Department::latest()->pluck('name_' . $this->activeLang(), 'id');
        return view('site.listings.doctors', $this->data)->with($request->all());
    }

    /**
     * Show single doctor's details
     *
     * @param $slug
     *
     * @author tanmayapatra
     * @date 29 dec 2020
     * @return view
     */
    public function doctor($slug)
    {
        $doctor = new DoctorAdmin;
        $this->data['duty_slots'] = $doctor->dutySlots;
        $this->data['days'] = days();
        $this->data['title'] = __('doctor');
        $doctorDetails = Doctor::whereSlug($slug)->with('department', 'availabilities')->first();
        $this->data['doctor'] = $doctorDetails;
        // Arrange Doctor's Shift Details for display in correct way
        $shiftDetails = [];
        $availabilities = $doctorDetails->availabilities;
        if ($availabilities->isNotEmpty()) {
            foreach ($availabilities as $key => $availability) {
                $shiftDetails[$availability->date][] = $availability->toArray();
            }
        }
        $this->data['availability_details'] = $shiftDetails;
        return view('site.listings.doctor', $this->data);
    }

    /**
     * Fetch ALl images list and also Single Image gallery details
     *
     * @author tanmayap
     * @date 12 nov 2020
     */
    public function imagesList($id = null)
    {
        $this->data['title'] = __('photo_gallery');
        $gallery = Media::whereType('image');
        $this->data['relationship'] = "cover";
        if ($id) {
            $this->data['relationship'] = "files";
            $galleryId = Crypt::decrypt($id);
            $gallery->whereId($galleryId);
        }
        $this->data['media'] = $gallery->with($this->data['relationship'])->get();
        return view('site.listings.image-gallery', $this->data);
    }

    public function videosList()
    {
        $this->data['title'] = __('video_gallery');
        $gallery = Media::latest();
        $this->data['media'] = $gallery->get();
        return view('site.listings.video-gallery', $this->data);
    }

    public function newsEvents()
    {
        $this->data['title'] = __('news_events');
        $this->data['newsEvents'] = NewsEvent::latest()->get();
        return view('site.listings.news-events', $this->data);
    }

    public function newsEventDetails($id)
    {
        $this->data['newsevent'] = NewsEvent::whereId($id)->first();
        $this->data['title'] = $this->data['newsevent']->{'title_' . app()->getLocale()};
        return view('site.listings.news-event-details', $this->data);
    }

    public function page($slug)
    {
        $pageDetails = \App\Models\Page::whereSlug($slug)->first();
        $this->data['title'] = $pageDetails->{'name_' . $this->activeLang()};
        $this->data['page'] = $pageDetails;
        $this->data['active_lang'] = $this->activeLang();
        return view('site.listings.page', $this->data);
    }

    public function hospital($slug = null)
    {
        $this->data['details'] = Hospital::where('slug', $slug)->with(
            'galleries',
            'doctors',
            'doctors.valid_avails',
            'doctors.department',
            'departments',
            'departments.department'
        )->first();
        $this->data['title'] = $this->data['details']->{'name_' . $this->activeLang()} ?? "";
        return view('site.listings.hospital', $this->data);
    }

    public function bmiCheck()
    {
        $this->data['title'] = __('bmi_check');
        return view('site.listings.bmi-check', $this->data);
    }

}
