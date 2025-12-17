@extends('layouts.master', ['panel' => 'organisation'])
@section('title', 'Requested Programs')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Programs /</span> Requested Programs
    </h4>

    @if ($requirements->count())
        <div class="row g-4 mb-4">
            @foreach ($requirements as $req)
                @php
                    $hasFailedPayment = $req->booking && $req->booking->payment_status === 'failed';
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        @if ($req->program->image)
                            <img src="{{ asset('storage/' . $req->program->image) }}"
                                 class="card-img-top"
                                 style="height:200px; object-fit:cover;">
                        @else
                            <div class="card-img-top bg-label-primary d-flex align-items-center justify-content-center"
                                 style="height:200px;">
                                <i class="bx bx-book-open" style="font-size:4rem;"></i>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $req->program->title }}</h5>
                                <span class="badge bg-label-info">
                                    {{ ucfirst($req->mode) }}
                                </span>
                            </div>

                            <p class="text-muted small mb-3">
                                {{ Str::limit($req->program->description, 90) }}
                            </p>

                            <ul class="list-unstyled mb-3">
                                <li class="mb-1">
                                    <i class="bx bx-calendar"></i>
                                    <strong>Requested On:</strong>
                                    {{ $req->created_at->format('d M Y') }}
                                </li>
                                <li class="mb-1">
                                    <i class="bx bx-time-five"></i>
                                    <strong>Duration:</strong>
                                    {{ $req->program->duration }} months
                                </li>
                                <li class="mb-1">
                                    <i class="bx bx-dollar"></i>
                                    <strong>Amount:</strong>
                                    ₹{{ number_format($req->program->cost, 2) }}
                                </li>
                                <li class="mb-1">
                                    <i class="bx bx-check-circle"></i>
                                    <strong>Status:</strong>
                                    @php
                                        $statusClass = match($req->status ?? 'pending') {
                                            'pending_payment' => 'bg-label-warning',
                                            'paid' => 'bg-label-success',
                                            'approved' => 'bg-label-info',
                                            'rejected' => 'bg-label-danger',
                                            default => 'bg-label-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ str_replace('_', ' ', ucfirst($req->status ?? 'pending')) }}
                                    </span>
                                </li>
                                @if($hasFailedPayment)
                                    <li class="mb-1">
                                        <i class="bx bx-error-circle text-danger"></i>
                                        <strong>Payment:</strong>
                                        <span class="badge bg-label-danger">Failed - Retry Available</span>
                                    </li>
                                @endif
                            </ul>

                            <div class="mt-auto">
                                @if(($req->status ?? 'pending') === 'pending_payment' || $hasFailedPayment)
                                    <button class="btn btn-success w-100 mb-2"
                                        onclick="initiatePayment({{ $req->requirement_id }})">
                                        <i class="bx bx-credit-card"></i> 
                                        {{ $hasFailedPayment ? 'Retry Payment' : 'Pay Now' }}
                                    </button>
                                @endif
                                
                                @if(in_array($req->status ?? 'pending', ['pending', 'pending_payment']) || $hasFailedPayment)
                                    <button class="btn btn-outline-danger w-100"
                                        onclick="cancelRequest({{ $req->requirement_id }})">
                                        <i class="bx bx-x-circle"></i> Cancel Request
                                    </button>
                                @endif

                                @if(($req->status ?? 'pending') === 'paid')
                                    <div class="alert alert-success mb-0 p-2">
                                        <i class="bx bx-check-circle"></i> Payment Completed
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Sneat UI Style Pagination -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="dataTables_info" role="status" aria-live="polite">
                            Showing {{ $requirements->firstItem() }} to {{ $requirements->lastItem() }} of {{ $requirements->total() }} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_paginate paging_simple_numbers">
                            <ul class="pagination justify-content-center justify-content-md-end">
                                {{-- Previous Page Link --}}
                                @if ($requirements->onFirstPage())
                                    <li class="paginate_button page-item previous disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="paginate_button page-item previous">
                                        <a href="{{ $requirements->previousPageUrl() }}" class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $start = max($requirements->currentPage() - 2, 1);
                                    $end = min($start + 4, $requirements->lastPage());
                                    $start = max($end - 4, 1);
                                @endphp

                                @if($start > 1)
                                    <li class="paginate_button page-item">
                                        <a href="{{ $requirements->url(1) }}" class="page-link">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="paginate_button page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $requirements->currentPage())
                                        <li class="paginate_button page-item active">
                                            <span class="page-link">{{ $i }}</span>
                                        </li>
                                    @else
                                        <li class="paginate_button page-item">
                                            <a href="{{ $requirements->url($i) }}" class="page-link">{{ $i }}</a>
                                        </li>
                                    @endif
                                @endfor

                                @if($end < $requirements->lastPage())
                                    @if($end < $requirements->lastPage() - 1)
                                        <li class="paginate_button page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                    <li class="paginate_button page-item">
                                        <a href="{{ $requirements->url($requirements->lastPage()) }}" class="page-link">{{ $requirements->lastPage() }}</a>
                                    </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if ($requirements->hasMorePages())
                                    <li class="paginate_button page-item next">
                                        <a href="{{ $requirements->nextPageUrl() }}" class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></a>
                                    </li>
                                @else
                                    <li class="paginate_button page-item next disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bx bx-package" style="font-size:4rem;color:#ddd;"></i>
                <h5 class="mt-3">No Requested Programs</h5>
                <p class="text-muted">You have not requested any programs yet.</p>
            </div>
        </div>
    @endif

</div>
@endsection

@push('ajax')
<!-- Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
function initiatePayment(requirementId) {
    // Show loading
    Swal.fire({
        title: 'Processing...',
        text: 'Initializing payment gateway',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Initiate payment
    fetch('{{ route("org.programs.payment.initiate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            requirement_id: requirementId
        })
    })
    .then(res => res.json())
    .then(data => {
        Swal.close();
        
        if (!data.status) {
            Swal.fire('Error', data.message, 'error');
            return;
        }

        // Open Razorpay checkout
        const options = {
            key: data.key, // Public key - SAFE to use in frontend
            amount: data.amount,
            currency: data.currency,
            name: 'Training Program Payment',
            description: data.description,
            order_id: data.order_id,
            prefill: {
                name: data.name,
                email: data.email,
                contact: data.contact
            },
            theme: {
                color: '#696cff'
            },
            method: {
                card: true,
                netbanking: true,
                wallet: false,
                upi: true,
                paylater: false,
                emi: false
            },
            handler: function(response) {
                verifyPayment(response, data.booking_id);
            },
            modal: {
                ondismiss: function() {
                    // Mark as dismissed/failed so user can retry
                    markPaymentFailed(data.booking_id, 'Payment cancelled by user');
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();

        rzp.on('payment.failed', function(response) {
            markPaymentFailed(data.booking_id, response.error.description);
            
            Swal.fire({
                icon: 'error',
                title: 'Payment Failed',
                html: `
                    <p>${response.error.description}</p>
                    <p class="text-muted">Error Code: ${response.error.code}</p>
                    <p class="mt-3">You can retry the payment anytime.</p>
                `,
                confirmButtonText: 'Retry Payment',
                showCancelButton: true,
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        });
    })
    .catch(error => {
        Swal.close();
        Swal.fire('Error', 'Failed to initiate payment: ' + error.message, 'error');
    });
}

function markPaymentFailed(bookingId, reason) {
    // Silently mark payment as failed in background
    fetch('{{ route("org.programs.payment.verify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            booking_id: bookingId,
            failed: true,
            reason: reason,
            razorpay_order_id: 'failed',
            razorpay_payment_id: 'failed',
            razorpay_signature: 'failed'
        })
    }).catch(err => console.log('Silent fail marking:', err));
}

function verifyPayment(response, bookingId) {
    // Show verifying message
    Swal.fire({
        title: 'Verifying Payment...',
        text: 'Please wait while we confirm your payment',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('{{ route("org.programs.payment.verify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            razorpay_order_id: response.razorpay_order_id,
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_signature: response.razorpay_signature,
            booking_id: bookingId
        })
    })
    .then(res => res.json())
    .then(data => {
        Swal.close();
        
        if (data.status) {
            Swal.fire({
                icon: 'success',
                title: 'Payment Successful!',
                html: `
                    <p>${data.message}</p>
                    <p class="text-muted">Transaction ID: ${response.razorpay_payment_id}</p>
                `,
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: data.message,
                confirmButtonText: 'Retry Payment',
                showCancelButton: true,
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }
    })
    .catch(error => {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Verification Error',
            html: `
                <p>Payment verification failed: ${error.message}</p>
                <p class="mt-3">Don't worry! If payment was deducted, it will be verified automatically or you can contact support.</p>
            `,
            confirmButtonText: 'OK'
        });
    });
}

function cancelRequest(id) {
    Swal.fire({
        title: 'Cancel Request?',
        html: `
            <p>This will cancel your program request.</p>
            <p class="text-danger">This action cannot be undone.</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff3e1d',
        confirmButtonText: 'Yes, Cancel',
        cancelButtonText: 'No, Keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Cancelling...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`{{ url('org/programs/request') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire('Cancelled!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Failed to cancel request: ' + error.message, 'error');
            });
        }
    });
}
</script>
@endpush