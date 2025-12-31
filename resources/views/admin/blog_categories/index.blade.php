@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Blog Categories')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin / Blogs /</span> Categories</h4>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Categories List</h5>
                    <button class="btn btn-primary" id="addCategoryBtn">Add New Category</button>
                </div>

                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" id="search" class="form-control" placeholder="Search by Name">
                        </div>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categories-table-body">
                                <!-- Rows loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3" id="pagination-links">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        @csrf
                        <input type="hidden" id="cat_id" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" id="slug" name="slug" class="form-control" placeholder="Auto-generated"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col mb-0">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $(document).ready(function () {
            fetchCategories();

            $('#search').on('keyup', function () {
                fetchCategories();
            });

            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                fetchCategories(page);
            });

            // Add
            $('#addCategoryBtn').click(function () {
                $('#categoryForm')[0].reset();
                $('#cat_id').val('');
                $('#modalTitle').text('Add Category');
                $('#categoryModal').modal('show');
                $('.form-control').removeClass('is-invalid');
            });

            // Edit
            $(document).on('click', '.edit-category', function () {
                let id = $(this).data('id');
                let url = "{{ route('admin.blog-categories.edit', ':id') }}".replace(':id', id);

                $.get(url, function (data) {
                    $('#cat_id').val(data.id);
                    $('#name').val(data.name);
                    $('#slug').val(data.slug);
                    $('#status').val(data.status);
                    $('#modalTitle').text('Edit Category');
                    $('#categoryModal').modal('show');
                    $('.form-control').removeClass('is-invalid');
                });
            });

            // Slug generation
            $('#name').on('keyup', function () {
                let name = $(this).val();
                let slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });

            // Save
            $('#saveBtn').click(function () {
                let id = $('#cat_id').val();
                let url = id ? "{{ route('admin.blog-categories.update', ':id') }}".replace(':id', id) : "{{ route('admin.blog-categories.store') }}";
                let type = "POST"; // Use POST for store and update (method override handled if needed, but here simple POST works for strict consistency or we can use PUT if we want to be RESTful, but POST is safer with Laravel usually unless hidden _method field is present. Wait, my route used POST for update. Good.

                let formData = $('#categoryForm').serialize();

                $.ajax({
                    url: url,
                    type: type, // My route uses POST for update.
                    data: formData,
                    success: function (response) {
                        $('#categoryModal').modal('hide');
                        Swal.fire('Success', response.success, 'success');
                        fetchCategories();
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                let input = $('#categoryForm [name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(value[0]);
                            });
                        }
                    }
                });
            });

            // Delete
            $(document).on('click', '.delete-category', function () {
                let id = $(this).data('id');
                let url = "{{ route('admin.blog-categories.destroy', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                Swal.fire('Deleted!', response.success, 'success');
                                fetchCategories();
                            }
                        });
                    }
                });
            });
        });

        function fetchCategories(page = 1) {
            let search = $('#search').val();
            $.ajax({
                url: "{{ route('admin.blog-categories.fetch') }}",
                type: "GET",
                data: { page: page, search: search },
                success: function (response) {
                    $('#categories-table-body').html(response.html);
                    $('#pagination-links').html(response.pagination);
                }
            });
        }
    </script>
@endpush