<?php

namespace App\Http\Livewire\Site;

use App\Models\Hospital;
use Livewire\Component;
use App\Models\HospitalDepartment;
use App\Models\Department;
use App\Models\Doctor;

class BookAppointment extends Component
{
    public $form = [
        
    ];
    public $hospital_id = "";
    public $department_id = "";
    public $hospitals = [];
    public $departments = [];
    public $doctors = [];
    protected $listeners = [
        'hospital_id' => 'getDepartments',
        'department_id' => 'getDoctors',
    ];

    public function getDepartments($playload)
    {
        // dd($playload['id']);
        $this->hospital_id = $playload['id'];
        $this->departments = Department::latest()->pluck('name', 'id');
    }

    public function getDoctors($playload)
    {
        // dd($playload);
        // dd($this->hospital_id);
        $this->department_id = $playload['id'];
        $this->doctors = Doctor::latest()->pluck('name', 'id');
    }

    // public function updatedHospitalId($hosp)
    // {
    //     $this->departments = Department::latest()->pluck('name', 'id');
    // }

    // public function hydrateDepartmentId($dept)
    // {
    //     // $this->departments = Department::latest()->pluck('name', 'id');
    //     // dd($dept);
    // }
    
    public function render()
    {
        $this->hospitals = Hospital::latest()->pluck('name', 'id');
        return view('livewire.site.book-appointment')->layout('layouts.site');
    }
}
