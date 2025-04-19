<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index()
    {
        $tags = Tag::withCount('articles')->get();
        return response()->json($tags);
    }

    /**
     * Store a newly created tag.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tag = Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json($tag, 201);
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag)
    {
        $tag->loadCount('articles');
        return response()->json($tag);
    }

    /**
     * Update the specified tag.
     */
    public function update(Request $request, Tag $tag)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tag->name = $request->name;
        $tag->slug = Str::slug($request->name);
        $tag->save();

        return response()->json($tag);
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json(null, 204);
    }
}