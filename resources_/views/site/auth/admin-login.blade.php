@extends('layouts.admin-login')

@section('content')
    <div class="wrapper vh-100">
        <div class="row align-items-center h-100">
            {{ Form::open(['url' => route('admin.login.post'), 'class' => 'col-lg-3 col-md-4 col-10 mx-auto text-center'])}}
                <img src="{{asset( path() . 'images/logo.png')}}" alt="" class="card-img-top img-fluid rounded mb-3">
                <h1 class="h6 mb-3">Sign in to continue to Admin Panel</h1>
                @include('includes.flash')
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Email address</label>
                    <input type="email" name="email" id="inputEmail" class="form-control form-control-lg" placeholder="Email address" required="" autofocus="">
                    @error('form.email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input type="password" name="password" id="inputPassword" class="form-control form-control-lg" placeholder="Password" required="">
                </div>
                <div class="checkbox mb-3">
                    <label><input type="checkbox" value="remember-me"> Stay logged in </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Let me in</button>
            <p class="mt-5 mb-3 text-muted">© {{env('APP_NAME')}}</p>
            </form>
        </div>
    </div>
@endsection