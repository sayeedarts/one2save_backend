<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\CustomerTicket;
use Illuminate\Http\Request;

class CustomerCareController extends Controller
{
    public $data;

    protected $rules = [
        'type' => 'required',
        'name' => 'required',
        'phone' => 'required',
        'email' => 'required',
        'details' => 'required',
    ];

    public function show()
    {
        $this->data['title'] = __('customer_care');
        return view('site.customer.care', $this->data);
    }

    /**
     * Save the Customer care request
     *
     * @author tanmayap
     * @date 11 nov 2020
     */
    public function store(Request $request)
    {
        $validate = \Validator::make($request->all(), $this->rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors())->withInput();
        } else {
            $request->request->add([
                'user_id' => \Auth::user()->id ?? 0,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);
            CustomerTicket::create($request->all());
            session()->flash('message', ['success' => 'Your request received successfully']);
            return redirect()->back();
        }
    }
}
