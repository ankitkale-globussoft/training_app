<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Test Title</th>
                <th>Attempt #</th>
                <th>Program</th>
                <th>Score</th>
                <th>Percentage</th>
                <th>Time Taken</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            @forelse($attempts as $attempt)
                @php
                    $percentage = ($attempt->score / $attempt->test->total_marks) * 100;
                    $isPassed = $percentage >= 60;
                    $minutes = floor($attempt->time_taken / 60);
                    $seconds = $attempt->time_taken % 60;
                    $timeTaken = $minutes > 0 ? "{$minutes}m {$seconds}s" : "{$seconds}s";
                @endphp
                <tr>
                    <td><strong>{{ $attempt->test->title }}</strong></td>
                    <td><span class="badge bg-label-secondary">{{ $attempt->attempt_number }}</span></td>
                    <td><span class="badge bg-label-primary">{{ $attempt->test->program->title }}</span></td>
                    <td><strong>{{ $attempt->score }}</strong> / {{ $attempt->test->total_marks }}</td>
                    <td>
                        <span class="badge {{ $isPassed ? 'bg-label-success' : 'bg-label-danger' }}">
                            {{ number_format($percentage, 2) }}%
                        </span>
                    </td>
                    <td>{{ $timeTaken }}</td>
                    <td>
                        @if($isPassed)
                            <span class="badge bg-success"><i class="bx bx-check-circle"></i> Passed</span>
                        @else
                            <span class="badge bg-danger"><i class="bx bx-x-circle"></i> Failed</span>
                        @endif
                    </td>
                    <td>{{ $attempt->created_at->format('d M Y, h:i A') }}</td>
                    <td>
                        <a href="{{ route('student.tests.result', $attempt->attempt_id) }}" class="btn btn-sm btn-info">
                            <i class="bx bx-show"></i> View Details
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3 d-flex justify-content-end">
    {{ $attempts->links('pagination::bootstrap-5') }}
</div>