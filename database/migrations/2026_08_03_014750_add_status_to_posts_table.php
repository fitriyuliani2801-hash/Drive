<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('posts', function (Blueprint $table) {
        $table->enum('status', ['draft', 'published'])->default('published')->after('content');
    });
}

public function down()
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
