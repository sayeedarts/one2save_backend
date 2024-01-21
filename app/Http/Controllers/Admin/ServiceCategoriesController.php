<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Auth;

use Illuminate\Http\Request;

class ServiceCategoriesController extends Controller
{

    private $data, $rules;

    public function __construct()
    {
        $this->data['required'] = '<span class="text-danger">*</span>';
        $this->rules = [
            'title' => 'required',
            // 'image' => 'required',
            'service_id' => 'required',
            // 'status ' => 'required',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['pageTitle'] = "Service Category List";
        $this->data['service_category'] = ServiceCategory::withCount('category_item')->with('service')->latest()->get();
        // dd($this->data['service_category']->toArray());
        return view('admin.ServiceCategories.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $this->data['pageTitle'] = "Service Category";
        $this->data['mode'] = "store";
        $this->data['services'] = Service::pluck('title', 'id');
        if (!empty($id)) {
            $this->data['mode'] = "update";
            $this->data += ServiceCategory::whereId($id)->with('category_item')->first()->toArray();
        }
        // dd($this->data);
        return view('admin.ServiceCategories.create', $this->data);
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
                'content' => $request->content,
                'service_id' => $request->service_id,
                'status' => $request->status,
                'display_type' => $request->display_type,
                'user_id' => Auth::user()->id,
                'seo_title' => $request->seo_title,
                'seo_keywords' => $request->seo_keywords,
                'seo_description' => $request->seo_description,
            ];
            $listOfItems = [];
            foreach ($request->item as $key => $item) {
                if (!empty($request->item[$key])) {
                    $listOfItems[] = [
                        'title' => $request->item[$key],
                        'status' => $request->item_featured[$key],
                    ];
                }
            }
            // upload image and push to data
            if (!empty($request->image)) {
                $saveData['icon'] = uploadImage($request->image, 'service_category');
            }
            if (!empty($request->id)) {
                // Update Old Record
                $update = ServiceCategory::find($request->id);
                $update->update($saveData);
                $update->category_item()->delete();
                $update->category_item()->createMany($listOfItems);
                $update->fresh();
                $update->slug = null;
                $update->update(['title' => $saveData['title']]);
                $message = "updated";
            } else {
                // Add new Record
                $serviceCategory = ServiceCategory::create($saveData);
                $serviceCategoryId = $serviceCategory->id;
                $serviceCategoryItem = ServiceCategory::find($serviceCategoryId);
                $serviceCategoryItem->category_item()->createMany($listOfItems);
                $message = "saved";
            }

            session()->flash('message', ['success' => 'Details ' . $message . ' successfully']);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went wrong!']);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
            if (ServiceCategory::find($id)->delete()) {
                session()->flash('message', ['success' => 'Record deleted successfully']);
                return redirect()->back();
            }
            session()->flash('message', ['danger' => 'Something went wrong!']);
            return redirect()->back();
        }
    }
}
