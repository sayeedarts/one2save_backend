@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="page-title">{{$title}}</h2>
            <div class="card shadow mb-4">
                <div class="card-body">
                @include('includes.flash')
                    {{-- {{ dd($patients->toArray()) }} --}}
                    <table id="registrants" class="table table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Source</th>
                                <th>Destination</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($quotes))
                            @foreach ($quotes as $quote)
                            <tr>
                                <td>
                                    {{ $quote->id }}
                                </td>
                                <td>
                                    {{ $quote->from_location}}
                                </td>
                                <td>
                                    {{ $quote->to_location}}
                                </td>
                                <td>
                                    {{ $quote->fullname }}
                                </td>
                                <td>
                                    {{ $quote->email }}
                                </td>
                                <td>
                                    {{ $quote->mobile }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle more-vertical" type="button" id="dr1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted sr-only">Action</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dr1">
                                            <a 
                                                class="dropdown-item" 
                                                target="_blank" 
                                                href="{{env('APP_URL')}}service/{{$quote->id}}/quote-generate?mode=stream">View More</a>
                                            @can('notify-quote')
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#notifyUser{{$quote->id}}">Notify User</a>
                                            @endcan
                                        </div>
                                    </div>
                                    <x-send-quote-alert :id="$quote->id" :email="$quote->email" :mobile="$quote->mobile"/>
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
<script src="https://raw.githubusercontent.com/jaz303/tipsy/master/src/javascripts/jquery.tipsy.js"></script>
<script>
    // $('.tooltip').tipsy();
</script>

<link rel="stylesheet" href="{{asset('public/admin/css/dataTables.bootstrap4.css')}}">
<script src='{{asset('public/admin/js/jquery.dataTables.min.js')}}'></script>
<script src='{{asset('public/admin/js/dataTables.bootstrap4.min.js')}}'></script>
<script>
    $('#registrants').DataTable({
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
