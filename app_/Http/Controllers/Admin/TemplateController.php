<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Constructer
     */
    public function __construct()
    {
        $this->data['required'] = '<span class="text-danger">*</span>';
        $this->rules = [
            'title' => 'required',
            'content' => 'required'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $this->data['pageTitle'] = "Template Manager";
        $this->data['templates'] = Template::latest()->get();
        return view('admin.Template.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['pageTitle'] = "Template";
        $this->data['mode'] = "store";
        $this->data['url'] = 'template.create';
        return view('admin.Template.create', $this->data);
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
                'category' => $request->category,
            ];
            if (!empty($request->id)) {
                // Update Old Record
                $update = Template::find($request->id);
                $update->update($saveData);

                $message = "updated";
            } else {
                // Add new Record
                // $saveData['slug'] = $this->slug($request->title);
                Template::create($saveData);
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
        $this->data['pageTitle'] = "Template";
        $this->data['mode'] = "update";
        $this->data['url'] = 'template.update';
        $this->data += Template::whereId($id)->first()->toArray();
        return view('admin.Template.create', $this->data);
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
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $saveData = [
                'title' => $request->title,
                'content' => $request->content,
                'category' => $request->category,
            ];
           
            if (!empty($id)) {
                // Update Old Record
                $update = Template::find($id);
                $update->update($saveData);

                $message = "updated";
            }

            session()->flash('message', ['success' => 'Details ' . $message . ' successfully']);
            return redirect()->back();
        }
        session()->flash('message', ['danger' => 'Something went wrong!']);
        return redirect()->back();
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
}
