@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Blog Management')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Blogs</h4>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Blogs List</h5>
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">Create New Blog</a>
                </div>

                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <input type="text" id="search" class="form-control" placeholder="Search by Title or Author">
                        </div>
                        <div class="col-md-2">
                         <select id="category" class="form-select">
                            <option value="">All Categories</option>
                            <!-- Categories loaded via AJAX -->
                        </select>
                    </div>
                        <div class="col-md-2">
                            <select id="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="date" class="form-control" placeholder="Filter by Date">
                        </div>
                        <div class="col-md-2">
                            <button id="filterBtn" class="btn btn-secondary w-100">Filter</button>
                        </div>
                    </div>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="blogs-table-body">
                                <!-- Rows will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3" id="pagination-links">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('ajax')
    <script>
        $(document).ready(function () {
            fetchBlogs();
            loadCategories(); // Fetch categories for filter

            $('#filterBtn').click(function () {
                fetchBlogs();
            });

            $('#search, #category, #status, #date').on('change keyup', function () {
                // Optional: Auto filter on change, or stick to button. Let's do auto for non-text, and debounce for text if we wanted.
                // For now, let's rely on the Filter button as per classic UX or just on change for selects.
                // fetchBlogs(); 
            });

            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                fetchBlogs(page);
            });

            $(document).on('click', '.delete-blog', function () {
                let id = $(this).data('id');
                let url = "{{ route('admin.blogs.destroy', ':id') }}".replace(':id', id);

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
                                fetchBlogs();
                            },
                            error: function (xhr) {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });

        function fetchBlogs(page = 1) {
            let search = $('#search').val();
            let category = $('#category').val();
            let status = $('#status').val();
            let date = $('#date').val();

            $.ajax({
                url: "{{ route('admin.blogs.fetch') }}",
                type: "GET",
                data: {
                    page: page,
                    search: search,
                    category: category,
                    status: status,
                    date: date
                },
                success: function (response) {
                    $('#blogs-table-body').html(response.html);
                    $('#pagination-links').html(response.pagination);
                },
                error: function (xhr) {
                    console.error(xhr);
                    alert('Failed to fetch data');
                }
            });
        }

        function loadCategories() {
            $.ajax({
                url: "{{ route('admin.blog-categories.fetch-all') }}",
                type: 'GET',
                success: function(response) {
                    let options = '<option value="">All Categories</option>';
                    $.each(response, function(key, val) {
                        options += `<option value="${val.id}">${val.name}</option>`;
                    });
                    $('#category').html(options);
                }
            });
        }
    </script>
@endpush