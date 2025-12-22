@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Edit Content')

@section('content')
    <div class="container-xxl container-p-y">

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <span class="text-muted fw-light">Content Manager /</span> Edit Content
                </h4>
            </div>
            <a href="{{ route('trainer.content.manage', $booking_id) }}" class="btn btn-label-secondary">
                <i class="bx bx-arrow-back"></i> Back to Content
            </a>
        </div>

        <!-- Edit Content Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bx bx-edit me-2"></i>Edit Content
                </h5>
            </div>
            <div class="card-body">
                <form id="editContentForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="content_id" value="{{ $content->content_id }}">



                    <!-- Content Type Selection (Readonly/Disabled to prevent major data loss confusion, or allowed?) -->
                    <!-- Allowing type change implies re-uploading file. Let's allow it but warn user visually if they change type. -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Content Type</label>
                            <div class="row g-3">
                                <div class="col-md-2 col-6">
                                    <input type="radio" class="btn-check" name="content_type" id="type_video" value="video"
                                        {{ $content->content_type == 'video' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3"
                                        for="type_video">
                                        <i class="bx bx-video bx-lg mb-2"></i> <span>Video</span>
                                    </label>
                                </div>
                                <div class="col-md-2 col-6">
                                    <input type="radio" class="btn-check" name="content_type" id="type_text" value="text" {{ $content->content_type == 'text' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3"
                                        for="type_text">
                                        <i class="bx bx-file bx-lg mb-2"></i> <span>Text</span>
                                    </label>
                                </div>
                                <div class="col-md-2 col-6">
                                    <input type="radio" class="btn-check" name="content_type" id="type_pdf" value="pdf" {{ $content->content_type == 'pdf' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3"
                                        for="type_pdf">
                                        <i class="bx bxs-file-pdf bx-lg mb-2"></i> <span>PDF</span>
                                    </label>
                                </div>
                                <div class="col-md-2 col-6">
                                    <input type="radio" class="btn-check" name="content_type" id="type_link" value="link" {{ $content->content_type == 'link' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3"
                                        for="type_link">
                                        <i class="bx bx-link bx-lg mb-2"></i> <span>Link</span>
                                    </label>
                                </div>
                                <div class="col-md-2 col-6">
                                    <input type="radio" class="btn-check" name="content_type" id="type_meeting"
                                        value="meeting" {{ $content->content_type == 'meeting' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3"
                                        for="type_meeting">
                                        <i class="bx bx-video-recording bx-lg mb-2"></i> <span>Meeting</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Title & Description -->
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Content Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $content->title }}">
                            <span class="invalid-feedback" id="error_title"></span>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                rows="3">{{ $content->description }}</textarea>
                        </div>
                    </div>

                    <!-- Dynamic Fields -->
                    <div id="content_fields_container">

                        <!-- Video -->
                        <div class="content-field {{ $content->content_type == 'video' ? '' : 'd-none' }}" id="field_video">
                            <div class="card bg-label-primary mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3">Update Video File (Optional)</h6>
                                    <input type="file" class="form-control mb-2" id="video_file" name="video_file"
                                        accept="video/*">
                                    @if($content->content_type == 'video' && $content->file_path)
                                        <div class="alert alert-info py-2 mb-0">
                                            <small><i class="bx bx-video me-1"></i> Current Video: <a
                                                    href="{{ $content->file_url }}" target="_blank">View Video</a></small>
                                        </div>
                                    @endif
                                    <div id="video_preview" class="d-none mt-2">
                                        <video controls class="w-100 rounded" style="max-height: 300px;"></video>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Text -->
                        <div class="content-field {{ $content->content_type == 'text' ? '' : 'd-none' }}" id="field_text">
                            <div class="card bg-label-info mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3">Text Content</h6>
                                    <textarea class="form-control" id="text_content" name="text_content"
                                        rows="10">{{ $content->text_content }}</textarea>
                                    <span class="invalid-feedback" id="error_text_content"></span>
                                </div>
                            </div>
                        </div>

                        <!-- PDF -->
                        <div class="content-field {{ $content->content_type == 'pdf' ? '' : 'd-none' }}" id="field_pdf">
                            <div class="card bg-label-danger mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3">Update PDF File (Optional)</h6>
                                    <input type="file" class="form-control mb-2" id="pdf_file" name="pdf_file"
                                        accept=".pdf">
                                    @if($content->content_type == 'pdf' && $content->file_path)
                                        <div class="alert alert-success py-2 mb-0">
                                            <small><i class="bx bxs-file-pdf me-1"></i> Current PDF: <a
                                                    href="{{ $content->file_url }}" target="_blank">View PDF</a></small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Link -->
                        <div class="content-field {{ $content->content_type == 'link' ? '' : 'd-none' }}" id="field_link">
                            <div class="card bg-label-success mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3">External Link</h6>
                                    <input type="url" class="form-control" id="external_url" name="external_url"
                                        value="{{ $content->external_url }}">
                                    <span class="invalid-feedback" id="error_external_url"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Meeting -->
                        <div class="content-field {{ $content->content_type == 'meeting' ? '' : 'd-none' }}"
                            id="field_meeting">
                            <div class="card bg-label-warning mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3">Meeting Link</h6>
                                    <input type="url" class="form-control" id="meeting_url" name="meeting_url"
                                        value="{{ $content->external_url }}">
                                    <span class="invalid-feedback" id="error_meeting_url"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Visibility -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">Visibility Settings</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="is_visible_to_org"
                                            name="is_visible_to_org" {{ $content->is_visible_to_org ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_visible_to_org">Visible to
                                            Organization</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="is_visible_to_candidates"
                                            name="is_visible_to_candidates" {{ $content->is_visible_to_candidates ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_visible_to_candidates">Visible to
                                            Candidates</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bx bx-save"></i> Update Content
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        const bookingId = {{ $booking_id }};

        $(document).ready(function () {
            // Checkbox change handler
            $('input[name="content_type"]').change(function () {
                switchContentFields($(this).val());
            });

            $('#video_file').change(function () { previewVideo(this); });

            $('#editContentForm').submit(function (e) {
                e.preventDefault();
                submitContent();
            });
        });


        function switchContentFields(type) {
            $('.content-field').addClass('d-none');
            $('.invalid-feedback').text('').hide();
            $('.form-control').removeClass('is-invalid');
            $('#field_' + type).removeClass('d-none');
        }

        function previewVideo(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    const video = $('#video_preview video')[0];
                    video.src = e.target.result;
                    $('#video_preview').removeClass('d-none');
                }
                reader.readAsDataURL(file);
            }
        }

        function submitContent() {
            $('.invalid-feedback').text('').hide();
            $('.form-control').removeClass('is-invalid');

            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

            const formData = new FormData($('#editContentForm')[0]);

            // Add logic for text/link/meeting fields based on selected type
            const contentType = $('input[name="content_type"]:checked').val();

            if (contentType === 'text') {
                formData.append('text_content', $('#text_content').val());
            } else if (contentType === 'link') {
                formData.append('external_url', $('#external_url').val());
            } else if (contentType === 'meeting') {
                formData.append('meeting_url', $('#meeting_url').val());
            }

            $.ajax({
                url: '{{ route("trainer.content.update") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('trainer.content.manage', $booking_id) }}";
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                        $('#submitBtn').prop('disabled', false).html('<i class="bx bx-save"></i> Update Content');
                    }
                },
                error: function (xhr) {
                    $('#submitBtn').prop('disabled', false).html('<i class="bx bx-save"></i> Update Content');
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('#error_' + key).text(value[0]).show();
                            $('#' + key).addClass('is-invalid');
                        });
                        Swal.fire('Validation Error', 'Please check fields.', 'error');
                    } else {
                        Swal.fire('Error', 'Failed to update.', 'error');
                    }
                }
            });
        }
    </script>
@endpush