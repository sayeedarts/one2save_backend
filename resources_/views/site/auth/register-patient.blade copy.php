@extends('layouts.public')

@section('content')
    <x-page-header :title="$title"/>
    <section class="about-style-four sec-pad">
        <div class="thm-container">
            <h4 class="sub-title">{{__("general.registration_subtitle")}}</h4>
            @include('includes.flash')
            {{ Form::open(['url' => route('add.patient'), 'autocomplete' => 'off']) }}
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('general.firstname')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="firstname" value="{{old('firstname')}}" class="form-control" placeholder="">
                                @error('firstname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('general.secondname')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="secondname" value="{{old('secondname')}}" class="form-control" placeholder="">
                                @error('secondname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('general.thirdname')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="thirdname" value="{{old('thirdname')}}" class="form-control" placeholder="">
                                @error('thirdname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('general.lastname')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="lastname" value="{{old('lastname')}}" class="form-control" placeholder="">
                                @error('lastname') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Arabic fields for name entry --}}
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('general.firstname_ar')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="firstname_ar" value="{{old('firstname_ar')}}" class="form-control" placeholder="">
                                @error('firstname_ar') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('general.secondname_ar')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="secondname_ar" value="{{old('secondname_ar')}}" class="form-control" placeholder="">
                                @error('secondname_ar') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('general.thirdname_ar')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="thirdname_ar" value="{{old('thirdname_ar')}}" class="form-control" placeholder="">
                                @error('thirdname_ar') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('general.lastname_ar')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="lastname_ar" value="{{old('lastname_ar')}}" class="form-control" placeholder="">
                                @error('lastname_ar') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.mobile")}}  {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="text" name="phone" value="{{old('phone')}}" class="form-control" placeholder="">
                                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.email")}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.gender")}} {!! $required !!}</label>
                            <div class="col-md-8">
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
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.dob")}} {!! $required !!}</label>
                            <div class="col-md-8 datepicker-wrap-x">
                                <input type="text" name="dob" value="{{old('dob')}}" class="form-control datepicker" >
                                @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.nationality")}} {!! $required !!}</label>
                            <div class="col-md-8">
                                {{Form::select('nationality', $nationalities, $nationality ?? '', ['class' => 'form-control form-control-lg', 'placeholder' => '-- Select --'])}}
                                @error('nationality') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.country")}} {!! $required !!}</label>
                            <div class="col-md-8 datepicker-wrap-x">
                                {{Form::select('country_id', $countries, $country_id ?? '', ['class' => 'form-control form-control-lg', 'placeholder' => '-- Select --'])}}
                                @error('country_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.city")}} {!! $required !!}</label>
                            <div class="col-md-8">
                                {{Form::select('city_id', $cities, $city_id ?? '', ['class' => 'form-control form-control-lg', 'placeholder' => '-- Select --'])}}
                                @error('city_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.national_id_type")}} {!! $required !!}</label>
                            <div class="col-md-8">
                                {{ Form::select('national_id_type', $national_id_types, $national_id_type ?? '', ['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.national_id")}} {!! $required !!}</label>
                            <div class="col-md-8">
                                    <input type="text" name="national_id" value="{{old('national_id')}}" class="form-control" placeholder="">
                                @error('national_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__("general.religion")}} {!! $required !!}</label>
                            <div class="col-md-8">
                                {{ Form::select('religion', $religions, $religion ?? '', ['class' => 'form-control form-control-lg', 'placeholder' => '-- Select --']) }}
                                
                                @error('religion') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4"></label>
                            <div class="col-md-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="terms_and_conditions" id="agree" value="option1">
                                    <label class="form-check-label" for="agree">
                                        {{__("general.agree")}} <a style="font-weight:600;" href="">{{__("general.terms_and_conditions")}}</a>
                                    </label>
                                </div>
                                @error('terms_and_conditions') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label class="col-sm-4"></label>
                        <div class="col-md-8">
                            <button class="btn-primary btn-lg" type="submit">{{__("general.register_now")}} <i class="fa fa-angle-double-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label class="col-sm-4"></label>
                        <div class="col-md-8">
                            <br />
                            <p>
                                {{__("general.already_have_account")}}
                                <a href="{{route('user.login')}}">{{__("general.login_now")}}</a>
                            </p>
                        </div>
                    </div>
                </div>
            {{Form::close()}}
        </div>
    </section>
</div>
@endsection

@section('scripts')
    <script>
    $( function() {
        $( ".datepicker" ).datepicker({
            changeYear: true,
            changeMonth: true,
            yearRange: "-100:+0"
        });
    });
    </script>
@endsection