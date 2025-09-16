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
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->text('url')->nullable();
            $table->text('image_url')->nullable();
            $table->timestamp('published_at');
            $table->string('author')->nullable();
            $table->foreignId('news_source_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['published_at']);
            $table->index(['news_source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
