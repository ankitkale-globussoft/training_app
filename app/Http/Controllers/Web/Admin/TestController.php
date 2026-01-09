<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tests = Test::with('program')->latest()->paginate(10);
        return view('admin.test.index', compact('tests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = Program::all();
        $test = null;
        return view('admin.test.create_edit', compact('programs', 'test'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'title' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($request, &$test) {
            $test = Test::create([
                'program_id' => $request->program_id,
                'title' => $request->title,
                'duration' => $request->duration,
                'total_marks' => 0,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Test created successfully.',
            'redirect' => route('admin.test.edit', $test->test_id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $test = Test::findOrFail($id);
        $programs = Program::all();
        return view('admin.test.create_edit', compact('test', 'programs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id',
            'title' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:1',
        ]);

        $test = Test::findOrFail($id);
        $test->update([
            'program_id' => $request->program_id,
            'title' => $request->title,
            'duration' => $request->duration,
        ]);

        return response()->json(['success' => true, 'message' => 'Test details updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $test = Test::findOrFail($id);
        $test->delete();
        return redirect()->route('admin.test.index')->with('success', 'Test deleted successfully.');
    }

    // AJAX Methods for Questions

    public function getQuestions($testId)
    {
        $questions = Question::where('test_id', $testId)->get();
        return response()->json([
            'success' => true,
            'data' => $questions
        ]);
    }

    public function addQuestion(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,test_id',
            'ques_text' => 'required|string',
            'opt_a' => 'required|string',
            'opt_b' => 'required|string',
            'opt_c' => 'required|string',
            'opt_d' => 'required|string',
            'ans_opt' => 'required|in:a,b,c,d',
            'marks' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            Question::create($request->all());
            $this->recalculateTotalMarks($request->test_id);
        });

        return response()->json(['success' => true, 'message' => 'Question added successfully.']);
    }

    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'ques_text' => 'required|string',
            'opt_a' => 'required|string',
            'opt_b' => 'required|string',
            'opt_c' => 'required|string',
            'opt_d' => 'required|string',
            'ans_opt' => 'required|in:a,b,c,d',
            'marks' => 'required|integer|min:1',
        ]);

        $question = Question::findOrFail($id);

        DB::transaction(function () use ($request, $question) {
            $question->update($request->all());
            $this->recalculateTotalMarks($question->test_id);
        });

        return response()->json(['success' => true, 'message' => 'Question updated successfully.']);
    }

    public function deleteQuestion($id)
    {
        $question = Question::findOrFail($id);
        $testId = $question->test_id;

        DB::transaction(function () use ($question, $testId) {
            $question->delete();
            $this->recalculateTotalMarks($testId);
        });

        return response()->json(['success' => true, 'message' => 'Question deleted successfully.']);
    }

    private function recalculateTotalMarks($testId)
    {
        $totalMarks = Question::where('test_id', $testId)->sum('marks');
        Test::where('test_id', $testId)->update(['total_marks' => $totalMarks]);
    }
}
