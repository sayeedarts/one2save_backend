<div>
    <div class="row">
        <div class="col-md-4">&nbsp;</div>
        <div class="col-md-4 text-center">
            <img src="{{asset('public/vectors/empty-record.png')}}" alt="no-found" width="100%" class="img-responsive">
            <div class="alert alert-{{$type ?? "info"}}">{{ $title ?? "Page Title" }}</div>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
</div>