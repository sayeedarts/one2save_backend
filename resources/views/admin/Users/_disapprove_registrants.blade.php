 <!-- Slide Modal -->
 <div class="modal fade modal-disapprove-{{$modalId}} modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="defaultModalLabel">You are about to dis-approve: {{ $patient->full_name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"> 
                <h4 class="mt-3 mb-3">Define the dis-approval Reason</h4>
                {{Form::open(['url' => route('admin.registrants.toggle')])}}
                {{Form::hidden('user_id', $patient->user->id)}}
                {{Form::textarea('reason', '', ['class' => 'form-control'])}}
                <div class="row">
                    <div class="col-12 mt-3">
                        {{Form::submit(__("disapprove"), ['class' => 'btn btn-sm ml-1 btn-outline-danger'])}}
                    </div>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
</div>