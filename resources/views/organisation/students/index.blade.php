@extends('layouts.master', ['panel' => 'organisation'])
@section('title', 'Student Management')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Org /</span> Students</h4>

        <div class="card mb-4">
            <h5 class="card-header">Select Active Program</h5>
            <div class="card-body">
                <form method="GET" action="{{ route('org.students.index') }}">
                    <div class="row">
                        <div class="col-md-10">
                            <select name="booking_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Select a Program</option>
                                @foreach($activeBookings as $booking)
                                    <option value="{{ $booking->booking_id }}" {{ $selectedBookingId == $booking->booking_id ? 'selected' : '' }}>
                                        {{ $booking->requirement->program->title ?? $booking->requirement->program->program_name }} 
                                        (Max: {{ $booking->requirement->number_of_students }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($selectedBookingId)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Students List ({{ count($students) }} / {{ $selectedBooking->requirement->number_of_students }})</h5>
                <div>
                     <button class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bx bx-upload"></i> Import CSV
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="bx bx-plus"></i> Add Student
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->phone }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-status" type="checkbox" 
                                                data-id="{{ $student->candidate_id }}" 
                                                {{ $student->status === 'active' ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-student" 
                                            data-id="{{ $student->candidate_id }}"
                                            data-name="{{ $student->name }}"
                                            data-email="{{ $student->email }}"
                                            data-phone="{{ $student->phone }}"
                                        >Edit</button>
                                        <button class="btn btn-sm btn-danger delete-student" data-id="{{ $student->candidate_id }}">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No students added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @elseif(count($activeBookings) > 0)
            <div class="alert alert-info">Please select a program to manage students.</div>
        @else
            <div class="alert alert-warning">No active paid programs found.</div>
        @endif
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addStudentForm">
                <input type="hidden" name="booking_id" value="{{ $selectedBookingId }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStudentForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" minlength="6">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importForm" enctype="multipart/form-data">
                <input type="hidden" name="booking_id" value="{{ $selectedBookingId }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">CSV File</label>
                        <input type="file" name="file" class="form-control" accept=".csv, .txt" required>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Format: Name, Email, Phone, Password (No Header)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('ajax')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function handleAjaxForm(formId, url, successCallback) {
            $(formId).submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = new FormData(this); // Use FormData for both normal and file uploads

                // Clear previous errors
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                $.ajax({
                    url: url ? url : form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                let input = form.find('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.next('.invalid-feedback').text(value[0]);
                            });
                            
                            // If it's a general message error (not specific to a field in validation array)
                            if (xhr.responseJSON.message && !errors) {
                                Swal.fire('Error', xhr.responseJSON.message, 'error');
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            Swal.fire('Error', xhr.responseJSON.message, 'error');
                        } else {
                            Swal.fire('Error', 'Something went wrong.', 'error');
                        }
                    }
                });
            });
        }

        // Attach Handlers
        handleAjaxForm('#addStudentForm', "{{ route('org.students.store') }}");
        
        $('#editStudentForm').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let id = $('#edit_id').val();
            let url = "{{ url('org/students') }}/" + id + "/update";
            
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => location.reload());
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

        handleAjaxForm('#importForm', "{{ route('org.students.import') }}");

        $('.edit-student').click(function() {
            let btn = $(this);
            $('#edit_id').val(btn.data('id'));
            $('#edit_name').val(btn.data('name'));
            $('#edit_email').val(btn.data('email'));
            $('#edit_phone').val(btn.data('phone'));
            
            // Clear errors
            $('#editStudentForm').find('.form-control').removeClass('is-invalid');
            $('#editStudentForm').find('.invalid-feedback').text('');
            
            $('#editStudentModal').modal('show');
        });

        $('.delete-student').click(function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('org/students') }}/" + id,
                        type: 'DELETE',
                        success: (res) => {
                             Swal.fire('Deleted', res.message, 'success').then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });

        $('.toggle-status').change(function() {
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('org/students') }}/" + id + "/toggle-status",
                type: 'POST',
                error: (xhr) => {
                    $(this).prop('checked', !$(this).prop('checked')); // Revert
                    Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong.', 'error');
                }
            });
        });
    });
</script>
@endpush
