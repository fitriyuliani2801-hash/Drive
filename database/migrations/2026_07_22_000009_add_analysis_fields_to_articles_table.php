<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('source_url')->nullable()->after('source');
            $table->string('platform')->nullable()->after('source_url');
            $table->enum('verdict', ['asli', 'hoaks'])->default('asli')->after('platform');
            $table->float('verdict_score')->default(95.0)->after('verdict');
            $table->text('verdict_reasoning')->nullable()->after('verdict_score');
            $table->integer('positive_count')->default(0)->after('verdict_reasoning');
            $table->integer('negative_count')->default(0)->after('positive_count');
            $table->integer('neutral_count')->default(0)->after('negative_count');
        });

        Schema::table('social_comments', function (Blueprint $table) {
            $table->foreignId('article_id')->nullable()->after('social_analysis_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->dropForeign(['article_id']);
            $table->dropColumn('article_id');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'source_url', 'platform', 'verdict', 'verdict_score',
                'verdict_reasoning', 'positive_count', 'negative_count', 'neutral_count'
            ]);
        });
    }
};
