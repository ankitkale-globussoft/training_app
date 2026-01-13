@extends('layouts.auth')
@section('title', 'Reset Password - Trainer')

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{ route('home') }}" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-body fw-bolder">{{ config('app.name') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <h4 class="mb-2">Reset Password 🔒</h4>
                        <p class="mb-4">Enter your new password below.</p>

                        <form id="resetPasswordForm" class="mb-3">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="{{ $email }}"
                                    readonly>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">New Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="••••••••••••" autofocus />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                                <div class="invalid-feedback" id="error-password"></div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">Confirm Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" class="form-control"
                                        name="password_confirmation" placeholder="••••••••••••" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <button class="btn btn-primary d-grid w-100" id="submitBtn" type="submit">Set New
                                Password</button>
                        </form>

                        <div id="successMessage" class="alert alert-success d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $(document).ready(function () {
            $('#resetPasswordForm').on('submit', function (e) {
                e.preventDefault();

                let btn = $('#submitBtn');
                let errorDiv = $('#error-password');
                let form = $(this);

                // Reset state
                $('.form-control').removeClass('is-invalid');
                errorDiv.text('');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');

                $.ajax({
                    url: "{{ route('trainer.password.update') }}",
                    type: "POST",
                    data: form.serialize(),
                    success: function (response) {
                        btn.prop('disabled', false).text('Set New Password');
                        $('#successMessage').text(response.status).removeClass('d-none');

                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1500);
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).text('Set New Password');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.email) {
                                alert(errors.email[0]);
                            }
                            if (errors.password) {
                                $('#password').addClass('is-invalid');
                                errorDiv.text(errors.password[0]);
                            }
                        } else {
                            alert('Something went wrong. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
@endpush