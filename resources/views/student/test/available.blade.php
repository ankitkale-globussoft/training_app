@extends('layouts.master', ['panel' => 'student'])
@section('title', 'Available Tests')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Student / Tests /</span> Available
            </h4>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($availableTests->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-file bx-lg text-muted mb-3"></i>
                        <h5 class="mb-2">No Tests Available</h5>
                        <p class="text-muted">
                            @if(!isset($completedBooking))
                                Complete your training to unlock tests.
                            @else
                                You have attempted all available tests or there are no tests for your program.
                            @endif
                        </p>
                        <a href="{{ route('student.tests.attempted') }}" class="btn btn-primary mt-3">View Attempted Tests</a>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($availableTests as $test)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <div class="badge bg-label-primary mb-1">{{ $test->program->title }}</div>
                                            <div class="badge bg-label-info">Attempt {{ $test->attempt_count + 1 }} / 3</div>
                                        </div>
                                        @if($test->duration)
                                            <small class="text-muted">
                                                <i class="bx bx-time-five"></i> {{ $test->duration }} mins
                                            </small>
                                        @endif
                                    </div>
                                    <h5 class="card-title mb-3">{{ $test->title }}</h5>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted"><i class="bx bx-notepad"></i> Questions</span>
                                            <strong>{{ $test->questions_count }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted"><i class="bx bx-trophy"></i> Total Marks</span>
                                            <strong>{{ $test->total_marks }}</strong>
                                        </div>
                                    </div>
                                    @if(!$test->duration)
                                        <p class="text-muted small mb-3">
                                            <i class="bx bx-info-circle"></i> No time limit
                                        </p>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('student.tests.show', $test->test_id) }}" class="btn btn-primary w-100">
                                        <i class="bx bx-play-circle me-1"></i> Attempt Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('student.tests.attempted') }}" class="btn btn-secondary">
                    <i class="bx bx-history me-1"></i> View Attempted Tests
                </a>
            </div>
        </div>
    </div>
@endsection