<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('platform'); // Instagram, Facebook, TikTok
            $table->string('author_name');
            $table->string('post_title');
            $table->text('post_content');
            $table->string('media_image')->nullable();
            $table->enum('verdict', ['asli', 'hoaks'])->default('asli');
            $table->float('verdict_score')->default(90.0); // e.g. 92.5%
            $table->text('verdict_reasoning')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('positive_count')->default(0);
            $table->integer('negative_count')->default(0);
            $table->integer('neutral_count')->default(0);
            $table->timestamps();
        });

        Schema::create('social_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_analysis_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('author_name');
            $table->text('raw_comment');
            $table->enum('sentiment', ['positif', 'negatif', 'netral'])->default('netral');
            $table->float('sentiment_score')->default(0.85);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_comments');
        Schema::dropIfExists('social_analyses');
    }
};
