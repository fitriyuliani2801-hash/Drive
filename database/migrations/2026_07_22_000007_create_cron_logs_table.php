<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command_name')->default('lda:auto-run');
            $table->string('status'); // running, success, failed
            $table->integer('comments_fetched_count')->default(0);
            $table->float('duration_seconds')->default(0);
            $table->text('log_message')->nullable();
            $table->timestamp('executed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cron_logs');
    }
};
