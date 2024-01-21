@extends('layouts.public')

@section('content')
<x-page-header :title="$title" />
<section class="about-style-four sec-pad">
    <div class="thm-container">
        <div class="">
            <div class="form">
                <div class="note">
                    <p> {{__('patient_register_heading')}} </p>
                </div>
                <div class="form-content">
                    <ul class="tabs">
                        <li class="tab-link @if($type == "ep") {{"current"}} @endif" onclick="location.href = '{{route('register-patient', ['type' => 'ep'])}}';"> 
                            {{__('existing_patient')}} 
                        </li>
                        <li class="tab-link @if($type == "np") {{"current"}} @endif" onclick="location.href = '{{route('register-patient', ['type' => 'np'])}}';">
                            {{__('new_patient')}}
                        </li>
                    </ul>
                    <div class="clear"></div>
                    @include('includes.flash', ['e_type' => 'compact'])
                    <!--Existing Patient Registartion Starts-->
                    <div id="tab-1" class="tab-content @if($type == "ep") {{"current"}} @endif">
                        @include('site.auth._register-by-code')
                    </div>
                    <!--Existing Patient Registartion Ends-->

                    <!--New Patient Registartion Starts-->
                    <div id="tab-2" class="tab-content @if($type == "np") {{"current"}} @endif">
                        {{ Form::open(['url' => route('add.patient'), 'autocomplete' => 'off']) }}
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="firstname" class="form-control" placeholder="{{__('firstname')}} {!! $required !!}" value="{{old('firstname')}}" />
                                        @error('firstname') 
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="secondname" value="{{old('secondname')}}" class="form-control" placeholder="{{__('secondname')}} {!! $required !!}">
                                        @error('secondname') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="thirdname" value="{{old('thirdname')}}" class="form-control" placeholder="{{__('thirdname')}} {!! $required !!}">
                                        @error('thirdname') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="lastname" value="{{old('lastname')}}" class="form-control" placeholder="{{__('lastname')}} {!! $required !!}">
                                        @error('lastname') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="firstname_ar" value="{{old('firstname_ar')}}" class="form-control" placeholder="{{__('firstname_ar')}}">
                                        @error('firstname_ar') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="secondname_ar" value="{{old('secondname_ar')}}" class="form-control" placeholder="{{__('secondname_ar')}}">
                                        @error('secondname_ar') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="thirdname_ar" value="{{old('thirdname_ar')}}" class="form-control" placeholder="{{__('thirdname_ar')}}">
                                        @error('thirdname_ar') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="lastname_ar" value="{{old('lastname_ar')}}" class="form-control" placeholder="{{__('lastname_ar')}}">
                                        @error('lastname_ar') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="phone" value="{{old('phone')}}" class="form-control" placeholder="{{__("mobile")}}  {!! $required !!}">
                                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="{{__("email")}} {!! $required !!}">
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label>{{__("gender")}} {!! $required !!}</label>
                                        <div class="form-check form-check-inline">
                                            @foreach ($genders as $genderCode => $genderName)
                                            <input class="form-check-input" type="radio"
                                                id="inlineRadio_{{md5($genderCode)}}" value="{{$genderCode}}" name="gender" {{ old('gender') == $genderCode ? 'checked' : ''}}>
                                            <label class="form-check-label"
                                                for="inlineRadio_{{md5($genderCode)}}">{{ucfirst($genderName)}}</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                        @endforeach
                                        </div>
                                        @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <div class="datepicker-wrap">
                                            <input type="text" name="dob" value="{{old('dob')}}" class="form-control datepicker" placeholder="{{__("dob")}} {!! $required !!}">
                                            @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{Form::select('nationality', $nationalities, $nationality ?? '', ['class' => 'form-control form-control-lg', 'placeholder' => __("nationality") . " " . $required])}}
                                        @error('nationality') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{Form::select('country_id', $countries, $country_id ?? '', ['class' => 'form-control form-control-lg', 'placeholder' => __("country") . " " . $required])}}
                                        @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{Form::select('city_id', $cities, $city_id ?? '', ['class' => 'form-control form-control-lg', 'placeholder' => __("city") . " " . $required])}}
                                        @error('city_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{ Form::select('religion', $religions, $religion ?? '', ['class' => 'form-control form-control-lg', 'placeholder' => __("religion") . " " . $required]) }}
                                        @error('religion') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        {{ Form::select('national_id_type', $national_id_types, $national_id_type ?? '', ['placeholder' => __("national_id_type") . " " . $required, 'class' => 'form-control']) }}
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="national_id" value="{{old('national_id')}}" class="form-control" placeholder="{{__("national_id")}} {!! $required !!}">
                                        @error('national_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="terms_and_conditions" id="agree" value="option1">
                                            <label class="form-check-label" for="agree">Agree <a style="font-weight:600;"
                                                    href="terms-conditions.php">{{__("terms_and_conditions")}}</a></label>
                                        </div>
                                        @error('terms_and_conditions') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button class="btn-primary btn-lg" type="submit">
                                        {{__("register_now")}}
                                        <i class="fa fa-angle-double-right"></i>
                                    </button>
                                </div>
                            </div>
                        {{Form::close()}}
                    </div>
                    <!--New Patient Registartion Ends-->
                    <div class="row">
                        <div class="col-sm-12">
                            <p><br />{{__("already_have_account")}} <a href="{{route('user.login')}}">{{__("login_now")}}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $(function () {
        $(".datepicker").datepicker({
            changeYear: true,
            changeMonth: true,
            yearRange: "-100:+0"
        });
    });
</script>
@endsection