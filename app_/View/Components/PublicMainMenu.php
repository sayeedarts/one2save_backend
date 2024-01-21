<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PublicMainMenu extends Component
{
    public $publicMainMenu;
    public $getLang;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        $this->publicMainMenu = [
            [
                'title' => __('home'),
                'link' => route('landing'),
            ],
        ];
        // Append Dynamic Page's Links
        $getPages = getPages();
        if (!empty($getPages[0])) {
            $primaryMenu = $subMenu = [];
            foreach ($getPages as $key => $page) {
                if ($page['slug'] == 'about-us') {
                    $primaryMenu = [
                        'title' => lang($page['name_' . $this->locale]),
                        'link' => 'javascript:void(0)',
                    ];
                } else {
                    $subMenu[] = [
                        'title' => lang($page['name_' . $this->locale]),
                        'link' => route('page.details', $page['slug']),
                    ];
                }
                $primaryMenu['submenu'] = $subMenu;
            } // end foreack
            $this->publicMainMenu[] = $primaryMenu;
        }
        // Append Hospital Menus
        $hospitalMenus = publicMenu();
        $allHospitalLinks = [];
        foreach ($hospitalMenus as $hosKey => $hospitalMenu) {
            $allHospitalLinks[] = [
                'title' => lang($hospitalMenu['name_' . $this->locale]),
                'link' => route('hospital.show', $hospitalMenu['slug']),
            ];
        }

        $this->publicMainMenu[] = [
            'title' => __('hospitals'),
            'link' => 'javascript:void(0)',
            'submenu' => $allHospitalLinks,
        ];

        $this->publicMainMenu[] = [
            'title' => __('departments'),
            'link' => route('departments'),
        ];

        $this->publicMainMenu[] = [
            'title' => __('media_center'),
            'link' => 'javascript:void(0);',
            'submenu' => [
                [
                    'title' => __('news_events'),
                    'link' => route('events.list'),
                ],
                [
                    'title' => __('photo_gallery'),
                    'link' => route('images.list'),
                ],
                [
                    'title' => __('video_gallery'),
                    'link' => route('videos.list'),
                ],
            ],
        ];
        // Service Menu listing
        $eServiceSubmenu = [];
        if (empty(\Auth::user()->id)) {
            $eServiceSubmenu = array_merge($eServiceSubmenu, [
                [
                    'title' => __('register'),
                    'link' => route('register-patient'),
                ],
            ]);
        }
        $eServiceSubmenu = array_merge($eServiceSubmenu, [
            [
                'title' => __('book_appointment'),
                'link' => route('book-an-appointment'),
            ],
            [
                'title' => __('lab_and_radiology_report'),
                'link' => route('user.my-reports'),
            ],
            [
                'title' => __('sick_leave_verification'),
                'link' => route('user.sick-leave.request'),
            ],
            [
                'title' => __('bmi_check'),
                'link' => route('bmi-check'),
            ],
        ]);
        $this->publicMainMenu[] = [
            'title' => __('e_services'),
            'link' => 'javascript:void(0);',
            'submenu' => $eServiceSubmenu,
        ];

        $this->publicMainMenu[] = [
            'title' => __('careers'),
            'link' => route('departments'),
            'submenu' => [
                [
                    'title' => __('job_vacancies'),
                    'link' => route('vacancies.list'),
                ],
                [
                    'title' => __('apply_online'),
                    'link' => route('job.apply'),
                ],
            ],
        ];
        $this->publicMainMenu[] = [
            'title' => __('customer_care'),
            'link' => route('customer.care'),
        ];
        return view('components.public-main-menu');
    }
}
