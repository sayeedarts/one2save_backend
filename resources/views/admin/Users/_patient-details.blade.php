 <!-- Slide Modal -->
 <div class="modal fade modal-right-{{$modalId}} modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="defaultModalLabel">{{__('details')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"> 
                <h4 class="mt-3 mb-3">Basic Details
                    @if (!empty($patient_info->rejection_reason))
                        <span class="badge badge-danger">{{__("disapproved")}}</span>
                    @endif
                </h4>
                <table class="table table-bordered table-hover mb-0">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <td>{{$patient_info->full_name}}</td>
                        </tr>
                        <tr>
                            <th>Patient ID</th>
                            <td>{{$patient_info->mrn}}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{$patient_info->user->email}}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{$patient_info->phone}}</td>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <td>{{$patient_info->gender_info->name}}</td>
                        </tr>
                        <tr>
                            <th>Nationality</th>
                            <td>{{$patient_info->nationality_info->name}}</td>
                        </tr>
                        <tr>
                            <th>Religion</th>
                            <td>{{$patient_info->religion_info->name}}</td>
                        </tr>
                        <tr>
                            <th>National ID</th>
                            <td>
                                {{$patient_info->nationalid_type->name}}: {{$patient_info->national_id}}
                            </td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td>{{dbtoDate($patient_info->dob)}}</td>
                        </tr>
                        <tr>
                            <th>Added on</th>
                            <td>{{dbtoDate($patient_info->created_at)}}</td>
                        </tr>
                        @if (!empty($patient_info->rejection_reason))
                        <tr>
                            <th>dis-approved for</th>
                            <td>{{$patient_info->rejection_reason}}</td>
                        </tr>
                        @endif 
                    </tbody>
                </table>
                <h4 class="mt-3 mb-3">{{__('mrn_respective_to_hospitals')}}</h4>
                <table class="table table-bordered table-hover mb-0">
                    <tbody>
                        @if (!empty($patient->mrns->count()))
                            @foreach ($patient->mrns as $mrnDetail)
                            <tr>
                                <th>{{$mrnDetail->hospital->name_en}}</th>
                                <td>{{$mrnDetail->mrn}}</td>
                            </tr>
                            @endforeach
                        @else 
                        <tr>
                            <td colspan="2">{{__('no_data_exists')}}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
            </div> --}}
        </div>
    </div>
</div>