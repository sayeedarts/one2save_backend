<?php

namespace App\Http\Livewire\Site;

use Livewire\Component;
use App\Models\Hospital as HospitalModel;

class Hospital extends Component
{
    public $title;
    public $details;


    public function mount($slug)
    {
        $this->details = HospitalModel::where('slug', $slug)
            ->with(
                'galleries', 'doctors', 'doctors.department', 'departments', 'departments.department'
            )
            ->first();
        $this->title = $this->details->name ?? "";
    }

    public function render()
    {
        return view('livewire.site.hospital')
            ->layout('layouts.site');;
    }
}
