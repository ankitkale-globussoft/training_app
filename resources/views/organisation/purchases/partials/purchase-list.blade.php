@if($purchases->isEmpty())
    <div class="text-center">
        <i class='bx bx-receipt text-muted' style="font-size: 4rem;"></i>
        <p class="mt-3 text-muted">No purchases found.</p>
    </div>
@else
    <table class="table table-hover border-top">
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Program</th>
                <th>Trainer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Invoice</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->created_at->format('d M Y') }}</td>
                    <td><span class="fw-semibold">#{{ $purchase->transaction_id ?? 'N/A' }}</span></td>
                    <td>{{ $purchase->requirement->program->title }}</td>
                    <td>{{ $purchase->booking && $purchase->booking->trainer ? $purchase->booking->trainer->name : 'N/A' }}</td>
                    <td>₹{{ number_format($purchase->amount, 2) }}</td>
                    <td>
                        @php
                            $statusClass = match ($purchase->payment_status) {
                                'paid', 'completed' => 'bg-label-success',
                                'pending' => 'bg-label-warning',
                                'failed' => 'bg-label-danger',
                                default => 'bg-label-secondary'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} text-uppercase">{{ $purchase->payment_status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('org.purchases.invoice', $purchase->booking_id) }}" target="_blank"
                            class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-download me-1"></i> Invoice
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif