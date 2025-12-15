@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Program Types')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">
                <span class="text-muted fw-light">Programs /</span> Program Types
            </h4>
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="bx bx-plus me-1"></i> Add Program Type
            </button>
        </div>

        <!-- Search -->
        <div class="card mb-4">
            <div class="card-body">
                <input type="text" id="searchInput" class="form-control" placeholder="Search program types...">
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Program Types</h5>
                <span class="badge bg-label-primary" id="totalCount">Total: 0</span>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="programTypesBody"></tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <span id="showingText" class="text-muted"></span>
                    <ul class="pagination mb-0" id="pagination"></ul>
                </div>
            </div>
        </div>
    </div>

    @include('admin.program_types.modal')
@endsection

@push('ajax')
    <script>
        let page = 1;

        $(document).ready(function() {
            loadProgramTypes();

            $('#searchInput').on('keyup', function() {
                page = 1;
                loadProgramTypes();
            });

            $('#programTypeForm').submit(function(e) {
                e.preventDefault();
                saveProgramType();
            });
        });

        function loadProgramTypes(p = page) {
            $.get("{{ route('admin.program-types.list') }}", {
                page: p,
                search: $('#searchInput').val()
            }, function(res) {
                renderTable(res.data);
                renderPagination(res);
                $('#totalCount').text(`Total: ${res.total}`);
                $('#showingText').text(`Showing ${res.from} to ${res.to} of ${res.total}`);
            });
        }

        function renderTable(data) {
            let html = '';
            data.forEach(pt => {
                let img = pt.image ?
                    `{{ asset('storage') }}/${pt.image}` :
                    `{{ asset('assets/img/avatars/default.png') }}`;

                html += `
            <tr>
                <td><img src="${img}" class="rounded" width="40"></td>
                <td class="fw-semibold">${pt.name}</td>
                <td>${pt.description ?? '-'}</td>
                <td>
                    <button class="btn btn-sm btn-icon" onclick='editType(${JSON.stringify(pt)})'>
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-icon text-danger" onclick="deleteType(${pt.id})">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>`;
            });
            $('#programTypesBody').html(html);
        }

        function renderPagination(res) {
            let html = '';
            for (let i = 1; i <= res.last_page; i++) {
                html += `
            <li class="page-item ${i === res.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadProgramTypes(${i})">${i}</a>
            </li>`;
            }
            $('#pagination').html(html);
        }

        function openCreateModal() {
            $('#programTypeForm')[0].reset();
            $('#programTypeId').val('');
            $('#programTypeModal').modal('show');
        }

        function editType(pt) {
            $('#programTypeId').val(pt.id);
            $('#name').val(pt.name);
            $('#description').val(pt.description);
            $('#programTypeModal').modal('show');
        }

        function saveProgramType() {
            let id = $('#programTypeId').val();
            let url = id ? `/admin/program-types/${id}` : `/admin/program-types`;

            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('name', $('#name').val());
            formData.append('description', $('#description').val());

            if ($('#image')[0].files.length) {
                formData.append('image', $('#image')[0].files[0]);
            }

            if (id) formData.append('_method', 'PUT');

            // 🔥 Clear old errors
            clearValidationErrors();

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,

                success: function() {
                    $('#programTypeModal').modal('hide');
                    loadProgramTypes();
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        showValidationErrors(xhr.responseJSON.errors);
                    }
                }
            });
        }

        function clearValidationErrors() {
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function showValidationErrors(errors) {
            $.each(errors, function(field, messages) {
                let input = $('#' + field);
                input.addClass('is-invalid');
                input.next('.invalid-feedback').text(messages[0]);
            });
        }

        function openCreateModal() {
            $('#programTypeForm')[0].reset();
            $('#programTypeId').val('');
            clearValidationErrors();
            $('#programTypeModal').modal('show');
        }

        function editType(pt) {
            clearValidationErrors();
            $('#programTypeId').val(pt.id);
            $('#name').val(pt.name);
            $('#description').val(pt.description);
            $('#programTypeModal').modal('show');
        }


        function deleteType(id) {
            if (!confirm('Delete this program type?')) return;

            $.ajax({
                url: `/admin/program-types/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: () => loadProgramTypes()
            });
        }
    </script>
@endpush
