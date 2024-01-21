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
                    <a href="{{route('role.list')}}" class="float-right btn btn-sm btn-primary">Roles List</a>
                </div>
                <div class="card-body">
                    @include('includes.flash')
                    @if (!empty($id))
                    {{ Form::open(['url' => route('role.update', ['id' => $id]), 'files' => true, 'autocomplete' => 'off']) }}
                    @else
                    {{ Form::open(['url' => route('role.store'), 'files' => true, 'autocomplete' => 'off']) }}
                    @endif
                    <input type="hidden" value="{{$id ?? ''}}" name="id">
                    <div class="row">
                        <div class="col-sm-4">
                            <h4>Basic Informations</h4>
                            <div class="form-group mb-3">
                                <label for="simpleinput">Role name {!! $required !!}</label>
                                {{Form::text('name', !empty($name) ? $name : old('name'), ['class' => 'form-control', 'placeholder' => 'Enter Role name'])}}
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        @isset ($components)
                            @foreach ($components as $component)
                                <div class="col-sm-4">
                                    <h4> {{$component['name']}} </h4>
                                    <div class="form-group mb-3">
                                        @foreach ($component['access'] as $access)
                                        <div class="custom-control custom-checkbox">
                                            {{Form::checkbox('permissions[]', $access, in_array($access, $selected_permissions), ['class' => 'custom-control-input', 'id' => 'checkbox_' . $access])}}
                                            <label class="custom-control-label" for="checkbox_{{$access}}"> {{convert_to_title_case($access)}} </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endisset
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary btn-md" value="Process Content">
                        </div>
                    </div>


                    </form>
                </div> <!-- / .card -->

            </div> <!-- .col-12 -->
        </div> <!-- .row -->
    </div> <!-- .container-fluid -->

    @endsection

    @section('scripts')
    <x-richtext-editor selector="#htmeditor" />
    @endsection