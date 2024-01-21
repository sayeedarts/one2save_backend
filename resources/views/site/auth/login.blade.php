@extends('layouts.public')

@section('content')
  <x-page-header :title="$title"/>
  <section class="about-style-four sec-pad">
    <div class="thm-container">
        @include('includes.flash')
        {{ Form::open(['url' => route('user.login.post'), 'autocomplete' => 'off']) }}
         <div class="row">
             <div class="col-md-6 col-sm-12">
               <div class="form-group">
                 <label class="col-sm-4"> {{__('email')}} </label>
                 <div class="col-md-8">
                    {{ Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => 'Email']) }}
                 </div>
               </div>
             </div>
           </div>
           <div class="row">
             <div class="col-md-6 col-sm-12">
               <div class="form-group">
                 <label class="col-sm-4"> {{__('password')}} </label>
                 <div class="col-md-8">
                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) }}
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
                    {{-- {{ Form::checkbox('remember', '', true, ['class' => 'form-check-input' 'id' => 'remember']) }} --}}
                    {{ Form::checkbox('name', 'value', false, ['class' => 'form-check-input', 'id' => 'remember']) }}
                    <label class="form-check-label" for="remember"> {{__('remember_me')}} </label>
                    <p><a href="{{route('forgot.password')}}"> {{__("forgot_password")}} </a></p></p>
                  </div>
               </div>
             </div>
           </div>
         </div>
         <div class="row">
           <div class="col-md-6 col-sm-12">
             <label class="col-sm-4"></label>
             <div class="col-md-8">
               <button class="btn-primary btn-lg" type="submit">Login Now <i class="fa fa-angle-double-right"></i></button>
             </div>
           </div>
         </div>
         <div class="row">
           <div class="col-md-6 col-sm-12">
             <label class="col-sm-4"></label>
             <div class="col-md-8">
               <br/><p>Don't have an account? <a href="{{route('register-patient')}}">Register Now</a></p>
               {{-- <p> <a href="#">Forgot Password?</a></p> --}}
             </div>
           </div>
         </div>
        {{ Form::close() }}
    </div>
 </section>

 @endsection