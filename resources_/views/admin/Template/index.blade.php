@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title"> {{$pageTitle}} List </h2>
                <p class="text-muted">Demo for form control styles, layout options, and custom components for creating a
                    wide variety of forms.</p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <a href="{{route('template.create')}}" class="float-right btn btn-sm btn-primary">Add {{$pageTitle}}</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        <table id="datatable" class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Quote Visibility</th>
                                    <th>Featured</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($templates as $item)
                                <tr>
                                    <td> {{$item->id}} </td>
                                    <td> 
                                        {{$item->title}} <br>
                                        <span class="badge badge-info pt-2">
                                            {{$item->slug}}
                                        </span>
                                    </td>
                                    <td> 
                                        @if ($item->is_blog == 1) 
                                        <span class="badge badge-warning pt-2">Only Page</span>
                                        @else
                                        <span class="badge badge-default pt-2">Available for Quote</span>
                                        @endif
                                    </td>
                                    <td> @if ($item->featured == 'yes') {{"Yes"}} @else <span class="text-danger">{{"No"}}</span> @endif </td>
                                    <td> 
                                        {{ \Carbon\Carbon::parse($item->updated_at)->diffForHumans() }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{route("template.edit", $item->id)}}" class="btn btn-sm btn-primary"><span class="fe fe-16 fe-edit"></span></a>
                                            <a href="{{ route('template.destroy', $item->id) }}" onclick="return confirm('Are you sure you want to remove this?')" class="btn btn-sm btn-danger"><span class="fe fe-16 fe-x-circle"></span></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> <!-- / .card -->
            </div> <!-- .col-12 -->
        </div> <!-- .row -->
    </div> <!-- .container-fluid -->
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="{{asset('public/admin/css/dataTables.bootstrap4.css')}}">
<script src='{{asset('public/admin/js/jquery.dataTables.min.js')}}'></script>
<script src='{{asset('public/admin/js/dataTables.bootstrap4.min.js')}}'></script>
<script>
    $('#datatable').DataTable({
        autoWidth: true,
        "order": [
            [0, 'desc']
        ],
        "lengthMenu": [
            [16, 32, 64, -1],
            [16, 32, 64, "All"]
        ]
    });
</script>
@endsection