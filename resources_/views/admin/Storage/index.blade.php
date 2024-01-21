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
                        @can('add-storage')
                            <a href="{{route('storage.create')}}" class="float-right btn btn-sm btn-primary">Add {{$pageTitle}}</a>
                        @endcan
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        <table id="datatable" class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Dimension</th>
                                    <th>Area</th>
                                    <th>Price</th>
                                    <th>Created on</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($storages as $item)
                                <tr>
                                    <td> {{$item->id}} </td>
                                    <td> {{$item->name}} </td>
                                    <td> {{$item->dimension}} </td>
                                    <td> {{$item->area}} </td>
                                    <td> {{$item->price}} </td>
                                    <td>  {{$item->created_at}} </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('edit-storage')
                                            <a href="{{route('storage.edit', $item->id)}}"
                                                class="btn btn-sm btn-primary"><span class="fe fe-16 fe-edit"></span></a>
                                            @endcan
                                            @can('delete-storage')
                                                <a href="{{ route('storage.delete', $item->id) }}"
                                                onclick="return confirm('Are you sure you want to remove this?')"
                                                class="btn btn-sm btn-danger"><span class="fe fe-16 fe-x-circle"></span></a>
                                            @endcan
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
<script src="{{asset('public/admin/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('public/admin/js/dataTables.bootstrap4.min.js')}}"></script>
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