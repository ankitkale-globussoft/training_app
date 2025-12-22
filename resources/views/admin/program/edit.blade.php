@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Edit Program')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card mb-4">
        <h5 class="card-header">Edit Program</h5>

        <div class="card-body">

            {{-- Edit Form --}}
            <form id="editProgramForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $program->id }}">

                <div class="row">

                    {{-- Title --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Title <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" 
                               value="{{ $program->title }}">
                        <span class="invalid-feedback" id="error-title"></span>
                    </div>

                    {{-- Duration --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Duration <span class="text-danger">*</span></label>
                        <input type="text" id="duration" name="duration" class="form-control" 
                               value="{{ $program->duration }}">
                        <span class="invalid-feedback" id="error-duration"></span>
                    </div>

                    {{-- Program Type --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Type <span class="text-danger">*</span></label>
                        <select id="program_type_id" name="program_type_id" class="form-select">
                            <option value="">Select Type</option>
                            @foreach ($program_types as $type)
                                <option value="{{ $type->id }}" 
                                    {{ $program->program_type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback" id="error-program_type_id"></span>
                    </div>

                    {{-- Cost --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Price Per Student <span class="text-danger">*</span></label>
                        <input type="number" id="cost" name="cost" class="form-control" 
                               value="{{ $program->cost }}" step="0.01">
                        <span class="invalid-feedback" id="error-cost"></span>
                    </div>

                    {{-- Min Students --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Min. Students <span class="text-danger">*</span></label>
                        <input type="number" id="min_students" name="min_students" class="form-control" 
                               value="{{ $program->min_students }}">
                        <span class="invalid-feedback" id="error-min_students"></span>
                    </div>

                    {{-- Description --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" 
                                  class="form-control" rows="3">{{ $program->description }}</textarea>
                        <span class="invalid-feedback" id="error-description"></span>
                    </div>

                    {{-- Image Upload --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Program Image</label>
                        <input type="file" id="image" name="image" class="form-control">

                        {{-- OLD IMAGE PREVIEW --}}
                        <div id="old_image_preview" class="mt-3 {{ $program->image ? '' : 'd-none' }}">
                            @if($program->image)
                                <img src="{{ asset('storage/'.$program->image) }}" 
                                     id="old_image" class="img-fluid rounded" width="200">
                                <button type="button" id="remove_old_image" 
                                        class="btn btn-sm btn-danger mt-2">Remove</button>
                            @endif
                        </div>

                        {{-- NEW IMAGE PREVIEW --}}
                        <div id="image_preview" class="mt-3 d-none">
                            <img id="image_preview_img" src="" class="img-fluid rounded" width="200">
                            <button type="button" id="remove_image" 
                                    class="btn btn-sm btn-danger mt-2">Remove</button>
                        </div>

                        <span class="invalid-feedback" id="error-image"></span>
                    </div>

                </div>

                <button type="submit" id="updateProgramBtn" class="btn btn-primary">
                    <span class="bx bx-save me-1"></span> Update Program
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

    // ---------------- IMAGE PREVIEW -----------------

    // new image preview
    $('#image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            $('#old_image_preview').addClass('d-none');

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview_img').attr('src', e.target.result);
                $('#image_preview').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // remove new image preview
    $('#remove_image').on('click', function() {
        $('#image').val('');
        $('#image_preview').addClass('d-none');
    });

    // remove old image
    $('#remove_old_image').on('click', function() {
        $('#old_image_preview').addClass('d-none');
        $('<input>').attr({
            type: 'hidden',
            name: 'remove_old_image',
            value: 1
        }).appendTo('#editProgramForm');
    });


    // ---------------- UPDATE PROGRAM AJAX -----------------

    $("#editProgramForm").on("submit", function(e) {
        e.preventDefault();

        $('.invalid-feedback').text('');
        $('.form-control, .form-select').removeClass('is-invalid');

        $("#updateProgramBtn").html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...')
                              .prop("disabled", true);

        let formData = new FormData(this);
        formData.append('_method', 'PUT');
        $.ajax({
            type: "POST",
            url: "{{ route('admin.program.update', $program->program_id) }}",
            data: formData,
            processData: false,
            contentType: false,

            success: function(response) {
                $("#updateProgramBtn").html('<span class="bx bx-save me-1"></span> Update Program')
                                      .prop("disabled", false);

                if (response.success === true) {
                    $("#programSuccess").removeClass("d-none").text(response.msg);

                    setTimeout(() => {
                        window.location.href = "{{ route('admin.program.index') }}";
                    }, 1200);
                }
            },

            error: function(xhr) {
                $("#updateProgramBtn").html('<span class="bx bx-save me-1"></span> Update Program')
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
