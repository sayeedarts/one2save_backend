<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CareerRequest;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Order;
use App\Models\QuoteRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public $data;

    public function show()
    {
        $this->data = [];

        $this->data['chart_data'] = $this->_homeOrderStats();
        $this->data['dashlets'] = $this->_getDashletData();
        // $this->data['dashlets'] = $this->getStatisticsReport();
        // $this->data['registrations'] = Patient::with('hospital')->latest()->take(5)->get();
        return view('admin.Dashboard.show', $this->data);
    }

    public function _homeOrderStats()
    {
        $storageOrders = $packingOrders = $days = [];

        $previousDays = array_reverse(lastNDays(7));
        foreach ($previousDays as $key => $day) {
            $storageOrders[] = Order::where('module', 'storage')
                ->whereDate('created_at', $day)
                ->count();

            $packingOrders[] = Order::where('module', 'packing')
                ->whereDate('created_at', $day)
                ->count();

            $days[] = date('d M', strtotime($day));
        }
        return [
            'days' => json_encode($days),
            'storage' => json_encode($storageOrders),
            'packing' => json_encode($packingOrders)
        ];
    }

    public function _getDashletData()
    {
        $storageOrders = Order::where('module', 'storage')->count();
        $packingOrders = Order::where('module', 'packing')->count();
        $quotes = QuoteRequest::count();
        $users = User::where('role', '!=', 'admin')->count();

        return [
            'storages' => ['title' => 'Storage Orders', 'count' => $storageOrders],
            'packings' => ['title' => 'Packing Orders', 'count' => $packingOrders],
            'quotes' => ['title' => 'Quote Requests', 'count' => $quotes],
            'users' => ['title' => 'Users', 'count' => $users],
        ];
    }

    /**
     * Get last 7 days statistic report for the chart view
     *
     * @param none
     *
     * @author tanmayapatra
     * @date 03 jan 2021
     * @return array
     */
    private function _last7DaysRegistration()
    {
        // $chartData = $dateList = $registrationData = $appointmentData = $jobRequestData = [];
        // $last7Days = array_reverse(last7Days());

        // foreach ($last7Days as $key => $day) {
        //     $registrationData[] = Patient::whereDate('created_at', $day)->count();
        //     $appointmentData[] = Appointment::whereDate('created_at', $day)->count();
        //     $jobRequestData[] = CareerRequest::whereDate('created_at', $day)->count();
        //     $dateList[] = dbtoDate($day);
        // }

        // return [
        //     'x_axis' => $dateList,
        //     'y_axis' => [
        //         [
        //             "name" => "Registration",
        //             "data" => $registrationData,
        //         ],
        //         [
        //             "name" => "Appointment",
        //             "data" => $appointmentData,
        //         ],
        //         [
        //             "name" => "Job Requests",
        //             "data" => $jobRequestData,
        //         ],
        //     ],
        // ];
    }

    public function getStatisticsReport()
    {
        // $statistics = [];
        // // get total registration counts
        // $totalRegistrations = Patient::count();
        // $totalAppointments = Appointment::count();
        // $totalDoctors = Doctor::count();
        // $totalJobApplications = CareerRequest::count();

        // return [
        //     [
        //         'name' => "Total Registrations",
        //         'icon' => 'fe-arrow-down-circle',
        //         'count' => $totalRegistrations,
        //     ],
        //     [
        //         'name' => "Total Appointments",
        //         'icon' => 'fe-at-sign',
        //         'count' => $totalAppointments,
        //     ],
        //     [
        //         'name' => "Total Doctors",
        //         'icon' => 'fe-plus',
        //         'count' => $totalDoctors,
        //     ],
        //     [
        //         'name' => "Total Job Applications",
        //         'icon' => 'fe-user-plus',
        //         'count' => $totalJobApplications,
        //     ]
        // ];
    }
}
