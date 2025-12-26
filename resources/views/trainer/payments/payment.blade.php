@extends('layouts.master', ['panel' => 'trainer'])
@section('title', 'All Payments')

@section('content')
    <div class="container-xxl container-p-y">

        <h4 class="fw-bold mb-4">
            <span class="text-muted fw-light">Payments /</span> All Payments
        </h4>

        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-earnings" aria-controls="navs-pills-earnings" aria-selected="true">
                        <i class="bx bx-money me-1"></i> Earnings
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-payouts" aria-controls="navs-pills-payouts" aria-selected="false">
                        <i class="bx bx-export me-1"></i> Payouts
                    </button>
                </li>
            </ul>
            <div class="tab-content shadow-none p-0 bg-transparent">

                <!-- Earnings Tab -->
                <div class="tab-pane fade show active" id="navs-pills-earnings" role="tabpanel">
                    <div class="card">
                        <h5 class="card-header">Completed Bookings</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Program</th>
                                        <th>Organization</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($earnings as $earning)
                                        <tr>
                                            <td>{{ $earning->updated_at->format('d M Y') }}</td>
                                            <td>
                                                <span
                                                    class="fw-semibold">{{ $earning->requirement->program->title ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $earning->organization->name ?? 'N/A' }}</td>
                                            <td class="fw-bold text-success">+₹{{ number_format($earning->amount, 2) }}</td>
                                            <td><span class="badge bg-label-success">Completed</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No earnings yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{ $earnings->links() }}
                        </div>
                    </div>
                </div>

                <!-- Payouts Tab -->
                <div class="tab-pane fade" id="navs-pills-payouts" role="tabpanel">
                    <div class="card">
                        <h5 class="card-header">Payout Requests</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Request Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Proof</th>
                                        <th>Admin Note</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse($payouts as $payout)
                                        <tr>
                                            <td>{{ $payout->created_at->format('d M Y, h:i A') }}</td>
                                            <td class="fw-bold text-danger">-₹{{ number_format($payout->amount, 2) }}</td>
                                            <td>
                                                @if ($payout->status === 'approved')
                                                    <span class="badge bg-label-success">Approved</span>
                                                @elseif($payout->status === 'pending')
                                                    <span class="badge bg-label-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-label-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($payout->payment_proof)
                                                    <a href="{{ asset('storage/' . $payout->payment_proof) }}" target="_blank"
                                                        class="btn btn-xs btn-outline-primary">
                                                        <i class="bx bx-file"></i> View
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $payout->admin_note ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No payouts record found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{ $payouts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection