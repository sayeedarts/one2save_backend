@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title"> {{ $mode == "store" ? "Add new" : "Update" }} {{ $pageTitle }} </h2>
                <p class="text-muted">Demo for form control styles, layout options, and custom components for creating a
                    wide variety of forms.</p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <a href="{{route('gallery.list')}}" class="float-right btn btn-sm btn-primary"> {{$pageTitle}} List</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route('gallery.store'), 'files' => true]) !!}
                        <input type="hidden" name="id" value="{{$id ?? old('id')}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Title {!! $required !!}</label>
                                    {{ Form::text('title', !empty($title) ? $title : old('title'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="simpleinput">Image {!! $required !!}</label>
                                            {{ Form::file('photo', ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                            @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-sm-4">
                                            <div>
                                                @isset($icon)
                                                <img class="align-top" src="{{show($icon, 'service', '50x50')}}" alt="icon">
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
                                </div>
                            </div> -->
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