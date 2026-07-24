<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lda_topics', function (Blueprint $table) {
            $table->id();
            $table->integer('topic_number');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label');
            $table->json('keywords'); // Array of ['word' => string, 'weight' => float]
            $table->float('coherence_score')->default(0.85);
            $table->timestamps();
        });

        Schema::create('crawled_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform'); // Instagram, X (Twitter), Berita Online Lampung
            $table->string('source_account'); // @pemkotmetro, @radar_lampung, @seputar_metro
            $table->string('author_name');
            $table->text('raw_text');
            $table->text('cleaned_text')->nullable();
            $table->json('tokens')->nullable();
            $table->json('stemmed_tokens')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lda_topic_id')->nullable()->constrained('lda_topics')->nullOnDelete();
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawled_comments');
        Schema::dropIfExists('lda_topics');
    }
};
