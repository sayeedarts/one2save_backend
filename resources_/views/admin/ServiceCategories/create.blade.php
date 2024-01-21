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
                        <a href="{{route('service-categories.list')}}" class="float-right btn btn-sm btn-primary"> {{$pageTitle}} List</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route('service-categories.store'), 'files' => true]) !!}
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
                                    <label for="simpleinput">Icon {!! $required !!}</label>
                                    {{ Form::file('image', ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Chosoe Service {!! $required !!}</label>
                                    {{ Form::select('service_id', $services, !empty($service_id) ? $service_id : old('service_id'), ['class' => 'form-control hospital', 'id' => 'service_id', 'placeholder' => '-- Select --']) }}
                                    @error('service_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Is Enabled? {!! $required !!}</label>
                                    {{ Form::select('status', [1 => 'Yes', 0 => 'No'], '', ['class' => 'form-control hospital', 'id' => 'status']) }}
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Include as a Page? {!! $required !!}</label>
                                    {{ Form::select('display_type', ['page' => "Only Page", 'quotation' => "Only Quotation Purpose", 'both' => 'Page & Quotation Purpose'], (!empty($display_type)) ? $display_type : old('display_type'), ['class' => 'form-control hospital', 'id' => 'display_type']) }}
                                    <small>If you want to show as a Page then It will appear at top-menu</small>
                                </div>

                            </div> <!-- /.col -->
                            <div class="col-6">
                                <h4>Add Service Category Items</h4>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input type="button" class="float-right btn btn-primary" id="add-new-item" value="Add">
                                    </div>
                                </div>
                                <div class="service-category-items-section">
                                    @isset($category_item)
                                    @foreach($category_item as $category)
                                    {!! item(1, $category, true) !!}
                                    @endforeach
                                    @endisset
                                    {!! item(1, [], true) !!}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="example-email">Descriptions {!! $required !!}</label>
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