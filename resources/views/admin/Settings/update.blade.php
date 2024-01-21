@extends('layouts.app')

@section('content')
<div>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <h2 class="h3 mb-4 page-title">Settings</h2>
                <div class="my-4">
                    {{ Form::open(['url' => route('settings.store'), 'files' => true]) }}
                    {{ Form::hidden('id', $id ?? 0) }}
                    <div class="row mt-5 align-items-center">
                        <div class="col-md-3 text-center mb-5">
                            <div class="avatar avatar-xl">
                                <img src="{{ asset('public/uploads/profile/') }}/{{$settings['logo']}}" alt="Logo" class="avatar-img img-fluid">
                            </div>
                        </div>
                        <div class="col">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h4 class="mb-1">{{$company_name ?? ""}}</h4>
                                    <p class="small mb-3"><span class="badge badge-dark">{!! strip_tags($address) ?? "" !!}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    @include('includes.flash')
                    <div class="row">
                        <h4 class="mb-1">Basic Information</h4>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="name">Full name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{$name ?? ''}}" placeholder="Full name">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="lastname">Phone no.</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{$phone ?? ''}}" placeholder="eg. 999999999">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="lastname">Email</label>
                            <input type="email" name="email" class="form-control" value="{{$email ?? ''}}" id="inputEmail4" placeholder="example@hmh.com">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="inputEmail4">Company Email</label>
                                <input type="email" name="company_email" class="form-control" value="{{$company_email ?? ''}}" id="inputEmail4" placeholder="example@hmh.com">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="inputEmail4">VAT (%)</label>
                                <input type="test" name="vat" class="form-control" value="{{$vat ?? ''}}" id="inputEmail4" placeholder="VAT">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputCompany5">Company</label>
                            <input type="text" name="company_name" class="form-control" value="{{$company_name ?? ''}}" id="inputCompany5" placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputCompany5">Company Logo</label>
                            <input type="file" name="logo" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputCompany5">Android App Link</label>
                            <input type="text" name="android_app_link" class="form-control" value="{{$android_app_link ?? ''}}" id="inputCompany5" placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputCompany5">IOS App Link</label>
                            <input type="text" name="ios_app_link" class="form-control" value="{{$ios_app_link ?? ''}}" id="inputCompany5" placeholder="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputAddress5">Address </label>
                        <input type="text" name="address" class="form-control" value="{{ !empty(${'address'}) ? ${'address'} : ''}}" id="inputAddress5" placeholder="P.O. Box 464, 5975 Eget Avenue">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputCompany5">Copyright </label>
                            <input type="text" name="copyright" class="form-control" value="{{ !empty(${'copyright'}) ? ${'copyright'} : ''}}" id="inputCompany5" placeholder="">
                        </div>
                    </div>

                    {{-- Seo Fields --}}
                    <div class="row">
                        <h4 class="mb-1">Seo Configuration</h4>
                    </div>

                    <div class="form-row">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Seo Title </label>
                                    {{ Form::text('seo_title', !empty($seo_title) ? $seo_title : old('seo_title'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('seo_title') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Keywords </label>
                                    {{ Form::text('seo_keywords', !empty($seo_keywords) ? $seo_keywords : old('seo_keywords'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('seo_keywords') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="simpleinput">Description </label>
                                    {{ Form::textarea('seo_description', !empty($seo_description) ? $seo_description : old('seo_description'), ['class' => 'form-control', 'id' => 'simpleinput']) }}
                                    @error('seo_description') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <h4 class="mb-1">Social Links</h4>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputCompany5">Facebook</label>
                            <input type="text" name="facebook" class="form-control" value="{{$facebook ?? ''}}" id="inputCompany5" placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputCompany5">Instagram</label>
                            <input type="text" name="instagram" class="form-control" value="{{$instagram ?? ''}}" id="inputCompany5" placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputCompany5">Twitter</label>
                            <input type="text" name="twitter" class="form-control" value="{{$twitter ?? ''}}" id="inputCompany5" placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputCompany5">Youtube</label>
                            <input type="text" name="youtube" class="form-control" value="{{$youtube ?? ''}}" id="inputCompany5" placeholder="">
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="row">
                        <h4 class="mb-1">Change Password</h4>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputPassword4">Old Password</label>
                                <input type="password" name="old_password" class="form-control" value="" id="inputPassword5">
                            </div>
                            <div class="form-group">
                                <label for="inputPassword5">New Password</label>
                                <input type="password" name="password" class="form-control" value="" id="inputPassword5">
                            </div>
                            <div class="form-group">
                                <label for="inputPassword6">Confirm Password</label>
                                <input type="password" name="re_password" class="form-control" value="" id="inputPassword6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">Password requirements</p>
                            <p class="small text-muted mb-2"> To create a new password, you have to meet all of the following requirements: </p>
                            <ul class="small text-muted pl-4 mb-0">
                                <li> Minimum 8 character </li>
                                <li>At least one special character</li>
                                <li>At least one number</li>
                                <li>Can’t be the same as a previous password </li>
                            </ul>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Change</button>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.col-12 -->
        </div>
        <!-- .row -->
    </div>
    <!-- .container-fluid -->
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
                        <div class="list-group-item bg-transparent">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="fe fe-box fe-24"></span>
                                </div>
                                <div class="col">
                                    <small><strong>Package has uploaded successfull</strong></small>
                                    <div class="my-0 text-muted small">Package is zipped and uploaded</div>
                                    <small class="badge badge-pill badge-light text-muted">1m ago</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="fe fe-download fe-24"></span>
                                </div>
                                <div class="col">
                                    <small><strong>Widgets are updated successfull</strong></small>
                                    <div class="my-0 text-muted small">Just create new layout Index, form, table</div>
                                    <small class="badge badge-pill badge-light text-muted">2m ago</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="fe fe-inbox fe-24"></span>
                                </div>
                                <div class="col">
                                    <small><strong>Notifications have been sent</strong></small>
                                    <div class="my-0 text-muted small">Fusce dapibus, tellus ac cursus commodo</div>
                                    <small class="badge badge-pill badge-light text-muted">30m ago</small>
                                </div>
                            </div>
                            <!-- / .row -->
                        </div>
                        <div class="list-group-item bg-transparent">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="fe fe-link fe-24"></span>
                                </div>
                                <div class="col">
                                    <small><strong>Link was attached to menu</strong></small>
                                    <div class="my-0 text-muted small">New layout has been attached to the menu</div>
                                    <small class="badge badge-pill badge-light text-muted">1h ago</small>
                                </div>
                            </div>
                        </div>
                        <!-- / .row -->
                    </div>
                    <!-- / .list-group -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Clear All</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade modal-shortcut modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="defaultModalLabel">Shortcuts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5">
                    <div class="row align-items-center">
                        <div class="col-6 text-center">
                            <div class="squircle bg-success justify-content-center">
                                <i class="fe fe-cpu fe-32 align-self-center text-white"></i>
                            </div>
                            <p>Control area</p>
                        </div>
                        <div class="col-6 text-center">
                            <div class="squircle bg-primary justify-content-center">
                                <i class="fe fe-activity fe-32 align-self-center text-white"></i>
                            </div>
                            <p>Activity</p>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-6 text-center">
                            <div class="squircle bg-primary justify-content-center">
                                <i class="fe fe-droplet fe-32 align-self-center text-white"></i>
                            </div>
                            <p>Droplet</p>
                        </div>
                        <div class="col-6 text-center">
                            <div class="squircle bg-primary justify-content-center">
                                <i class="fe fe-upload-cloud fe-32 align-self-center text-white"></i>
                            </div>
                            <p>Upload</p>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-6 text-center">
                            <div class="squircle bg-primary justify-content-center">
                                <i class="fe fe-users fe-32 align-self-center text-white"></i>
                            </div>
                            <p>Users</p>
                        </div>
                        <div class="col-6 text-center">
                            <div class="squircle bg-primary justify-content-center">
                                <i class="fe fe-settings fe-32 align-self-center text-white"></i>
                            </div>
                            <p>Settings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection