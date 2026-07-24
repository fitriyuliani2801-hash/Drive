<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('reporter_name');
            $table->string('reporter_phone')->nullable();
            $table->string('title');
            $table->text('description');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('location_address');
            $table->string('district')->default('Metro Pusat'); // Metro Pusat, Metro Timur, Metro Barat, Metro Utara, Metro Selatan
            $table->string('image_path')->nullable();
            $table->enum('status', ['pending', 'verified', 'in_progress', 'resolved', 'rejected'])->default('pending');
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('admin_note')->nullable();
            $table->string('resolution_image_path')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
