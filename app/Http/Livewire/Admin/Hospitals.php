<?php

namespace App\Http\Livewire\Admin;

use App\Models\Hospital;
use App\Models\Department;
use App\Models\HospitalDepartment;
use Auth;
use Illuminate\Support\Facades\Route;
use Interven;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Hospitals extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $foo;
    public $routeName = "";
    public $required = '<span class="text-danger">*</span>';
    public $gallery;
    public $photo;
    public $dimension = [300 => 100, 750 => 400];
    public $hospitals = [];
    public $departments;
    public $selectedDepartments;
    public $form = [
        'id' => '',
        'name' => '',
        'phone' => '',
        'email' => 'somename@gmail.com',
        'address' => '',
        'details' => '',
        'department' => '',
        'facebook' => '',
        'instagram' => '',
        'twitter' => '',
        'photo' => '',
        'user_id' => '',
    ];

    protected $rules = [
        'form.name' => 'required|min:3',
        'form.email' => 'required|min:3',
        // 'form.image' => 'image|max:2048',
    ];

    public function saveHospital()
    {
        $data = $this->validate();
        $this->form['photo'] = $this->upload('hospitals', $this->photo, [300 => 100]);
        $this->form['user_id'] = Auth::user()->id;
        $hospital = Hospital::create($this->form);
        // dd($hospital->slug);
        // Process Gallery Images
        $galleryImages = [];
        if (!empty($this->gallery)) {
            foreach ($this->gallery as $galleryPhoto) {
                $galleryImages[] = [
                    'hospital_id' => $hospital->id,
                    'photo' => $this->upload('hospitals', $galleryPhoto, [952 => 484]),
                ];
            }
            $hospital->galleries()->createMany($galleryImages);
        }
        $departments = [];
        if (!empty($this->form['department'])) {
            foreach ($this->form['department'] as $department) {
                $departments[] = [
                    'hospital_id' => $hospital->id,
                    'department_id' => $department,
                ];
            }
            $hospital->departments()->createMany($departments);
        }
        $this->form = [];
        session()->flash('message', ['success' => 'Hospital is saved']);
        return redirect()->back();
    }

    public function updateHospital()
    {
        if (!empty($this->photo)) {
            $this->form['photo'] = $this->upload('hospitals', $this->photo, [300 => 100]);
        }
        $updateData = \Arr::except($this->form, ['created_at', 'updated_at', 'galleries', 'department']);
        // $updateData['slug'] = null;
        $update = Hospital::where('id', $this->form['id'])->update($updateData);
        
        $hospital = Hospital::find($this->form['id']);
        $departments = [];
        if (!empty($this->form['department'])) {
            foreach ($this->form['department'] as $department) {
                $departments[] = [
                    'hospital_id' => $hospital->id,
                    'department_id' => $department,
                ];
            }
            \App\Models\HospitalDepartment::where('hospital_id', $this->form['id'])->delete();
            $hospital->departments()->createMany($departments);
        }
        
        session()->flash('message', ['success' => 'Hospital is updated']);
        return redirect()->back();
    }

    public function delete($id)
    {
        $hospital = Hospital::find($id);
        $hospital->delete();
        session()->flash('message', ['warning' => 'Record is deleted successfully']);
        return redirect()->route('hospital.list');
    }

    protected function upload($location = "misc", $file, $dimension)
    {
        $fileName = "";
        if (!empty($dimension)) {
            $this->dimension = $dimension;
        }
        if ($file) {
            // calculate md5 hash of encoded image
            $fileName = md5($file->__toString()) . '.jpg';
            foreach ($this->dimension as $width => $height) {
                $convertImg = Interven::make($file)->resize($width, $height)->encode('jpg');
                // use hash as a name
                $path = "uploads/{$location}/{$width}x{$height}_{$fileName}";
                // save it locally to ~/public/images/{$hash}.jpg
                $convertImg->save(public_path($path));
            }
            return $fileName;
        }
        return null;
    }

    public function mount($id = null)
    {
        $this->routeName = Route::getFacadeRoot()->current()->action['as'];
        if (!empty($id)) {
            $this->form = Hospital::find($id)->toArray();
            $this->selectedDepartments = HospitalDepartment::where('hospital_id', $id)->pluck('department_id')->toArray();
            // dd($this->selectedDepartments);
        }
        if ($this->routeName == "hospital.list") {
            $this->hospitals = Hospital::latest()->get();
        }
        $this->departments = Department::pluck('name', 'id');
        // dd($this->departments);
    }

    public function render()
    {
        return view('livewire.admin.hospitals')
            ->layout('layouts.admin');
    }
}
