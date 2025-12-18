@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body">

                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="javascript:void(0)" class="app-brand-link gap-2">
                            <span class="app-brand-text demo text-body fw-bolder">{{ config('app.name') }}</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <h4 class="mb-2 text-center">Reset Password 🔑</h4>
                    <p class="mb-4 text-center">Create a new password</p>

                    <form id="resetForm">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label">New Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control" name="password" placeholder="••••••••">
                                <span class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <small class="text-danger d-block mt-1" id="error-password"></small>
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control" name="password_confirmation" placeholder="••••••••">
                                <span class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <small class="text-danger d-block mt-1" id="error-password_confirmation"></small>
                        </div>

                        <button class="btn btn-primary d-grid w-100" id="resetBtn">
                            Reset Password
                        </button>
                    </form>

                    <div id="resetSuccess" class="alert alert-success d-none mt-3"></div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('ajax')
<script>
$(function () {
    $('#resetForm').on('submit', function (e) {
        e.preventDefault();

        $('#error-password').text('');
        $('#error-password_confirmation').text('');

        $('#resetBtn').html('<span class="spinner-border spinner-border-sm"></span> Updating...')
            .prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.password.update') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                $('#resetBtn').html('Reset Password').prop('disabled', false);
                $('#resetSuccess').removeClass('d-none').text(res.msg);

                if (res.redirect) {
                    setTimeout(() => {
                        window.location.href = res.redirect;
                    }, 1500);
                }
            },
            error: function (xhr) {
                $('#resetBtn').html('Reset Password').prop('disabled', false);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.password) {
                        $('#error-password').text(errors.password[0]);
                    }
                    if (errors.password_confirmation) {
                        $('#error-password_confirmation').text(errors.password_confirmation[0]);
                    }
                    if (errors.token) {
                        $('#error-password_confirmation').text(errors.token[0]);
                    }
                }
            }
        });
    });
});
</script>
@endpush