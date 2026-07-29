<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crawled_comments', function (Blueprint $table) {
            $table->string('post_url')->nullable()->after('source_account');
        });
    }

    public function down(): void
    {
        Schema::table('crawled_comments', function (Blueprint $table) {
            $table->dropColumn('post_url');
        });
    }
};
