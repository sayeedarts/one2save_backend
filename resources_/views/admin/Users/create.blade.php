@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="page-title">{{$title}}</h2>
            <p class="text-muted">Demo for form control styles, layout options, and custom components for creating a
                wide variety of forms.</p>
            <div class="card shadow mb-4">
                <div class="card-header">
                    <a href="{{route('user.list')}}" class="float-right btn btn-sm btn-primary">Users List</a>
                </div>
                <div class="card-body">
                    @include('includes.flash')
                    @if (!empty($id))
                        {{ Form::open(['url' => route('user.update', ['id' => $id]), 'files' => true, 'autocomplete' => 'off']) }}
                    @else 
                        {{ Form::open(['url' => route('user.store'), 'files' => true, 'autocomplete' => 'off']) }}
                    @endif
                    <input type="hidden" value="{{$id ?? ''}}" name="id">
                    <div class="row">
                        <div class="col-sm-4">
                            <h4>Basic Informations</h4>
                            <div class="form-group mb-3">
                                <label for="simpleinput">Full name {!! $required !!}</label>
                                {{Form::text('name', !empty($name) ? $name : old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Full name'])}}
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="simpleinput">Choose Role {!! $required !!}</label>
                                {{Form::select('role', $roles, !empty($role) ? $role : old('role'), ['class' => 'form-control', 'placeholder' => '-- select --'])}}
                                @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="simpleinput">Email Address {!! $required !!}</label>
                                {{Form::email('email', !empty($email) ? $email : old('email'), ['class' => 'form-control', 'placeholder' => 'Enter Email Address'])}}
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="simpleinput">Phone no. {!! $required !!}</label>
                                {{Form::number('mobile', !empty($mobile) ? $mobile : old('mobile'), ['class' => 'form-control', 'placeholder' => 'Enter Phone no'])}}
                                @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="simpleinput">Password {!! $required !!}</label>
                                {{Form::text('password', !empty($password) ? $password : old('password'), ['class' => 'form-control', 'placeholder' => 'Enter Password'])}}
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                          
                            <div class="row">
                                <div class="col-12">
                                    <input type="submit" class="btn btn-primary btn-md" value="Process Content">
                                </div>
                            </div>

                        </div> <!-- /.col -->
                    </div>
                    </form>
                </div>
            </div> <!-- / .card -->

        </div> <!-- .col-12 -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->

@endsection

@section('scripts')
<x-richtext-editor selector="#htmeditor"/>
@endsection