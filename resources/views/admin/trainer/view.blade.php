@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Trainers')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold"><span class="text-muted fw-light">Management /</span> Trainers</h4>
            {{-- <a href="#" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add Trainer
            </a> --}}
        </div>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Search by name, email, phone...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Organization Type</label>
                        <select id="orgTypeFilter" class="form-select">
                            <option value="">All Types</option>
                            <option value="corporate">Corporate</option>
                            <option value="educational">Educational</option>
                            <option value="government">Government</option>
                            <option value="ngo">NGO</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Training Mode</label>
                        <select id="trainingModeFilter" class="form-select">
                            <option value="">All Modes</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select id="verifiedFilter" class="form-select">
                            <option value="">All</option>
                            <option value="pending" selected>Pending</option>
                            <option value="verified">Verified</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trainers Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Trainers List</h5>
                <span class="badge bg-label-primary" id="totalCount">Total: 0</span>
            </div>
            <div class="card-body">
                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive" id="tableContainer">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Location</th>
                                <th>Org Type</th>
                                <th>Mode</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="trainersTableBody">
                            <!-- Data will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="text-center py-5 d-none">
                    <i class="bx bx-search-alt bx-lg text-muted mb-3"></i>
                    <h5 class="text-muted">No trainers found</h5>
                    <p class="text-muted">Try adjusting your search or filters</p>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <span class="text-muted" id="showingText">Showing 0 to 0 of 0 entries</span>
                    </div>
                    <nav>
                        <ul class="pagination mb-0" id="paginationContainer">
                            <!-- Pagination will be loaded here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="trainerDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Trainer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="trainerDetailsContent">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $(document).ready(function () {
            let currentPage = 1;
            let debounceTimer;

            // Load trainers on page load
            loadTrainers();

            // Search with debounce
            $('#searchInput').on('keyup', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    currentPage = 1;
                    loadTrainers();
                }, 500);
            });

            // Filter changes
            $('#orgTypeFilter, #trainingModeFilter, #verifiedFilter').on('change', function () {
                currentPage = 1;
                loadTrainers();
            });

            // Load trainers function
            function loadTrainers(page = 1) {
                currentPage = page;

                $('#loadingSpinner').removeClass('d-none');
                $('#tableContainer').addClass('d-none');
                $('#noResults').addClass('d-none');

                $.ajax({
                    url: "{{ route('admin.trainers.list') }}",
                    type: 'GET',
                    data: {
                        page: page,
                        search: $('#searchInput').val(),
                        org_type: $('#orgTypeFilter').val(),
                        training_mode: $('#trainingModeFilter').val(),
                        verified: $('#verifiedFilter').val()
                    },
                    success: function (response) {
                        $('#loadingSpinner').addClass('d-none');

                        if (response.data.length > 0) {
                            $('#tableContainer').removeClass('d-none');
                            renderTable(response.data);
                            renderPagination(response);
                            updateStats(response);
                        } else {
                            $('#noResults').removeClass('d-none');
                            $('#paginationContainer').html('');
                            $('#totalCount').text('Total: 0');
                            $('#showingText').text('Showing 0 to 0 of 0 entries');
                        }
                    },
                    error: function (xhr) {
                        $('#loadingSpinner').addClass('d-none');
                        $('#noResults').removeClass('d-none');
                        console.error('Error loading trainers:', xhr);
                    }
                });
            }

            // Render table rows
    function renderTable(trainers) {
        let html = '';
        
        trainers.forEach(function(trainer) {
            const profilePic = trainer.profile_pic 
                ? `{{ asset('storage/') }}/${trainer.profile_pic}`
                : `{{ asset('assets/img/avatars/default.png') }}`;
            
            let verifiedBadge = '';
            let actionButtons = '';

            switch(trainer.verified) {
                case 'verified':
                    verifiedBadge = '<span class="badge bg-label-success">Verified</span>';
                    actionButtons = `
                        <button class="btn btn-sm btn-icon btn-text-danger rounded-pill" onclick="suspendTrainer(${trainer.trainer_id}, 'suspend')" title="Suspend">
                            <i class="bx bx-block"></i>
                        </button>
                    `;
                    break;
                case 'suspended':
                    verifiedBadge = '<span class="badge bg-label-danger">Suspended</span>';
                    actionButtons = `
                        <button class="btn btn-sm btn-icon btn-text-success rounded-pill" onclick="suspendTrainer(${trainer.trainer_id}, 'activate')" title="Activate">
                            <i class="bx bx-check-circle"></i>
                        </button>
                    `;
                    break;
                case 'pending':
                default:
                    verifiedBadge = '<span class="badge bg-label-warning">Pending</span>';
                    actionButtons = `
                        <button class="btn btn-sm btn-icon btn-text-success rounded-pill" onclick="verifyTrainer(${trainer.trainer_id})" title="Verify">
                            <i class="bx bx-check"></i>
                        </button>
                    `;
                    break;
            }
            
            html += `
                <tr>
                    <td>
                        <img src="${profilePic}" alt="Avatar" class="rounded-circle" width="40" height="40">
                    </td>
                    <td>
                        <div class="fw-semibold">${trainer.name}</div>
                    </td>
                    <td>${trainer.email}</td>
                    <td>${trainer.phone}</td>
                    <td>
                        <small class="text-muted">${trainer.city}, ${trainer.state}</small>
                    </td>
                    <td><span class="badge bg-label-info">${trainer.for_org_type || 'N/A'}</span></td>
                    <td><span class="badge bg-label-primary">${trainer.training_mode || 'N/A'}</span></td>
                    <td>${verifiedBadge}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                                    onclick="viewTrainer(${trainer.trainer_id})" 
                                    title="View Details">
                                <i class="bx bx-show"></i>
                            </button>
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `;
        });
        
        $('#trainersTableBody').html(html);
    }

    // Render pagination
    function renderPagination(response) {
        let html = '';
        
        // Previous button
        if (response.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadTrainersPage(${response.current_page - 1}); return false;"><i class="tf-icon bx bx-chevron-left"></i></a></li>`;
        }
        
        // Page numbers
        for (let i = 1; i <= response.last_page; i++) {
            if (i === response.current_page) {
                html += `<li class="page-item active"><a class="page-link" href="#">${i}</a></li>`;
            } else if (i === 1 || i === response.last_page || (i >= response.current_page - 2 && i <= response.current_page + 2)) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadTrainersPage(${i}); return false;">${i}</a></li>`;
            } else if (i === response.current_page - 3 || i === response.current_page + 3) {
                html += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }
        }
        
        // Next button
        if (response.current_page < response.last_page) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadTrainersPage(${response.current_page + 1}); return false;"><i class="tf-icon bx bx-chevron-right"></i></a></li>`;
        }
        
        $('#paginationContainer').html(html);
    }

    // Update stats
    function updateStats(response) {
        const from = (response.current_page - 1) * response.per_page + 1;
        const to = Math.min(response.current_page * response.per_page, response.total);
        
        $('#totalCount').text(`Total: ${response.total}`);
        $('#showingText').text(`Showing ${from} to ${to} of ${response.total} entries`);
    }

    // Global function for pagination
    window.loadTrainersPage = function(page) {
        loadTrainers(page);
    };

    // View trainer details
    window.viewTrainer = function(trainerId) {
        $.ajax({
            url: `/admin/trainers/${trainerId}`,
            type: 'GET',
            success: function(trainer) {
                const profilePic = trainer.profile_pic 
                    ? `{{ asset('storage/') }}/${trainer.profile_pic}`
                    : `{{ asset('assets/img/avatars/default.png') }}`;
                
                let verifiedBadge = '';
                switch(trainer.verified) {
                    case 'verified': verifiedBadge = '<span class="badge bg-success">Verified</span>'; break;
                    case 'suspended': verifiedBadge = '<span class="badge bg-danger">Suspended</span>'; break;
                    case 'pending': verifiedBadge = '<span class="badge bg-warning">Pending Review</span>'; break;
                }
                
                const resumeLink = trainer.resume_link 
                    ? `<a href="${trainer.resume_link}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bx bx-link-external me-1"></i> View Resume</a>`
                    : '<span class="text-muted">Not provided</span>';
                
                const signedForm = trainer.signed_form_pdf 
                    ? `<a href="{{ asset('storage/') }}/${trainer.signed_form_pdf}" target="_blank" class="btn btn-sm btn-outline-info"><i class="bx bx-file-blank me-1"></i> View Signed Document</a>`
                    : '<span class="text-muted">Not uploaded</span>';
                
                let html = `
                    <div class="text-center mb-4">
                        <img src="${profilePic}" alt="Profile" class="rounded-circle mb-3 border border-3 border-light shadow-sm" width="120" height="120" style="object-fit: cover;">
                        <h5 class="mb-1">${trainer.name}</h5>
                        <div>${verifiedBadge}</div>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3"><i class="bx bx-info-circle me-1"></i> Contact Information</h6>
                            <p class="mb-1"><strong>Email:</strong> ${trainer.email}</p>
                            <p class="mb-1"><strong>Phone:</strong> ${trainer.phone}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3"><i class="bx bx-map me-1"></i> Address Details</h6>
                            <p class="mb-1">${trainer.addr_line1}</p>
                            ${trainer.addr_line2 ? `<p class="mb-1">${trainer.addr_line2}</p>` : ''}
                            <p class="mb-1">${trainer.city}, ${trainer.district}</p>
                            <p class="mb-1">${trainer.state} - ${trainer.pincode}</p>
                        </div>
                        
                        <div class="col-12">
                            <hr class="m-0">
                        </div>

                        <div class="col-12">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3"><i class="bx bx-briefcase me-1"></i> Professional Profile</h6>
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <div class="p-2 border rounded bg-light">
                                        <small class="text-muted d-block">Organization Type</small>
                                        <span class="fw-bold">${trainer.for_org_type?.toUpperCase() || 'N/A'}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="p-2 border rounded bg-light">
                                        <small class="text-muted d-block">Availability</small>
                                        <span class="fw-bold">${trainer.availability || 'N/A'}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="p-2 border rounded bg-light">
                                        <small class="text-muted d-block">Training Mode</small>
                                        <span class="fw-bold">${trainer.training_mode?.toUpperCase() || 'N/A'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">Biography</h6>
                            <div class="p-3 border rounded">
                                ${trainer.biodata || '<span class="text-muted">No bio available</span>'}
                            </div>
                        </div>
                        
                        ${trainer.achievements ? `
                        <div class="col-12">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">Achievements</h6>
                            <div class="p-3 border rounded">
                                ${trainer.achievements}
                            </div>
                        </div>
                        ` : ''}
                        
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">Resume link</h6>
                            ${resumeLink}
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">Signed PDF Form</h6>
                            ${signedForm}
                        </div>
                    </div>
                `;
                
                $('#trainerDetailsContent').html(html);
                $('#trainerDetailsModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to load trainer details', 'error');
            }
        });
    };

    // Verify Trainer
    window.verifyTrainer = function(trainerId) {
        Swal.fire({
            title: 'Verify Trainer?',
            text: "Are you sure you want to verify this trainer? They will gain access to the platform.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#71dd37',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Yes, verify!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/trainers/${trainerId}/verify`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Verified!', response.message, 'success');
                        loadTrainers(currentPage);
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to verify trainer', 'error');
                    }
                });
            }
        });
    };

    // Suspend/Activate Trainer
    window.suspendTrainer = function(trainerId, action) {
        const title = action === 'suspend' ? 'Suspend Trainer?' : 'Activate Trainer?';
        const text = action === 'suspend' 
            ? "Suspended trainers cannot access their dashboard or any platform features." 
            : "Activating this trainer will restore their access to the platform.";
        const confirmBtnColor = action === 'suspend' ? '#ff3e1d' : '#71dd37';

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmBtnColor,
            cancelButtonColor: '#8592a3',
            confirmButtonText: action === 'suspend' ? 'Yes, suspend!' : 'Yes, activate!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/trainers/${trainerId}/suspend`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire('Success!', response.message, 'success');
                        loadTrainers(currentPage);
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to update trainer status', 'error');
                    }
                });
            }
        });
    };
        });
    </script>
@endpush