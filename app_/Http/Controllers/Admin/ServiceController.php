<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Auth;

class ServiceController extends Controller
{
    /**
     * Some Variables used in this Class
     */
    private $data, $rules, $yesNo;

    /**
     * Constructer
     */
    public function __construct()
    {
        $this->data['required'] = '<span class="text-danger">*</span>';
        $this->rules = [
            'title' => 'required',
            // 'image' => 'required',
            'status' => 'required',
            // 'featured ' => 'required',
        ];
        $this->data['yesNo'] = yesNo();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['pageTitle'] = "Service";
        $this->data['service'] = Service::latest()->get();
        // dd($this->data['service_category']->toArray());
        return view('admin.Service.index', $this->data);
    }

    /**
     * Sort the listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sort()
    {
        $this->data['pageTitle'] = "Service";
        $this->data['services'] = Service::orderBy('sorting')->get();
        // dd($this->data['service_category']->toArray());
        return view('admin.Service.sort', $this->data);
    }

    public function sortSave(Request $request)
    {
        foreach ($request->id as $key => $id) {
            Service::where('id', $id)->update(['sorting' => $key + 1]);
        }

        session()->flash('message', ['success' => 'Successfully Sorted all Services']);
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $this->data['pageTitle'] = "Service";
        $this->data['mode'] = "store";
        if (!empty($id)) {
            $this->data['mode'] = "update";
            $this->data += Service::whereId($id)->first()->toArray();
        }
        // dd($this->data);
        return view('admin.Service.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $saveData = [
                'title' => $request->title,
                'featured' => $request->featured,
                'status' => $request->status,
                'display_type' => $request->display_type,
                'user_id' => Auth::user()->id,
                'content' => $request->content
            ];
            // upload image and push to data
            if (!empty($request->image)) {
                $saveData['icon'] = uploadImage($request->image, 'service');
            }
            if (!empty($request->picture)) {
                $saveData['image'] = uploadImage($request->picture, 'service_image');
            }
            if (!empty($request->id)) {
                // Update Old Record
                // $saveData['slug'] = $this->slug($request->title, $request->id);
                $update = Service::find($request->id);
                $update->update($saveData);
                $update->fresh();
                $update->slug = null;
                $update->update(['title' => $saveData['title']]);

                $message = "updated";
            } else {
                // Add new Record
                // $saveData['slug'] = $this->slug($request->title);
                Service::create($saveData);
                $message = "saved";
            }

            session()->flash('message', ['success' => 'Details ' . $message . ' successfully']);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went wrong!']);
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slug($title, $id = null)
    {
        $uuid = !empty($id) ? $id : rand(1111,9999);
        $slug = \Str::slug($title);
        $checkSlug = Service::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        if ($checkSlug) {
            return $slug . "-" . $uuid;
        }
        return $slug;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!empty($id)) {
            if (Service::find($id)->delete()) {
                session()->flash('message', ['success' => 'Record deleted successfully']);
                return redirect()->back();
            }
            session()->flash('message', ['danger' => 'Something went wrong!']);
            return redirect()->back();
        }
    }
}
