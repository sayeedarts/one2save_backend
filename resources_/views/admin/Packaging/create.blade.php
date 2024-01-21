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
                        <a href="{{route('packaging.list')}}" class="float-right btn btn-sm btn-primary"> {{$pageTitle}} List</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route('packaging.store'), 'id' => 'quillForm', 'files' => true]) !!}
                        <input type="hidden" name="id" value="{{$id ?? old('id')}}">
                        <div class="row">
                            <div class="col-sm-6">

                                <div class="form-group mb-3">
                                    <label for="simpleinput">Name {!! $required !!}</label>
                                    {{ Form::text('name', !empty($name) ? $name : old('name'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Category {!! $required !!}</label>
                                    {{ Form::select('category_id', $categories, !empty($category_id) ? $category_id : old('category_id'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Price {!! $required !!}</label>
                                    {{ Form::text('price', !empty($price) ? $price : old('price'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label for="simpleinput">Add Images </label>
                                            {{ Form::file('pictures[]', ['class' => 'form-control', 'id' => 'simpleinput', 'multiple']) }}
                                        </div>
                                        <div class="col-sm-4">
                                            <div>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                                                    Gallery
                                                </button>
                                                @isset($image)
                                                <img class="align-top img-thumbnail" src="{{show($image, 'service_image', '280x200')}}" alt="icon">
                                                @endisset
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- /.col -->
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Dimension (W x D x H) in ft. {!! $required !!}</label>
                                    {{ Form::text('dimension', !empty($dimension) ? $dimension : old('dimension'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('dimension') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Short Description {!! $required !!}</label>
                                    {{ Form::textarea('short_description', !empty($short_description) ? $short_description : old('short_description'), ['class' => 'form-control', 'id' => 'simpleinput', 'rows' => 5]) }}
                                    @error('short_description') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                            </div>
                            <div class="col-sm-12 mb-5">
                            <label for="simpleinput">Long Description {!! $required !!}</label>
                                <div id="editor">
                                    @if (!empty($description))
                                        {!! $description !!}
                                    @else
                                        <p></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-12">
                                {{Form::textarea('description', !empty($description) ? $description : "", ['class' => 'getFullText d-none'])}}
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
                        <div class="col-sm-3 mb-3 storage-img-{{$image['id']}}">
                            <div class="card p-3">
                                <img src="{{ show($image['image'], 'packaging', '600x600') }}" height="150" width="150"/>
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
<link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href="//cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
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


        // $("#quillForm").on("submit", function(e) {
        //     e.preventDefault();
        //     var content = $("#editor").html();
        //     console.log(content);
        //     // $("#hiddenArea").val($("#editor").html());

        //     return false;
        // })
    });

    function removeRow() {
        console.log("Called");
        $(".remove-item").on('click', function() {
            var getDeleteId = $(this).attr('data-id');
            $(".item_" + getDeleteId).remove();
        });
    }


    // editor
    var editor = document.getElementById("editor");
    if (editor) {
        var toolbarOptions = [
            [{
                font: [],
            }, ],
            [{
                header: [1, 2, 3, 4, 5, 6, false],
            }, ],
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{
                    header: 1,
                },
                {
                    header: 2,
                },
            ],
            [{
                    list: "ordered",
                },
                {
                    list: "bullet",
                },
            ],
            [{
                    script: "sub",
                },
                {
                    script: "super",
                },
            ],
            [{
                    indent: "-1",
                },
                {
                    indent: "+1",
                },
            ], // outdent/indent
            [{
                    color: [],
                },
                {
                    background: [],
                },
            ], // dropdown with defaults from theme
            [{
                align: [],
            }, ],
            ["clean"], // remove formatting button
        ];
        var quill = new Quill(editor, {
            modules: {
                toolbar: toolbarOptions,
            },
            theme: "snow",
        });
        quill.on('text-change', function(delta, oldDelta, source) {
            if (source == 'user') {
                var content = $("#editor").html();
                $(".getFullText").val(content);
            }
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