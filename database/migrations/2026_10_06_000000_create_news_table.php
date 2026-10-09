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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 190)->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body');

            // A category slug, resolved to its label through config('school.news').
            $table->string('category', 64)->index();

            // Optional media: a filename under images/berita, extra photos as a
            // JSON list of filenames, and a clip or an external URL.
            $table->string('image', 190)->nullable();
            $table->json('gallery')->nullable();
            $table->string('video', 190)->nullable();

            $table->string('author', 120)->nullable();

            // The archive is reverse-chronological and "popular" ranks by views.
            $table->unsignedInteger('views')->default(0)->index();
            $table->timestamp('published_at')->index();

            // One headline on the home page, a curated run in the picks slider.
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_picked')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
