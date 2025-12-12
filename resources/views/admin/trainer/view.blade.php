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
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, phone...">
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
                        <option value="1">Verified</option>
                        <option value="0">Not Verified</option>
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
$(document).ready(function() {
    let currentPage = 1;
    let debounceTimer;

    // Load trainers on page load
    loadTrainers();

    // Search with debounce
    $('#searchInput').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            currentPage = 1;
            loadTrainers();
        }, 500);
    });

    // Filter changes
    $('#orgTypeFilter, #trainingModeFilter, #verifiedFilter').on('change', function() {
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
            success: function(response) {
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
            error: function(xhr) {
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
            
            const verifiedBadge = trainer.verified 
                ? '<span class="badge bg-label-success">Verified</span>'
                : '<span class="badge bg-label-warning">Not Verified</span>';
            
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
                        <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                                onclick="viewTrainer(${trainer.trainer_id})" 
                                title="View Details">
                            <i class="bx bx-show"></i>
                        </button>
                        <a href="/admin/trainers/edit/${trainer.trainer_id}" 
                           class="btn btn-sm btn-icon btn-text-secondary rounded-pill" 
                           title="Edit">
                            <i class="bx bx-edit"></i>
                        </a>
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
                
                const verifiedBadge = trainer.verified 
                    ? '<span class="badge bg-success">Verified</span>'
                    : '<span class="badge bg-warning">Not Verified</span>';
                
                const resumeLink = trainer.resume_link 
                    ? `<a href="${trainer.resume_link}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bx bx-link-external me-1"></i> View Resume</a>`
                    : '<span class="text-muted">Not provided</span>';
                
                const signedForm = trainer.signed_form_pdf 
                    ? `<a href="{{ asset('storage/') }}/${trainer.signed_form_pdf}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bx bx-file-blank me-1"></i> View Form</a>`
                    : '<span class="text-muted">Not uploaded</span>';
                
                let html = `
                    <div class="text-center mb-4">
                        <img src="${profilePic}" alt="Profile" class="rounded-circle mb-3" width="120" height="120">
                        <h5 class="mb-1">${trainer.name}</h5>
                        ${verifiedBadge}
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-primary">Contact Information</h6>
                            <p class="mb-1"><strong>Email:</strong> ${trainer.email}</p>
                            <p class="mb-1"><strong>Phone:</strong> ${trainer.phone}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Address</h6>
                            <p class="mb-1">${trainer.addr_line1}</p>
                            ${trainer.addr_line2 ? `<p class="mb-1">${trainer.addr_line2}</p>` : ''}
                            <p class="mb-1">${trainer.city}, ${trainer.district}</p>
                            <p class="mb-1">${trainer.state} - ${trainer.pincode}</p>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary">Professional Details</h6>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <small class="text-muted">Organization Type</small>
                                    <p class="mb-0 fw-semibold">${trainer.for_org_type || 'N/A'}</p>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <small class="text-muted">Availability</small>
                                    <p class="mb-0 fw-semibold">${trainer.availability || 'N/A'}</p>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <small class="text-muted">Training Mode</small>
                                    <p class="mb-0 fw-semibold">${trainer.training_mode || 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary">Bio</h6>
                            <p>${trainer.biodata || 'Not provided'}</p>
                        </div>
                        
                        ${trainer.achievements ? `
                        <div class="col-12">
                            <h6 class="text-primary">Achievements</h6>
                            <p>${trainer.achievements}</p>
                        </div>
                        ` : ''}
                        
                        <div class="col-md-6">
                            <h6 class="text-primary">Resume</h6>
                            ${resumeLink}
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Signed Form</h6>
                            ${signedForm}
                        </div>
                    </div>
                `;
                
                $('#trainerDetailsContent').html(html);
                $('#trainerDetailsModal').modal('show');
            },
            error: function(xhr) {
                alert('Error loading trainer details');
                console.error(xhr);
            }
        });
    };
});
</script>
@endpush