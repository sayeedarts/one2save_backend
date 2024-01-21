@extends('layouts.public')

@section('content')
    <x-page-header :title="$title"/>
    <section class="about-style-four sec-pad">
        <div class="thm-container">
            @include('includes.flash')
            {{ Form::open(['url' => route('forgot.password.send'), 'autocomplete' => 'off']) }}
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="col-sm-4">{{__('email')}} {!! $required !!}</label>
                            <div class="col-md-8">
                                <input type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
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
