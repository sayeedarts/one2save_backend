@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title">Manage Job vacancies</h2>
                <p class="text-muted">Demo for form control styles, layout options, and custom components for creating a
                    wide variety of forms.</p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title">{{ $mode == "store" ? "Add new" : "Update" }} Job</strong>
                        <a href="{{route('career.list')}}" class="float-right btn btn-sm btn-primary">Job List</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route('career.'. $mode), 'files' => true]) !!}
                            <input type="hidden" name="id" value="{{$id ?? old('id')}}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="simpleinput">Title {!! $required !!}</label>
                                        {{ Form::text('title', !empty($title) ? $title : old('title'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="simpleinput">Hospital {!! $required !!}</label>
                                        {{ Form::select('hospital_id', $hospitals, !empty($hospital_id) ? $hospital_id : old('hospital_id'), ['class' => 'form-control hospital', 'id' => 'hospital', 'placeholder' => '-- Select --', 'onchange' => 'getDepartments(this.value)']) }}
                                        @error('hospital_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="simpleinput">Job type {!! $required !!}</label>
                                        {{ Form::select('job_type', ['Full Time' => 'Full Time', 'Part Time' => 'Part Time'], !empty($job_type) ? $job_type : old('job_type'), ['class' => 'form-control hospital', 'id' => 'hospital', 'placeholder' => '-- Select --', 'onchange' => 'getDepartments(this.value)']) }}
                                        @error('job_type') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="simpleinput">Job field </label>
                                        <input type="text" name="field" id="simpleinput" class="form-control" value="{{ $field ?? old('field')}}">
                                        @error('field') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="simpleinput">Job level </label>
                                        <input type="text" name="level" id="simpleinput" class="form-control" value="{{ $level ?? old('level')}}">
                                        @error('level') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="simpleinput">Publish on/after {!! $required !!} (Leave it for current date)</label>
                                        <input type="text" name="publish_on" id="simpleinput" class="drgpicker form-control" value="{{ $publish_on ?? old('publish_on')}}">
                                        @error('publish_on') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="submit" class="btn btn-primary btn-md" value="{{ $mode == "store" ? "Add" : "Update" }} Job">
                                        </div>
                                    </div>
                                </div> <!-- /.col -->
                                <div class="col-6">
                                    <div class="form-group mb-3">
                                        <label for="simpleinput">Details {!! $required !!}</label>
                                        <input type="hidden" name="details" id="details" class="form-control" value="{{ $details ?? old('details')}}">
                                        <trix-editor input="details"></trix-editor>
                                        @error('details') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div> <!-- / .card -->
            </div> <!-- .col-12 -->
        </div> <!-- .row -->
    </div> <!-- .container-fluid -->
</div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ asset( path('admin') . '/plugins/trix/trix.css') }}">
    <script src='{{ asset( path('admin') . '/plugins/trix/trix.js') }}'></script>
    <script src='{{ asset( path('admin') . '/js/daterangepicker.js') }}'></script>
    <script src='{{ asset( path('admin') . '/js/select2.min.js') }}'></script>
    <script>
        $('.drgpicker').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            showDropdowns: true,
            locale: {
                format: 'MM/DD/YYYY'
            }
        });
    </script>
@endsection