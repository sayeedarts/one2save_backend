<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storage;
use App\Models\StorageImage;
use App\Models\StorageType;
use Illuminate\Http\Request;

class StorageController extends Controller
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
        $this->data['pageTitle'] = "Storage";
        $this->data['storages'] = Storage::latest()->get();
        // dd($this->data['service_category']->toArray());
        return view('admin.Storage.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $this->data['pageTitle'] = "Storage";
        $this->data['mode'] = "store";
        $this->data['types'] = StorageType::pluck('name', 'id');
        if (!empty($id)) {
            $this->data['mode'] = "update";
            $this->data += Storage::whereId($id)->with('images')->first()->toArray();
        }
        // dd($this->data);
        return view('admin.Storage.create', $this->data);
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
            // dd($request->all());
            $maxSortingValue = Storage::max('sorting');
            $nextSortingValue = intval($maxSortingValue + 1);
            $saveData = [
                'storage_type_id' => $request->storage_type_id,
                'name' => $request->name,
                'price' => $request->price,
                'area' => $request->area,
                'dimension' => $request->dimension,
                'description' => $request->description,
                'user_id' => \Auth::user()->id,
            ];
            // upload image and push to data
            if (!empty($request->picture)) {
                $saveData['file'] = upload($request->picture, 'storage');
                // foreach ($request->picture as $key => $picture) {
                //     $uploadFile[] = upload($picture, 'storage');
                // }
            }
            if (!empty($request->id)) {
                // Update Old Record
                $storageId = $request->id;
                $update = Storage::find($request->id);
                $update->update($saveData);
                // if (!empty($uploadFile)) {
                //     // delete Images then add new
                //     $images = StorageImage::whereStorageId($request->id);
                //     if ($images->count() > 0) {
                //         $fileList = $images->get();
                //         foreach ($fileList as $key => $file) {
                //             // deleteFile($file->image, 'storage');
                //         }
                //         // $images->delete();
                //     }
                // }
                $message = "updated";
            } else {
                $saveData['sorting'] = $nextSortingValue;
                // Add new Record
                $saveStorage = Storage::create($saveData);
                $storageId = $saveStorage->id;
                $message = "saved";
            }
            /**
             * Image details store in the DB
             */
            // $storageImages = [];
            // if (!empty($uploadFile)) {
            //     foreach ($uploadFile as $key => $image) {
            //         $storageImages[] = [
            //             'storage_id' => $storageId,
            //             'image' => $image
            //         ];
            //     }
            //     $serviceCategoryItem = Storage::find($storageId);
            //     // $serviceCategoryItem->update(['file' => ]);
            // }
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
        //
    }

    public function deleteImages(Request $request)
    {
        $jsonResponse = ['status' => 0, 'message' => 'Something went wrong'];
        $storage = StorageImage::query();
        if (!empty($request->storage_id)) {
            $storage->where('storage_id', $request->storage_id);
            if (!empty($request->type == "single")) {
                $storage->where('id', $request->id);
            }
            if ($storage->delete()) {
                $jsonResponse = [
                    'status' => 1,
                    'message' => 'Data deleted successfully',
                ];
            }
        }
        return \json_encode($jsonResponse);
    }
}
