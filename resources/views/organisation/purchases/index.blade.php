@extends('layouts.master', ['panel' => 'organisation'])
@section('title', 'Purchases')

@section('content')
    <div class="container-xxl container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Dashboard /</span> Purchases
        </h4>

        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Purchase History</h5>
                <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4 user_role">
                        <input type="text" id="purchase_search" class="form-control"
                            placeholder="Search Transaction ID or Program...">
                    </div>
                    <div class="col-md-4 user_plan">
                        <select id="purchase_status_filter" class="form-select text-capitalize">
                            <option value="">All Statuses</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-4 user_status">
                        <select id="purchase_sort" class="form-select text-capitalize">
                            <option value="date_desc">Newest First</option>
                            <option value="date_asc">Oldest First</option>
                            <option value="amount_desc">Amount (High to Low)</option>
                            <option value="amount_asc">Amount (Low to High)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body mt-4">
                <div class="table-responsive" id="purchases_container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-center" id="purchases_pagination">
                <!-- Pagination -->
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        $(document).ready(function () {
            fetchPurchases();

            $('#purchase_search').on('keyup', function () {
                fetchPurchases();
            });

            $('#purchase_status_filter, #purchase_sort').on('change', function () {
                fetchPurchases();
            });

            $(document).on('click', '.pagination a', function (event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchPurchases(page);
            });
        });

        function fetchPurchases(page = 1) {
            let search = $('#purchase_search').val();
            let status = $('#purchase_status_filter').val();
            let sort = $('#purchase_sort').val();

            $.ajax({
                url: "{{ route('org.purchases.index') }}?page=" + page,
                type: "GET",
                data: {
                    search: search,
                    status: status,
                    sort: sort
                },
                success: function (response) {
                    if (response.status) {
                        $('#purchases_container').html(response.html);
                        $('#purchases_pagination').html(response.pagination);
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>
@endpush