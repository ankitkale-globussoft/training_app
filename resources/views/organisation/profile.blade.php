@extends('layouts.master', ['panel' => 'organisation'])
@section('title', 'Organisation Profile')

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">

            <div class="card">
                <div class="card-body">

                    <div class="app-brand justify-content-center mb-4">
                        <span class="app-brand-text demo text-body fw-bolder">
                            Organisation Profile
                        </span>
                    </div>

                    <form id="orgUpdateForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Organisation Name -->
                        <div class="mb-3">
                            <label class="form-label">Organisation Name</label>
                            <input type="text" id="name" name="name"
                                   class="form-control"
                                   value="{{ $org->name }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Designation -->
                        <div class="mb-3">
                            <label class="form-label">Representative Designation</label>
                            <input type="text" id="rep_designation" name="rep_designation"
                                   class="form-control"
                                   value="{{ $org->rep_designation }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                   class="form-control"
                                   value="{{ $org->email }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Mobile -->
                        <div class="mb-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" id="mobile" name="mobile"
                                   class="form-control"
                                   value="{{ $org->mobile }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Alternate Mobile -->
                        <div class="mb-3">
                            <label class="form-label">Alternate Mobile</label>
                            <input type="text" id="alt_mobile" name="alt_mobile"
                                   class="form-control"
                                   value="{{ $org->alt_mobile }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Organisation Logo -->
                        <div class="mb-3">
                            <label class="form-label">Organisation Logo</label>
                            <input type="file" id="org_image" name="org_image"
                                   class="form-control" accept="image/*">
                            <div class="invalid-feedback"></div>

                            @if($org->org_image)
                                <div class="mt-3" style="max-width:150px;">
                                    <img src="{{ asset('storage/'.$org->org_image) }}"
                                         class="img-thumbnail w-100">
                                </div>
                            @endif
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label class="form-label">Address Line 1</label>
                            <input type="text" id="addr_line1" name="addr_line1"
                                   class="form-control"
                                   value="{{ $org->addr_line1 }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" id="addr_line2" name="addr_line2"
                                   class="form-control"
                                   value="{{ $org->addr_line2 }}">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" id="city" name="city"
                                       class="form-control"
                                       value="{{ $org->city }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">District</label>
                                <input type="text" id="district" name="district"
                                       class="form-control"
                                       value="{{ $org->district }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">State</label>
                                <input type="text" id="state" name="state"
                                       class="form-control"
                                       value="{{ $org->state }}">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" id="pincode" name="pincode"
                                       class="form-control"
                                       value="{{ $org->pincode }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <hr>

                        <!-- Update Password -->
                        <h5 class="mb-3">Change Password (Optional)</h5>

                        <div class="mb-3">
                            <label class="form-label">Old Password</label>
                            <input type="password" id="old_password"
                                   name="old_password"
                                   class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" id="password"
                                   name="password"
                                   class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>

                        <button class="btn btn-primary d-grid w-100" id="updateBtn">
                            Update Profile
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@push('ajax')
<script>
$('#orgUpdateForm').on('submit', function(e) {
    e.preventDefault();
    clearErrors();

    let btn = $('#updateBtn');
    btn.prop('disabled', true).text('Updating...');

    let formData = new FormData(this);

    $.ajax({
        url: "{{ route('org.profile.update') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            location.reload();
        },
        error: function(xhr) {
            btn.prop('disabled', false).text('Update Profile');
            if (xhr.status === 422) {
                showErrors(xhr.responseJSON.errors);
            }
        }
    });
});

function clearErrors() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

function showErrors(errors) {
    $.each(errors, function(field, messages) {
        let input = $('#' + field);
        input.addClass('is-invalid');
        input.next('.invalid-feedback').text(messages[0]);
    });
}
</script>
@endpush
