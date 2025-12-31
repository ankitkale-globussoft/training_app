@extends('layouts.master', ['panel' => 'admin'])
@section('title', isset($blog) ? 'Edit Blog' : 'Create Blog')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin / Blogs /</span>
                {{ isset($blog) ? 'Edit' : 'Create' }} Blog</h4>

            <div class="card mb-4">
                <h5 class="card-header">{{ isset($blog) ? 'Edit' : 'Create New' }} Blog</h5>
                <div class="card-body">
                    <form id="blogForm" enctype="multipart/form-data">
                        @csrf
                        @if(isset($blog))
                            <input type="hidden" name="id" value="{{ $blog->id }}">
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ $blog->title ?? '' }}" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="slug">Slug (Auto-generated)</label>
                                <input type="text" class="form-control" id="slug" name="slug"
                                    value="{{ $blog->slug ?? '' }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="category">Category</label>
                                <div class="input-group">
                                    <select class="form-select" id="category" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <!-- Categories loaded via AJAX -->
                                    </select>
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#createCategoryModal"><i class='bx bx-plus'></i></button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="draft" {{ (isset($blog) && $blog->status == 'draft') ? 'selected' : '' }}>
                                        Draft</option>
                                    <option value="published" {{ (isset($blog) && $blog->status == 'published') ? 'selected' : '' }}>Published</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="author">Author</label>
                                <input type="text" class="form-control" id="author" name="author"
                                    value="{{ $blog->author ?? Auth::user()->name ?? 'Admin' }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="image">Featured Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            @if(isset($blog) && $blog->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $blog->image) }}" alt="Preview" width="100" class="rounded">
                                </div>
                            @endif
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="content">Content</label>
                            <div id="editor-container" style="height: 300px;">
                                {!! $blog->content ?? '' !!}
                            </div>
                            <input type="hidden" name="content" id="content">
                            <div class="invalid-feedback"></div>
                        </div>

                        <hr>
                        <h5>SEO Settings</h5>

                        <div class="mb-3">
                            <label class="form-label" for="meta_title">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title"
                                value="{{ $blog->meta_title ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="meta_description">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description"
                                rows="3">{{ $blog->meta_description ?? '' }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" id="saveBtn">{{ isset($blog) ? 'Update' : 'Create' }}
                            Blog</button>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Category Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createCategoryForm">
                        @csrf
                        <div class="row">
                            <div class="col mb-3">
                                <label for="cat_name" class="form-label">Name</label>
                                <input type="text" id="cat_name" name="name" class="form-control" placeholder="Enter Name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-0">
                                <label for="cat_slug" class="form-label">Slug</label>
                                <input type="text" id="cat_slug" name="slug" class="form-control" placeholder="Auto-generated">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col mb-0">
                                <label for="cat_status" class="form-label">Status</label>
                                <select id="cat_status" name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        $(document).ready(function () {
            loadCategories();

            // Initialize Quill editor

            var quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'script': 'sub' }, { 'script': 'super' }],
                        [{ 'indent': '-1' }, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'font': [] }],
                        [{ 'align': [] }],
                        ['clean'],
                        ['link', 'image', 'video']
                    ]
                }
            });

            // Slug generation
            $('#title').on('keyup', function () {
                let title = $(this).val();
                let slug = title.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#slug').val(slug);
            });

            $('#blogForm').on('submit', function (e) {
                e.preventDefault();

                // Populate hidden content field from Quill
                $('#content').val(quill.root.innerHTML);

                let formData = new FormData(this);
                let url = "{{ isset($blog) ? route('admin.blogs.update', $blog->id) : route('admin.blogs.store') }}";

                // Clear previous errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.success,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            window.location.href = "{{ route('admin.blogs.index') }}";
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');

                                // For content (special handling because of Quill/hidden input)
                                if (key === 'content') {
                                    $('#editor-container').after('<div class="invalid-feedback d-block">' + value[0] + '</div>');
                                } else {
                                    input.siblings('.invalid-feedback').text(value[0]);
                                }
                            });
                            Swal.fire('Error!', 'Please fix the validation errors.', 'error');
                        } else {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    }
                });
            });
        });
            // Category Modal Logic
            $('#cat_name').on('keyup', function() {
                let name = $(this).val();
                let slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $('#cat_slug').val(slug);
            });

            $('#saveCategoryBtn').click(function() {
                let formData = $('#createCategoryForm').serialize();
                $.ajax({
                    url: "{{ route('admin.blog-categories.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#createCategoryModal').modal('hide');
                        $('#createCategoryForm')[0].reset();
                        loadCategories(response.id); // Reload and select new
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Category added successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                let input = $('#createCategoryForm [name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(value[0]);
                            });
                        }
                    }
                });
            });

            // Re-fetch logic (used in create_edit)
            function loadCategories(selectedId = null) {
                let currentCatId = "{{ $blog->category_id ?? '' }}";
                if(selectedId) currentCatId = selectedId; // If newly created, select it

                $.ajax({
                    url: "{{ route('admin.blog-categories.fetch-all') }}",
                    type: 'GET',
                    success: function(response) {
                        let options = '<option value="">Select Category</option>';
                        $.each(response, function(key, val) {
                            let selected = (currentCatId == val.id) ? 'selected' : '';
                            options += `<option value="${val.id}" ${selected}>${val.name}</option>`;
                        });
                        $('#category').html(options);
                    }
                });
            }
    </script>
@endpush