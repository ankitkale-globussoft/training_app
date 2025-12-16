@extends('layouts.master', ['panel' => 'organisation'])
@section('title', 'All Programs')

@section('content')
    <div class="container-xxl container-p-y">

        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Programs /</span> All Programs
        </h4>

        <!-- Filters Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('org.programs.index') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- Search -->
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" placeholder="Search programs..."
                                value="{{ request('search') }}">
                        </div>

                        <!-- Program Type -->
                        <div class="col-md-3">
                            <label class="form-label">Program Type</label>
                            <select class="form-select" name="program_type">
                                <option value="">All Types</option>
                                @foreach ($programTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ request('program_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Duration -->
                        <div class="col-md-2">
                            <label class="form-label">Max Duration (months)</label>
                            <input type="number" class="form-control" name="duration" placeholder="e.g., 12"
                                value="{{ request('duration') }}">
                        </div>

                        <!-- Cost Range -->
                        <div class="col-md-2">
                            <label class="form-label">Min Cost</label>
                            <input type="number" class="form-control" name="min_cost" placeholder="Min"
                                value="{{ request('min_cost') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Max Cost</label>
                            <input type="number" class="form-control" name="max_cost" placeholder="Max"
                                value="{{ request('max_cost') }}">
                        </div>

                        <!-- Buttons -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-search"></i> Apply Filters
                            </button>
                            <a href="{{ route('org.programs.index') }}" class="btn btn-label-secondary">
                                <i class="bx bx-reset"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Programs Grid -->
        @if ($programs->count() > 0)
            <div class="row g-4 mb-4">
                @foreach ($programs as $program)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            @if ($program->image)
                                <img class="card-img-top" src="{{ asset('storage/' . $program->image) }}"
                                    alt="{{ $program->title }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-label-primary d-flex align-items-center justify-content-center"
                                    style="height: 200px;">
                                    <i class="bx bx-book-open" style="font-size: 4rem;"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $program->title }}</h5>
                                    @if ($program->programType)
                                        <span class="badge bg-label-info">{{ $program->programType->name }}</span>
                                    @endif
                                </div>

                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($program->description, 100) }}
                                </p>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">
                                            <i class="bx bx-time-five"></i> Duration:
                                        </span>
                                        <span class="fw-semibold">{{ $program->duration }} months</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">
                                            <i class="bx bx-dollar"></i> Cost:
                                        </span>
                                        <span class="fw-semibold">${{ number_format($program->cost, 2) }}</span>
                                    </div>
                                    @if ($program->trainers->count() > 0)
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">
                                                <i class="bx bx-user"></i> Trainers:
                                            </span>
                                            <span class="fw-semibold">{{ $program->trainers->count() }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-auto">
                                    <button type="button" class="btn btn-outline-primary w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#programModal"
                                        onclick='showProgramDetails(@json($program))'>
                                        <i class="bx bx-show"></i> Explore
                                    </button>
                                    @php
                                        $isRequested = $program->trainingRequirements->isNotEmpty();
                                    @endphp

                                    @if ($isRequested)
                                        <button type="button" class="btn btn-success w-100 mb-2" disabled>
                                            <i class="bx bx-check-circle"></i> Requested
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-primary w-100 mb-2"
                                            onclick='requestProgram(@json($program))'>
                                            <i class="bx bx-cart"></i> Request Program
                                        </button>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sneat UI Style Pagination -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div
                            class="col-sm-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_info" role="status" aria-live="polite">
                                Showing {{ $programs->firstItem() }} to {{ $programs->lastItem() }} of
                                {{ $programs->total() }} entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_paginate paging_simple_numbers">
                                <ul class="pagination justify-content-center justify-content-md-end">
                                    {{-- Previous Page Link --}}
                                    @if ($programs->onFirstPage())
                                        <li class="paginate_button page-item previous disabled">
                                            <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                        </li>
                                    @else
                                        <li class="paginate_button page-item previous">
                                            <a href="{{ $programs->previousPageUrl() }}" class="page-link"><i
                                                    class="tf-icon bx bx-chevrons-left"></i></a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                        $start = max($programs->currentPage() - 2, 1);
                                        $end = min($start + 4, $programs->lastPage());
                                        $start = max($end - 4, 1);
                                    @endphp

                                    @if ($start > 1)
                                        <li class="paginate_button page-item">
                                            <a href="{{ $programs->url(1) }}" class="page-link">1</a>
                                        </li>
                                        @if ($start > 2)
                                            <li class="paginate_button page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                    @endif

                                    @for ($i = $start; $i <= $end; $i++)
                                        @if ($i == $programs->currentPage())
                                            <li class="paginate_button page-item active">
                                                <span class="page-link">{{ $i }}</span>
                                            </li>
                                        @else
                                            <li class="paginate_button page-item">
                                                <a href="{{ $programs->url($i) }}"
                                                    class="page-link">{{ $i }}</a>
                                            </li>
                                        @endif
                                    @endfor

                                    @if ($end < $programs->lastPage())
                                        @if ($end < $programs->lastPage() - 1)
                                            <li class="paginate_button page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                        <li class="paginate_button page-item">
                                            <a href="{{ $programs->url($programs->lastPage()) }}"
                                                class="page-link">{{ $programs->lastPage() }}</a>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($programs->hasMorePages())
                                        <li class="paginate_button page-item next">
                                            <a href="{{ $programs->nextPageUrl() }}" class="page-link"><i
                                                    class="tf-icon bx bx-chevrons-right"></i></a>
                                        </li>
                                    @else
                                        <li class="paginate_button page-item next disabled">
                                            <span class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-search-alt" style="font-size: 4rem; color: #ddd;"></i>
                    <h5 class="mt-3">No Programs Found</h5>
                    <p class="text-muted">Try adjusting your filters or search criteria.</p>
                </div>
            </div>
        @endif

    </div>

    <!-- Program Details Modal -->
    <div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="programModalLabel">Program Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="programModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('ajax')
    <script>
        function showProgramDetails(program) {
            const modalBody = document.getElementById('programModalBody');
            const modalTitle = document.getElementById('programModalLabel');


            console.log('Program data:', program);

            modalTitle.textContent = program.title;


            let trainersHtml = '';
            if (program.trainers && program.trainers.length > 0) {
                trainersHtml = `
            <div class="mb-4">
                <h6 class="fw-semibold mb-3">
                    <i class="bx bx-user-circle"></i> Trainers (${program.trainers.length})
                </h6>
                <div class="row g-3">
                    ${program.trainers.map(trainer => `
                                            <div class="col-md-6">
                                                <div class="card bg-label-secondary">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            ${trainer.profile_pic ? `
                                            <img src="{{ asset('storage') }}/${trainer.profile_pic}" 
                                                 alt="${trainer.name}" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        ` : `
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    ${trainer.name ? trainer.name.charAt(0).toUpperCase() : 'T'}
                                                </span>
                                            </div>
                                        `}
                                                            <div>
                                                                <h6 class="mb-0">${trainer.name || 'N/A'}</h6>
                                                                ${trainer.email ? `<small class="text-muted">${trainer.email}</small>` : ''}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                </div>
            </div>
        `;
            }

            modalBody.innerHTML = `
        ${program.image ? `
                                <img src="{{ asset('storage') }}/${program.image}" class="img-fluid rounded mb-4" 
                                     alt="${program.title}" style="max-height: 300px; width: 100%; object-fit: cover;">
                            ` : ''}
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="badge badge-center rounded-pill bg-label-primary w-px-50 h-px-50">
                            <i class="bx bx-time-five bx-sm"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Duration</h6>
                        <small class="text-muted">${program.duration} months</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="badge badge-center rounded-pill bg-label-success w-px-50 h-px-50">
                            <i class="bx bx-dollar bx-sm"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Cost</h6>
                        <small class="text-muted">$${parseFloat(program.cost).toFixed(2)}</small>
                    </div>
                </div>
            </div>
            ${program.program_type ? `
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <span class="badge badge-center rounded-pill bg-label-info w-px-50 h-px-50">
                                                    <i class="bx bx-category bx-sm"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">Type</h6>
                                                <small class="text-muted">${program.program_type.name}</small>
                                            </div>
                                        </div>
                                    </div>
                                ` : ''}
        </div>

        <div class="mb-4">
            <h6 class="fw-semibold mb-2">
                <i class="bx bx-detail"></i> Description
            </h6>
            <p class="text-muted">${program.description || 'No description available.'}</p>
        </div>

        ${trainersHtml}
    `;
        }
    </script>

    <script>
        function requestProgram(program) {
            Swal.fire({
                title: 'Request Program',
                html: `
            <div class="text-start">
                <label class="form-label">Select Training Mode</label>
                <select id="training_mode" class="form-select">
                    <option value="">-- Select Mode --</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>
                <div id="mode_error" class="text-danger mt-1" style="display:none;">
                    Please select a training mode
                </div>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Request',
                preConfirm: () => {
                    const mode = document.getElementById('training_mode').value;

                    if (!mode) {
                        document.getElementById('mode_error').style.display = 'block';
                        return false;
                    }

                    return {
                        mode: mode,
                        program_id: program.program_id
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitProgramRequest(result.value);
                }
            });
        }

        function submitProgramRequest(data) {
            fetch("{{ route('org.programs.request') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content")
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(res => {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                });
        }
    </script>
@endpush
