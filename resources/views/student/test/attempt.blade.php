@extends('layouts.master', ['panel' => 'student'])
@section('title', 'Attempt Test')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Student / Tests /</span> Attempt
            </h4>

            <div class="row">
                <!-- Test Info Card -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-2">{{ $test->title }}</h5>
                                    <p class="text-muted mb-0">
                                        <span class="badge bg-label-primary">{{ $test->program->title }}</span>
                                        <span class="ms-2"><i class="bx bx-trophy"></i> Total Marks:
                                            <strong>{{ $test->total_marks }}</strong></span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    @if($test->duration)
                                        <div class="timer-container">
                                            <h6 class="text-muted mb-1">Time Remaining</h6>
                                            <div class="timer-display fs-2 fw-bold text-primary" id="timer">
                                                <i class="bx bx-time-five"></i> <span
                                                    id="timer-text">{{ $test->duration }}:00</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <i class="bx bx-infinite"></i><br>
                                            <small>No time limit</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Card -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Answer all questions</h5>
                        </div>
                        <div class="card-body">
                            <form id="testForm">
                                @csrf
                                <input type="hidden" name="test_id" value="{{ $test->test_id }}">
                                <input type="hidden" name="time_taken" id="time_taken" value="0">

                                <div id="questions-container">
                                    <!-- Questions will be loaded via AJAX -->
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading questions...</p>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex justify-content-between">
                                    <a href="{{ route('student.tests.available') }}" class="btn btn-secondary">
                                        <i class="bx bx-arrow-back"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bx bx-check-circle"></i> Submit Test
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('ajax')
    <script>
        let startTime = Date.now();
        let timerInterval = null;
        let testDuration = {{ $test->duration ?? 0 }};
        let remainingSeconds = testDuration * 60;

        $(document).ready(function () {
            // Load test questions
            loadQuestions();

            // Start timer if test has duration
            if (testDuration > 0) {
                startTimer();
            }

            // Handle form submission
            $('#testForm').submit(function (e) {
                e.preventDefault();

                // Check if all questions are answered
                let allAnswered = true;
                $('input[type="radio"]:checked').length;
                let totalQuestions = $('.question-block').length;
                let answeredQuestions = new Set();

                $('input[type="radio"]:checked').each(function () {
                    answeredQuestions.add($(this).attr('name').replace('answers[', '').replace(']', ''));
                });

                if (answeredQuestions.size < totalQuestions) {
                    Swal.fire({
                        title: 'Incomplete!',
                        text: `You have answered ${answeredQuestions.size} out of ${totalQuestions} questions. Do you want to submit anyway?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, submit!',
                        cancelButtonText: 'No, let me review'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitTest();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Submit Test?',
                        text: "You won't be able to change your answers after submission.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, submit!',
                        cancelButtonText: 'Review again'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitTest();
                        }
                    });
                }
            });
        });

        function loadQuestions() {
            $.ajax({
                url: "{{ route('student.tests.data', $test->test_id) }}",
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        displayQuestions(response.data.questions);
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to load questions', 'error');
                    setTimeout(() => {
                        window.location.href = "{{ route('student.tests.available') }}";
                    }, 2000);
                }
            });
        }

        function displayQuestions(questions) {
            let html = '';
            questions.forEach((q, index) => {
                html += `
                    <div class="question-block mb-4 p-4 border rounded">
                        <h6 class="mb-3"><strong>Q${index + 1}.</strong> ${q.ques_text} <span class="badge bg-label-success">${q.marks} mark(s)</span></h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[${q.ques_id}]" value="a" id="q${q.ques_id}_a">
                            <label class="form-check-label" for="q${q.ques_id}_a">
                                A. ${q.opt_a}
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[${q.ques_id}]" value="b" id="q${q.ques_id}_b">
                            <label class="form-check-label" for="q${q.ques_id}_b">
                                B. ${q.opt_b}
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[${q.ques_id}]" value="c" id="q${q.ques_id}_c">
                            <label class="form-check-label" for="q${q.ques_id}_c">
                                C. ${q.opt_c}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[${q.ques_id}]" value="d" id="q${q.ques_id}_d">
                            <label class="form-check-label" for="q${q.ques_id}_d">
                                D. ${q.opt_d}
                            </label>
                        </div>
                    </div>
                `;
            });
            $('#questions-container').html(html);
        }

        function startTimer() {
            timerInterval = setInterval(function () {
                remainingSeconds--;

                let minutes = Math.floor(remainingSeconds / 60);
                let seconds = remainingSeconds % 60;

                $('#timer-text').text(`${minutes}:${seconds.toString().padStart(2, '0')}`);

                // Change color when time is running out
                if (remainingSeconds <= 60) {
                    $('#timer-text').parent().removeClass('text-primary').addClass('text-danger');
                } else if (remainingSeconds <= 300) {
                    $('#timer-text').parent().removeClass('text-primary').addClass('text-warning');
                }

                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    Swal.fire({
                        title: 'Time\'s Up!',
                        text: 'Your test will be submitted automatically.',
                        icon: 'warning',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        submitTest();
                    });
                }
            }, 1000);
        }

        function submitTest() {
            // Stop timer
            if (timerInterval) {
                clearInterval(timerInterval);
            }

            // Calculate time taken in seconds
            let timeTaken = Math.floor((Date.now() - startTime) / 1000);
            $('#time_taken').val(timeTaken);

            // Disable submit button
            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Submitting...');

            let formData = $('#testForm').serialize();

            $.ajax({
                url: "{{ route('student.tests.submit') }}",
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Submitted!',
                            text: `You scored ${response.data.score}/${response.data.total_marks}`,
                            showConfirmButton: true
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    }
                },
                error: function (xhr) {
                    $('#submitBtn').prop('disabled', false).html('<i class="bx bx-check-circle"></i> Submit Test');
                    if (timerInterval && testDuration > 0) {
                        startTimer();
                    }
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed to submit test', 'error');
                }
            });
        }

        // Prevent page refresh/close during test
        window.addEventListener('beforeunload', function (e) {
            e.preventDefault();
            e.returnValue = '';
        });
    </script>
@endpush