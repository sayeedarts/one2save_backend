<div>
    <!-- Modal -->
    <div class="modal fade" id="notifyUser{{$id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Send Notification to User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ Form::open(['url' => route('quotes.notify')]) }}
                    {{ Form::hidden('id', $id) }}
                    <div class="form-group mb-3">
                        <label for="example-textarea">Compose a Message</label>
                        {{Form::textarea('message', old('$message'), ['class' => 'form-control', 'row' => 4])}}
                        <small>This will notify user by Email ({{$email}}) and SMS ({{$mobile}})</small>
                    </div>
                    <div></div>
                    {{ Form::submit('Notify User', ['class' => 'btn btn-sm btn-success']) }}
                    {{ Form::close() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>