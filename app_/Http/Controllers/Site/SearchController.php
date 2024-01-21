<?php

namespace App\Http\Controllers\Site;

use App\Models\Doctor;
use App\Models\Department;
use App\Models\NewsEvent;
use App\Models\Hospital;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class SearchController extends Controller
{
    public $data;
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct();
    }

    public function search()
    {
        $this->data['title'] = __('search_keywords');
        $searchResults = [];
        $keyword = $this->request->q;
        // Check Doctor's Model 
        $checkDoctor = Doctor::where('name_en', "LIKE", '%' . $keyword . '%')
            ->get();
        foreach ($checkDoctor as $key => $doctor) {
            $searchResults[] = [
                'category' => 'Doctors',
                'title' => $doctor->name_en,
                'url' => route('doctor.details', $doctor->slug),
                'details' => $doctor->details_en
            ];
        }

        // Check Department Model
        $checkDepartment = Department::where('name_en', "LIKE", '%' . $keyword . '%')
            ->get();
        foreach ($checkDepartment as $key => $department) {
            $searchResults[] = [
                'category' => 'Departments',
                'title' => $department->name_en,
                'url' => route('department.details', $department->slug),
                'details' => $department->details_en
            ];
        }

        // Check News And Event Model
        $checknewsEvents = NewsEvent::where('title_en', "LIKE", '%' . $keyword . '%')
            ->get();
        foreach ($checknewsEvents as $key => $event) {
            $searchResults[] = [
                'category' => 'News & Events',
                'title' => $event->title_en,
                'url' => route('events.details', $event->id),
                'details' => $event->details_en
            ];
        }

        // Check Hospital Model
        $checkHospitals = Hospital::where('name_en', "LIKE", '%' . $keyword . '%')
        ->get();
        foreach ($checkHospitals as $key => $hospital) {
            $searchResults[] = [
                'category' => 'Hospital',
                'title' => $hospital->name_en,
                'url' => route('hospital.show', $hospital->slug),
                'details' => $hospital->details_en
            ];
        }
        
        $this->data['keyword'] = $keyword;
        $this->data['results'] = $searchResults;
        return view('site.listings.search', $this->data);
    }
}
