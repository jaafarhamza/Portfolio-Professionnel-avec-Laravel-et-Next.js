<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->string('url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('category');
            $table->json('tags')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('order')->default(0);
            $table->date('completed_at')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};