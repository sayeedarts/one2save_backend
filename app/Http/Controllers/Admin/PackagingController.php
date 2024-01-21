<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Packaging;
use App\Models\PackagingImage;
use Illuminate\Http\Request;

class PackagingController extends Controller
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
            'storage_type_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'area' => 'required',
            'dimension' => 'required',
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
        $this->data['pageTitle'] = "Packaging";
        $this->data['storages'] = Packaging::latest()->get();
        // dd($this->data['service_category']->toArray());
        return view('admin.Packaging.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $this->data['pageTitle'] = "Packaging Materials";
        $this->data['mode'] = "store";
        $this->data['categories'] = Category::where('type', 'packaging')->pluck('name', 'id');
        // $this->data['types'] = StorageType::pluck('name', 'id');
        if (!empty($id)) {
            $this->data['mode'] = "update";
            // $this->data += 
            $this->data += Packaging::whereId($id)->with('images')->first()->toArray();
            // dd($p);
        }
        // dd($this->data);
        return view('admin.Packaging.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = \Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            // 'dimension' => 'required',
            // 'short_description' => 'required',
        ]);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            // dd($request->all());
            $saveData = [
                'name' => $request->name,
                'price' => $request->price,
                'dimension' => $request->dimension,
                'category_id' => $request->category_id,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'user_id' => \Auth::user()->id,
                'seo_title' => $request->seo_title,
                'seo_keywords' => $request->seo_keywords,
                'seo_description' => $request->seo_description,
            ];
            // upload image and push to data
            $uploadFile = [];
            if (!empty($request->pictures)) {
                foreach ($request->pictures as $key => $picture) {
                    $uploadFile[] = uploadImage($picture, 'packaging');
                }
            }

            if (!empty($request->id)) {
                // Update Old Record
                $storageId = $request->id;
                $update = Packaging::find($request->id);
                $updateStatus = $update->update($saveData);

                $storageImages = [];
                if (!empty($uploadFile)) {
                    foreach ($uploadFile as $key => $image) {
                        $storageImages[] = [
                            'packaging_id' => $storageId,
                            'image' => $image
                        ];
                    }
                }

                // dd($storageImages);
                if ($updateStatus) {
                    PackagingImage::insert($storageImages);
                }
                // dd($saveData);
                $message = "updated";
            } else {
                // Add new Record
                $saveStorage = Packaging::create($saveData);
                $storageId = $saveStorage->id;

                $storageImages = [];
                if (!empty($uploadFile)) {
                    foreach ($uploadFile as $key => $image) {
                        $storageImages[] = [
                            'packaging_id' => $storageId,
                            'image' => $image
                        ];
                    }
                }

                // dd($storageImages);
                PackagingImage::insert($storageImages);
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
        $images = PackagingImage::where('packaging_id', $id)->get();
        foreach ($images as $key => $image) {
            deleteFile($image->image, 'packaging');
        }
        if (Packaging::find($id)->images()->delete()) {
            Packaging::find($id)->delete();
            session()->flash('message', ['success' => 'Record was deleted successfully']);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went wrong']);
        return redirect()->back();
    }
}
