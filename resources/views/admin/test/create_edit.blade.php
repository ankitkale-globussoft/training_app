@extends('layouts.master', ['panel' => 'admin'])
@section('title', $test ? 'Edit Test' : 'Create Test')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin / Tests /</span> {{ $test ? 'Edit' : 'Create' }}
        </h4>

        <div class="row">
            <!-- Test Details -->
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Test Details</h5>
                    <div class="card-body">
                        <form id="testDetailsForm" action="{{ $test ? route('admin.test.update', $test->test_id) : route('admin.test.store') }}">
                            @csrf
                            @if($test) @method('PUT') @endif
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $test->title ?? '') }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Program</label>
                                    <select name="program_id" class="form-select">
                                        <option value="">Select Program</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->program_id }}" {{ (old('program_id', $test->program_id ?? '') == $program->program_id) ? 'selected' : '' }}>
                                                {{ $program->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duration (Minutes) - <small class="text-muted">Leave empty for no limit</small></label>
                                    <input type="number" name="duration" class="form-control" value="{{ old('duration', $test->duration ?? '') }}" min="1">
                                    <div class="invalid-feedback"></div>
                                </div>
                                @if($test)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Marks (Auto-calculated)</label>
                                    <input type="text" class="form-control" value="{{ $test->total_marks }}" readonly id="displayTotalMarks">
                                </div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary">{{ $test ? 'Update Test Details' : 'Create & Continue' }}</button>
                                <a href="{{ route('admin.test.index') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Questions Section (Only in Edit Mode) -->
            @if($test)
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Questions</h5>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="bx bx-plus"></i> Add Question
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered" id="questionsTable">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Options</th>
                                        <th>Answer</th>
                                        <th>Marks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($test)
<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addQuestionForm">
                <div class="modal-body">
                    <input type="hidden" name="test_id" value="{{ $test->test_id }}">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Question Text</label>
                            <textarea name="ques_text" class="form-control"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option A</label>
                            <input type="text" name="opt_a" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option B</label>
                            <input type="text" name="opt_b" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option C</label>
                            <input type="text" name="opt_c" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option D</label>
                            <input type="text" name="opt_d" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correct Option</label>
                            <select name="ans_opt" class="form-select">
                                <option value="a">Option A</option>
                                <option value="b">Option B</option>
                                <option value="c">Option C</option>
                                <option value="d">Option D</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label class="form-label">Marks</label>
                            <input type="number" name="marks" class="form-control" min="1" value="1">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editQuestionForm">
                <input type="hidden" name="id" id="edit_ques_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Question Text</label>
                            <textarea name="ques_text" id="edit_ques_text" class="form-control"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option A</label>
                            <input type="text" name="opt_a" id="edit_opt_a" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option B</label>
                            <input type="text" name="opt_b" id="edit_opt_b" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option C</label>
                            <input type="text" name="opt_c" id="edit_opt_c" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Option D</label>
                            <input type="text" name="opt_d" id="edit_opt_d" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correct Option</label>
                            <select name="ans_opt" id="edit_ans_opt" class="form-select">
                                <option value="a">Option A</option>
                                <option value="b">Option B</option>
                                <option value="c">Option C</option>
                                <option value="d">Option D</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <label class="form-label">Marks</label>
                            <input type="number" name="marks" id="edit_marks" class="form-control" min="1">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('ajax')
<script>
    $(document).ready(function() {
        // Generic AJAX Form Handler function
        function handleAjaxForm(formId, successCallback) {
            $(formId).submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action') || (formId === '#addQuestionForm' ? "{{ route('admin.test.question.add') }}" : "{{ url('admin/test/question') }}/" + $('#edit_ques_id').val() + "/update");
                let formData = form.serialize();

                // Clear previous errors
                form.find('.form-control, .form-select').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                $.ajax({
                    url: url,
                    type: form.attr('method') || 'POST',
                    data: formData + "&_token={{ csrf_token() }}",
                    success: function(response) {
                        if (successCallback) successCallback(response, form);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                let input = form.find('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').text(value[0]);
                            });
                        } else {
                            Swal.fire('Error', 'Something went wrong.', 'error');
                        }
                    }
                });
            });
        }

        // Test Details Form Handler
        handleAjaxForm('#testDetailsForm', function(response, form) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: response.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            });
        });

        @if($test)
        loadQuestions();

        // Load Questions
        function loadQuestions() {
            $.ajax({
                url: "{{ url('admin/test') }}/" + {{ $test->test_id }} + "/questions",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let rows = '';
                        let totalMarks = 0;
                        $.each(response.data, function(index, q) {
                            totalMarks += parseInt(q.marks);
                            rows += `
                                <tr>
                                    <td>${q.ques_text}</td>
                                    <td>
                                        A: ${q.opt_a}<br>
                                        B: ${q.opt_b}<br>
                                        C: ${q.opt_c}<br>
                                        D: ${q.opt_d}
                                    </td>
                                    <td><span class="badge bg-label-success">${q.ans_opt.toUpperCase()}</span></td>
                                    <td>${q.marks}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-question" 
                                            data-id="${q.ques_id}"
                                            data-text="${q.ques_text}"
                                            data-opta="${q.opt_a}"
                                            data-optb="${q.opt_b}"
                                            data-optc="${q.opt_c}"
                                            data-optd="${q.opt_d}"
                                            data-ans="${q.ans_opt}"
                                            data-marks="${q.marks}"
                                        >Edit</button>
                                        <button class="btn btn-sm btn-danger delete-question" data-id="${q.ques_id}">Delete</button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#questionsTable tbody').html(rows);
                        $('#displayTotalMarks').val(totalMarks);
                    }
                }
            });
        }

        // Add Question Form Handler
        handleAjaxForm('#addQuestionForm', function(response, form) {
            $('#addQuestionModal').modal('hide');
            form[0].reset();
            loadQuestions();
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: response.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        // Edit Question Form Handler
        handleAjaxForm('#editQuestionForm', function(response, form) {
            $('#editQuestionModal').modal('hide');
            loadQuestions();
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: response.message,
                showConfirmButton: false,
                timer: 1500
            });
        });

        // Open Edit Modal - No change needed here
        $(document).on('click', '.edit-question', function() {
            let btn = $(this);
            $('#edit_ques_id').val(btn.data('id'));
            $('#edit_ques_text').val(btn.data('text'));
            $('#edit_opt_a').val(btn.data('opta'));
            $('#edit_opt_b').val(btn.data('optb'));
            $('#edit_opt_c').val(btn.data('optc'));
            $('#edit_opt_d').val(btn.data('optd'));
            $('#edit_ans_opt').val(btn.data('ans'));
            $('#edit_marks').val(btn.data('marks'));
            
            // Clear Validation Errors on open
            $('#editQuestionForm').find('.form-control').removeClass('is-invalid');
            $('#editQuestionForm').find('.invalid-feedback').text('');
            
            $('#editQuestionModal').modal('show');
        });

        // Clear Add Modal Errors on Open
         $('#addQuestionModal').on('show.bs.modal', function () {
            $('#addQuestionForm').find('.form-control').removeClass('is-invalid');
            $('#addQuestionForm').find('.invalid-feedback').text('');
         });

        // Delete Question
        $(document).on('click', '.delete-question', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the question.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/test/question') }}/" + id + "/delete",
                        type: "DELETE",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            loadQuestions();
                            Swal.fire('Deleted!', response.message, 'success');
                        }
                    });
                }
            });
        });
        @endif
    });
</script>
@endpush