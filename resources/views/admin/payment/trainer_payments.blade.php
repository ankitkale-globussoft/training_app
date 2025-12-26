@extends('layouts.master', ['panel' => 'admin'])
@section('title', 'Trainer Payments')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Payments /</span> Trainer Payments
        </h4>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-pending" aria-controls="navs-pills-pending" aria-selected="true">
                        <i class="bx bx-time me-1"></i> Pending Requests
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-history" aria-controls="navs-pills-history" aria-selected="false">
                        <i class="bx bx-history me-1"></i> Payment History
                    </button>
                </li>
            </ul>
            <div class="tab-content shadow-none p-0 bg-transparent">
                <!-- Pending Requests Tab -->
                <div class="tab-pane fade show active" id="navs-pills-pending" role="tabpanel">
                    <div class="card">
                        <h5 class="card-header">Pending Requests</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Trainer</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($pendingRequests as $req)
                                        <tr>
                                            <td>{{ $req->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        @if ($req->trainer->profile_pic)
                                                            <img src="{{ asset('storage/' . $req->trainer->profile_pic) }}"
                                                                alt="Avatar" class="rounded-circle">
                                                        @else
                                                            <span
                                                                class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($req->trainer->name, 0, 2)) }}</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $req->trainer->name }}</h6>
                                                        <small class="text-muted">{{ $req->trainer->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-bold">₹{{ number_format($req->amount, 2) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info me-1"
                                                    onclick="viewBankDetails({{ $req->trainer->trainer_id }})">
                                                    <i class="bx bx-id-card"></i> Account
                                                </button>
                                                <button class="btn btn-sm btn-success me-1"
                                                    onclick="openActionModal({{ $req->request_id }}, 'approved')">
                                                    <i class="bx bx-check"></i> Pay
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="openActionModal({{ $req->request_id }}, 'rejected')">
                                                    <i class="bx bx-x"></i> Reject
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No pending requests.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{ $pendingRequests->links() }}
                        </div>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" id="navs-pills-history" role="tabpanel">
                    <div class="card">
                        <h5 class="card-header">Payment History</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Updated Date</th>
                                        <th>Trainer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Proof</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($history as $req)
                                        <tr>
                                            <td>{{ $req->updated_at->format('d M Y, h:i A') }}</td>
                                            <td>{{ $req->trainer->name }}</td>
                                            <td class="fw-bold">₹{{ number_format($req->amount, 2) }}</td>
                                            <td>
                                                @if ($req->status === 'approved')
                                                    <span class="badge bg-label-success">Approved</span>
                                                @else
                                                    <span class="badge bg-label-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($req->payment_proof)
                                                    <a href="{{ asset('storage/' . $req->payment_proof) }}" target="_blank"
                                                        class="btn btn-xs btn-outline-primary">
                                                        <i class="bx bx-file"></i> View
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($req->admin_note)
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ $req->admin_note }}">
                                                        <i class="bx bx-info-circle text-muted"></i>
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No history found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{ $history->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Details Modal -->
    <div class="modal fade" id="bankDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bank Account Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="bankDetailsBody">
                    <div class="text-center py-3">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal (Approve/Reject) -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.payments.update-status') }}" method="POST" enctype="multipart/form-data"
                    onsubmit="return submitActionForm(this)">
                    @csrf
                    <input type="hidden" name="request_id" id="actionRequestId">
                    <input type="hidden" name="status" id="actionStatus">

                    <div class="modal-header">
                        <h5 class="modal-title" id="actionModalTitle">Process Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="admin_note" class="form-label">Note (Optional)</label>
                            <textarea class="form-control" name="admin_note" id="admin_note" rows="3"
                                placeholder="Add a note..."></textarea>
                        </div>
                        <div class="mb-3 d-none" id="proofUploadDiv">
                            <label for="payment_proof" class="form-label">Payment Proof (Image/PDF)</label>
                            <input class="form-control" type="file" name="payment_proof" id="payment_proof"
                                accept="image/*,.pdf">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="actionSubmitBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        function submitActionForm(form) {
            const btn = $('#actionSubmitBtn');
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...');
            return true;
        }
        function viewBankDetails(trainerId) {
            $('#bankDetailsModal').modal('show');
            $('#bankDetailsBody').html('<div class="text-center py-3">Loading...</div>');

            $.ajax({
                url: "{{ route('admin.payments.bank-details', ':id') }}".replace(':id', trainerId),
                type: "GET",
                success: function (response) {
                    if (response.status) {
                        const data = response.data;
                        let html = `
                                    <div class="row">
                                        <div class="col-12 mb-2"><strong>Holder Name:</strong> <br> ${data.account_holder_name}</div>
                                        <div class="col-12 mb-2"><strong>Bank:</strong> <br> ${data.bank_name}</div>
                                        <div class="col-12 mb-2"><strong>Account No:</strong> <br> ${data.account_number}</div>
                                        <div class="col-12 mb-2"><strong>IFSC:</strong> <br> ${data.ifsc_code}</div>
                                        <div class="col-12 mb-2"><strong>UPI ID:</strong> <br> ${data.upi_id || 'N/A'}</div>
                                    </div>
                                `;
                        $('#bankDetailsBody').html(html);
                    } else {
                        $('#bankDetailsBody').html(
                            '<div class="text-center py-3 text-warning">Details not found or not provided by trainer.</div>'
                        );
                    }
                },
                error: function () {
                    $('#bankDetailsBody').html('<div class="text-center py-3 text-danger">Failed to fetch details.</div>');
                }
            });
        }

        function openActionModal(requestId, status) {
            $('#actionRequestId').val(requestId);
            $('#actionStatus').val(status);
            $('#admin_note').val('');
            $('#payment_proof').val('');

            if (status === 'approved') {
                $('#actionModalTitle').text('Approve Payment');
                $('#proofUploadDiv').removeClass('d-none');
                $('#actionSubmitBtn').text('Approve & Save').removeClass('btn-danger').addClass('btn-success');
            } else {
                $('#actionModalTitle').text('Reject Request');
                $('#proofUploadDiv').addClass('d-none');
                $('#actionSubmitBtn').text('Reject').removeClass('btn-success').addClass('btn-danger');
            }

            $('#actionModal').modal('show');
        }
    </script>
@endpush