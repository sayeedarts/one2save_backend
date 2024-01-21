<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public $data;
    private $allowedTypes;
    protected $rules = [];

    public function __construct()
    {
        $this->data['required'] = '<span class="text-danger">*</span>';
        $this->data['types'] = page_types();
        $this->allowedTypes = ['how_it_works', 'storage_dvc_home_ad', 'testimonials', 'service_addl_help', 'blogs', 'page'];
        // Setup validations with langauge
        if (!empty($this->getLanguages())) {
            foreach($this->getLanguages() as $lang) {
                $this->rules += [
                    'name' => 'required|min:1',
                    'content' => 'required|min:3',
                ];
            }
        }
    }

    public function list()
    {
        $this->data['title'] = "Dynamic Page List";
        $this->data['pages'] = Page::latest()->get();
        $this->data['types'] = page_types();
        return view('admin.Page.index', $this->data);
    }

    public function create($id = null)
    {
        $this->data['title'] = "Add new Page";
        $this->data['mode'] = 'store';
        if (!empty($id)) {
            $this->data += Page::find($id)->toArray();
            $this->data['mode'] = 'update';
            // dd($this->data);
        }
        return view('admin.Page.show', $this->data);
    }

    public function store(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $postData = [
                'name' => $request->name,
                'content' => $request->content,
                'type' => $request->type
            ];
            
            // Upload the file and get the file name
            if (!empty($request->file) && (in_array($request->type, $this->allowedTypes))) {
                $postData['asset'] = uploadImage($request->file, $request->type);
            }
            try {
                Page::create($postData);
                $message = "Page details are saved successfully";
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
            }
            session()->flash('message', ['success' => $message]);
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $postData = [
                'name' => $request->name,
                'content' => $request->content,
                'type' => $request->type
            ];
            // Upload the file and get the file name
            if (!empty($request->file) && (in_array($request->type, $this->allowedTypes))) {
                $postData['asset'] = uploadImage($request->file, $request->type);
            }
            $hospital = Page::whereId($request->id)->update($postData);
            session()->flash('message', ['success' => 'Page details are updated successfully']);
            return redirect()->back();
        }
    }

    public function delete($id = null)
    {
        if (Page::whereId($id)->count() > 0) {
            Page::whereId($id)->delete();
            session()->flash('message', ['warning' => 'Page details are deleted successfully']);
            return redirect()->back();
        }
    }
}
