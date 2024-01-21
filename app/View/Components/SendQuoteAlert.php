<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SendQuoteAlert extends Component
{
    /**
     * The alert type.
     *
     * @var string
     */
    public $email;

    /**
     * The alert type.
     *
     * @var string
     */
    public $mobile;
    public $id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $email, $mobile)
    {
        $this->id = $id;
        $this->email = $email;
        $this->mobile = $mobile;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.send-quote-alert');
    }
}
