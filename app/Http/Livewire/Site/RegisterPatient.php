<?php

namespace App\Http\Livewire\Site;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Country;
use App\Models\Religion;
use App\Models\User;
use Illuminate\Support\Str;

class RegisterPatient extends Component
{
    /**
     * Initializing Form parameters
     *
     * @author tanmayapatra09
     * @date 29 Oct 2020
     */
    public $form = [
        'firstname' => '',
        'secondname' => '',
        'thirdname' => '',
        'lastname' => '',
        'nationality' => '',
        'dob' => '',
        'national_id' => '',
        'religion' => '',
        'email' => '',
        'phone' => '',
        'gender' => ''
    ];

    public $title = "Register as Patient";
  
    public $nationalities, $genders, $religions;
    public $required = '<span class="text-danger">*</span>';
    protected $rules = [
        'form.firstname' => 'required|min:3',
        'form.lastname' => 'required|min:3',
        'form.email' => 'required|email|unique:users,email',
        'form.phone' => 'required|digits_between:10,15',
        'form.gender' => 'required',
        'form.national_id' => 'required',
        'form.nationality' => 'required',
        'form.religion' => 'required',
        'form.dob' => 'required',
    ];

    /**
     * Register a Patient to System
     *
     * @author tanmayapatra09
     * @date 20 Oct 2020
     */

    public function addPatient()
    {
        $data = $this->validate();
        $password = 'password';//Str::random(10);
        $fullname = Str::of($this->form['firstname'])->append('' . $this->form['secondname'])->append(' ' . $this->form['thirdname'])->append(' ' . $this->form['lastname']);
        $user = User::create([
            'name' => $fullname,
            'role' => 'user',
            'email' => $this->form['email'], 
            'password' => $password, 
            'role_id' => 2
        ]);
        $this->form['user_id'] = $user->id;
        $this->form['created_by'] = $user->id;
        $patient = Patient::create($this->form);
        // generate a temp MRN number and update to Database
        $tempMRN = 'PAT' . Str::padLeft($patient->id, 10, '0');
        $patient->mrn = $tempMRN;
        $patient->temp_mrn = 1;
        $patient->save();
        
        session()->flash('message', ['success' => 'Registration is success. Please check you email']);
        return redirect()->to('/register-patient');
    }

    public function mount()
    {
        // get nationality details 
        $this->nationalities = Country::pluck('nationality', 'id');
        // load genders
        $this->genders = [
            'male',
            'female',
            'others'
        ];
        $this->religions = Religion::get();
    }

    /**
     * Called after updating any element
     */
    public function updated($prop)
    {
        // $this->validateOnly($prop);
    }

    /**
     * Rendering Element to view
     * @author tanmayapatra09
     * @date 29 Oct 2020
     */
    public function render()
    {
        return view('livewire.site.register-patient')
            ->layout('layouts.site');
    }
}
