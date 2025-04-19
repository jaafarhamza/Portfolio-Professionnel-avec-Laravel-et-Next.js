<?php

namespace App\Http\Controllers\API;

use Log;
use App\Models\Skill;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Psr7\Request as Psr7Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSortFields = ['title', 'created_at', 'completed_at', 'order'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $perPage = $request->get('per_page', 10);

        $projects = $query->paginate($perPage);

        return response()->json($projects);
    }
    // upload images
    private function uploadFile($file, $path)
    {
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '.' . $file->getClientOriginalExtension();

        $filePath = $file->storeAs($path, $filename, 's3');

        return $filePath;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'featured' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'completed_at' => 'nullable|date',
            'published' => 'nullable|boolean',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;

        while (Project::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $this->uploadFile($request->file('thumbnail'), 'projects/thumbnails');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $this->uploadFile($image, 'projects/images');
            }
        }

        $project = Project::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
            'images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
            'url' => $request->url,
            'github_url' => $request->github_url,
            'category' => $request->category,
            'tags' => $request->tags ? json_encode($request->tags) : null,
            'featured' => $request->boolean('featured', false),
            'order' => $request->input('order', 0),
            'completed_at' => $request->completed_at,
            'published' => $request->boolean('published', false),
            'published_at' => $request->boolean('published', false) ? now() : null,
        ]);

        if ($request->has('skills')) {
            $project->skills()->attach($request->skills);
        }

        $project->load('skills');

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Allow finding by ID or slug
        $project = is_numeric($id)
            ? Project::with('skills')->findOrFail($id)
            : Project::with('skills')->where('slug', $id)->firstOrFail();

        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'featured' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'completed_at' => 'nullable|date',
            'published' => 'nullable|boolean',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:skills,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updates = [];

        if ($request->has('title')) {
            $updates['title'] = $request->title;


            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;

            while (Project::where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $updates['slug'] = $slug;
        }

        if ($request->has('description')) $updates['description'] = $request->description;
        if ($request->has('content')) $updates['content'] = $request->content;
        if ($request->has('url')) $updates['url'] = $request->url;
        if ($request->has('github_url')) $updates['github_url'] = $request->github_url;
        if ($request->has('category')) $updates['category'] = $request->category;
        if ($request->has('tags')) $updates['tags'] = json_encode($request->tags);
        if ($request->has('featured')) $updates['featured'] = $request->boolean('featured');
        if ($request->has('order')) $updates['order'] = $request->order;
        if ($request->has('completed_at')) $updates['completed_at'] = $request->completed_at;


        if ($request->has('published')) {
            $newPublishedState = $request->boolean('published');
            $updates['published'] = $newPublishedState;

            if ($newPublishedState && !$project->published) {
                $updates['published_at'] = now();
            }
        }

        if ($request->hasFile('thumbnail')) {

            if ($project->thumbnail && Storage::disk('s3')->exists($project->thumbnail)) {
                Storage::disk('s3')->delete($project->thumbnail);
            }

            $updates['thumbnail'] = $this->uploadFile($request->file('thumbnail'), 'projects/thumbnails');
        }

        if ($request->hasFile('images')) {

            if ($project->images) {
                $oldImages = json_decode($project->images, true);
                foreach ($oldImages as $image) {
                    if (Storage::disk('s3')->exists($image)) {
                        Storage::disk('s3')->delete($image);
                    }
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $this->uploadFile($image, 'projects/images');
            }

            $updates['images'] = json_encode($imagePaths);
        }

        $project->update($updates);

        if ($request->has('skills')) {
            $project->skills()->sync($request->skills);
        }
        $project->refresh();

        $project->load('skills');

        \Log::info('Project update', [
            'id' => $project->id,
            'request_data' => $request->all(),
            'final_project_data' => $project->toArray()
        ]);
        
        return response()->json([
            'project' => $project,
            'request_received' => $request->all()
        ]);

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if ($project->thumbnail && Storage::disk('s3')->exists($project->thumbnail)) {
            Storage::disk('s3')->delete($project->thumbnail);
        }

        if ($project->images) {
            $images = json_decode($project->images, true);
            foreach ($images as $image) {
                if (Storage::disk('s3')->exists($image)) {
                    Storage::disk('s3')->delete($image);
                }
            }
        }

        $project->delete();

        return response()->json(null, 204);
    }
}