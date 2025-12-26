@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Open Training Requests')

@section('content')
    <div class="container-xxl container-p-y">

        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Trainings /</span> Open Training Requests
        </h4>

        <!-- Filters Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('trainer.trainings.open') }}" method="GET" class="row g-3">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label">Search Programs</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by title..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Mode -->
                    <div class="col-md-2">
                        <label class="form-label">Training Mode</label>
                        <select name="mode" class="form-select">
                            <option value="">All Modes</option>
                            <option value="online" {{ request('mode') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="offline" {{ request('mode') == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>

                    <!-- City -->
                    <div class="col-md-2">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" placeholder="Filter by city"
                            value="{{ request('city') }}">
                    </div>

                    <!-- Distance Range -->
                    <div class="col-md-2">
                        <label class="form-label">Range (For Offline)</label>
                        <select name="range" class="form-select">
                            <option value="">Any distance</option>
                            <option value="10" {{ request('range') == '10' ? 'selected' : '' }}>Within ~10km</option>
                            <option value="30" {{ request('range') == '30' ? 'selected' : '' }}>Within ~30km</option>
                            <option value="100" {{ request('range') == '100' ? 'selected' : '' }}>Within ~100km</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="col-md-2">
                        <label class="form-label">Sort By</label>
                        <select name="sort" class="form-select">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to
                                Low</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to
                                High</option>
                            <option value="students_high" {{ request('sort') == 'students_high' ? 'selected' : '' }}>Most
                                Students</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-1 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary d-block w-100">
                            <i class="bx bx-filter-alt"></i>
                        </button>
                        <a href="{{ route('trainer.trainings.open') }}" class="btn btn-label-secondary d-block w-100">
                            <i class="bx bx-refresh"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if ($requirements->count())
            <div class="row g-4 mb-4">
                @foreach ($requirements as $req)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">

                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">
                                        {{ $req->program->title }}
                                    </h5>
                                    <span class="badge bg-label-primary">
                                        {{ ucfirst($req->mode) }}
                                    </span>
                                </div>

                                <p class="text-muted small mb-2">
                                    {{ Str::limit($req->program->description, 90) }}
                                </p>

                                <ul class="list-unstyled mb-3">
                                    <li class="mb-1">
                                        <i class="bx bx-buildings"></i>
                                        <strong>Organisation:</strong>
                                        {{ $req->organisation->name }}
                                    </li>
                                    <li class="mb-1">
                                        <i class="bx bx-calendar"></i>
                                        <strong>Requested On:</strong>
                                        {{ $req->created_at->format('d M Y') }}
                                    </li>
                                    <li class="mb-1">
                                        <i class="bx bx-group"></i>
                                        <strong>Students:</strong>
                                        {{ $req->number_of_students }}
                                    </li>
                                    <li class="mb-1">
                                        <i class="bx bx-dollar"></i>
                                        <strong>Total Price:</strong>
                                        ₹{{ number_format($req->number_of_students * $req->program->cost, 2) }}
                                    </li>
                                    @if ($req->schedule_date)
                                        <li class="mb-1 text-nowrap">
                                            <i class="bx bx-calendar-event"></i>
                                            <strong>Schedule:</strong>
                                            {{ \Carbon\Carbon::parse($req->schedule_date)->format('d M Y') }} at
                                            {{ $req->schedule_time }}
                                        </li>
                                    @endif
                                    @if ($req->mode === 'offline')
                                        <li class="mb-1">
                                            <i class="bx bx-map-pin"></i>
                                            <strong>Location:</strong>
                                            {{ $req->organisation->city }}, {{ $req->organisation->pincode }}
                                        </li>
                                    @endif
                                    @if ($req->location)
                                        <li class="mb-1">
                                            <i class="bx bx-map"></i>
                                            <strong>Venue:</strong>
                                            {{ $req->location }}
                                        </li>
                                    @endif
                                    @if ($req->schedule_start)
                                        <li class="mb-1">
                                            <i class="bx bx-time"></i>
                                            <strong>Window:</strong>
                                            {{ $req->schedule_start }} – {{ $req->schedule_end }}
                                        </li>
                                    @endif
                                </ul>

                                <div class="mt-auto d-flex gap-2">
                                    <button class="btn btn-outline-primary w-100" onclick="viewDetails({{ $req->requirement_id }})">
                                        <i class="bx bx-show"></i> View
                                    </button>

                                    <button class="btn btn-success w-100" onclick="acceptTraining({{ $req->requirement_id }})">
                                        <i class="bx bx-check-circle"></i> Accept
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <div class="card-body">
                    {{ $requirements->links() }}
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-calendar-x" style="font-size:4rem;color:#ddd;"></i>
                    <h5 class="mt-3">No Open Training Requests</h5>
                    <p class="text-muted">Please check back later.</p>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('ajax')
    <script>
        function acceptTraining(requirementId) {
            Swal.fire({
                title: 'Accept Training?',
                text: 'You will be assigned to this training.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Accept',
                confirmButtonColor: '#28c76f'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('trainer.trainings.accept') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            requirement_id: requirementId
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status) {
                                Swal.fire('Accepted!', data.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        });
                }
            });
        }

        function viewDetails(id) {
            Swal.fire({
                icon: 'info',
                title: 'Details',
                text: 'You can extend this to a modal or page.'
            });
        }
    </script>
@endpush