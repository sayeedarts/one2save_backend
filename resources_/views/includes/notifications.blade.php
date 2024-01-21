
<div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="defaultModalLabel">Notifications</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="list-group list-group-flush my-n3">
            {{-- @foreach (auth()->user()->unreadNotifications as $notification)
                <div class="list-group-item bg-transparent">
                    <div class="row align-items-center">
                        <div class="">
                            <div class="my-0 small font-weight-bold">{{$notification->data['data']}}</div>
                            <small class="my-0 badge badge-pill badge-light text-muted">
                                {{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach --}}
          </div> <!-- / .list-group -->
        </div>
        <div class="modal-footer">
          {!! Form::open(['url' => route('noti-mark-all-read')]) !!}
            <button type="submit" class="btn btn-secondary btn-block">Mark all read</button>
          {{ Form::close() }}
        </div>
      </div>
    </div>
</div>