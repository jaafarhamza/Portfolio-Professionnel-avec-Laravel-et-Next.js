<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('articles')->get();
        return response()->json($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json($category, 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->loadCount('articles');
        return response()->json($category);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('name')) {
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
        }

        if ($request->has('description')) {
            $category->description = $request->description;
        }

        $category->save();

        return response()->json($category);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        if ($category->articles()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category. It is still being used by articles.'
            ], 422);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}