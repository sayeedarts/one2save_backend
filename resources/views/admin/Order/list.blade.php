@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <h2 class="page-title">{{$title}}</h2>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <table id="registrants" class="table table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Order Type</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Price</th>
                                <th>Payment Status</th>
                                <th>Payment Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($orders))
                            @foreach ($orders as $order)
                            <tr>
                                <td>
                                    {{ $order->order_number }}
                                </td>
                                <td>
                                    @if ($order->module == 'packing')
                                        Packing Material
                                    @else 
                                        Storage Space
                                    @endif
                                </td>
                                <td>
                                    {{ $order->buyer->name}}
                                </td>
                                <td>
                                    {{ $order->buyer->email}}
                                </td>
                                <td>
                                {{ $order->price_total}} {{ $order->price_currency}}
                                </td>
                                <td>
                                    @if ($order->payment_status == "COMPLETED")
                                        Success
                                    @else 
                                        Pending
                                    @endif
                                </td>
                                <td>
                                    {{ $order->created_at }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle more-vertical" type="button" id="dr1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted sr-only">Action</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dr1">
                                            @can('view-order-invoice')
                                                <a class="dropdown-item" target="_blank" href="{{route('order.invoice', $order->id)}}">View Invoice</a>
                                            @endcan

                                            {{-- <a class="dropdown-item" href="#">Notify User</a> --}}
                                        </div>
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