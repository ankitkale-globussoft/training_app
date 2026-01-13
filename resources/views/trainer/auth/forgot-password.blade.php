@extends('layouts.auth')
@section('title', 'Forgot Password - Trainer')

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

                        <h4 class="mb-2">Forgot Password? 🔒</h4>
                        <p class="mb-4">Enter your email and we'll send you instructions to reset your password.</p>

                        <form id="forgotPasswordForm" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" autofocus>
                                <div class="invalid-feedback" id="error-email"></div>
                            </div>
                            <button class="btn btn-primary d-grid w-100" id="submitBtn" type="submit">Send Reset
                                Link</button>
                        </form>

                        <div id="successMessage" class="alert alert-success d-none"></div>

                        <div class="text-center">
                            <a href="{{ route('trainer.login') }}" class="d-flex align-items-center justify-content-center">
                                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                                Back to login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $(document).ready(function () {
            $('#forgotPasswordForm').on('submit', function (e) {
                e.preventDefault();

                let btn = $('#submitBtn');
                let errorDiv = $('#error-email');
                let successDiv = $('#successMessage');
                let emailInput = $('#email');

                // Reset state
                emailInput.removeClass('is-invalid');
                errorDiv.text('');
                successDiv.addClass('d-none');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Sending...');

                $.ajax({
                    url: "{{ route('trainer.password.email') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function (response) {
                        btn.prop('disabled', false).text('Send Reset Link');
                        successDiv.text(response.status).removeClass('d-none');
                        emailInput.val('');
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).text('Send Reset Link');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.email) {
                                emailInput.addClass('is-invalid');
                                errorDiv.text(errors.email[0]);
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