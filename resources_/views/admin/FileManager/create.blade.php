@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title"> {{ $mode == "store" ? "Add new" : "Update" }} {{ $pageTitle }} </h2>
                <p class="text-">In case you need any public location/URL of any file/Image/PDF, then you can upload that file here and get the corresponding Path for later usages. </p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <a href="{{route('file-manager.list')}}" class="float-right btn btn-sm btn-primary"> {{$pageTitle}} List</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route('file-manager.store'), 'files' => true]) !!}
                        <input type="hidden" name="id" value="{{$id ?? old('id')}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="simpleinput">Choose File {!! $required !!}</label>
                                            {{ Form::file('upload', ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                            @error('upload') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-sm-4">
                                            <div>
                                                @isset($file)
                                                <img class="align-top" src="{{show($file, 'file_manager', '1200x730')}}" alt="icon">
                                                @endisset
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- /.col -->
                            <div class="col-6">
                            <!-- <div class="form-group mb-3">
                                    <label for="example-email">Descriptions {!! $required !!}</label>
                                    {{Form::textarea('description', !empty($description) ? $description : old('description'), ['class' => 'form-control', 'cols' => 30, 'rows' => 5])}}
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>-->
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-primary btn-md" value="{{ $mode == "store" ? "Add" : "Update" }} {{$pageTitle}}">
                            </div>
                        </div>
                        @if (!empty($image))
                        <div class="row my-5">
                            <div class="col-12">
                                <img src="{{$image_url}}" class="img-fluid" alt="{{$title}}"/>
                            </div>
                        </div>
                        @endif
                        {{ Form::close() }}
                    </div>
                </div> <!-- / .card -->
            </div> <!-- .col-12 -->
        </div> <!-- .row -->
    </div> <!-- .container-fluid -->
</div>
@endsection

@section('scripts')

@endsection