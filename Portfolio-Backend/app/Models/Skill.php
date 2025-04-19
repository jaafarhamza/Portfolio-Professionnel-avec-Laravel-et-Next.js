<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'category',
        'proficiency', 'is_highlighted', 'order'
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}