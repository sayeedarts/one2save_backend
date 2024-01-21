@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="page-title"> {{$pageTitle}} List </h2>
                <p class="text-">In case you need any public location/URL of any file/Image/PDF, then you can upload that file here and get the corresponding Path for later usages. </p>
                <div class="card shadow mb-4">
                    <div class="card-header">
                        @can('add-file')
                            <a href="{{route('file-manager.create')}}" class="float-right btn btn-sm btn-primary">Add {{$pageTitle}}</a>
                        @endcan
                    </div>
                    <div class="card-body">
                        @include('includes.flash')
                        <table id="datatable" class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Added on</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $item)
                                <tr>
                                    <td> {{$item->id}} </td>
                                    <td>
                                        <img src="{{$item->file_url}}" class="img-fluid preview" style="max-width:200px" /> <br />
                                        <div class="input-group mb-3 my-3">
                                            <div class="input-group-prepend">
                                                <span class="btn btn-warning input-group-text" id="basic-addon1" onclick="copyText({{$item->id}})">Copy</span>
                                            </div>
                                            <input type="text" class="form-control" id="copy_text_{{$item->id}}" placeholder="URL" aria-label="Username" aria-describedby="basic-addon1" value="{{$item->file_url}}">
                                        </div>
                                    </td>
                                    <td> {{dbtoDate($item->created_at)}} </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('delete-file')
                                                <a href="{{ route('file-manager.delete', $item->id) }}" onclick="return confirm('Are you sure you want to remove this?')" class="btn btn-sm btn-danger"><span class="fe fe-16 fe-x-circle"></span></a>
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

<script>
    function copyText(elm) {
        /* Get the text field */
        var copyText = document.getElementById("copy_text_" + elm);
        alert(copyText.value)
        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */

        /* Copy the text inside the text field */
        navigator.clipboard.writeText(copyText.value);

        /* Alert the copied text */
        alert("Copied the text: " + copyText.value);
    }
</script>
@endsection