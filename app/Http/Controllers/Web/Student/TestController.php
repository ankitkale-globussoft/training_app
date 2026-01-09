<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\BookingProgress;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Display available tests for the student
     */
    public function available()
    {
        $student = Auth::guard('student_web')->user();

        // Get completed booking (percentage = 100)
        $completedBooking = BookingProgress::where('booking_id', $student->booking_id)
            ->where('percentage', 100)
            ->first();

        $availableTests = [];

        if ($completedBooking) {
            // Get the booking to find program_id
            $booking = $student->booking;

            if ($booking && $booking->requirement) {
                $programId = $booking->requirement->program_id;

                // Get tests for this program that the student hasn't attempted
                $attemptedTestIds = Attempt::where('candidate_id', $student->candidate_id)
                    ->pluck('test_id')
                    ->toArray();

                $availableTests = Test::where('program_id', $programId)
                    ->whereNotIn('test_id', $attemptedTestIds)
                    ->withCount('questions')
                    ->with('program')
                    ->get();
            }
        }

        return view('student.test.available', compact('availableTests'));
    }

    /**
     * Show test attempt page
     */
    public function show($test_id)
    {
        $student = Auth::guard('student_web')->user();

        // Check if student has already attempted this test
        $existingAttempt = Attempt::where('candidate_id', $student->candidate_id)
            ->where('test_id', $test_id)
            ->first();

        if ($existingAttempt) {
            return redirect()->route('student.tests.result', $existingAttempt->attempt_id)
                ->with('error', 'You have already attempted this test.');
        }

        // Check if booking progress is 100%
        $completedBooking = BookingProgress::where('booking_id', $student->booking_id)
            ->where('percentage', 100)
            ->exists();

        if (!$completedBooking) {
            return redirect()->route('student.tests.available')
                ->with('error', 'You must complete your training before taking tests.');
        }

        $test = Test::with('program')->findOrFail($test_id);

        // Verify test belongs to student's program
        $booking = $student->booking;
        if (!$booking || !$booking->requirement || $booking->requirement->program_id != $test->program_id) {
            return redirect()->route('student.tests.available')
                ->with('error', 'This test is not available for your program.');
        }

        return view('student.test.attempt', compact('test'));
    }

    /**
     * Get test questions via AJAX (without correct answers)
     */
    public function getTest($test_id)
    {
        $student = Auth::guard('student_web')->user();

        // Security check
        $existingAttempt = Attempt::where('candidate_id', $student->candidate_id)
            ->where('test_id', $test_id)
            ->first();

        if ($existingAttempt) {
            return response()->json([
                'success' => false,
                'message' => 'You have already attempted this test.'
            ], 403);
        }

        $test = Test::with([
            'questions' => function ($query) {
                $query->select('ques_id', 'test_id', 'ques_text', 'opt_a', 'opt_b', 'opt_c', 'opt_d', 'marks');
            }
        ])->findOrFail($test_id);

        return response()->json([
            'success' => true,
            'data' => [
                'test' => [
                    'test_id' => $test->test_id,
                    'title' => $test->title,
                    'duration' => $test->duration,
                    'total_marks' => $test->total_marks
                ],
                'questions' => $test->questions
            ]
        ]);
    }

    /**
     * Submit test attempt
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tests,test_id',
            'answers' => 'required|array',
            'time_taken' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $student = Auth::guard('student_web')->user();
        $testId = $request->test_id;

        // Check for existing attempt
        $existingAttempt = Attempt::where('candidate_id', $student->candidate_id)
            ->where('test_id', $testId)
            ->first();

        if ($existingAttempt) {
            return response()->json([
                'success' => false,
                'message' => 'You have already attempted this test.'
            ], 403);
        }

        $test = Test::with('questions')->findOrFail($testId);
        $studentAnswers = $request->answers; // Array of [question_id => selected_option]

        // Calculate score
        $score = 0;
        foreach ($test->questions as $question) {
            $questionId = $question->ques_id;
            if (isset($studentAnswers[$questionId])) {
                if (strtolower($studentAnswers[$questionId]) === strtolower($question->ans_opt)) {
                    $score += $question->marks;
                }
            }
        }

        // Store attempt
        $attempt = Attempt::create([
            'candidate_id' => $student->candidate_id,
            'test_id' => $testId,
            'answers' => $studentAnswers,
            'score' => $score,
            'time_taken' => $request->time_taken
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test submitted successfully!',
            'data' => [
                'attempt_id' => $attempt->attempt_id,
                'score' => $score,
                'total_marks' => $test->total_marks
            ],
            'redirect' => route('student.tests.result', $attempt->attempt_id)
        ]);
    }

    /**
     * Display attempted tests
     */
    public function attempted()
    {
        $student = Auth::guard('student_web')->user();

        $attempts = Attempt::where('candidate_id', $student->candidate_id)
            ->with(['test.program'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.test.attempted', compact('attempts'));
    }

    /**
     * Show detailed result for an attempt
     */
    public function result($attempt_id)
    {
        $student = Auth::guard('student_web')->user();

        $attempt = Attempt::where('attempt_id', $attempt_id)
            ->where('candidate_id', $student->candidate_id)
            ->with(['test.program', 'test.questions'])
            ->firstOrFail();

        // Calculate percentage
        $percentage = ($attempt->score / $attempt->test->total_marks) * 100;

        // Prepare question results
        $questionResults = [];
        foreach ($attempt->test->questions as $question) {
            $studentAnswer = $attempt->answers[$question->ques_id] ?? null;
            $isCorrect = $studentAnswer && strtolower($studentAnswer) === strtolower($question->ans_opt);

            $questionResults[] = [
                'question' => $question,
                'student_answer' => $studentAnswer,
                'correct_answer' => $question->ans_opt,
                'is_correct' => $isCorrect,
                'marks_obtained' => $isCorrect ? $question->marks : 0
            ];
        }

        return view('student.test.result', compact('attempt', 'percentage', 'questionResults'));
    }
}
