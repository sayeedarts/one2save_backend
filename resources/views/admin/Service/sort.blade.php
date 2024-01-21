@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title"> Sort {{$pageTitle}} </h2>
                <p class="text-muted">Demo for form control styles, layout options, and custom components for creating a
                    wide variety of forms.</p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <a href="{{route('service.list')}}" class="float-right btn btn-sm btn-primary">
                            {{$pageTitle}} List
                        </a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {!! Form::open(['url' => route('service.sort.save')]) !!}
                        <div class="row">
                            <div class="col-12">
                                <ol class='draggable'>
                                    @foreach($services as $service)
                                    <li class="my-2">
                                        <input type="hidden" name="id[]" value="{{$service->id}}">
                                        {{$service->title}} 
                                        @if ($service->is_blog == 1) 
                                            <span class="badge badge-warning my-2">Blog Page</span>
                                        @else 
                                            <span class="badge badge-success my-2">Quotation</span>
                                        @endif  
                                    </li>
                                    @endforeach
                                </ol>
                            </div>
                            <div class="col-12 text-center">
                                <input type="submit" class="btn btn-primary btn-md" value="Update Sorting">
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
<style>
    body.dragging,
    body.dragging * {
        cursor: move !important;
    }

    .dragged {
        position: absolute;
        opacity: 0.5;
        z-index: 2000;
    }

    ol.example li.placeholder {
        position: relative;
        /** More li styles **/
    }

    ol.example li.placeholder:before {
        position: absolute;
        /** Define arrowhead **/
    }

    ol.draggable li {
        cursor: move;
        background: #e1dede;
        padding: 5px 10px;
        border-radius: 2px;
        border: 1px solid #cdcdcd;
    }
</style>
<script src='{{asset("public/js/jquery-sortable.js")}}'></script>
<script>
    $(function() {
        $("ol.draggable").sortable();
    });
</script>
@endsection