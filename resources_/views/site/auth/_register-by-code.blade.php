
{{ Form::open(['url' => route('add.patient.bymrn'), 'autocomplete' => 'off']) }}
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group">
                <input type="text" class="form-control" name="mrn_number"
                    placeholder="Enter Your Medical File No (MRN) *" value="" />
                @error('mrn_number') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group">
                {{ Form::select('hospital_code', $hospitals, $hospital_code ?? '', ['placeholder' => __("hospitals") . " " . $required, 'class' => 'form-control']) }}
                @error('hospital_code') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group">
                {{ Form::select('national_id_type', $national_id_types, $national_id_type ?? '', ['placeholder' => __("national_id_type") . " " . $required, 'class' => 'form-control']) }}
                @error('national_id_type') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="form-group">
                <input type="text" name="national_id" value="{{old('national_id')}}" class="form-control" placeholder="{{__("national_id")}} {!! $required !!}">
                @error('national_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <button class="btn-primary btn-lg" type="submit">
                Submit <i class="fa fa-angle-double-right"></i>
            </button>
        </div>
    </div>
{{Form::close()}}
