@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title">{{$title}}</h2>
                <div class="card shadow mb-4">
                    <div class="card-body">
                        {{-- {{ dd($patients->toArray()) }} --}}
                        <table id="registrants" class="table table-hover table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>{{__('id')}}</th>
                                    <th>{{__('name')}}</th>
                                    <th>{{__('mrn')}}</th>
                                    <th>{{__('email')}}</th>
                                    <th>{{__('added_on')}}</th>
                                    <th>{{__('actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($patients))
                                    @foreach ($patients as $patient)
                                        <tr>
                                            <td>
                                                {{ $patient->mrn }}
                                            </td>
                                            <td>
                                                {{ $patient->full_name }}
                                            </td>
                                            <td>
                                                @if (!empty($patient->mrns->toArray()))
                                                    <i class="fe fe-check fe-32 text-success"></i>
                                                @else 
                                                    <i class="fe fe-x fe-32 text-danger"></i>
                                                @endif
                                            </td>
                                            <td>{{ $patient->user->email }}</td>
                                            <td>{{ dbtoDate($patient->created_at, 'M-d-Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target=".modal-right-{{$patient->id}}">{{__('view')}}</button>
                                                    @if ($patient->user->is_active)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target=".modal-disapprove-{{$patient->id}}">{{__('disapprove')}}</button>
                                                    @else
                                                        {{Form::open(['url' => route('admin.registrants.toggle')])}}
                                                        {{Form::hidden('user_id', $patient->user->id)}}
                                                        {{Form::submit('Activate', ['class' => 'btn btn-sm btn-outline-success'])}}
                                                        {{Form::close()}}
                                                    @endif 
                                                </div>
                                                @include('admin.Users._patient-details', ['modalId' => $patient->id, 'patient_info' => $patient])
                                                @include('admin.Users._disapprove_registrants', ['modalId' => $patient->id, 'patient_info' => $patient])
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
$('#registrants').DataTable(
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