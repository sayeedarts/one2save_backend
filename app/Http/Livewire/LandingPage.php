<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\App;
use App\Models\Department;
use App\Models\Hospital;
use App\Models\Media;
use App\Models\NewsEvent;
use App\Models\Slider;
use App\Models\Language;
use Livewire\Component;

class LandingPage extends Component
{
    public $locale;
    public $sliders;
    public $hospitals;
    public $events;
    public $doctors_list;
    public $hospitals_list;
    public $departments_list;
    public $insurance_partners;

    public function mount()
    {
        $this->locale = app()->getLocale();
        $this->sliders = Slider::orderBy('sequence', 'asc')->get();
        $this->hospitals = Hospital::with('cover')->latest()->take(3)->get();
        $this->events = NewsEvent::latest()->take(3)->get();

        $this->hospitals_list = Hospital::latest()->pluck('name_' . $this->locale, 'id');
        $this->departments_list = Department::latest()->pluck('name_' . $this->locale, 'id');
        $this->insurance_partners = Media::whereType('insurance_partner')->with('files')->first();
    }

    public function render()
    {
        return view('livewire.landing-page')->layout('layouts.site');
    }
}
