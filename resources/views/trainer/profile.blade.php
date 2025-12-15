@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Trainer Profile')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Trainer /</span> Profile
    </h4>

    <div id="alertBox"></div>

    <div class="card">
        <h5 class="card-header">Profile Information</h5>

        <div class="card-body">
            <form id="trainerProfileForm" enctype="multipart/form-data">
                @csrf

                {{-- Profile Image --}}
                <div class="d-flex align-items-start gap-4 mb-4">
                    <img
                        src="{{ Auth::guard('trainer_web')->user()->profile_pic ? asset('storage/'.Auth::guard('trainer_web')->user()->profile_pic) : asset('assets/img/avatars/1.png') }}"
                        class="rounded"
                        width="100"
                        id="profilePreview"
                    >

                    <div>
                        <input type="file" name="profile_pic" id="profile_pic" class="form-control mb-2">
                        @if(Auth::guard('trainer_web')->user()->profile_pic)
                            <a href="{{ asset('storage/'.Auth::guard('trainer_web')->user()->profile_pic) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                View Image
                            </a>
                        @endif
                        <span class="text-danger error-text profile_pic_error"></span>
                    </div>
                </div>

                <hr>

                {{-- Basic Info --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input class="form-control" name="name" value="{{ Auth::guard('trainer_web')->user()->name }}">
                        <span class="text-danger error-text name_error"></span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input class="form-control" name="email" value="{{ Auth::guard('trainer_web')->user()->email }}">
                        <span class="text-danger error-text email_error"></span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Phone</label>
                        <input class="form-control" name="phone" value="{{ Auth::guard('trainer_web')->user()->phone }}">
                        <span class="text-danger error-text phone_error"></span>
                    </div>
                </div>

                <hr>

                {{-- Address --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Address Line 1</label>
                        <input class="form-control" name="addr_line1" value="{{ Auth::guard('trainer_web')->user()->addr_line1 }}">
                        <span class="text-danger error-text addr_line1_error"></span>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Address Line 2</label>
                        <input class="form-control" name="addr_line2" value="{{ Auth::guard('trainer_web')->user()->addr_line2 }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>City</label>
                        <input class="form-control" name="city" value="{{ Auth::guard('trainer_web')->user()->city }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>District</label>
                        <input class="form-control" name="district" value="{{ Auth::guard('trainer_web')->user()->district }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>State</label>
                        <input class="form-control" name="state" value="{{ Auth::guard('trainer_web')->user()->state }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Pincode</label>
                        <input class="form-control" name="pincode" value="{{ Auth::guard('trainer_web')->user()->pincode }}">
                    </div>
                </div>

                <hr>

                {{-- Professional --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Resume Link</label>
                        <input class="form-control" name="resume_link" value="{{ Auth::guard('trainer_web')->user()->resume_link }}">
                        <a href="{{ Auth::guard('trainer_web')->user()->resume_link }}" target="_blank"
                           class="btn btn-sm btn-outline-primary mt-1">View</a>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Signed Form (PDF)</label>
                        <input type="file" name="signed_form_pdf" class="form-control">
                        @if(Auth::guard('trainer_web')->user()->signed_form_pdf)
                            <a href="{{ asset('storage/'.Auth::guard('trainer_web')->user()->signed_form_pdf) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary mt-1">
                                View PDF
                            </a>
                        @endif
                        <span class="text-danger error-text signed_form_pdf_error"></span>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Biodata</label>
                        <textarea class="form-control" name="biodata">{{ Auth::guard('trainer_web')->user()->biodata }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Achievements</label>
                        <textarea class="form-control" name="achievements">{{ Auth::guard('trainer_web')->user()->achievements }}</textarea>
                    </div>
                </div>

                <hr>

                {{-- Preferences --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>For Organization</label>
                        <select name="for_org_type" class="form-select">
                            @foreach(['school','corporate','both'] as $opt)
                                <option value="{{ $opt }}" @selected(Auth::guard('trainer_web')->user()->for_org_type==$opt)>
                                    {{ ucfirst($opt) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Training Mode</label>
                        <select name="training_mode" class="form-select">
                            @foreach(['online','offline','both'] as $opt)
                                <option value="{{ $opt }}" @selected(Auth::guard('trainer_web')->user()->training_mode==$opt)>
                                    {{ ucfirst($opt) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Availability</label>
                        <input class="form-control" name="availability"
                               value="{{ Auth::guard('trainer_web')->user()->availability }}">
                    </div>
                </div>

                <hr>

                {{-- Password --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Old Password</label>
                        <input type="password" class="form-control" name="old_password">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation">
                    </div>
                </div>

                <button class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('ajax')
<script>
$('#profile_pic').change(function () {
    let reader = new FileReader();
    reader.onload = e => $('#profilePreview').attr('src', e.target.result);
    reader.readAsDataURL(this.files[0]);
});

$('#trainerProfileForm').submit(function(e){
    e.preventDefault();
    $('.error-text').text('');
    $('#alertBox').html('');

    $.ajax({
        url: "{{ route('trainer.profile.update') }}",
        method: "POST",
        data: new FormData(this),
        processData:false,
        contentType:false,

        success: res => {
            $('#alertBox').html(`
                <div class="alert alert-success alert-dismissible fade show">
                    ${res.message}
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
        },

        error: xhr => {
            if(xhr.status === 422){
                $.each(xhr.responseJSON.errors, (k,v)=>{
                    $('.'+k+'_error').text(v[0]);
                });
                $('#alertBox').html(`<div class="alert alert-danger">Fix errors below</div>`);
            }
        }
    });
});
</script>
@endpush
