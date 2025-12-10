@extends('layouts.auth')
@section('title', 'Trainer Register')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">
            <!-- Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Trainer Registration</h5>
                    <small class="text-muted float-end">Complete all fields</small>
                </div>
                <div class="card-body">
                    <form id="trainerSignupForm" enctype="multipart/form-data">
                        @csrf
                        <!-- Personal Information -->
                        <h6 class="mb-3 text-primary">Personal Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                                <div class="invalid-feedback" id="error-name"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="phone">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="1234567890" required>
                                <div class="invalid-feedback" id="error-phone"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" required>
                                <div class="invalid-feedback" id="error-email"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                                <div class="invalid-feedback" id="error-password"></div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <h6 class="mb-3 text-primary">Address Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label" for="addr_line1">Address Line 1 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="addr_line1" name="addr_line1" placeholder="Street address" required>
                                <div class="invalid-feedback" id="error-addr_line1"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="addr_line2">Address Line 2</label>
                                <input type="text" class="form-control" id="addr_line2" name="addr_line2" placeholder="Apartment, suite, etc.">
                                <div class="invalid-feedback" id="error-addr_line2"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="city">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Mumbai" required>
                                <div class="invalid-feedback" id="error-city"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="district">District <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="district" name="district" placeholder="Mumbai Suburban" required>
                                <div class="invalid-feedback" id="error-district"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="state">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" placeholder="Maharashtra" required>
                                <div class="invalid-feedback" id="error-state"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="pincode">Pincode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pincode" name="pincode" placeholder="400001" required>
                                <div class="invalid-feedback" id="error-pincode"></div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <h6 class="mb-3 text-primary">Professional Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label" for="biodata">Bio/About Yourself <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="biodata" name="biodata" rows="3" placeholder="Tell us about your experience and expertise..." required></textarea>
                                <div class="invalid-feedback" id="error-biodata"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="achievements">Achievements</label>
                                <textarea class="form-control" id="achievements" name="achievements" rows="3" placeholder="List your key achievements and certifications..."></textarea>
                                <div class="invalid-feedback" id="error-achievements"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="resume_link">Resume/CV Link</label>
                                <input type="url" class="form-control" id="resume_link" name="resume_link" placeholder="https://drive.google.com/...">
                                <div class="invalid-feedback" id="error-resume_link"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="for_org_type">Organization Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="for_org_type" name="for_org_type" required>
                                    <option value="">Select Type</option>
                                    <option value="corporate">Corporate</option>
                                    <option value="school">School</option>
                                    <option value="both">Both</option>
                                </select>
                                <div class="invalid-feedback" id="error-for_org_type"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="availability">Availability <span class="text-danger">*</span></label>
                                <select class="form-select" id="availability" name="availability" required>
                                    <option value="">Select Availability</option>
                                    <option value="full-time">Full Time</option>
                                    <option value="part-time">Part Time</option>
                                    <option value="weekends">Weekends Only</option>
                                    <option value="flexible">Flexible</option>
                                </select>
                                <div class="invalid-feedback" id="error-availability"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="training_mode">Training Mode <span class="text-danger">*</span></label>
                                <select class="form-select" id="training_mode" name="training_mode" required>
                                    <option value="">Select Mode</option>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                    <option value="both">Both (Hybrid)</option>
                                </select>
                                <div class="invalid-feedback" id="error-training_mode"></div>
                            </div>
                        </div>

                        <!-- File Uploads -->
                        <h6 class="mb-3 text-primary">Documents & Media</h6>
                        <div class="row g-3 mb-4">
                            <!-- Profile Picture -->
                            <div class="col-md-6">
                                <label class="form-label" for="profile_pic">Profile Picture <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*" required>
                                <div class="invalid-feedback" id="error-profile_pic"></div>
                                <small class="text-muted">Max size: 2MB (JPG, PNG)</small>
                                <!-- Image Preview -->
                                <div id="profile_pic_preview" class="mt-3 d-none">
                                    <div class="position-relative d-inline-block">
                                        <img id="profile_pic_img" src="" alt="Profile Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" id="remove_profile_pic" style="width: 30px; height: 30px; padding: 0;">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Signed Form PDF -->
                            <div class="col-md-6">
                                <label class="form-label" for="signed_form_pdf">Signed Agreement Form <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="signed_form_pdf" name="signed_form_pdf" accept=".pdf" required>
                                <div class="invalid-feedback" id="error-signed_form_pdf"></div>
                                <small class="text-muted">Max size: 5MB (PDF only)</small>
                                <!-- PDF Preview -->
                                <div id="signed_form_pdf_preview" class="mt-3 d-none">
                                    <div class="alert alert-info d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="bx bxs-file-pdf me-2"></i>
                                            <span id="pdf_filename"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger" id="remove_signed_form_pdf">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <div class="alert alert-success d-none" id="signupSuccess" role="alert"></div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-label-secondary">Reset</button>
                            <button type="submit" class="btn btn-primary" id="signupBtn">
                                <i class="bx bx-user-plus me-1"></i> Register as Trainer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->

<script>
$(document).ready(function() {
    // Profile Picture Preview
    $('#profile_pic').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profile_pic_img').attr('src', e.target.result);
                $('#profile_pic_preview').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove Profile Picture
    $('#remove_profile_pic').on('click', function() {
        $('#profile_pic').val('');
        $('#profile_pic_preview').addClass('d-none');
    });

    // Signed Form PDF Preview
    $('#signed_form_pdf').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            $('#pdf_filename').text(file.name);
            $('#signed_form_pdf_preview').removeClass('d-none');
        }
    });

    // Remove PDF
    $('#remove_signed_form_pdf').on('click', function() {
        $('#signed_form_pdf').val('');
        $('#signed_form_pdf_preview').addClass('d-none');
    });

    // Form Submission
    $("#trainerSignupForm").on("submit", function(e) {
        e.preventDefault();

        // Clear previous errors
        $('.invalid-feedback').text('');
        $('.form-control, .form-select').removeClass('is-invalid');
        $('#signupSuccess').addClass('d-none');

        // Button loading state
        $("#signupBtn").html('<span class="spinner-border spinner-border-sm me-1"></span> Registering...').prop("disabled", true);
        
        let formData = new FormData(this);

        $.ajax({
            type: "POST",
            url: "{{ route('trainer.register') }}",
            data: formData,
            processData: false,
            contentType: false,  
            success: function(response) {
                $("#signupBtn").html('<i class="bx bx-user-plus me-1"></i> Register as Trainer').prop("disabled", false);

                if (response.success === true) {
                    $("#signupSuccess").removeClass("d-none").text(response.msg ?? "Registration successful!");
                    
                    // Reset form and previews
                    $("#trainerSignupForm")[0].reset();
                    $('#profile_pic_preview').addClass('d-none');
                    $('#signed_form_pdf_preview').addClass('d-none');

                    // Redirect if provided
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    }
                }
            },
            error: function(xhr) {
                $("#signupBtn").html('<i class="bx bx-user-plus me-1"></i> Register as Trainer').prop("disabled", false);

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    
                    // Display errors
                    $.each(errors, function(key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#error-' + key).text(value[0]);
                    });

                    // Scroll to first error
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);
                }
            }
        });
    });
});
</script>
@endsection