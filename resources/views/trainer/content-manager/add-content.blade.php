@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Add Content')

@section('content')
<div class="container-xxl container-p-y">

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Content Manager /</span> Add Content
            </h4>
            <p class="text-muted mb-0" id="training_info">
                <i class="bx bx-building me-1"></i>
                <span id="org_name">Loading...</span>
            </p>
        </div>
        <a href="{{ route('trainer.content-manager') }}" class="btn btn-label-secondary">
            <i class="bx bx-arrow-back"></i> Back to Content
        </a>
    </div>

    <!-- Training Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row" id="booking_details">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-book-open bx-sm"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Program</small>
                            <h6 class="mb-0" id="program_title">Loading...</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-desktop bx-sm"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Mode</small>
                            <h6 class="mb-0" id="training_mode">Loading...</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-calendar bx-sm"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Schedule</small>
                            <h6 class="mb-0" id="schedule">Loading...</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-folder-open bx-sm"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Content</small>
                            <h6 class="mb-0" id="content_count">0 Items</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Content Form -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bx bx-plus-circle me-2"></i>Add New Content
            </h5>
        </div>
        <div class="card-body">
            <form id="addContentForm" enctype="multipart/form-data">
                @csrf

                <!-- Module Selection (Optional) -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Module (Optional)</label>
                        <select class="form-select" id="module_id" name="module_id">
                            <option value="">-- No Module (General Content) --</option>
                            <!-- Modules will be loaded via AJAX -->
                        </select>
                        <div class="form-text">
                            <i class="bx bx-info-circle"></i> Organize content into modules for better structure
                        </div>
                    </div>
                </div>

                <!-- Content Type Selection -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Content Type <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-md-2 col-6">
                                <input type="radio" class="btn-check" name="content_type" id="type_video" value="video" checked>
                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3" for="type_video">
                                    <i class="bx bx-video bx-lg mb-2"></i>
                                    <span>Video</span>
                                </label>
                            </div>
                            <div class="col-md-2 col-6">
                                <input type="radio" class="btn-check" name="content_type" id="type_text" value="text">
                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3" for="type_text">
                                    <i class="bx bx-file bx-lg mb-2"></i>
                                    <span>Text</span>
                                </label>
                            </div>
                            <div class="col-md-2 col-6">
                                <input type="radio" class="btn-check" name="content_type" id="type_pdf" value="pdf">
                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3" for="type_pdf">
                                    <i class="bx bxs-file-pdf bx-lg mb-2"></i>
                                    <span>PDF</span>
                                </label>
                            </div>
                            <div class="col-md-2 col-6">
                                <input type="radio" class="btn-check" name="content_type" id="type_link" value="link">
                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3" for="type_link">
                                    <i class="bx bx-link bx-lg mb-2"></i>
                                    <span>Link</span>
                                </label>
                            </div>
                            <div class="col-md-2 col-6">
                                <input type="radio" class="btn-check" name="content_type" id="type_meeting" value="meeting">
                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3" for="type_meeting">
                                    <i class="bx bx-video-recording bx-lg mb-2"></i>
                                    <span>Meeting</span>
                                </label>
                            </div>
                        </div>
                        <span class="invalid-feedback d-block" id="error_content_type"></span>
                    </div>
                </div>

                <!-- Title & Description -->
                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            Content Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter content title...">
                        <span class="invalid-feedback" id="error_title"></span>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of this content..."></textarea>
                        <span class="invalid-feedback" id="error_description"></span>
                    </div>
                </div>

                <!-- Dynamic Content Fields -->
                <div id="content_fields_container">
                    
                    <!-- Video Upload Field -->
                    <div class="content-field" id="field_video">
                        <div class="card bg-label-primary mb-4">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="bx bx-cloud-upload me-2"></i>Upload Video File
                                </h6>
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                                    <div class="form-text">
                                        <i class="bx bx-info-circle"></i> Supported formats: MP4, AVI, MOV (Max: 100MB)
                                    </div>
                                    <span class="invalid-feedback" id="error_video_file"></span>
                                </div>
                                <div id="video_preview" class="d-none">
                                    <video controls class="w-100 rounded" style="max-height: 300px;"></video>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Text Content Field -->
                    <div class="content-field d-none" id="field_text">
                        <div class="card bg-label-info mb-4">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="bx bx-edit me-2"></i>Text Content
                                </h6>
                                <textarea class="form-control" id="text_content" name="text_content" rows="10" placeholder="Enter your text content here..."></textarea>
                                <span class="invalid-feedback" id="error_text_content"></span>
                            </div>
                        </div>
                    </div>

                    <!-- PDF Upload Field -->
                    <div class="content-field d-none" id="field_pdf">
                        <div class="card bg-label-danger mb-4">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="bx bx-cloud-upload me-2"></i>Upload PDF Document
                                </h6>
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
                                    <div class="form-text">
                                        <i class="bx bx-info-circle"></i> Only PDF files (Max: 25MB)
                                    </div>
                                    <span class="invalid-feedback" id="error_pdf_file"></span>
                                </div>
                                <div id="pdf_preview" class="d-none">
                                    <div class="alert alert-success d-flex align-items-center">
                                        <i class="bx bxs-file-pdf fs-3 me-2"></i>
                                        <span id="pdf_name"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- External Link Field -->
                    <div class="content-field d-none" id="field_link">
                        <div class="card bg-label-success mb-4">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="bx bx-link-external me-2"></i>External Link/URL
                                </h6>
                                <input type="url" class="form-control" id="external_url" name="external_url" placeholder="https://example.com/resource">
                                <div class="form-text">
                                    <i class="bx bx-info-circle"></i> Enter a valid URL (e.g., YouTube, Google Drive, etc.)
                                </div>
                                <span class="invalid-feedback" id="error_external_url"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Meeting Link Field -->
                    <div class="content-field d-none" id="field_meeting">
                        <div class="card bg-label-warning mb-4">
                            <div class="card-body">
                                <h6 class="mb-3">
                                    <i class="bx bx-video-recording me-2"></i>Meeting/Webinar Link
                                </h6>
                                <input type="url" class="form-control" id="meeting_url" name="meeting_url" placeholder="https://zoom.us/j/123456789 or Google Meet link">
                                <div class="form-text">
                                    <i class="bx bx-info-circle"></i> Enter Zoom, Google Meet, or any meeting platform link
                                </div>
                                <span class="invalid-feedback" id="error_meeting_url"></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Visibility Settings -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">
                            <i class="bx bx-show me-2"></i>Visibility Settings
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_visible_to_org" name="is_visible_to_org" checked>
                                    <label class="form-check-label" for="is_visible_to_org">
                                        <i class="bx bx-building text-primary"></i> Visible to Organization
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_visible_to_candidates" name="is_visible_to_candidates" checked>
                                    <label class="form-check-label" for="is_visible_to_candidates">
                                        <i class="bx bx-group text-info"></i> Visible to Candidates
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary" onclick="window.history.back()">
                        <i class="bx bx-x"></i> Cancel
                    </button>
                    <div>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bx bx-save"></i> Save Content
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@push('ajax')
<script>
const bookingId = {{ $booking_id }};

$(document).ready(function() {
    // Load booking details
    loadBookingDetails();
    
    // Load modules for dropdown
    loadModules();
    
    // Content type change handler
    $('input[name="content_type"]').change(function() {
        switchContentFields($(this).val());
    });
    
    // File preview handlers
    $('#video_file').change(function() {
        previewVideo(this);
    });
    
    $('#pdf_file').change(function() {
        previewPDF(this);
    });
    
    // Form submission
    $('#addContentForm').submit(function(e) {
        e.preventDefault();
        submitContent();
    });
});

function loadBookingDetails() {
    $.ajax({
        url: '{{ url("trainer/content-manager/booking") }}/' + bookingId,
        method: 'GET',
        success: function(response) {
            if (response.status) {
                const data = response.data;
                $('#org_name').text(data.organization.name);
                $('#program_title').text(data.requirement.program.title);
                $('#training_mode').text(data.requirement.mode.toUpperCase());
                
                if (data.requirement.schedule_start) {
                    const start = new Date(data.requirement.schedule_start).toLocaleDateString('en-IN');
                    const end = new Date(data.requirement.schedule_end).toLocaleDateString('en-IN');
                    $('#schedule').text(start + ' - ' + end);
                } else {
                    $('#schedule').text('Not Scheduled');
                }
                
                $('#content_count').text(data.content_count + ' Items');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to load booking details', 'error');
        }
    });
}

function loadModules() {
    $.ajax({
        url: '{{ url("trainer/content-manager/modules") }}/' + bookingId,
        method: 'GET',
        success: function(response) {
            if (response.status && response.data.length > 0) {
                let options = '<option value="">-- No Module (General Content) --</option>';
                response.data.forEach(module => {
                    options += `<option value="${module.module_id}">${module.module_name}</option>`;
                });
                $('#module_id').html(options);
            }
        }
    });
}

function switchContentFields(type) {
    // Hide all content fields
    $('.content-field').addClass('d-none');
    
    // Clear all error messages
    $('.invalid-feedback').text('').hide();
    $('.form-control, .form-select').removeClass('is-invalid');
    
    // Show selected content field
    $('#field_' + type).removeClass('d-none');
}

function previewVideo(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const video = $('#video_preview video')[0];
            video.src = e.target.result;
            $('#video_preview').removeClass('d-none');
        }
        
        reader.readAsDataURL(file);
    }
}

function previewPDF(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        $('#pdf_name').text(file.name);
        $('#pdf_preview').removeClass('d-none');
    }
}

function submitContent() {
    // Clear previous errors
    $('.invalid-feedback').text('').hide();
    $('.form-control, .form-select').removeClass('is-invalid');
    
    // Disable submit button
    $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
    
    const formData = new FormData($('#addContentForm')[0]);
    formData.append('booking_id', bookingId);
    
    // Get content type and add appropriate field
    const contentType = $('input[name="content_type"]:checked').val();
    
    if (contentType === 'video') {
        // Video file is already in formData
    } else if (contentType === 'text') {
        formData.append('text_content', $('#text_content').val());
    } else if (contentType === 'pdf') {
        // PDF file is already in formData
    } else if (contentType === 'link') {
        formData.append('external_url', $('#external_url').val());
    } else if (contentType === 'meeting') {
        formData.append('external_url', $('#meeting_url').val());
    }
    
    $.ajax({
        url: '{{ route("trainer.content.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    confirmButtonText: 'Add More Content',
                    showCancelButton: true,
                    cancelButtonText: 'View All Content'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset form
                        $('#addContentForm')[0].reset();
                        $('#video_preview, #pdf_preview').addClass('d-none');
                        loadBookingDetails(); // Reload to update count
                    } else {
                        window.location.href = '{{ route("trainer.content-manager") }}';
                    }
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
            
            $('#submitBtn').prop('disabled', false).html('<i class="bx bx-save"></i> Save Content');
        },
        error: function(xhr) {
            $('#submitBtn').prop('disabled', false).html('<i class="bx bx-save"></i> Save Content');
            
            if (xhr.status === 422) {
                // Validation errors
                const errors = xhr.responseJSON.errors;
                
                $.each(errors, function(key, value) {
                    const errorElement = $('#error_' + key);
                    const inputElement = $('#' + key);
                    
                    errorElement.text(value[0]).show();
                    inputElement.addClass('is-invalid');
                });
                
                Swal.fire('Validation Error', 'Please check the form and fix the errors', 'error');
            } else {
                Swal.fire('Error', 'Failed to save content: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
            }
        }
    });
}
</script>
@endpush