@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="page-title">{{$title}}</h2>
            <p class="text-muted">You can add dynamic pages which will be shown on the Website's menu bar</p>
            <div class="card shadow mb-4">
                <div class="card-header">
                    <strong class="card-title">Dynamic Page List</strong>
                    @can('add-page')
                    <a href="{{route('page.create')}}" class="float-right btn btn-sm btn-primary">Add Dynamic Page</a>
                    @endcan
                </div>
                <div class="card-body">
                    @include('includes.flash')
                    <table id="dataTable" class="table table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <!-- <th>Slug</th> -->
                                <th>Added Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($pages))
                                @foreach ($pages as $page)
                                <tr>
                                    <td>{{$page->id}}</td>
                                    <td>
                                        {{$page->name}} <br />
                                        <div class="badge badge-success" title="{{$page->slug}}">
                                            {{limit_text($page->slug, 70)}}
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ !empty ($page->type) ? $types[$page->type] : ""}}</div>
                                        <div class="text-info small">
                                            @if ($page->is_default == 1)
                                            System Default
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($page->updated_at)->diffForHumans() }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('edit-page')
                                                <a href="{{ route('page.edit', $page->id) }}" class="btn btn-sm btn-primary"><span class="fe fe-16 fe-edit"></span></a>
                                            @endcan
                                            @can('delete-page')
                                                @if($page->is_default == 0)
                                                    <a href="{{ route('page.delete', $page->id) }}" onclick="return confirm('are you usre to delete this item ?')" class="btn btn-sm btn-danger"><span class="fe fe-16 fe-x-circle"></span></a>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
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
    $('#dataTable').DataTable({
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