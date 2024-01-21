<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models as Mo;

class LanguageController extends Controller
{
    public $data;
    private $baseDirectory;
    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'required',
    ];

    public function __construct()
    {
        $this->data['required'] = "<span class='text-danger'>*</soan>";
        $this->baseDirectory = getcwd() . "/resources/lang";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->data['languages'] = Mo\Language::latest()->get();
        return view('admin.Language.index', $this->data);
    }

    public function editParams($id)
    {
        $getContent = $this->getLanguageContents($id);
        $this->data['contents'] = $getContent['content'];
        $this->data['short_form'] = $getContent['code'];
        return view('admin.Language.edit-language-contents', $this->data);
    }

    public function getLanguageContents($id)
    {
        
        $getLanguage = Mo\Language::whereId($id)->select('short_form')->first();
        $landDirectory = getcwd() . "/resources/lang";
        $defaultLanguage = $getLanguage->short_form;
        // echo $defaultLanguage; exit;
        $getDefaultLanguageContent = $landDirectory . '/' . $defaultLanguage . ".json";
        $getContent = \file_get_contents($getDefaultLanguageContent);
        return [
            'code' => $getLanguage->short_form,
            'content' => json_decode($getContent, true)
        ];

         // $content = \json_encode([
        //     "latest_news_event" => "Latest News & Events EN"
        // ]);
        // $landDirectory = getcwd() . "/resources/lang";
        // $filename = $landDirectory . "/file.json";
        // // echo fileperms($filename); exit;
        // file_put_contents($filename, $content);
        // $this->getLanguage();

        // exit;
    }

    public function updateContent(Request $request)
    {
        $shortForm = $request->short_form;
        $getContent = json_encode($request->except('_token', 'short_form'));
        $filename = $this->baseDirectory . "/" . $shortForm .".json";
        file_put_contents($filename, $getContent);
        session()->flash('message', ['success' => 'Language file updated!']);
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
