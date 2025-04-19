<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of ressourse.
     */
    public function index(Request $request)
    {
        $query = Article::with(['category', 'tags']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('tag')) {
            $tagName = $request->tag;
            $query->whereHas('tags', function($q) use ($tagName) {
                $q->where('name', $tagName)
                  ->orWhere('slug', $tagName);
            });
        }
        if ($request->has('published')) {
            $query->where('published', $request->boolean('published'));
        } else {
        
            $query->where('published', true);
        }

    
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

    
        $sortField = $request->get('sort_by', 'published_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['title', 'created_at', 'published_at', 'reading_time'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

    
        $perPage = $request->get('per_page', 10);
        
        $articles = $query->paginate($perPage);
        
        return response()->json($articles);
    }

    /**
     * Store a newly created article.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category_id' => 'nullable|exists:categories,id',
            'reading_time' => 'nullable|integer',
            'published' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

    
        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        
    
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

    
        $readingTime = $request->reading_time;
        if (!$readingTime && $request->content) {
        
            $wordCount = str_word_count(strip_tags($request->content));
            $readingTime = ceil($wordCount / 200);
        }

    
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $this->uploadFile($request->file('featured_image'), 'articles/images');
        }

    
        $article = Article::create([
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'featured_image' => $featuredImagePath,
            'category_id' => $request->category_id,
            'reading_time' => $readingTime,
            'published' => $request->boolean('published', false),
            'published_at' => $request->boolean('published', false) ? now() : null,
        ]);

    
        if ($request->has('tags')) {
            $article->tags()->attach($request->tags);
        }

    
        $article->load(['category', 'tags']);

        return response()->json($article, 201);
    }

    /**
     * Display the specified article.
     */
    public function show($id)
    {
    
        $article = is_numeric($id) 
            ? Article::with(['category', 'tags'])->findOrFail($id)
            : Article::with(['category', 'tags'])->where('slug', $id)->firstOrFail();

        return response()->json($article);
    }

    /**
     * Update the specified article.
     */
    public function update(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20200',
            'category_id' => 'nullable|exists:categories,id',
            'reading_time' => 'nullable|integer',
            'published' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

    
        if ($request->has('title')) {
            $article->title = $request->title;
            
        
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            
            $article->slug = $slug;
        }

        if ($request->has('excerpt')) $article->excerpt = $request->excerpt;
        if ($request->has('content')) {
            $article->content = $request->content;
            
        
            if (!$request->has('reading_time')) {
                $wordCount = str_word_count(strip_tags($request->content));
                $article->reading_time = ceil($wordCount / 200);
            }
        }
        if ($request->has('category_id')) $article->category_id = $request->category_id;
        if ($request->has('reading_time')) $article->reading_time = $request->reading_time;

    
        if ($request->hasFile('featured_image')) {
        
            if ($article->featured_image && Storage::disk('s3')->exists($article->featured_image)) {
                Storage::disk('s3')->delete($article->featured_image);
            }
            
            $article->featured_image = $this->uploadFile($request->file('featured_image'), 'articles/images');
        }

    
        if ($request->has('published')) {
            $newPublishedState = $request->boolean('published');
            
        
            if ($newPublishedState && !$article->published) {
                $article->published_at = now();
            }
            
            $article->published = $newPublishedState;
        }

        $article->save();

    
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }

    
        $article->load(['category', 'tags']);

        return response()->json($article);
    }

    /**
     * Remove the specified article.
     */
    public function destroy(Article $article)
    {
    
        if ($article->featured_image && Storage::disk('s3')->exists($article->featured_image)) {
            Storage::disk('s3')->delete($article->featured_image);
        }

    
        $article->delete();

        return response()->json(null, 204);
    }

    /**
     * Helper function to upload a file to storage.
     */
    private function uploadFile($file, $path)
    {
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) 
            . '.' . $file->getClientOriginalExtension();
        
        $filePath = $file->storeAs($path, $filename, 's3');
        
        return $filePath;
    }
}