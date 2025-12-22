@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Content Manager')

@section('content')
    <div class="container-xxl container-p-y">

        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Trainer Panel /</span> Content Manager
        </h4>

        <!-- Search & Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Search Program</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" id="search" class="form-control"
                                placeholder="Search by Program Title or Org Name...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter by Mode</label>
                        <select id="filter_mode" class="form-select">
                            <option value="">All Modes</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking List -->
        <div class="row" id="bookings-container">
            <!-- Content loaded via AJAX -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('ajax')
    <script>
        $(document).ready(function () {
            loadBookings();

            let timeout = null;
            $('#search').on('keyup', function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    loadBookings();
                }, 500);
            });

            $('#filter_mode').on('change', function () {
                loadBookings();
            });

            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                loadBookings(page);
            });
        });

        function loadBookings(page = 1) {
            let search = $('#search').val();
            let mode = $('#filter_mode').val();

            $('#bookings-container').addClass('opacity-50');

            $.ajax({
                url: "{{ route('trainer.content-manager') }}?page=" + page,
                type: "GET",
                data: {
                    search: search,
                    filter_mode: mode
                },
                success: function (response) {
                    if (response.status) {
                        $('#bookings-container').html(response.html).removeClass('opacity-50');
                    }
                },
                error: function () {
                    $('#bookings-container').html('<div class="col-12 text-center text-danger">Failed to load data.</div>').removeClass('opacity-50');
                }
            });
        }
    </script>
@endpush