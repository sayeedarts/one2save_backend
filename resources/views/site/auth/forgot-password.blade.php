@extends('layouts.public')

@section('content')
    <x-page-header :title="$title"/>
    <section class="about-style-four sec-pad">
        <div class="thm-container">
            @include('includes.flash')
            {{ Form::open(['url' => route($target), 'autocomplete' => 'off']) }}
                @if (!empty($type) && $type == "change" && !empty($email))
                    {{ Form::hidden('email', $email) }}
                    {{ Form::hidden('token', $token) }}
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-4">{{__('password')}} {!! $required !!}</label>
                                <div class="col-md-8">
                                    {{Form::password('password', ['class' => 'form-control', 'placeholder' => __("password")])}}
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-4">{{__('reenter_password')}} {!! $required !!}</label>
                                <div class="col-md-8">
                                    {{Form::password('re_enter_password', ['class' => 'form-control', 'placeholder' => __("reenter_password")])}}
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @else 
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-4">{{__('email')}} {!! $required !!}</label>
                                <div class="col-md-8">
                                    <input type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="Enter your recovery Email address" required>
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label class="col-sm-4"></label>
                        <div class="col-md-8">
                            <button class="btn-primary btn-lg" type="submit">{{__("send_link")}} <i class="fa fa-angle-double-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label class="col-sm-4"></label>
                        <div class="col-md-8">
                            <br />
                            <p>
                                {{__("already_have_account")}}
                                <a href="{{route('user.login')}}">{{__("login_now")}}</a>
                            </p>
                        </div>
                    </div>
                </div>
            {{Form::close()}}
        </div>
    </section>

@endsection
