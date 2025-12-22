@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Manage Content')

@section('content')
    <div class="container-xxl container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <span class="text-muted fw-light">Content Manager /</span> {{ $booking->requirement->program->title }}
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-building me-1"></i> {{ $booking->organization->name }}
                </p>
            </div>
            <div>
                <a href="{{ route('trainer.content-manager') }}" class="btn btn-label-secondary me-2">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <a href="{{ route('trainer.content.add', $booking->booking_id) }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Add Content
                </a>
            </div>
        </div>

        <!-- Content Navigation -->
        <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-video" aria-controls="navs-video" aria-selected="true">
                        <i class="bx bx-video me-1"></i> Video
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1">
                            {{ $contents->where('content_type', 'video')->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pdf"
                        aria-controls="navs-pdf" aria-selected="false">
                        <i class="bx bxs-file-pdf me-1"></i> PDF
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1">
                            {{ $contents->where('content_type', 'pdf')->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-text"
                        aria-controls="navs-text" aria-selected="false">
                        <i class="bx bx-text me-1"></i> Text
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-secondary ms-1">
                            {{ $contents->where('content_type', 'text')->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-link"
                        aria-controls="navs-link" aria-selected="false">
                        <i class="bx bx-link me-1"></i> Link
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-info ms-1">
                            {{ $contents->where('content_type', 'link')->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-meeting"
                        aria-controls="navs-meeting" aria-selected="false">
                        <i class="bx bx-calendar-event me-1"></i> Meeting
                        <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-success ms-1">
                            {{ $contents->where('content_type', 'meeting')->count() }}
                        </span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Video Tab -->
                <div class="tab-pane fade show active" id="navs-video" role="tabpanel">
                    @forelse($contents->where('content_type', 'video') as $content)
                        @include('trainer.content-manager.partials.content-item', ['content' => $content])
                    @empty
                        <div class="text-center py-5">
                            <i class='bx bx-video-off text-muted' style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">No videos added yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- PDF Tab -->
                <div class="tab-pane fade" id="navs-pdf" role="tabpanel">
                    @forelse($contents->where('content_type', 'pdf') as $content)
                        @include('trainer.content-manager.partials.content-item', ['content' => $content])
                    @empty
                        <div class="text-center py-5">
                            <i class='bx bxs-file-pdf text-muted' style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">No PDFs added yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Text Tab -->
                <div class="tab-pane fade" id="navs-text" role="tabpanel">
                    @forelse($contents->where('content_type', 'text') as $content)
                        @include('trainer.content-manager.partials.content-item', ['content' => $content])
                    @empty
                        <div class="text-center py-5">
                            <i class='bx bx-text text-muted' style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">No text content added yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Link Tab -->
                <div class="tab-pane fade" id="navs-link" role="tabpanel">
                    @forelse($contents->where('content_type', 'link') as $content)
                        @include('trainer.content-manager.partials.content-item', ['content' => $content])
                    @empty
                        <div class="text-center py-5">
                            <i class='bx bx-link-alt text-muted' style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">No links added yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Meeting Tab -->
                <div class="tab-pane fade" id="navs-meeting" role="tabpanel">
                    @forelse($contents->where('content_type', 'meeting') as $content)
                        @include('trainer.content-manager.partials.content-item', ['content' => $content])
                    @empty
                        <div class="text-center py-5">
                            <i class='bx bx-calendar-x text-muted' style="font-size: 3rem;"></i>
                            <p class="mt-2 text-muted">No meetings scheduled.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection

@push('ajax')
    <script>
        const bookingId = {{ $booking->booking_id }};



        $(document).ready(function () {
            // ... (existing delete code) ...
            $('.delete-content').click(function () {
                var contentId = $(this).data('id');
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
                            url: '{{ url("trainer/content-manager") }}/' + contentId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Content has been deleted.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', 'Failed to delete content', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Something went wrong', 'error');
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush