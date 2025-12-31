<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class BlogCategoryController extends Controller
{
    public function index()
    {
        return view('admin.blog_categories.index');
    }

    public function fetch(Request $request)
    {
        $query = BlogCategory::query();

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'html' => view('admin.blog_categories.partials.table_rows', compact('categories'))->render(),
            'pagination' => (string) $categories->links('pagination::bootstrap-5')
        ]);
    }

    public function fetchAll()
    {
        $categories = BlogCategory::where('status', 'active')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        BlogCategory::create($request->all());

        return response()->json(['success' => 'Category created successfully!']);
    }

    public function edit($id)
    {
        $category = BlogCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug,' . $id,
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update($request->all());

        return response()->json(['success' => 'Category updated successfully!']);
    }

    public function destroy($id)
    {
        BlogCategory::findOrFail($id)->delete();
        return response()->json(['success' => 'Category deleted successfully!']);
    }
}
