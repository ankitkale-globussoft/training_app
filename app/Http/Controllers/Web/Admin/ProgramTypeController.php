<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.program_types.index');
    }

    public function list(Request $request)
    {
        $query = ProgramType::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return $query->latest()->paginate(10);
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('program_types', 'public');
        }

        ProgramType::create($data);

        return response()->json(['success' => true]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, ProgramType $programType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('program_types', 'public');
        }

        $programType->update($data);
        return response()->json(['success' => true]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramType $programType)
    {
        $programType->delete();
        return response()->json(['success' => true]);
    }
}
