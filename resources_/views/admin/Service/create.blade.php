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
                        <a href="{{route('service.list')}}" class="float-right btn btn-sm btn-primary"> {{$pageTitle}} List</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route('service.store'), 'files' => true]) !!}
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
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="simpleinput">Icon {!! $required !!}</label>
                                            {{ Form::file('image', ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
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
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Include as a Page? {!! $required !!}</label>
                                    {{ Form::select('display_type', ['page' => "Only Page", 'quotation' => "Only Quotation Purpose", 'both' => 'Page & Quotation Purpose'], (!empty($display_type)) ? $display_type : old('display_type'), ['class' => 'form-control hospital', 'id' => 'display_type']) }}
                                    <small>If you want to show as a Page then It will appear at top-menu</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Quotation Enabled? {!! $required !!}</label>
                                    {{ Form::select('status', $yesNo, (!empty($status)) ? $status : old('status'), ['class' => 'form-control hospital', 'id' => 'status']) }}
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Is Featured? {!! $required !!}</label>
                                    {{ Form::select('featured', $yesNo, (!empty($featured)) ? $featured : old('featured'), ['class' => 'form-control hospital', 'id' => 'featured']) }}
                                </div>
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="simpleinput">Image {!! $required !!}</label>
                                            {{ Form::file('picture', ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                        </div>
                                        <div class="col-sm-4">
                                            <div>
                                                @isset($image)
                                                <img class="align-top img-thumbnail" src="{{show($image, 'service_image', '280x200')}}" alt="icon">
                                                @endisset
                                            </div>
                                        </div>
                                    </div>
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

<script>
    $(document).ready(function() {
        $("#add-new-item").on('click', function() {
            var itemHtml, uniqueHtml, uuid;
            itemHtml = '{!! item() !!}';
            uuid = Math.floor(Math.random() * 99999) + Date.now() + Math.floor(Math.random() * 99999);;
            uniqueHtml = itemHtml.replace(/UUID/g, uuid);
            $(".service-category-items-section").append(uniqueHtml);
            removeRow();
        });
        // removeRow();
    });

    function removeRow() {
        console.log("Called");
        $(".remove-item").on('click', function() {
            var getDeleteId = $(this).attr('data-id');
            $(".item_" + getDeleteId).remove();
        });
    }
</script>
@endsection