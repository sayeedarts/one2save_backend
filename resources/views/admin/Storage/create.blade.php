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
                        <a href="{{route('storage.list')}}" class="float-right btn btn-sm btn-primary"> {{$pageTitle}} List</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route('storage.store'), 'files' => true]) !!}
                        <input type="hidden" name="id" value="{{$id ?? old('id')}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Storage Category {!! $required !!}</label>
                                    {{ Form::select('storage_type_id', $types, !empty($storage_type_id) ? $storage_type_id : old('storage_type_id'), ['class' => 'form-control', 'id' => 'simpleinput', 'placeholder' => '-- Select --']) }}
                                    @error('storage_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Name {!! $required !!}</label>
                                    {{ Form::text('name', !empty($name) ? $name : old('name'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Price {!! $required !!}</label>
                                    {{ Form::text('price', !empty($price) ? $price : old('price'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Area {!! $required !!}</label>
                                    {{ Form::text('area', !empty($area) ? $area : old('area'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('area') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="submit" class="btn btn-primary btn-md" value="{{ $mode == "store" ? "Add" : "Update" }} {{$pageTitle}}">
                                    </div>
                                </div>
                            </div> <!-- /.col -->
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Dimension (W x D x H) in ft. {!! $required !!}</label>
                                    {{ Form::text('dimension', !empty($dimension) ? $dimension : old('dimension'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('dimension') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Description {!! $required !!}</label>
                                    {{ Form::textarea('description', !empty($description) ? $description : old('description'), ['class' => 'form-control', 'id' => 'simpleinput', 'rows' => 5]) }}
                                    @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="simpleinput">Add File </label>
                                            {{ Form::file('picture', ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                        </div>
                                        <div class="col-sm-4">
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div>
                                            @isset($file)
                                                @if (get_file_type($file) == "video")
                                                    <video width="640" height="360" controls>
                                                        <source src="{{show_file($file, 'storage', '')}}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @elseif (get_file_type($file) == "image")
                                                    <img class="align-top img-thumbnail" src="{{show_file($file, 'storage', '')}}" alt="icon">
                                                @else
                                                    <p>File is not supported for the preview!</p>
                                                @endif
                                            @endisset
                                        </div>
                                    </div>
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


<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Storage Gallery</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Start -->
                <div class="row">
                    @isset($images)
                        @foreach($images as $image)
                        <div class="col-sm-12 mb-3 storage-img-{{$image['id']}}">
                            <div class="card p-3">
                                <div class="embed-responsive embed-responsive-4by3">
                                    <iframe class="embed-responsive-item" src="{{ show_file($image['image'], 'storage') }}"></iframe>
                                </div>
                                <div class="card-body text-center p-0 mt-3">
                                    <button class="btn btn-danger btn-sm" onclick="deleteStorageImage({{$image['id']}})">Delete</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endisset
                </div>

                <!-- End -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
<script>
    var storage_images_delete = "{{ route('storage-images.delete') }}";
    var storage_id = "{{!empty($id) ? $id : 0}}";
    function deleteStorageImage(id = null) {
        // e.preventDefault();
        // var formData = $('.subscribe-form').serialize();
        $.ajax(storage_images_delete, {
            type: 'POST',
            data: {
                storage_id: storage_id,
                id: id,
                type: 'single'
            },
            dataType: 'json',
            success: function(response, status, xhr) {
                $(".storage-img-" + id).remove();
            },
            error: function(jqXhr, textStatus, errorMessage) {
                // $('p').append('Error' + errorMessage);
            }
        });
    }
</script>
@endsection
