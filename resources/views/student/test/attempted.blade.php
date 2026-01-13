@extends('layouts.master', ['panel' => 'student'])
@section('title', 'Attempted Tests')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Student / Tests /</span> Attempted
            </h4>

            @if($attempts->isEmpty() && !request()->filled('search') && !request()->filled('program_id'))
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-file bx-lg text-muted mb-3"></i>
                        <h5 class="mb-2">No Attempted Tests</h5>
                        <p class="text-muted">You haven't attempted any tests yet.</p>
                        <a href="{{ route('student.tests.available') }}" class="btn btn-primary mt-3">
                            <i class="bx bx-play-circle me-1"></i> View Available Tests
                        </a>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-3">Your Test History</h5>
                        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                            <div class="col-md-4 user_role">
                                <select id="filter-program" class="form-select text-capitalize">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $prog)
                                        <option value="{{ $prog->program_id }}">{{ $prog->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 user_plan"></div> <!-- Spacer -->
                            <div class="col-md-4 user_status">
                                <input type="text" class="form-control" id="search-input" placeholder="Search Test Title...">
                            </div>
                        </div>
                    </div>
                    
                    <div id="table-container">
                        @include('student.test.partials.attempt_table')
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('student.tests.available') }}" class="btn btn-primary">
                        <i class="bx bx-play-circle me-1"></i> Attempt More Tests
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('footer-script')
<script>
    $(document).ready(function() {
        function fetchAttempts(page = 1) {
            let search = $('#search-input').val();
            let program_id = $('#filter-program').val();

            $.ajax({
                url: "{{ route('student.tests.attempted') }}?page=" + page,
                type: "GET",
                data: {
                    search: search,
                    program_id: program_id
                },
                success: function(response) {
                    $('#table-container').html(response);
                },
                error: function(xhr) {
                    console.error('Error fetching data');
                }
            });
        }

        // Keyup delay for search
        let timeout = null;
        $('#search-input').on('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                fetchAttempts();
            }, 500);
        });

        // Filter change
        $('#filter-program').on('change', function() {
            fetchAttempts();
        });

        // Pagination click
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            fetchAttempts(page);
        });
    });
</script>
@endpush