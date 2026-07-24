<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->string('comment_id')->nullable()->unique()->after('id');
            $table->enum('platform', ['Instagram', 'X', 'Facebook', 'YouTube', 'Metrologi'])->default('Metrologi')->after('comment_id');
            $table->string('author_avatar')->nullable()->after('author_name');
            $table->enum('status', ['approved', 'pending', 'spam'])->default('approved')->after('sentiment_score');
            $table->timestamp('posted_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->dropColumn(['comment_id', 'platform', 'author_avatar', 'status', 'posted_at']);
        });
    }
};
