@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="page-title">{{$title}}</h2>
            <p class="text-muted">You can manage user's basic information from this page.</p>
            <div class="card shadow mb-4">
                <div class="card-header">
                    <strong class="card-title">{{$title}}</strong>
                    <a href="{{route('role.create')}}" class="float-right btn btn-sm btn-primary">Add new Role</a>
                </div>
                <div class="card-body">
                    @include('includes.flash')
                    <table id="dataTable" class="table table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Added on</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($roles))
                                @foreach ($roles as $role)
                                <tr>
                                    <td>{{$role->id}}</td>
                                    <td>
                                        {{$role->name}} <br />
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($role->updated_at)->diffForHumans() }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('role.edit', $role->id) }}" class="btn btn-sm btn-primary"><span class="fe fe-16 fe-edit"></span></a>
                                            @if($role->name != "admin")
                                            @endif
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
