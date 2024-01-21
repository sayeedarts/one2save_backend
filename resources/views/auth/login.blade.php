@extends('layouts.public')

@section('content')
<div class="inner-banner" style="background-image:url({{asset('public/images')}}/inner-banner.jpg)">
    <div class="thm-container">
       <h3>Login</h3>
    </div>
 </div>
 <section class="about-style-four sec-pad">
    <div class="thm-container">
        @include('includes.flash')
        {{ Form::open(['url' => route('user.login.post'), 'autocomplete' => 'off']) }}
         <div class="row">
             <div class="col-md-6 col-sm-12">
               <div class="form-group">
                 <label class="col-sm-4">Email</label>
                 <div class="col-md-8">
                    {{ Form::text('email', old('email'), ['class' => 'form-control', 'placeholder' => 'Email']) }}
                 </div>
               </div>
             </div>
           </div>
           <div class="row">
             <div class="col-md-6 col-sm-12">
               <div class="form-group">
                 <label class="col-sm-4">Password</label>
                 <div class="col-md-8">
                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) }}
                    @captcha
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
                    <label class="form-check-label" for="remember">Remember Me</label>
                    <p><a href="{{route('forgot.password')}}"> {{__("forgot_password")}} </a></p></p>
                  </div>
               </div>
             </div>
           </div>
         </div>
         <div class="row">
            <div class="col-md-6 col-sm-12">
              
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
               {{-- <p> <a href="forgot-password.html">Forgot Password?</a></p> --}}
             </div>
           </div>
         </div>
        {{ Form::close() }}
    </div>
 </section>

 @endsection