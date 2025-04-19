<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\CategoryController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Admin only 
    Route::middleware('admin')->group(function () {
        
    });
});

// Public project routes
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{project}', [ProjectController::class, 'show']);

// Protected project routes (admin only)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
});
Route::middleware(['auth:sanctum', 'admin'])->post('/upload', [UploadController::class, 'store']);

// Public blog routes
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{article}', [ArticleController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{tag}', [TagController::class, 'show']);

// Protected blog routes (admin only)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Articles
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::put('/articles/{article}', [ArticleController::class, 'update']);
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);
    
    // Categories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    
    // Tags
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
});

// Public comment routes
Route::get('/articles/{article}/comments', [CommentController::class, 'index']);
Route::post('/articles/{article}/comments', [CommentController::class, 'store']);

// Admin comment routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/comments', [CommentController::class, 'adminIndex']);
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

// Public contact route with rate limiting
Route::post('/contact', [ContactController::class, 'submit']);

// Admin contact routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/contact-messages', [ContactController::class, 'index']);
    Route::patch('/contact-messages/{message}/read', [ContactController::class, 'markAsRead']);
    Route::delete('/contact-messages/{message}', [ContactController::class, 'destroy']);
});