@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title"> {{$pageTitle}} </h2>
                <p class="text-muted">Demo for form control styles, layout options, and custom components for creating a
                    wide variety of forms.</p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title">Listing</strong>
                        @can('add-service-category')
                            <a href="{{route('service-categories.create')}}" class="float-right btn btn-sm btn-primary">Add {{$pageTitle}}</a>
                        @endcan
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        <table id="datatable" class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Service Name</th>
                                    <th>Total Items</th>
                                    <th>Created on</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service_category as $item)
                                <tr>
                                    <td> {{$item->id}} </td>
                                    <td>
                                        {{$item->title}}<br />
                                        <div class="badge badge-info pt-2 my-2">{{$item->slug}}</div>
                                        <br />
                                        @if ($item->display_type == 'quotation')
                                        <div class="badge badge-success pt-2">Only Quotation</div>
                                        @elseif($item->display_type == 'page')
                                        <div class="badge badge-info pt-2">Only Page</div>
                                        @else 
                                        <div class="badge badge-warning pt-2">Both page & Quotation</div>
                                        @endif
                                    </td>
                                    <td> {{$item->service->title ?? ""}} </td>
                                    <td> {{$item->category_item_count}} </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->updated_at)->diffForHumans() }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('edit-service-category')
                                                <a href="{{route('service-categories.edit', $item->id)}}" class="btn btn-sm btn-primary"><span class="fe fe-16 fe-edit"></span></a>
                                            @endcan 
                                            @can('delete-service-category')
                                                <a href="{{ route('service-categories.delete', $item->id) }}" onclick="return confirm('Are you sure you want to remove this?')" class="btn btn-sm btn-danger"><span class="fe fe-16 fe-x-circle"></span></a>
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
