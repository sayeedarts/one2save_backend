<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Gallery;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    /**
     * Constructer
     */
    public function __construct()
    {
        $this->data['required'] = '<span class="text-danger">*</span>';
        $this->rules = [
            'upload' => 'required',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['pageTitle'] = "File Manager";
        $this->data['files'] = File::latest()->get();
        // dd($this->data['service_category']->toArray());
        return view('admin.FileManager.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = null)
    {
        $this->data['pageTitle'] = "File";
        $this->data['mode'] = "store";
        if (!empty($id)) {
            $this->data['mode'] = "update";
            $this->data += File::whereId($id)->first()->toArray();
        }
        // dd($this->data);
        return view('admin.FileManager.create', $this->data);
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
            $saveData = [];
            // upload image and push to data
            if (!empty($request->upload)) {
                $saveData['file'] = upload($request->upload, 'file_manager');
            }
            File::create($saveData);
            $message = "saved";

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
            $fileInfo = File::find($id);
            try {
                removeFile($fileInfo->file, 'file_manager');
                if ($fileInfo->delete()) {
                    session()->flash('message', ['success' => 'Record deleted successfully']);
                    return redirect()->back();
                }
            } catch (\Exception $ex) {
                //throw $th;
            }
            session()->flash('message', ['danger' => 'Something went wrong!']);
            return redirect()->back();
        }
    }
}
