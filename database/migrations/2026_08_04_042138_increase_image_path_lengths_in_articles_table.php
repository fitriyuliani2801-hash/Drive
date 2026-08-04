<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('image_path')->nullable()->change();
            $table->text('middle_image_path')->nullable()->change();
            $table->text('end_image_path')->nullable()->change();
            $table->text('comment_image_path')->nullable()->change();
            $table->text('source_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('image_path', 255)->nullable()->change();
            $table->string('middle_image_path', 255)->nullable()->change();
            $table->string('end_image_path', 255)->nullable()->change();
            $table->string('comment_image_path', 255)->nullable()->change();
            $table->string('source_url', 255)->nullable()->change();
        });
    }
};
