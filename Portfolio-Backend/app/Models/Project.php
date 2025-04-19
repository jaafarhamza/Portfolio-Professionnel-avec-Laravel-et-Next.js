<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'content', 'thumbnail', 
        'images', 'url', 'github_url', 'category', 'tags', 
        'featured', 'order', 'completed_at', 'published', 'published_at'
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'featured' => 'boolean',
        'published' => 'boolean',
        'completed_at' => 'date',
        'published_at' => 'datetime',
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }
}