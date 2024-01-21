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
                        <a href="{{route('template.index')}}" class="float-right btn btn-sm btn-primary"> {{$pageTitle}} List</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route($url, $id ?? null), 'method' => $mode == 'update' ? 'put' : 'post']) !!}
                        <input type="hidden" name="id" value="{{$id ?? old('id')}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Title {!! $required !!}</label>
                                    {{ Form::text('title', !empty($title) ? $title : old('title'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                @isset($slug)
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Slug (auto-generated)</label>
                                    {{ Form::text('', $slug, ['class' => 'form-control', 'disabled' => true, 'id' => 'simpleinput']) }}
                                </div>
                                @endisset
                            </div> <!-- /.col -->
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Category {!! $required !!}</label>
                                    {{ Form::select('category', ['quotation' => 'Quotation'], (!empty($category)) ? $category : old('category'), ['class' => 'form-control hospital', 'id' => 'category']) }}
                                    <small>If you want to show as a Blog then It will appear at top-menu and won't be seen in Quotation Module</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="example-email">Service Descriptions {!! $required !!}</label>
                                    {{Form::textarea('content', !empty($content) ? $content : old('content'), ['id' => 'htmeditor'])}}
                                    @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <input type="submit" class="btn btn-primary btn-md" value="{{ $mode == "store" ? "Add" : "Update" }} {{$pageTitle}}">
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
<x-richtext-editor selector="#htmeditor"/>
@endsection