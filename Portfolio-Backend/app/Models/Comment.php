<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id', 'name', 'email', 'content', 'is_approved'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}