@forelse($bookings as $booking)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2 text-truncate" title="{{ $booking->requirement->program->title }}">
                    {{ $booking->requirement->program->title }}
                </h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                        <a class="dropdown-item" href="{{ route('trainer.content.manage', $booking->booking_id) }}">Manage
                            Content</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded-circle bg-label-primary"><i class='bx bx-building'></i></span>
                    </div>
                    <span class="text-muted">{{ $booking->organization->name }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-label-info">{{ strtoupper($booking->requirement->mode ?? 'N/A') }}</span>
                    <small class="text-muted"><i class="bx bx-file"></i> {{ $booking->content_count ?? 0 }}
                        ContentItems</small>
                </div>

                <!-- Schedule -->
                @if($booking->requirement->schedule_start)
                    <small class="text-muted d-block mb-3">
                        <i class="bx bx-calendar me-1"></i>
                        {{ \Carbon\Carbon::parse($booking->requirement->schedule_start)->format('d M, Y') }}
                    </small>
                @else
                    <small class="text-muted d-block mb-3">Not Scheduled</small>
                @endif

                <a href="{{ route('trainer.content.manage', $booking->booking_id) }}" class="btn btn-outline-primary w-100">
                    <i class="bx bx-cog me-1"></i> Manage Content
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <div class="mb-3">
            <span class="avatar avatar-xl rounded-circle bg-label-secondary">
                <i class="bx bx-folder-open bx-lg"></i>
            </span>
        </div>
        <h4>No Programs Found</h4>
        <p class="text-muted">You are not enrolled in any programs matching your criteria.</p>
    </div>
@endforelse

<div class="col-12 mt-4">
    <div class="d-flex justify-content-center">
        {!! $bookings->links() !!}
    </div>
</div>