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
                    <a href="{{route('user.create')}}" class="float-right btn btn-sm btn-primary">Add new User</a>
                </div>
                <div class="card-body">
                    @include('includes.flash')
                    <table id="dataTable" class="table table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Role</th>
                                <th>Added on</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($users))
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>
                                        {{$user->name}} <br />
                                    </td>
                                    <td>
                                        {{$user->email}}
                                    </td>
                                    <td>
                                        {{$user->mobile}}
                                    </td>
                                    <td>
                                        @if (!empty($user->roles[0]))
                                            {{$user->roles[0]->name}}
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($user->updated_at)->diffForHumans() }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(!empty($user->roles[0]->name) && $user->roles[0]->name == "admin")
                                                <span>!</span>
                                            @else 
                                                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-primary"><span class="fe fe-16 fe-edit"></span></a>
                                                <a href="{{ route('user.delete', $user->id) }}" onclick="return confirm('are you usre to delete this item ?')" class="btn btn-sm btn-danger"><span class="fe fe-16 fe-x-circle"></span></a>
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