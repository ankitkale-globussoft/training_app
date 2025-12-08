<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $program = Program::all();

        return response()->json([
            'success' => true,
            'result'  => $program
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'           => 'required|string|max:255|unique:programs,title',
            'duration'        => 'required|string|max:255',
            'program_type_id' => 'required|exists:program_types,id',
            'cost'            => 'required|string|max:255',
            'description'     => 'required|string|max:255',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'msg'     => 'Validation failed'
            ], 422);
        }
        $validated = $validator->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('programs_images', 'public');
        }

        $program = Program::create($validated);
        return response()->json([
            'success' => true,
            'result'  => ['program' => $program],
            'msg'     => 'Program created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $program = Program::find($id);
        return response()->json([
            'success' => true,
            'result'  => $program
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $program = Program::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'title'           => 'required|string|max:255|unique:programs,title,' . $program->id,
            'duration'        => 'required|string|max:255',
            'program_type_id' => 'required|exists:program_types,id',
            'cost'            => 'required|string|max:255',
            'description'     => 'required|string|max:255',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'msg'     => 'Validation failed'
            ], 422);
        }
        $validated = $validator->validated();

        if ($request->hasFile('image')) {
            // Remove old image if exists
            if ($program->image && Storage::disk('public')->exists($program->image)) {
                Storage::disk('public')->delete($program->image);
            }
            // Save new image
            $validated['image'] = $request->file('image')->store('programs_images', 'public');
        }

        $program->update($validated);

        return response()->json([
            'success' => true,
            'result'  => ['program' => $program],
            'msg'     => 'Program updated successfully'
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $program = Program::findOrFail($id);

        if ($program->image && Storage::disk('public')->exists($program->image)) {
            Storage::disk('public')->delete($program->image);
        }

        $program->delete();

        return response()->json([
            'success' => true,
            'msg'     => 'Program deleted successfully'
        ], 200);
    }
}
