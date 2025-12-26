@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Organisations Payments')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Payments /</span> Organisation Payments
        </h4>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bx bx-filter-alt me-2"></i>Filters
                </h5>
                <button type="button" class="btn btn-sm btn-label-secondary" onclick="resetFilters()">
                    <i class="bx bx-reset"></i> Reset
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" placeholder="Org name, Transaction ID...">
                    </div>

                    <!-- Payment Status -->
                    <div class="col-md-2">
                        <label class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <!-- Booking Status -->
                    <div class="col-md-2">
                        <label class="form-label">Booking Status</label>
                        <select class="form-select" id="booking_status">
                            <option value="">All Status</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>

                    <!-- Amount Range -->
                    <div class="col-md-2">
                        <label class="form-label">Min Amount</label>
                        <input type="number" class="form-control" id="min_amount" placeholder="0">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Max Amount</label>
                        <input type="number" class="form-control" id="max_amount" placeholder="100000">
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date">
                    </div>

                    <!-- Sort -->
                    <div class="col-md-3">
                        <label class="form-label">Sort By</label>
                        <select class="form-select" id="sort_by">
                            <option value="created_at">Date</option>
                            <option value="amount">Amount</option>
                            <option value="org_name">Organization</option>
                            <option value="payment_status">Payment Status</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Order</label>
                        <select class="form-select" id="sort_order">
                            <option value="desc">Descending</option>
                            <option value="asc">Ascending</option>
                        </select>
                    </div>

                    <!-- Apply Button -->
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="loadPayments()">
                            <i class="bx bx-search"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bx bx-wallet me-2"></i>Payment Records
                </h5>
                <div>
                    <span class="badge bg-label-primary" id="total_count">0 records</span>
                    <span class="badge bg-label-success ms-2" id="total_amount">₹0.00</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Organization</th>
                                <th>Trainer</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Booking Status</th>
                                <th>Transaction ID</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="payments_table_body">
                            <!-- Loading State -->
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Loading payment records...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div
                        class="col-sm-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="dataTables_info" id="pagination_info">
                            Showing 0 to 0 of 0 entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_paginate paging_simple_numbers">
                            <ul class="pagination justify-content-center justify-content-md-end" id="pagination_links">
                                <!-- Pagination will be generated here -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="payment_details_body">
                    <!-- Details will be loaded here -->
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
        let currentPage = 1;

        // Load payments on page load
        $(document).ready(function () {
            loadPayments();

            // Trigger search on Enter key
            $('#search').on('keypress', function (e) {
                if (e.which === 13) {
                    loadPayments();
                }
            });
        });

        function loadPayments(page = 1) {
            currentPage = page;

            const filters = {
                search: $('#search').val(),
                payment_status: $('#payment_status').val(),
                booking_status: $('#booking_status').val(),
                min_amount: $('#min_amount').val(),
                max_amount: $('#max_amount').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                sort_by: $('#sort_by').val(),
                sort_order: $('#sort_order').val(),
                page: page
            };

            // Show loading
            $('#payments_table_body').html(`
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading payment records...</p>
                </td>
            </tr>
        `);

            $.ajax({
                url: '{{ route("admin.payments.data") }}',
                method: 'GET',
                data: filters,
                success: function (response) {
                    if (response.status) {
                        renderPaymentsTable(response.data.data);
                        renderPagination(response.data);
                        updateStats(response.stats);
                    } else {
                        showError('Failed to load payments');
                    }
                },
                error: function (xhr) {
                    showError('Error loading payments: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }

        function renderPaymentsTable(payments) {
            const tbody = $('#payments_table_body');

            if (payments.length === 0) {
                tbody.html(`
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bx bx-search-alt" style="font-size: 4rem; color: #ddd;"></i>
                        <h5 class="mt-3">No Payments Found</h5>
                        <p class="text-muted">Try adjusting your filters</p>
                    </td>
                </tr>
            `);
                return;
            }

            let html = '';
            payments.forEach(payment => {
                const paymentStatusBadge = getPaymentStatusBadge(payment.payment_status);
                const bookingStatusBadge = getBookingStatusBadge(payment.booking_status);

                html += `
                <tr>
                    <td><strong>#${payment.booking_id}</strong></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                ${payment.organization.org_image ?
                        `<img src="{{ asset('storage') }}/${payment.organization.org_image}" class="rounded-circle">` :
                        `<span class="avatar-initial rounded-circle bg-label-primary">${payment.organization.name.charAt(0)}</span>`
                    }
                            </div>
                            <div>
                                <h6 class="mb-0">${payment.organization.name}</h6>
                                <small class="text-muted">${payment.organization.email || 'N/A'}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        ${payment.trainer ? `
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    ${payment.trainer.profile_pic ?
                            `<img src="{{ asset('storage') }}/${payment.trainer.profile_pic}" class="rounded-circle">` :
                            `<span class="avatar-initial rounded-circle bg-label-info">${payment.trainer.name.charAt(0)}</span>`
                        }
                                </div>
                                <div>
                                    <h6 class="mb-0">${payment.trainer.name}</h6>
                                    <small class="text-muted">${payment.trainer.email || 'N/A'}</small>
                                </div>
                            </div>
                        ` : '<span class="text-muted">Not Assigned</span>'}
                    </td>
                    <td><strong class="text-success">₹${parseFloat(payment.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</strong></td>
                    <td>${paymentStatusBadge}</td>
                    <td>${bookingStatusBadge}</td>
                    <td>
                        ${payment.transaction_id && payment.transaction_id !== 'failed' ?
                        `<code class="text-xs">${payment.transaction_id.substring(0, 20)}${payment.transaction_id.length > 20 ? '...' : ''}</code>` :
                        '<span class="text-muted">N/A</span>'
                    }
                    </td>
                    <td>
                        <small>${new Date(payment.created_at).toLocaleDateString('en-IN')}</small><br>
                        <small class="text-muted">${new Date(payment.created_at).toLocaleTimeString('en-IN')}</small>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-icon btn-outline-primary" onclick="viewPaymentDetails(${payment.booking_id})" title="View Details">
                            <i class="bx bx-show"></i>
                        </button>
                    </td>
                </tr>
            `;
            });

            tbody.html(html);
        }

        function renderPagination(data) {
            const paginationInfo = $('#pagination_info');
            const paginationLinks = $('#pagination_links');

            // Update info
            paginationInfo.html(`Showing ${data.from || 0} to ${data.to || 0} of ${data.total} entries`);

            if (data.last_page <= 1) {
                paginationLinks.html('');
                return;
            }

            let html = '';

            // Previous button
            html += `
            <li class="paginate_button page-item previous ${data.current_page === 1 ? 'disabled' : ''}">
                ${data.current_page === 1 ?
                    '<span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>' :
                    `<a href="javascript:void(0)" onclick="loadPayments(${data.current_page - 1})" class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></a>`
                }
            </li>
        `;

            // Page numbers
            const start = Math.max(data.current_page - 2, 1);
            const end = Math.min(start + 4, data.last_page);
            const adjustedStart = Math.max(end - 4, 1);

            if (adjustedStart > 1) {
                html += `<li class="paginate_button page-item"><a href="javascript:void(0)" onclick="loadPayments(1)" class="page-link">1</a></li>`;
                if (adjustedStart > 2) {
                    html += `<li class="paginate_button page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            for (let i = adjustedStart; i <= end; i++) {
                html += `
                <li class="paginate_button page-item ${i === data.current_page ? 'active' : ''}">
                    ${i === data.current_page ?
                        `<span class="page-link">${i}</span>` :
                        `<a href="javascript:void(0)" onclick="loadPayments(${i})" class="page-link">${i}</a>`
                    }
                </li>
            `;
            }

            if (end < data.last_page) {
                if (end < data.last_page - 1) {
                    html += `<li class="paginate_button page-item disabled"><span class="page-link">...</span></li>`;
                }
                html += `<li class="paginate_button page-item"><a href="javascript:void(0)" onclick="loadPayments(${data.last_page})" class="page-link">${data.last_page}</a></li>`;
            }

            // Next button
            html += `
            <li class="paginate_button page-item next ${data.current_page === data.last_page ? 'disabled' : ''}">
                ${data.current_page === data.last_page ?
                    '<span class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></span>' :
                    `<a href="javascript:void(0)" onclick="loadPayments(${data.current_page + 1})" class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></a>`
                }
            </li>
        `;

            paginationLinks.html(html);
        }

        function updateStats(stats) {
            $('#total_count').text(`${stats.total_records} records`);
            $('#total_amount').text(`₹${parseFloat(stats.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}`);
        }

        function getPaymentStatusBadge(status) {
            const badges = {
                'pending': '<span class="badge bg-label-warning">Pending</span>',
                'completed': '<span class="badge bg-label-success">Paid</span>',
                'failed': '<span class="badge bg-label-danger">Failed</span>'
            };
            return badges[status] || '<span class="badge bg-label-secondary">Unknown</span>';
        }

        function getBookingStatusBadge(status) {
            const badges = {
                'assigned': '<span class="badge bg-label-info">Assigned</span>',
                'in_progress': '<span class="badge bg-label-primary">In Progress</span>',
                'completed': '<span class="badge bg-label-success">Completed</span>',
                'canceled': '<span class="badge bg-label-danger">Canceled</span>'
            };
            return badges[status] || '<span class="badge bg-label-secondary">Unknown</span>';
        }

        function viewPaymentDetails(bookingId) {
            const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
            const modalBody = $('#payment_details_body');

            modalBody.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3">Loading details...</p>
            </div>
        `);

            modal.show();

            $.ajax({
                url: '{{ url("admin/payments") }}/' + bookingId,
                method: 'GET',
                success: function (response) {
                    if (response.status) {
                        renderPaymentDetails(response.data);
                    } else {
                        modalBody.html('<div class="alert alert-danger">Failed to load details</div>');
                    }
                },
                error: function () {
                    modalBody.html('<div class="alert alert-danger">Error loading payment details</div>');
                }
            });
        }

        function renderPaymentDetails(payment) {
            const html = `
            <div class="row g-3">
                <div class="col-12">
                    <div class="card bg-label-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="mb-1">Booking #${payment.booking_id}</h5>
                                    <p class="mb-0 text-muted">Created: ${new Date(payment.created_at).toLocaleString('en-IN')}</p>
                                </div>
                                <div class="text-end">
                                    <h4 class="mb-1 text-success">₹${parseFloat(payment.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</h4>
                                    ${getPaymentStatusBadge(payment.payment_status)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="bx bx-building me-2"></i>Organization</h6>
                            <p class="mb-1"><strong>Name:</strong> ${payment.organization.name}</p>
                            <p class="mb-1"><strong>Email:</strong> ${payment.organization.email || 'N/A'}</p>
                            <p class="mb-0"><strong>Mobile:</strong> ${payment.organization.mobile || 'N/A'}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="bx bx-user me-2"></i>Trainer</h6>
                            ${payment.trainer ? `
                                <p class="mb-1"><strong>Name:</strong> ${payment.trainer.name}</p>
                                <p class="mb-1"><strong>Email:</strong> ${payment.trainer.email || 'N/A'}</p>
                                <p class="mb-0"><strong>Phone:</strong> ${payment.trainer.phone || 'N/A'}</p>
                            ` : '<p class="text-muted">Not assigned yet</p>'}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="bx bx-info-circle me-2"></i>Booking Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Booking Status:</strong> ${getBookingStatusBadge(payment.booking_status)}</p>
                                    <p class="mb-2"><strong>Payment Status:</strong> ${getPaymentStatusBadge(payment.payment_status)}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Transaction ID:</strong><br><code>${payment.transaction_id || 'N/A'}</code></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                ${payment.requirement ? `
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title mb-3"><i class="bx bx-book-open me-2"></i>Program Details</h6>
                                <p class="mb-1"><strong>Program:</strong> ${payment.requirement.program?.title || 'N/A'}</p>
                                <p class="mb-1"><strong>Mode:</strong> ${payment.requirement.mode ? payment.requirement.mode.toUpperCase() : 'N/A'}</p>
                                <p class="mb-1"><strong>Price:</strong> ₹${parseFloat(payment.requirement.program?.cost || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })} / student</p>
                                <p class="mb-1"><strong>Students:</strong> ${payment.requirement.number_of_students || 'N/A'}</p>
                                ${payment.requirement.schedule_date ? `<p class="mb-0"><strong>Schedule:</strong> ${new Date(payment.requirement.schedule_date).toLocaleDateString('en-IN')} at ${payment.requirement.schedule_time}</p>` : ''}
                                ${payment.requirement.schedule_start ? `<p class="mb-0"><strong>Alternative:</strong> ${new Date(payment.requirement.schedule_start).toLocaleDateString('en-IN')} to ${new Date(payment.requirement.schedule_end).toLocaleDateString('en-IN')}</p>` : ''}
                            </div>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;

            $('#payment_details_body').html(html);
        }

        function resetFilters() {
            $('#search').val('');
            $('#payment_status').val('');
            $('#booking_status').val('');
            $('#min_amount').val('');
            $('#max_amount').val('');
            $('#from_date').val('');
            $('#to_date').val('');
            $('#sort_by').val('created_at');
            $('#sort_order').val('desc');
            loadPayments();
        }

        function showError(message) {
            $('#payments_table_body').html(`
            <tr>
                <td colspan="9" class="text-center py-5">
                    <i class="bx bx-error-circle text-danger" style="font-size: 4rem;"></i>
                    <h5 class="mt-3 text-danger">Error</h5>
                    <p class="text-muted">${message}</p>
                </td>
            </tr>
        `);
        }
    </script>
@endpush