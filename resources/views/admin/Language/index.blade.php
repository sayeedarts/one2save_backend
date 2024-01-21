@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title">Manage Langauges</h2>
                <p class="text-muted">Demo for form control styles, layout options, and custom components for creating a
                    wide variety of forms.</p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title">All Languages</strong>
                        {{-- <a href="{{route('career.create')}}" class="float-right btn btn-sm btn-primary">Add Jobs</a> --}}
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {{-- {{dd($careers)}} --}}
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Langauge Name</th>
                                    <th>Short Name</th>
                                    <th>Is Enabled</th>
                                    <th>Is Default</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($languages as $language)
                                <tr>
                                    <td>{{$language->id}}</td>
                                    <td>{{ $language->name }}</td>
                                    <td>{{ $language->short_form }}</td>
                                    <td>{{ $language->status == 1 ? "Yes" : "No" }}</td>
                                    <td>{{ $language->is_default == 1 ? "Yes" : "No" }}</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <a href="{{ route('langauge.edit.params', $language->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            {{-- <a href="{{route('career.delete', $career->id)}}" onclick="return confirm('Are you sure you want to remove this?')" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                            <button type="button" class="btn btn-sm btn-info">
                                                <i class="fa fa-edit"></i> More
                                            </button> --}}
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
    <link rel="stylesheet" href="{{ asset('admin/plugins/trix/trix.css') }}">
    <script src='{{ asset('admin/plugins/trix/trix.js') }}'></script>
    <script src='{{ asset('admin/js/jquery.timepicker.js') }}'></script>
    <script src='{{ asset('admin/js/select2.min.js') }}'></script>
    <script>
    $('.select2').select2({
        theme: 'bootstrap4',
    });
    $('.time-input').timepicker({
        'scrollDefault': 'now',
        'zindex': '9999' /* fix modal open */
    });
    </script>
@endsection