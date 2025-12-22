@if($activePrograms->isEmpty())
    <div class="text-center">
        <i class='bx bx-data text-muted' style="font-size: 4rem;"></i>
        <p class="mt-3 text-muted">No active programs found.</p>
    </div>
@else
    <div class="row g-4">
        @foreach($activePrograms as $req)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border shadow-none hover-shadow transition-3d-hover">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar avatar-xl me-3">
                                @if($req->program->image)
                                    <img src="{{ asset('storage/' . $req->program->image) }}" alt="Program Image" class="rounded">
                                @else
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class='bx bx-book-open bx-sm'></i>
                                    </span>
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <h5 class="mb-1 text-truncate" title="{{ $req->program->title }}">
                                    {{ $req->program->title }}
                                </h5>
                                <span class="badge bg-label-secondary">{{ optional($req->program->programType)->name }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-lighter rounded">
                            <div class="text-center">
                                <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Mode</small>
                                <span class="fw-bold text-dark text-capitalize">{{ $req->mode }}</span>
                            </div>
                            <div class="vr"></div>
                            <div class="text-center">
                                <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Duration</small>
                                <span class="fw-bold text-dark">{{ $req->program->duration }} Days</span>
                            </div>
                            <div class="vr"></div>
                            <div class="text-center">
                                <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Trainer</small>
                                <span class="fw-bold text-primary">
                                    @if($req->booking && $req->booking->trainer)
                                        {{ Str::limit($req->booking->trainer->name, 10) }}
                                    @else
                                        <span class="text-warning">Pending</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            @if($req->booking && $req->booking->trainer)
                                <button type="button" class="btn btn-outline-primary"
                                    onclick="showTrainerDetails({{ $req->booking->trainer->trainer_id }})">
                                    <i class="bx bx-user me-1"></i> Trainer Details
                                </button>
                                <a href="{{ route('org.active-programs.content', $req->booking->booking_id) }}"
                                    class="btn btn-primary">
                                    <i class="bx bx-folder-open me-1"></i> View Content
                                </a>
                            @else
                                <button disabled class="btn btn-secondary">Waiting for Trainer Assignment</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif