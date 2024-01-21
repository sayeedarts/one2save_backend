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
                    <a href="{{route('page.list')}}" class="float-right btn btn-sm btn-primary">Dynamic Page List</a>
                </div>
                <div class="card-body">
                    @include('includes.flash')
                    {{ Form::open(['url' => route('page.' . $mode), 'files' => true, 'autocomplete' => 'off']) }}
                    <input type="hidden" value="{{$id ?? ''}}" name="id">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Basic Informations</h4>
                            <div class="form-group mb-3">
                                <label for="simpleinput">Page Title {!! $required !!}</label>
                                {{Form::text('name', !empty($name) ? $name : old('name'), ['class' => 'form-control', 'placeholder' => 'Enter a Page Name'])}}
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group mb-3">
                                        <label for="example-email">Page Type {!! $required !!}</label>
                                        @if (!empty($id))
                                            <p>{{$types[$type]}}</p>
                                        @endif
                                        {{Form::select('type', $types, $type ?? '', ['class' => (!empty($id)) ? 'd-none' : 'form-control'])}}
                                        @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group mb-3">
                                        <label for="file">Upload Image/File</label>
                                        {{Form::file('file', ['class' => 'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <p>File preview</p>
                                    @if (!empty($id) && !empty($asset))
                                        @php 
                                            $resolution = config('params.resolutions');
                                        @endphp
                                        <img src="{{show($asset, $type, $resolution[$type][0])}}" class="img-fluid"/>
                                    @else 
                                    <p>[No file choosen]</p>
                                    @endif
                                    
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="example-email">Page Contents {!! $required !!}</label>
                                {{Form::textarea('content', !empty($content) ? $content : old('content'), ['id' => 'htmeditor'])}}
                                @error('content') <span class="text-danger">{{ $message }}</span> @enderror
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