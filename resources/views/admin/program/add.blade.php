@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Add Program')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-4">
            <h5 class="card-header">Add New Program</h5>

            <div class="card-body">

                <form id="addProgramForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        {{-- Title --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control">
                            <span class="invalid-feedback" id="error-title"></span>
                        </div>

                        {{-- Duration --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration <span class="text-danger">*</span></label>
                            <input type="text" id="duration" name="duration" class="form-control"
                                placeholder="e.g. 3 months">
                            <span class="invalid-feedback" id="error-duration"></span>
                        </div>

                        {{-- Program Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Type <span class="text-danger">*</span></label>
                            <select id="program_type_id" name="program_type_id" class="form-select">
                                <option value="">Select Type</option>
                                @foreach ($program_types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback" id="error-program_type_id"></span>
                        </div>

                        {{-- Cost --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cost <span class="text-danger">*</span></label>
                            <input type="text" id="cost" name="cost" class="form-control">
                            <span class="invalid-feedback" id="error-cost"></span>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                            <span class="invalid-feedback" id="error-description"></span>
                        </div>

                        {{-- Image --}}
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Program Image</label>
                            <input type="file" id="image" name="image" class="form-control">

                            <div id="image_preview" class="mt-3 d-none">
                                <img id="image_preview_img" src="" class="img-fluid rounded" width="200">
                                <button type="button" id="remove_image" class="btn btn-sm btn-danger mt-2">Remove</button>
                            </div>

                            <span class="invalid-feedback" id="error-image"></span>
                        </div>

                    </div>

                    <button type="submit" id="saveProgramBtn" class="btn btn-primary">
                        <span class="bx bx-save me-1"></span> Save Program
                    </button>

                    <div id="programSuccess" class="alert alert-success mt-3 d-none"></div>

                </form>

            </div>
        </div>

    </div>
@endsection
@push('ajax')
    <script>
        $(document).ready(function() {

            // Image Preview
            $('#image').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image_preview_img').attr('src', e.target.result);
                        $('#image_preview').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Remove Image
            $('#remove_image').on('click', function() {
                $('#image').val('');
                $('#image_preview').addClass('d-none');
            });

            // Submit Form
            $("#addProgramForm").on("submit", function(e) {
                e.preventDefault();

                // Reset errors
                $('.invalid-feedback').text('');
                $('.form-control, .form-select').removeClass('is-invalid');

                // Loading state
                $("#saveProgramBtn").html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Saving...')
                    .prop("disabled", true);

                let formData = new FormData(this);

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.program.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(response) {
                        $("#saveProgramBtn").html(
                                '<span class="bx bx-save me-1"></span> Save Program')
                            .prop("disabled", false);

                        if (response.success === true) {
                            $("#programSuccess").removeClass("d-none").text(response.msg);

                            $("#addProgramForm")[0].reset();
                            $('#image_preview').addClass('d-none');

                            setTimeout(() => {
                                window.location.href =
                                    "{{ route('admin.program.index') }}";
                            }, 1200);
                        }
                    },

                    error: function(xhr) {
                        $("#saveProgramBtn").html(
                                '<span class="bx bx-save me-1"></span> Save Program')
                            .prop("disabled", false);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#error-' + key).text(value[0]);
                            });

                            $('html, body').animate({
                                scrollTop: $('.is-invalid').first().offset().top - 100
                            }, 500);
                        }
                    }
                });
            });

        });
    </script>
@endpush
