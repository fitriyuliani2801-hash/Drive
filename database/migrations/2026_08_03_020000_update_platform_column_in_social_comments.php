<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->string('platform', 50)->default('Media Sosial')->change();
        });
    }

    public function down(): void
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->string('platform')->change();
        });
    }
};
