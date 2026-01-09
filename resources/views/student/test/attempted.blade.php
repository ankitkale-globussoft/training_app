@extends('layouts.master', ['panel' => 'student'])
@section('title', 'Attempted Tests')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Student / Tests /</span> Attempted
            </h4>

            @if($attempts->isEmpty())
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
                    <h5 class="card-header">Your Test History</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Test Title</th>
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
                                @foreach($attempts as $attempt)
                                    @php
                                        $percentage = ($attempt->score / $attempt->test->total_marks) * 100;
                                        $isPassed = $percentage >= 60;
                                        $minutes = floor($attempt->time_taken / 60);
                                        $seconds = $attempt->time_taken % 60;
                                        $timeTaken = $minutes > 0 ? "{$minutes}m {$seconds}s" : "{$seconds}s";
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $attempt->test->title }}</strong></td>
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
                                            <a href="{{ route('student.tests.result', $attempt->attempt_id) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="bx bx-show"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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