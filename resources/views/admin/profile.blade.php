@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Admin Profile')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Profile
    </h4>

    <div class="row">
        <div class="col-md-12">

            <div class="card mb-4">
                <h5 class="card-header">Profile Details</h5>

                <div class="card-body">
                    <div id="alertBox"></div>

                    <form id="profileForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Profile Image -->
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img
                                src="{{ auth()->user()->profile_pic ? asset('storage/' . auth()->user()->profile_pic) : asset('assets/img/avatars/1.png') }}"
                                alt="user-avatar"
                                class="d-block rounded"
                                height="100"
                                width="100"
                                id="profilePreview"
                            />

                            <div class="button-wrapper">
                                <label for="profile_pic" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new photo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" id="profile_pic" name="profile_pic" class="account-file-input" hidden>
                                    <span class="text-danger error-text profile_pic_error"></span>
                                </label>

                                <p class="text-muted mb-0">Allowed JPG, PNG. Max size 2MB</p>
                            </div>
                        </div>

                        <hr class="my-4" />

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Name</label>
                                <input class="form-control" type="text" name="name" value="{{ auth()->user()->name }}">
                                <span class="text-danger error-text name_error"></span>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="email" value="{{ auth()->user()->email }}">
                                <span class="text-danger error-text email_error"></span>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Phone</label>
                                <input class="form-control" type="text" name="phone" value="{{ auth()->user()->phone }}">
                                <span class="text-danger error-text phone_error"></span>
                            </div>
                        </div>

                        <hr class="my-4" />

                        <h6 class="mb-3">Change Password</h6>

                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label class="form-label">Old Password</label>
                                <input class="form-control" type="password" name="old_password">
                                <span class="text-danger error-text old_password_error"></span>
                            </div>

                            <div class="mb-3 col-md-4">
                                <label class="form-label">New Password</label>
                                <input class="form-control" type="password" name="password">
                                <span class="text-danger error-text password_error"></span>
                            </div>

                            <div class="mb-3 col-md-4">
                                <label class="form-label">Confirm Password</label>
                                <input class="form-control" type="password" name="password_confirmation">
                                <span class="text-danger error-text password_confirmation_error"></span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('ajax')
<script>
$(function () {

    // Image preview
    $('#profile_pic').on('change', function () {
        let reader = new FileReader();
        reader.onload = (e) => {
            $('#profilePreview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });

    $('#profileForm').submit(function (e) {
        e.preventDefault();

        $('.error-text').text('');
        $('#alertBox').html('');

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.profile.update') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function () {
                $('#alertBox').html(`
                    <div class="alert alert-info">
                        Updating profile...
                    </div>
                `);
            },

            success: function (res) {
                $('#alertBox').html(`
                    <div class="alert alert-success alert-dismissible fade show">
                        ${res.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
            },

            error: function (xhr) {
                if (xhr.status === 422) {

                    // Validation errors
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function (key, value) {
                        $('.' + key + '_error').text(value[0]);
                    });

                    $('#alertBox').html(`
                        <div class="alert alert-danger">
                            Please fix the errors below.
                        </div>
                    `);
                } else {
                    $('#alertBox').html(`
                        <div class="alert alert-danger">
                            Something went wrong. Try again!
                        </div>
                    `);
                }
            }
        });
    });

});
</script>
@endpush

