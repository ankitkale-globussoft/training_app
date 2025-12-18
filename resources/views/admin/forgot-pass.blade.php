@extends('layouts.auth')
@section('title', 'Forgot Password')

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

                        <h4 class="mb-2 text-center">Forgot Password 🔒</h4>
                        <p class="mb-4 text-center">
                            Enter your email and we’ll send you a reset link
                        </p>

                        <form id="forgotForm">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter your email"
                                    autofocus>
                                <small class="text-danger d-block mt-1" id="error-email"></small>
                            </div>

                            <button class="btn btn-primary d-grid w-100" id="forgotBtn" type="submit">
                                Send Reset Link
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('admin.login') }}">
                                <i class="bx bx-chevron-left"></i> Back to login
                            </a>
                        </div>

                        <div id="forgotSuccess" class="alert alert-success d-none mt-3"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $(function() {
            $('#forgotForm').on('submit', function(e) {
                e.preventDefault();

                $('#error-email').text('');
                $('#forgotBtn').html('<span class="spinner-border spinner-border-sm"></span> Sending...')
                    .prop('disabled', true);

                $.ajax({
                    url: "{{ route('admin.forgot-pass') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#forgotBtn').html('Send Reset Link').prop('disabled', false);
                        $('#forgotSuccess').removeClass('d-none').text(res.msg);

                        if (res.success === true) {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }
                    },
                    error: function(xhr) {
                        $('#forgotBtn').html('Send Reset Link').prop('disabled', false);
                        if (xhr.status === 422) {
                            $('#error-email').text(xhr.responseJSON.errors.email[0]);
                        }
                    }
                });
            });
        });
    </script>
@endpush
