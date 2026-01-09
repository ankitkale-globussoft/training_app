@extends('layouts.master', ['panel' => 'student'])
@section('title', 'Test Result')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Student / Tests /</span> Result
            </h4>

            <!-- Summary Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-2">{{ $attempt->test->title }}</h4>
                                    <p class="text-muted mb-2">
                                        <span class="badge bg-label-primary">{{ $attempt->test->program->title }}</span>
                                        <span class="ms-2"><small>Attempted on:
                                                {{ $attempt->created_at->format('d M Y, h:i A') }}</small></span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    @php
                                        $isPassed = $percentage >= 60;
                                    @endphp
                                    @if($isPassed)
                                        <span class="badge bg-success fs-5 px-3 py-2">
                                            <i class="bx bx-check-circle"></i> PASSED
                                        </span>
                                    @else
                                        <span class="badge bg-danger fs-5 px-3 py-2">
                                            <i class="bx bx-x-circle"></i> FAILED
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score Summary -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-trophy fs-3"></i>
                                </span>
                            </div>
                            <h6 class="mb-1">Score</h6>
                            <h4 class="mb-0">{{ $attempt->score }} / {{ $attempt->test->total_marks }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded bg-label-{{ $isPassed ? 'success' : 'danger' }}">
                                    <i class="bx bx-bar-chart fs-3"></i>
                                </span>
                            </div>
                            <h6 class="mb-1">Percentage</h6>
                            <h4 class="mb-0">{{ number_format($percentage, 2) }}%</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-time-five fs-3"></i>
                                </span>
                            </div>
                            <h6 class="mb-1">Time Taken</h6>
                            @php
                                $minutes = floor($attempt->time_taken / 60);
                                $seconds = $attempt->time_taken % 60;
                            @endphp
                            <h4 class="mb-0">{{ $minutes }}m {{ $seconds }}s</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar avatar-md mx-auto mb-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-notepad fs-3"></i>
                                </span>
                            </div>
                            <h6 class="mb-1">Correct Answers</h6>
                            @php
                                $correctCount = collect($questionResults)->where('is_correct', true)->count();
                                $totalCount = count($questionResults);
                            @endphp
                            <h4 class="mb-0">{{ $correctCount }} / {{ $totalCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question-wise Results -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detailed Answers</h5>
                    <button onclick="window.print()" class="btn btn-sm btn-secondary">
                        <i class="bx bx-printer"></i> Print
                    </button>
                </div>
                <div class="card-body">
                    @foreach($questionResults as $index => $result)
                        <div
                            class="question-result mb-4 p-3 border rounded {{ $result['is_correct'] ? 'border-success bg-light-success' : 'border-danger bg-light-danger' }}">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="mb-0">
                                    <strong>Q{{ $index + 1 }}.</strong> {{ $result['question']->ques_text }}
                                </h6>
                                <div>
                                    <span class="badge {{ $result['is_correct'] ? 'bg-success' : 'bg-danger' }}">
                                        {{ $result['is_correct'] ? 'Correct' : 'Wrong' }}
                                    </span>
                                    <span class="badge bg-label-primary ms-1">{{ $result['question']->marks }} mark(s)</span>
                                </div>
                            </div>

                            <div class="options-list">
                                @php
                                    $options = ['a' => $result['question']->opt_a, 'b' => $result['question']->opt_b, 'c' => $result['question']->opt_c, 'd' => $result['question']->opt_d];
                                @endphp

                                @foreach($options as $key => $value)
                                    @php
                                        $isStudentAnswer = strtolower($result['student_answer']) === $key;
                                        $isCorrectAnswer = strtolower($result['correct_answer']) === $key;
                                    @endphp

                                    <div
                                        class="form-check mb-2 p-2 rounded
                                                {{ $isCorrectAnswer ? 'bg-success bg-opacity-10 border border-success' : '' }}
                                                {{ $isStudentAnswer && !$isCorrectAnswer ? 'bg-danger bg-opacity-10 border border-danger' : '' }}">
                                        <label class="form-check-label d-flex align-items-center">
                                            <span class="me-2 fw-bold">{{ strtoupper($key) }}.</span>
                                            <span>{{ $value }}</span>
                                            @if($isStudentAnswer)
                                                <span class="ms-auto badge bg-info">Your Answer</span>
                                            @endif
                                            @if($isCorrectAnswer)
                                                <span class="ms-2 badge bg-success">
                                                    <i class="bx bx-check-circle"></i> Correct Answer
                                                </span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            @if(!$result['student_answer'])
                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="bx bx-info-circle"></i> You did not answer this question
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('student.tests.attempted') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back to Attempted Tests
                </a>
                <a href="{{ route('student.tests.available') }}" class="btn btn-primary">
                    <i class="bx bx-play-circle"></i> Attempt More Tests
                </a>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .btn,
            .fw-bold.py-3,
            .navbar,
            .menu,
            .footer {
                display: none !important;
            }

            .content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        .bg-light-success {
            background-color: rgba(113, 221, 55, 0.05) !important;
        }

        .bg-light-danger {
            background-color: rgba(255, 62, 29, 0.05) !important;
        }
    </style>
@endsection