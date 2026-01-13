@extends('layouts.master', ['panel' => 'student'])
@section('title', 'My Certificates')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Student /</span> My Certificates
            </h4>

            @if($certificates->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-award bx-lg text-muted mb-3"></i>
                        <h5 class="mb-2">No Certificates Yet</h5>
                        <p class="text-muted">You haven't passed any tests yet. Complete assessments to earn certificates.</p>
                        <a href="{{ route('student.tests.available') }}" class="btn btn-primary mt-3">
                            <i class="bx bx-play-circle me-1"></i> Attempt Tests
                        </a>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($certificates as $attempt)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bx bx-certification text-warning mb-3" style="font-size: 4rem;"></i>
                                    <h5 class="card-title">{{ $attempt->test->program->title }}</h5>
                                    <p class="text-muted mb-2">{{ $attempt->test->title }}</p>
                                    <p class="small text-muted mb-3">
                                        Issued on {{ $attempt->created_at->format('F d, Y') }}
                                    </p>
                                    <a href="{{ route('student.certificates.view', $attempt->attempt_id) }}"
                                        class="btn btn-primary w-100" target="_blank">
                                        <i class="bx bx-show me-1"></i> View Certificate
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection