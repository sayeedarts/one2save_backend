@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title">Manage Language</h2>
                <p class="text-muted">Demo for form control styles, layout options, and custom components for creating a
                    wide variety of forms.</p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <strong class="card-title">Edit language</strong>
                        <a href="{{route('language.index')}}" class="float-right btn btn-sm btn-primary">All languages</a>
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        {{-- {{dd($careers)}} --}}
                        {{ Form::open(['url' => route('langauge.content.update'), 'files' => true]) }}
                        {{Form::hidden('short_form', $short_form)}}
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="30%">Key</th>
                                    <th width="70%">Label</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contents as $key => $value)
                                <tr>
                                    <td>{{$key}}</td>
                                    <td>
                                        {{Form::text($key, $value, ['class' => 'form-control'])}}
                                        {{-- {{ $value }} --}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-12">
                                {{Form::submit('Save Changes', ['class' => 'btn btn-primary'])}}
                                {{Form::close()}}
                            </div>
                        </div>
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
    $('#dataTable-1').DataTable(
    {
        autoWidth: true,
        "order": [[ 0, 'desc' ]],
        "lengthMenu": [
            [16, 32, 64, -1],
            [16, 32, 64, "All"]
        ]
    });
    </script>
@endsection