@extends('layouts.master', ['panel' => 'organisation'])
@section('title', 'Active Programs')

@section('content')
    <div class="container-xxl container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Dashboard /</span> Active Programs
        </h4>

        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Your Active Training Programs</h5>
                <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4 user_role"> <!-- Search -->
                        <input type="text" id="active_program_search" class="form-control" placeholder="Search Program...">
                    </div>
                    <div class="col-md-4 user_plan"> <!-- Filter -->
                        <select id="active_program_type_filter" class="form-select text-capitalize">
                            <option value="">All Program Types</option>
                            @foreach($programTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body mt-4" id="active_programs_container">
                <!-- Content loaded via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-center" id="active_programs_pagination">
                <!-- Pagination -->
            </div>
        </div>
    </div>
@endsection

<!-- Trainer Details Modal -->
<div class="modal fade" id="trainerDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trainerModalTitle">Trainer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-4">
                    <img src="" id="trainerProfilePic" alt="Trainer" class="rounded-circle me-3" width="80" height="80"
                        style="object-fit:cover; display:none;">
                    <div id="trainerInitials"
                        class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center me-3"
                        style="width: 80px; height: 80px; font-size: 2rem; display:none;"></div>

                    <div>
                        <h4 class="mb-1" id="trainerName"></h4>
                        <p class="mb-0 text-muted"><i class="bx bx-map me-1"></i> <span id="trainerLocation"></span></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <small class="fw-semibold d-block text-uppercase mb-1">Contact Info</small>
                        <p class="mb-1"><i class="bx bx-envelope me-2"></i> <span id="trainerEmail"></span></p>
                        <p class="mb-0"><i class="bx bx-phone me-2"></i> <span id="trainerPhone"></span></p>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row">
                    <div class="col-12">
                        <small class="fw-semibold d-block text-uppercase mb-2">Bio & Achievements</small>
                        <p class="text-muted mb-2 small" id="trainerBio"></p>
                        <p class="text-muted small" id="trainerAchievements"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('ajax')
    <script>
        $(document).ready(function () {
            fetchActivePrograms();

            $('#active_program_search').on('keyup', function () {
                fetchActivePrograms();
            });

            $('#active_program_type_filter').on('change', function () {
                fetchActivePrograms();
            });

            $(document).on('click', '.pagination a', function (event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchActivePrograms(page);
            });
        });

        function fetchActivePrograms(page = 1) {
            let search = $('#active_program_search').val();
            let type = $('#active_program_type_filter').val();

            $.ajax({
                url: "{{ route('org.active-programs.index') }}?page=" + page,
                type: "GET",
                data: {
                    search: search,
                    program_type: type
                },
                success: function (response) {
                    if (response.status) {
                        $('#active_programs_container').html(response.html);
                        $('#active_programs_pagination').html(response.pagination);
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        function showTrainerDetails(trainerId) {
            // Show loading state or clear modal
            $('#trainerName').text('Loading...');
            $('#trainerLocation').text('');
            $('#trainerEmail').text('');
            $('#trainerPhone').text('');
            $('#trainerBio').text('');
            $('#trainerAchievements').text('');
            $('#trainerProfilePic').hide();
            $('#trainerInitials').hide();

            $('#trainerDetailsModal').modal('show');

            // Fetch details
            $.ajax({
                url: "{{ url('org/active-programs/trainer') }}/" + trainerId,
                type: "GET",
                success: function (response) {
                    if (response.status) {
                        const data = response.data;
                        $('#trainerName').text(data.name);
                        $('#trainerLocation').text((data.city || '') + (data.state ? ', ' + data.state : ''));
                        $('#trainerEmail').text(data.email);
                        $('#trainerPhone').text(data.phone || 'N/A');
                        $('#trainerBio').text(data.biodata || 'No biography available.');
                        $('#trainerAchievements').text(data.achievements || '');

                        if (data.profile_pic) {
                            $('#trainerProfilePic').attr('src', data.profile_pic).show();
                        } else {
                            $('#trainerInitials').text(data.name.charAt(0)).show();
                        }
                    }
                },
                error: function () {
                    $('#trainerName').text('Error fetching details');
                }
            });
        }

        function toggleNote(element) {
            let container = $(element).closest('.bg-label-secondary');
            let shortText = container.find('.note-short');
            let fullText = container.find('.note-full');

            if (shortText.is(':visible')) {
                shortText.addClass('d-none');
                fullText.removeClass('d-none');
                $(element).text('Read Less');
            } else {
                shortText.removeClass('d-none');
                fullText.addClass('d-none');
                $(element).text('Read More');
            }
        }
    </script>
@endpush