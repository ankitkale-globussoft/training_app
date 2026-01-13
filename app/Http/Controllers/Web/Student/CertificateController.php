<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * List all certificates (passed tests)
     */
    public function index()
    {
        $student = Auth::guard('student_web')->user();

        // Get all attempts that are passed
        // Since pass logic is dynamic (>= 60%), we fetch attempts and filter or use collection
        // Better to fetch successful ones.
        // Assuming pass is 60%

        $attempts = Attempt::where('candidate_id', $student->candidate_id)
            ->with(['test.program'])
            ->orderBy('created_at', 'desc')
            ->get();

        $certificates = $attempts->filter(function ($attempt) {
            if ($attempt->test->total_marks > 0) {
                $percentage = ($attempt->score / $attempt->test->total_marks) * 100;
                return $percentage >= 60;
            }
            return false;
        });

        return view('student.certificate.index', compact('certificates'));
    }

    /**
     * View/Download Certificate
     */
    public function show($attempt_id)
    {
        $student = Auth::guard('student_web')->user();

        $attempt = Attempt::where('attempt_id', $attempt_id)
            ->where('candidate_id', $student->candidate_id)
            ->with(['test.program', 'candidate'])
            ->firstOrFail();

        // Security: Ensure it's passed
        $percentage = ($attempt->score / $attempt->test->total_marks) * 100;
        if ($percentage < 60) {
            return redirect()->route('student.certificates.index')
                ->with('error', 'Certificate not available for this test.');
        }

        return view('student.certificate.view', compact('attempt'));
    }
}
