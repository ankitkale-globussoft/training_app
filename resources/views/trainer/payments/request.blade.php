@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'Request Payments')

@section('content')
    <div class="container-xxl container-p-y">

        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Payments /</span> Request Payments
        </h4>

        <!-- Wallet Stats -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Total Earnings</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h4 class="mb-0 me-2">₹{{ number_format($completedBookings, 2) }}</h4>
                                </div>
                                <small class="text-success">(Completed Bookings)</small>
                            </div>
                            <span class="badge bg-label-success rounded p-2">
                                <i class="bx bx-money bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Withdrawn</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h4 class="mb-0 me-2">₹{{ number_format($withdrawn, 2) }}</h4>
                                </div>
                                <small class="text-primary">(Approved)</small>
                            </div>
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-wallet bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Pending Request</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h4 class="mb-0 me-2">₹{{ number_format($pending, 2) }}</h4>
                                </div>
                                <small class="text-warning">(In Review)</small>
                            </div>
                            <span class="badge bg-label-warning rounded p-2">
                                <i class="bx bx-time bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Available Balance</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h4 class="mb-0 me-2">₹{{ number_format($available, 2) }}</h4>
                                </div>
                                <small class="text-info">(Withdrawable)</small>
                            </div>
                            <span class="badge bg-label-info rounded p-2">
                                <i class="bx bx-credit-card bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Request Form -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Request Payout</h5>
                    </div>
                    <div class="card-body">
                        @if ($available > 0)
                            <form action="{{ route('trainer.payments.request.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="amount">Amount (₹)</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="amount" name="amount"
                                            placeholder="Enter amount" min="1" max="{{ $available }}" step="0.01" required>
                                    </div>
                                    <div class="form-text">Max available: ₹{{ number_format($available, 2) }}</div>
                                </div>
                                <button type="submit" class="btn btn-primary d-grid w-100">Send Request</button>
                            </form>
                        @else
                            <div class="text-center py-3">
                                <i class="bx bx-lock-alt fs-1 text-muted mb-2"></i>
                                <p class="mb-0 text-muted">Insufficient balance to request payout.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="col-md-8">
                <div class="card">
                    <h5 class="card-header">Payout History</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Proof</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($requests as $req)
                                    <tr>
                                        <td>{{ $req->created_at->format('d M Y, h:i A') }}</td>
                                        <td class="fw-bold">₹{{ number_format($req->amount, 2) }}</td>
                                        <td>
                                            @if ($req->status === 'approved')
                                                <span class="badge bg-label-success">Approved</span>
                                            @elseif($req->status === 'pending')
                                                <span class="badge bg-label-warning">Pending</span>
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
                                        <td colspan="5" class="text-center py-4 text-muted">No payout requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection