<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->boolean('is_free')->default(false);
            $table->unsignedInteger('position')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('lessons');
    }
};
