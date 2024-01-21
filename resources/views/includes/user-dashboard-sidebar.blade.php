@php
    $routeName = \Request::route()->getName();
@endphp
<div class="single-sidebar category-sidebar">
    <ul class="categories-list">
        <li class="{{ $routeName == "user.my-account" ? "active" : ""}}">
            <a class="sidemenu" href="{{ route('user.my-account') }}"><i class="fa fa-home" aria-hidden="true"></i> {{__("my_account")}}</a>
        </li>
        <li class="{{ $routeName == "user.profile" ? "active" : ""}}">
            <a class="sidemenu" href="{{ route('user.profile') }}"><i class="fa fa-user" aria-hidden="true"></i> {{__("update_profile")}}</a>
        </li>
        <li class="{{ $routeName == "user.my-appointments" ? "active" : ""}}">
            <a class="sidemenu" href="{{ route('user.my-appointments') }}"><i class="fa fa-stethoscope" aria-hidden="true"></i> {{__('my_appointments')}} </a>
        </li>
        <li class="{{ $routeName == "user.my-reports" ? "active" : ""}}">
            <a class="sidemenu" href="{{ route('user.my-reports') }}"><i class="fa fa-calendar" aria-hidden="true"></i> {{__('my_reports')}} <span class="new"></span></a>
        </li>
        <li class="{{ $routeName == "user.sick-leave.request" ? "active" : ""}}">
            <a class="sidemenu" href="{{ route('user.sick-leave.request') }}">
                <i class="fa fa-medkit" aria-hidden="true"></i> {{__('sick_leave_request')}}
            </a>
        </li>
        <li class="{{ $routeName == "user.change-password" ? "active" : ""}}">
            <a class="sidemenu" href="{{ route('user.change-password') }}"><i class="fa fa-unlock-alt" aria-hidden="true"></i> {{__('change_password')}} </a>
        </li>
        <li class="{{ $routeName == "user.logout" ? "active" : ""}}">
            {{ Form::open(['url' => route('user.logout')]) }}
            {{-- <a href="{{ route('user.logout') }}"><i class="fa fa-power-off" aria-hidden="true"></i> Logout</a> --}}
            <button type="submit" class="sidemenu"><i class="fa fa-power-off" aria-hidden="true"></i> {{__('logout')}} </button>
            {{ Form::close() }}
        </li>
    </ul>
</div>