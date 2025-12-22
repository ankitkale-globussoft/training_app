<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramType;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProgramTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $program_types = ProgramType::all();
        return response()->json([
            'success' => true,
            'message' => 'Program types fetched successfully',
            'data' => $program_types,
            'status' => 200
        ], 200);
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
            'name' => 'required|string|max:255|unique:program_types,name',
            'description' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'msg' => 'Validation failed'
            ], 422);
        }
        $validated = $validator->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('program_type_images', 'public');
        }

        $programtype = ProgramType::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Program type created successfully',
            'data' => $programtype,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $program_type = ProgramType::find($id);
        if (!$program_type) {
            return response()->json([
                'success' => false,
                'message' => 'Program type not found',
                'data' => null,
                'status' => 404
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Program type details fetched successfully',
            'data' => $program_type,
            'status' => 200
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
    public function update(Request $request, $id)
    {
        $programType = ProgramType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:program_types,name,' . $programType->id,
            'description' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'msg' => 'Validation failed'
            ], 422);
        }

        $validated = $validator->validated();

        if ($request->hasFile('image')) {

            if ($programType->image && Storage::disk('public')->exists($programType->image)) {
                Storage::disk('public')->delete($programType->image);
            }

            // Save new image
            $validated['image'] = $request->file('image')->store('program_type_images', 'public');
        }

        $programType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Program type updated successfully',
            'data' => $programType,
            'status' => 200
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $programType = ProgramType::findOrFail($id);
        if ($programType->image && Storage::disk('public')->exists($programType->image)) {
            Storage::disk('public')->delete($programType->image);
        }
        $programType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program type deleted successfully',
            'data' => null,
            'status' => 200
        ], 200);
    }
}
