@extends('layouts.auth')
@section('title', 'Organisation Register')

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">

                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">

                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4">
                            <a href="#" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-body fw-bolder">
                                    Organisation Signup
                                </span>
                            </a>
                        </div>

                        <p class="mb-4 text-center text-muted">
                            Register your organisation to access training programs
                        </p>

                        <form id="orgRegisterForm" enctype="multipart/form-data">
                            @csrf

                            <!-- Organisation Name -->
                            <div class="mb-3">
                                <label class="form-label">Organisation Name</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="ABC Pvt Ltd">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Representative Designation -->
                            <div class="mb-3">
                                <label class="form-label">Representative Designation</label>
                                <input type="text" id="rep_designation" name="rep_designation" class="form-control"
                                    placeholder="HR Manager">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="org@email.com">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Mobile -->
                            <div class="mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" id="mobile" name="mobile" class="form-control"
                                    placeholder="10 digit number">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Alternate Mobile -->
                            <div class="mb-3">
                                <label class="form-label">Alternate Mobile</label>
                                <input type="text" id="alt_mobile" name="alt_mobile" class="form-control"
                                    placeholder="Optional">
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Organisation Image -->
                            <div class="mb-3">
                                <label class="form-label">Organisation Logo</label>
                                <input type="file" id="org_image" name="org_image" class="form-control" accept="image/*">
                                <div class="invalid-feedback"></div>

                                <!-- Preview -->
                                <div id="imagePreviewWrapper" class="mt-3 d-none position-relative"
                                    style="max-width:150px;">
                                    <img id="imagePreview" class="img-thumbnail w-100">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                        onclick="removeImage()">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" id="addr_line1" name="addr_line1" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" id="addr_line2" name="addr_line2" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" id="city" name="city" class="form-control">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">District</label>
                                    <input type="text" id="district" name="district" class="form-control">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" id="state" name="state" class="form-control">
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" id="pincode" name="pincode" class="form-control">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-4 form-password-toggle">
                                <label class="form-label">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="••••••••">
                                    <span class="input-group-text cursor-pointer" id="togglePassword">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Submit -->
                            <button class="btn btn-primary d-grid w-100" id="submitBtn">
                                Sign up
                            </button>
                        </form>


                        <p class="text-center mt-3">
                            <span>Already have an account?</span>
                            <a href="{{ route('org.login') }}">Sign in</a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('ajax')
    <script>
        /* =============================
       Password Toggle (Fixed)
    ============================= */
        $('#togglePassword').on('click', function() {
            const input = $('#password');
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bx-hide').addClass('bx-show');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bx-show').addClass('bx-hide');
            }
        });

        /* =============================
           Image Preview
        ============================= */
        $('#org_image').on('change', function() {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewWrapper').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        });

        function removeImage() {
            $('#org_image').val('');
            $('#imagePreviewWrapper').addClass('d-none');
            $('#imagePreview').attr('src', '');
        }

        /* =============================
           AJAX Submit (FormData)
        ============================= */
        $('#orgRegisterForm').on('submit', function(e) {
            e.preventDefault();
            clearErrors();

            let btn = $('#submitBtn');
            btn.prop('disabled', true).text('Please wait...');

            let formData = new FormData(this);

            formData.append('_token', "{{ csrf_token() }}");
            console.log(formData);

            $.ajax({
                url: "{{ route('org.register.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    window.location.href = res.redirect;
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Sign up');
                    if (xhr.status === 422) {
                        showErrors(xhr.responseJSON.errors);
                    }
                }
            });
        });

        /* =============================
           Validation Helpers
        ============================= */
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